<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletExchange;
use App\Models\WalletTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function wallets(Request $request)
    {
        $pageTitle = 'Manage Wallets';

        $wallets = Wallet::with('user:id,firstname,lastname,username,email')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($q2) use ($search) {
                    $q2->where('currency_code', 'like', "%$search%")
                       ->orWhereHas('user', fn($u) => $u->where('username', 'like', "%$search%")
                           ->orWhere('email', 'like', "%$search%")
                           ->orWhereRaw("CONCAT(firstname,' ',lastname) like ?", ["%$search%"]));
                });
            })
            ->when($request->filled('currency'), fn($q) => $q->where('currency_code', $request->currency))
            ->when($request->filled('remittance'), fn($q) => $q->where('remittance_enabled', true))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $currencies   = Wallet::distinct('currency_code')->orderBy('currency_code')->pluck('currency_code');
        $totalBalance = Wallet::groupBy('currency_code')
            ->selectRaw('currency_code, SUM(balance) as total')
            ->orderBy('currency_code')
            ->get();

        return view('admin.wallet.index', compact('pageTitle', 'wallets', 'currencies', 'totalBalance'));
    }

    public function credit(Request $request, Wallet $wallet)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'remark' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $wallet) {
            $wallet->increment('balance', $request->amount);

            Transaction::create([
                'user_id'      => $wallet->user_id,
                'wallet_id'    => $wallet->id,
                'amount'       => $request->amount,
                'post_balance' => $wallet->fresh()->balance,
                'charge'       => 0,
                'trx_type'     => '+',
                'remark'       => 'admin_wallet_credit',
                'details'      => $request->remark ?: 'Admin manual credit (' . $wallet->currency_code . ')',
                'trx'          => getTrx(),
            ]);
        });

        $notify[] = ['success', 'Wallet credited successfully'];
        return back()->withNotify($notify);
    }

    public function debit(Request $request, Wallet $wallet)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'remark' => 'nullable|string|max:255',
        ]);

        if ($request->amount > $wallet->balance) {
            $notify[] = ['error', 'Debit amount exceeds wallet balance'];
            return back()->withNotify($notify);
        }

        DB::transaction(function () use ($request, $wallet) {
            $wallet->decrement('balance', $request->amount);

            Transaction::create([
                'user_id'      => $wallet->user_id,
                'wallet_id'    => $wallet->id,
                'amount'       => $request->amount,
                'post_balance' => $wallet->fresh()->balance,
                'charge'       => 0,
                'trx_type'     => '-',
                'remark'       => 'admin_wallet_debit',
                'details'      => $request->remark ?: 'Admin manual debit (' . $wallet->currency_code . ')',
                'trx'          => getTrx(),
            ]);
        });

        $notify[] = ['success', 'Wallet debited successfully'];
        return back()->withNotify($notify);
    }

    public function exchanges(Request $request)
    {
        $pageTitle = 'Wallet Exchanges';

        $exchanges = WalletExchange::with([
                'user:id,firstname,lastname,username,email',
                'fromWallet:id,currency_code,currency_symbol',
                'toWallet:id,currency_code,currency_symbol',
            ])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($q2) use ($search) {
                    $q2->where('trx', 'like', "%$search%")
                       ->orWhere('from_currency', 'like', "%$search%")
                       ->orWhere('to_currency', 'like', "%$search%")
                       ->orWhereHas('user', fn($u) => $u->where('username', 'like', "%$search%")
                           ->orWhere('email', 'like', "%$search%"));
                });
            })
            ->when($request->filled('currency'), function ($q) use ($request) {
                $q->where('from_currency', $request->currency)
                  ->orWhere('to_currency', $request->currency);
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $currencies = WalletExchange::selectRaw('from_currency as currency')->union(
            WalletExchange::selectRaw('to_currency as currency')
        )->distinct()->orderBy('currency')->pluck('currency');

        return view('admin.wallet.exchanges', compact('pageTitle', 'exchanges', 'currencies'));
    }

    public function transfers(Request $request)
    {
        $pageTitle = 'Wallet Transfers';

        $transfers = WalletTransfer::with([
                'sender:id,firstname,lastname,username,email',
                'receiver:id,firstname,lastname,username,email',
            ])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where('trx', 'like', "%$search%")
                  ->orWhere('currency_code', 'like', "%$search%")
                  ->orWhereHas('sender', fn($u) => $u->where('username', 'like', "%$search%")->orWhere('email', 'like', "%$search%"))
                  ->orWhereHas('receiver', fn($u) => $u->where('username', 'like', "%$search%")->orWhere('email', 'like', "%$search%"));
            })
            ->when($request->filled('currency'), fn($q) => $q->where('currency_code', $request->currency))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $currencies = WalletTransfer::distinct('currency_code')->orderBy('currency_code')->pluck('currency_code');

        $totalTransferred = WalletTransfer::groupBy('currency_code')
            ->selectRaw('currency_code, SUM(amount) as total, COUNT(*) as count')
            ->orderBy('currency_code')
            ->get();

        return view('admin.wallet.transfers', compact('pageTitle', 'transfers', 'currencies', 'totalTransferred'));
    }

    public function setting()
    {
        $pageTitle = 'Wallet Settings';
        $general   = gs();

        $allCurrencies = Country::active()
            ->select('currency', 'name')
            ->whereNotNull('currency')
            ->where('currency', '!=', '')
            ->orderBy('currency')
            ->get()
            ->unique('currency')
            ->values();

        return view('admin.wallet.setting', compact('pageTitle', 'general', 'allCurrencies'));
    }

    public function settingUpdate(Request $request)
    {
        $request->validate([
            'wallet_transfer_fixed_charge'   => 'required|numeric|min:0',
            'wallet_transfer_percent_charge' => 'required|numeric|min:0|max:100',
            'wallet_exchange_fixed_charge'   => 'required|numeric|min:0',
            'wallet_exchange_percent_charge' => 'required|numeric|min:0|max:100',
        ]);

        $general = gs();

        $wasModuleOn  = (bool) $general->wallet_module;
        $wasMulti     = (bool) $general->wallet_currency_mode;
        $nowModuleOn  = (bool) $request->wallet_module;
        $nowMulti     = (bool) $request->wallet_currency_mode;

        $general->wallet_module          = $nowModuleOn  ? Status::ENABLE : Status::DISABLE;
        $general->wallet_currency_mode   = $nowMulti     ? Status::ENABLE : Status::DISABLE;
        // Transfer can only be enabled when the wallet module itself is on
        $general->wallet_transfer        = ($nowModuleOn && $request->wallet_transfer) ? Status::ENABLE : Status::DISABLE;
        $general->wallet_transfer_methods         = $request->transfer_methods ?? [];
        $general->wallet_transfer_fixed_charge    = $request->wallet_transfer_fixed_charge;
        $general->wallet_transfer_percent_charge  = $request->wallet_transfer_percent_charge;
        $general->wallet_exchange_fixed_charge    = $request->wallet_exchange_fixed_charge;
        $general->wallet_exchange_percent_charge  = $request->wallet_exchange_percent_charge;
        $general->wallet_allowed_currencies = $request->has('wallet_allowed_currencies')
            ? array_values(array_filter((array) $request->wallet_allowed_currencies))
            : null;
        $general->save();

        // Module turned off: refund all wallet balances to user.balance
        if ($wasModuleOn && !$nowModuleOn) {
            $this->refundAllWalletsToUserBalance();
        }
        // Single-currency mode: consolidate any non-default-currency wallets that still have a balance.
        // Runs every time the setting is saved in single mode so a failed previous attempt can be retried.
        elseif (!$nowMulti && $nowModuleOn) {
            $this->consolidateToDefaultCurrencyWallet();
        }

        $notify[] = ['success', 'Wallet settings updated successfully'];
        return back()->withNotify($notify);
    }

    // Collapse every non-default-currency wallet into each user's system-default-currency wallet.
    // Idempotent: safe to call multiple times — only moves wallets that still have a balance.
    private function consolidateToDefaultCurrencyWallet(): void
    {
        $defaultCurrency = gs('cur_text');
        $defaultSymbol   = gs('cur_sym') ?: $defaultCurrency;
        $rates           = Country::active()->whereNotNull('rate')->where('rate', '>', 0)->pluck('rate', 'currency');

        $defaultRate = (float) ($rates[$defaultCurrency] ?? 1);
        if ($defaultRate <= 0) $defaultRate = 1;

        $country = Country::active()->where('currency', $defaultCurrency)->first();

        // Find every user who has any wallet (regardless of balance) so we can ensure
        // a default-currency wallet exists for them, then move non-zero balances.
        User::whereHas('wallets')->chunk(100, function ($users) use ($defaultCurrency, $defaultSymbol, $rates, $defaultRate, $country) {
            foreach ($users as $user) {
                DB::transaction(function () use ($user, $defaultCurrency, $defaultSymbol, $rates, $defaultRate, $country) {

                    // Ensure the default-currency wallet exists; create it if not
                    $target = Wallet::forUser($user->id)
                        ->where('currency_code', $defaultCurrency)
                        ->lockForUpdate()
                        ->first();

                    if (!$target) {
                        $target = Wallet::create([
                            'user_id'            => $user->id,
                            'currency_code'      => $defaultCurrency,
                            'currency_name'      => ($country ? $country->name . ' / ' : '') . $defaultCurrency,
                            'currency_symbol'    => $defaultSymbol,
                            'balance'            => 0,
                            'is_default'         => 1,
                            'remittance_enabled' => $country
                                ? ($country->is_sending == Status::ENABLE || $country->is_receiving == Status::ENABLE)
                                : false,
                            'status'             => Wallet::STATUS_ACTIVE,
                        ]);
                    }

                    // Mark it as default; demote all others
                    Wallet::forUser($user->id)->where('id', '!=', $target->id)->update(['is_default' => 0]);
                    if (!$target->is_default) {
                        $target->update(['is_default' => 1]);
                    }

                    // Move balances from every other wallet that still has funds
                    $sources = Wallet::forUser($user->id)
                        ->where('id', '!=', $target->id)
                        ->where('balance', '>', 0)
                        ->lockForUpdate()
                        ->get();

                    $totalConverted = 0;

                    foreach ($sources as $source) {
                        $sourceRate = (float) ($rates[$source->currency_code] ?? 1);
                        if ($sourceRate <= 0) $sourceRate = 1;

                        // Convert using the same rate formula as the exchange feature
                        $converted       = round($source->balance * ($defaultRate / $sourceRate), 8);
                        $totalConverted += $converted;

                        Transaction::create([
                            'user_id'      => $user->id,
                            'wallet_id'    => $source->id,
                            'amount'       => $source->balance,
                            'post_balance' => 0,
                            'charge'       => 0,
                            'trx_type'     => '-',
                            'remark'       => 'wallet_consolidation',
                            'details'      => 'Balance consolidated to ' . $defaultCurrency . ' wallet (multi→single mode)',
                            'trx'          => getTrx(),
                        ]);

                        $source->update(['balance' => 0]);
                    }

                    if ($totalConverted > 0) {
                        $target->increment('balance', $totalConverted);

                        Transaction::create([
                            'user_id'      => $user->id,
                            'wallet_id'    => $target->id,
                            'amount'       => $totalConverted,
                            'post_balance' => $target->fresh()->balance,
                            'charge'       => 0,
                            'trx_type'     => '+',
                            'remark'       => 'wallet_consolidation',
                            'details'      => 'Received consolidated balances from multi-currency wallets',
                            'trx'          => getTrx(),
                        ]);
                    }
                });
            }
        });
    }

    // Zero out all wallets and credit each user's main balance (in system default currency).
    // Called when admin disables the wallet module entirely.
    private function refundAllWalletsToUserBalance(): void
    {
        $defaultCurrency = gs('cur_text');
        $rates           = Country::active()->whereNotNull('rate')->pluck('rate', 'currency');

        $defaultRate = (float) ($rates[$defaultCurrency] ?? 1);
        if ($defaultRate <= 0) $defaultRate = 1;

        User::whereHas('wallets', fn($q) => $q->where('balance', '>', 0))->chunk(100, function ($users) use ($defaultCurrency, $rates, $defaultRate) {
            foreach ($users as $user) {
                DB::transaction(function () use ($user, $rates, $defaultRate, $defaultCurrency) {
                    $wallets = Wallet::forUser($user->id)->where('balance', '>', 0)->lockForUpdate()->get();

                    $refundTotal = 0;

                    foreach ($wallets as $wallet) {
                        $sourceRate = (float) ($rates[$wallet->currency_code] ?? 1);
                        if ($sourceRate <= 0) $sourceRate = 1;

                        $converted    = round($wallet->balance * ($defaultRate / $sourceRate), 8);
                        $refundTotal += $converted;

                        Transaction::create([
                            'user_id'      => $user->id,
                            'wallet_id'    => $wallet->id,
                            'amount'       => $wallet->balance,
                            'post_balance' => 0,
                            'charge'       => 0,
                            'trx_type'     => '-',
                            'remark'       => 'wallet_module_disabled',
                            'details'      => 'Wallet balance refunded — wallet module disabled by admin',
                            'trx'          => getTrx(),
                        ]);

                        $wallet->update(['balance' => 0]);
                    }

                    if ($refundTotal > 0) {
                        $user->increment('balance', $refundTotal);

                        Transaction::create([
                            'user_id'      => $user->id,
                            'wallet_id'    => null,
                            'amount'       => $refundTotal,
                            'post_balance' => $user->fresh()->balance,
                            'charge'       => 0,
                            'trx_type'     => '+',
                            'remark'       => 'wallet_module_disabled',
                            'details'      => 'Wallet funds refunded to account balance (' . $defaultCurrency . ')',
                            'trx'          => getTrx(),
                        ]);
                    }
                });
            }
        });
    }
}
