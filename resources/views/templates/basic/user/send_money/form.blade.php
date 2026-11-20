@extends($activeTemplate . 'layouts.master')
@section('activePage', 'send-money')
@section('content')
    @php
        if (old()) {
            $sendingAmount = old('sending_amount');
            $recipientAmount = old('recipient_amount');
            $deliveryMethodId = old('delivery_method');
            $sendingCountryId = old('sending_country');
            $recipientCountryId = old('recipient_country');
        }

        $user = auth()->user();
    @endphp
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="mb-4">
                        <h1 class="text-3xl font-bold text-brand-teal mb-2">Send Money</h1>
                        <p class="text-text-secondary text-sm">Fast, secure, and reliable international money transfers</p>

                    </div>
                    <div class="">
                        <form id="sendMoneyForm" action="{{ route('user.send.money.save') }}" class="grid grid-cols-1 lg:grid-cols-2 gap-6 register disableSubmission" method="post">
                            @csrf
                            <input type="hidden" id="pin_verified" value="0">

                            <div class="lg:col-span-1">
                                <div class="bg-card-white rounded-2xl p-8 shadow-[0_10px_30px_rgba(8,75,95,0.08)]">
                                    <div class="mb-8"><label class="block text-brand-teal text-sm font-semibold mb-3">You
                                            Send</label>
                                        <div class="relative">
                                            <div
                                                class="flex items-center bg-card-white border border-border-gray rounded-xl overflow-hidden focus-within:border-brand-teal focus-within:ring-2 focus-within:ring-brand-teal/10 transition-all">
                                                <input id="from" placeholder="0.00" name="sending_amount"
                                                    class="flex-1 px-4 py-3.5 text-text-primary text-lg font-semibold focus:outline-none bg-card-white sending-amount"
                                                    type="number" value="{{ @$sendingAmount }}" min="0" step="any" required>
                                                <div class="relative border-l border-border-gray">
                                                    <select id="sending-country" name="sending_country"
                                                        class="sending-countries country-picker pl-4 pr-10 py-3.5 text-brand-teal text-sm font-semibold focus:outline-none appearance-none bg-transparent cursor-pointer">
                                                        @foreach ($sendingCountries ?? [] as $sendingCountry)
                                                            <option value="{{ $sendingCountry->id }}"
                                                                data-conversion_rates="{{ $sendingCountry->conversionRates }}"
                                                                data-currency="{{ $sendingCountry->currency }}"
                                                                data-dial_code="{{ $sendingCountry->dial_code }}" data-id="{{ $sendingCountry->id }}"
                                                                data-image="{{ getImage(getFilePath('country') . '/' . $sendingCountry->image, getFileSize('country')) }}"
                                                                data-name="{{ $sendingCountry->name }}" data-rate="{{ $sendingCountry->rate ?: 1 }}">
                                                                {{ $sendingCountry->currency }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <i
                                                        class="ri-arrow-down-s-line absolute right-3 top-1/2 -translate-y-1/2 text-text-secondary pointer-events-none text-sm"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Inline amount warning --}}
                                    <div id="amountWarning" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 flex items-start gap-2">
                                        <i class="ri-error-warning-line flex-shrink-0 mt-0.5"></i>
                                        <span id="amountWarningText"></span>
                                    </div>
                                    {{-- Limit info hint (shown once limits are loaded) --}}
                                    <div id="limitInfoBar" class="hidden mb-4 p-3 bg-bg-main border border-border-gray rounded-xl">
                                        <p class="text-[10px] font-bold text-text-muted uppercase tracking-widest mb-2">Transfer Limits</p>
                                        <div class="grid grid-cols-3 gap-2 text-center">
                                            <div>
                                                <p class="text-xs font-semibold text-text-primary send_money_limit">—</p>
                                                <p class="text-[10px] text-text-muted">Per transfer</p>
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold text-text-primary today_limit">—</p>
                                                <p class="text-[10px] text-text-muted">Today left</p>
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold text-text-primary this_month_limit">—</p>
                                                <p class="text-[10px] text-text-muted">Month left</p>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Legacy limitMessage (kept for jQuery compat) --}}
                                    <small class="limitMessage hidden"></small>
                                    <div class="mb-8 p-4 bg-brand-teal/5 rounded-xl border border-border-gray">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <div
                                                    class="w-8 h-8 bg-brand-teal/10 rounded-lg flex items-center justify-center">
                                                    <i class="ri-exchange-line text-brand-teal text-sm"></i>
                                                </div>
                                                <span class="text-sm text-text-secondary">Exchange Rate</span>
                                            </div>
                                            <div class="text-sm font-semibold text-brand-teal flex items-center gap-1">
                                                <span>1</span>
                                                <span class="sending-currency"></span>
                                                <span>=</span>
                                                <span class="exchange-rate"></span>
                                                <span class="recipient-currency"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-8"><label
                                            class="block text-brand-teal text-sm font-semibold mb-3">Recipient Gets</label>
                                        <div class="relative">
                                            <div
                                                class="flex items-center bg-card-white border border-border-gray rounded-xl overflow-hidden focus-within:border-brand-teal focus-within:ring-2 focus-within:ring-brand-teal/10 transition-all">
                                                <input readonly id="to" placeholder="0.00" name="recipient_amount"
                                                    class="flex-1 px-4 py-3.5 text-text-primary text-lg font-semibold focus:outline-none bg-card- recipient-amount"
                                                    type="text" value="{{ @$recipientAmount }}">
                                                <div class="relative border-l border-border-gray"><select id="recipient-country" name="recipient_country"
                                                        class="country-picker recipient-countries pl-4 pr-10 py-3.5 text-brand-teal text-sm font-semibold focus:outline-none appearance-none bg-transparent cursor-pointer">
                                                        @foreach ($receivingCountries ?? [] as $receivingCountry)
                                                            <option value="{{ $receivingCountry->id }}" data-currency="{{ $receivingCountry->currency }}"
                                                                data-delivery_methods="{{ $receivingCountry->countryDeliveryMethods }}"
                                                                data-dial_code="{{ $receivingCountry->dial_code }}"
                                                                data-fixed_charge="{{ $receivingCountry->fixed_charge }}"
                                                                data-id="{{ $receivingCountry->id }}"
                                                                data-image="{{ getImage(getFilePath('country') . '/' . $receivingCountry->image, getFileSize('country')) }}"
                                                                data-name="{{ $receivingCountry->name }}"
                                                                data-percent_charge="{{ $receivingCountry->percent_charge }}"
                                                                data-has_agent="{{ $receivingCountry->has_agent }}"
                                                                data-rate="{{ $receivingCountry->rate }}"
                                                                data-country_code="{{ $receivingCountry->country_code }}">
                                                                {{ $receivingCountry->currency }}
                                                            </option>
                                                        @endforeach
                                                    </select><i
                                                        class="ri-arrow-down-s-line absolute right-3 top-1/2 -translate-y-1/2 text-text-secondary pointer-events-none text-sm"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                            
                                    </div>
                                       
                                    <div class="mb-8"><label class="block text-brand-teal text-sm font-semibold mb-3">Source
                                            of Funds</label>
                                        <div class="relative">
                                            <select name="source_of_funds" required
                                                class="w-full px-4 py-3.5 bg-card-white border border-border-gray rounded-xl text-text-primary text-sm focus:outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 transition-all appearance-none cursor-pointer">
                                                @foreach ($sources as $source)
                                                    <option @selected(old('source_of_funds') == $source->id) value="{{ $source->id }}">
                                                        {{ __($source->name) }}
                                                    </option>
                                                @endforeach
                                            </select><i
                                                class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-text-secondary pointer-events-none"></i>
                                        </div>
                                    </div>
                                    <div class="mb-8"><label class="block text-brand-teal text-sm font-semibold mb-3">Sending
                                            Purpose</label>
                                        <div class="relative">
                                            <select data-minimum-results-for-search="-1" name="sending_purpose" required
                                                class="w-full px-4 py-3.5 bg-card-white border border-border-gray rounded-xl text-text-primary text-sm focus:outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 transition-all appearance-none cursor-pointer">
                                                @foreach ($purposes as $purpose)
                                                    <option @selected(old('sending_purpose') == $purpose->id) value="{{ $purpose->id }}">
                                                        {{ __($purpose->name) }}
                                                    </option>
                                                @endforeach
                                            </select><i
                                                class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-text-secondary pointer-events-none"></i>
                                        </div>
                                    </div>
                                    <input type="hidden" name="wallet_id" id="walletIdInput" value="">

                                    <div>
                                        <label class="block text-brand-teal text-sm font-semibold mb-4">
                                            Payment Method
                                        </label>

                                        <div class="grid grid-cols-2 gap-3 mb-3">

                                            <!-- Direct Payment -->
                                            <label
                                                for="directBtn"
                                                class="paymentMethod relative p-5 rounded-xl border-2 transition-all duration-300 text-left cursor-pointer
                                                       border-border-gray bg-card-white hover:border-brand-teal/30"
                                                data-method="card"
                                            >
                                                <input
                                                    id="directBtn"
                                                    type="radio"
                                                    name="payment_type"
                                                    value="2"
                                                    required
                                                    class="btn-selected__input directPayment sr-only"
                                                >
                                                <div class="absolute top-4 right-4 w-5 h-5 bg-brand-teal rounded-full flex items-center justify-center checkmark hidden">
                                                    <i class="ri-check-line text-white text-xs"></i>
                                                </div>
                                                <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-3 bg-brand-teal">
                                                    <i class="ri-bank-card-line text-xl text-brand-gold"></i>
                                                </div>
                                                <h4 class="text-brand-teal font-semibold text-sm mb-1">Direct Payment</h4>
                                                <p class="text-text-secondary text-xs">Pay with card or bank</p>
                                            </label>

                                            <!-- Apaylo Interac e-Transfer -->
                                            <label
                                                for="apayloBtn"
                                                class="paymentMethod relative p-5 rounded-xl border-2 transition-all duration-300 text-left cursor-pointer
                                                       border-border-gray bg-card-white hover:border-brand-teal/30"
                                                data-method="apaylo"
                                            >
                                                <input
                                                    id="apayloBtn"
                                                    type="radio"
                                                    name="payment_type"
                                                    value="3"
                                                    class="btn-selected__input apayloPayment sr-only"
                                                >
                                                <div class="absolute top-4 right-4 w-5 h-5 bg-brand-teal rounded-full flex items-center justify-center checkmark hidden">
                                                    <i class="ri-check-line text-white text-xs"></i>
                                                </div>
                                                <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-3" style="background:#A71B47;">
                                                    <i class="ri-send-plane-fill text-xl text-white"></i>
                                                </div>
                                                <h4 class="text-brand-teal font-semibold text-sm mb-1">Interac e-Transfer</h4>
                                                <p class="text-text-secondary text-xs">Pay via Apaylo — sent to your email</p>
                                            </label>

                                        </div>

                                        {{-- Account Balance payment (always available when user has a balance) --}}
                                        @if(auth()->user()->balance > 0)
                                        <div class="mb-3">
                                            <p class="text-xs font-semibold text-text-secondary uppercase tracking-wide mb-2">Pay from Account Balance</p>
                                            <label for="balanceBtn"
                                                class="paymentMethod relative flex items-center justify-between gap-3 p-4 rounded-xl border-2 transition-all duration-300 cursor-pointer border-border-gray bg-card-white hover:border-brand-teal/30"
                                                data-method="balance">
                                                <input
                                                    id="balanceBtn"
                                                    type="radio"
                                                    name="payment_type"
                                                    value="4"
                                                    class="btn-selected__input balancePayment sr-only"
                                                    data-balance="{{ auth()->user()->balance }}">
                                                <div class="checkmark absolute top-3 right-3 w-5 h-5 bg-brand-teal rounded-full items-center justify-center hidden">
                                                    <i class="ri-check-line text-white text-xs"></i>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-xl bg-brand-teal flex items-center justify-center flex-shrink-0">
                                                        <i class="ri-coins-line text-brand-gold text-base"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-brand-teal font-semibold text-sm">Account Balance</p>
                                                        <p class="text-text-secondary text-xs">Instant — no gateway needed</p>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-brand-teal font-bold text-sm">{{ number_format(auth()->user()->balance, 2) }}</p>
                                                    <p class="text-text-secondary text-xs">{{ gs('cur_text') }}</p>
                                                </div>
                                            </label>
                                            <div id="insufficientAccountBalanceError" class="hidden mt-2 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 flex items-center gap-2">
                                                <i class="ri-error-warning-line flex-shrink-0"></i>
                                                <span>Insufficient account balance for this transfer.</span>
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Wallet payment (only shown when wallet module is enabled) --}}
                                        @if(gs('wallet_module'))
                                        @if($wallets->count())
                                        <div>
                                            <p class="text-xs font-semibold text-text-secondary uppercase tracking-wide mb-2">Pay from Wallet</p>
                                            <div class="space-y-2" id="walletPaymentList">
                                                @foreach($wallets as $wallet)
                                                @php
                                                    $wCountry   = $walletCountries[$wallet->currency_code] ?? null;
                                                    $wRate      = (float) ($wCountry->rate ?? 1);
                                                    $balanceCad = $wRate > 0 ? round($wallet->balance / $wRate, 8) : 0;
                                                @endphp
                                                <label for="walletBtn_{{ $wallet->id }}"
                                                    class="paymentMethod wallet-method-label relative flex items-center justify-between gap-3 p-4 rounded-xl border-2 transition-all duration-300 cursor-pointer border-border-gray bg-card-white hover:border-brand-teal/30"
                                                    data-method="wallet"
                                                    data-wallet-id="{{ $wallet->id }}"
                                                    data-balance-cad="{{ $balanceCad }}">
                                                    <input
                                                        id="walletBtn_{{ $wallet->id }}"
                                                        type="radio"
                                                        name="payment_type"
                                                        value="1"
                                                        class="btn-selected__input walletPayment sr-only"
                                                        data-balance="{{ $balanceCad }}"
                                                        data-wallet-id="{{ $wallet->id }}">
                                                    <div class="checkmark absolute top-3 right-3 w-5 h-5 bg-brand-teal rounded-full items-center justify-center hidden">
                                                        <i class="ri-check-line text-white text-xs"></i>
                                                    </div>
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 rounded-xl bg-brand-teal flex items-center justify-center flex-shrink-0">
                                                            <i class="ri-wallet-3-line text-brand-gold text-base"></i>
                                                        </div>
                                                        <div>
                                                            <p class="text-brand-teal font-semibold text-sm">{{ $wallet->currency_code }} Wallet</p>
                                                            <p class="text-text-secondary text-xs">{{ $wallet->currency_name }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-brand-teal font-bold text-sm">{{ number_format($wallet->balance, 2) }}</p>
                                                        <p class="text-text-secondary text-xs">{{ $wallet->currency_code }}</p>
                                                        @if($wallet->is_default)
                                                        <span class="text-[9px] font-bold text-teal-600 bg-teal-100 px-1.5 py-0.5 rounded-full">Default</span>
                                                        @endif
                                                    </div>
                                                </label>
                                                @endforeach
                                            </div>
                                            <div id="insufficientBalanceError" class="d-none mt-2 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 flex items-center gap-2">
                                                <i class="ri-error-warning-line flex-shrink-0"></i>
                                                <span>Insufficient wallet balance for this transfer.</span>
                                            </div>
                                        </div>
                                        @else
                                        <div class="p-4 bg-teal-50 border border-teal-200 rounded-xl text-sm text-teal-800">
                                            <i class="ri-wallet-3-line mr-1"></i>
                                            No wallets yet.
                                            <a href="{{ route('user.wallets') }}" class="font-bold underline">Create one</a> to pay from a wallet.
                                        </div>
                                        @endif
                                        @endif {{-- gs('wallet_module') --}}

                                    </div>

                                </div>
                            </div>
                            <div class="lg:col-span-1">
                                <div
                                    class="bg-card-white rounded-2xl p-8 shadow-[0_10px_30px_rgba(8,75,95,0.08)] h-fit sticky top-8">
                                    <div class="mb-6"><label class="block text-brand-teal text-sm font-semibold mb-3">Select
                                            Recipient</label>
                        <div class="relative"><select
                                                class="existing-recipient w-full px-4 py-3.5 bg-card-white border border-border-gray rounded-xl text-text-primary text-sm focus:outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 transition-all appearance-none cursor-pointer">
                                                <option >Select a recipient
                                                    </option>
                                                @foreach ($recipients as $recipient)
                                                    <option data-recipient="{{ json_encode($recipient->only(['id','name','mobile','email','address','country_id','delivery_method_id','service_id','service_form_data'])) }}">{{ $recipient->name }}</option>
                                                @endforeach
                                            </select><i
                                                class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-text-secondary pointer-events-none"></i>
                                        </div><button onclick="openAddRecipientModal()"
                                            class="mt-3 text-brand-teal text-sm font-medium hover:text-brand-gold transition-colors flex items-center cursor-pointer whitespace-nowrap"><i
                                                class="ri-add-circle-line mr-1.5"></i>Add New Recipient</button>
                                    </div>
                                    <div class="mb-6"><label
                                            class="block text-brand-teal text-sm font-semibold mb-3">Recipient
                                            Name</label><input placeholder="Enter full name"
                                            class="recipient_name w-full px-4 py-3.5 bg-card-white border border-border-gray rounded-xl text-text-primary text-sm placeholder:text-text-secondary focus:outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 transition-all"
                                            type="text" id="recipient_name" name="recipient[name]" required type="text" value="{{ old('recipient')['name'] ?? null }}"></div>
                                    <div class="mb-6"><label class="block text-brand-teal text-sm font-semibold mb-3">Mobile
                                            Number</label><input placeholder="+1 (555) 000-0000"
                                            class="checkRecipient w-full px-4 py-3.5 bg-card-white border border-border-gray rounded-xl text-text-primary text-sm placeholder:text-text-secondary focus:outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 transition-all"
                                            type="tel" id="recipient_mobile" name="recipient[mobile]" required type="number" value="{{ old('recipient')['mobile'] ?? null }}"></div>
                                    <div class="mb-6"><label class="block text-brand-teal text-sm font-semibold mb-3">Email
                                            Address</label><input placeholder="recipient@example.com"
                                            class="checkRecipient w-full px-4 py-3.5 bg-card-white border border-border-gray rounded-xl text-text-primary text-sm placeholder:text-text-secondary focus:outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 transition-all"
                                            type="email" id="recipient_email" name="recipient[email]" required type="email" value="{{ old('recipient')['email'] ?? null }}"></div>
                                    <div class="mb-6"><label
                                            class="block text-brand-teal text-sm font-semibold mb-3">Address</label><input
                                            placeholder="Enter complete address" rows="3"
                                            class="w-full px-4 py-3.5 bg-card-white border border-border-gray rounded-xl text-text-primary text-sm placeholder:text-text-secondary focus:outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 transition-all resize-none" id="recipient_address" name="recipient[address]" value="{{ old('recipient')['address'] ?? null }}" required>
                                    </div>
                                    <div class="mb-8"><label
                                            class="block text-brand-teal text-sm font-semibold mb-3" for="deliveryMethod">Delivery Method</label>
                                        <div class="relative">
                                            <select data-minimum-results-for-search="-1" id="deliveryMethod" name="delivery_method" required
                                                class="w-full px-4 py-3.5 bg-card-white border border-border-gray rounded-xl text-text-primary text-sm focus:outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 transition-all appearance-none cursor-pointer">
                                                <option value="">Select delivery method</option>
                                            </select><i
                                                class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-text-secondary pointer-events-none"></i>
                                        </div>
                                    </div>

                                    <div class="mb-8 services-div d-none"><label
                                            class="block text-brand-teal text-sm font-semibold mb-3">Service</label>
                                        <div class="relative">
                                            <select data-minimum-results-for-search="-1" name="service" 
                                                class="w-full px-4 py-3.5 bg-card-white border border-border-gray rounded-xl text-text-primary text-sm focus:outline-none focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 transition-all appearance-none cursor-pointer countryServices">
                                                <option value="">Select one</option>
                                                
                                            </select><i
                                                class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-text-secondary pointer-events-none"></i>
                                        </div>
                                    </div>
                                     <div class="mb-8 formData"></div>
                                    <div id="saveRecipientContainer" class="mt-4 mb-3 flex items-center gap-3">
                                        <input
                                            type="checkbox"
                                            name="save_recipient"
                                            id="save_recipient"
                                            @checked(old('save_recipient'))
                                            class="h-5 w-5 rounded border-border-gray text-brand-teal focus:ring-2 focus:ring-brand-teal focus:ring-offset-2 cursor-pointer"
                                        >
                                    
                                        <label
                                            for="save_recipient"
                                            class="text-sm font-medium text-text-primary cursor-pointer select-none"
                                        >
                                            @lang('Save Recipient')
                                        </label>
                                    </div>
                                    <div class="border-t border-border-gray pt-6 mb-6">
                                        <h3 class="text-brand-teal text-sm font-semibold mb-4">
                                            Transaction Summary</h3>
                                        <div class="space-y-3 mb-4">
                                            <div class="flex items-center justify-between"><span
                                                    class="text-sm text-text-secondary">Sending
                                                    Amount</span><span class="text-sm font-medium text-text-primary"><span class="sending-amount-total"></span>
                                                        <span class="sending-currency"></span></span>
                                            </div>
                                            <div class="flex items-center justify-between"><span
                                                    class="text-sm text-text-secondary">Total
                                                    Charges</span><span class="text-sm font-medium text-text-primary"><span class="charge-amount-text"></span>
                                                        <span class="sending-currency"></span></span></div>
                                        </div>
                                        <div class="pt-4 border-t border-border-gray">
                                            <div class="flex items-center justify-between"><span
                                                    class="text-base font-semibold text-brand-teal">Final Amount</span><span
                                                     class="text-xl font-bold text-brand-gold"><span class="final-amount-text"></span>
                                                        <span class="sending-currency"></span></span></div>
                                        </div>
                                    </div>
                                    <button type="button"
                                        onclick="handleTransferInitiation()"
                                        class="formSubmitButton w-full bg-brand-gold hover:bg-brand-gold/90 text-white font-semibold py-4 rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-brand-gold/20 cursor-pointer whitespace-nowrap text-base">Continue</button>
                                    <div class="mt-4 flex items-start space-x-2 p-3 bg-bg-main rounded-lg"><i
                                            class="ri-shield-check-line text-brand-teal text-sm mt-0.5"></i>
                                        <p class="text-xs text-text-secondary">Your transaction is secured with bank-grade
                                            encryption</p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @include($activeTemplate . 'partials.pin-verification-modal')

<script>
        "use strict";
        let agentStatus = `{{ gs('agent_module') }}`;
        let agent = `@lang('Agent')`;
        let agentFixedCharge = `{{ gs('agent_charges')->fixed_charge ?? 0 }}`;
        let agentPercentCharge = `{{ gs('agent_charges')->percent_charge ?? 0 }}`;

        let sendLimit = `{{ gs('user_send_money_limit') }}`;
        let sendingAmount = `{{ $sendingAmount }}`;
        let recipientAmount = `{{ $recipientAmount }}`;
        let deliveryMethodId = `{{ $deliveryMethodId }}`;
        let recipientCountryId = `{{ $recipientCountryId }}`;
        let sendingCountryId = `{{ $sendingCountryId }}`;

        if (deliveryMethodId) {
            deliveryMethodId *= 1;
        }

        let defaultSelectOption = `@lang('Select One')`;
        let serviceStatus = true;
        let serviceURL = "{{ route('services') }}";
        let serviceLabel = `@lang('Service')`;
        let isAgent = false;
        

    </script>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const methods = document.querySelectorAll('.paymentMethod');

    methods.forEach(method => {
        const input = method.querySelector('input[type="radio"]');

        input.addEventListener('change', function () {
            // Reset all
            methods.forEach(m => {
                m.classList.remove('selected');
                const check = m.querySelector('.checkmark');
                if (check) check.classList.add('hidden');
            });

            // Activate current
            method.classList.add('selected');
            const checkmark = method.querySelector('.checkmark');
            if (checkmark) checkmark.classList.remove('hidden');

            // Track wallet_id
            const walletInput = document.getElementById('walletIdInput');
            if (this.value === '1' && this.dataset.walletId) {
                walletInput.value = this.dataset.walletId;
                checkBalance();
            } else {
                walletInput.value = '';
                document.getElementById('insufficientBalanceError')?.classList.add('d-none');
                if (!document.querySelector('.limitMessage:not(.d-none)')) {
                    document.querySelector('.formSubmitButton')?.removeAttribute('disabled');
                }
            }
        });
    });
});
</script>

    
    <script>
document.getElementById('sendMoneyForm').addEventListener('submit', function (e) {
    e.preventDefault(); // ⛔ stop immediate submit
    handleTransferInitiation();
});
function openPINSetupModal() {
    initPINVerificationModal(false); // user does NOT have PIN
}

function openPINVerificationModal() {
    initPINVerificationModal(true); // user HAS PIN
}

</script>


    <script src="{{ asset('assets1/global/js/sendMoney.js') }}"></script>


    <script>
    (function ($) {
        function flagOption(option) {
            if (!option.id) return $('<span>' + option.text + '</span>');
            var img  = $(option.element).data('image');
            var code = $(option.element).data('currency') || option.text;
            var name = $(option.element).data('name') || '';
            if (img) {
                return $('<span style="display:flex;align-items:center;gap:8px;">' +
                    '<img src="' + img + '" style="width:22px;height:15px;object-fit:cover;border-radius:2px;flex-shrink:0;" onerror="this.style.display=\'none\'">' +
                    '<strong style="font-size:13px;">' + code + '</strong>' +
                    (name ? '<span style="color:#777;font-size:12px;">' + name + '</span>' : '') +
                    '</span>');
            }
            return $('<span>' + code + '</span>');
        }

        function flagSelection(option) {
            if (!option.id) return $('<span>' + option.text + '</span>');
            var img  = $(option.element).data('image');
            var code = $(option.element).data('currency') || option.text;
            if (img) {
                return $('<span style="display:flex;align-items:center;gap:6px;">' +
                    '<img src="' + img + '" style="width:22px;height:15px;object-fit:cover;border-radius:2px;flex-shrink:0;" onerror="this.style.display=\'none\'">' +
                    '<strong style="font-size:13px;">' + code + '</strong>' +
                    '</span>');
            }
            return $('<span>' + code + '</span>');
        }

        $('#sending-country').select2({
            templateResult: flagOption,
            templateSelection: flagSelection,
            minimumResultsForSearch: 5,
            width: 'auto',
            dropdownAutoWidth: true,
        });

        $('#recipient-country').select2({
            templateResult: flagOption,
            templateSelection: flagSelection,
            minimumResultsForSearch: 5,
            width: 'auto',
            dropdownAutoWidth: true,
        });

        // Re-fire native change when Select2 changes (sendMoney.js listens on native change)
        $('#sending-country, #recipient-country').on('select2:select', function () {
            $(this).trigger('change');
        });

        // Restore previously selected values after Select2 init
        if (sendingCountryId) $('#sending-country').val(sendingCountryId).trigger('change');
        if (recipientCountryId) $('#recipient-country').val(recipientCountryId).trigger('change');
    })(jQuery);
    </script>

    <script>
        (function($) {
            "use strict";

            $(document).on('change', '.countryServices', function() {
                let serviceId = $(this).val();
                if (serviceId) {
                    let data = {
                        service_id: serviceId
                    }
                    const fieldClasses = `
                        w-full px-4 py-3.5 bg-card-white border border-border-gray rounded-xl
                        text-text-primary text-sm placeholder:text-text-secondary
                        focus:outline-none focus:border-brand-teal
                        focus:ring-2 focus:ring-brand-teal/10 transition-all
                        `;


                    $.get("{{ route('service.form') }}", data,
                        function(data, textStatus, jqXHR) {
                            if (data.success && data.html.length) {
                                $('.formData').html(data.html);
                                $('.formData').find('label').addClass(
                                    'block text-brand-teal text-sm font-semibold mb-3');
                                $('.formData').find('input, select, textarea').each(function () {
                                    $(this).addClass(fieldClasses);
                                });
                                $('.formData').removeClass('d-none');
                            } else {
                                $('.formData').html('');
                                $('.formData').addClass('d-none');
                            }
                        }
                    );
                } else {
                    $('.formData').empty();
                }
            });

            $(document).on('click', '.walletPayment', function() {
                checkBalance();
            });

            let availableForToday = 0;
            let availableForThisMonth = 0;
            let limitPerSendMoney = 0;
            let dailyLimit = 0;
            let monthlyLimit = 0;

            $('.country-picker').on('change', function() {
                let general = @json(gs());
                let user = @json($user);
                let todaySendMoneyInBaseCur = @json($todaySendMoney);
                let thisMonthSendMoneyInBase = @json($thisMonthSendMoney);
                let sender = $('[name=sending_country]');
                let baseToSenderCurrency = parseFloat(sender.find(':selected').data('rate'));

                if (user.daily_send_money_limit) {
                    dailyLimit = parseFloat(user.daily_send_money_limit * baseToSenderCurrency).toFixed(2);
                } else {
                    dailyLimit = parseFloat(general.user_daily_send_money_limit * baseToSenderCurrency).toFixed(
                        2);
                }

                if (user.monthly_send_money_limit) {
                    monthlyLimit = parseFloat(user.monthly_send_money_limit * baseToSenderCurrency).toFixed(2);
                } else {
                    monthlyLimit = parseFloat(general.user_monthly_send_money_limit * baseToSenderCurrency)
                        .toFixed(2);
                }

                if (user.per_send_money_limit) {
                    limitPerSendMoney = parseFloat(user.per_send_money_limit * baseToSenderCurrency).toFixed(2);
                } else {
                    limitPerSendMoney = parseFloat(general.user_send_money_limit * baseToSenderCurrency)
                        .toFixed(2);
                }

                availableForToday = parseFloat(dailyLimit - todaySendMoneyInBaseCur * baseToSenderCurrency)
                    .toFixed(2);
                availableForToday = parseFloat(availableForToday > 0 ? availableForToday : 0).toFixed(2);
                availableForThisMonth = parseFloat(monthlyLimit - thisMonthSendMoneyInBase *
                    baseToSenderCurrency).toFixed(2);
                availableForThisMonth = parseFloat(availableForThisMonth > 0 ? availableForThisMonth : 0)
                    .toFixed(2);

                if (parseFloat(availableForToday) > parseFloat(availableForThisMonth)) {
                    availableForToday = availableForThisMonth;
                }

                // Show the limit info bar once limits are known
                let sendingCur = $('[name=sending_country]').find(':selected').data('currency') || '';

                $('.send_money_limit').text(sendingCur + ' ' + limitPerSendMoney);
                $('.daily_send_money_limit').text(sendingCur + ' ' + dailyLimit);
                $('.monthly_send_money_limit').text(sendingCur + ' ' + monthlyLimit);
                $('.today_limit').text(sendingCur + ' ' + availableForToday);
                $('.this_month_limit').text(sendingCur + ' ' + availableForThisMonth);
                document.getElementById('limitInfoBar').classList.remove('hidden');
            }).change();

            $(document).on('input', '.sending-amount, .recipient-amount', function() {
                let sendingAmount = parseFloat($('[name=sending_amount]').val());
                let sendingCur   = $('[name=sending_country]').find(':selected').data('currency') || '';
                const warnEl     = document.getElementById('amountWarning');
                const warnText   = document.getElementById('amountWarningText');

                let warningMsg = '';
                if (!sendingAmount || sendingAmount <= 0) {
                    warningMsg = 'Please enter an amount greater than 0.';
                } else if (sendingAmount > parseFloat(limitPerSendMoney)) {
                    warningMsg = `Amount exceeds the per-transfer limit of ${parseFloat(limitPerSendMoney).toFixed(2)} ${sendingCur}.`;
                } else if (sendingAmount > parseFloat(availableForToday)) {
                    warningMsg = `Amount exceeds your remaining daily limit of ${parseFloat(availableForToday).toFixed(2)} ${sendingCur}.`;
                } else if (sendingAmount > parseFloat(availableForThisMonth)) {
                    warningMsg = `Amount exceeds your remaining monthly limit of ${parseFloat(availableForThisMonth).toFixed(2)} ${sendingCur}.`;
                }

                if (warningMsg) {
                    warnText.textContent = warningMsg;
                    warnEl.classList.remove('hidden');
                    $('.limitMessage').removeClass('hidden');
                    $('.formSubmitButton').attr('disabled', true);
                } else {
                    warnEl.classList.add('hidden');
                    $('.limitMessage').addClass('hidden');
                    $('.formSubmitButton').attr('disabled', false);
                }

                if ($('[name=payment_type]:checked').val() == 1) {
                    checkBalance();
                }
                if ($('[name=payment_type]:checked').val() == 4) {
                    checkAccountBalance();
                }
            });

            function checkBalance() {
                const selected = $('.walletPayment:checked');
                if (!selected.length) return;
                const balance     = parseFloat(selected.data('balance')) || 0;
                const finalAmount = parseFloat($('.base-amount-text').text()) || 0;
                if (finalAmount > balance) {
                    $('.insufficientBalanceError').removeClass('d-none').fadeIn();
                    $('.formSubmitButton').attr('disabled', true);
                } else {
                    $('.insufficientBalanceError').fadeOut().addClass('d-none');
                    if ($('.limitMessage').hasClass('d-none')) {
                        $('.formSubmitButton').attr('disabled', false);
                    }
                }
            }

            function checkAccountBalance() {
                const selected = $('.balancePayment:checked');
                if (!selected.length) return;
                const balance     = parseFloat(selected.data('balance')) || 0;
                const finalAmount = parseFloat($('.base-amount-text').text()) || 0;
                const errEl       = document.getElementById('insufficientAccountBalanceError');
                if (finalAmount > balance) {
                    if (errEl) errEl.classList.remove('hidden');
                    $('.formSubmitButton').attr('disabled', true);
                } else {
                    if (errEl) errEl.classList.add('hidden');
                    if ($('.limitMessage').hasClass('d-none')) {
                        $('.formSubmitButton').attr('disabled', false);
                    }
                }
            }

            $(document).on('click', '.balancePayment', function() {
                $('#insufficientAccountBalanceError').addClass('hidden');
                $('.insufficientBalanceError').fadeOut().addClass('d-none');
                checkAccountBalance();
            });

            $('.directPayment, .apayloPayment').on('click', function() {
                $('.insufficientBalanceError').fadeOut().addClass('d-none');
                $('#insufficientAccountBalanceError').addClass('hidden');
                if ($('.limitMessage').hasClass('d-none')) {
                    $('.formSubmitButton').attr('disabled', false);
                }
            });

            $('.formSubmitButton').on('click', function() {
                var paymentType = $('[name=payment_type]:checked').val();
                if (!paymentType) {
                    notify('error', 'Please select a payment type')
                }
            });

            $('.showLimit').on('click', function() {
                $('#limitModal').modal('show');
            });

            @if (old('payment_type') == 1 && old('wallet_id'))
                const oldWalletBtn = document.getElementById('walletBtn_{{ old('wallet_id') }}');
                if (oldWalletBtn) { oldWalletBtn.click(); }
            @endif

            @if (old('payment_type') == 2)
                $('.directPayment').click();
            @endif

            @if (old('payment_type') == 3)
                $('.apayloPayment').click();
            @endif

            $('.existing-recipient').on('change', function() {
                let recipient = $(this).find(':selected').data('recipient');
                if (recipient != undefined && recipient.name !== undefined) {
                    $('#saveRecipientContainer').addClass('d-none');

                    // Always populate the basic contact fields
                    $('.recipient_name').val(recipient.name || '');
                    $('#recipient_mobile').val(recipient.mobile || '');
                    $('#recipient_email').val(recipient.email || '');
                    $('#recipient_address').val(recipient.address || '');

                    // Pre-fill country, delivery method, service when the recipient has that context
                    if (recipient.country_id) {
                        let countrySelect = $('#recipient-country');

                        // Set country; trigger change so setDeliveryMethods() rebuilds the dropdown
                        countrySelect.val(recipient.country_id).trigger('change');

                        // setDeliveryMethods() is synchronous, so the dropdown is ready immediately.
                        // Set delivery method then trigger change to fire setServices() for that method.
                        setTimeout(function() {
                            if (recipient.delivery_method_id) {
                                $('#deliveryMethod').val(recipient.delivery_method_id).trigger('change');
                            }

                            if (recipient.service_id) {
                                // Wait for setServices() AJAX to complete, then pre-select service
                                // and load its form fields.
                                setTimeout(function() {
                                    $('.countryServices').val(recipient.service_id).trigger('change');

                                    if (recipient.service_form_data && Array.isArray(recipient.service_form_data)) {
                                        // Give service form AJAX a moment to render, then pre-fill values
                                        setTimeout(function() {
                                            recipient.service_form_data.forEach(function(field) {
                                                let key = (field.name || field.label || '').trim();
                                                // Match directly on the input's name attribute (= $data->label,
                                                // the snake_case key used in the form builder).
                                                // Also try a spaces-to-underscores fallback for display-name storage.
                                                let $input = $('.formData').find(
                                                    '[name="' + key + '"], [name="' + key.toLowerCase().replace(/\s+/g,'_') + '"]'
                                                ).first();
                                                if ($input.length) {
                                                    $input.val(field.value || '').trigger('change');
                                                }
                                            });
                                        }, 600);
                                    }
                                }, 600);
                            }
                        }, 100);
                    }
                } else {
                    $('#saveRecipientContainer').removeClass('d-none');
                    $('.recipient_name').val('');
                    $('#recipient_mobile').val('');
                    $('#recipient_email').val('');
                    $('#recipient_address').val('');
                }
            });
            
            $(window).on('load', function () {
                const params = new URLSearchParams(window.location.search);
                const recipientId = params.get('recipient');
            
                if (recipientId) {
                    const $select = $('.existing-recipient');
            
                    let found = false;
            
                    $select.find('option').each(function() {
                        let recipientData = $(this).data('recipient');
                        let recipient = typeof recipientData === 'string' ? JSON.parse(recipientData) : recipientData;
            
                        if (recipient && recipient.id == recipientId) {
                            $(this).prop('selected', true);
                            found = true;
                            return false; // break the loop
                        }
                    });
            
                    if (found) {
                        // Trigger change (Select2 safe)
                        // if ($select.hasClass('select2-hidden-accessible')) {
                        //     $select.trigger('change.select2');
                        // } else {
                            $select.trigger('change');
                        // }
                    }
                }
            });


        })(jQuery);
    </script>
    <script>
function validateSendMoneyForm() {
    const requiredFields = [
        '[name="sending_amount"]',
        '[name="sending_country"]',
        '[name="recipient_country"]',
        '[name="source_of_funds"]',
        '[name="sending_purpose"]',
        '[name="payment_type"]:checked',
        '#recipient_name',
        '#recipient_mobile',
        '#recipient_email',
        '#recipient_address',
        '#deliveryMethod'
    ];

    for (let selector of requiredFields) {
        if (!document.querySelector(selector)) {
            notify('error', 'Please complete all required fields');
            return false;
        }
    }

    return true;
}
</script>

    <script>
    function handleTransferInitiation() {
        // basic frontend validation still applies
        if (!validateSendMoneyForm()) {
            return;
        }

        @if(auth()->user()->has_pin)
            openPINVerificationModal();
        @else
            openPINSetupModal();
        @endif
    }

    function onPinVerifiedSuccess() {
        document.getElementById('pin_verified').value = 1;
    
        const form = document.querySelector(
            'form[action="{{ route('user.send.money.save') }}"]'
        );
    
        form.submit();
    }

    // ── Recipient modal context ─────────────────────────────────────────────
    // Keep a global snapshot of the current service context so the
    // "Add New Recipient" modal can pick it up without fragile DOM parsing.
    window._smRecipientCtx = { country_id:'', delivery_method_id:'', service_id:'', service_form_data:'' };

    function _rebuildSmCtx() {
        var countryId        = $('#recipient-country').val() || '';
        var deliveryMethodId = $('#deliveryMethod').val()    || '';
        var serviceId        = $('.countryServices').val()   || '';
        var fields           = [];

        if (serviceId) {
            $('.formData').find('.form-group').each(function () {
                // Strip child elements (icons, asterisks) and keep only the label text
                var labelText = $(this).find('label').first().clone().children().remove().end().text().trim();
                var $inp      = $(this).find('input:not([type=file]):not([type=hidden]), select, textarea').first();
                if ($inp.length && labelText) {
                    fields.push({
                        name:  labelText,
                        type:  $inp.attr('type') || $inp.prop('tagName').toLowerCase(),
                        value: $inp.val() || '',
                    });
                }
            });
        }

        window._smRecipientCtx = {
            country_id:         countryId,
            delivery_method_id: deliveryMethodId,
            service_id:         serviceId,
            service_form_data:  fields.length ? JSON.stringify(fields) : '',
        };
    }

    // Rebuild whenever any relevant field changes
    $(document).on(
        'change input',
        '#recipient-country, #deliveryMethod, .countryServices, .formData input, .formData select, .formData textarea',
        _rebuildSmCtx
    );
    // Also rebuild once after service form HTML is injected (handled by triggering change on .countryServices above)


</script>




@endsection
<style>
/* Country-picker Select2 — inline inside amount inputs */
#sending-country + .select2-container,
#recipient-country + .select2-container {
    min-width: 110px;
}
#sending-country + .select2-container .select2-selection--single,
#recipient-country + .select2-container .select2-selection--single {
    height: 50px;
    border: none;
    border-radius: 0;
    background: transparent;
    display: flex;
    align-items: center;
    padding: 0 32px 0 12px;
    box-shadow: none;
}
#sending-country + .select2-container .select2-selection__rendered,
#recipient-country + .select2-container .select2-selection__rendered {
    line-height: normal;
    padding: 0;
    color: inherit;
}
#sending-country + .select2-container .select2-selection__arrow,
#recipient-country + .select2-container .select2-selection__arrow {
    height: 100%;
    right: 6px;
}
.select2-dropdown {
    border: 1.5px solid #E5E5E5;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.10);
    overflow: hidden;
}
.select2-results__option {
    padding: 8px 14px;
    font-size: 13px;
}
.select2-results__option--highlighted {
    background: #fef2f2 !important;
    color: #A71B47 !important;
}
.select2-search--dropdown .select2-search__field {
    border: 1px solid #E5E5E5;
    border-radius: 8px;
    padding: 6px 10px;
    font-size: 13px;
    outline: none;
}
</style>
