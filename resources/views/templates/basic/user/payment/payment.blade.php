@extends($activeTemplate . 'layouts.master')
@section('activePage', 'send-money')
@section('content')

@php
    $recipient  = $sendMoney->recipient;   // already cast to object
    $sendTotal  = $sendMoney->sending_amount + $sendMoney->sending_charge;
    $baseTotal  = $sendMoney->base_currency_amount + $sendMoney->base_currency_charge;
@endphp

<div class="max-w-3xl mx-auto py-6 md:py-8 px-2">

    {{-- Page heading --}}
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-text-primary">Choose Payment Method</h2>
        <p class="text-sm text-text-secondary mt-1">Select how you'd like to fund this transfer.</p>
    </div>

    <form action="{{ route('user.deposit.insert') }}" method="post" id="paymentForm">
        @csrf
        <input type="hidden" name="currency" id="gatewayCurrencyInput">
        <input type="hidden" name="amount" class="amount"
               value="{{ $baseTotal }}">

        <div class="space-y-4">

            {{-- ── Transfer summary ── --}}
            <div class="bg-card-white rounded-2xl border border-border-gray p-5">
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-widest mb-4">Transfer Summary</p>
                <div class="flex items-start justify-between gap-4">
                    {{-- Recipient --}}
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold text-white" style="background:#A71B47;">
                            {{ strtoupper(substr($recipient->name ?? 'R', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-text-primary text-sm truncate">{{ $recipient->name ?? 'Recipient' }}</p>
                            @if(!empty($recipient->mobile))
                            <p class="text-xs text-text-secondary">{{ $recipient->mobile }}</p>
                            @endif
                        </div>
                    </div>
                    {{-- Amount --}}
                    <div class="text-right flex-shrink-0">
                        <p class="text-xl font-extrabold text-text-primary">
                            {{ showAmount($sendMoney->sending_amount, currencyFormat: false) }}
                            <span class="text-sm font-semibold text-text-secondary">{{ $sendMoney->sending_currency }}</span>
                        </p>
                        <p class="text-xs text-text-secondary mt-0.5">
                            incl. {{ showAmount($sendMoney->sending_charge, currencyFormat: false) }} {{ $sendMoney->sending_currency }} fee
                        </p>
                    </div>
                </div>

                {{-- Summary rows --}}
                <div class="mt-4 pt-4 border-t border-border-gray space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-text-secondary">You send</span>
                        <span class="font-semibold text-text-primary">
                            {{ showAmount($sendTotal, currencyFormat: false) }} {{ $sendMoney->sending_currency }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-text-secondary">In {{ gs('cur_text') }}</span>
                        <span class="font-semibold text-text-primary">
                            {{ showAmount($baseTotal) }}
                        </span>
                    </div>
                    <div class="flex justify-between text-xs text-text-muted">
                        <span>Ref</span>
                        <span class="font-mono">{{ $sendMoney->trx }}</span>
                    </div>
                </div>
            </div>

            {{-- ── Gateway selection ── --}}
            <div class="bg-card-white rounded-2xl border border-border-gray p-5">
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-widest mb-4">Payment Method</p>

                <div class="space-y-2" id="gatewayList">
                    @foreach ($gatewayCurrency as $data)
                    @php $gatewayKey = 'gateway_' . $data->id; @endphp
                    <label for="{{ $gatewayKey }}"
                           class="gateway-option flex items-center justify-between gap-3 p-3.5 rounded-xl border border-border-gray cursor-pointer transition-all hover:border-brand-gold"
                           style="{{ $loop->first ? 'border-color:#A71B47;background:rgba(167,27,71,0.04)' : '' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 gateway-radio-dot"
                                 style="{{ $loop->first ? 'border-color:#A71B47' : 'border-color:#cbd5e1' }}">
                                <div class="w-2.5 h-2.5 rounded-full {{ $loop->first ? '' : 'hidden' }} gateway-radio-fill" style="background:#A71B47;"></div>
                            </div>
                            <div class="min-w-0">
                                <span class="font-semibold text-sm text-text-primary">{{ $data->name }}</span>
                                @if($data->currency !== gs('cur_text'))
                                    <span class="ml-1.5 text-xs text-text-muted font-normal">({{ $data->currency }})</span>
                                @endif
                            </div>
                        </div>
                        <img class="h-7 object-contain rounded"
                             src="{{ getImage(getFilePath('gateway') . '/' . $data->method->image) }}"
                             alt="{{ $data->name }}">
                        <input class="gateway-input sr-only" id="{{ $gatewayKey }}"
                               data-gateway='@json($data)'
                               data-min-amount="{{ showAmount($data->min_amount) }}"
                               data-max-amount="{{ showAmount($data->max_amount) }}"
                               type="radio" name="gateway" value="{{ $data->method_code }}"
                               data-currency="{{ $data->currency }}"
                               @if(old('gateway')) @checked(old('gateway') == $data->method_code) @else @checked($loop->first) @endif>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- ── Fee breakdown ── --}}
            <div class="bg-card-white rounded-2xl border border-border-gray p-5">
                <p class="text-[10px] font-bold text-text-muted uppercase tracking-widest mb-4">Fee Breakdown</p>

                <div class="space-y-2.5">
                    <div class="flex justify-between text-sm">
                        <span class="text-text-secondary">Limit</span>
                        <span class="font-medium text-text-primary gateway-limit">—</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-text-secondary">Processing Fee</span>
                        <span class="font-medium text-text-primary">
                            <span class="processing-fee">0.00</span> {{ gs('cur_text') }}
                        </span>
                    </div>

                    {{-- Conversion (hidden by default) --}}
                    <div class="gateway-conversion hidden flex justify-between text-sm">
                        <span class="text-text-secondary">Rate</span>
                        <span class="font-medium text-text-primary"></span>
                    </div>
                    <div class="conversion-currency hidden flex justify-between text-sm">
                        <span class="text-text-secondary">In <span class="gateway-currency"></span></span>
                        <span class="font-medium text-text-primary in-currency"></span>
                    </div>

                    <div class="crypto-message hidden p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-700">
                        Crypto conversion rate with <span class="gateway-currency font-semibold"></span> will be shown on the next step.
                    </div>

                    <div class="pt-2 border-t border-border-gray flex justify-between">
                        <span class="font-bold text-text-primary">Total to pay</span>
                        <span class="font-extrabold text-text-primary">
                            <span class="final-amount">{{ number_format($baseTotal, 2) }}</span>
                            {{ gs('cur_text') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- ── Amount limit warning ── --}}
            <div id="limitWarning" class="hidden p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 flex items-center gap-2">
                <i class="ri-error-warning-line flex-shrink-0"></i>
                <span id="limitWarningText"></span>
            </div>

            {{-- ── Confirm button ── --}}
            <button type="submit" id="confirmBtn"
                class="w-full bg-brand-gold text-white font-bold py-4 rounded-2xl text-sm hover:bg-brand-gold/90 transition-all shadow-lg shadow-brand-gold/20 flex items-center justify-center gap-2">
                <i class="ri-shield-check-line"></i>
                Confirm &amp; Pay {{ showAmount($baseTotal) }}
            </button>

            <p class="text-xs text-text-muted text-center">
                <i class="ri-lock-2-line"></i>
                Secured with 256-bit SSL encryption
            </p>
        </div>
    </form>
</div>

@push('script')
<script>
(function () {
    const amount     = parseFloat(document.querySelector('.amount')?.value || 0);
    const curText    = '{{ gs("cur_text") }}';
    const baseCur    = '{{ gs("cur_text") }}';
    let   gateway    = null;

    const labels     = document.querySelectorAll('.gateway-option');
    const inputs     = document.querySelectorAll('.gateway-input');
    const confirmBtn = document.getElementById('confirmBtn');

    // Custom radio UX
    function selectGateway(input, label) {
        // Reset all
        labels.forEach(l => {
            l.style.borderColor = '';
            l.style.background  = '';
            l.querySelector('.gateway-radio-dot').style.borderColor = '#cbd5e1';
            l.querySelector('.gateway-radio-fill').classList.add('hidden');
        });
        // Highlight selected
        label.style.borderColor = '#A71B47';
        label.style.background  = 'rgba(167,27,71,0.04)';
        label.querySelector('.gateway-radio-dot').style.borderColor = '#A71B47';
        label.querySelector('.gateway-radio-fill').classList.remove('hidden');

        input.checked = true;
        gateway = input.dataset.gateway ? JSON.parse(input.dataset.gateway) : null;
        calculate();
    }

    labels.forEach(label => {
        label.addEventListener('click', function () {
            const input = this.querySelector('.gateway-input');
            selectGateway(input, this);
        });
    });

    function calculate() {
        if (!gateway) return;

        const minA = parseFloat(gateway.min_amount);
        const maxA = parseFloat(gateway.max_amount);

        document.querySelector('.gateway-limit').textContent = minA.toFixed(2) + ' – ' + maxA.toFixed(2) + ' ' + curText;

        const pct       = parseFloat(gateway.percent_charge || 0);
        const fixed     = parseFloat(gateway.fixed_charge || 0);
        const pctCharge = amount / 100 * pct;
        const totalFee  = pctCharge + fixed;
        const total     = amount + totalFee;

        document.querySelector('.processing-fee').textContent = totalFee.toFixed(2);
        document.querySelector('.final-amount').textContent   = total.toFixed(2);
        document.getElementById('gatewayCurrencyInput').value = gateway.currency;
        document.querySelectorAll('.gateway-currency').forEach(el => el.textContent = gateway.currency);

        // Limit check
        const warn     = document.getElementById('limitWarning');
        const warnText = document.getElementById('limitWarningText');
        if (amount < minA || amount > maxA) {
            warn.classList.remove('hidden');
            warnText.textContent = `Amount ${amount.toFixed(2)} ${curText} is outside the allowed range (${minA.toFixed(2)} – ${maxA.toFixed(2)} ${curText}).`;
            confirmBtn.disabled = true;
            confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            warn.classList.add('hidden');
            confirmBtn.disabled = false;
            confirmBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }

        // Conversion
        const convWrap = document.querySelector('.gateway-conversion');
        const curWrap  = document.querySelector('.conversion-currency');
        const crypto   = document.querySelector('.crypto-message');

        if (gateway.currency !== baseCur && gateway.method?.crypto != 1) {
            convWrap.classList.remove('hidden');
            curWrap.classList.remove('hidden');
            const rate = parseFloat(gateway.rate || 1);
            convWrap.querySelector('span:last-child').textContent = `1 ${baseCur} = ${rate.toFixed(4)} ${gateway.currency}`;
            document.querySelector('.in-currency').textContent = (total * rate).toFixed(2);
        } else {
            convWrap.classList.add('hidden');
            curWrap.classList.add('hidden');
        }

        if (gateway.method?.crypto == 1) {
            crypto.classList.remove('hidden');
        } else {
            crypto.classList.add('hidden');
        }
    }

    // Init with first selected
    const firstChecked = document.querySelector('.gateway-input:checked');
    if (firstChecked) {
        const firstLabel = firstChecked.closest('.gateway-option');
        selectGateway(firstChecked, firstLabel);
    }
})();
</script>
@endpush

@endsection
