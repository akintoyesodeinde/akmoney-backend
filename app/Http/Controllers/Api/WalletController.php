<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Country;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletExchange;
use App\Models\WalletTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    // ── List all wallets for the authenticated user ───────────────────────
    public function index()
    {
        if (!gs('wallet_module')) {
            return responseError('feature_disabled', ['Wallet module is disabled']);
        }

        $general         = gs();
        $isMultiCurrency = (bool) $general->wallet_currency_mode;
        $user            = auth()->user();

        // Single-currency: auto-create the default wallet if the user has none
        if (!$isMultiCurrency) {
            $defaultCurrency = $general->cur_text;
            $hasWallet       = Wallet::forUser($user->id)->where('currency_code', $defaultCurrency)->exists();
            if (!$hasWallet) {
                $country = Country::active()->where('currency', $defaultCurrency)->first();
                Wallet::create([
                    'user_id'            => $user->id,
                    'currency_code'      => $defaultCurrency,
                    'currency_name'      => ($country ? $country->name . ' / ' : '') . $defaultCurrency,
                    'currency_symbol'    => $defaultCurrency,
                    'balance'            => 0,
                    'is_default'         => true,
                    'remittance_enabled' => $country
                        ? $country->is_sending == Status::ENABLE || $country->is_receiving == Status::ENABLE
                        : false,
                    'status'             => Wallet::STATUS_ACTIVE,
                ]);
            }
        }

        $walletsQuery = Wallet::forUser($user->id)
            ->active()
            ->orderByDesc('is_default')
            ->orderBy('currency_code');

        // In single-currency mode only expose the system-default-currency wallet
        if (!$isMultiCurrency) {
            $walletsQuery->where('currency_code', $general->cur_text);
        }

        $wallets = $walletsQuery->get();

        return responseSuccess('wallets', ['Wallet list'], [
            'wallets'         => $wallets,
            'currency_mode'   => $isMultiCurrency ? 'multi' : 'single',
            'system_currency' => $general->cur_text,
        ]);
    }

    // ── Supported currencies the user can open a wallet in ─────────────────
    public function currencies()
    {
        if (!gs('wallet_module')) {
            return responseError('feature_disabled', ['Wallet module is disabled']);
        }
        $existing = Wallet::forUser(auth()->id())->pluck('currency_code');

        // In single-currency mode only the system default currency is available
        if (!gs('wallet_currency_mode')) {
            $default  = gs('cur_text');
            $country  = Country::active()->where('currency', $default)->first();
            $currencies = collect([[
                'currency_code'      => $default,
                'country_name'       => $country?->name ?? '',
                'remittance_enabled' => $country
                    ? $country->is_sending == Status::ENABLE || $country->is_receiving == Status::ENABLE
                    : false,
                'has_wallet'         => $existing->contains($default),
            ]]);
            return responseSuccess('currencies', ['Supported currencies'], [
                'currencies'      => $currencies,
                'currency_mode'   => 'single',
                'system_currency' => $default,
            ]);
        }

        $allowedCodes = walletAllowedCurrencyCodes(); // null = unrestricted

        $query = Country::active()
            ->select('currency', 'name', 'is_sending', 'is_receiving')
            ->whereNotNull('currency')
            ->where('currency', '!=', '');

        if ($allowedCodes !== null) {
            $query->whereIn('currency', $allowedCodes);
        }

        $currencies = $query->get()
            ->unique('currency')
            ->values()
            ->map(fn($c) => [
                'currency_code'      => $c->currency,
                'country_name'       => $c->name,
                'remittance_enabled' => $c->is_sending == Status::ENABLE || $c->is_receiving == Status::ENABLE,
                'has_wallet'         => $existing->contains($c->currency),
            ]);

        return responseSuccess('currencies', ['Supported currencies'], [
            'currencies'       => $currencies,
            'currency_mode'    => 'multi',
            'restricted'       => $allowedCodes !== null,
            'allowed_count'    => $allowedCodes !== null ? count($allowedCodes) : null,
        ]);
    }

    // ── Create a new wallet ───────────────────────────────────────────────
    public function store(Request $request)
    {
        if (!gs('wallet_module')) {
            return responseError('feature_disabled', ['Wallet module is disabled']);
        }

        $user = auth()->user();

        // Single-currency mode: currency is always the system default
        if (!gs('wallet_currency_mode')) {
            $code = strtoupper(gs('cur_text'));
        } else {
            $validator = Validator::make($request->all(), [
                'currency_code' => 'required|string|max:10',
            ]);
            if ($validator->fails()) {
                return responseError('validation_error', $validator->errors());
            }
            $code = strtoupper($request->currency_code);

            // Check against admin-restricted currency list
            $allowedCodes = walletAllowedCurrencyCodes();
            if ($allowedCodes !== null && !in_array($code, $allowedCodes)) {
                return responseError('currency_not_allowed', [$code . ' is not available for wallet creation']);
            }
        }

        $exists = Wallet::forUser($user->id)->where('currency_code', $code)->exists();
        if ($exists) {
            return responseError('wallet_exists', ['You already have a wallet for ' . $code]);
        }

        $country = Country::active()
            ->where('currency', $code)
            ->first();

        if (!$country) {
            return responseError('invalid_currency', ['Currency not supported']);
        }

        $isDefault  = !Wallet::forUser($user->id)->exists(); // first wallet is default
        $remittance = $country->is_sending == Status::ENABLE || $country->is_receiving == Status::ENABLE;

        $wallet = Wallet::create([
            'user_id'            => $user->id,
            'currency_code'      => $code,
            'currency_name'      => $country->name . ' / ' . $code,
            'currency_symbol'    => $code,
            'balance'            => 0,
            'is_default'         => $isDefault,
            'remittance_enabled' => $remittance,
            'status'             => Wallet::STATUS_ACTIVE,
        ]);

        return responseSuccess('wallet_created', ['Wallet created successfully'], ['wallet' => $wallet]);
    }

    // ── Get single wallet ─────────────────────────────────────────────────
    public function show(int $id)
    {
        if (!gs('wallet_module')) {
            return responseError('feature_disabled', ['Wallet module is disabled']);
        }

        $wallet = Wallet::forUser(auth()->id())->active()->find($id);
        if (!$wallet) {
            return responseError('not_found', ['Wallet not found']);
        }
        return responseSuccess('wallet', ['Wallet details'], ['wallet' => $wallet]);
    }

    // ── Initiate and process a deposit (top-up) into a specific wallet ──────
    public function depositInit(Request $request, int $walletId)
    {
        if (!gs('wallet_module')) {
            return responseError('feature_disabled', ['Wallet module is disabled']);
        }
        $validator = Validator::make($request->all(), [
            'amount'      => 'required|numeric|gt:0',
            'method_code' => 'required',
            'currency'    => 'required',
        ]);
        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user   = auth()->user();
        $wallet = Wallet::forUser($user->id)->active()->find($walletId);

        if (!$wallet) {
            return responseError('not_found', ['Wallet not found']);
        }

        $gate = GatewayCurrency::whereHas('method', fn($q) => $q->where('status', Status::ENABLE))
            ->where('method_code', $request->method_code)
            ->where('currency', $request->currency)
            ->first();

        if (!$gate) {
            return responseError('invalid_gateway', ['Invalid payment method']);
        }

        $amount = (float) $request->amount;
        if ($gate->min_amount > $amount || $gate->max_amount < $amount) {
            return responseError('gateway_limit', ['Amount must be between ' . $gate->min_amount . ' and ' . $gate->max_amount . ' ' . $gate->currency]);
        }

        $charge      = $gate->fixed_charge + ($amount * $gate->percent_charge / 100);
        $payable     = $amount + $charge;
        $finalAmount = $payable * ($gate->rate ?? 1);

        $deposit                  = new Deposit();
        $deposit->user_id         = $user->id;
        $deposit->wallet_id       = $wallet->id;
        $deposit->trx             = getTrx();
        $deposit->method_code     = $gate->method_code;
        $deposit->method_currency = strtoupper($gate->currency);
        $deposit->amount          = $amount;
        $deposit->charge          = $charge;
        $deposit->rate            = $gate->rate ?? 1;
        $deposit->final_amount    = $finalAmount;
        $deposit->btc_amount      = 0;
        $deposit->btc_wallet      = '';
        $deposit->payment_try     = 0;
        $deposit->status          = Status::PAYMENT_INITIATE;
        $deposit->from_api        = 1;
        $deposit->is_web          = $request->is_web ? 1 : 0;
        $deposit->success_url     = $request->success_url ?? url('/');
        $deposit->failed_url      = $request->failed_url ?? url('/');
        $deposit->save();

        // Automatic gateways (code < 1000): process through the gateway now
        if ($gate->method_code < 1000) {
            $dirName     = $gate->method->alias;
            $processor   = 'App\\Http\\Controllers\\Gateway\\' . $dirName . '\\ProcessController';
            $gatewayData = json_decode($processor::process($deposit));

            if (@$gatewayData->session) {
                $deposit->btc_wallet = $gatewayData->session->id;
                $deposit->save();
            }

            $deposit->load('gateway', 'gateway.form');

            if (isset($gatewayData->redirect) && !$request->is_web) {
                return responseSuccess('deposit_initiated', ['Deposit initiated'], [
                    'deposit'      => $deposit,
                    'redirect_url' => $gatewayData->redirect_url,
                    'amount'       => $amount,
                    'charge'       => $charge,
                    'payable'      => $payable,
                    'currency'     => $gate->currency,
                    'wallet'       => $wallet,
                ]);
            }

            return responseSuccess('deposit_initiated', ['Deposit initiated'], [
                'deposit'      => $deposit,
                'gateway_data' => $gatewayData,
                'amount'       => $amount,
                'charge'       => $charge,
                'payable'      => $payable,
                'currency'     => $gate->currency,
                'wallet'       => $wallet,
            ]);
        }

        // Manual gateways (code >= 1000): return redirect to manual confirmation page
        return responseSuccess('deposit_initiated', ['Deposit initiated — complete via manual confirmation'], [
            'deposit'      => $deposit,
            'redirect_url' => route('deposit.app.confirm', encrypt($deposit->id)),
            'amount'       => $amount,
            'charge'       => $charge,
            'payable'      => $payable,
            'currency'     => $gate->currency,
            'wallet'       => $wallet,
        ]);
    }

    // ── Confirm a manual wallet deposit ───────────────────────────────────
    public function depositConfirm(Request $request, int $walletId)
    {
        if (!gs('wallet_module')) {
            return responseError('feature_disabled', ['Wallet module is disabled']);
        }
        $validator = Validator::make($request->all(), [
            'trx' => 'required|string',
        ]);
        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user    = auth()->user();
        $wallet  = Wallet::forUser($user->id)->active()->find($walletId);

        if (!$wallet) {
            return responseError('not_found', ['Wallet not found']);
        }

        $deposit = Deposit::where('trx', $request->trx)
            ->where('user_id', $user->id)
            ->where('wallet_id', $wallet->id)
            ->where('status', Status::PAYMENT_INITIATE)
            ->with('gateway')
            ->first();

        if (!$deposit) {
            return responseError('not_found', ['Pending deposit not found for this wallet']);
        }

        $gatewayCurrency = $deposit->gatewayCurrency();
        $gateway         = $gatewayCurrency->method;

        if (!$gateway->form) {
            return responseError('no_form', ['This gateway does not require manual confirmation']);
        }

        $formData = $gateway->form->form_data;

        $formProcessor  = new \App\Lib\FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $validator2     = Validator::make($request->all(), $validationRule);
        if ($validator2->fails()) {
            return responseError('validation_error', $validator2->errors());
        }

        $userData       = $formProcessor->processFormData($request, $formData);
        $deposit->detail = $userData;
        $deposit->status = Status::PAYMENT_PENDING;
        $deposit->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'Wallet deposit request from ' . $user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $deposit->id);
        $adminNotification->save();

        notify($user, 'DEPOSIT_REQUEST', [
            'method_name'     => $gatewayCurrency->name,
            'method_currency' => $deposit->method_currency,
            'method_amount'   => showAmount($deposit->final_amount, currencyFormat: false),
            'amount'          => showAmount($deposit->amount, currencyFormat: false),
            'charge'          => showAmount($deposit->charge, currencyFormat: false),
            'rate'            => showAmount($deposit->rate, currencyFormat: false),
            'trx'             => $deposit->trx,
        ]);

        return responseSuccess('deposit_request_taken', ['Deposit request submitted for review'], [
            'trx'    => $deposit->trx,
            'wallet' => $wallet,
        ]);
    }

    // ── List available deposit methods ────────────────────────────────────
    public function depositMethods()
    {
        if (!gs('wallet_module')) {
            return responseError('feature_disabled', ['Wallet module is disabled']);
        }
        $methods = GatewayCurrency::whereHas('method', fn($q) => $q->where('status', Status::ENABLE))
            ->with('method')
            ->orderBy('method_code')
            ->get();

        return responseSuccess('deposit_methods', ['Payment methods'], [
            'methods'    => $methods,
            'image_path' => getFilePath('gateway'),
        ]);
    }

    // ── Exchange between two wallets ──────────────────────────────────────
    public function exchange(Request $request)
    {
        if (!gs('wallet_currency_mode')) {
            return responseError('feature_disabled', ['Currency exchange is not available in single-currency mode']);
        }

        $validator = Validator::make($request->all(), [
            'from_wallet_id' => 'required|integer',
            'to_wallet_id'   => 'required|integer|different:from_wallet_id',
            'amount'         => 'required|numeric|gt:0',
        ]);
        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user      = auth()->user();
        $fromWallet = Wallet::forUser($user->id)->active()->find($request->from_wallet_id);
        $toWallet   = Wallet::forUser($user->id)->active()->find($request->to_wallet_id);

        if (!$fromWallet || !$toWallet) {
            return responseError('not_found', ['One or both wallets not found']);
        }

        $amount = (float) $request->amount;

        $fromCountry = Country::active()->where('currency', $fromWallet->currency_code)->first();
        $toCountry   = Country::active()->where('currency', $toWallet->currency_code)->first();

        if (!$fromCountry || !$toCountry) {
            return responseError('rate_unavailable', ['Exchange rate not available for this currency pair']);
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
            return responseError('insufficient_balance', [
                'Insufficient balance. Need ' . number_format($totalDeduct, 2) . ' ' . $fromWallet->currency_code .
                ' (amount + fee), available ' . number_format($fromWallet->balance, 2) . ' ' . $fromWallet->currency_code . '.',
            ]);
        }

        $toAmount = round($amount * $exchangeRate, 8);

        DB::transaction(function () use ($fromWallet, $toWallet, $amount, $fee, $totalDeduct, $toAmount, $exchangeRate, $user) {
            $affected = DB::table('wallets')
                ->where('id', $fromWallet->id)
                ->where('balance', '>=', $totalDeduct)
                ->decrement('balance', $totalDeduct);

            if (!$affected) {
                throw new \RuntimeException('Insufficient wallet balance');
            }

            $toWallet->increment('balance', $toAmount);

            $trx = getTrx();

            WalletExchange::create([
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
                'status'         => WalletExchange::STATUS_COMPLETED,
            ]);

            $transaction               = new Transaction();
            $transaction->user_id      = $user->id;
            $transaction->amount       = $totalDeduct;
            $transaction->post_balance = $fromWallet->fresh()->balance;
            $transaction->charge       = $fee;
            $transaction->trx_type     = '-';
            $transaction->remark       = 'wallet_exchange';
            $transaction->details      = 'Exchanged ' . $fromWallet->currency_code . ' → ' . $toWallet->currency_code;
            $transaction->trx          = $trx;
            $transaction->save();

            $transaction               = new Transaction();
            $transaction->user_id      = $user->id;
            $transaction->amount       = $toAmount;
            $transaction->post_balance = $toWallet->fresh()->balance;
            $transaction->charge       = 0;
            $transaction->trx_type     = '+';
            $transaction->remark       = 'wallet_exchange';
            $transaction->details      = 'Received from ' . $fromWallet->currency_code . ' exchange';
            $transaction->trx          = $trx;
            $transaction->save();
        });

        $fromWallet->refresh();
        $toWallet->refresh();

        return responseSuccess('exchange_done', ['Exchange completed'], [
            'from_wallet'   => $fromWallet,
            'to_wallet'     => $toWallet,
            'exchanged'     => $amount,
            'fee'           => $fee,
            'total_deducted' => $totalDeduct,
            'received'      => $toAmount,
            'exchange_rate' => $exchangeRate,
        ]);
    }

    // ── Exchange history ──────────────────────────────────────────────────
    public function exchangeHistory()
    {
        if (!gs('wallet_module')) {
            return responseError('feature_disabled', ['Wallet module is disabled']);
        }
        $history = WalletExchange::forUser(auth()->id())
            ->with('fromWallet:id,currency_code,currency_symbol', 'toWallet:id,currency_code,currency_symbol')
            ->latest()
            ->paginate(20);

        return responseSuccess('exchange_history', ['Exchange history'], ['history' => $history]);
    }

    // ── Deposit history for a wallet ──────────────────────────────────────
    public function depositHistory(int $walletId)
    {
        if (!gs('wallet_module')) {
            return responseError('feature_disabled', ['Wallet module is disabled']);
        }
        $wallet = Wallet::forUser(auth()->id())->find($walletId);
        if (!$wallet) {
            return responseError('not_found', ['Wallet not found']);
        }

        $deposits = Deposit::where('wallet_id', $walletId)
            ->latest()
            ->paginate(20);

        return responseSuccess('deposit_history', ['Deposit history'], ['deposits' => $deposits]);
    }

    // ── Unified transaction history for a wallet ─────────────────────────
    public function walletTransactions(int $id)
    {
        if (!gs('wallet_module')) {
            return responseError('feature_disabled', ['Wallet module is disabled']);
        }
        $user   = auth()->user();
        $wallet = Wallet::forUser($user->id)->active()->find($id);

        if (!$wallet) {
            return responseError('not_found', ['Wallet not found']);
        }

        // Top-ups: deposits credited to this wallet
        $deposits = Deposit::where('wallet_id', $wallet->id)
            ->where('status', Status::PAYMENT_SUCCESS)
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

        // Spends: send-money or other deductions from this wallet
        $spends = Transaction::where('wallet_id', $wallet->id)
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
        $exchanges = WalletExchange::where(function ($q) use ($wallet) {
                $q->where('from_wallet_id', $wallet->id)
                  ->orWhere('to_wallet_id', $wallet->id);
            })
            ->where('user_id', $user->id)
            ->with('fromWallet:id,currency_code', 'toWallet:id,currency_code')
            ->latest()->get()
            ->map(fn($e) => [
                'type'        => 'exchange',
                'date'        => $e->created_at,
                'trx'         => $e->trx,
                'amount'      => $e->from_wallet_id === $wallet->id ? $e->from_amount : $e->to_amount,
                'currency'    => $wallet->currency_code,
                'sign'        => $e->from_wallet_id === $wallet->id ? '-' : '+',
                'description' => $e->from_wallet_id === $wallet->id
                    ? 'Exchange to ' . optional($e->toWallet)->currency_code . ' wallet'
                    : 'Exchange from ' . optional($e->fromWallet)->currency_code . ' wallet',
            ]);

        $items = $deposits->concat($spends)->concat($exchanges)
            ->sortByDesc('date')->values();

        return responseSuccess('wallet_transactions', ['Wallet transactions'], [
            'wallet' => $wallet,
            'items'  => $items,
        ]);
    }

    // ── Set a wallet as default ───────────────────────────────────────────
    public function setDefault(int $id)
    {
        if (!gs('wallet_module')) {
            return responseError('feature_disabled', ['Wallet module is disabled']);
        }
        $user   = auth()->user();
        $wallet = Wallet::forUser($user->id)->active()->find($id);
        if (!$wallet) {
            return responseError('not_found', ['Wallet not found']);
        }

        Wallet::forUser($user->id)->update(['is_default' => false]);
        $wallet->is_default = true;
        $wallet->save();

        return responseSuccess('default_set', ['Default wallet updated'], ['wallet' => $wallet]);
    }

    // ── Look up a transfer recipient ──────────────────────────────────────
    public function transferLookup(Request $request)
    {
        if (!gs('wallet_module') || !gs('wallet_transfer')) {
            return responseError('feature_disabled', ['Wallet transfers are disabled']);
        }

        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'type'       => 'required|in:username,email,mobile',
        ]);
        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $methods = gs()->wallet_transfer_methods ?? ['username', 'email', 'mobile'];
        if (!in_array($request->type, $methods)) {
            return responseError('method_disabled', ['This lookup method is not enabled']);
        }

        $recipient = User::where('status', Status::ENABLE)
            ->where($request->type, $request->identifier)
            ->where('id', '!=', auth()->id())
            ->first();

        if (!$recipient) {
            return responseError('not_found', ['No user found with that ' . $request->type]);
        }

        return responseSuccess('recipient_found', ['Recipient found'], [
            'recipient' => [
                'id'       => $recipient->id,
                'name'     => $recipient->firstname . ' ' . $recipient->lastname,
                'username' => $recipient->username,
            ],
        ]);
    }

    // ── Execute a wallet-to-wallet transfer ───────────────────────────────
    public function transfer(Request $request)
    {
        if (!gs('wallet_module') || !gs('wallet_transfer')) {
            return responseError('feature_disabled', ['Wallet transfers are disabled']);
        }

        $validator = Validator::make($request->all(), [
            'from_wallet_id' => 'required|integer',
            'receiver_id'    => 'required|integer',
            'amount'         => 'required|numeric|gt:0',
        ]);
        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user       = auth()->user();
        $fromWallet = Wallet::forUser($user->id)->active()->find($request->from_wallet_id);

        if (!$fromWallet) {
            return responseError('not_found', ['Wallet not found']);
        }

        if ($request->receiver_id == $user->id) {
            return responseError('invalid_receiver', ['You cannot transfer to yourself']);
        }

        $receiver = User::where('status', Status::ENABLE)->find($request->receiver_id);
        if (!$receiver) {
            return responseError('not_found', ['Recipient not found']);
        }

        $amount  = (float) $request->amount;
        $general = gs();

        $fixedCharge   = (float) ($general->wallet_transfer_fixed_charge ?? 0);
        $percentCharge = (float) ($general->wallet_transfer_percent_charge ?? 0);
        $charge        = $fixedCharge + ($amount * $percentCharge / 100);
        $totalDeduct   = $amount + $charge;

        if ($fromWallet->balance < $totalDeduct) {
            return responseError('insufficient_balance', [
                'Insufficient balance. Need ' . number_format($totalDeduct, 2) . ' ' . $fromWallet->currency_code .
                ' (amount + fee), available ' . number_format($fromWallet->balance, 2) . ' ' . $fromWallet->currency_code . '.',
            ]);
        }

        $toWallet = Wallet::forUser($receiver->id)
            ->where('currency_code', $fromWallet->currency_code)
            ->first();

        if (!$toWallet) {
            $country  = Country::active()->where('currency', $fromWallet->currency_code)->first();
            $toWallet = Wallet::create([
                'user_id'            => $receiver->id,
                'currency_code'      => $fromWallet->currency_code,
                'currency_name'      => $country ? $country->name . ' / ' . $fromWallet->currency_code : $fromWallet->currency_name,
                'currency_symbol'    => $fromWallet->currency_symbol,
                'balance'            => 0,
                'is_default'         => !Wallet::forUser($receiver->id)->exists(),
                'remittance_enabled' => $fromWallet->remittance_enabled,
                'status'             => Wallet::STATUS_ACTIVE,
            ]);
        }

        DB::transaction(function () use ($user, $receiver, $fromWallet, $toWallet, $amount, $charge, $totalDeduct) {
            $affected = DB::table('wallets')
                ->where('id', $fromWallet->id)
                ->where('balance', '>=', $totalDeduct)
                ->decrement('balance', $totalDeduct);

            if (!$affected) {
                throw new \RuntimeException('Insufficient wallet balance');
            }

            $toWallet->increment('balance', $amount);

            $trx = getTrx();

            WalletTransfer::create([
                'trx'                => $trx,
                'sender_id'          => $user->id,
                'receiver_id'        => $receiver->id,
                'sender_wallet_id'   => $fromWallet->id,
                'receiver_wallet_id' => $toWallet->id,
                'currency_code'      => $fromWallet->currency_code,
                'amount'             => $amount,
                'charge'             => $charge,
                'sender_deducted'    => $totalDeduct,
                'receiver_credited'  => $amount,
                'status'             => WalletTransfer::STATUS_COMPLETED,
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

        $fromWallet->refresh();

        return responseSuccess('transfer_done', ['Transfer completed'], [
            'from_wallet'       => $fromWallet,
            'amount'            => $amount,
            'charge'            => $charge,
            'total_deducted'    => $totalDeduct,
            'receiver_credited' => $amount,
            'receiver'          => [
                'id'       => $receiver->id,
                'name'     => $receiver->firstname . ' ' . $receiver->lastname,
                'username' => $receiver->username,
            ],
            'currency' => $fromWallet->currency_code,
        ]);
    }

    // ── Transfer history ──────────────────────────────────────────────────
    public function transferHistory()
    {
        $history = WalletTransfer::forUser(auth()->id())
            ->with(
                'sender:id,firstname,lastname,username',
                'receiver:id,firstname,lastname,username',
                'senderWallet:id,currency_code',
                'receiverWallet:id,currency_code'
            )
            ->latest()
            ->paginate(20);

        return responseSuccess('transfer_history', ['Transfer history'], ['history' => $history]);
    }
}
