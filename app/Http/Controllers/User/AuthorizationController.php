<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Constants\Status;
use App\Lib\Intended;
use App\Models\Form;
use Illuminate\Support\Facades\Mail;

class AuthorizationController extends Controller
{
    protected function checkCodeValidity($user, $addMin = 2)
    {
        if (!$user->ver_code_send_at) {
            return false;
        }
        if ($user->ver_code_send_at->addMinutes($addMin) < Carbon::now()) {
            return false;
        }
        return true;
    }

    public function authorizeForm()
    {
        $user = auth()->user();
        if (!$user->status) {
            $pageTitle = 'Banned';
            $type = 'ban';
        } elseif (!$user->ev && gs('ev')) {
            $type = 'email';
            $pageTitle = 'Verify Email';
            $notifyTemplate = 'EVER_CODE';
        // } elseif ($user->kv == 0) {
        //     $pageTitle = 'KYC Documents';
        //     $form = Form::where('act', 'user.kyc')->first();
        //     return view('Template::user.kyc.form', compact('pageTitle', 'form', 'user'));
        } elseif (!$user->sv && gs('sv')) {
            $type = 'sms';
            $pageTitle = 'Verify Mobile Number';
            $notifyTemplate = 'SVER_CODE';
        } elseif (!$user->tv) {
            $pageTitle = '2FA Verification';
            $type = '2fa';
            $method = $user->two_fa_method ?? 'totp';
            if (in_array($method, ['sms', 'email']) && !$this->checkCodeValidity($user)) {
                $user->ver_code = verificationCode(6);
                $user->ver_code_send_at = Carbon::now();
                $user->save();
                if ($method === 'sms') {
                    notify($user, 'SVER_CODE', ['code' => $user->ver_code], ['sms']);
                } else {
                    notify($user, 'EVER_CODE', ['code' => $user->ver_code], ['email']);
                }
            }
        } else {
            return to_route('user.home');
        }

        if (!$this->checkCodeValidity($user) && ($type != '2fa') && ($type != 'ban')) {
            $user->ver_code = verificationCode(6);
            $user->ver_code_send_at = Carbon::now();
            $user->save();
            notify($user, $notifyTemplate, [
                'code' => $user->ver_code
            ], [$type]);
        }

        $method = $method ?? null;
        return view('Template::user.auth.authorization.' . $type, compact('user', 'pageTitle', 'method'));
    }

    public function sendVerifyCode($type)
    {
        $user = auth()->user();

        if ($this->checkCodeValidity($user)) {
            $targetTime = $user->ver_code_send_at->addMinutes(2)->timestamp;
            $delay = $targetTime - time();
            throw ValidationException::withMessages(['resend' => 'Please try after ' . $delay . ' seconds']);
        }

        $user->ver_code = verificationCode(6);
        $user->ver_code_send_at = Carbon::now();
        $user->save();

        if ($type == 'email') {
            $type = 'email';
            $notifyTemplate = 'EVER_CODE';
        } else {
            $type = 'sms';
            $notifyTemplate = 'SVER_CODE';
        }

        notify($user, $notifyTemplate, [
            'code' => $user->ver_code
        ], [$type]);

        $notify[] = ['success', 'Verification code sent successfully'];
        return back()->withNotify($notify);
    }

    public function emailVerification(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);

        $user = auth()->user();

        if ($user->ver_code == $request->code) {
            $user->ev = Status::VERIFIED;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();

            $redirection = Intended::getRedirection();
            return $redirection ? $redirection : to_route('user.home');
        }
        throw ValidationException::withMessages(['code' => 'Verification code didn\'t match!']);
    }

    public function mobileVerification(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $user = auth()->user();
        if ($user->ver_code == $request->code) {
            $user->sv = Status::VERIFIED;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();
            $redirection = Intended::getRedirection();
            return $redirection ? $redirection : to_route('user.home');
        }
        throw ValidationException::withMessages(['code' => 'Verification code didn\'t match!']);
    }

    public function g2faVerification(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'code' => 'required',
        ]);
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $redirection = Intended::getRedirection();
            return $redirection ? $redirection : to_route('user.home');
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function sms2faVerification(Request $request)
    {
        $request->validate(['code' => 'required']);
        $user = auth()->user();
        if ($user->ver_code == $request->code && $this->checkCodeValidity($user, 10)) {
            $user->tv = Status::VERIFIED;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();
            $redirection = Intended::getRedirection();
            return $redirection ? $redirection : to_route('user.home');
        }
        throw ValidationException::withMessages(['code' => 'Invalid or expired verification code']);
    }

    public function email2faVerification(Request $request)
    {
        $request->validate(['code' => 'required']);
        $user = auth()->user();
        if ($user->ver_code == $request->code && $this->checkCodeValidity($user, 10)) {
            $user->tv = Status::VERIFIED;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();
            $redirection = Intended::getRedirection();
            return $redirection ? $redirection : to_route('user.home');
        }
        throw ValidationException::withMessages(['code' => 'Invalid or expired verification code']);
    }

    public function resend2faCode(Request $request)
    {
        $user = auth()->user();
        if ($this->checkCodeValidity($user, 1)) {
            throw ValidationException::withMessages(['resend' => 'Please wait before requesting another code']);
        }
        $user->ver_code = verificationCode(6);
        $user->ver_code_send_at = Carbon::now();
        $user->save();
        $method = $user->two_fa_method ?? 'totp';
        if ($method === 'sms') {
            notify($user, 'SVER_CODE', ['code' => $user->ver_code], ['sms']);
        } elseif ($method === 'email') {
            notify($user, 'EVER_CODE', ['code' => $user->ver_code], ['email']);
        }
        return response()->json(['status' => 'sent']);
    }
}
