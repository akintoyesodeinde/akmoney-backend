<?php

namespace App\Http\Controllers\Api;

use App\Models\Form;
use App\Models\Country;
use App\Constants\Status;
use App\Models\ProfileAuditLog;
use App\Models\User;
use App\Models\BackupCode;
use App\Models\KycProvider;
use App\Models\SendMoney;
use App\Lib\FormProcessor;
use App\Models\DeviceToken;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\DeliveryMethod;
use App\Models\NotificationLog;
use App\Support\NotificationHelper;
use Illuminate\Validation\Rule;
use App\Lib\GoogleAuthenticator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function dashboard()
    {
        $user                           = auth()->user();
        $widget['balance']              = $user->balance;
        $sendMoney                      = SendMoney::filterUser()->with('recipientCountry');

        $transfers    = (clone $sendMoney)->with('deposit', 'deposit.gateway', 'recipientCountry', 'countryDeliveryMethod.deliveryMethod')
            ->latest()
            ->take(5)
            ->get();

        $widget['send_money_amount']    = (clone $sendMoney)->completed()->sum('base_currency_amount');
        $widget['send_money_pending']   = (clone $sendMoney)->pending()->sum('base_currency_amount');
        $widget['send_money_initiated'] = (clone $sendMoney)->initiated()->sum('base_currency_amount');
        $widget['payment_pending']      = (clone $sendMoney)->paymentPending()->sum('base_currency_amount');
        $widget['payment_rejected']     = (clone $sendMoney)->paymentRejected()->sum('base_currency_amount');

        $notify[] = 'User dashboard';
        return responseSuccess('dashboard', $notify, [
            'user' => $user,
            'transfers' => $transfers,
            'widget' => $widget,
        ]);
    }

    public function userDataSubmit(Request $request)
    {
        $user = auth()->user();
        if ($user->profile_complete == Status::YES) {
            $notify[] = 'You\'ve already completed your profile';
            return responseError('already_completed', $notify);
        }

        $countryData  = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));

        $validator = Validator::make($request->all(), [
            'country_code' => 'required|in:' . $countryCodes,
            'country'      => 'required|in:' . $countries,
            'mobile_code'  => 'required|in:' . $mobileCodes,
            'username'     => 'required|unique:users|min:6',
            'mobile'       => ['required', 'regex:/^([0-9]*)$/', Rule::unique('users')->where('dial_code', $request->mobile_code)],
        ]);

        if ($validator->fails()) {
            if ($validator->fails()) return responseError('validation_error', $validator->errors());
        }

        if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
            $notify[] = 'No special character, space or capital letters in username';
            return responseError('validation_error', $notify);
        }

        $user->country_code = $request->country_code;
        $user->mobile       = $request->mobile;
        $user->username     = $request->username;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->zip = $request->zip;
        $user->country_name = @$request->country;
        $user->dial_code = $request->mobile_code;
        $user->profile_complete = Status::YES;

        if ($user->type == Status::BUSINESS_USER) {
            $form           = Form::where('act', 'business-account.data')->first();
            $formData       = $form->form_data;
            $formProcessor  = new FormProcessor();
            $validationRule = $formProcessor->valueValidation($formData);
            $validator = Validator::make($request->all(), $validationRule);
            if ($validator->fails()) {
                return responseError('validation_error', $validator->errors());
            }
            $userData                    = $formProcessor->processFormData($request, $formData);
            $user->business_profile_data = $userData;
        }

        $user->save();

        $notify[] = 'Profile completed successfully';
        return responseSuccess('profile_completed', $notify, ['user' => $user]);
    }

    public function businessUserForm()
    {
        if (auth()->user()) {
            $form = Form::Where('act', 'business-account.data')->first();
        }
        $notify[] = 'Business profile field is below';
        return responseSuccess('business_profile_form', $notify, [
            'form' => $form->form_data
        ]);
    }

    public function kycForm()
    {
        $user = auth()->user();

        if ($user->kv == Status::KYC_PENDING) {
            $notify[] = 'Your KYC is under review';
            return responseError('under_review', $notify);
        }
        if ($user->kv == Status::KYC_VERIFIED) {
            $notify[] = 'You are already KYC verified';
            return responseError('already_verified', $notify);
        }

        $isManual = KycProvider::where('slug', 'manual')->where('is_enabled', true)->exists();

        if ($isManual) {
            if ($user->type == Status::BUSINESS_USER) {
                $form = Form::where('act', 'business-user.kyc')->first();
            } else {
                $form = Form::where('act', 'personal-user.kyc')->first();
            }
            $notify[] = 'KYC field is below';
            return responseSuccess('kyc_form', $notify, [
                'kyc_type' => 'manual',
                'form'     => $form->form_data,
            ]);
        }

        $provider = KycProvider::where('is_enabled', true)->first();
        if (!$provider) {
            $notify[] = 'No KYC provider is currently enabled. Please contact support.';
            return responseError('no_provider', $notify);
        }

        $user->kv = Status::KYC_PENDING;
        $user->save();

        if ($provider->slug === 'verif') {
            $response = Http::timeout(30)->withHeaders([
                'x-auth-client' => $provider->api_key,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->post('https://stationapi.veriff.com/v1/sessions', [
                'verification' => [
                    'vendorData' => (string) $user->id,
                    'person'     => [
                        'firstName' => $user->firstname,
                        'lastName'  => $user->lastname,
                    ],
                ],
            ]);

            if (!$response->successful()) {
                Log::error('Veriff session creation failed (API)', [
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                    'user_id' => $user->id,
                ]);
                $user->kv = 0;
                $user->save();
                $notify[] = 'Unable to start Veriff verification. Please try again.';
                return responseError('veriff_error', $notify);
            }

            $sessionId  = $response['verification']['id'];
            $sessionUrl = $response['verification']['url'];
            Cache::put('veriff_session_' . $sessionId, $user->id, now()->addHours(2));

            $notify[] = 'Veriff KYC session created';
            return responseSuccess('kyc_form', $notify, [
                'kyc_type'    => 'veriff',
                'session_url' => $sessionUrl,
            ]);
        }

        if ($provider->slug === 'persona') {
            $notify[] = 'Persona KYC session created';
            return responseSuccess('kyc_form', $notify, [
                'kyc_type'    => 'persona',
                'template_id' => $provider->api_key,
                'reference_id' => (string) $user->id,
            ]);
        }

        if ($provider->slug === 'complycube') {
            $client = Http::timeout(30)->withToken($provider->api_key)
                ->post('https://api.complycube.com/v1/clients', [
                    'reference'     => (string) $user->id,
                    'type'          => 'person',
                    'personDetails' => [
                        'firstName' => $user->firstname,
                        'lastName'  => $user->lastname,
                        'email'     => $user->email,
                    ],
                ]);

            if (!$client->successful()) {
                $user->kv = 0;
                $user->save();
                $notify[] = 'Unable to create ComplyCube client.';
                return responseError('complycube_error', $notify);
            }

            $check = Http::timeout(30)->withToken($provider->api_key)
                ->post('https://api.complycube.com/v1/checks', [
                    'clientId'    => $client['id'],
                    'type'        => 'identity_check',
                    'callbackUrl' => route('user.kyc.webhook', ['provider' => 'complycube']),
                ]);

            if (!$check->successful()) {
                $user->kv = 0;
                $user->save();
                $notify[] = 'Unable to start ComplyCube verification.';
                return responseError('complycube_error', $notify);
            }

            $notify[] = 'ComplyCube KYC session created';
            return responseSuccess('kyc_form', $notify, [
                'kyc_type'    => 'complycube',
                'session_url' => $check['redirectUrl'],
            ]);
        }

        // Fallback for unknown providers
        $user->kv = 0;
        $user->save();
        $notify[] = 'KYC provider not supported on this platform. Please use the web portal.';
        return responseError('unsupported_provider', $notify);
    }

    public function kycSubmit(Request $request)
    {
        $user = auth()->user();
        if ($user->type == Status::BUSINESS_USER) {
            $form = Form::Where('act', 'business-user.kyc')->first();
        } else {
            $form = Form::Where('act', 'personal-user.kyc')->first();
        }
        if (!$form) {
            $notify[] = 'Invalid KYC request';
            return responseError('invalid_request', $notify);
        }
        $formData = $form->form_data;
        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);

        $validator = Validator::make($request->all(), $validationRule);
        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        foreach (@$user->kyc_data ?? [] as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
            }
        }
        $userData = $formProcessor->processFormData($request, $formData);

        $user->kyc_data = $userData;
        $user->kyc_rejection_reason = null;
        $user->kv = Status::KYC_PENDING;
        $user->save();

        $notify[] = 'KYC data submitted successfully';
        return responseSuccess('kyc_submitted', $notify, ['kyc_data' => $user->kyc_data]);
    }

    public function depositHistory(Request $request)
    {
        $deposits = auth()->user()->deposits();
        if ($request->search) {
            $deposits = $deposits->where('trx', $request->search);
        }
        $deposits = $deposits->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        $notify[] = 'Payment data';
        return responseSuccess('deposits', $notify, ['deposits' => $deposits]);
    }

    public function transactions(Request $request)
    {
        $remarks = Transaction::distinct('remark')->get('remark');
        $transactions = Transaction::where('user_id', auth()->id());

        if ($request->search) {
            $transactions = $transactions->where('trx', $request->search);
        }

        if ($request->type) {
            $type = $request->type == 'plus' ? '+' : '-';
            $transactions = $transactions->where('trx_type', $type);
        }

        if ($request->remark) {
            $transactions = $transactions->where('remark', $request->remark);
        }

        $transactions = $transactions->orderBy('id', 'desc')->paginate(getPaginate());
        $notify[] = 'Transactions data';
        return responseSuccess('transactions', $notify, [
            'transactions' => $transactions,
            'remarks' => $remarks,
        ]);
    }

    public function submitProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname'  => 'required',
        ], [
            'firstname.required' => 'The first name field is required',
            'lastname.required'  => 'The last name field is required',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

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
        $user->save();

        if (!empty($changed)) {
            notify($user, 'PROFILE_CHANGED', [
                'changed_fields' => implode(', ', $changed),
                'time'           => now()->format('Y-m-d H:i:s') . ' UTC',
                'ip'             => request()->ip(),
                'browser'        => osBrowser()['browser'] ?? 'Unknown',
            ], ['email']);
        }

        $notify[] = 'Profile updated successfully';
        return responseSuccess('profile_updated', $notify, ['user' => $user]);
    }

    // ─── Email Change ─────────────────────────────────────────────────────────

    public function requestEmailChange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_email' => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user = auth()->user();

        if ($user->pending_email === $request->new_email
            && $user->profile_change_code_sent_at
            && $user->profile_change_code_sent_at->addMinutes(10)->isFuture()) {
            $delay = (int) $user->profile_change_code_sent_at->addMinutes(10)->diffInSeconds(now());
            $notify[] = 'A code was already sent';
            return responseError('too_soon', $notify, ['retry_after_seconds' => $delay]);
        }

        if ($user->email_change_at && $user->email_change_at->addMinutes(60)->isFuture()) {
            $remaining = (int) now()->diffInMinutes($user->email_change_at->addMinutes(60));
            $notify[] = "Email change is on cooldown. Try again in {$remaining} minute(s).";
            return responseError('cooldown', $notify, ['cooldown_minutes' => $remaining]);
        }

        $code = verificationCode(6);
        $user->pending_email                = $request->new_email;
        $user->profile_change_code          = $code;
        $user->profile_change_code_sent_at  = now();
        $user->save();

        ProfileAuditLog::record($user->id, 'email_change_requested', 'email', $user->email, $request->new_email);

        // OTP to new email
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

        // Security alert to old email
        notify($user, 'EMAIL_CHANGE_ALERT', [
            'new_email' => $request->new_email,
            'time'      => now()->format('Y-m-d H:i:s') . ' UTC',
            'ip'        => request()->ip(),
            'browser'   => osBrowser()['browser'] ?? 'Unknown',
        ], ['email']);

        $notify[] = 'Verification code sent to new email. Security alert sent to current email.';
        return responseSuccess('email_change_otp_sent', $notify, [
            'pending_email'          => $request->new_email,
            'profile_change_code_sent_at' => $user->profile_change_code_sent_at,
        ]);
    }

    public function verifyEmailChange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user = auth()->user();

        if (!$user->pending_email) {
            $notify[] = 'No pending email change found';
            return responseError('not_found', $notify);
        }

        if (!$user->profile_change_code_sent_at || $user->profile_change_code_sent_at->addMinutes(10)->isPast()) {
            $notify[] = 'Verification code has expired. Please request a new one.';
            return responseError('code_expired', $notify);
        }

        if ($user->profile_change_code !== $request->code) {
            ProfileAuditLog::record($user->id, 'email_change_failed', 'code', null, 'Wrong code entered');
            $notify[] = 'Verification code does not match';
            return responseError('validation_error', $notify);
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

        $notify[] = 'Email address updated successfully';
        return responseSuccess('email_changed', $notify, ['user' => $user]);
    }

    // ─── Phone Change ─────────────────────────────────────────────────────────

    public function requestMobileChange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile'      => 'required|string',
            'mobile_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user = auth()->user();

        if ($user->mobile_change_at && $user->mobile_change_at->addMinutes(60)->isFuture()) {
            $remaining = (int) now()->diffInMinutes($user->mobile_change_at->addMinutes(60));
            $notify[] = "Phone change is on cooldown. Try again in {$remaining} minute(s).";
            return responseError('cooldown', $notify, ['cooldown_minutes' => $remaining]);
        }

        $exists = User::where('mobile', $request->mobile)
            ->where('mobile_code', $request->mobile_code)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($exists) {
            $notify[] = 'This phone number is already registered to another account';
            return responseError('validation_error', $notify);
        }

        $code = verificationCode(6);
        $user->pending_mobile               = $request->mobile;
        $user->pending_mobile_code          = $request->mobile_code;
        $user->profile_change_code          = $code;
        $user->profile_change_code_sent_at  = now();
        $user->save();

        ProfileAuditLog::record($user->id, 'mobile_change_requested', 'mobile', $user->mobileNumber, '+' . $request->mobile_code . $request->mobile);

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

        notify($user, 'PHONE_CHANGE_ALERT', [
            'new_mobile' => '+' . $request->mobile_code . $request->mobile,
            'time'       => now()->format('Y-m-d H:i:s') . ' UTC',
            'ip'         => request()->ip(),
        ], ['email']);

        $notify[] = 'Verification code sent via SMS to new number. Security alert sent to your email.';
        return responseSuccess('phone_change_otp_sent', $notify, [
            'pending_mobile'             => $request->mobile_code . $request->mobile,
            'profile_change_code_sent_at' => $user->profile_change_code_sent_at,
        ]);
    }

    public function verifyMobileChange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user = auth()->user();

        if (!$user->pending_mobile) {
            $notify[] = 'No pending phone change found';
            return responseError('not_found', $notify);
        }

        if (!$user->profile_change_code_sent_at || $user->profile_change_code_sent_at->addMinutes(10)->isPast()) {
            $notify[] = 'Verification code has expired. Please request a new one.';
            return responseError('code_expired', $notify);
        }

        if ($user->profile_change_code !== $request->code) {
            ProfileAuditLog::record($user->id, 'mobile_change_failed', 'code', null, 'Wrong code entered');
            $notify[] = 'Verification code does not match';
            return responseError('validation_error', $notify);
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

        $notify[] = 'Phone number updated successfully';
        return responseSuccess('mobile_changed', $notify, ['user' => $user]);
    }

    public function submitPassword(Request $request)
    {
        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => ['required', 'confirmed', $passwordValidation]
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user = auth()->user();
        if (Hash::check($request->current_password, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->save();

            // Revoke all other tokens so other devices are signed out
            $currentTokenId = $user->currentAccessToken()->id;
            $user->tokens()->where('id', '!=', $currentTokenId)->delete();

            $notify[] = 'Password changed successfully';
            return responseSuccess('password_changed', $notify);
        } else {
            $notify[] = 'The password doesn\'t match!';
            return responseError('validation_error', $notify);
        }
    }

    public function addDeviceToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            $notify[] = 'Token already exists';
            return responseError('token_exists', $notify);
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->user_id = auth()->user()->id;
        $deviceToken->token   = $request->token;
        $deviceToken->is_app  = Status::YES;
        $deviceToken->save();

        $notify[] = 'Token saved successfully';
        return responseSuccess('token_saved', $notify);
    }

    public function show2faForm()
    {
        $ga     = new GoogleAuthenticator();
        $user   = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl(
            $user->username . '@' . gs('site_name'),
            $secret,
            gs('site_name')
        );

        $notify[] = '2FA Setup';
        return responseSuccess('2fa_setup', $notify, [
            'method'           => 'totp',
            'secret'           => $secret,
            'qr_code_url'      => $qrCodeUrl,
            'backup_codes_remaining' => $user->backupCodes()->unused()->count(),
        ]);
    }

    public function setupSmsTwoFa()
    {
        $user = auth()->user();

        if (!$user->sv) {
            $notify[] = 'Mobile number must be verified before enabling SMS two-factor authentication';
            return responseError('mobile_unverified', $notify);
        }

        // Send an SMS confirmation code (reuses the ver_code mechanism)
        $codeValid = $user->ver_code_send_at && $user->ver_code_send_at->addMinutes(2) > now();
        if ($codeValid) {
            $delay = $user->ver_code_send_at->addMinutes(2)->diffInSeconds(now());
            $notify[] = 'Please wait ' . $delay . ' seconds before requesting another code';
            return responseError('too_many_requests', $notify, ['ver_code_send_at' => $user->ver_code_send_at]);
        }

        $user->ver_code         = verificationCode(6);
        $user->ver_code_send_at = now();
        $user->save();
        notify($user, 'SVER_CODE', ['code' => $user->ver_code], ['sms']);

        $notify[] = 'A confirmation code has been sent to your registered mobile number';
        return responseSuccess('sms_2fa_code_sent', $notify, [
            'method'          => 'sms',
            'ver_code_send_at' => $user->ver_code_send_at,
        ]);
    }

    public function create2fa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'method' => 'required|in:totp,sms',
            'code'   => 'required',
            'secret' => 'required_if:method,totp',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        /** @var \App\Models\User $user */
        $user   = auth()->user();
        $method = $request->method;

        if ($method === 'sms') {
            // Verify the SMS confirmation code sent by setupSmsTwoFa()
            $expired = !$user->ver_code_send_at || $user->ver_code_send_at->addMinutes(10) < now();
            if ($expired) {
                $notify[] = 'The confirmation code has expired. Please request a new one.';
                return responseError('code_expired', $notify);
            }
            if ($user->ver_code != $request->code) {
                $notify[] = 'Invalid confirmation code';
                return responseError('wrong_verification', $notify);
            }

            $user->tsc              = null;
            $user->ts               = Status::ENABLE;
            $user->tv               = Status::VERIFIED;
            $user->two_fa_method    = 'sms';
            $user->ver_code         = null;
            $user->ver_code_send_at = null;
            $user->save();

            $backupCodes = $this->generateBackupCodes($user);

            $notify[] = 'SMS two-factor authentication enabled successfully';
            return responseSuccess('2fa_enabled', $notify, [
                'method'       => 'sms',
                'backup_codes' => $backupCodes,
            ]);
        }

        // TOTP method
        $response = verifyG2fa($user, $request->code, $request->secret);
        if (!$response) {
            $notify[] = 'Wrong verification code';
            return responseError('wrong_verification', $notify);
        }

        $user->tsc           = $request->secret;
        $user->ts            = Status::ENABLE;
        $user->tv            = Status::VERIFIED;
        $user->two_fa_method = 'totp';
        $user->save();

        $backupCodes = $this->generateBackupCodes($user);

        $notify[] = 'Authenticator app two-factor authentication enabled successfully';
        return responseSuccess('2fa_enabled', $notify, [
            'method'       => 'totp',
            'backup_codes' => $backupCodes,
        ]);
    }

    public function disable2fa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        /** @var \App\Models\User $user */
        $user     = auth()->user();
        $verified = false;

        // Allow disabling via backup code
        if ($request->has('backup_code')) {
            $verified = $this->consumeBackupCode($user, $request->input('backup_code'));
        } elseif ($user->two_fa_method === 'sms') {
            $expired = !$user->ver_code_send_at || $user->ver_code_send_at->addMinutes(10) < now();
            $verified = !$expired && $user->ver_code == $request->code;
            if ($verified) {
                $user->ver_code         = null;
                $user->ver_code_send_at = null;
            }
        } else {
            $verified = verifyG2fa($user, $request->code);
        }

        if (!$verified) {
            $notify[] = 'Wrong verification code';
            return responseError('wrong_verification', $notify);
        }

        $user->tsc           = null;
        $user->ts            = Status::DISABLE;
        $user->two_fa_method = null;
        $user->save();

        // Revoke all backup codes
        $user->backupCodes()->delete();

        $notify[] = 'Two-factor authentication disabled successfully';
        return responseSuccess('2fa_disabled', $notify);
    }

    public function backupCodes()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$user->ts) {
            $notify[] = 'Two-factor authentication is not enabled';
            return responseError('2fa_not_enabled', $notify);
        }

        $remaining = $user->backupCodes()->unused()->count();

        $notify[] = 'Backup codes info';
        return responseSuccess('backup_codes', $notify, [
            'remaining' => $remaining,
        ]);
    }

    public function regenerateBackupCodes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$user->ts) {
            $notify[] = 'Two-factor authentication is not enabled';
            return responseError('2fa_not_enabled', $notify);
        }

        // Verify identity before regenerating (TOTP or SMS)
        $verified = false;
        if ($user->two_fa_method === 'sms') {
            $expired  = !$user->ver_code_send_at || $user->ver_code_send_at->addMinutes(10) < now();
            $verified = !$expired && $user->ver_code == $request->code;
        } else {
            $verified = verifyG2fa($user, $request->code);
        }

        if (!$verified) {
            $notify[] = 'Wrong verification code';
            return responseError('wrong_verification', $notify);
        }

        $backupCodes = $this->generateBackupCodes($user);

        $notify[] = 'Backup codes regenerated. Store them safely — they will not be shown again.';
        return responseSuccess('backup_codes_regenerated', $notify, [
            'backup_codes' => $backupCodes,
        ]);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function generateBackupCodes(\App\Models\User $user): array
    {
        $user->backupCodes()->delete();

        $chars     = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $charLen   = strlen($chars);
        $plainCodes = [];

        for ($i = 0; $i < 8; $i++) {
            $part1 = $part2 = '';
            for ($j = 0; $j < 5; $j++) {
                $part1 .= $chars[random_int(0, $charLen - 1)];
                $part2 .= $chars[random_int(0, $charLen - 1)];
            }
            $plain = $part1 . '-' . $part2;
            $plainCodes[] = $plain;

            BackupCode::create([
                'user_id' => $user->id,
                'code'    => hash('sha256', $plain),
            ]);
        }

        return $plainCodes;
    }

    private function consumeBackupCode(\App\Models\User $user, string $plain): bool
    {
        $hashed = hash('sha256', strtoupper(str_replace(' ', '', $plain)));
        $code   = $user->backupCodes()->unused()->where('code', $hashed)->first();

        if (!$code) {
            return false;
        }

        $code->used_at = now();
        $code->save();
        return true;
    }

    public function pushNotifications()
    {
        $notifications = NotificationLog::where('user_id', auth()->id())->where('sender', 'firebase')->orderBy('id', 'desc')->paginate(getPaginate());
        $notify[] = 'Push notifications';
        return responseSuccess('notifications', $notify, [
            'notifications' => $notifications,
        ]);
    }

    public function pushNotificationsRead($id)
    {
        $notification = NotificationLog::where('user_id', auth()->id())->where('sender', 'firebase')->find($id);
        if (!$notification) {
            $notify[] = 'Notification not found';
            return responseError('notification_not_found', $notify);
        }
        $notify[] = 'Notification marked as read successfully';
        $notification->user_read = 1;
        $notification->save();

        return responseSuccess('notification_read', $notify);
    }

    public function notifications()
    {
        $data    = NotificationHelper::get();
        $notify[] = 'Notifications';
        return responseSuccess('notifications', $notify, $data);
    }

    public function notificationsRead($id = null)
    {
        NotificationHelper::markAsRead($id ? (int) $id : null);
        $notify[] = 'Notifications marked as read';
        return responseSuccess('notifications_read', $notify);
    }

    public function userInfo()
    {
        $user     = auth()->user();
        $notify[] = 'User information';
        return responseSuccess('user_info', $notify, [
            'user'                => $user,
            'onboarding_progress' => $user->getOnboardingProgress(),
            'kyc_status'          => $user->getKycStatus(),
        ]);
    }

    public function deleteAccount()
    {
        $user = auth()->user();
        $user->username = 'deleted_' . $user->username;
        $user->email = 'deleted_' . $user->email;
        $user->save();

        $user->tokens()->delete();

        $notify[] = 'Account deleted successfully';
        return responseSuccess('account_deleted', $notify);
    }

    public function transactionDetail($id)
    {
        $transaction = Transaction::where('user_id', auth()->id())->find($id);
        if (!$transaction) {
            $notify[] =  'Invalid request';
            return responseError('transaction_detail', $notify);
        }
        $notify[] = 'Transaction successfull';
        return responseSuccess('transaction_detail', $notify, ['transaction' => $transaction]);
    }

    public function home(Request $request)
    {
        if ($request->has('reference')) {
            session()->put('reference', $request->input('reference'));
        }

        $sendingCountries = Country::active()->sending()->with('conversionRates')->get();
        $receivingCountries = Country::receivableCountries()->get();

        $notify[] = ['Home screen'];
        return responseSuccess('home_screen', $notify, [
            'sending_countries' => $sendingCountries,
            'receiving_countries' => $receivingCountries,
        ]);
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'sending_amount' => 'required|numeric|gt:0',
            'recipient_amount'    => 'required|numeric|gt:0',
            'sending_country' => 'required|integer',
            'recipient_country' => 'required|integer',
            'delivery_method' => 'nullable|integer',
        ]);

        $sendingCountry = Country::active()->sending()->find($request->sending_country);
        if (!$sendingCountry) {
            $notify[] = ['Sending country not found'];
            return reponseError('calculate', $notify);
        }
        $recipientCountry = Country::active()->receiving()->find($request->recipient_country);
        if (!$recipientCountry) {
            $notify[] = ['Recipient country not found'];
            return reponseError('calculate', $notify);
        }

        if ($request->delivery_method) {
            $deliveryMethod = DeliveryMethod::active()->find($request->delivery_method);
            if (!$deliveryMethod) {
                $notify[] = ['Delivery method not found'];
                return reponseError('calculate', $notify);
            }
        }

        $send_money = [
            'sending_amount'    => $request->sending_amount,
            'recipient_amount'  => $request->recipient_amount,
            'sending_country'   => $request->sending_country,
            'recipient_country' => $request->recipient_country,
            'delivery_method'   => $request->delivery_method,
        ];

        session()->put('send_money', $send_money);
        $notify[] = ['Calculation'];
        return responseSuccess('calculate', $notify, [
            'send_money' => $send_money
        ]);
    }

    public function kycData()
    {
        $user = auth()->user();
        $kycData = $user->kyc_data ?? [];
        $kycValues = [];
        foreach ($kycData as $kycInfo) {
            if (!$kycInfo->value) {
                continue;
            }
            if ($kycInfo->type == 'checkbox') {
                $value = implode(', ', $kycInfo->value);
            } elseif ($kycInfo->type == 'file') {
                $value = encrypt(getFilePath('verify') . '/' . $kycInfo->value);
            } else {
                $value = $kycInfo->value;
            }

            $kycValues[] = [
                'name' => $kycInfo->name,
                'type' => $kycInfo->type,
                'value' => $value
            ];
        }
        $notify[] = 'KYC data';
        return responseSuccess('kyc_data', $notify, [
            'kyc_status' => $user->getKycStatus(),
            'kyc_data'   => $kycValues,
        ]);
    }

    public function downloadAttachment($fileHash)
    {

        try {
            $filePath = decrypt($fileHash);
        } catch (\Exception $e) {
            $notify[] = 'Invalid file';
            return responseError('invalid_failed', $notify);
        }
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title = slug(gs('site_name')) . '-attachments.' . $extension;
        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = 'File downloaded failed';
            return responseError('download_failed', $notify);
        }
        if (!headers_sent()) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET,');
            header('Access-Control-Allow-Headers: Content-Type');
        }
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }
}
