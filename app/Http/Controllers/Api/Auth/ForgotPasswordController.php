<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function sendResetCodeEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors()->all());
        }

        $fieldType = $this->findFieldType();
        $user = User::where($fieldType, $request->value)->first();

        if (!$user) {
            $notify[] = 'The account could not be found';
            return responseError('validation_error', $notify);
        }

        PasswordReset::where('email', $user->email)->delete();
        $code = verificationCode(6);
        $password = new PasswordReset();
        $password->email = $user->email;
        $password->token = $code;
        $password->created_at = Carbon::now();
        $password->save();

        $userIpInfo = getIpInfo();
        $userBrowserInfo = osBrowser();
        notify($user, 'PASS_RESET_CODE', [
            'code'             => $code,
            'operating_system' => @$userBrowserInfo['os_platform'],
            'browser'          => @$userBrowserInfo['browser'],
            'ip'               => @$userIpInfo['ip'],
            'time'             => @$userIpInfo['time'],
        ], ['email']);

        $notify[] = 'Verification code sent to your email';
        return responseSuccess('code_sent', $notify, ['email' => $user->email]);
    }

    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code'  => 'required',
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors()->all());
        }

        $reset = PasswordReset::where('token', $request->code)
            ->where('email', $request->email)
            ->first();

        if (!$reset || $reset->created_at->lt(now()->subMinutes(60))) {
            if ($reset) $reset->delete();
            $notify[] = 'Verification code is invalid or has expired';
            return responseError('validation_error', $notify);
        }

        $notify[] = 'You can now change your password';
        return responseSuccess('verified', $notify);
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules());
        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors()->all());
        }

        $reset = PasswordReset::where('token', $request->token)
            ->where('email', $request->email)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$reset || $reset->created_at->lt(now()->subMinutes(60))) {
            if ($reset) $reset->delete();
            $notify[] = 'Invalid or expired verification code';
            return responseError('validation_error', $notify);
        }

        $user = User::where('email', $reset->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        $reset->delete();

        // Revoke all existing tokens so any stolen token is invalidated
        $user->tokens()->delete();

        $userIpInfo = getIpInfo();
        $userBrowser = osBrowser();
        notify($user, 'PASS_RESET_DONE', [
            'operating_system' => @$userBrowser['os_platform'],
            'browser'          => @$userBrowser['browser'],
            'ip'               => @$userIpInfo['ip'],
            'time'             => @$userIpInfo['time'],
        ], ['email']);

        $notify[] = 'Password changed successfully';
        return responseSuccess('password_changed', $notify);
    }

    protected function rules()
    {
        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }
        return [
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => ['required', 'confirmed', $passwordValidation],
        ];
    }

    private function findFieldType()
    {
        $input = request()->input('value');
        $fieldType = filter_var($input, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $input]);
        return $fieldType;
    }
}
