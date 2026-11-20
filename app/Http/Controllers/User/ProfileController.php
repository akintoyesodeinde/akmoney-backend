<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ProfileAuditLog;
use App\Models\User;
use App\Constants\Status;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\SendMoney;

class ProfileController extends Controller
{
    /** Cooldown between email/phone change requests (minutes). */
    const CHANGE_COOLDOWN_MINUTES = 60;

    /** OTP validity window (minutes). */
    const OTP_EXPIRY_MINUTES = 10;

    // ─── Profile ─────────────────────────────────────────────────────────────

    public function profile()
    {
        $pageTitle = 'Profile Setting';
        $user      = auth()->user();

        $baseQuery = SendMoney::where('user_id', auth()->id())
            ->whereIn('status', [Status::SEND_MONEY_PENDING, Status::SEND_MONEY_COMPLETED]);

        $daily   = (clone $baseQuery)->whereDate('created_at', today())->sum('base_currency_amount');
        $monthly = (clone $baseQuery)->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->sum('base_currency_amount');

        $dailyLimit   = gs('user_daily_send_money_limit');
        $monthlyLimit = gs('user_monthly_send_money_limit');
        $dailyPercent   = $dailyLimit > 0   ? min(100, round(($daily   / $dailyLimit)   * 100, 1)) : 0;
        $monthlyPercent = $monthlyLimit > 0 ? min(100, round(($monthly / $monthlyLimit) * 100, 1)) : 0;

        $onboardingProgress = $user->getOnboardingProgress();

        return view('Template::user.profile_setting', compact(
            'monthlyPercent', 'dailyPercent', 'pageTitle',
            'user', 'daily', 'monthly', 'onboardingProgress'
        ));
    }

    public function submitProfile(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string',
            'lastname'  => 'required|string',
            'image'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'firstname.required' => 'The first name field is required',
            'lastname.required'  => 'The last name field is required',
        ]);

        $user    = auth()->user();
        $changed = [];

        $tracked = ['firstname', 'lastname', 'address', 'city', 'state', 'zip'];
        foreach ($tracked as $field) {
            $old = is_object($user->$field) ? json_encode($user->$field) : (string) $user->$field;
            $new = is_array($request->$field) ? json_encode($request->$field) : (string) $request->$field;
            if ($old !== $new) {
                $changed[] = $field;
                ProfileAuditLog::record($user->id, 'profile_updated', $field, $old, $new);
            }
        }

        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;
        $user->address   = $request->address;
        $user->city      = $request->city;
        $user->state     = $request->state;
        $user->zip       = $request->zip;

        if ($request->hasFile('image')) {
            $user->image = fileUploader(
                $request->file('image'),
                'assets/images/user/profile',
                '350x350',
                $user->image
            );
        }

        $user->save();

        if (!empty($changed)) {
            notify($user, 'PROFILE_CHANGED', [
                'changed_fields' => implode(', ', $changed),
                'time'           => now()->format('Y-m-d H:i:s') . ' UTC',
                'ip'             => request()->ip(),
                'browser'        => osBrowser()['browser'] ?? 'Unknown',
            ], ['email']);
        }

        $notify[] = ['success', 'Profile updated successfully'];
        return back()->withNotify($notify);
    }

    // ─── Email Change ─────────────────────────────────────────────────────────

    public function requestEmailChange(Request $request)
    {
        $request->validate([
            'new_email' => 'required|email|unique:users,email',
        ]);

        $user = auth()->user();

        // If same pending email and code still valid, don't resend
        if ($user->pending_email === $request->new_email && $this->changeCodeStillValid($user)) {
            $delay = $user->profile_change_code_sent_at->addMinutes(self::OTP_EXPIRY_MINUTES)->diffInSeconds(now());
            $notify[] = ['error', "A code was already sent. Try again in {$delay} seconds."];
            return back()->withNotify($notify);
        }

        // Cooldown between completed changes
        if ($user->email_change_at && $user->email_change_at->addMinutes(self::CHANGE_COOLDOWN_MINUTES)->isFuture()) {
            $remaining = (int) now()->diffInMinutes($user->email_change_at->addMinutes(self::CHANGE_COOLDOWN_MINUTES));
            $notify[] = ['error', "Please wait {$remaining} minute(s) before requesting another email change."];
            return back()->withNotify($notify);
        }

        $code = verificationCode(6);
        $user->pending_email                = $request->new_email;
        $user->profile_change_code          = $code;
        $user->profile_change_code_sent_at  = now();
        $user->save();

        ProfileAuditLog::record($user->id, 'email_change_requested', 'email', $user->email, $request->new_email);

        // OTP to new email address
        $newEmailProxy = (object)[
            'id'             => $user->id,
            'username'       => $user->username,
            'email'          => $request->new_email,
            'mobile'         => $user->mobile,
            'mobile_code'    => $user->mobile_code,
            'hasMobileNumber'=> (bool) $user->mobile,
        ];
        notify($newEmailProxy, 'EMAIL_CHANGE_OTP', [
            'code' => $code,
            'time' => now()->format('Y-m-d H:i:s') . ' UTC',
            'ip'   => request()->ip(),
        ], ['email']);

        // Security alert to OLD email
        notify($user, 'EMAIL_CHANGE_ALERT', [
            'new_email' => $request->new_email,
            'time'      => now()->format('Y-m-d H:i:s') . ' UTC',
            'ip'        => request()->ip(),
            'browser'   => osBrowser()['browser'] ?? 'Unknown',
        ], ['email']);

        return redirect()->route('user.email.change.verify')
            ->with('info', 'A verification code has been sent to ' . $request->new_email . '. A security alert was also sent to your current email address.');
    }

    public function showEmailChangeVerify()
    {
        $user = auth()->user();
        if (!$user->pending_email) {
            return redirect()->route('user.profile.setting');
        }
        $pageTitle = 'Verify New Email';
        return view('Template::user.email_change_verify', compact('pageTitle', 'user'));
    }

    public function verifyEmailChange(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $user = auth()->user();

        if (!$user->pending_email) {
            $notify[] = ['error', 'No pending email change found.'];
            return redirect()->route('user.profile.setting')->withNotify($notify);
        }

        if (!$this->changeCodeStillValid($user)) {
            $notify[] = ['error', 'The verification code has expired. Please request a new one.'];
            return back()->withNotify($notify);
        }

        if ($user->profile_change_code !== $request->code) {
            ProfileAuditLog::record($user->id, 'email_change_failed', 'code', null, 'Wrong code entered');
            $notify[] = ['error', 'Verification code does not match.'];
            return back()->withNotify($notify);
        }

        $oldEmail = $user->email;
        $newEmail = $user->pending_email;

        $user->email                       = $newEmail;
        $user->ev                          = gs('ev') ? Status::UNVERIFIED : Status::VERIFIED;
        $user->pending_email               = null;
        $user->profile_change_code         = null;
        $user->profile_change_code_sent_at = null;
        $user->email_change_at             = now();
        $user->save();

        ProfileAuditLog::record($user->id, 'email_changed', 'email', $oldEmail, $newEmail);

        notify($user, 'EMAIL_CHANGED', [
            'old_email' => $oldEmail,
            'new_email' => $newEmail,
            'time'      => now()->format('Y-m-d H:i:s') . ' UTC',
        ], ['email']);

        $notify[] = ['success', 'Your email address has been updated successfully.'];
        return redirect()->route('user.profile.setting')->withNotify($notify);
    }

    public function resendEmailChangeCode()
    {
        $user = auth()->user();

        if (!$user->pending_email) {
            $notify[] = ['error', 'No pending email change found.'];
            return redirect()->route('user.profile.setting')->withNotify($notify);
        }

        if ($this->changeCodeStillValid($user)) {
            $delay = (int) $user->profile_change_code_sent_at->addMinutes(self::OTP_EXPIRY_MINUTES)->diffInSeconds(now());
            $notify[] = ['error', "Please wait {$delay} seconds before requesting a new code."];
            return back()->withNotify($notify);
        }

        $code = verificationCode(6);
        $user->profile_change_code         = $code;
        $user->profile_change_code_sent_at = now();
        $user->save();

        $newEmailProxy = (object)[
            'id'             => $user->id,
            'username'       => $user->username,
            'email'          => $user->pending_email,
            'mobile'         => $user->mobile,
            'mobile_code'    => $user->mobile_code,
            'hasMobileNumber'=> (bool) $user->mobile,
        ];
        notify($newEmailProxy, 'EMAIL_CHANGE_OTP', [
            'code' => $code,
            'time' => now()->format('Y-m-d H:i:s') . ' UTC',
            'ip'   => request()->ip(),
        ], ['email']);

        $notify[] = ['success', 'A new verification code has been sent.'];
        return back()->withNotify($notify);
    }

    // ─── Phone Change ─────────────────────────────────────────────────────────

    public function requestMobileChange(Request $request)
    {
        $request->validate([
            'mobile'      => 'required|string',
            'mobile_code' => 'required|string',
        ]);

        $user = auth()->user();

        if ($user->mobile_change_at && $user->mobile_change_at->addMinutes(self::CHANGE_COOLDOWN_MINUTES)->isFuture()) {
            $remaining = (int) now()->diffInMinutes($user->mobile_change_at->addMinutes(self::CHANGE_COOLDOWN_MINUTES));
            $notify[] = ['error', "Please wait {$remaining} minute(s) before requesting another phone change."];
            return back()->withNotify($notify);
        }

        $exists = User::where('mobile', $request->mobile)
            ->where('mobile_code', $request->mobile_code)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($exists) {
            $notify[] = ['error', 'This phone number is already registered to another account.'];
            return back()->withNotify($notify);
        }

        $code = verificationCode(6);
        $user->pending_mobile               = $request->mobile;
        $user->pending_mobile_code          = $request->mobile_code;
        $user->profile_change_code          = $code;
        $user->profile_change_code_sent_at  = now();
        $user->save();

        ProfileAuditLog::record($user->id, 'mobile_change_requested', 'mobile', $user->mobileNumber, '+' . $request->mobile_code . $request->mobile);

        // OTP to new mobile (SMS) + current email
        $newMobileProxy = (object)[
            'id'             => $user->id,
            'username'       => $user->username,
            'email'          => $user->email,
            'mobile'         => $request->mobile,
            'mobile_code'    => $request->mobile_code,
            'hasMobileNumber'=> true,
        ];
        notify($newMobileProxy, 'PHONE_CHANGE_OTP', [
            'code'       => $code,
            'new_mobile' => '+' . $request->mobile_code . $request->mobile,
            'time'       => now()->format('Y-m-d H:i:s') . ' UTC',
            'ip'         => request()->ip(),
        ], ['sms', 'email']);

        // Alert to current email
        notify($user, 'PHONE_CHANGE_ALERT', [
            'new_mobile' => '+' . $request->mobile_code . $request->mobile,
            'time'       => now()->format('Y-m-d H:i:s') . ' UTC',
            'ip'         => request()->ip(),
        ], ['email']);

        return redirect()->route('user.mobile.change.verify')
            ->with('info', 'A verification code has been sent via SMS to your new number, and a security alert has been sent to your email.');
    }

    public function showMobileChangeVerify()
    {
        $user = auth()->user();
        if (!$user->pending_mobile) {
            return redirect()->route('user.profile.setting');
        }
        $pageTitle = 'Verify New Phone Number';
        return view('Template::user.phone_change_verify', compact('pageTitle', 'user'));
    }

    public function verifyMobileChange(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $user = auth()->user();

        if (!$user->pending_mobile) {
            $notify[] = ['error', 'No pending phone change found.'];
            return redirect()->route('user.profile.setting')->withNotify($notify);
        }

        if (!$this->changeCodeStillValid($user)) {
            $notify[] = ['error', 'The verification code has expired. Please request a new one.'];
            return back()->withNotify($notify);
        }

        if ($user->profile_change_code !== $request->code) {
            ProfileAuditLog::record($user->id, 'mobile_change_failed', 'code', null, 'Wrong code entered');
            $notify[] = ['error', 'Verification code does not match.'];
            return back()->withNotify($notify);
        }

        $oldMobile = $user->mobileNumber;
        $newMobile = '+' . $user->pending_mobile_code . $user->pending_mobile;

        $user->mobile                      = $user->pending_mobile;
        $user->mobile_code                 = $user->pending_mobile_code;
        $user->sv                          = gs('sv') ? Status::UNVERIFIED : Status::VERIFIED;
        $user->pending_mobile              = null;
        $user->pending_mobile_code         = null;
        $user->profile_change_code         = null;
        $user->profile_change_code_sent_at = null;
        $user->mobile_change_at            = now();
        $user->save();

        ProfileAuditLog::record($user->id, 'mobile_changed', 'mobile', $oldMobile, $newMobile);

        notify($user, 'PHONE_CHANGED', [
            'old_mobile' => $oldMobile,
            'new_mobile' => $newMobile,
            'time'       => now()->format('Y-m-d H:i:s') . ' UTC',
        ], ['email', 'sms']);

        $notify[] = ['success', 'Your phone number has been updated successfully.'];
        return redirect()->route('user.profile.setting')->withNotify($notify);
    }

    public function resendMobileChangeCode()
    {
        $user = auth()->user();

        if (!$user->pending_mobile) {
            $notify[] = ['error', 'No pending phone change found.'];
            return redirect()->route('user.profile.setting')->withNotify($notify);
        }

        if ($this->changeCodeStillValid($user)) {
            $delay = (int) $user->profile_change_code_sent_at->addMinutes(self::OTP_EXPIRY_MINUTES)->diffInSeconds(now());
            $notify[] = ['error', "Please wait {$delay} seconds before requesting a new code."];
            return back()->withNotify($notify);
        }

        $code = verificationCode(6);
        $user->profile_change_code         = $code;
        $user->profile_change_code_sent_at = now();
        $user->save();

        $newMobileProxy = (object)[
            'id'             => $user->id,
            'username'       => $user->username,
            'email'          => $user->email,
            'mobile'         => $user->pending_mobile,
            'mobile_code'    => $user->pending_mobile_code,
            'hasMobileNumber'=> true,
        ];
        notify($newMobileProxy, 'PHONE_CHANGE_OTP', [
            'code'       => $code,
            'new_mobile' => '+' . $user->pending_mobile_code . $user->pending_mobile,
            'time'       => now()->format('Y-m-d H:i:s') . ' UTC',
            'ip'         => request()->ip(),
        ], ['sms', 'email']);

        $notify[] = ['success', 'A new verification code has been sent.'];
        return back()->withNotify($notify);
    }

    // ─── Password ────────────────────────────────────────────────────────────

    public function changePassword()
    {
        $user      = auth()->user();
        $pageTitle = 'Change Password';
        return view('Template::user.password', compact('pageTitle', 'user'));
    }

    public function submitPassword(Request $request)
    {
        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', $passwordValidation],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            $notify[] = ['error', "The password doesn't match!"];
            return back()->withNotify($notify);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        ProfileAuditLog::record($user->id, 'password_changed');

        $notify[] = ['success', 'Password changed successfully'];
        return back()->withNotify($notify);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function changeCodeStillValid($user): bool
    {
        return $user->profile_change_code_sent_at
            && $user->profile_change_code_sent_at->addMinutes(self::OTP_EXPIRY_MINUTES)->isFuture();
    }
}
