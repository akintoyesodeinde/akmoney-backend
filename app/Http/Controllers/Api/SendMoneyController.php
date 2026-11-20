<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\ProcessSendMoney;
use App\Models\Country;
use App\Models\CountryDeliveryMethod;
use App\Models\Deposit;
use App\Models\DeliveryMethod;
use App\Models\Form;
use App\Models\GatewayCurrency;
use App\Models\Recipient;
use App\Models\SendingPurpose;
use App\Models\SendMoney;
use App\Models\Service;
use App\Models\SourceOfFund;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Services\Aml\AmlRiskService;
use Illuminate\Support\Facades\Validator;

class SendMoneyController extends Controller
{
    public function sendMoney()
    {
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

        $todaySendMoney     = SendMoney::whereIn('status', [Status::SEND_MONEY_PENDING, Status::SEND_MONEY_COMPLETED])
            ->where('user_id', auth()->id())
            ->whereDate('created_at', now())
            ->sum('base_currency_amount');
        $thisMonthSendMoney = SendMoney::whereIn('status', [Status::SEND_MONEY_PENDING, Status::SEND_MONEY_COMPLETED])
            ->where('user_id', auth()->id())
            ->whereMonth('created_at', now()->month)
            ->sum('base_currency_amount');

        $recipients = Recipient::where('user_id', auth()->user()->id)->get();
        $sendingAmount      = @$sessionData['sending_amount'];
        $recipientAmount    = @$sessionData['recipient_amount'];

        session()->forget('send_money');

        $general = gs();
        $user    = auth()->user();

        $perLimit     = $user->per_send_money_limit     ?? $general->user_send_money_limit;
        $dailyLimit   = $user->daily_send_money_limit   ?? $general->user_daily_send_money_limit;
        $monthlyLimit = $user->monthly_send_money_limit ?? $general->user_monthly_send_money_limit;

        if (gs('wallet_module')) {
            $walletQuery = Wallet::forUser($user->id)->active()->orderByDesc('is_default');
            if (!gs('wallet_currency_mode')) {
                $walletQuery->where('currency_code', gs('cur_text'));
            }
            $wallets = $walletQuery->get();
            $walletCountries = Country::active()
                ->whereIn('currency', $wallets->pluck('currency_code'))
                ->get()
                ->keyBy('currency');
        } else {
            $wallets         = collect();
            $walletCountries = collect();
        }

        return response()->json([
            'remark' => 'send_money',
            'status' => 'success',
            'data' => [
                'recipients'                => $recipients,
                'sources'                   => $sources,
                'purposes'                  => $purposes,
                'sending_amount'            => $sendingAmount,
                'recipient_amount'          => $recipientAmount,
                'sending_country_id'        => $sendingCountryId,
                'recipient_country_id'      => $recipientCountryId,
                'sending_countries'         => $sendingCountries,
                'receiving_countries'       => $receivingCountries,
                'delivery_method_id'        => $deliveryMethodId,
                'today_send_money'          => $todaySendMoney,
                'this_month_sendMoney'      => $thisMonthSendMoney,
                'per_send_money_limit'      => $perLimit,
                'daily_send_money_limit'    => $dailyLimit,
                'monthly_send_money_limit'  => $monthlyLimit,
                'wallet_module'             => (bool) gs('wallet_module'),
                'wallets'                   => $wallets,
                'wallet_countries'          => $walletCountries,
                'account_balance'           => $user->balance,
                'account_balance_currency'  => gs('cur_text'),
            ],
        ]);
    }

    public function saveMoney(Request $request)
    {
        $rules = [
            'sending_amount'    => 'required|numeric|gt:0',
            'sending_country'   => 'required|gt:0',
            'recipient_country' => 'required|gt:0',
            'payment_type'      => 'required|in:1,2,3,4',
            'source_of_funds'   => 'required|gt:0',
            'sending_purpose'   => 'required|gt:0',
            'recipient'         => 'required|array|min:3',
            'recipient.*'       => 'required|string',
            'delivery_method'   => 'required|numeric',
            'service'           => 'nullable|required_unless:delivery_method,0|integer',
        ];

        $messages = [
            'recipient.name.required'    => 'Recipient name field is required',
            'recipient.mobile.required'  => 'Recipient mobile number field is required',
            'recipient.address.required' => 'Recipient address field is required',
            'service.required_unless'    => 'Service field is required if delivery method is not an agent',
        ];

        $serviceFormData = null;

        if ($request->service) {
            $service = Service::find($request->service);
            if (!$service) {
                return responseError('service', ['Invalid Service']);
            }

            if ($service->form_id) {
                $form = Form::where('act', 'service_form')->find($service->form_id);
                if (!$form) {
                    return responseError('service_form', ['Invalid request']);
                }

                $formData       = $form->form_data;
                $formProcessor  = new FormProcessor();
                $validationRule = $formProcessor->valueValidation($formData);
                $rules          = array_merge($rules, $validationRule);

                $validator = Validator::make($request->all(), $rules, $messages);
                if ($validator->fails()) {
                    return responseError('validation_error', $validator->errors());
                }
                $serviceFormData = $formProcessor->processFormData($request, $formData);
            } else {
                $validator = Validator::make($request->all(), $rules, $messages);
                if ($validator->fails()) {
                    return responseError('validation_error', $validator->errors());
                }
            }
        } else {
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return responseError('validation_error', $validator->errors());
            }
        }

        if ($request->delivery_method) {
            $countryDeliveryMethod = CountryDeliveryMethod::where('country_id', $request->recipient_country)
                ->where('delivery_method_id', $request->delivery_method)
                ->exists();
        } else {
            $receivingCountry = Country::find($request->recipient_country);
            $countryDeliveryMethod = $receivingCountry && gs()->agent_module && $receivingCountry->has_agent;
        }

        if (!$countryDeliveryMethod) {
            return responseError('delivery_method', ['Invalid delivery method selected']);
        }

        $user    = auth()->user();
        $payment = new ProcessSendMoney();

        // payment_type 1 = wallet, 2 = online gateway, 3 = Apaylo Interac, 4 = account balance
        if ($request->payment_type == 1) {
            $validator = Validator::make($request->all(), ['wallet_id' => 'required|integer']);
            if ($validator->fails()) {
                return responseError('validation_error', $validator->errors());
            }

            $wallet = Wallet::forUser($user->id)->active()->find($request->wallet_id);
            if (!$wallet) {
                return responseError('not_found', ['Selected wallet not found']);
            }

            $walletCountry      = Country::where('currency', $wallet->currency_code)->first();
            $walletRate         = (float) ($walletCountry->rate ?? 1);
            $walletDeductAmount = round($payment->amountWithCharge * $walletRate, 8);

            if ($wallet->balance < $walletDeductAmount) {
                return responseError('insufficient_balance', [
                    'Insufficient balance in ' . $wallet->currency_code . ' wallet. Need ' .
                    number_format($walletDeductAmount, 2) . ' ' . $wallet->currency_code .
                    ', available ' . number_format($wallet->balance, 2) . ' ' . $wallet->currency_code . '.',
                ]);
            }

            $payment->wallet             = $wallet;
            $payment->walletDeductAmount = $walletDeductAmount;
        }

        // Account balance payment — deduct from user.balance in system default currency
        if ($request->payment_type == 4) {
            if ($user->balance < $payment->amountWithCharge) {
                return responseError('insufficient_balance', [
                    'Insufficient account balance. Need ' .
                    number_format($payment->amountWithCharge, 2) . ' ' . gs('cur_text') .
                    ', available ' . number_format($user->balance, 2) . ' ' . gs('cur_text') . '.',
                ]);
            }
        }

        $payment->user       = $user;
        $payment->columnName = 'user_id';
        $sendMoney           = $payment->createSendMoney($request, $serviceFormData);

        // AML: score transaction risk
        try {
            app(AmlRiskService::class)->scoreTransaction($sendMoney, 'send_money');
        } catch (\Throwable $e) {
            logger()->error('AML scoring error (send_money API): ' . $e->getMessage());
        }

        // Wallet or account balance — deduct and complete immediately
        if ($request->payment_type == 1 || $request->payment_type == 4) {
            $payment->createTransaction();
            ProcessSendMoney::updateSendMoney($sendMoney, $user);

            return responseSuccess('send_money', ['Send money request submitted successfully'], [
                'payment_type' => $request->payment_type == 1 ? 'wallet' : 'account_balance',
                'send_money'   => $sendMoney,
            ]);
        }

        // Apaylo Interac e-Transfer — initiate deposit through Apaylo gateway
        if ($request->payment_type == 3) {
            $gate = GatewayCurrency::whereHas('method', function ($q) {
                $q->where('alias', 'apaylo')->where('status', Status::ENABLE);
            })->first();

            if (!$gate) {
                return responseError('gateway_unavailable', ['Apaylo payment gateway is not available. Please choose another method.']);
            }

            $amount      = $sendMoney->base_currency_amount + $sendMoney->base_currency_charge;
            $charge      = $gate->fixed_charge + ($amount * $gate->percent_charge / 100);
            $payable     = $amount + $charge;
            $finalAmount = $payable * $gate->rate;

            $deposit                  = new Deposit();
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
            $deposit->from_api        = 1;
            $deposit->is_web          = $request->is_web ? 1 : 0;
            $deposit->success_url     = $request->success_url ?? url('/');
            $deposit->failed_url      = $request->failed_url ?? url('/');
            $deposit->save();

            $dirName     = $gate->method->alias;
            $processor   = 'App\\Http\\Controllers\\Gateway\\' . $dirName . '\\ProcessController';
            $gatewayData = json_decode($processor::process($deposit));

            return responseSuccess('send_money', ['Send money request submitted — complete Interac payment'], [
                'payment_type' => 'interac',
                'send_money'   => $sendMoney,
                'deposit'      => $deposit,
                'gateway_data' => $gatewayData,
            ]);
        }

        // payment_type 2 — online gateway, client calls payment/insert next
        session()->put('payment_trx', $sendMoney->trx);

        return responseSuccess('send_money', ['Send money request submitted successfully'], [
            'payment_type' => 'gateway',
            'send_money'   => $sendMoney,
            'trx'          => $sendMoney->trx,
        ]);
    }

    public function history()
    {
        $transfers    = SendMoney::with('deposit.gateway', 'recipientCountry', 'countryDeliveryMethod.deliveryMethod')->filterUser()->latest()->paginate(getPaginate());

        return response()->json([
            'status' => 'success',
            'data' => [
                'transfers' => $transfers,
            ],
        ]);
    }

    public function getServices(Request $request)
    {
        $services = [];
        if ($request->country_id && $request->delivery_method_id) {
            $countryDeliveryMethod = CountryDeliveryMethod::where('country_id', $request->country_id)
                ->where('delivery_method_id', $request->delivery_method_id)
                ->first();
            if ($countryDeliveryMethod) {
                $services = Service::where('country_delivery_method_id', $countryDeliveryMethod->id)->with('form')->orderBy('name')->get();
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'services' => $services
            ]
        ]);
    }

    public function sendMoneyDetail($id)
    {
        $sendMoney = SendMoney::where('user_id', auth()->id())->find($id);
        if (!$sendMoney) {
            return response()->json([
                'remark' => 'send_money_detail',
                'status' => 'error',
                "message" => ['error' => 'Invalid request']
            ]);
        }

        return response()->json([
            'remark' => 'send_money_detail',
            'status' => 'success',
            'data' => [
                'send_money' => $sendMoney,
            ]
        ]);
    }

    public function trackTransfer($trx)
    {
        $sendMoney = SendMoney::with('recipientCountry', 'countryDeliveryMethod.deliveryMethod', 'sendingCountry')
            ->where('user_id', auth()->id())
            ->where('trx', $trx)
            ->first();

        if (!$sendMoney) {
            return response()->json([
                'remark' => 'track_transfer',
                'status' => 'error',
                'message' => ['error' => 'Transfer not found'],
            ]);
        }

        return response()->json([
            'remark' => 'track_transfer',
            'status' => 'success',
            'data'   => [
                'trx'                    => $sendMoney->trx,
                'status'                 => $sendMoney->status,
                'status_label'           => $sendMoney->status_badge['text'],
                'payment_status'         => $sendMoney->payment_status,
                'sending_amount'         => $sendMoney->sending_amount,
                'sending_currency'       => $sendMoney->sending_currency,
                'recipient_amount'       => $sendMoney->recipient_amount,
                'recipient_currency'     => $sendMoney->recipient_currency,
                'sending_country'        => optional($sendMoney->sendingCountry)->name,
                'recipient_country'      => optional($sendMoney->recipientCountry)->name,
                'delivery_method'        => optional(optional($sendMoney->countryDeliveryMethod)->deliveryMethod)->name,
                'recipient'              => $sendMoney->recipient,
                'created_at'             => $sendMoney->created_at,
                'updated_at'             => $sendMoney->updated_at,
            ],
        ]);
    }

    public function trackTransferPublic($trx)
    {
        $sendMoney = SendMoney::with('recipientCountry', 'countryDeliveryMethod.deliveryMethod', 'sendingCountry')
            ->where('trx', $trx)
            ->first();

        if (!$sendMoney) {
            return response()->json([
                'remark' => 'track_transfer_public',
                'status' => 'error',
                'message' => ['error' => 'Transfer not found'],
            ]);
        }

        return response()->json([
            'remark' => 'track_transfer_public',
            'status' => 'success',
            'data'   => [
                'trx'                => $sendMoney->trx,
                'status'             => $sendMoney->status,
                'status_label'       => $sendMoney->status_badge['text'],
                'sending_currency'   => $sendMoney->sending_currency,
                'recipient_currency' => $sendMoney->recipient_currency,
                'sending_country'    => optional($sendMoney->sendingCountry)->name,
                'recipient_country'  => optional($sendMoney->recipientCountry)->name,
                'delivery_method'    => optional(optional($sendMoney->countryDeliveryMethod)->deliveryMethod)->name,
                'created_at'         => $sendMoney->created_at,
            ],
        ]);
    }
}
