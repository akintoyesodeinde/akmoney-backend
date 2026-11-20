<div class="{{ $class }} mb-0">
    <div class="wr-input-wrapper">
        <div class="wr-left-content">
            <label for="from">@lang('You send')</label>
            <input class="form-control form--control text--left sending-amount wr-input-field" min="0" id="from"
                name="sending_amount" placeholder="0.00" required step="any" type="number" value="{{ @$sendingAmount }}">
        </div>

        <div class="wr-currency-block">
             <select autocomplete="off" class="sending-countries country-picker select2" name="sending_country" id="sending-country" required>
                @foreach ($sendingCountries ?? [] as $sendingCountry)
                    <option value="{{ $sendingCountry->id }}" style="background-color: var(--primary-color)"
                        data-conversion_rates="{{ $sendingCountry->conversionRates }}"
                        data-currency="{{ $sendingCountry->currency }}"
                        data-dial_code="{{ $sendingCountry->dial_code }}" data-id="{{ $sendingCountry->id }}"
                        data-image="{{ getImage(getFilePath('country') . '/' . $sendingCountry->image, getFileSize('country')) }}"
                        data-name="{{ $sendingCountry->name }}" data-rate="{{ $sendingCountry->rate }}">
                        {{ $sendingCountry->currency }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    
    @if ($showLimit)
        <small class="limitMessage d-none text--danger">@lang('Limit exceeded')</small>
    @endif
</div>

<div class="wr-connector-area">
    <div class="wr-vertical-line"></div>
    <div class="wr-rate-badge">
        1 <span class="sending-currency">USD</span> = <span class="exchange-rate">...</span> <span class="recipient-currency">NGN</span>
    </div>
</div>

<div class="{{ $class }}">
    <div class="wr-input-wrapper">
        <div class="wr-left-content">
            <label for="to">@lang('They get')</label>
            <input class="form-control form--control text--left recipient-amount wr-input-field" min="0" id="to"
                name="recipient_amount" placeholder="0.00" required step="any" type="number"
                value="{{ @$recipientAmount }}">
        </div>

        <div class="wr-currency-block">
            <select class="form-control form--control country-picker recipient-countries select2" id="recipient-country"
                name="recipient_country">
                @foreach ($receivingCountries ?? [] as $receivingCountry)
                    <option value="{{ $receivingCountry->id }}" data-currency="{{ $receivingCountry->currency }}"
                        data-delivery_methods="{{ $receivingCountry->countryDeliveryMethods }}"
                        data-dial_code="{{ $receivingCountry->dial_code }}"
                        data-fixed_charge="{{ $receivingCountry->fixed_charge }}"
                        data-id="{{ $receivingCountry->id }}"
                        data-image="{{ getImage(getFilePath('country') . '/' . $receivingCountry->image, getFileSize('country')) }}"
                        data-name="{{ $receivingCountry->name }}"
                        data-percent_charge="{{ $receivingCountry->percent_charge }}"
                        data-has_agent="{{ $receivingCountry->has_agent }}" data-rate="{{ $receivingCountry->rate }}">
                        {{ $receivingCountry->currency }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>