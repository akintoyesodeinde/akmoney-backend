<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\BackupCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthorizationController extends Controller
{
    /** Verification codes expire after this many minutes */
    const CODE_EXPIRY_MINUTES = 10;

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

    public function authorization()
    {
        $user = auth()->user();

        if (!$user->status) {
            $type = 'ban';
        } elseif (!$user->ev && gs('ev')) {
            $type           = 'email';
            $notifyTemplate = 'EVER_CODE';
        } elseif (!$user->sv && gs('sv')) {
            $type           = 'sms';
            $notifyTemplate = 'SVER_CODE';
        } elseif (!$user->tv) {
            $type = '2fa';
        } else {
            $notify[] = 'You are already verified';
            return responseError('already_verified', $notify);
        }

        // For email/SMS account verification: resend code if expired
        if (in_array($type, ['email', 'sms']) && !$this->checkCodeValidity($user)) {
            $user->ver_code         = verificationCode(6);
            $user->ver_code_send_at = Carbon::now();
            $user->save();
            notify($user, $notifyTemplate, ['code' => $user->ver_code], [$type]);
        }

        // For SMS 2FA login: send OTP if expired
        if ($type === '2fa' && $user->two_fa_method === 'sms') {
            if (!$this->checkCodeValidity($user)) {
                $user->ver_code         = verificationCode(6);
                $user->ver_code_send_at = Carbon::now();
                $user->save();
                notify($user, 'SVER_CODE', ['code' => $user->ver_code], ['sms']);
            }

            $notify[] = 'A verification code has been sent to your mobile number';
            return responseSuccess('2fa_sms_sent', $notify, [
                'verification_type' => '2fa_sms',
                'ver_code_send_at'  => $user->ver_code_send_at,
            ]);
        }

        $verType = $type === '2fa' ? 'totp' : $type;
        $notify[] = 'Verify your account';
        return responseSuccess('code_sent', $notify, [
            'verification_type' => $verType,
            'ver_code_send_at'  => $user->ver_code_send_at ?? null,
        ]);
    }

    public function sendVerifyCode($type)
    {
        $user = auth()->user();

        if ($type == 'email') {
            if (!gs('ev')) {
                $notify[] = 'Email verification is not required';
                return responseError('not_required', $notify);
            }
            if ($user->ev) {
                $notify[] = 'Email is already verified';
                return responseError('already_verified', $notify);
            }
            $notifyTemplate = 'EVER_CODE';
        } elseif ($type == 'sms') {
            if (!gs('sv')) {
                $notify[] = 'Mobile verification is not required';
                return responseError('not_required', $notify);
            }
            if ($user->sv) {
                $notify[] = 'Mobile is already verified';
                return responseError('already_verified', $notify);
            }
            $notifyTemplate = 'SVER_CODE';
        } else {
            $notify[] = 'Invalid verification type';
            return responseError('invalid_type', $notify);
        }

        if ($this->checkCodeValidity($user)) {
            $targetTime = $user->ver_code_send_at->addMinutes(2)->timestamp;
            $delay = $targetTime - time();
            $notify[] = 'Please try after ' . $delay . ' seconds';
            return responseError('validation_error', $notify, ['ver_code_send_at' => $user->ver_code_send_at]);
        }

        $user->ver_code         = verificationCode(6);
        $user->ver_code_send_at = Carbon::now();
        $user->save();

        notify($user, $notifyTemplate, ['code' => $user->ver_code], [$type]);

        $notify[] = 'Verification code sent successfully';
        return responseSuccess('code_sent', $notify, ['ver_code_send_at' => $user->ver_code_send_at]);
    }

    public function emailVerification(Request $request)
    {
        $validator = Validator::make($request->all(), ['code' => 'required']);
        if ($validator->fails()) return responseError('validation_error', $validator->errors());

        $user = auth()->user();

        if ($user->ver_code_send_at && $user->ver_code_send_at->addMinutes(self::CODE_EXPIRY_MINUTES) < Carbon::now()) {
            $notify[] = 'Verification code has expired. Please request a new one.';
            return responseError('code_expired', $notify);
        }

        if ($user->ver_code != $request->code) {
            $notify[] = 'Verification code doesn\'t match';
            return responseError('validation_error', $notify);
        }

        $user->ev               = Status::VERIFIED;
        $user->ver_code         = null;
        $user->ver_code_send_at = null;
        $user->save();

        $notify[] = 'Email verified successfully';
        return responseSuccess('email_verified', $notify, ['user' => $user]);
    }

    public function mobileVerification(Request $request)
    {
        $validator = Validator::make($request->all(), ['code' => 'required']);
        if ($validator->fails()) return responseError('validation_error', $validator->errors());

        $user = auth()->user();

        if ($user->ver_code_send_at && $user->ver_code_send_at->addMinutes(self::CODE_EXPIRY_MINUTES) < Carbon::now()) {
            $notify[] = 'Verification code has expired. Please request a new one.';
            return responseError('code_expired', $notify);
        }

        if ($user->ver_code != $request->code) {
            $notify[] = 'Verification code doesn\'t match';
            return responseError('validation_error', $notify);
        }

        $user->sv               = Status::VERIFIED;
        $user->ver_code         = null;
        $user->ver_code_send_at = null;
        $user->save();

        $notify[] = 'Mobile verified successfully';
        return responseSuccess('mobile_verified', $notify, ['user' => $user]);
    }

    public function resend2faSms()
    {
        $user = auth()->user();

        if (!$user->ts || $user->two_fa_method !== 'sms') {
            $notify[] = 'SMS 2FA is not your active method';
            return responseError('not_applicable', $notify);
        }

        if ($user->tv) {
            $notify[] = 'Already verified';
            return responseError('already_verified', $notify);
        }

        if ($this->checkCodeValidity($user)) {
            $delay    = $user->ver_code_send_at->addMinutes(2)->timestamp - time();
            $notify[] = 'Please try after ' . $delay . ' seconds';
            return responseError('validation_error', $notify, ['ver_code_send_at' => $user->ver_code_send_at]);
        }

        $user->ver_code         = verificationCode(6);
        $user->ver_code_send_at = Carbon::now();
        $user->save();
        notify($user, 'SVER_CODE', ['code' => $user->ver_code], ['sms']);

        $notify[] = 'Verification code resent to your mobile number';
        return responseSuccess('code_sent', $notify, ['ver_code_send_at' => $user->ver_code_send_at]);
    }

    public function g2faVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code'        => 'required_without:backup_code',
            'backup_code' => 'required_without:code',
        ]);
        if ($validator->fails()) return responseError('validation_error', $validator->errors());

        $user = auth()->user();

        // ── Backup code path ──────────────────────────────────────────────
        if ($request->filled('backup_code')) {
            $hashed  = hash('sha256', strtoupper(str_replace(' ', '', $request->backup_code)));
            $entry   = $user->backupCodes()->unused()->where('code', $hashed)->first();

            if (!$entry) {
                $notify[] = 'Invalid or already-used backup code';
                return responseError('invalid_backup_code', $notify);
            }

            $entry->used_at = Carbon::now();
            $entry->save();

            $user->tv = Status::VERIFIED;
            $user->save();

            $remaining = $user->backupCodes()->unused()->count();
            $notify[]  = 'Verified with backup code' . ($remaining > 0 ? ". {$remaining} backup code(s) remaining." : '. All backup codes used — please regenerate them.');
            return responseSuccess('twofa_verified', $notify, [
                'user'                   => $user->fresh(),
                'backup_codes_remaining' => $remaining,
            ]);
        }

        // ── SMS 2FA path ──────────────────────────────────────────────────
        if ($user->two_fa_method === 'sms') {
            $expired = !$user->ver_code_send_at
                || $user->ver_code_send_at->addMinutes(self::CODE_EXPIRY_MINUTES) < Carbon::now();

            if ($expired) {
                $notify[] = 'The verification code has expired. Please request a new one.';
                return responseError('code_expired', $notify);
            }

            if ($user->ver_code != $request->code) {
                $notify[] = 'Wrong verification code';
                return responseError('validation_error', $notify);
            }

            $user->tv               = Status::VERIFIED;
            $user->ver_code         = null;
            $user->ver_code_send_at = null;
            $user->save();

            $notify[] = 'Verification successful';
            return responseSuccess('twofa_verified', $notify, ['user' => $user->fresh()]);
        }

        // ── TOTP path (default) ───────────────────────────────────────────
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tv = Status::VERIFIED;
            $user->save();

            $notify[] = 'Verification successful';
            return responseSuccess('twofa_verified', $notify, ['user' => $user->fresh()]);
        }

        $notify[] = 'Wrong verification code';
        return responseError('validation_error', $notify);
    }
}
