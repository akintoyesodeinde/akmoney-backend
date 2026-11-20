@extends($activeTemplate . 'layouts.master')
@section('activePage', 'send-money')
@section('content')

{{-- Search / Filter bar --}}
<form method="GET">
    <div class="flex items-center justify-between mt-2 flex-wrap gap-4">
        <input name="search" value="{{ request('search') }}" type="text"
            placeholder="Search by MTCN or transaction ID..."
            onchange="this.form.submit()"
            class="w-[300px] px-4 py-2 border border-brand-teal rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal focus:border-transparent bg-transparent">

        <div class="gap-3 flex items-center">
            <select name="payment_status" onchange="this.form.submit()"
                class="px-4 py-2 border border-brand-teal rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-teal focus:border-transparent bg-card-white text-text-primary">
                <option value="">All Status</option>
                <option {{ request('payment_status')=='0' ? 'selected' : '' }} value="0">Awaiting Payment</option>
                <option {{ request('payment_status')=='1' ? 'selected' : '' }} value="1">Paid</option>
                <option {{ request('payment_status')=='2' ? 'selected' : '' }} value="2">Pending</option>
                <option {{ request('payment_status')=='3' ? 'selected' : '' }} value="3">Rejected</option>
            </select>
            <a href="{{ route('user.send.money.now') }}"
               class="px-6 py-2.5 bg-brand-teal text-white rounded-lg text-sm font-semibold hover:bg-brand-gold transition-colors shadow-lg shadow-gray-400 flex items-center gap-2">
                <i class="ri-send-plane-line"></i> New Transfer
            </a>
        </div>
    </div>
</form>

<div class="mt-4 overflow-x-auto">
    {{-- Desktop Table --}}
    <table class="hidden md:table w-full text-left border-collapse">
        <thead>
            <tr class="border-t border-gray-200">
                <th class="text-left py-4 px-2 text-sm font-semibold text-brand-teal">Recipient</th>
                <th class="text-left py-4 px-2 text-sm font-semibold text-brand-teal">Send / Received Date</th>
                <th class="text-left py-4 px-2 text-sm font-semibold text-brand-teal">MTCN</th>
                <th class="text-left py-4 px-2 text-sm font-semibold text-brand-teal">Amount</th>
                <th class="text-left py-4 px-2 text-sm font-semibold text-brand-teal">Status</th>
                <th class="py-4 px-2 text-sm font-semibold text-brand-teal">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($transfers as $transfer)
                @php
                    $badge   = $transfer->statusBadge;
                    $details = $transfer->detail != null ? json_encode($transfer->detail) : null;
                @endphp
                <tr class="border-t border-gray-200 hover:bg-brand-teal/5 transition-colors relative">
                    <td class="py-4 px-2">
                        <div class="text-sm text-text-primary font-medium">{{ $transfer->recipient->name }}</div>
                        <div class="text-xs text-text-secondary">{{ @$transfer->recipientCountry->name }}</div>
                    </td>
                    <td class="py-4 px-2">
                        <div class="text-sm text-text-primary font-medium">{{ showDateTime($transfer->created_at, 'd M, y h:i a') }}</div>
                        <div class="text-xs text-text-secondary mt-1">
                            {{ $transfer->received_at ? showDateTime($transfer->received_at, 'd M, y h:i a') : 'N/A' }}
                        </div>
                    </td>
                    <td class="py-4 px-2 text-sm text-text-primary font-mono">{{ $transfer->mtcn_number }}</td>
                    <td class="py-4 px-2">
                        <div class="text-sm font-semibold text-text-primary">
                            {{ __($transfer->sending_currency) }}{{ showAmount($transfer->sending_amount, currencyFormat: false) }}
                        </div>
                        <div class="text-xs text-text-secondary mt-0.5">
                            → {{ showAmount($transfer->recipient_amount, currencyFormat: false) }} {{ __($transfer->recipient_currency) }}
                        </div>
                    </td>
                    <td class="py-2 md:py-4 px-2">
                        <span class="inline-flex items-center px-2 md:px-3 py-1 rounded-full text-xs font-medium {{ $badge['bg'] }} {{ $badge['textColor'] }}">
                            <span class="w-1 h-1 rounded-full mr-1 {{ $badge['dot'] }}"></span>
                            {{ $badge['text'] }}
                        </span>
                    </td>
                    <td class="py-4 px-2 relative">
                        <button class="action-btn text-text-secondary hover:text-brand-teal transition-colors">
                            <i class="ri-more-2-fill"></i>
                        </button>
                        <div class="action-dropdown absolute right-8 top-8 bg-card-white dark:bg-card-dark border border-border-gray rounded-lg shadow-lg py-2 z-50 min-w-[160px] hidden">
                            @php $btnData = 'data-address="'.e($transfer->recipient->address).'"
                                data-country="'.e(@$transfer->recipientCountry->name).'"
                                data-id="'.encrypt($transfer->id).'"
                                data-info="'.e($details).'"
                                data-service_form_data="'.e($transfer->service_form_data ? json_encode($transfer->service_form_data) : '').'"
                                data-mobile="'.e($transfer->recipient->mobile).'"
                                data-name="'.e($transfer->recipient->name).'"
                                data-payment_status="'.$transfer->payment_status.'"
                                data-payment_via="'.e($transfer->deposit ? @$transfer->deposit->gateway->name : __('Wallet Balance')).'"
                                data-recipient_amount="'.e(showAmount($transfer->recipient_amount, currencyFormat: false).' '.__($transfer->recipient_currency)).'"
                                data-send_money_amount="'.$transfer->sending_amount.'"
                                data-sending_currency="'.e(__($transfer->sending_currency)).'"
                                data-send_amount_in_base_currency="'.e(showAmount($transfer->base_currency_amount + $transfer->base_currency_charge, currencyFormat: false)).'"
                                data-total_payable_amount="'.e(showAmount($transfer->paidAmount(), currencyFormat: false)).'"
                                data-deposit="'.e(json_encode(['charge' => optional($transfer->deposit)->charge ?? 0])).'"
                                data-delivery_charge="'.e(showAmount($transfer->sending_charge, currencyFormat: false)).'"
                                data-including_charge="'.e(showAmount($transfer->sending_amount + $transfer->sending_charge, currencyFormat: false)).'"
                                data-status="'.$transfer->status.'"
                                data-mtcn_number="'.e($transfer->mtcn_number).'"
                                data-trx="'.e($transfer->trx).'"
                                data-conversion_rate="'.e(showAmount($transfer->conversion_rate, decimal: 8, exceptZeros: true, currencyFormat: false)).'"
                                data-base_currency_rate="'.e(showAmount($transfer->base_currency_rate, decimal: 8, exceptZeros: true, currencyFormat: false)).'"
                                data-recipient_currency="'.e($transfer->recipient_currency).'"
                                data-delivery_method="'.e($transfer->country_delivery_method_id ? __($transfer->countryDeliveryMethod->deliveryMethod->name) : __('Agent')).'"
                                data-service="'.e($transfer->service ? $transfer->service->name : '').'"
                                data-sent_at="'.e(showDateTime($transfer->created_at)).'"'; @endphp

                            <button class="view-details-btn w-full px-4 py-2 text-left text-sm text-text-primary hover:bg-bg-secondary flex items-center gap-2 cursor-pointer whitespace-nowrap"
                                {!! $btnData !!}>
                                <i class="ri-eye-line"></i>View Details
                            </button>
                            <button class="download-receipt-btn w-full px-4 py-2 text-left text-sm text-text-primary hover:bg-bg-secondary flex items-center gap-2 cursor-pointer whitespace-nowrap"
                                {!! $btnData !!}>
                                <i class="ri-download-line"></i>Download Receipt
                            </button>
                            <a href="{{ route('user.send.money.again', $transfer->id) }}"
                               class="w-full px-4 py-2 text-left text-sm text-text-primary hover:bg-bg-secondary flex items-center gap-2 cursor-pointer whitespace-nowrap">
                                <i class="ri-send-plane-line"></i>Send Again
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-12 text-center">
                        <div class="flex flex-col items-center justify-center gap-3">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="ri-inbox-line text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-text-primary">No Transfers Yet</h3>
                            <p class="text-sm text-text-secondary">Your send money history will appear here</p>
                            <a href="{{ route('user.send.money.now') }}"
                               class="mt-2 px-6 py-2.5 bg-brand-teal text-white rounded-lg text-sm font-semibold hover:bg-brand-gold transition-colors">
                                Start a Transfer
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Mobile Card View --}}
    <div class="md:hidden space-y-3">
        @forelse ($transfers as $transfer)
            @php
                $badge   = $transfer->statusBadge;
                $details = $transfer->detail != null ? json_encode($transfer->detail) : null;
            @endphp
            <div class="bg-card-white border border-border-gray rounded-2xl p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-full bg-brand-teal flex items-center justify-center flex-shrink-0 text-white text-sm font-bold">
                            {{ strtoupper(substr($transfer->recipient->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-text-primary truncate">{{ $transfer->recipient->name }}</p>
                            <p class="text-xs text-text-secondary">{{ @$transfer->recipientCountry->name }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium flex-shrink-0 {{ $badge['bg'] }} {{ $badge['textColor'] }}">
                        <span class="w-1.5 h-1.5 rounded-full mr-1 {{ $badge['dot'] }}"></span>
                        {{ $badge['text'] }}
                    </span>
                </div>
                <div class="flex items-center justify-between border-t border-border-gray pt-3">
                    <div>
                        <p class="text-xs text-text-secondary">{{ showDateTime($transfer->created_at, 'd M, y') }}</p>
                        <p class="text-xs font-mono text-text-muted mt-0.5">{{ $transfer->mtcn_number }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-text-primary">{{ __($transfer->sending_currency) }}{{ showAmount($transfer->sending_amount, currencyFormat: false) }}</p>
                        <p class="text-xs text-text-secondary">→ {{ showAmount($transfer->recipient_amount, currencyFormat: false) }} {{ __($transfer->recipient_currency) }}</p>
                    </div>
                </div>
                <div class="flex gap-2 mt-3">
                    <button class="view-details-btn flex-1 py-2 bg-brand-teal/10 text-brand-teal rounded-lg text-xs font-semibold hover:bg-brand-teal/20 transition-colors flex items-center justify-center gap-1"
                        data-address="{{ e($transfer->recipient->address) }}"
                        data-country="{{ e(@$transfer->recipientCountry->name) }}"
                        data-id="{{ encrypt($transfer->id) }}"
                        data-service_form_data="{{ e($transfer->service_form_data ? json_encode($transfer->service_form_data) : '') }}"
                        data-mobile="{{ e($transfer->recipient->mobile) }}"
                        data-name="{{ e($transfer->recipient->name) }}"
                        data-payment_status="{{ $transfer->payment_status }}"
                        data-payment_via="{{ e($transfer->deposit ? @$transfer->deposit->gateway->name : __('Wallet Balance')) }}"
                        data-recipient_amount="{{ e(showAmount($transfer->recipient_amount, currencyFormat: false).' '.__($transfer->recipient_currency)) }}"
                        data-send_money_amount="{{ $transfer->sending_amount }}"
                        data-sending_currency="{{ e(__($transfer->sending_currency)) }}"
                        data-send_amount_in_base_currency="{{ e(showAmount($transfer->base_currency_amount + $transfer->base_currency_charge, currencyFormat: false)) }}"
                        data-total_payable_amount="{{ e(showAmount($transfer->paidAmount(), currencyFormat: false)) }}"
                        data-deposit="{{ e(json_encode(['charge' => optional($transfer->deposit)->charge ?? 0])) }}"
                        data-delivery_charge="{{ e(showAmount($transfer->sending_charge, currencyFormat: false)) }}"
                        data-including_charge="{{ e(showAmount($transfer->sending_amount + $transfer->sending_charge, currencyFormat: false)) }}"
                        data-status="{{ $transfer->status }}"
                        data-mtcn_number="{{ e($transfer->mtcn_number) }}"
                        data-trx="{{ e($transfer->trx) }}"
                        data-conversion_rate="{{ e(showAmount($transfer->conversion_rate, decimal: 8, exceptZeros: true, currencyFormat: false)) }}"
                        data-base_currency_rate="{{ e(showAmount($transfer->base_currency_rate, decimal: 8, exceptZeros: true, currencyFormat: false)) }}"
                        data-recipient_currency="{{ e($transfer->recipient_currency) }}"
                        data-delivery_method="{{ e($transfer->country_delivery_method_id ? __($transfer->countryDeliveryMethod->deliveryMethod->name) : __('Agent')) }}"
                        data-service="{{ e($transfer->service ? $transfer->service->name : '') }}"
                        data-sent_at="{{ e(showDateTime($transfer->created_at)) }}">
                        <i class="ri-eye-line"></i> Details
                    </button>
                    <a href="{{ route('user.send.money.again', $transfer->id) }}"
                       class="flex-1 py-2 bg-bg-secondary text-text-primary rounded-lg text-xs font-semibold hover:bg-border-gray transition-colors flex items-center justify-center gap-1">
                        <i class="ri-send-plane-line"></i> Send Again
                    </a>
                </div>
            </div>
        @empty
            <div class="py-12 text-center">
                <div class="flex flex-col items-center justify-center gap-3">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="ri-inbox-line text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-text-primary">No Transfers Yet</h3>
                    <p class="text-sm text-text-secondary">Your send money history will appear here</p>
                </div>
            </div>
        @endforelse
    </div>

    @if ($transfers->hasPages())
        <div class="mt-6">{{ paginateLinks($transfers) }}</div>
    @endif
</div>

{{-- Details Modal --}}
<div id="detailsModal"
    style="display:none"
    class="fixed inset-0 bg-slate-900/40 backdrop-blur-md items-center justify-center z-50 p-4 transition-all duration-300">
    <div class="bg-card-white/95 dark:bg-card-dark/95 backdrop-blur-xl rounded-3xl max-w-md w-full shadow-[0_32px_64px_-15px_rgba(0,0,0,0.3)] border border-border-gray overflow-y-auto relative max-h-[90vh]">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-brand-gold to-transparent"></div>
        <div class="p-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.2em] text-brand-gold font-bold mb-1">Transfer Receipt</p>
                    <img src="{{ siteLogo() }}" alt="{{ gs('site_name') }}" style="max-height:28px;width:auto;object-fit:contain;">
                </div>
                <button onclick="closeDetailsModal()"
                    class="h-10 w-10 flex items-center justify-center rounded-full bg-bg-secondary text-text-secondary hover:bg-border-gray hover:text-text-primary transition-all">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>

            <div class="text-center mb-8 py-5 border-y border-border-gray">
                <p class="text-xs uppercase tracking-widest text-text-secondary mb-2">You Sent</p>
                <h1 id="modalTransactionAmount" class="text-4xl font-bold text-text-primary tabular-nums tracking-tight"></h1>
                <p id="modalRecipientGets" class="text-sm text-text-secondary mt-1"></p>
            </div>

            <div class="grid grid-cols-1 gap-y-4">
                {{-- Beneficiary / Destination --}}
                <p class="text-[10px] uppercase tracking-widest text-brand-gold font-bold mt-1 mb-1">Destination Details</p>
                <div class="flex justify-between items-center">
                    <span class="text-xs uppercase tracking-wider text-text-secondary">Beneficiary</span>
                    <span id="modalName" class="text-sm font-medium text-text-primary"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs uppercase tracking-wider text-text-secondary">Country</span>
                    <span id="modalCountry" class="text-sm font-medium text-text-primary"></span>
                </div>
                <div id="modalMobileRow" class="flex justify-between items-center hidden">
                    <span class="text-xs uppercase tracking-wider text-text-secondary">Mobile</span>
                    <span id="modalMobile" class="text-sm font-medium text-text-primary"></span>
                </div>
                <div id="modalAddressRow" class="flex justify-between items-center hidden">
                    <span class="text-xs uppercase tracking-wider text-text-secondary">Address</span>
                    <span id="modalAddress" class="text-sm font-medium text-text-primary text-right max-w-[60%]"></span>
                </div>

                {{-- Payment / Service --}}
                <p class="text-[10px] uppercase tracking-widest text-brand-gold font-bold mt-3 mb-1">Payment &amp; Service</p>
                <div class="flex justify-between items-center">
                    <span class="text-xs uppercase tracking-wider text-text-secondary">Delivery Method</span>
                    <span id="modalDelivery" class="text-sm font-medium text-text-primary"></span>
                </div>
                <div id="modalServiceRow" class="flex justify-between items-center hidden">
                    <span class="text-xs uppercase tracking-wider text-text-secondary">Service</span>
                    <span id="modalService" class="text-sm font-medium text-text-primary"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs uppercase tracking-wider text-text-secondary">Payment Via</span>
                    <span id="modalPaymentVia" class="text-sm font-medium text-text-primary"></span>
                </div>
                <div id="modalAccountDetailsSection" class="hidden">
                    <p class="text-[10px] uppercase tracking-widest text-brand-gold font-bold mt-3 mb-1">Account Details</p>
                    <div id="modalServiceFieldsWrap"></div>
                </div>

                {{-- Transaction info --}}
                <p class="text-[10px] uppercase tracking-widest text-brand-gold font-bold mt-3 mb-1">Transaction Info</p>
                <div class="flex justify-between items-center">
                    <span class="text-xs uppercase tracking-wider text-text-secondary">MTCN</span>
                    <span id="modalMtcn" class="text-sm font-mono text-text-secondary"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs uppercase tracking-wider text-text-secondary">Date</span>
                    <span id="modalDate" class="text-sm text-text-secondary"></span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-border-gray">
                    <span class="text-xs uppercase tracking-wider text-text-secondary">Fee</span>
                    <span id="modalFee" class="text-sm font-medium text-text-primary"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs uppercase tracking-wider text-text-secondary">Total Paid</span>
                    <span id="modalTotal" class="text-sm font-bold text-brand-teal"></span>
                </div>
                <div class="flex justify-between items-center pt-2">
                    <span class="text-xs uppercase tracking-wider text-text-secondary">Status</span>
                    <span id="modalStatus" class="text-[10px] uppercase tracking-widest font-bold px-3 py-1 rounded-full border"></span>
                </div>
            </div>

            <div class="flex flex-col gap-3 mt-8">
                <form id="makePaymentForm" action="{{ route('user.send.money.pay') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="id" id="makePaymentId">
                </form>
                <button id="makePaymentBtn" onclick="document.getElementById('makePaymentForm').submit()"
                    class="hidden w-full py-4 bg-brand-gold hover:bg-brand-gold/90 text-white rounded-xl transition-all font-bold text-sm flex items-center justify-center gap-2 shadow-lg shadow-brand-gold/20">
                    <i class="ri-secure-payment-line"></i> Complete Payment
                </button>
                <button class="download-receipt w-full py-4 bg-brand-teal hover:bg-brand-teal/90 text-white rounded-xl transition-all font-medium text-sm flex items-center justify-center gap-2 shadow-lg shadow-brand-teal/20">
                    <i class="ri-download-cloud-2-line"></i> Download Receipt
                </button>
                <button onclick="closeDetailsModal()"
                    class="w-full py-3 bg-transparent text-text-secondary hover:text-text-primary transition-all text-xs font-semibold uppercase tracking-widest">
                    Dismiss
                </button>
            </div>
        </div>
        <div class="absolute -bottom-4 -right-4 opacity-[0.04] pointer-events-none">
            <img src="{{ siteLogo() }}" alt="" style="width:180px;filter:grayscale(1);">
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    // ── Action dropdowns ──────────────────────────────────────────────────────
    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const dd = this.closest('td').querySelector('.action-dropdown');
            const isHidden = dd.classList.contains('hidden');
            document.querySelectorAll('.action-dropdown').forEach(d => d.classList.add('hidden'));
            if (isHidden) dd.classList.remove('hidden');
        });
    });
    document.addEventListener('click', () => {
        document.querySelectorAll('.action-dropdown').forEach(d => d.classList.add('hidden'));
    });

    // ── View Details ──────────────────────────────────────────────────────────
    document.querySelectorAll('.view-details-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openDetailsModal(this);
        });
    });

    function openDetailsModal(btn) {
        const d             = btn.dataset;
        const status        = parseInt(d.status, 10);
        const paymentStatus = parseInt(d.payment_status, 10);
        const deposit       = JSON.parse(d.deposit || '{}');
        const currency      = d.sending_currency;
        const amount        = parseFloat(d.send_money_amount || 0);
        const baseCur       = "{{ __(gs('cur_text')) }}";

        document.getElementById('modalTransactionAmount').textContent = currency + amount.toFixed(2);
        document.getElementById('modalRecipientGets').textContent     = '→ ' + d.recipient_amount;
        document.getElementById('modalName').textContent              = d.name;
        document.getElementById('modalCountry').textContent           = d.country || '—';
        document.getElementById('modalDelivery').textContent          = d.delivery_method;
        document.getElementById('modalPaymentVia').textContent        = d.payment_via;
        document.getElementById('modalMtcn').textContent              = '#' + d.mtcn_number;
        document.getElementById('modalDate').textContent              = d.sent_at;
        document.getElementById('modalFee').textContent               = d.delivery_charge + ' ' + currency;
        document.getElementById('modalTotal').textContent             = d.total_payable_amount + ' ' + baseCur;

        // Recipient mobile
        const mobileRow = document.getElementById('modalMobileRow');
        if (d.mobile) {
            document.getElementById('modalMobile').textContent = d.mobile;
            mobileRow.classList.remove('hidden');
        } else {
            mobileRow.classList.add('hidden');
        }

        // Recipient address
        const addressRow = document.getElementById('modalAddressRow');
        if (d.address) {
            document.getElementById('modalAddress').textContent = d.address;
            addressRow.classList.remove('hidden');
        } else {
            addressRow.classList.add('hidden');
        }

        // Service name
        const serviceRow = document.getElementById('modalServiceRow');
        if (d.service) {
            document.getElementById('modalService').textContent = d.service;
            serviceRow.classList.remove('hidden');
        } else {
            serviceRow.classList.add('hidden');
        }

        // Account Details (service form fields)
        const serviceWrap    = document.getElementById('modalServiceFieldsWrap');
        const accountSection = document.getElementById('modalAccountDetailsSection');
        serviceWrap.innerHTML = '';
        try {
            const info = d.service_form_data ? JSON.parse(d.service_form_data) : null;
            if (Array.isArray(info) && info.length) {
                let hasFields = false;
                info.forEach(function(field) {
                    if (!field.value || field.type === 'file') return;
                    const val = Array.isArray(field.value) ? field.value.join(', ') : field.value;
                    const row = document.createElement('div');
                    row.className = 'flex justify-between items-center';
                    row.innerHTML = '<span class="text-xs uppercase tracking-wider text-text-secondary">' + (field.name || field.label || '') + '</span>'
                                  + '<span class="text-sm font-medium text-text-primary">' + val + '</span>';
                    serviceWrap.appendChild(row);
                    hasFields = true;
                });
                accountSection.classList.toggle('hidden', !hasFields);
            } else {
                accountSection.classList.add('hidden');
            }
        } catch(e) { accountSection.classList.add('hidden'); }

        // Status — covers all payment_status and status combinations
        let label, cls;
        if (paymentStatus === 0) {
            label = 'Awaiting Payment'; cls = 'bg-amber-100 text-amber-700 border-amber-200';
        } else if (paymentStatus === 3) {
            label = 'Payment Failed';   cls = 'bg-red-100 text-red-700 border-red-200';
        } else if (status === 1) {
            label = 'Completed';        cls = 'bg-teal-100 text-teal-700 border-teal-200';
        } else if (status === 3) {
            label = 'Refunded';         cls = 'bg-purple-100 text-purple-700 border-purple-200';
        } else if (paymentStatus === 1) {
            label = 'Paid — Processing'; cls = 'bg-teal-100 text-teal-700 border-teal-200';
        } else if (paymentStatus === 2) {
            label = 'Payment Pending';   cls = 'bg-blue-100 text-blue-700 border-blue-200';
        } else {
            label = 'In Progress';       cls = 'bg-blue-100 text-blue-700 border-blue-200';
        }
        const statusEl = document.getElementById('modalStatus');
        statusEl.textContent = label;
        statusEl.className   = 'text-[10px] uppercase tracking-widest font-bold px-3 py-1 rounded-full border ' + cls;

        // Make Payment button — only show when payment has not been initiated
        const makePayBtn = document.getElementById('makePaymentBtn');
        const makePayId  = document.getElementById('makePaymentId');
        if (paymentStatus === 0) {
            makePayId.value = d.id;
            makePayBtn.classList.remove('hidden');
        } else {
            makePayBtn.classList.add('hidden');
        }

        const modal = document.getElementById('detailsModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeDetailsModal() {
        document.getElementById('detailsModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // ── Backdrop click closes modal ───────────────────────────────────────────
    document.getElementById('detailsModal').addEventListener('click', function(e) {
        if (e.target === this) closeDetailsModal();
    });

    // ── Download Receipt ──────────────────────────────────────────────────────
    document.querySelectorAll('.download-receipt, .download-receipt-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.dataset.name) openDetailsModal(this);
            const modal   = document.getElementById('detailsModal');
            const content = modal.querySelector('.p-8');
            modal.style.display = 'flex';
            requestAnimationFrame(() => {
                html2canvas(content, { scale: 2, useCORS: true }).then(canvas => {
                    const link = document.createElement('a');
                    link.href  = canvas.toDataURL('image/png');
                    link.download = 'receipt-' + (this.dataset.mtcn_number || Date.now()) + '.png';
                    link.click();
                });
            });
        });
    });

    // ── Sidebar / page init ───────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        window.initSidebar?.();
        const toggleBtn = document.getElementById('toggle-sidebar');
        if (toggleBtn) toggleBtn.addEventListener('click', () => window.toggleSidebar?.());
    });
</script>

@endsection
