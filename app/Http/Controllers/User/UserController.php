<?php

namespace App\Http\Controllers\User;

use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\Form;
use App\Constants\Status;
use App\Models\SendMoney;
use App\Lib\FormProcessor;
use App\Models\DeviceToken;
use App\Models\Country;
use App\Models\Recipient;
use App\Models\User;
use App\Models\KycProvider;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Lib\GoogleAuthenticator;
use App\Http\Controllers\Controller;
use App\Models\WalletTransfer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;
use Yoti\DocScan\DocScanClient;
use Yoti\DocScan\Session\Create\Check\RequestedDocumentAuthenticityCheckBuilder;
use Yoti\DocScan\Session\Create\Check\RequestedFaceMatchCheckBuilder;
use Yoti\DocScan\Session\Create\Check\RequestedLivenessCheckBuilder;
use Yoti\DocScan\Session\Create\SdkConfigBuilder;
use Yoti\DocScan\Session\Create\SessionSpecificationBuilder;
use Yoti\DocScan\Session\Create\Task\RequestedTextExtractionTaskBuilder;
use Yoti\DocScan\Session\Create\NotificationConfigBuilder;
use Yoti\Util\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function viewAttachment(string $path)
    {
        $fullPath = decrypt($path);
    
        // 🔒 Security: ensure file is inside verify directory
        $verifyPath = realpath(getFilePath('verify'));
        $realFile  = realpath($fullPath);
    
        if (!$realFile || !str_starts_with($realFile, $verifyPath)) {
            abort(403, 'Unauthorized file access');
        }
    
        if (!file_exists($realFile)) {
            abort(404, 'File not found');
        }
    
        $mime = mime_content_type($realFile);
    
        // Inline view for images & PDFs
        if (str_starts_with($mime, 'image/') || $mime === 'application/pdf') {
            return response()->file($realFile, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline',
            ]);
        }
    
        // Fallback: download
        return response()->download($realFile);
    }


    public function downloadReceipt($id)
    {
        $transfer = SendMoney::with([
            'deposit.gateway',
            'sendingCountry',
            'recipientCountry',
            'countryDeliveryMethod.deliveryMethod',
            'service',
            'sourceOfFund',
            'sendingPurpose',
        ])->where('user_id', auth()->id())->findOrFail($id);

        $pdf = Pdf::loadView('templates.basic.user.receipt', compact('transfer'));
        return $pdf->download('receipt-' . $transfer->mtcn_number . '.pdf');
    }
    public function updateTheme(Request $request)
    {
        $request->validate([
            'theme_mode' => 'required|in:light,dark',
        ]);
    
        $user = auth()->user();
        $user->current_mode = $request->theme_mode;
        $user->save();
    
        return back();
    }

    public function saveReps(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'mobile'  => 'nullable|string|max:20',
            'email'   => [
                'nullable', 'email',
                Rule::unique('recipients')->where(fn($q) => $q->where('user_id', auth()->id())),
            ],
            'address'              => 'nullable|string|max:500',
            'country_id' => 'nullable|integer|exists:countries,id',
            'delivery_method_id'   => 'nullable|integer',
            'service_id'           => 'nullable|integer',
            'service_form_data'    => 'nullable|string',
        ]);

        $formData = null;
        if ($request->filled('service_form_data')) {
            $decoded = json_decode($request->service_form_data, true);
            $formData = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }

        Recipient::create([
            'user_id'              => auth()->id(),
            'name'                 => $request->name,
            'mobile'               => $request->mobile,
            'email'                => $request->email,
            'address'              => $request->address,
            'country_id' => $request->country_id ?: null,
            'delivery_method_id'   => $request->delivery_method_id   ?: null,
            'service_id'           => $request->service_id           ?: null,
            'service_form_data'    => $formData,
        ]);

        return back()->withNotify([['success', 'Recipient Created Successfully']]);
    }
    
    public function updateRep(Request $request, Recipient $recipient)
    {
        abort_if($recipient->user_id != auth()->user()->id, 403);

        $request->validate([
            'name'    => 'required|string|max:255',
            'mobile'  => 'nullable|string|max:20',
            'email'   => 'nullable|email',
            'address' => 'nullable|string|max:500',
            'country_id' => 'nullable|integer',
            'delivery_method_id'   => 'nullable|integer',
            'service_id'           => 'nullable|integer',
            'service_form_data'    => 'nullable|string',
        ]);

        $formData = $recipient->service_form_data;
        if ($request->filled('service_form_data')) {
            $decoded = json_decode($request->service_form_data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $formData = $decoded;
            }
        }

        $recipient->update([
            'name'                 => $request->name,
            'mobile'               => $request->mobile,
            'email'                => $request->email,
            'address'              => $request->address,
            'country_id' => $request->country_id ?: null,
            'delivery_method_id'   => $request->delivery_method_id   ?: null,
            'service_id'           => $request->service_id           ?: null,
            'service_form_data'    => $formData,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Recipient updated successfully']);
        }

        return back()->withNotify([['success', 'Recipient updated successfully']]);
    }
    
    public function deleteRep(Recipient $recipient)
    {
        abort_if($recipient->user_id != auth()->id(), 403);
    
        $recipient->delete();

        return response()->json([
            'success' => true,
            'message' => 'Recipient deleted successfully',
        ]);
    }
    
    public function track(string $mtcn)
    {
        $transfer = SendMoney::with([
                'recipientCountry',
                'deposit.gateway',
                'countryDeliveryMethod.deliveryMethod',
            ])
            ->filterUser()
            ->where('mtcn_number', $mtcn)
            ->first();
    
        if (! $transfer) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }
    
        return response()->json([
            'success' => true,
            'data' => [
                'mtcn' => $transfer->mtcn_number,
                'recipient' => $transfer->recipient->name,
                'amount' => showAmount($transfer->sending_amount).' '.$transfer->sending_currency,
                'status' => ucfirst($transfer->statusBadge['text']),
                'statusColor' => $transfer->statusBadge['bg'].' '.$transfer->statusBadge['textColor'],
                'statusIcon' => $transfer->statusBadge['dot'],
                'date' => showDateTime($transfer->created_at),
                'details' => $transfer->detail != null ? json_encode($transfer->detail) : null,
                'timeline' => $this->buildTimeline($transfer)
            ],
        ]);
    }
    
    private function buildTimeline(SendMoney $t): array
    {
        $status = $t->status;
    
        return [
            [
                'step' => 'Transfer Initiated',
                'time' => showDateTime($t->created_at),
                'icon' => 'ri-send-plane-fill',
                'completed' => true,
            ],
    
            [
                'step' => 'Awaiting Processing',
                'time' => $status >= Status::SEND_MONEY_PENDING ? 'In progress' : 'Pending',
                'icon' => 'ri-time-line',
                'completed' => in_array($status, [
                    Status::SEND_MONEY_PENDING,
                    Status::SEND_MONEY_COMPLETED,
                    Status::SEND_MONEY_REFUNDED,
                ]),
            ],
    
            [
                'step' => 'Transfer Processing',
                'time' => $status == Status::SEND_MONEY_PENDING ? 'Processing' : 'Pending',
                'icon' => 'ri-loader-4-line',
                'completed' => in_array($status, [
                    Status::SEND_MONEY_COMPLETED,
                    Status::SEND_MONEY_REFUNDED,
                ]),
            ],
    
            $status == Status::SEND_MONEY_REFUNDED
                ? [
                    'step' => 'Refunded',
                    'time' => 'Completed',
                    'icon' => 'ri-arrow-go-back-line',
                    'completed' => true,
                ]
                : [
                    'step' => 'Completed',
                    'time' => $status == Status::SEND_MONEY_COMPLETED ? 'Completed' : 'Pending',
                    'icon' => 'ri-checkbox-circle-fill',
                    'completed' => $status == Status::SEND_MONEY_COMPLETED,
                ],
        ];
    }
    
    public function home()
    {
        $pageTitle = 'Dashboard';
        $user                           = auth()->user();
        $widget['balance']              = $user->balance;
        $sendMoney                      = SendMoney::filterUser()->with('recipientCountry');
        $transferss                      = (clone $sendMoney)->latest()->with('deposit')->take(5)->get();
        $widget['send_money_amount']    = (clone $sendMoney)->completed()->sum('base_currency_amount');
        $transactions = Transaction::where('user_id', auth()->id())->searchable(['trx'])->filter(['trx_type', 'remark'])->orderBy('id', 'desc')->paginate(getPaginate());
        $lastTransaction = Transaction::where('user_id', auth()->id())->where('trx_type', '+')->searchable(['trx'])->filter(['trx_type', 'remark'])->orderBy('created_at', 'desc')->first('amount') ?? 0;
        $widget['send_money_pending']   = (clone $sendMoney)->pending()->sum('base_currency_amount');
        $widget['send_money_initiated'] = (clone $sendMoney)->initiated()->sum('base_currency_amount');
        $widget['payment_pending']      = (clone $sendMoney)->paymentPending()->sum('base_currency_amount');
        $widget['payment_rejected']     = (clone $sendMoney)->paymentRejected()->sum('base_currency_amount');
        $rep = Recipient::where('user_id', $user->id)->get();
        $recentTransfers = SendMoney::where('user_id', auth()->id())
            ->whereIn('status', [
                Status::SEND_MONEY_PENDING,
                Status::SEND_MONEY_COMPLETED,
            ])
            ->latest()
            ->get();
        $rep = $rep->map(function ($recipient) use ($recentTransfers) {

            $lastTransfer = $recentTransfers->first(function ($transfer) use ($recipient) {
                return data_get($transfer->recipient, 'email') === $recipient->email
                    || data_get($transfer->recipient, 'mobile') === $recipient->mobile;
            });
        
            $recipient->last_amount_sent = $lastTransfer
                ? $lastTransfer->recipient_amount // or base_currency_amount
                : null;
        
            $recipient->last_sent_at = $lastTransfer?->created_at;
        
            return $recipient;
        });


        $transfers    = SendMoney::with('deposit.gateway', 'recipientCountry', 'countryDeliveryMethod.deliveryMethod')->filterUser()->latest()->take(5)->get();
        $countries   = Country::active()->with('conversionRates')->get();
        $tocountry   = Country::active()->with('conversionRates')->first();
        $countries = $countries->map(function ($c) {
            $c->image_url = getImage(getFilePath('country') . '/' . $c->image, getFileSize('country'));
            return $c;
        });
        $tocountry->image_url = getImage(getFilePath('country') . '/' . $tocountry->image, getFileSize('country'));

        $pendingReceiptDeposit = Deposit::where('user_id', auth()->id())
            ->where('method_code', 511)
            ->where('receipt_required', 1)
            ->whereNull('receipt_path')
            ->latest()
            ->first();

        return view('Template::user.dashboard', compact('pageTitle', 'user', 'widget', 'transferss', 'transactions', 'lastTransaction', 'rep', 'transfers', 'countries', 'tocountry', 'pendingReceiptDeposit'));
    }

    public function uploadDepositReceipt(Request $request, $id)
    {
        $deposit = Deposit::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('method_code', 511)
            ->where('receipt_required', 1)
            ->whereNull('receipt_path')
            ->firstOrFail();

        $request->validate([
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $location = getFilePath('depositVerify');
        $filename = fileUploader($request->file('receipt'), $location);

        $deposit->receipt_path     = $filename;
        $deposit->receipt_required = 0;
        $deposit->save();

        AdminNotification::create([
            'title'     => 'Receipt uploaded for payment #' . $deposit->trx,
            'click_url' => route('admin.payment.details', $deposit->id),
            'module'    => 'payments',
        ]);

        $notify[] = ['success', 'Receipt uploaded successfully. Our team will review it shortly.'];
        return redirect()->route('user.home')->withNotify($notify);
    }

    public function setPin(Request $request)
    {
        try{
            $request->validate([
                'pin' => ['required', 'digits:6', 'confirmed'],
                'security_question_id' => 'required|string',
                'security_answer' => 'required|string|min:2',
            ]);
        
            $user = auth()->user();
            
            if ($user->pin) {
                // return back()->withNotify([['error', 'Pin already set']]);
                return response()->json([
                    'success' => false,
                    'message' => 'PIN already set'
                ]);
            }
            $user->update([
                'pin' => Hash::make($request->pin),
                'security_question_id' => $request->security_question_id,
                'security_answer_hash' => Hash::make(strtolower($request->security_answer)),
                'has_pin' => 1,
                'pin_set_at' => now(),
                'pin_failed_attempts' => 0,
                'pin_locked_until' => null,
            ]);
        
            // return back()->withNotify([['success', 'Pin successfully set']]);
            return response()->json([
                'success' => true,
                'message' => 'PIN successfully set'
            ]);
        } catch (\Exception $e){
            Log::error('setPin failed', ['user_id' => auth()->id(), 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to set PIN. Please try again.'], 500);
        }
    }

    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:6',
        ]);
    
        $user = auth()->user();
    
        if ($user->pin_locked_until && now()->lt($user->pin_locked_until)) {
            return back()->withNotify([['error', 'Pin temporarily locked']]);
        }
    
        if (! Hash::check($request->pin, $user->pin)) {
    
            $user->increment('pin_failed_attempts');
    
            if ($user->pin_failed_attempts >= 3) {
                $user->update([
                    'pin_locked_until' => now()->addMinutes(15),
                    'pin_failed_attempts' => 0,
                ]);
            }
    
            return response()->json([
                'success' => false
            ]);
        }
    
        $user->update([
            'pin_failed_attempts' => 0,
            'pin_locked_until' => null,
        ]);
    
        session(['pin_verified_at' => now()]);
    
        return response()->json([
            'success' => true
        ]);
    }
    
    public function verifySecurityAnswer(Request $request)
    {
        $request->validate([
            'security_answer' => 'required|string',
        ]);

        $user = auth()->user();

        // Require 2FA pre-verification if user has 2FA enabled
        if ($user->ts == 1 && ! session('pin_reset_2fa_verified')) {
            return back()->withNotify([['error', 'Please complete 2FA verification first.']]);
        }

        if (! Hash::check(
            strtolower($request->security_answer),
            $user->security_answer_hash
        )) {
            return back()->withNotify([['error', 'Security answer is incorrect.']]);
        }

        session(['pin_reset_verified' => true]);
        session()->forget('pin_reset_2fa_verified');

        return redirect()->route('user.reset_pin_form');
    }

    public function viewResetPinForm(Request $request)
    {
        if (! session()->has('pin_reset_verified')) {
            return redirect()->route('user.view_reset_pin')
                ->withNotify([['error', 'Please verify your identity first.']]);
        }
        $pageTitle = 'Reset Transaction PIN';
        $user      = auth()->user();
        return view('Template::user.reset-pin', compact('pageTitle', 'user'));
    }

    public function sendPinResetCode(Request $request)
    {
        $user = auth()->user();

        $user->ver_code         = verificationCode(6);
        $user->ver_code_send_at = now();
        $user->saveQuietly();

        notify($user, 'EVER_CODE', ['code' => $user->ver_code], ['email']);

        $masked = substr($user->email, 0, 3) . '***' . substr($user->email, strpos($user->email, '@'));
        return response()->json(['success' => true, 'message' => "Code sent to {$masked}"]);
    }

    public function verifyPinResetCode(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $user = auth()->user();

        if (! $user->ver_code || ! $user->ver_code_send_at) {
            return response()->json(['success' => false, 'message' => 'No code found. Please request a new one.']);
        }

        if (now()->diffInMinutes($user->ver_code_send_at) > 10) {
            $user->ver_code         = null;
            $user->ver_code_send_at = null;
            $user->saveQuietly();
            return response()->json(['success' => false, 'message' => 'Code expired. Please request a new one.']);
        }

        if ((string) $request->code !== (string) $user->ver_code) {
            return response()->json(['success' => false, 'message' => 'Invalid verification code.']);
        }

        $user->ver_code         = null;
        $user->ver_code_send_at = null;
        $user->saveQuietly();

        session(['pin_reset_2fa_verified' => true]);

        return response()->json(['success' => true]);
    }

    public function viewSetPin(Request $request)
    {
        if (auth()->user()->has_pin) {
            return redirect()->route('user.view_reset_pin');
        }
        $pageTitle = 'Set Transaction PIN';
        return view('Template::user.set-pin', compact('pageTitle'));
    }

    public function viewResetPin(Request $request)
    {
        $pageTitle  = 'Reset Transaction PIN';
        $user       = auth()->user();
        $requires2fa = $user->ts == 1;
        $tfa_verified = session('pin_reset_2fa_verified', false);
        return view('Template::user.account-forgot-pin', compact('pageTitle', 'user', 'requires2fa', 'tfa_verified'));
    }
    
    public function resetPin(Request $request)
    {
        if (! session()->has('pin_reset_verified')) {
            return back()->withNotify([['error', 'Unauthorized reset']]);
        }
    
        $request->validate([
            'pin' => 'required|digits:6|confirmed',
        ]);
    
        $user = auth()->user();
    
        $user->update([
            'pin' => Hash::make($request->pin),
            'pin_failed_attempts' => 0,
            'pin_locked_until' => null,
        ]);
    
        session()->forget('pin_reset_verified');
    
        return back()->withNotify([['success', 'Pin reset successful']]);
    }
    
    public function reps(Request $request)
    {
        $pageTitle = 'Recipients';
        $user      = auth()->user();
        $search    = $request->input('search');

        $reps = Recipient::where('user_id', $user->id)
            ->with('country:id,name', 'deliveryMethod:id,name', 'service:id,name')
            ->when($search, fn($q) => $q->where(fn($q2) => $q2
                ->where('name',  'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            ))
            ->latest()
            ->get();

        $receivingCountries = Country::receivableCountries()->get();

        return view('Template::user.recipients', compact(
            'pageTitle', 'reps', 'user', 'search', 'receivingCountries'
        ));
    }
    
    // public function reps(Request $request)
    // {
    //     $pageTitle = 'Recipients';
    //     $user = auth()->user();
    //     $reps = Recipient::where('user_id', $user->id)->get();
    //     return view('Template::user.recipients', compact('pageTitle', 'reps', 'user'));
    // }

    public function depositHistory(Request $request)
    {
        $pageTitle = 'Deposit History';
        $user = auth()->user();
        $deposits = auth()->user()->deposits()->searchable(['trx'])->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.deposit_history', compact('pageTitle', 'deposits', 'user'));
    }

    public function show2faForm()
    {
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . gs('site_name'), $secret);
        $pageTitle = '2FA Security';
        $user = auth()->user();
        return view('Template::user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl', 'user'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user, $request->code, $request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts = Status::ENABLE;
            $user->two_fa_method = 'totp';
            $user->save();
            $notify[] = ['success', 'Two factor authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function enable2faSms(Request $request)
    {
        $user = auth()->user();
        $user->ts = Status::ENABLE;
        $user->two_fa_method = 'sms';
        $user->tsc = null;
        $user->save();
        $notify[] = ['success', 'SMS two-factor authentication enabled'];
        return back()->withNotify($notify);
    }

    public function enable2faEmail(Request $request)
    {
        $user = auth()->user();
        $user->ts = Status::ENABLE;
        $user->two_fa_method = 'email';
        $user->tsc = null;
        $user->save();
        $notify[] = ['success', 'Email two-factor authentication enabled'];
        return back()->withNotify($notify);
    }

    public function disable2fa(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $user = auth()->user();
        $method = $user->two_fa_method ?? 'totp';

        if ($method === 'totp') {
            $response = verifyG2fa($user, $request->code);
        } else {
            $response = ($user->ver_code == $request->code);
        }

        if ($response) {
            $user->tsc = null;
            $user->ts = Status::DISABLE;
            $user->two_fa_method = null;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();
            $notify[] = ['success', 'Two-factor authentication disabled'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function transactions(Request $request)
    {
        $pageTitle = 'Transactions';
        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');
        $user = auth()->user();
        $transfers = SendMoney::where('user_id', $user->id)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('mtcn_number', 'like', '%' . $request->search . '%')
                       ->orWhere('trx', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->filled('payment_status'), function ($q) use ($request) {
                $q->where('payment_status', (int) $request->payment_status);
            })
            ->with('deposit.gateway', 'recipientCountry', 'countryDeliveryMethod.deliveryMethod', 'service')
            ->latest()
            ->paginate(getPaginate())
            ->withQueryString();
        $transactions = Transaction::where('user_id', $user->id)->searchable(['trx'])->filter(['trx_type', 'remark'])->orderBy('id', 'desc')->paginate(getPaginate());

        return view('Template::user.transactions', compact('pageTitle', 'transactions', 'remarks', 'user', 'transfers'));
    }

    public function kycForm()
    {
        // dd('Hi');
        $user = auth()->user();

        if ($user->kv === Status::KYC_PENDING || $user->kv === Status::KYC_VERIFIED) {
            return back()->withNotify([['error', 'KYC already in progress or completed']]);
        }
        
        $manual = KycProvider::where('slug', 'manual')->where('is_enabled', true)->exists();

        if ($manual) {
            // return $this->startManualKyc($user);
            $pageTitle = 'KYC Documents';
            $user = auth()->user();
    
            $form = Form::where('act', 'user.kyc')->first();
            return view('Template::user.kyc.form', compact('pageTitle', 'form', 'user'));
        }
    
        $provider = KycProvider::where('is_enabled', true)->first();
        if (!$provider) {
            // No KYC providers are enabled
            return back()->withNotify([['error', 'No KYC providers are currently enabled. Please contact support.']]);
        }
    
        $user->kv = Status::KYC_PENDING;
        $user->save();
    
        return match ($provider->slug) {
            'persona' => $this->startPersona($user, $provider),
            'verif' => $this->startVeriff($user, $provider),
            'complycube' => $this->startComplyCube($user, $provider),
            'yoti' => $this->startYoti($user, $provider),
        };

        // $pageTitle = 'KYC Documents';
        // $user = auth()->user();

        // $form = Form::where('act', 'user.kyc')->first();
        // return view('Template::user.kyc.form', compact('pageTitle', 'form', 'user'));
    }
    
    protected function startManualKyc(User $user)
    {
        $form = Form::where('act', 'user.kyc')->firstOrFail();
        $pageTitle = 'KYC Documents';
    
        return view('Template::user.kyc.form', compact('pageTitle', 'form', 'user'));
    }

    
    protected function startVeriff(User $user, KycProvider $provider)
    {
        $response = Http::timeout(30)->withHeaders([
            'x-auth-client' => $provider->api_key,
            'Content-Type'  => 'application/json',
            'Accept' => 'application/json',
        ])->post('https://stationapi.veriff.com/v1/sessions', [
            'verification' => [
                'vendorData' => (string) $user->id,
                'person' => [
                    'firstName' => $user->firstname,
                    'lastName'  => $user->lastname,
                ],
            ],
        ]);

        if (!$response->successful()) {
            Log::error('Veriff session creation failed', [
                'status'  => $response->status(),
                'body'    => $response->body(),
                'user_id' => $user->id,
            ]);
            $user->kv = 0;
            $user->save();
            return back()->withNotify([['error', 'Unable to start Veriff verification. Please try again.']]);
        }

        // Cache sessionId → user_id so we can re-authenticate the user on callback
        // even if their session expired during the verification flow.
        // The dashboard callback URL is fixed so uid cannot be in the URL directly.
        $sessionId = $response['verification']['id'];
        Cache::put('veriff_session_' . $sessionId, $user->id, now()->addHours(2));

        $sessionUrl = $response['verification']['url'];

        return redirect()->away($sessionUrl);
    }

    public function veriffReturn(Request $request)
    {
        // Re-authenticate from the signed URL uid if session expired
        if (!auth()->check() && $request->has('uid')) {
            $user = User::find($request->uid);
            if ($user) {
                auth()->login($user);
            }
        }

        $notify[] = ['success', 'Your identity verification has been submitted and is under review. We will notify you once it is approved.'];

        if (auth()->check()) {
            return redirect()->route('user.kyc.data')->withNotify($notify);
        }

        return redirect()->route('user.login')->withNotify($notify);
    }
    
    protected function startPersona(User $user, KycProvider $provider)
    {
        return view('Template::user.kyc.persona', [
            'user' => $user,
            'provider' => $provider,
        ]);
    }
    
    protected function startYoti(User $user, KycProvider $provider)
    {
        // $config = new Config([
        //     'apiUrl' => 'https://api.yoti.com/sandbox/idverify/v1',
        // ]);
        $yotiApiUrl = config('services.yoti.api_url', 'https://api.yoti.com/idverify/v1');
        $client = new DocScanClient(
            $provider->api_key,              // SDK ID
            storage_path('app/yoti-pem.pem'),
            [],
            $yotiApiUrl
        );
        // dd($client);
        $sdkConfig = (new SdkConfigBuilder())
            ->withAllowsCamera(true)
            ->withSuccessUrl(route('user.kyc.webhook', ['provider' => 'yoti']))
            ->withErrorUrl(route('user.kyc.webhook', ['provider' => 'yoti']))
            // ->withAllowsUploads(true)
            ->build();
    
        $sessionSpec = (new SessionSpecificationBuilder())
            ->withClientSessionTokenTtl(600)
            ->withResourcesTtl(86400)
            ->withUserTrackingId((string) $user->id)
            ->withSdkConfig($sdkConfig)
            ->withRequestedCheck(
                (new RequestedDocumentAuthenticityCheckBuilder())->build()
            )
            ->withRequestedCheck(
                (new RequestedLivenessCheckBuilder())
                ->forZoomLiveness()
              	->withMaxRetries(3)
              	->build()
            )
            // ->withNotifications(
            //     route('user.kyc.webhook', ['provider' => 'yoti'])
            // )
            ->build();
    
        $session = $client->createSession($sessionSpec);
    
        return view('Template::user.kyc.yoti', [
            'sessionId' => $session->getSessionId(),
            'sessionToken' => $session->getClientSessionToken(),
            'sdkId' => $provider->api_key,
        ]);
    }

    protected function startComplyCube(User $user, KycProvider $provider)
    {
        // Step 1: Create client
        $client = Http::timeout(30)->withToken($provider->api_key)
            ->post('https://api.complycube.com/v1/clients', [
                'reference' => (string) $user->id,
                'type' => 'person',
                'personDetails' => [
                    'firstName' => $user->firstname,
                    'lastName'  => $user->lastname,
                    'email'     => $user->email,
                ],
            ]);
    
        if (!$client->successful()) {
            // abort(500, 'Unable to create ComplyCube client');
            $user = auth()->user();
            $user->kv = 0;
            $user->save();
            return back()->withNotify([['error', 'Unable to create ComplyCube client.']]);
        }
    
        $clientId = $client['id'];
    
        // Step 2: Create check
        $check = Http::timeout(30)->withToken($provider->api_key)
            ->post('https://api.complycube.com/v1/checks', [
                'clientId' => $clientId,
                'type' => 'identity_check',
                'callbackUrl' => route('user.kyc.webhook', ['provider' => 'complycube']),
            ]);
    
        if (!$check->successful()) {
            // abort(500, 'Unable to start ComplyCube verification');
            $user = auth()->user();
            $user->kv = 0;
            $user->save();
            return back()->withNotify([['error', 'Unable to start ComplyCube verification']]);
        }
    
        $redirectUrl = $check['redirectUrl'];
    
        return redirect()->away($redirectUrl);
    }

    public function handle(Request $request, string $provider)
    {
        // dd($request);
        if ($request->isMethod('get')) {
            // Re-authenticate if session expired during the Veriff flow.
            // Veriff appends ?sessionId=xxx to the dashboard callback URL — use it to look up the user.
            if (!auth()->check() && $request->has('sessionId')) {
                $userId = Cache::get('veriff_session_' . $request->sessionId);
                if ($userId) {
                    $u = User::find($userId);
                    if ($u) auth()->login($u);
                }
            }

            $notify[] = ['success', 'Your identity verification is under review. We will notify you once it is approved.'];
            if (auth()->check()) {
                return redirect()->route('user.kyc.data')->withNotify($notify);
            }
            return redirect()->route('user.login')->withNotify($notify);
        }
    
        // From here on: POST only
        Log::info('KYC webhook received', [
            'provider' => $provider,
            'payload'  => $request->all(),
            'raw'      => $request->getContent(),
            'headers'  => $request->headers->all(),
        ]);
        if ($provider == 'verif'){
            $this->verifyVeriffSignature($request);

            // Veriff sends event webhooks (e.g. submitted, started) that are not decisions.
            // Only decision webhooks have a 'verification' object — ignore everything else.
            if (!$request->has('verification')) {
                return response()->json(['status' => 'received']);
            }
        }

        if ($provider === 'yoti') {
            return $this->handleYotiWebhook($request);
        }

        $userId = $this->resolveUserId($request, $provider);

        if (!$userId) {
            return response()->json(['error' => 'User not found'], 422);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'Invalid user'], 404);
        }

        if ($user->kv === Status::KYC_VERIFIED) {
            return response()->json(['status' => 'already_verified']);
        }

        $status = $this->resolveStatus($request, $provider);
        $reason = $this->resolveRejectionReason($request, $provider);

        return match ($status) {
            'approved' => $this->approve($user),
            'rejected' => $this->reject($user, $reason),
            default    => $this->pending($user),
        };

        // return response()->json(['success' => true]);
        // return redirect()->route('user.home')->withNotify([['success', 'Verification']]);
    }
    
    protected function handleYotiWebhook(Request $request)
    {
        $sessionId = $request->input('session_id');
        $state     = $request->input('state'); // COMPLETED / FAILED
    
        $userId = $request->input('user_tracking_id');
    
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        return match ($state) {
            'COMPLETED' => $this->approve($user),
            'FAILED'    => $this->reject($user, 'Yoti verification failed'),
            default     => $this->pending($user),
        };
    }

    
    protected function verifyVeriffSignature(Request $request): void
    {
        $provider = KycProvider::where('slug', 'verif')->first();
        if (!$provider || !$provider->secret_key) {
            abort(401, 'Veriff provider not configured');
        }

        $signature = $request->header('X-Hmac-Signature');
        $payload   = $request->getContent() ?: json_encode($request->all());

        $expected = hash_hmac(
            'sha256',
            $payload,
            $provider->secret_key
        );
    
        if (!$signature || !hash_equals($expected, $signature)) {
            abort(401, 'Invalid webhook signature');
        }
    }

    protected function approve(User $user)
    {
        $user->kv = Status::KYC_VERIFIED;
        $user->kyc_rejection_reason = null;
        $user->save();
        return response()->json(['status' => 'approved']);
    }

    protected function reject(User $user, ?string $reason)
    {
        $user->kv = Status::KYC_REJECTED;
        $user->kyc_rejection_reason = $reason;
        $user->save();
        return response()->json(['status' => 'rejected']);
    }

    protected function pending(User $user)
    {
        $user->kv = Status::KYC_PENDING;
        $user->save();
        return response()->json(['status' => 'pending']);
    }
    
    protected function resolveUserId(Request $request, string $provider): ?int
    {
        $id = match ($provider) {
            'persona'    => $request->data['attributes']['reference-id'] ?? null,
            // Decision webhooks: verification.vendorData; event webhooks: root vendorData
            'verif'      => $request->input('verification.vendorData') ?? $request->input('vendorData') ?? null,
            'complycube' => $request->clientReference ?? null,
            default      => null,
        };

        return $id ? (int) $id : null;
    }

    protected function resolveStatus(Request $request, string $provider): string
    {
        return match ($provider) {
            'persona' =>
                $request->data['attributes']['status'] === 'completed' && $request->data['attributes']['decision'] === 'approved'
                    ? 'approved'
                    : 'pending',

            'verif' =>
                $request->verification['status'] === 'approved'
                    ? 'approved'
                    : ($request->verification['status'] === 'declined' ? 'rejected' : 'pending'),

            'complycube' =>
                $request->check['outcome'] === 'clear'
                    ? 'approved'
                    : ($request->check['outcome'] === 'rejected' ? 'rejected' : 'pending'),

            default => 'pending',
        };
    }

    protected function resolveRejectionReason(Request $request, string $provider): ?string
    {
        return match ($provider) {
            'persona' => $request->data['attributes']['inquiry']['failure-reasons'][0] ?? null,
            'verif'   => $request->verification['reason'] ?? null,
            'complycube' => $request->check['reasons'][0] ?? null,
            default   => null,
        };
    }

    public function kycData()
    {
        $user = auth()->user();
        $pageTitle = 'KYC Data';
        // abort_if($user->kv == Status::VERIFIED, 403);
        return view('Template::user.kyc.info', compact('pageTitle', 'user'));
    }

    public function kycSubmit(Request $request)
    {
        $user = auth()->user();
        if ($user->type == Status::BUSINESS_USER) {
            $form = Form::Where('act', 'business-user.kyc')->firstOrFail();
        } else {
            $form = Form::Where('act', 'personal-user.kyc')->firstOrFail();
        }
        $formData = $form->form_data;
        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);

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

        $notify[] = ['success', 'KYC data submitted successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function userData()
    {
        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $pageTitle  = 'User Data';
        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('Template::user.user_data', compact('pageTitle', 'user', 'countries', 'mobileCode'));
    }

    public function userDataSubmit(Request $request)
    {
        $user = auth()->user();
        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $countryData  = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));

        $request->validate([
            'country_code' => 'required|in:' . $countryCodes,
            'country'      => 'required|in:' . $countries,
            'mobile_code'  => 'required|in:' . $mobileCodes,
            'username'     => 'required|unique:users|min:6',
            'mobile'       => ['required', 'regex:/^([0-9]*)$/', Rule::unique('users')->where('dial_code', $request->mobile_code)],
        ]);

        if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
            $notify[] = ['info', 'Username can contain only small letters, numbers and underscore.'];
            $notify[] = ['error', 'No special character, space or capital letters in username.'];
            return back()->withNotify($notify)->withInput($request->all());
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
            $validator      = Validator::make($request->all(), $validationRule);
            if ($validator->fails()) {
                return response()->json([
                    'remark'  => 'validation_error',
                    'status'  => 'error',
                    'message' => ['error' => $validator->errors()->all()],
                ]);
            }
            $userData                    = $formProcessor->processFormData($request, $formData);
            $user->business_profile_data = $userData;
        }
        $user->save();

        return to_route('user.home');
    }

    public function addDeviceToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->all()];
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            return ['success' => true, 'message' => 'Already exists'];
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->user_id = auth()->user()->id;
        $deviceToken->token   = $request->token;
        $deviceToken->is_app  = Status::NO;
        $deviceToken->save();

        return ['success' => true, 'message' => 'Token saved successfully'];
    }

    public function wallets()
    {
        $pageTitle      = 'My Wallets';
        $user           = auth()->user();
        $isMultiCurrency = (bool) gs('wallet_currency_mode'); // 1=multi, 0=single

        if (!$isMultiCurrency) {
            // Single-currency mode: auto-create the system-default wallet if missing
            $defaultCurrency = gs('cur_text');
            $hasWallet = \App\Models\Wallet::forUser($user->id)->where('currency_code', $defaultCurrency)->exists();
            if (!$hasWallet) {
                $country = Country::active()->where('currency', $defaultCurrency)->first();
                \App\Models\Wallet::create([
                    'user_id'            => $user->id,
                    'currency_code'      => $defaultCurrency,
                    'currency_name'      => ($country ? $country->name . ' / ' : '') . $defaultCurrency,
                    'currency_symbol'    => $defaultCurrency,
                    'balance'            => 0,
                    'is_default'         => true,
                    'remittance_enabled' => $country
                        ? $country->is_sending == Status::ENABLE || $country->is_receiving == Status::ENABLE
                        : false,
                    'status'             => \App\Models\Wallet::STATUS_ACTIVE,
                ]);
            }
        }

        $walletsQuery = \App\Models\Wallet::forUser($user->id)->active()->orderByDesc('is_default');
        if (!$isMultiCurrency) {
            $walletsQuery->where('currency_code', gs('cur_text'));
        }
        $wallets = $walletsQuery->get();
        $existing = $wallets->pluck('currency_code');

        if ($isMultiCurrency) {
            $allowedCodes = walletAllowedCurrencyCodes(); // null = all allowed
            $currencyQuery = Country::active()
                ->select('currency', 'name', 'is_sending', 'is_receiving')
                ->whereNotNull('currency')->where('currency', '!=', '');
            if ($allowedCodes !== null) {
                $currencyQuery->whereIn('currency', $allowedCodes);
            }
            $currencies = $currencyQuery->get()->unique('currency')->values()
                ->map(fn($c) => [
                    'currency_code'      => $c->currency,
                    'country_name'       => $c->name,
                    'remittance_enabled' => $c->is_sending == Status::ENABLE || $c->is_receiving == Status::ENABLE,
                    'has_wallet'         => $existing->contains($c->currency),
                ]);
        } else {
            $currencies = collect();
        }

        // Multi-currency: recent exchange sidebar; Single-currency: recent transactions on the default wallet
        if ($isMultiCurrency) {
            $exchanges       = \App\Models\WalletExchange::forUser($user->id)
                ->with('fromWallet:id,currency_code', 'toWallet:id,currency_code')
                ->latest()->take(20)->get();
            $recentTxItems   = collect();
            $defaultWallet   = null;
        } else {
            $exchanges     = collect();
            $defaultWallet = $wallets->first();
            if ($defaultWallet) {
                $deps = \App\Models\Deposit::where('wallet_id', $defaultWallet->id)
                    ->where('status', Status::PAYMENT_SUCCESS)->latest()->take(30)->get()
                    ->map(fn($d) => ['type'=>'topup','date'=>$d->created_at,'trx'=>$d->trx,'amount'=>$d->amount,'sign'=>'+',
                        'description'=>'Top-up via '.(optional($d->gateway)->name ?? 'gateway')]);

                $txns = Transaction::where('wallet_id', $defaultWallet->id)->where('user_id', $user->id)->latest()->take(30)->get()
                    ->map(fn($t) => ['type'=>'txn','date'=>$t->created_at,'trx'=>$t->trx,'amount'=>$t->amount,
                        'sign'=>$t->trx_type,'description'=>$t->details]);

                $recentTxItems = $deps->concat($txns)->sortByDesc('date')->take(20)->values();
            } else {
                $recentTxItems = collect();
            }
        }

        $depositMethods = \App\Models\GatewayCurrency::whereHas('method', fn($q) => $q->where('status', Status::ENABLE))
            ->with('method')->orderBy('method_code')->get();

        return view('Template::user.wallet.index', compact(
            'pageTitle', 'wallets', 'currencies', 'exchanges', 'depositMethods',
            'isMultiCurrency', 'recentTxItems', 'defaultWallet'
        ));
    }

    public function walletTransactions(int $id)
    {
        $user   = auth()->user();
        $wallet = \App\Models\Wallet::forUser($user->id)->active()->findOrFail($id);

        // Top-ups: deposits credited to this wallet
        $deposits = \App\Models\Deposit::where('wallet_id', $wallet->id)
            ->where('status', \App\Constants\Status::PAYMENT_SUCCESS)
            ->latest()->get()
            ->map(fn($d) => [
                'type'        => 'topup',
                'date'        => $d->created_at,
                'trx'         => $d->trx,
                'amount'      => $d->amount,
                'currency'    => $wallet->currency_code,
                'sign'        => '+',
                'description' => 'Wallet top-up via ' . (optional($d->gateway)->name ?? 'gateway'),
            ]);

        // Deductions: send-money payments made from this wallet
        $spends = \App\Models\Transaction::where('wallet_id', $wallet->id)
            ->where('user_id', $user->id)
            ->latest()->get()
            ->map(fn($t) => [
                'type'        => 'spend',
                'date'        => $t->created_at,
                'trx'         => $t->trx,
                'amount'      => $t->amount,
                'currency'    => $wallet->currency_code,
                'sign'        => '-',
                'description' => $t->details,
            ]);

        // Exchanges: currency conversions involving this wallet
        $exchanges = \App\Models\WalletExchange::where('from_wallet_id', $wallet->id)
            ->orWhere('to_wallet_id', $wallet->id)
            ->where('user_id', $user->id)
            ->with('fromWallet:id,currency_code', 'toWallet:id,currency_code')
            ->latest()->get()
            ->map(fn($e) => [
                'type'        => 'exchange',
                'date'        => $e->created_at,
                'trx'         => $e->trx ?? null,
                'amount'      => $e->from_wallet_id === $wallet->id ? $e->from_amount : $e->to_amount,
                'currency'    => $wallet->currency_code,
                'sign'        => $e->from_wallet_id === $wallet->id ? '-' : '+',
                'description' => $e->from_wallet_id === $wallet->id
                    ? 'Exchange to ' . optional($e->toWallet)->currency_code . ' wallet'
                    : 'Exchange from ' . optional($e->fromWallet)->currency_code . ' wallet',
            ]);

        $items = $deposits->concat($spends)->concat($exchanges)
            ->sortByDesc('date')->values();

        $pageTitle = $wallet->currency_code . ' Wallet Transactions';
        return view('Template::user.wallet.transactions', compact('pageTitle', 'wallet', 'items'));
    }

    public function walletStore(Request $request)
    {
        $user = auth()->user();

        // In single-currency mode, only the system default currency is allowed
        if (!gs('wallet_currency_mode')) {
            $code = strtoupper(gs('cur_text'));
        } else {
            $request->validate(['currency_code' => 'required|string|max:10']);
            $code = strtoupper($request->currency_code);

            $allowedCodes = walletAllowedCurrencyCodes();
            if ($allowedCodes !== null && !in_array($code, $allowedCodes)) {
                return back()->withErrors(['currency_code' => $code . ' is not available for wallet creation.']);
            }
        }

        if (\App\Models\Wallet::forUser($user->id)->where('currency_code', $code)->exists()) {
            return back()->withErrors(['currency_code' => 'You already have a ' . $code . ' wallet.']);
        }

        $country = Country::active()->where('currency', $code)->firstOrFail();
        $isFirst = !\App\Models\Wallet::forUser($user->id)->exists();

        \App\Models\Wallet::create([
            'user_id'            => $user->id,
            'currency_code'      => $code,
            'currency_name'      => $country->name . ' / ' . $code,
            'currency_symbol'    => $code,
            'balance'            => 0,
            'is_default'         => $isFirst,
            'remittance_enabled' => $country->is_sending == Status::ENABLE || $country->is_receiving == Status::ENABLE,
            'status'             => \App\Models\Wallet::STATUS_ACTIVE,
        ]);

        return back()->with('success', $code . ' wallet created successfully.');
    }

    public function walletSetDefault(Request $request, int $id)
    {
        $user   = auth()->user();
        $wallet = \App\Models\Wallet::forUser($user->id)->active()->findOrFail($id);
        \App\Models\Wallet::forUser($user->id)->update(['is_default' => false]);
        $wallet->update(['is_default' => true]);

        return back()->with('success', 'Default wallet updated.');
    }

    public function walletExchange(Request $request)
    {
        if (!gs('wallet_currency_mode')) {
            return back()->withErrors(['error' => 'Currency exchange is not available in single-currency mode.']);
        }

        $request->validate([
            'from_wallet_id' => 'required|integer',
            'to_wallet_id'   => 'required|integer|different:from_wallet_id',
            'amount'         => 'required|numeric|gt:0',
        ]);

        $user       = auth()->user();
        $fromWallet = \App\Models\Wallet::forUser($user->id)->active()->findOrFail($request->from_wallet_id);
        $toWallet   = \App\Models\Wallet::forUser($user->id)->active()->findOrFail($request->to_wallet_id);
        $amount     = (float) $request->amount;

        if ($fromWallet->balance < $amount) {
            return back()->withErrors(['amount' => 'Insufficient balance in ' . $fromWallet->currency_code . ' wallet.']);
        }

        $fromCountry = Country::active()->where('currency', $fromWallet->currency_code)->first();
        $toCountry   = Country::active()->where('currency', $toWallet->currency_code)->first();

        if (!$fromCountry || !$toCountry) {
            return back()->withErrors(['amount' => 'Exchange rate not available for this currency pair.']);
        }

        $fromRate     = (float) ($fromCountry->rate ?: 1);
        $toRate       = (float) ($toCountry->rate ?: 1);
        $exchangeRate = $toRate / $fromRate;

        $general       = gs();
        $fixedCharge   = (float) ($general->wallet_exchange_fixed_charge ?? 0);
        $percentCharge = (float) ($general->wallet_exchange_percent_charge ?? 0);
        $fee           = $fixedCharge + ($amount * $percentCharge / 100);
        $totalDeduct   = $amount + $fee;

        if ($fromWallet->balance < $totalDeduct) {
            return back()->withErrors(['amount' =>
                'Insufficient balance. Need ' . number_format($totalDeduct, 2) . ' ' . $fromWallet->currency_code .
                ' (amount + fee), available ' . number_format($fromWallet->balance, 2) . ' ' . $fromWallet->currency_code . '.'
            ]);
        }

        $toAmount = round($amount * $exchangeRate, 8);

        \Illuminate\Support\Facades\DB::transaction(function () use ($fromWallet, $toWallet, $amount, $fee, $totalDeduct, $toAmount, $exchangeRate, $user) {
            $affected = \Illuminate\Support\Facades\DB::table('wallets')
                ->where('id', $fromWallet->id)
                ->where('balance', '>=', $totalDeduct)
                ->decrement('balance', $totalDeduct);

            if (!$affected) {
                throw new \RuntimeException('Insufficient wallet balance');
            }

            $toWallet->increment('balance', $toAmount);

            $trx = getTrx();
            \App\Models\WalletExchange::create([
                'trx'            => $trx,
                'user_id'        => $user->id,
                'from_wallet_id' => $fromWallet->id,
                'to_wallet_id'   => $toWallet->id,
                'from_currency'  => $fromWallet->currency_code,
                'to_currency'    => $toWallet->currency_code,
                'from_amount'    => $amount,
                'to_amount'      => $toAmount,
                'exchange_rate'  => $exchangeRate,
                'fee'            => $fee,
                'status'         => \App\Models\WalletExchange::STATUS_COMPLETED,
            ]);

            foreach ([
                ['-', $totalDeduct, $fromWallet->fresh()->balance, $fee, $fromWallet->currency_code . ' → ' . $toWallet->currency_code],
                ['+', $toAmount,    $toWallet->fresh()->balance,   0,    'Received from ' . $fromWallet->currency_code . ' exchange'],
            ] as [$type, $amt, $bal, $charge, $detail]) {
                Transaction::create([
                    'user_id'      => $user->id,
                    'amount'       => $amt,
                    'post_balance' => $bal,
                    'charge'       => $charge,
                    'trx_type'     => $type,
                    'remark'       => 'wallet_exchange',
                    'details'      => $detail,
                    'trx'          => $trx,
                ]);
            }
        });

        return back()->with('success', 'Exchanged ' . $amount . ' ' . $fromWallet->currency_code . ' → ' . round($toAmount, 4) . ' ' . $toWallet->currency_code . '.');
    }

    public function downloadAttachment($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        // $title = slug(gs('site_name')) . '- attachments.' . $extension;
        $title = slug(gs('site_name')) . '_' . basename($filePath);

        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = ['error', 'File does not exists'];
            return back()->withNotify($notify);
        }
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    // ── Wallet Transfer: look up recipient ───────────────────────────────
    public function walletTransferLookup(Request $request)
    {
        if (!gs('wallet_module') || !gs('wallet_transfer')) {
            return response()->json(['error' => 'Wallet transfers are disabled.'], 403);
        }

        $request->validate(['identifier' => 'required|string', 'type' => 'required|in:username,email,mobile']);

        $type  = $request->type;
        $value = $request->identifier;
        $user  = auth()->user();

        $methods = gs()->wallet_transfer_methods ?? ['username', 'email', 'mobile'];
        if (!in_array($type, $methods)) {
            return response()->json(['error' => 'This lookup method is not enabled.'], 422);
        }

        $recipient = User::where('status', Status::ENABLE)->where($type, $value)->where('id', '!=', $user->id)->first();

        if (!$recipient) {
            return response()->json(['error' => 'No user found with that ' . $type . '.'], 404);
        }

        return response()->json([
            'id'       => $recipient->id,
            'name'     => $recipient->firstname . ' ' . $recipient->lastname,
            'username' => $recipient->username,
        ]);
    }

    // ── Wallet Transfer: execute ──────────────────────────────────────────
    public function walletTransfer(Request $request)
    {
        if (!gs('wallet_module') || !gs('wallet_transfer')) {
            return back()->withErrors(['error' => 'Wallet transfers are disabled.']);
        }

        $request->validate([
            'from_wallet_id' => 'required|integer',
            'receiver_id'    => 'required|integer',
            'amount'         => 'required|numeric|gt:0',
        ]);

        $user        = auth()->user();
        $fromWallet  = \App\Models\Wallet::forUser($user->id)->active()->findOrFail($request->from_wallet_id);
        $receiver    = User::where('status', Status::ENABLE)->where('id', $request->receiver_id)->firstOrFail();

        if ($receiver->id === $user->id) {
            return back()->withErrors(['receiver_id' => 'You cannot transfer to yourself.']);
        }

        $amount  = (float) $request->amount;
        $general = gs();

        $fixedCharge   = (float) ($general->wallet_transfer_fixed_charge ?? 0);
        $percentCharge = (float) ($general->wallet_transfer_percent_charge ?? 0);
        $charge        = $fixedCharge + ($amount * $percentCharge / 100);
        $totalDeduct   = $amount + $charge;

        if ($fromWallet->balance < $totalDeduct) {
            return back()->withErrors(['amount' =>
                'Insufficient balance. Need ' . number_format($totalDeduct, 2) . ' ' . $fromWallet->currency_code .
                ' (amount + fee), available ' . number_format($fromWallet->balance, 2) . ' ' . $fromWallet->currency_code . '.'
            ]);
        }

        // Get or create matching wallet for receiver
        $toWallet = \App\Models\Wallet::forUser($receiver->id)
            ->where('currency_code', $fromWallet->currency_code)
            ->first();

        if (!$toWallet) {
            $country  = Country::active()->where('currency', $fromWallet->currency_code)->first();
            $toWallet = \App\Models\Wallet::create([
                'user_id'            => $receiver->id,
                'currency_code'      => $fromWallet->currency_code,
                'currency_name'      => $country ? $country->name . ' / ' . $fromWallet->currency_code : $fromWallet->currency_name,
                'currency_symbol'    => $fromWallet->currency_symbol,
                'balance'            => 0,
                'is_default'         => !\App\Models\Wallet::forUser($receiver->id)->exists(),
                'remittance_enabled' => $fromWallet->remittance_enabled,
                'status'             => \App\Models\Wallet::STATUS_ACTIVE,
            ]);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $receiver, $fromWallet, $toWallet, $amount, $charge, $totalDeduct) {
            $affected = \Illuminate\Support\Facades\DB::table('wallets')
                ->where('id', $fromWallet->id)
                ->where('balance', '>=', $totalDeduct)
                ->decrement('balance', $totalDeduct);

            if (!$affected) {
                throw new \RuntimeException('Insufficient wallet balance');
            }

            $toWallet->increment('balance', $amount);

            $trx = getTrx();

            WalletTransfer::create([
                'trx'               => $trx,
                'sender_id'         => $user->id,
                'receiver_id'       => $receiver->id,
                'sender_wallet_id'  => $fromWallet->id,
                'receiver_wallet_id' => $toWallet->id,
                'currency_code'     => $fromWallet->currency_code,
                'amount'            => $amount,
                'charge'            => $charge,
                'sender_deducted'   => $totalDeduct,
                'receiver_credited' => $amount,
                'status'            => WalletTransfer::STATUS_COMPLETED,
            ]);

            Transaction::create([
                'user_id'      => $user->id,
                'wallet_id'    => $fromWallet->id,
                'amount'       => $totalDeduct,
                'post_balance' => $fromWallet->fresh()->balance,
                'charge'       => $charge,
                'trx_type'     => '-',
                'remark'       => 'wallet_transfer',
                'details'      => 'Wallet transfer to @' . $receiver->username . ' (' . $fromWallet->currency_code . ')',
                'trx'          => $trx,
            ]);

            Transaction::create([
                'user_id'      => $receiver->id,
                'wallet_id'    => $toWallet->id,
                'amount'       => $amount,
                'post_balance' => $toWallet->fresh()->balance,
                'charge'       => 0,
                'trx_type'     => '+',
                'remark'       => 'wallet_transfer',
                'details'      => 'Wallet transfer received from @' . $user->username . ' (' . $fromWallet->currency_code . ')',
                'trx'          => $trx,
            ]);
        });

        return back()->with('success',
            'Transferred ' . number_format($amount, 2) . ' ' . $fromWallet->currency_code .
            ' to ' . $receiver->firstname . ' ' . $receiver->lastname . '.'
        );
    }

    // ── Wallet Transfer history ───────────────────────────────────────────
    public function walletTransferHistory()
    {
        $user      = auth()->user();
        $pageTitle = 'Wallet Transfer History';
        $transfers = WalletTransfer::forUser($user->id)
            ->with('sender:id,firstname,lastname,username', 'receiver:id,firstname,lastname,username')
            ->latest()
            ->paginate(getPaginate());

        return view('Template::user.wallet.transfers', compact('pageTitle', 'transfers', 'user'));
    }
}
