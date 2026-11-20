<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\ProcessSendMoney;
use App\Models\Country;
use App\Models\CountryDeliveryMethod;
use App\Models\Service;
use App\Models\DeliveryMethod;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\SendingPurpose;
use App\Models\SendMoney;
use App\Models\Form;
use App\Models\Recipient;
use App\Models\SourceOfFund;
use Illuminate\Http\Request;
use App\Services\Aml\AmlRiskService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SendMoneyController extends Controller
{
    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:4',
        ]);
    
        $user = auth()->user();
    
        if ($user->pin_locked_until && now()->lt($user->pin_locked_until)) {
            return back()->withNotify([['error', 'Pin temporarily locked']]);
        }
    
        if (! Hash::check($request->pin, $user->transaction_pin_hash)) {
    
            $user->increment('pin_failed_attempts');
    
            if ($user->pin_failed_attempts >= 5) {
                $user->update([
                    'pin_locked_until' => now()->addMinutes(15),
                    'pin_failed_attempts' => 0,
                ]);
            }
    
            return back()->withNotify([['error', 'Invalid pin']]);
        }
    
        $user->update([
            'pin_failed_attempts' => 0,
            'pin_locked_until' => null,
        ]);
    
        session(['pin_verified_at' => now()]);
    
        return back()->withNotify([['Success', 'Pin verified']]);
    }
    public function sendMoney()
    {
        // $countries = Country::all();
        // $deliveryMethod = DeliveryMethod::firstOrCreate([
        //     'name' => 'Mobile Recharge',
        // ]);
        // $cryptoServices = [
        //     ['name' => 'BTC', 'form_id' => 41],
        //     ['name' => 'USDT', 'form_id' => 42],
        //     ['name' => 'ETH', 'form_id' => 43],
        //     ['name' => 'USDC', 'form_id' => 44],
        //     ['name' => 'LTC', 'form_id' => 45],
        // ];
        // foreach ($countries as $country) {
        
        //     $countryDeliveryMethod = CountryDeliveryMethod::firstOrCreate([
        //         'country_id' => $country->id,
        //         'delivery_method_id' => $deliveryMethod->id,
        //     ]);
        
        //     foreach ($cryptoServices as $crypto) {
        //         Service::firstOrCreate(
        //             [
        //                 'country_delivery_method_id' => $countryDeliveryMethod->id,
        //                 'form_id' => $crypto['form_id'],
        //             ],
        //             [
        //                 'name' => $crypto['name'],
        //                 'status' => 1,
        //             ]
        //         );
        //     }
        // }

        $user = auth()->user();
        $pageTitle = 'Send Money';
        $sources            = SourceOfFund::active()->get();
        $purposes           = SendingPurpose::active()->get();
        $sessionData        = session()->get('send_money') ?? [];

        $recipientCountryId = null;
        $deliveryMethodId   = null;
        $sendingCountries   = Country::active()->sending()->with('conversionRates')->get();
        $receivingCountries = Country::receivableCountries()->get();

        if ($sessionData) {
            $sendingCountryId   = $sendingCountries->where('id', $sessionData['sending_country'])->first()->id;
            $recipientCountryId = $receivingCountries->where('id', $sessionData['recipient_country'])->first()->id;
            $deliveryMethodId   = $sessionData['delivery_method'];
        } else {
            $ipInfo           = json_decode(json_encode(getIpInfo()), true);
            $countryName      = @implode(',', $ipInfo['country']);
            $sendingCountryId = @$sendingCountries->where('name', $countryName)->first()->id ?? @$sendingCountries->first()->id;
        }

        $todaySendMoney     = SendMoney::whereIn('status', [Status::SEND_MONEY_PENDING, Status::SEND_MONEY_COMPLETED])->where('user_id', auth()->id())->whereDate('created_at', now())->sum('base_currency_amount');
        $thisMonthSendMoney = SendMoney::whereIn('status', [Status::SEND_MONEY_PENDING, Status::SEND_MONEY_COMPLETED])->where('user_id', auth()->id())->whereMonth('created_at', now()->month)->sum('base_currency_amount');
        $recipients = Recipient::where('user_id', auth()->user()->id)->get();

        $sendingAmount   = @$sessionData['sending_amount'];
        $recipientAmount = @$sessionData['recipient_amount'];

        if (gs('wallet_module')) {
            $walletQuery = \App\Models\Wallet::forUser($user->id)->active()->orderByDesc('is_default');
            // In single-currency mode only show the default-currency wallet
            if (!gs('wallet_currency_mode')) {
                $walletQuery->where('currency_code', gs('cur_text'));
            }
            $wallets = $walletQuery->get();
            $walletCountries = Country::active()
                ->whereIn('currency', $wallets->pluck('currency_code'))
                ->get()->keyBy('currency');
        } else {
            $wallets         = collect();
            $walletCountries = collect();
        }

        session()->forget('send_money');
        return view('Template::user.send_money.form', compact('user', 'pageTitle', 'recipients', 'sources', 'purposes', 'sendingAmount', 'recipientAmount', 'sendingCountryId', 'recipientCountryId', 'sendingCountries', 'receivingCountries', 'deliveryMethodId', 'todaySendMoney', 'thisMonthSendMoney', 'wallets', 'walletCountries'));
    }

    public function save(Request $request)
    {
        if (! session()->has('pin_verified_at')) {
            return back()->withNotify([['error', 'Pin verification required']]);
        }
    
        if (now()->diffInMinutes(session('pin_verified_at')) > 3) {
            return back()->withNotify([['error', 'Pin session expired']]);
        }
        // dd($request);
        $rules =  [
            'sending_amount'    => 'required|numeric|gt:0',
            'sending_country'   => 'required|integer|gt:0|exists:countries,id',
            'recipient_country' => 'required|integer|gt:0|exists:countries,id',
            'payment_type'      => 'required|in:1,2,3,4',
            'source_of_funds'   => 'required|integer|gt:0|exists:source_of_funds,id',
            'sending_purpose'   => 'required|integer|gt:0|exists:sending_purposes,id',
            'recipient'         => 'required|array|min:3',
            'recipient.*'       => 'required|string',
            'delivery_method'   => 'required|numeric',
            'service'           => 'nullable|required_unless:delivery_method,0|integer'
        ];

        $messages = [
            'recipient.name.required'    => 'Recipient name field is required',
            'recipient.mobile.required'  => 'Recipient mobile number field is required',
            'recipient.address.required' => 'Recipient address field is required',
            'service.required_unless'    => 'Service field is required if delivery method is not an agent'
        ];

        $serviceFormData = null;

        if ($request->service) {
            $service = Service::find($request->service);
            if (!$service) {
                return back()->withNotify([['error', 'Invalid service selected.']]);
            }
            // A service may have no form (no extra fields required)
            $form = $service->form_id ? Form::where('act', 'service_form')->find($service->form_id) : null;
            if ($form) {
                $formData       = $form->form_data;
                $formProcessor  = new FormProcessor();
                $validationRule = $formProcessor->valueValidation($formData);
                $rules          = array_merge($rules, $validationRule);
                $request->validate($rules, $messages);
                $serviceFormData = $formProcessor->processFormData($request, $formData);
            } else {
                $request->validate($rules, $messages);
            }
        } else {
            $request->validate($rules, $messages);
        }

        $recipientCountry = Country::find($request->recipient_country);
        if (!$recipientCountry) {
            return back()->withNotify([['error', 'Selected recipient country is no longer available.']]);
        }

        if ($request->delivery_method) {
            $countryDeliveryMethod = CountryDeliveryMethod::where('country_id', $request->recipient_country)->where('delivery_method_id', $request->delivery_method)->exists();
        } else {
            $countryDeliveryMethod = gs()->agent_module && $recipientCountry->has_agent;
        }

        if (!$countryDeliveryMethod) {
            throw ValidationException::withMessages(['error' => 'Invalid delivery method selected']);
        }

        $user    = auth()->user();
        $payment = new ProcessSendMoney();

        if ($request->payment_type == 1) {
            $request->validate(['wallet_id' => 'required|integer']);

            $wallet = \App\Models\Wallet::forUser($user->id)->active()->find($request->wallet_id);
            if (!$wallet) {
                return back()->withNotify([['error', 'Selected wallet not found.']]);
            }

            $walletCountry = Country::where('currency', $wallet->currency_code)->first();
            $walletRate    = (float) ($walletCountry->rate ?? 1);

            // Convert base-currency (CAD) amount to wallet currency
            $walletDeductAmount = round($payment->amountWithCharge * $walletRate, 8);

            if ($wallet->balance < $walletDeductAmount) {
                return back()->withNotify([['error',
                    'Insufficient balance in ' . $wallet->currency_code . ' wallet. Need ' .
                    number_format($walletDeductAmount, 2) . ' ' . $wallet->currency_code .
                    ', available ' . number_format($wallet->balance, 2) . ' ' . $wallet->currency_code . '.'
                ]]);
            }

            $payment->wallet             = $wallet;
            $payment->walletDeductAmount = $walletDeductAmount;
        }

        // Account Balance payment — deduct from user.balance (system default currency)
        if ($request->payment_type == 4) {
            if ($user->balance < $payment->amountWithCharge) {
                return back()->withNotify([['error',
                    'Insufficient account balance. Need ' .
                    number_format($payment->amountWithCharge, 2) . ' ' . gs('cur_text') .
                    ', available ' . number_format($user->balance, 2) . ' ' . gs('cur_text') . '.'
                ]]);
            }
        }

        $payment->user       = $user;
        $payment->columnName = 'user_id';
        $sendMoney           = $payment->createSendMoney($request, $serviceFormData);

        // AML: score transaction risk
        try {
            app(AmlRiskService::class)->scoreTransaction($sendMoney, 'send_money');
        } catch (\Throwable $e) {
            logger()->error('AML scoring error (send_money): ' . $e->getMessage());
        }

        if ($request->payment_type == 1 || $request->payment_type == 4) {
            $payment->createTransaction();
            ProcessSendMoney::updateSendMoney($sendMoney, $user);

            $notify[] = ['success', 'Send money request submitted successfully'];
            return to_route('user.send.money.history')->withNotify($notify);
        }

        // Apaylo Interac e-Transfer — skip the gateway-selection page and initiate directly
        if ($request->payment_type == 3) {
            $gate = GatewayCurrency::whereHas('method', function ($q) {
                $q->where('alias', 'apaylo')->where('status', Status::ENABLE);
            })->first();

            if (!$gate) {
                $notify[] = ['error', 'Apaylo payment gateway is not available. Please choose another method.'];
                return back()->withNotify($notify);
            }

            $amount      = $sendMoney->base_currency_amount + $sendMoney->base_currency_charge;
            $charge      = $gate->fixed_charge + ($amount * $gate->percent_charge / 100);
            $payable     = $amount + $charge;
            $finalAmount = $payable * $gate->rate;

            $deposit = new Deposit();
            $deposit->user_id         = $user->id;
            $deposit->method_code     = $gate->method_code;
            $deposit->method_currency = strtoupper($gate->currency);
            $deposit->amount          = $amount;
            $deposit->charge          = $charge;
            $deposit->rate            = $gate->rate;
            $deposit->final_amount    = $finalAmount;
            $deposit->btc_amount      = 0;
            $deposit->btc_wallet      = '';
            $deposit->trx             = getTrx();
            $deposit->send_money_id   = $sendMoney->id;
            $deposit->success_url     = route('user.send.money.history');
            $deposit->failed_url      = route('user.send.money.history');
            $deposit->save();

            session()->put('Track', $deposit->trx);
            session()->forget('pin_verified_at');
            return to_route('user.deposit.confirm');
        }

        session()->put('payment_trx', $sendMoney->trx);
        session()->forget('pin_verified_at');
        return to_route('user.send.money.pay.now');
    }
    
    public function sendAgain(SendMoney $transfer)
    {
        abort_if($transfer->user_id != auth()->id(), 403);
    
        return redirect()
            ->route('user.send.money.now')
            ->withInput([
                'sending_amount'     => $transfer->sending_amount,
                'recipient_amount'   => $transfer->recipient_amount,
                'sending_country'    => $transfer->sending_country_id,
                'recipient_country'  => $transfer->recipient_country_id,
                'delivery_method'    => $transfer->delivery_method_id,
                'source_of_funds'    => $transfer->source_of_funds_id,
                'sending_purpose'    => $transfer->sending_purpose_id,
                'payment_type'       => $transfer->payment_type,
    
                'recipient' => [
                    'name'    => $transfer->recipient->name,
                    'mobile'  => $transfer->recipient->mobile,
                    'email'   => $transfer->recipient->email,
                    'address' => $transfer->recipient->address,
                ],
            ]);
    }

    public function payNow()
    {
        $user = auth()->user();
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();

        $pageTitle = 'Pay Money';
        $trx       = session()->get('payment_trx');
        $sendMoney = SendMoney::filterUser()->with('sendingCountry:id,rate')->where('trx', $trx)->first();
        if (!$sendMoney) {
            $notify[] = ['error', 'The session is invalid'];
            return to_route('user.home')->withNotify($notify);
        }
        return view('Template::user.payment.payment', compact('user','gatewayCurrency', 'pageTitle', 'sendMoney'));
    }

    public function pay(Request $request)
    {

        $sendMoney = SendMoney::filterUser()->initiated()->findOrFail(decrypt($request->id));
        session()->put('payment_trx', $sendMoney->trx);
        return to_route('user.send.money.pay.now');
    }

    public function history()
    {
        $user = auth()->user();
        $pageTitle    = 'Send Money History';
        $emptyMessage = 'No send money found';
        $transfers    = SendMoney::with('deposit.gateway', 'recipientCountry', 'countryDeliveryMethod.deliveryMethod')->filterUser()->latest()->paginate(getPaginate());
        return view('Template::user.send_money.history', compact('pageTitle', 'emptyMessage', 'transfers', 'user'));
    }

    public function checkRecipient(Request $request)
    {
        $exist['data']  = false;
        $exist['type']  = null;
        $recipientQuery = Recipient::where('user_id', auth()->user()->id);
        if ($request->email) {
            $exist['data'] = $recipientQuery->where('email', @$request->email)->first();
            $exist['type'] = 'email';
        } elseif ($request->mobile) {
            $exist['data'] = $recipientQuery->where('mobile', @$request->mobile)->first();
            $exist['type'] = 'mobile';
        }

        return response($exist);
    }
}
