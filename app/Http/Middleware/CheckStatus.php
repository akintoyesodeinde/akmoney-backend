<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = auth()->user();

            if ($user->status && (!gs('ev') || $user->ev) && (!gs('sv') || $user->sv) && $user->tv) {
                // Account is fully verified — check for admin-enforced 2FA setup requirement
                if ($user->force_tfa && !$user->ts) {
                    if ($request->is('api/*')) {
                        $notify[] = 'Two-factor authentication is required for your account. Please enable 2FA to continue.';
                        return response()->json([
                            'remark'  => 'force_2fa_setup',
                            'status'  => 'error',
                            'message' => ['error' => $notify],
                            'data'    => ['user' => $user],
                        ], 403);
                    }
                    if (!$request->routeIs('user.twofactor', 'user.twofactor.enable', 'user.twofactor.disable')) {
                        $notify[] = ['warning', 'Your account requires two-factor authentication. Please set it up to continue.'];
                        return to_route('user.twofactor')->withNotify($notify);
                    }
                }

                return $next($request);
            }

            if ($request->is('api/*')) {
                // Banned account — short-circuit immediately
                if (!$user->status) {
                    $notify[] = 'Your account has been banned';
                    return response()->json([
                        'remark'  => 'account_banned',
                        'status'  => 'error',
                        'message' => ['error' => $notify],
                        'data'    => ['user' => $user],
                    ]);
                }

                // Determine what still needs verification and auto-resend OTP if expired
                $verType    = null;
                $codeSentAt = null;

                if (!$user->ev && gs('ev')) {
                    $verType = 'email';
                    if (!$user->ver_code_send_at || $user->ver_code_send_at->addMinutes(2) < now()) {
                        $user->ver_code         = verificationCode(6);
                        $user->ver_code_send_at = now();
                        $user->save();
                        notify($user, 'EVER_CODE', ['code' => $user->ver_code], ['email']);
                    }
                    $codeSentAt = $user->ver_code_send_at;
                } elseif (!$user->sv && gs('sv')) {
                    $verType = 'sms';
                    if (!$user->ver_code_send_at || $user->ver_code_send_at->addMinutes(2) < now()) {
                        $user->ver_code         = verificationCode(6);
                        $user->ver_code_send_at = now();
                        $user->save();
                        notify($user, 'SVER_CODE', ['code' => $user->ver_code], ['sms']);
                    }
                    $codeSentAt = $user->ver_code_send_at;
                } elseif (!$user->tv) {
                    $verType = $user->two_fa_method === 'sms' ? '2fa_sms' : '2fa';
                    // For SMS 2FA, auto-send the OTP if expired
                    if ($user->two_fa_method === 'sms' &&
                        (!$user->ver_code_send_at || $user->ver_code_send_at->addMinutes(2) < now())) {
                        $user->ver_code         = verificationCode(6);
                        $user->ver_code_send_at = now();
                        $user->save();
                        notify($user, 'SVER_CODE', ['code' => $user->ver_code], ['sms']);
                        $codeSentAt = $user->ver_code_send_at;
                    }
                }

                $notify[] = 'You need to verify your account first';
                return response()->json([
                    'remark'  => 'unverified',
                    'status'  => 'error',
                    'message' => ['error' => $notify],
                    'data'    => [
                        'user'              => $user,
                        'verification_type' => $verType,
                        'ver_code_send_at'  => $codeSentAt,
                    ],
                ]);
            }

            return to_route('user.authorization');
        }

        abort(403);
    }
}
