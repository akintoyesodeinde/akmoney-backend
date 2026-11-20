@push('style')
<style>
/* ── AkMoney Hero ── */
:root {
    --km-crimson: #A71B47;
    --km-teal: #0F4650;
    --km-dark: #071318;
}

.km-hero {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    overflow: hidden;
    background-color: #faf5ee;
    background-image: url('{{ asset('assets/images/kinmoney/photo_2026-05-31_22-18-21.jpg') }}');
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    margin-top: -78px; /* pull under fixed header */
}

.km-hero__overlay {
    display: none;
}

.km-hero__glow {
    position: absolute;
    right: -5rem;
    top: 6rem;
    width: 400px;
    height: 400px;
    border-radius: 50%;
    background: #A71B47;
    opacity: 0.10;
    filter: blur(100px);
    z-index: 1;
    pointer-events: none;
}

.km-hero__inner {
    position: relative;
    z-index: 2;
    width: min(calc(100% - 48px), 1200px);
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    align-items: center;
    gap: 48px;
    padding: calc(80px + 78px) 0 80px; /* 78px = header height */
    min-height: 100vh;
}

/* ── LEFT ── */
.km-hero__badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 7px 14px;
    border-radius: 999px;
    background: rgba(0,0,0,0.06);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(0,0,0,0.18);
    color: #000000;
    font-size: 13px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
}

.km-hero__h1 {
    margin: 22px 0 0;
    font-family: 'Poppins', sans-serif;
    font-size: clamp(28px, 3.2vw, 46px);
    font-weight: 600;
    line-height: 1.08;
    letter-spacing: -1px;
    color: #000000;
}

.km-hero__h1 em {
    font-style: normal;
    color: #A71B47;
}

.km-hero__sub {
    margin: 20px 0 0;
    font-family: 'Poppins', sans-serif;
    font-size: 18px;
    line-height: 1.75;
    color: #000000;
    max-width: 480px;
}

.km-hero__ctas {
    margin-top: 32px;
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.km-cta-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 26px;
    border-radius: 10px;
    background: #A71B47;
    color: #fff;
    font-family: 'Poppins', sans-serif;
    font-size: 15px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 8px 24px rgba(167,27,71,.25);
    transition: opacity .18s, gap .18s;
}
.km-cta-primary:hover { opacity: .88; gap: 12px; color: #fff; }

.km-cta-secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 26px;
    border-radius: 10px;
    border: 1.5px solid rgba(0,0,0,0.25);
    color: #000000;
    font-family: 'Poppins', sans-serif;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    transition: background .18s;
}
.km-cta-secondary:hover { background: rgba(0,0,0,0.06); color: #000000; }

/* Stats strip */
.km-hero__stats {
    margin-top: 40px;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    max-width: 460px;
}

.km-stat {
    padding: 12px 10px;
    border-radius: 12px;
    background: rgba(255,255,255,0.55);
    border: 1px solid rgba(0,0,0,0.10);
    backdrop-filter: blur(6px);
    text-align: center;
}

.km-stat__num {
    font-family: 'Poppins', sans-serif;
    font-size: 20px;
    font-weight: 700;
    color: #A71B47;
    line-height: 1;
}

.km-stat__label {
    font-family: 'Poppins', sans-serif;
    font-size: 10.5px;
    color: #000000;
    margin-top: 4px;
}

/* ── CALCULATOR CARD ── */
.km-calc-card {
    background: #fff;
    border: 1.5px solid #d0d0d0;
    border-radius: 4px;
    overflow: visible;
    max-width: 420px;
    margin: 0 auto;
    font-family: 'Poppins', sans-serif;
    box-shadow: 0 32px 80px rgba(0,0,0,.28);
}

.km-calc-header {
    padding: 15px 20px 13px;
    border-bottom: 1px solid #ebebeb;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.km-calc-live {
    display: flex;
    align-items: center;
    gap: 7px;
}

.km-calc-pulse {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--km-crimson);
    animation: kmPulse 2s ease infinite;
}

@keyframes kmPulse {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.30; }
}

.km-calc-live-label {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #888;
}

.km-calc-fintrac {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    color: #aaa;
    font-weight: 500;
}

.km-calc-body { padding: 20px; }

.km-calc-lbl {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: .10em;
    text-transform: uppercase;
    color: #aaa;
    margin-bottom: 8px;
}

.km-calc-row {
    background: #f7f6f3;
    border: 1px solid #e8e8e8;
    border-radius: 2px;
    padding: 13px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.km-calc-amount {
    font-family: 'DM Mono', monospace;
    font-size: 26px;
    font-weight: 500;
    background: transparent;
    border: none;
    outline: none;
    color: var(--km-teal);
    width: 150px;
    min-width: 0;
}

.km-calc-amount-recv {
    font-family: 'DM Mono', monospace;
    font-size: 26px;
    font-weight: 500;
    color: #A71B47;
}

.km-calc-curr-btn {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 6px 10px 6px 7px;
    background: #fff;
    border: 1px solid #d8d8d8;
    border-radius: 2px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
    color: var(--km-teal);
    letter-spacing: .02em;
    white-space: nowrap;
    font-family: 'Poppins', sans-serif;
    transition: background .12s;
}

.km-calc-curr-btn img { width: 22px; height: 15px; object-fit: cover; border-radius: 1px; }
.km-calc-curr-btn:hover { background: #f7f6f3; }

.km-calc-swap-row {
    display: flex;
    align-items: center;
    margin: 12px 0;
}

.km-calc-line {
    flex: 1;
    height: 1px;
    background: #ebebeb;
}

.km-calc-swap {
    width: 34px;
    height: 34px;
    margin: 0 10px;
    border: 1.5px solid #d0d0d0;
    border-radius: 2px;
    background: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #888;
    transition: background .12s, border-color .12s, color .12s;
}
.km-calc-swap:hover { background: #A71B47; border-color: #A71B47; color: #fff; }

.km-calc-rate-row {
    margin-top: 14px;
    padding: 11px 14px;
    background: #f7f6f3;
    border: 1px solid #e8e8e8;
    border-radius: 2px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.km-calc-rate-row span:first-child { font-size: 11px; color: #aaa; font-weight: 500; }
.km-calc-rate-row span:last-child  { font-family: 'DM Mono', monospace; font-size: 11px; font-weight: 500; color: #555; }

.km-calc-details { margin-top: 14px; }

.km-calc-detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 9px 0;
    border-bottom: 1px solid #ebebeb;
}

.km-calc-detail-row span:first-child { font-size: 12px; color: #888; }
.km-calc-detail-row span:last-child  { font-family: 'DM Mono', monospace; font-size: 12px; font-weight: 500; color: #444; }

.km-calc-total-row {
    margin-top: 12px;
    padding: 11px 14px;
    background: #f7f6f3;
    border: 1px solid #d0d0d0;
    border-radius: 2px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.km-calc-total-row span:first-child { font-size: 12px; font-weight: 600; color: var(--km-teal); }
.km-calc-total-row span:last-child  { font-family: 'DM Mono', monospace; font-size: 14px; font-weight: 600; color: var(--km-teal); }

.km-calc-cta {
    margin-top: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 15px 0;
    background: #A71B47;
    border: none;
    border-radius: 2px;
    color: #fff;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    text-decoration: none;
    cursor: pointer;
    transition: opacity .12s;
}
.km-calc-cta:hover { opacity: .88; color: #fff; }

.km-calc-footer {
    padding: 12px 20px;
    border-top: 1px solid #ebebeb;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.km-calc-footer span {
    font-size: 10px;
    color: #bbb;
    letter-spacing: .04em;
    text-transform: uppercase;
    font-weight: 500;
}

/* Currency dropdown */
.km-curr-wrap { position: relative; }
.km-curr-menu {
    position: absolute;
    right: 0;
    top: calc(100% + 5px);
    width: 165px;
    background: #fff;
    border: 1px solid #dce8f7;
    border-radius: 8px;
    box-shadow: 0 18px 45px rgba(15,23,42,.16);
    padding: 6px;
    z-index: 999;
    opacity: 0;
    visibility: hidden;
    transform: translateY(6px) scale(.98);
    transition: opacity .2s, transform .2s, visibility .2s;
    max-height: 220px;
    overflow-y: auto;
}
.km-curr-wrap.open .km-curr-menu { opacity: 1; visibility: visible; transform: translateY(0) scale(1); }

.km-curr-option {
    width: 100%;
    border: 0;
    background: transparent;
    border-radius: 6px;
    padding: 8px 10px;
    display: flex;
    align-items: center;
    gap: 9px;
    cursor: pointer;
    color: #1f2d43;
    font-size: 12px;
    font-weight: 700;
    text-align: left;
    transition: background .15s;
    font-family: 'Poppins', sans-serif;
}
.km-curr-option:hover, .km-curr-option.active { background: #f7f6f3; color: var(--km-teal); }
.km-curr-option img { width: 22px; height: 15px; object-fit: cover; border-radius: 1px; flex-shrink: 0; }
.km-curr-option .km-chk { margin-left: auto; font-size: 12px; color: var(--km-crimson); opacity: 0; }
.km-curr-option.active .km-chk { opacity: 1; }

/* Responsive */
@media(max-width: 1024px) {
    .km-hero__inner { gap: 32px; }
}

@media(max-width: 860px) {
    .km-hero__inner {
        grid-template-columns: 1fr;
        padding-top: calc(48px + 78px);
        padding-bottom: 56px;
        min-height: auto;
    }
    .km-calc-card { max-width: 100%; }
    .km-hero__glow { display: none; }
}

@media(max-width: 560px) {
    .km-hero__h1 { font-size: 38px; letter-spacing: -1.2px; }
    .km-hero__stats { grid-template-columns: repeat(2, 1fr); }
    .km-hero__ctas { flex-direction: column; }
    .km-cta-primary, .km-cta-secondary { justify-content: center; }
    .km-calc-amount { font-size: 22px; }
    .km-calc-amount-recv { font-size: 22px; }
}
</style>
@endpush

@php
    $data = session()->get('intended_info');
    $sendingAmount      = @$data['form_data']['sending_amount'];
    $recipientAmount    = @$data['form_data']['recipient_amount'];
    $deliveryMethodId   = @$data['form_data']['delivery_method'];
    $recipientCountryId = @$data['form_data']['recipient_country'];
    $sendingCountryId   = @$data['form_data']['sending_country'];
@endphp

<section class="km-hero">
    <div class="km-hero__overlay"></div>
    <div class="km-hero__glow" aria-hidden="true"></div>

    <div class="km-hero__inner">

        {{-- ── LEFT ── --}}
        <div class="km-hero__left">

            <span class="km-hero__badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Canadian MSB Licensed · FINTRAC Registered
            </span>

            <h1 class="km-hero__h1">
                Send money to your <em>loved ones</em> in minutes
            </h1>

            <p class="km-hero__sub">
                Transfer from Canada to Africa, the Middle East, and the USA with competitive rates, zero hidden fees, and full FINTRAC compliance.
            </p>

            <div class="km-hero__ctas">
                <a href="{{ route('user.register') }}" class="km-cta-primary">
                    Send Money Now
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
                <a href="#how-it-works" class="km-cta-secondary">How It Works</a>
            </div>

            <div class="km-hero__stats">
                <div class="km-stat">
                    <div class="km-stat__num">50K+</div>
                    <div class="km-stat__label">Customers</div>
                </div>
                <div class="km-stat">
                    <div class="km-stat__num">7</div>
                    <div class="km-stat__label">Countries</div>
                </div>
                <div class="km-stat">
                    <div class="km-stat__num">24h</div>
                    <div class="km-stat__label">Avg. Delivery</div>
                </div>
                <div class="km-stat">
                    <div class="km-stat__num">99.9%</div>
                    <div class="km-stat__label">Uptime</div>
                </div>
            </div>

        </div>

        {{-- ── CALCULATOR CARD ── --}}
        <div class="km-calc-card">

            <div class="km-calc-header">
                <div class="km-calc-live">
                    <span class="km-calc-pulse"></span>
                    <span class="km-calc-live-label">Live Rate</span>
                </div>
                <div class="km-calc-fintrac">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#A71B47" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    FINTRAC Secured
                </div>
            </div>

            <form action="{{ route('currency.calculator') }}" method="POST" id="kmCalcForm" class="disableSubmission">
                @csrf
                <input type="hidden" name="sending_country"   id="kmHSendCountry"   value="{{ $sendingCountryId   ?? (@$sendingCountries->first()->id  ?? '') }}">
                <input type="hidden" name="recipient_country" id="kmHRecvCountry"   value="{{ $recipientCountryId ?? (@$receivingCountries->first()->id ?? '') }}">
                <input type="hidden" name="delivery_method"   id="kmHDeliveryMethod" value="{{ $deliveryMethodId ?? '' }}">

                <div class="km-calc-body">

                    <div class="km-calc-lbl">You send</div>
                    <div class="km-calc-row">
                        <input class="km-calc-amount" type="number" name="sending_amount" id="kmSendAmt"
                            value="{{ $sendingAmount ?: 1000 }}" min="0" step="any"
                            placeholder="1000" aria-label="Amount to send">
                        <div class="km-curr-wrap" id="kmSendCurrWrap">
                            <button type="button" class="km-calc-curr-btn" onclick="kmToggle('kmSendCurrWrap')" aria-label="Select send currency">
                                <img src="" alt="" id="kmSendFlag">
                                <span id="kmSendCode">CAD</span>
                                <span style="font-size:11px;color:#aaa;">▾</span>
                            </button>
                            <div class="km-curr-menu" id="kmSendMenu">
                                @foreach($sendingCountries ?? [] as $c)
                                <button type="button"
                                    class="km-curr-option{{ $loop->first ? ' active' : '' }}"
                                    data-id="{{ $c->id }}"
                                    onclick="kmSelect('send', {{ $c->id }}, '{{ $c->currency }}', '{{ getImage(getFilePath('country').'/'.$c->image, getFileSize('country')) }}', {{ (float)$c->rate }}, {{ json_encode($c->conversionRates) }})">
                                    <img src="{{ getImage(getFilePath('country').'/'.$c->image, getFileSize('country')) }}" alt="{{ $c->currency }}">
                                    {{ $c->currency }}
                                    <span class="km-chk">✓</span>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="km-calc-swap-row">
                        <div class="km-calc-line"></div>
                        <button type="button" class="km-calc-swap" onclick="kmSwapCurrencies()" aria-label="Swap currencies">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                        </button>
                        <div class="km-calc-line"></div>
                    </div>

                    <div class="km-calc-lbl">Recipient gets</div>
                    <div class="km-calc-row">
                        <span class="km-calc-amount-recv" id="kmRecvAmt">{{ $recipientAmount ?? '0.00' }}</span>
                        <div class="km-curr-wrap" id="kmRecvCurrWrap">
                            <button type="button" class="km-calc-curr-btn" onclick="kmToggle('kmRecvCurrWrap')" aria-label="Select receive currency">
                                <img src="" alt="" id="kmRecvFlag">
                                <span id="kmRecvCode">NGN</span>
                                <span style="font-size:11px;color:#aaa;">▾</span>
                            </button>
                            <div class="km-curr-menu" id="kmRecvMenu">
                                @foreach($receivingCountries ?? [] as $c)
                                <button type="button"
                                    class="km-curr-option{{ $loop->first ? ' active' : '' }}"
                                    data-id="{{ $c->id }}"
                                    onclick="kmSelect('recv', {{ $c->id }}, '{{ $c->currency }}', '{{ getImage(getFilePath('country').'/'.$c->image, getFileSize('country')) }}', {{ (float)$c->rate }}, {{ json_encode($c->countryDeliveryMethods) }})">
                                    <img src="{{ getImage(getFilePath('country').'/'.$c->image, getFileSize('country')) }}" alt="{{ $c->currency }}">
                                    {{ $c->currency }}
                                    <span class="km-chk">✓</span>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="km-calc-rate-row">
                        <span>Exchange rate</span>
                        <span id="kmRateText" style="font-family:'DM Mono',monospace;">—</span>
                    </div>

                    <div class="km-calc-details">
                        <div class="km-calc-detail-row">
                            <span>Transfer fee</span>
                            <span id="kmFeeDisplay" style="font-family:'DM Mono',monospace;">—</span>
                        </div>
                        <div class="km-calc-detail-row">
                            <span>Delivery time</span>
                            <span style="font-family:'DM Mono',monospace;">≈ 24 hrs</span>
                        </div>
                    </div>

                    <div class="km-calc-total-row">
                        <span>Total to pay</span>
                        <span id="kmTotalDisplay" style="font-family:'DM Mono',monospace;">—</span>
                    </div>

                    <input type="hidden" id="kmDeliveryHidden" name="delivery_method_id">

                    <a href="{{ route('user.register') }}" class="km-calc-cta">
                        Start Transfer
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>

                </div>{{-- end .km-calc-body --}}
            </form>

            <div class="km-calc-footer">
                <span>Canadian MSB Licensed · Regulated</span>
            </div>

        </div>{{-- end .km-calc-card --}}

    </div>{{-- end .km-hero__inner --}}
</section>

@push('script')
<script>
(function(){
    var snd = { id: null, currency: 'CAD', image: '', rate: 1, convRates: [] };
    var rcv = { id: null, currency: 'NGN', image: '', rate: 1, methods: [] };

    function kmToggle(wrapId) {
        var wrap = document.getElementById(wrapId);
        var open = wrap.classList.contains('open');
        document.querySelectorAll('.km-curr-wrap.open').forEach(function(el){ el.classList.remove('open'); });
        if (!open) wrap.classList.add('open');
    }
    window.kmToggle = kmToggle;

    function kmSelect(side, id, currency, image, rate, extra) {
        if (side === 'send') {
            snd = { id: id, currency: currency, image: image, rate: rate, convRates: extra || [] };
            document.getElementById('kmSendFlag').src = image;
            document.getElementById('kmSendFlag').alt = currency;
            document.getElementById('kmSendCode').textContent = currency;
            document.getElementById('kmHSendCountry').value = id;
            document.querySelectorAll('#kmSendMenu .km-curr-option').forEach(function(el){
                el.classList.toggle('active', parseInt(el.dataset.id) === id);
            });
            document.getElementById('kmSendCurrWrap').classList.remove('open');
        } else {
            rcv = { id: id, currency: currency, image: image, rate: rate, methods: extra || [] };
            document.getElementById('kmRecvFlag').src = image;
            document.getElementById('kmRecvFlag').alt = currency;
            document.getElementById('kmRecvCode').textContent = currency;
            document.getElementById('kmHRecvCountry').value = id;
            document.querySelectorAll('#kmRecvMenu .km-curr-option').forEach(function(el){
                el.classList.toggle('active', parseInt(el.dataset.id) === id);
            });
            document.getElementById('kmRecvCurrWrap').classList.remove('open');
            kmPopulateMethods();
        }
        kmUpdate();
    }
    window.kmSelect = kmSelect;

    function kmSwapCurrencies() {
        var tmp = { id: snd.id, currency: snd.currency, image: snd.image, rate: snd.rate };
        // visual only swap of displayed currencies
        var tempCode = document.getElementById('kmSendCode').textContent;
        var tempFlag = document.getElementById('kmSendFlag').src;
        document.getElementById('kmSendCode').textContent = document.getElementById('kmRecvCode').textContent;
        document.getElementById('kmSendFlag').src = document.getElementById('kmRecvFlag').src;
        document.getElementById('kmRecvCode').textContent = tempCode;
        document.getElementById('kmRecvFlag').src = tempFlag;
        kmUpdate();
    }
    window.kmSwapCurrencies = kmSwapCurrencies;

    function kmGetRate() {
        if (snd.convRates && snd.convRates.length) {
            var sp = snd.convRates.filter(function(r){ return r.to_country == rcv.id; });
            if (sp.length) return parseFloat(sp[0].rate);
        }
        if (snd.rate > 0 && rcv.rate > 0) return rcv.rate / snd.rate;
        return 1;
    }

    function kmPopulateMethods() {
        var methods = rcv.methods || [];
        // pick first available method fee for display
        var firstMethod = methods.find(function(m){ return m && m.delivery_method; });
        if (firstMethod) {
            var fc = firstMethod.charge ? (firstMethod.charge.fixed_charge || 0) : 0;
            document.getElementById('kmHDeliveryMethod').value = firstMethod.delivery_method.id;
            document.getElementById('kmFeeDisplay').textContent = '+ ' + parseFloat(fc).toFixed(2) + ' ' + snd.currency;
        } else {
            document.getElementById('kmFeeDisplay').textContent = '—';
        }
    }

    function kmUpdate() {
        var rate    = kmGetRate();
        var sendAmt = parseFloat(document.getElementById('kmSendAmt').value) || 0;
        var recvAmt = sendAmt > 0 && rate > 0 ? sendAmt * rate : 0;

        document.getElementById('kmRateText').textContent =
            '1 ' + snd.currency + ' = ' + rate.toFixed(2) + ' ' + rcv.currency;

        if (recvAmt > 0) {
            document.getElementById('kmRecvAmt').textContent =
                rcv.currency === 'NGN'
                    ? '₦' + recvAmt.toLocaleString('en-NG', {minimumFractionDigits:2, maximumFractionDigits:2})
                    : recvAmt.toFixed(2);
        } else {
            document.getElementById('kmRecvAmt').textContent = '—';
        }

        // total
        var fee = 0;
        var feeEl = document.getElementById('kmFeeDisplay').textContent;
        var feeMatch = feeEl.match(/[\d.]+/);
        if (feeMatch) fee = parseFloat(feeMatch[0]);
        var total = sendAmt + fee;
        document.getElementById('kmTotalDisplay').textContent =
            sendAmt > 0 ? total.toFixed(2) + ' ' + snd.currency : '—';
    }

    // Close dropdowns on outside click
    document.addEventListener('click', function(e){
        if (!e.target.closest('.km-curr-wrap')) {
            document.querySelectorAll('.km-curr-wrap.open').forEach(function(el){ el.classList.remove('open'); });
        }
    });

    document.getElementById('kmSendAmt').addEventListener('input', kmUpdate);

    // Init with first options
    window.addEventListener('DOMContentLoaded', function(){
        var fs = document.querySelector('#kmSendMenu .km-curr-option');
        var fr = document.querySelector('#kmRecvMenu .km-curr-option');
        if (fs) fs.click();
        if (fr) fr.click();
    });
})();
</script>
@endpush
