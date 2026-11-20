<?php

namespace App\Lib;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Country;
use App\Models\CountryDeliveryMethod;
use App\Models\Recipient;
use App\Models\SendingPurpose;
use App\Models\SendMoney;
use App\Models\SourceOfFund;
use App\Models\Transaction;
use App\Models\PayoutApiCountry;
use App\Services\PawaPayPayoutService;
use App\Services\PagaPayoutService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProcessSendMoney
{
    public $user;
    public $columnName;
    public $sendingAmount;
    public $sendingCountry;
    public $recipientCountry;
    public $sourceOfFunds;
    public $sendingPurpose;

    public $sendingAmountInBaseCurrency;
    public $chargeInBaseCurrency;

    public $chargeInSendingCurrency;
    public $receivableAmount;

    public $amountWithCharge;
    public $conversionRate;

    public $countryDeliveryMethod;

    public $trx;
    public $sendMoneyId;

    // Wallet payment — set when payment_type == 1
    public $wallet            = null;
    public $walletDeductAmount = 0;

    public function __construct()
    {
        $request                     = request();
        $this->sendingAmount         = $request->sending_amount;
        $this->sendingCountry        = Country::with('conversionRates')->findOrFail($request->sending_country);
        $this->recipientCountry      = Country::findOrFail($request->recipient_country);
        $this->sourceOfFunds         = SourceOfFund::findOrFail($request->source_of_funds);
        $this->sendingPurpose        = SendingPurpose::findOrFail($request->sending_purpose);
        $this->countryDeliveryMethod = CountryDeliveryMethod::with('charge')->where('country_id', $request->recipient_country)->where('delivery_method_id', $request->delivery_method)->first();
        $this->trx                   = getTrx();
        $this->setAmountVariables();
    }

    public function createSendMoney($request, $serviceFormData = null)
    {


        if ($this->columnName == 'user_id') {
            $this->userTransferAbility();
        } else {
            $this->agentTransferAbility();
        }

        if ($request->save_recipient == 'on') {
            $mobile = $request->recipient['mobile'] ?? null;
            $email  = $request->recipient['email']  ?? null;

            $recipient = Recipient::where('user_id', auth()->id())
                ->where(function ($q) use ($mobile, $email) {
                    if ($mobile) $q->where('mobile', $mobile);
                    if ($email)  $q->orWhere('email', $email);
                })
                ->when(!$mobile && !$email, fn($q) => $q->whereRaw('0=1')) // no identifiers → always create new
                ->first();

            if (!$recipient) {
                $recipient = new Recipient();
            }

            $recipient->user_id              = $this->user->id;
            $recipient->name                 = @$request->recipient['name'];
            $recipient->mobile               = @$request->recipient['mobile'];
            $recipient->email                = @$request->recipient['email'];
            $recipient->address              = @$request->recipient['address'];
            $recipient->country_id = $this->recipientCountry->id ?? null;
            $recipient->delivery_method_id   = $this->countryDeliveryMethod
                ? $this->countryDeliveryMethod->delivery_method_id
                : null;
            $recipient->service_id           = $request->service ?? null;
            // Persist service form data in the same shape as history/receipt views use
            if ($serviceFormData) {
                $formDataForRecipient = [];
                foreach ((array) $serviceFormData as $field) {
                    if (is_object($field)) {
                        $formDataForRecipient[] = [
                            'name'  => $field->name  ?? '',
                            'value' => $field->value ?? '',
                            'type'  => $field->type  ?? 'text',
                        ];
                    } elseif (is_array($field)) {
                        $formDataForRecipient[] = $field;
                    }
                }
                $recipient->service_form_data = $formDataForRecipient;
            }
            $recipient->save();
        }

        $sendMoney              = new SendMoney();
        $column                 = $this->columnName;
        $sender                 = (array) ($request->sender ?? []);
        $recipient              = (array) ($request->recipient ?? []);
        $sendMoney->mtcn_number = getTrx(10);
        $sendMoney->$column     = $this->user->id;
        $sender['mobile']       = $this->sendingCountry->dial_code . ($sender['mobile'] ?? '');
        $sender['email']        = $sender['email'] ?? '';
        $sender['address']      = $sender['address'] ?? '';

        $recipient['mobile']  = $this->recipientCountry->dial_code . ($recipient['mobile'] ?? '');
        $recipient['email']   = $recipient['email'] ?? '';
        $recipient['address'] = $recipient['address'] ?? '';

        $sendMoney->service_id                 = $request->service ?? 0;
        $sendMoney->service_form_data          = $serviceFormData;
        $sendMoney->country_delivery_method_id = $this->countryDeliveryMethod->id ?? 0;
        $sendMoney->base_currency_amount       = $this->sendingAmountInBaseCurrency;
        $sendMoney->base_currency_charge       = $this->chargeInBaseCurrency;

        $sendMoney->sending_country_id   = $this->sendingCountry->id;
        $sendMoney->sending_currency     = $this->sendingCountry->currency;
        $sendMoney->sending_amount       = $this->sendingAmount;
        $sendMoney->sending_charge       = $this->chargeInSendingCurrency;
        $sendMoney->recipient_country_id = $this->recipientCountry->id;
        $sendMoney->recipient_currency   = $this->recipientCountry->currency;
        $sendMoney->recipient_amount     = $this->receivableAmount;
        $sendMoney->source_of_fund_id    = $this->sourceOfFunds->id;
        $sendMoney->sending_purpose_id   = $this->sendingPurpose->id;
        $sendMoney->trx                  = $this->trx;
        $sendMoney->conversion_rate      = $this->conversionRate;
        $sendMoney->base_currency_rate   = $this->sendingCountry->rate;
        $sendMoney->sender               = $sender;
        $sendMoney->recipient            = $recipient;
        $sendMoney->save();

        $this->sendMoneyId = $sendMoney->id;
        return $sendMoney;
    }

    private function userTransferAbility()
    {
        $general = gs();


        $sendingAmountInBaseCurrency = getAmount($this->sendingAmountInBaseCurrency);


        // Per-transfer limit — 0 means unlimited
        $perLimit = !is_null($this->user->per_send_money_limit)
            ? $this->user->per_send_money_limit
            : $general->user_send_money_limit;

        if ($perLimit > 0 && $sendingAmountInBaseCurrency > $perLimit) {
            throw ValidationException::withMessages(['error' => 'Sending amount exceeds the limit per transfer']);
        }

        $allSendMoney = SendMoney::where('user_id', $this->user->id)->whereIn('status', [Status::SEND_MONEY_PENDING, Status::SEND_MONEY_COMPLETED]);

        // Monthly limit — 0 means unlimited
        $monthlyLimit = !is_null($this->user->monthly_send_money_limit)
            ? $this->user->monthly_send_money_limit
            : $general->user_monthly_send_money_limit;

        if ($monthlyLimit > 0) {
            $thisMonthSendMoney = (clone $allSendMoney)->whereMonth('created_at', now()->month)->sum('base_currency_amount');
            $thisMonthAvailable = getAmount($monthlyLimit - $thisMonthSendMoney);
            if ($thisMonthAvailable < $sendingAmountInBaseCurrency) {
                throw ValidationException::withMessages(['error' => 'Your monthly send money amount exceeds the monthly send money limit']);
            }
        }

        // Daily limit — 0 means unlimited
        $dailyLimit = !is_null($this->user->daily_send_money_limit)
            ? $this->user->daily_send_money_limit
            : $general->user_daily_send_money_limit;

        if ($dailyLimit > 0) {
            $todaySendMoney = (clone $allSendMoney)->whereDate('created_at', now())->sum('base_currency_amount');
            $todayAvailable = getAmount($dailyLimit - $todaySendMoney);
            if ($todayAvailable < $sendingAmountInBaseCurrency) {
                throw ValidationException::withMessages(['error' => 'Your daily send money amount exceeds the daily send money limit']);
            }
        }
    }

    private function agentTransferAbility()
    {
        $general                     = gs();
        $sendingAmountInBaseCurrency = getAmount($this->sendingAmountInBaseCurrency);

        // Per-transfer limit — 0 means unlimited
        if ($general->agent_send_money_limit > 0 && $sendingAmountInBaseCurrency > $general->agent_send_money_limit) {
            throw ValidationException::withMessages(['error' => 'Sending amount exceeds the limit per transfer']);
        }

        $allSendMoney = SendMoney::where('agent_id', $this->user->id)->whereIn('status', [Status::SEND_MONEY_PENDING, Status::SEND_MONEY_COMPLETED]);

        // Monthly limit — 0 means unlimited
        if ($general->agent_monthly_send_money_limit > 0) {
            $thisMonthSendMoney = (clone $allSendMoney)->whereMonth('created_at', now()->month)->sum('base_currency_amount');
            $thisMonthAvailable = getAmount($general->agent_monthly_send_money_limit - $thisMonthSendMoney);
            if ($thisMonthAvailable < $sendingAmountInBaseCurrency) {
                throw ValidationException::withMessages(['error' => 'Your monthly send money amount exceeds the monthly send money limit']);
            }
        }

        // Daily limit — 0 means unlimited
        if ($general->agent_daily_send_money_limit > 0) {
            $todaySendMoney = (clone $allSendMoney)->whereDate('created_at', now())->sum('base_currency_amount');
            $todayAvailable = getAmount($general->agent_daily_send_money_limit - $todaySendMoney);
            if ($todayAvailable < $sendingAmountInBaseCurrency) {
                throw ValidationException::withMessages(['error' => 'Your daily send money amount exceeds the daily send money limit']);
            }
        }
    }

    public function setAmountVariables()
    {
        $senderToReceiverConversionRate = $this->sendingCountry->conversionRates->where('to_country', $this->recipientCountry->id)->first();


        if ($senderToReceiverConversionRate) {
            $conversionRate = $senderToReceiverConversionRate->rate;
        } else {
            $conversionRate = $this->recipientCountry->rate / $this->sendingCountry->rate;
        }

        $this->conversionRate = $conversionRate;
        $receivableAmount     = $conversionRate * $this->sendingAmount;  //In Recipient's currency

        if ($this->countryDeliveryMethod) {
            $fixedCharge   = @$this->countryDeliveryMethod->charge->fixed_charge ?? 0;
            $percentCharge = @$this->countryDeliveryMethod->charge->percent_charge ?? 0;
        } else {
            $general       = gs();
            $fixedCharge   = @$general->agent_charges->fixed_charge ?? 0;
            $percentCharge = @$general->agent_charges->percent_charge ?? 0;
        }

        $percentCharge             = $receivableAmount * $percentCharge / 100;
        $chargeInRecipientCurrency = ($fixedCharge * $conversionRate) + $percentCharge;
        $chargeInSendingCurrency   = $chargeInRecipientCurrency / $conversionRate;

        $this->chargeInBaseCurrency        = $chargeInSendingCurrency / $this->sendingCountry->rate;
        $this->sendingAmountInBaseCurrency = $this->sendingAmount / $this->sendingCountry->rate;
        $this->chargeInSendingCurrency     = $chargeInSendingCurrency;
        $this->receivableAmount            = $receivableAmount;
        $this->amountWithCharge            = $this->sendingAmountInBaseCurrency + $this->chargeInBaseCurrency;
    }


    public static function updateSendMoney($sendMoney, $user)
    {
        $sendMoney->payment_status = Status::PAYMENT_SUCCESS;
        $sendMoney->status         = Status::SEND_MONEY_PENDING;
        $sendMoney->save();
        $general = gs();

        if ($general->referral_system && $sendMoney->user_id && $user->ref_by && $general->commission_count > $user->total_bonus_given) {
            giveReferralCommission($user, $sendMoney);
        }

        notify($user, 'SEND_MONEY_COMPLETE', [
            'trx'                => $sendMoney->trx,
            'sending_country'    => @$sendMoney->sendingCountry->name,
            'sending_amount'     => showAmount($sendMoney->sending_amount, currencyFormat: false),
            'sending_currency'   => $sendMoney->sending_currency,
            'recipient__country' => @$sendMoney->recipientCountry->name,
            'recipient_amount'   => showAmount($sendMoney->recipient_amount, currencyFormat: false),
            'recipient_currency' => $sendMoney->recipient_currency,
        ]);

        // Auto-trigger payout if an active provider is configured for this recipient country
        $payoutCountry = PayoutApiCountry::autoForCurrency($sendMoney->recipient_currency);

        if ($payoutCountry) {
            // Attach the first active method (PawaPay uses this; Paga ignores it)
            $method = $payoutCountry->activeMethods()->first();
            if ($method) {
                $sendMoney->payout_api_method_id = $method->id;
                $sendMoney->save();
            }

            try {
                match ($payoutCountry->payout_provider) {
                    PayoutApiCountry::PROVIDER_PAWAPAY => (new PawaPayPayoutService())->initiatePayout($sendMoney),
                    PayoutApiCountry::PROVIDER_PAGA    => (new PagaPayoutService())->initiatePayout($sendMoney),
                    default => null,
                };
            } catch (\Throwable $e) {
                Log::error('AutoPayout: initiatePayout exception (' . $payoutCountry->payout_provider . '): ' . $e->getMessage());
            }
        }
    }

    public function createTransaction()
    {
        DB::transaction(function () {
            $user   = $this->user;
            $column = $this->columnName;

            if ($this->wallet) {
                // Deduct from the selected wallet
                $affected = DB::table('wallets')
                    ->where('id', $this->wallet->id)
                    ->where('balance', '>=', $this->walletDeductAmount)
                    ->decrement('balance', $this->walletDeductAmount);

                if (!$affected) {
                    throw new \RuntimeException('Insufficient wallet balance');
                }

                $this->wallet->balance -= $this->walletDeductAmount;
                $postBalance            = $this->wallet->balance;
                $txAmount               = $this->walletDeductAmount;
                $details                = 'Send money to ' . $this->recipientCountry->name . ' from ' . $this->wallet->currency_code . ' wallet';
            } else {
                // Deduct from the user or agent account balance
                $targetTable = ($column === 'agent_id') ? 'agents' : 'users';
                $affected = DB::table($targetTable)
                    ->where('id', $user->id)
                    ->where('balance', '>=', $this->amountWithCharge)
                    ->decrement('balance', $this->amountWithCharge);

                if (!$affected) {
                    throw new \RuntimeException('Insufficient balance');
                }

                $user->balance -= $this->amountWithCharge;
                $postBalance    = $user->balance;
                $txAmount       = $this->amountWithCharge;
                $details        = 'Send money to ' . $this->recipientCountry->name;
            }

            $adminNotification          = new AdminNotification();
            $adminNotification->$column = $user->id;
            $adminNotification->title     = 'Send money to ' . $this->recipientCountry->name;
            $adminNotification->click_url = urlPath('admin.send.money.details', $this->sendMoneyId);
            $adminNotification->save();

            $transaction               = new Transaction();
            $transaction->$column      = $user->id;
            $transaction->wallet_id    = $this->wallet ? $this->wallet->id : null;
            $transaction->amount       = $txAmount;
            $transaction->post_balance = $postBalance;
            $transaction->charge       = 0;
            $transaction->trx_type     = '-';
            $transaction->details      = $details;
            $transaction->remark       = 'send_money_payment';
            $transaction->trx          = $this->trx;
            $transaction->save();
        });
    }
}
