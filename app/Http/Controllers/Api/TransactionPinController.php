<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TransactionPinController extends Controller
{
    /**
     * GET /api/pin/status
     * Returns whether the user has a PIN set and if it is currently locked.
     */
    public function status()
    {
        $user = auth()->user();

        $locked    = $user->pin_locked_until && now()->lt($user->pin_locked_until);
        $lockedUntil = $locked ? $user->pin_locked_until : null;

        $notify[] = 'PIN status';
        return responseSuccess('pin_status', $notify, [
            'has_pin'      => (bool) $user->has_pin,
            'is_locked'    => $locked,
            'locked_until' => $lockedUntil,
            'pin_set_at'   => $user->pin_set_at,
        ]);
    }

    /**
     * POST /api/pin/set
     * Set a transaction PIN for the first time.
     * Body: { pin, pin_confirmation, security_question_id, security_answer }
     */
    public function setPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin'                  => 'required|digits:6|confirmed',
            'security_question_id' => 'required|string',
            'security_answer'      => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user = auth()->user();

        if ($user->has_pin) {
            $notify[] = 'PIN already set. Use the update endpoint to change it.';
            return responseError('pin_already_set', $notify);
        }

        $user->update([
            'pin'                  => Hash::make($request->pin),
            'security_question_id' => $request->security_question_id,
            'security_answer_hash' => Hash::make(strtolower($request->security_answer)),
            'has_pin'              => 1,
            'pin_set_at'           => now(),
            'pin_failed_attempts'  => 0,
            'pin_locked_until'     => null,
        ]);

        $notify[] = 'Transaction PIN set successfully';
        return responseSuccess('pin_set', $notify);
    }

    /**
     * POST /api/pin/verify
     * Verify the user's current PIN (e.g. before confirming a transaction).
     * Body: { pin }
     */
    public function verifyPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user = auth()->user();

        if (!$user->has_pin) {
            $notify[] = 'No PIN set. Please set a PIN first.';
            return responseError('pin_not_set', $notify);
        }

        if ($user->pin_locked_until && now()->lt($user->pin_locked_until)) {
            $notify[] = 'PIN is temporarily locked. Try again after ' . $user->pin_locked_until->diffForHumans();
            return responseError('pin_locked', $notify, [
                'locked_until' => $user->pin_locked_until,
            ]);
        }

        if (!Hash::check($request->pin, $user->pin)) {
            $user->increment('pin_failed_attempts');

            $attemptsLeft = max(0, 3 - $user->pin_failed_attempts);

            if ($user->pin_failed_attempts >= 3) {
                $user->update([
                    'pin_locked_until'    => now()->addMinutes(15),
                    'pin_failed_attempts' => 0,
                ]);
                $notify[] = 'Too many failed attempts. PIN locked for 15 minutes.';
                return responseError('pin_locked', $notify, [
                    'locked_until' => $user->pin_locked_until,
                ]);
            }

            $notify[] = 'Invalid PIN. ' . $attemptsLeft . ' attempt(s) remaining.';
            return responseError('invalid_pin', $notify, [
                'attempts_remaining' => $attemptsLeft,
            ]);
        }

        $user->update([
            'pin_failed_attempts' => 0,
            'pin_locked_until'    => null,
        ]);

        $notify[] = 'PIN verified';
        return responseSuccess('pin_verified', $notify);
    }

    /**
     * POST /api/pin/reset/send-code
     * Send a 6-digit verification code to the user's email to begin PIN reset.
     */
    public function sendResetCode()
    {
        $user = auth()->user();

        if (!$user->has_pin) {
            $notify[] = 'No PIN set. Please set a PIN first.';
            return responseError('pin_not_set', $notify);
        }

        $user->ver_code         = verificationCode(6);
        $user->ver_code_send_at = now();
        $user->saveQuietly();

        notify($user, 'EVER_CODE', ['code' => $user->ver_code], ['email']);

        $masked  = substr($user->email, 0, 3) . '***' . substr($user->email, strpos($user->email, '@'));
        $notify[] = 'Verification code sent to ' . $masked;
        return responseSuccess('reset_code_sent', $notify, ['email' => $masked]);
    }

    /**
     * POST /api/pin/reset/verify-code
     * Verify the emailed code to unlock PIN reset.
     * Body: { code }
     */
    public function verifyResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user = auth()->user();

        if (!$user->ver_code || !$user->ver_code_send_at) {
            $notify[] = 'No code found. Please request a new one.';
            return responseError('no_code', $notify);
        }

        if (now()->diffInMinutes($user->ver_code_send_at) > 10) {
            $user->ver_code         = null;
            $user->ver_code_send_at = null;
            $user->saveQuietly();
            $notify[] = 'Code expired. Please request a new one.';
            return responseError('code_expired', $notify);
        }

        if ((string) $request->code !== (string) $user->ver_code) {
            $notify[] = 'Invalid verification code.';
            return responseError('invalid_code', $notify);
        }

        $user->ver_code         = null;
        $user->ver_code_send_at = null;
        $user->saveQuietly();

        // Issue a short-lived token the client must send back to reset the PIN
        $resetToken = base64_encode($user->id . '|' . now()->addMinutes(10)->timestamp);
        cache()->put('pin_reset_token_' . $user->id, $resetToken, now()->addMinutes(10));

        $notify[] = 'Code verified. Proceed to reset your PIN.';
        return responseSuccess('code_verified', $notify, ['reset_token' => $resetToken]);
    }

    /**
     * POST /api/pin/reset/confirm
     * Reset the PIN using the token from verify-code step.
     * Body: { reset_token, pin, pin_confirmation }
     */
    public function resetPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reset_token' => 'required|string',
            'pin'         => 'required|digits:6|confirmed',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user       = auth()->user();
        $cachedToken = cache()->get('pin_reset_token_' . $user->id);

        if (!$cachedToken || $cachedToken !== $request->reset_token) {
            $notify[] = 'Invalid or expired reset token. Please start over.';
            return responseError('invalid_reset_token', $notify);
        }

        cache()->forget('pin_reset_token_' . $user->id);

        $user->update([
            'pin'                 => Hash::make($request->pin),
            'pin_failed_attempts' => 0,
            'pin_locked_until'    => null,
            'pin_set_at'          => now(),
        ]);

        $notify[] = 'Transaction PIN reset successfully';
        return responseSuccess('pin_reset', $notify);
    }

    /**
     * POST /api/pin/update
     * Change PIN by providing the current PIN and a new one.
     * Body: { current_pin, pin, pin_confirmation }
     */
    public function updatePin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_pin' => 'required|digits:6',
            'pin'         => 'required|digits:6|confirmed|different:current_pin',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user = auth()->user();

        if (!$user->has_pin) {
            $notify[] = 'No PIN set. Please set a PIN first.';
            return responseError('pin_not_set', $notify);
        }

        if ($user->pin_locked_until && now()->lt($user->pin_locked_until)) {
            $notify[] = 'PIN is temporarily locked. Try again after ' . $user->pin_locked_until->diffForHumans();
            return responseError('pin_locked', $notify, ['locked_until' => $user->pin_locked_until]);
        }

        if (!Hash::check($request->current_pin, $user->pin)) {
            $notify[] = 'Current PIN is incorrect.';
            return responseError('invalid_pin', $notify);
        }

        $user->update([
            'pin'                 => Hash::make($request->pin),
            'pin_failed_attempts' => 0,
            'pin_locked_until'    => null,
            'pin_set_at'          => now(),
        ]);

        $notify[] = 'Transaction PIN updated successfully';
        return responseSuccess('pin_updated', $notify);
    }
}
