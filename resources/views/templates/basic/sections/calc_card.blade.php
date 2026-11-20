<div class="sc-calc-card">
    <div class="sc-calc-body">
        <p class="sc-calc-sendnow">
            <span class="sc-calc-sendnow-line"></span>
            Send Money Now
        </p>

        <form action="{{ route('currency.calculator') }}" id="calculatorForm" method="POST" class="disableSubmission">
            @csrf

            {{-- You Send --}}
            <label class="sc-calc-label">You Send</label>
            <div class="sc-calc-input-row">
                <input class="sc-calc-num sending-amount" min="0" name="sending_amount"
                    placeholder="500" required step="any" type="number"
                    value="{{ @$sendingAmount ?: 500 }}">
                <div class="sc-curr-wrap" style="position:relative;flex-shrink:0;">
                    <select class="sending-countries" name="sending_country"
                        id="sending-country" required
                        style="position:absolute;opacity:0;top:0;left:0;right:0;bottom:0;width:100%;height:100%;cursor:pointer;z-index:10;">
                        @foreach ($sendingCountries ?? [] as $c)
                        <option value="{{ $c->id }}"
                            data-conversion_rates="{{ $c->conversionRates }}"
                            data-currency="{{ $c->currency }}"
                            data-dial_code="{{ $c->dial_code }}"
                            data-id="{{ $c->id }}"
                            data-image="{{ getImage(getFilePath('country').'/'.$c->image, getFileSize('country')) }}"
                            data-name="{{ $c->name }}"
                            data-rate="{{ $c->rate }}">{{ $c->currency }}</option>
                        @endforeach
                    </select>
                    <div class="sc-calc-curr-btn">
                        <span class="sc-calc-flag">
                            <img src="" alt="" class="sc-calc-flag-img" style="width:100%;height:100%;object-fit:cover;">
                        </span>
                        <span class="sc-calc-curr-code">CAD</span>
                        <svg width="11" height="11" fill="none" stroke="#9CA3AF" stroke-width="2.2" viewBox="0 0 24 24" style="margin-left:2px;"><polyline points="6 9 12 15 18 9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </div>
            </div>

            {{-- Rate row --}}
            <div class="sc-calc-rate-row">
                <span class="sc-calc-rate-text">
                    1 <span class="sending-currency">CAD</span> = <span class="exchange-rate">—</span> <span class="recipient-currency">NGN</span>
                </span>
                <span class="sc-calc-live-badge">
                    <span class="sc-calc-live-dot"></span>
                    Live Rate
                </span>
            </div>

            {{-- They Receive --}}
            <label class="sc-calc-label">They Receive</label>
            <div class="sc-calc-input-row" style="margin-bottom:18px;">
                <input class="sc-calc-num recipient-amount" min="0" name="recipient_amount"
                    placeholder="0" required step="any" type="number"
                    value="{{ @$recipientAmount }}">
                <div class="sc-curr-wrap" style="position:relative;flex-shrink:0;">
                    <select class="recipient-countries" id="recipient-country"
                        name="recipient_country"
                        style="position:absolute;opacity:0;top:0;left:0;right:0;bottom:0;width:100%;height:100%;cursor:pointer;z-index:10;">
                        @foreach ($receivingCountries ?? [] as $c)
                        <option value="{{ $c->id }}"
                            data-currency="{{ $c->currency }}"
                            data-delivery_methods="{{ $c->countryDeliveryMethods }}"
                            data-dial_code="{{ $c->dial_code }}"
                            data-fixed_charge="{{ $c->fixed_charge }}"
                            data-id="{{ $c->id }}"
                            data-image="{{ getImage(getFilePath('country').'/'.$c->image, getFileSize('country')) }}"
                            data-name="{{ $c->name }}"
                            data-percent_charge="{{ $c->percent_charge }}"
                            data-has_agent="{{ $c->has_agent }}"
                            data-rate="{{ $c->rate }}">{{ $c->currency }}</option>
                        @endforeach
                    </select>
                    <div class="sc-calc-curr-btn">
                        <span class="sc-calc-flag">
                            <img src="" alt="" class="sc-calc-flag-img" style="width:100%;height:100%;object-fit:cover;">
                        </span>
                        <span class="sc-calc-curr-code">NGN</span>
                        <svg width="11" height="11" fill="none" stroke="#9CA3AF" stroke-width="2.2" viewBox="0 0 24 24" style="margin-left:2px;"><polyline points="6 9 12 15 18 9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </div>
            </div>

            {{-- Delivery Method --}}
            <select class="sc-delivery-select" id="deliveryMethod" name="delivery_method" required style="margin-bottom:14px;">
                <option value="">Select Delivery Method</option>
            </select>

            {{-- Fee breakdown --}}
            <div class="sc-calc-fees-box charge-section alreal-charge-section">
                <div class="sc-calc-fee-row">
                    <span class="lbl">Transfer Fee</span>
                    <span class="val">+ <span class="charge-amount-text">0</span> <span class="sending-currency"></span></span>
                </div>
                <div class="sc-calc-fee-row">
                    <span class="lbl">Exchange Rate</span>
                    <span class="val"><span class="exchange-rate-display">—</span></span>
                </div>
                <div class="sc-calc-fee-divider">
                    <span class="lbl">Total to Pay</span>
                    <span class="val"><span class="final-amount-text">0</span> <span class="sending-currency"></span></span>
                </div>
            </div>

            <button type="submit" class="sc-calc-submit">
                Start Sending Money
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </form>

        <p class="sc-calc-secure">
            <svg width="11" height="11" fill="none" stroke="#D1D5DB" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4" stroke-linecap="round"/></svg>
            Secured by Canadian MSB License
        </p>
    </div>
</div>
