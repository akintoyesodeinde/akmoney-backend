<?php

namespace App\Http\Controllers\Api\Auth;

use App\Constants\Status;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $username;

    public function __construct()
    {
        parent::__construct();
        $this->username = $this->findUsername();
    }

    public function login(Request $request)
    {
        if ($request->is_web) {
            if (!verifyCaptcha()) {
                $notify[] = 'Invalid captcha provided';
                return responseError('captcha_error', $notify);
            }
        }

        $validator = $this->validateLogin($request);
        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $credentials = request([$this->username, 'password']);
        if (!Auth::attempt($credentials)) {
            $notify[] = 'Unauthorized user';
            return responseError('unauthorized_user', $notify);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $this->authenticated($request, $user);

        // Check if user needs verification before granting full access
        if ($user->status && (!gs('ev') || $user->ev) && (!gs('sv') || $user->sv) && $user->tv) {
            $tokenResult = $user->createToken('auth_token', ['user'])->plainTextToken;
            $notify[] = 'Login Successful';
            return responseSuccess('login_success', $notify, [
                'user'         => $user->fresh(),
                'kyc_data'     => $user->kyc_data,
                'access_token' => $tokenResult,
                'token_type'   => 'Bearer',
            ]);
        }

        // User is not fully verified — issue a token so they can call verify endpoints,
        // then determine what needs verifying and resend OTP if applicable.
        $tokenResult = $user->createToken('auth_token', ['user'])->plainTextToken;

        $verType = null;
        $notifyTemplate = null;

        if (!$user->status) {
            $notify[] = 'Your account has been banned';
            return responseError('account_banned', $notify, [
                'access_token' => $tokenResult,
                'token_type'   => 'Bearer',
                'user'         => $user->fresh(),
            ]);
        } elseif (!$user->ev && gs('ev')) {
            $verType          = 'email';
            $notifyTemplate   = 'EVER_CODE';
        } elseif (!$user->sv && gs('sv')) {
            $verType          = 'sms';
            $notifyTemplate   = 'SVER_CODE';
        } elseif (!$user->tv) {
            $notify[] = 'Two-factor authentication required';
            return responseError('unverified', $notify, [
                'access_token'      => $tokenResult,
                'token_type'        => 'Bearer',
                'user'              => $user->fresh(),
                'verification_type' => '2fa',
            ]);
        }

        // Resend OTP only if previous code has expired (2-min cooldown)
        $codeSentAt = null;
        if ($notifyTemplate) {
            $codeValid = $user->ver_code_send_at && $user->ver_code_send_at->addMinutes(2) > now();
            if (!$codeValid) {
                $user->ver_code         = verificationCode(6);
                $user->ver_code_send_at = now();
                $user->save();
                notify($user, $notifyTemplate, ['code' => $user->ver_code], [$verType]);
            }
            $codeSentAt = $user->ver_code_send_at;
        }

        $notify[] = 'You need to verify your account';
        return responseError('unverified', $notify, [
            'access_token'      => $tokenResult,
            'token_type'        => 'Bearer',
            'user'              => $user->fresh(),
            'verification_type' => $verType,
            'ver_code_send_at'  => $codeSentAt,
        ]);
    }

    public function findUsername()
    {
        $login = request()->input('username');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $login]);
        return $fieldType;
    }

    public function username()
    {
        return $this->username;
    }

    protected function validateLogin(Request $request)
    {
        $validationRule = [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ];

        $validate = Validator::make($request->all(), $validationRule);
        return $validate;
    }

    public function logout()
    {
        // Revoke only the current device's token
        auth()->user()->currentAccessToken()->delete();
        $notify[] = 'Logout Successful';
        return responseSuccess('logout_success', $notify);
    }

    public function logoutAll()
    {
        // Revoke all tokens for this user (sign out of every device)
        auth()->user()->tokens()->delete();
        $notify[] = 'Logged out from all devices';
        return responseSuccess('logout_all_success', $notify);
    }

    public function authenticated(Request $request, $user)
    {
        $user->tv = $user->ts == Status::VERIFIED ? Status::UNVERIFIED : Status::VERIFIED;
        $user->save();
        $ip = getRealIP();
        $exist = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();
        if ($exist) {
            $userLogin->longitude =  $exist->longitude;
            $userLogin->latitude =  $exist->latitude;
            $userLogin->city =  $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country =  $exist->country;
        } else {
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude =  @implode(',', $info['long']);
            $userLogin->latitude =  @implode(',', $info['lat']);
            $userLogin->city =  @implode(',', $info['city']);
            $userLogin->country_code = @implode(',', $info['code']);
            $userLogin->country =  @implode(',', $info['country']);
        }

        $userAgent = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip =  $ip;
        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();

        // Notify user when logging in from a new IP address
        $isNewIp = !UserLogin::where('user_id', $user->id)
            ->where('user_ip', $ip)
            ->where('id', '!=', $userLogin->id)
            ->exists();

        if ($isNewIp) {
            notify($user, 'NEW_DEVICE_LOGIN', [
                'ip'      => $ip,
                'browser' => @$userAgent['browser'] ?? 'Unknown',
                'os'      => @$userAgent['os_platform'] ?? 'Unknown',
                'time'    => now()->format('Y-m-d H:i:s') . ' UTC',
            ], ['email']);
        }
    }

    public function checkToken(Request $request)
    {
        $validationRule = [
            'token' => 'required',
        ];

        $validator = Validator::make($request->all(), $validationRule);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $exists = PersonalAccessToken::where('token', hash('sha256', $request->token))->exists();

        if ($exists) {
            $notify[] = 'Token exists';
            return responseSuccess('token_exists', $notify);
        }

        $notify[] = 'Token doesn\'t exists';
        return responseError('token_not_exists', $notify);
    }
}
