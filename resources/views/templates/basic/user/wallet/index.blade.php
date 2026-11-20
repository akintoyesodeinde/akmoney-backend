@extends($activeTemplate . 'layouts.master')
@section('activePage', 'wallets')
@section('content')

<div class="max-w-5xl mx-auto py-6 px-2">

    {{-- ── Page header ── --}}
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h2 class="text-2xl font-extrabold text-text-primary">My {{ $isMultiCurrency ? 'Currencies' : 'Currency' }}</h2>
            <p class="text-sm text-text-secondary mt-1">
                @if($isMultiCurrency)
                    Manage balances, top up, and exchange between currencies.
                @else
                    Your {{ gs('cur_text') }} wallet — top up, transfer, and track transactions.
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if(gs('wallet_transfer'))
            <a href="{{ route('user.wallets.transfer.history') }}"
                class="flex items-center gap-2 px-4 py-2.5 border border-border-gray text-text-secondary text-sm font-semibold rounded-xl hover:bg-gray-100 transition">
                <i class="ri-swap-line text-base"></i> Transfers
            </a>
            @endif
            @if($isMultiCurrency)
            <button onclick="openModal('overlayCreate')"
                class="flex items-center gap-2 px-5 py-2.5 bg-brand-teal text-white text-sm font-semibold rounded-xl shadow hover:bg-opacity-90 transition">
                <i class="ri-add-line text-base"></i> Add currencies
            </button>
            @endif
        </div>
    </div>

    {{-- ── Flash messages ── --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-teal-50 border border-teal-200 text-teal-800 rounded-xl text-sm font-medium flex items-center gap-2">
            <i class="ri-checkbox-circle-line text-teal-600"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-medium flex items-center gap-2">
            <i class="ri-error-warning-line"></i> {{ $errors->first() }}
        </div>
    @endif

    {{-- ── Wallet cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        @forelse($wallets as $wallet)
        <div class="bg-card-white border {{ $wallet->is_default ? 'border-brand-teal ring-1 ring-brand-teal/30' : 'border-border-gray' }}
            rounded-2xl p-5 shadow-sm hover:-translate-y-0.5 transition-transform">

            <div class="flex items-start justify-between mb-4">
                <div class="w-11 h-11 rounded-full bg-teal-50 border border-teal-200 flex items-center justify-center font-bold text-sm text-brand-teal">
                    {{ $wallet->currency_code }}
                </div>
                <div class="flex flex-col items-end gap-1">
                    @if($wallet->is_default)
                        <span class="text-[10px] font-bold bg-teal-100 text-teal-800 px-2 py-0.5 rounded-full">Default</span>
                    @endif
                    @if($wallet->remittance_enabled)
                        <span class="text-[10px] font-bold bg-red-100 text-red-600 px-2 py-0.5 rounded-full">Remittance</span>
                    @endif
                </div>
            </div>

            <p class="text-xs text-text-secondary font-medium mb-1">{{ $wallet->currency_name }}</p>
            <p class="text-2xl font-extrabold text-text-primary tracking-tight">
                {{ number_format($wallet->balance, 2) }}
                <span class="text-sm font-normal text-text-secondary">{{ $wallet->currency_code }}</span>
            </p>

            <div class="flex gap-2 mt-4 pt-4 border-t border-border-gray">
                <button onclick="openDeposit({{ $wallet->id }}, '{{ $wallet->currency_code }}')"
                    class="flex-1 text-xs font-semibold py-2 rounded-lg bg-teal-50 text-teal-800 hover:bg-teal-100 transition">
                    <i class="ri-add-circle-line mr-1"></i>Deposit
                </button>
                @if($isMultiCurrency)
                <button onclick="openExchange({{ $wallet->id }})"
                    class="flex-1 text-xs font-semibold py-2 rounded-lg border border-border-gray text-text-primary hover:bg-gray-100 transition">
                    <i class="ri-arrow-left-right-line mr-1"></i>Exchange
                </button>
                @endif
                @if(gs('wallet_transfer'))
                <button onclick="openTransfer({{ $wallet->id }}, '{{ $wallet->currency_code }}', {{ $wallet->balance }})"
                    title="Transfer to another user"
                    class="text-xs font-semibold px-2.5 py-2 rounded-lg border border-border-gray text-text-secondary hover:bg-gray-100 transition flex items-center">
                    <i class="ri-send-plane-line"></i>
                </button>
                @endif
                <a href="{{ route('user.wallets.transactions', $wallet->id) }}" title="Transaction history"
                    class="text-xs font-semibold px-2.5 py-2 rounded-lg border border-border-gray text-text-secondary hover:bg-gray-100 transition flex items-center">
                    <i class="ri-file-list-3-line"></i>
                </a>
                @if(!$wallet->is_default && $isMultiCurrency)
                <form method="POST" action="{{ route('user.wallets.set.default', $wallet->id) }}" class="inline">
                    @csrf
                    <button type="submit" title="Set as default"
                        class="text-xs font-semibold px-2.5 py-2 rounded-lg border border-border-gray text-text-secondary hover:bg-gray-100 transition">
                        <i class="ri-star-line"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-14 bg-card-white rounded-2xl border border-border-gray">
            <i class="ri-wallet-3-line text-4xl text-text-secondary mb-3 block"></i>
            <p class="text-text-secondary text-sm mb-4">No currencies yet. Add your first one.</p>
            <button onclick="openModal('overlayCreate')"
                class="px-5 py-2.5 bg-brand-teal text-white text-sm font-semibold rounded-xl hover:bg-opacity-90 transition">
                Add currencies
            </button>
        </div>
        @endforelse

        {{-- Add wallet card — multi mode only --}}
        @if($isMultiCurrency && $wallets->count())
        <button onclick="openModal('overlayCreate')"
            class="border-2 border-dashed border-border-gray rounded-2xl p-5 flex flex-col items-center justify-center gap-2
            min-h-[160px] text-text-secondary hover:border-brand-teal hover:text-brand-teal hover:bg-teal-50 transition">
            <i class="ri-add-circle-line text-3xl"></i>
            <span class="text-xs font-semibold">Add new currency</span>
        </button>
        @endif
    </div>

    {{-- ── Bottom section: Exchanges (multi) or Transaction history (single) ── --}}
    @if($isMultiCurrency)
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-base font-bold text-text-primary">Recent Exchanges</h3>
    </div>
    <div class="bg-card-white rounded-2xl border border-border-gray overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-border-gray bg-gray-50/60">
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">Date</th>
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">From</th>
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">To</th>
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exchanges as $ex)
                    <tr class="border-t border-border-gray hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3.5 text-sm text-text-secondary">{{ showDateTime($ex->created_at, 'd M Y') }}</td>
                        <td class="px-4 py-3.5 text-sm">
                            <span class="font-semibold text-red-600">-{{ number_format($ex->from_amount, 2) }}</span>
                            <span class="text-text-secondary ml-1 text-xs">{{ $ex->from_currency }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-sm">
                            <span class="font-semibold text-teal-700">+{{ number_format($ex->to_amount, 2) }}</span>
                            <span class="text-text-secondary ml-1 text-xs">{{ $ex->to_currency }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-xs text-text-secondary">
                            1 {{ $ex->from_currency }} = {{ number_format($ex->exchange_rate, 4) }} {{ $ex->to_currency }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-sm text-text-secondary">No exchanges yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @else
    {{-- Single-currency: Wallet Transaction History ── --}}
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-base font-bold text-text-primary">Wallet Transaction History</h3>
        @if($defaultWallet)
        <a href="{{ route('user.wallets.transactions', $defaultWallet->id) }}"
            class="text-xs font-semibold text-brand-teal hover:underline">View all</a>
        @endif
    </div>
    <div class="bg-card-white rounded-2xl border border-border-gray overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-border-gray bg-gray-50/60">
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">Date</th>
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">Type</th>
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">Description</th>
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTxItems as $item)
                    <tr class="border-t border-border-gray hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3.5 text-sm text-text-secondary whitespace-nowrap">
                            {{ showDateTime($item['date'], 'd M Y') }}
                        </td>
                        <td class="px-4 py-3.5">
                            @if($item['type'] === 'topup')
                                <span class="text-xs font-bold px-2 py-0.5 bg-teal-100 text-teal-700 rounded-full">Top-up</span>
                            @else
                                <span class="text-xs font-bold px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full">Txn</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-sm text-text-secondary">{{ $item['description'] }}</td>
                        <td class="px-4 py-3.5 text-sm font-bold text-right
                            {{ $item['sign'] === '+' ? 'text-teal-700' : 'text-red-600' }}">
                            {{ $item['sign'] }}{{ number_format($item['amount'], 2) }}
                            <span class="text-xs font-normal text-text-secondary ml-0.5">{{ gs('cur_text') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-sm text-text-secondary">No transactions yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

{{-- ══ MODAL: Add wallet ══ --}}
<div id="overlayCreate" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-card-white rounded-2xl w-full max-w-md shadow-2xl">
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-border-gray">
            <h3 class="text-lg font-extrabold text-text-primary">Add a currency</h3>
            <button onclick="closeModal('overlayCreate')" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-text-secondary">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('user.wallets.store') }}">
            @csrf
            <div class="px-6 py-4">
                <p class="text-sm text-text-secondary mb-4">
                    Choose a currency. <span class="text-red-600 font-semibold">Remittance</span> currencies can be used directly for sending money.
                </p>
                <div class="grid grid-cols-3 gap-2 max-h-72 overflow-y-auto mb-4 pr-1">
                    @forelse($currencies as $c)
                    <label class="cur-opt {{ $c['has_wallet'] ? 'used' : '' }} cursor-pointer">
                        <input type="radio" name="currency_code" value="{{ $c['currency_code'] }}"
                            class="sr-only" {{ $c['has_wallet'] ? 'disabled' : '' }}
                            onchange="document.getElementById('btnCreateWallet').disabled = false;">
                        <div class="text-sm font-bold text-text-primary">{{ $c['currency_code'] }}</div>
                        <div class="text-[11px] text-text-secondary truncate">{{ $c['country_name'] }}</div>
                        @if($c['remittance_enabled'])
                            <div class="text-[9px] font-bold text-red-500 mt-0.5">Remittance</div>
                        @endif
                        @if($c['has_wallet'])
                            <div class="text-[9px] text-text-secondary mt-0.5">Added</div>
                        @endif
                    </label>
                    @empty
                    <p class="col-span-3 text-center text-sm text-text-secondary py-4">No currencies available.</p>
                    @endforelse
                </div>
            </div>
            <div class="px-6 pb-6">
                <button id="btnCreateWallet" type="submit" disabled
                    class="w-full py-3 bg-brand-teal text-white font-bold rounded-xl disabled:opacity-40 disabled:cursor-not-allowed hover:bg-opacity-90 transition text-sm">
                    Open wallet
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL: Deposit ══ --}}
<div id="overlayDeposit" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-card-white rounded-2xl w-full max-w-md shadow-2xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-border-gray flex-shrink-0">
            <div>
                <h3 class="text-lg font-extrabold text-text-primary">Deposit funds</h3>
                <p class="text-xs text-text-secondary mt-0.5">Top up your <strong id="depositWalletLabel">wallet</strong></p>
            </div>
            <button onclick="closeModal('overlayDeposit')" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-text-secondary">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>
        <form id="depositForm" method="POST" action="" class="flex flex-col flex-1 overflow-hidden">
            @csrf
            <input type="hidden" name="gateway" id="depositGateway">
            <input type="hidden" name="currency" id="depositCurrency">
            <div class="px-6 py-5 overflow-y-auto flex-1 space-y-5">
                {{-- Amount --}}
                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Amount</label>
                    <input name="amount" id="depositAmount" type="number" min="0.01" step="any" placeholder="0.00" required
                        class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent text-text-primary">
                    <p id="depositLimitHint" class="text-[11px] text-text-secondary mt-1 hidden"></p>
                </div>
                {{-- Payment method --}}
                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-2">Payment Method</label>
                    @if($depositMethods->isEmpty())
                        <p class="text-sm text-text-secondary">No payment methods available.</p>
                    @else
                    <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                        @foreach($depositMethods as $method)
                        <label class="deposit-method-opt flex items-center gap-3 p-3 border-2 border-border-gray rounded-xl cursor-pointer hover:border-brand-teal transition"
                            data-code="{{ $method->method_code }}"
                            data-currency="{{ $method->currency }}"
                            data-min="{{ $method->min_amount }}"
                            data-max="{{ $method->max_amount }}"
                            data-fixed="{{ $method->fixed_charge }}"
                            data-percent="{{ $method->percent_charge }}">
                            <input type="radio" name="_method_pick" class="sr-only deposit-method-radio">
                            <div class="w-9 h-9 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                                <img src="{{ getImage(getFilePath('gateway') . '/' . $method->method->image, getFileSize('gateway')) }}"
                                    alt="{{ $method->method->name }}" class="w-full h-full object-contain" onerror="this.parentElement.innerHTML='<i class=\'ri-bank-card-line text-text-secondary text-lg flex items-center justify-center h-full\'></i>'">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-text-primary truncate">{{ $method->method->name }}</p>
                                <p class="text-[11px] text-text-secondary">{{ $method->currency }} &nbsp;·&nbsp; {{ $method->min_amount }}–{{ $method->max_amount }}</p>
                            </div>
                            <div class="deposit-check w-5 h-5 rounded-full border-2 border-border-gray flex items-center justify-center flex-shrink-0">
                                <div class="w-2.5 h-2.5 rounded-full bg-brand-teal hidden"></div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @endif
                </div>
                {{-- Charge preview --}}
                <div id="depositPreview" class="hidden px-4 py-3 bg-teal-50 border border-teal-200 rounded-xl text-sm text-teal-800 space-y-1"></div>
            </div>
            <div class="px-6 pb-6 flex-shrink-0">
                <button type="submit" id="btnDeposit" disabled
                    class="w-full py-3 bg-brand-teal text-white font-bold rounded-xl disabled:opacity-40 disabled:cursor-not-allowed hover:bg-opacity-90 transition text-sm">
                    Proceed to Payment
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL: Exchange (multi-currency only) ══ --}}
@if($isMultiCurrency)
<div id="overlayExchange" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-card-white rounded-2xl w-full max-w-md shadow-2xl">
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-border-gray">
            <h3 class="text-lg font-extrabold text-text-primary">Exchange currencies</h3>
            <button onclick="closeModal('overlayExchange')" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-text-secondary">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('user.wallets.exchange') }}" id="exchangeForm">
            @csrf
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">From wallet</label>
                    <select name="from_wallet_id" id="exchangeFrom" onchange="updateExchangePreview()"
                        class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-card-white text-text-primary">
                        @foreach($wallets as $w)
                        <option value="{{ $w->id }}" data-balance="{{ $w->balance }}" data-code="{{ $w->currency_code }}">
                            {{ $w->currency_code }} — {{ number_format($w->balance, 2) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-center">
                    <button type="button" onclick="swapWallets()"
                        class="w-9 h-9 rounded-full border border-border-gray flex items-center justify-center text-text-secondary hover:bg-gray-100 transition">
                        <i class="ri-arrow-up-down-line"></i>
                    </button>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">To wallet</label>
                    <select name="to_wallet_id" id="exchangeTo" onchange="updateExchangePreview()"
                        class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-card-white text-text-primary">
                        @foreach($wallets as $i => $w)
                        <option value="{{ $w->id }}" data-code="{{ $w->currency_code }}" {{ $i === 1 ? 'selected' : '' }}>
                            {{ $w->currency_code }} — {{ number_format($w->balance, 2) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Amount</label>
                    <input name="amount" id="exchangeAmount" type="number" min="0.01" step="any" placeholder="0.00"
                        oninput="updateExchangePreview()"
                        class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent text-text-primary">
                </div>

                <div id="exchangePreview" class="hidden px-4 py-3 bg-teal-50 border border-teal-200 rounded-xl text-sm text-teal-800"></div>
            </div>
            <div class="px-6 pb-6">
                <button type="submit"
                    class="w-full py-3 bg-brand-teal text-white font-bold rounded-xl hover:bg-opacity-90 transition text-sm">
                    Exchange now
                </button>
            </div>
        </form>
    </div>
</div>
@endif {{-- end @if($isMultiCurrency) --}}

@if(gs('wallet_transfer'))
{{-- ══ MODAL: Transfer ══ --}}
<div id="overlayTransfer" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-card-white rounded-2xl w-full max-w-md shadow-2xl">
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-border-gray">
            <div>
                <h3 class="text-lg font-extrabold text-text-primary">Transfer funds</h3>
                <p class="text-xs text-text-secondary mt-0.5">Send from your <strong id="transferWalletLabel">wallet</strong></p>
            </div>
            <button onclick="closeModal('overlayTransfer')" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-text-secondary">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('user.wallets.transfer') }}" id="transferForm">
            @csrf
            <input type="hidden" name="from_wallet_id" id="transferWalletId">
            <input type="hidden" name="receiver_id"    id="transferReceiverId">

            <div class="px-6 py-5 space-y-4">

                {{-- Lookup method tabs --}}
                @php $methods = gs()->wallet_transfer_methods ?? ['username','email','mobile']; @endphp
                @if(count($methods) > 1)
                <div class="flex gap-1 p-1 bg-gray-100 rounded-xl">
                    @foreach($methods as $m)
                    <button type="button" onclick="setLookupMethod('{{ $m }}')"
                        id="tab_{{ $m }}"
                        class="lookup-tab flex-1 py-1.5 text-xs font-semibold rounded-lg transition {{ $loop->first ? 'bg-white shadow text-text-primary' : 'text-text-secondary' }}">
                        {{ ucfirst($m === 'mobile' ? 'Phone' : $m) }}
                    </button>
                    @endforeach
                </div>
                @endif

                {{-- Recipient lookup --}}
                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5" id="lookupLabel">
                        @lang('Username')
                    </label>
                    <div class="flex gap-2">
                        <input type="text" id="transferIdentifier" placeholder="Enter username"
                            class="flex-1 px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent text-text-primary">
                        <button type="button" onclick="lookupRecipient()"
                            class="px-4 py-2 bg-brand-teal text-white text-xs font-bold rounded-xl hover:bg-opacity-90 transition">
                            Find
                        </button>
                    </div>
                    <div id="recipientResult" class="mt-2 hidden px-4 py-3 rounded-xl border text-sm"></div>
                </div>

                {{-- Amount --}}
                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Amount</label>
                    <input type="number" name="amount" id="transferAmount" min="0.01" step="any" placeholder="0.00"
                        oninput="updateTransferPreview()"
                        class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent text-text-primary">
                    <p class="text-[11px] text-text-secondary mt-1">
                        Available: <span id="transferAvailBalance">0.00</span> <span id="transferCurrencyLabel"></span>
                    </p>
                </div>

                {{-- Charge preview --}}
                @php
                    $tfFixed   = gs('wallet_transfer_fixed_charge') ?? 0;
                    $tfPercent = gs('wallet_transfer_percent_charge') ?? 0;
                @endphp
                <div id="transferPreview" class="hidden px-4 py-3 bg-teal-50 border border-teal-200 rounded-xl text-sm text-teal-800 space-y-1"></div>
            </div>

            <div class="px-6 pb-6">
                <button type="submit" id="btnTransfer" disabled
                    class="w-full py-3 bg-brand-teal text-white font-bold rounded-xl disabled:opacity-40 disabled:cursor-not-allowed hover:bg-opacity-90 transition text-sm">
                    Send now
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@push('style')
<style>
    .cur-opt {
        display: block;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 6px;
        text-align: center;
        transition: border-color .15s, background .15s;
    }
    .cur-opt:has(input:checked) { border-color: #0F4650; background: #eef8f2; }
    .cur-opt.used { opacity: .38; pointer-events: none; }
    .cur-opt:not(.used):hover { border-color: #0F4650; background: #eef8f2; }
</style>
@endpush

@push('script')
<script>
    function openModal(id)  { const el = document.getElementById(id); el.classList.remove('hidden'); el.classList.add('flex'); }
    function closeModal(id) { const el = document.getElementById(id); el.classList.add('hidden');    el.classList.remove('flex'); }

    document.querySelectorAll('[id^="overlay"]').forEach(el => {
        el.addEventListener('click', e => { if (e.target === el) closeModal(el.id); });
    });

    function openDeposit(walletId, code) {
        document.getElementById('depositWalletLabel').textContent = code + ' wallet';
        // Set form action to wallet-specific deposit route
        const baseUrl = "{{ route('user.deposit.wallet.insert', ['walletId' => '__ID__']) }}".replace('__ID__', walletId);
        document.getElementById('depositForm').action = baseUrl;
        // Reset selections
        document.querySelectorAll('.deposit-method-opt').forEach(el => {
            el.classList.remove('border-brand-teal');
            el.querySelector('.deposit-check div').classList.add('hidden');
        });
        document.getElementById('depositGateway').value = '';
        document.getElementById('depositCurrency').value = '';
        document.getElementById('depositAmount').value = '';
        document.getElementById('depositPreview').classList.add('hidden');
        document.getElementById('btnDeposit').disabled = true;
        openModal('overlayDeposit');
    }

    // Deposit method selection
    document.querySelectorAll('.deposit-method-opt').forEach(opt => {
        opt.addEventListener('click', function () {
            document.querySelectorAll('.deposit-method-opt').forEach(el => {
                el.classList.remove('border-brand-teal');
                el.querySelector('.deposit-check div').classList.add('hidden');
            });
            this.classList.add('border-brand-teal');
            this.querySelector('.deposit-check div').classList.remove('hidden');
            document.getElementById('depositGateway').value  = this.dataset.code;
            document.getElementById('depositCurrency').value = this.dataset.currency;
            const hint = document.getElementById('depositLimitHint');
            hint.textContent = `Limit: ${this.dataset.min} – ${this.dataset.max} ${this.dataset.currency}`;
            hint.classList.remove('hidden');
            updateDepositPreview();
            checkDepositReady();
        });
    });

    document.getElementById('depositAmount')?.addEventListener('input', function () {
        updateDepositPreview();
        checkDepositReady();
    });

    function updateDepositPreview() {
        const opt    = document.querySelector('.deposit-method-opt.border-brand-teal');
        const amount = parseFloat(document.getElementById('depositAmount').value) || 0;
        const prev   = document.getElementById('depositPreview');
        if (!opt || amount <= 0) { prev.classList.add('hidden'); return; }
        const fixed   = parseFloat(opt.dataset.fixed)   || 0;
        const pct     = parseFloat(opt.dataset.percent) || 0;
        const charge  = fixed + (amount * pct / 100);
        const payable = amount + charge;
        prev.classList.remove('hidden');
        prev.innerHTML = `
            <div class="flex justify-between"><span>Amount</span><strong>${amount.toFixed(2)} ${opt.dataset.currency}</strong></div>
            <div class="flex justify-between"><span>Charge</span><strong>${charge.toFixed(2)} ${opt.dataset.currency}</strong></div>
            <div class="flex justify-between text-base font-bold border-t border-teal-200 pt-1 mt-1"><span>Payable</span><strong>${payable.toFixed(2)} ${opt.dataset.currency}</strong></div>`;
    }

    function checkDepositReady() {
        const gateway = document.getElementById('depositGateway').value;
        const amount  = parseFloat(document.getElementById('depositAmount').value) || 0;
        const opt     = document.querySelector('.deposit-method-opt.border-brand-teal');
        const btn     = document.getElementById('btnDeposit');
        if (gateway && amount > 0 && opt && amount >= parseFloat(opt.dataset.min) && amount <= parseFloat(opt.dataset.max)) {
            btn.disabled = false;
        } else {
            btn.disabled = true;
        }
    }

    @if($isMultiCurrency)
    function openExchange(walletId) {
        const from = document.getElementById('exchangeFrom');
        for (let i = 0; i < from.options.length; i++) {
            if (parseInt(from.options[i].value) === walletId) { from.selectedIndex = i; break; }
        }
        const to = document.getElementById('exchangeTo');
        for (let i = 0; i < to.options.length; i++) {
            if (parseInt(to.options[i].value) !== walletId) { to.selectedIndex = i; break; }
        }
        document.getElementById('exchangeAmount').value = '';
        document.getElementById('exchangePreview').classList.add('hidden');
        openModal('overlayExchange');
    }

    function swapWallets() {
        const from = document.getElementById('exchangeFrom');
        const to   = document.getElementById('exchangeTo');
        const tmp  = from.value; from.value = to.value; to.value = tmp;
        updateExchangePreview();
    }

    function updateExchangePreview() {
        const from   = document.getElementById('exchangeFrom');
        const to     = document.getElementById('exchangeTo');
        const amount = parseFloat(document.getElementById('exchangeAmount').value) || 0;
        const prev   = document.getElementById('exchangePreview');

        const fromCode = from.options[from.selectedIndex]?.dataset.code ?? '';
        const toCode   = to.options[to.selectedIndex]?.dataset.code ?? '';

        if (!from.value || !to.value || from.value === to.value || amount <= 0) {
            prev.classList.add('hidden'); return;
        }
        prev.classList.remove('hidden');
        prev.innerHTML = `Exchanging <strong>${amount} ${fromCode}</strong> &rarr; <strong>${toCode}</strong><br>
            <span class="text-xs opacity-75">Exchange rate applied at time of submission.</span>`;
    }

    // Re-open exchange modal if validation failed for it
    @if($errors->has('amount') && old('from_wallet_id'))
        openModal('overlayExchange');
    @endif

    // Re-open create modal if currency validation failed
    @if($errors->has('currency_code'))
        openModal('overlayCreate');
    @endif
    @endif {{-- end isMultiCurrency JS --}}

    @if(gs('wallet_transfer'))
    // ── Transfer modal ──
    let _lookupMethod = '{{ ($methods[0] ?? 'username') }}';
    const TF_FIXED   = {{ gs('wallet_transfer_fixed_charge') ?? 0 }};
    const TF_PERCENT = {{ gs('wallet_transfer_percent_charge') ?? 0 }};
    const LOOKUP_URL = "{{ route('user.wallets.transfer.lookup') }}";

    function openTransfer(walletId, code, balance) {
        document.getElementById('transferWalletId').value     = walletId;
        document.getElementById('transferReceiverId').value   = '';
        document.getElementById('transferIdentifier').value   = '';
        document.getElementById('transferAmount').value       = '';
        document.getElementById('transferWalletLabel').textContent = code + ' wallet';
        document.getElementById('transferAvailBalance').textContent = parseFloat(balance).toFixed(2);
        document.getElementById('transferCurrencyLabel').textContent = code;
        document.getElementById('recipientResult').classList.add('hidden');
        document.getElementById('transferPreview').classList.add('hidden');
        document.getElementById('btnTransfer').disabled = true;
        openModal('overlayTransfer');
    }

    function setLookupMethod(method) {
        _lookupMethod = method;
        document.querySelectorAll('.lookup-tab').forEach(t => {
            t.classList.remove('bg-white', 'shadow', 'text-text-primary');
            t.classList.add('text-text-secondary');
        });
        const tab = document.getElementById('tab_' + method);
        if (tab) { tab.classList.add('bg-white', 'shadow', 'text-text-primary'); tab.classList.remove('text-text-secondary'); }
        const labels = { username: 'Username', email: 'Email address', mobile: 'Phone number' };
        const placeholders = { username: 'Enter username', email: 'Enter email', mobile: 'Enter phone number' };
        document.getElementById('lookupLabel').textContent    = labels[method]      || method;
        document.getElementById('transferIdentifier').placeholder = placeholders[method] || 'Enter ' + method;
        document.getElementById('recipientResult').classList.add('hidden');
        document.getElementById('transferReceiverId').value = '';
        checkTransferReady();
    }

    function lookupRecipient() {
        const identifier = document.getElementById('transferIdentifier').value.trim();
        const result     = document.getElementById('recipientResult');
        if (!identifier) return;

        result.className = 'mt-2 px-4 py-3 rounded-xl border text-sm text-text-secondary';
        result.textContent = 'Searching…';
        result.classList.remove('hidden');

        fetch(LOOKUP_URL + '?identifier=' + encodeURIComponent(identifier) + '&type=' + _lookupMethod, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                result.className = 'mt-2 px-4 py-3 rounded-xl border border-red-200 bg-red-50 text-sm text-red-700';
                result.textContent = data.error;
                document.getElementById('transferReceiverId').value = '';
            } else {
                result.className = 'mt-2 px-4 py-3 rounded-xl border border-teal-200 bg-teal-50 text-sm text-teal-800';
                result.innerHTML = '<strong>' + data.name + '</strong> <span class="opacity-70">@' + data.username + '</span>';
                document.getElementById('transferReceiverId').value = data.id;
            }
            checkTransferReady();
        })
        .catch(() => {
            result.className = 'mt-2 px-4 py-3 rounded-xl border border-red-200 bg-red-50 text-sm text-red-700';
            result.textContent = 'Lookup failed. Please try again.';
            checkTransferReady();
        });
    }

    document.getElementById('transferIdentifier')?.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); lookupRecipient(); }
    });

    function updateTransferPreview() {
        const amount  = parseFloat(document.getElementById('transferAmount').value) || 0;
        const prev    = document.getElementById('transferPreview');
        const balance = parseFloat(document.getElementById('transferAvailBalance').textContent) || 0;
        const code    = document.getElementById('transferCurrencyLabel').textContent;
        if (amount <= 0) { prev.classList.add('hidden'); checkTransferReady(); return; }
        const charge  = TF_FIXED + (amount * TF_PERCENT / 100);
        const total   = amount + charge;
        prev.classList.remove('hidden');
        prev.innerHTML = `
            <div class="flex justify-between"><span>Amount</span><strong>${amount.toFixed(2)} ${code}</strong></div>
            <div class="flex justify-between"><span>Fee</span><strong>${charge.toFixed(2)} ${code}</strong></div>
            <div class="flex justify-between text-base font-bold border-t border-teal-200 pt-1 mt-1"><span>Total deducted</span><strong>${total.toFixed(2)} ${code}</strong></div>
            ${total > balance ? '<p class="text-red-600 text-xs mt-1">⚠ Insufficient balance</p>' : ''}`;
        checkTransferReady();
    }

    function checkTransferReady() {
        const receiverId = document.getElementById('transferReceiverId').value;
        const amount     = parseFloat(document.getElementById('transferAmount').value) || 0;
        const balance    = parseFloat(document.getElementById('transferAvailBalance').textContent) || 0;
        const charge     = TF_FIXED + (amount * TF_PERCENT / 100);
        const total      = amount + charge;
        document.getElementById('btnTransfer').disabled = !(receiverId && amount > 0 && total <= balance);
    }
    @endif
</script>
@endpush
