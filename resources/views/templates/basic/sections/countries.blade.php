@php
    $receivingCountries = App\Models\Country::active()->receiving()->orderBy('name')->get();
@endphp

@push('style')
<style>
.km-countries { padding: 80px 0; background: #f8f3f1; font-family: 'Work Sans', sans-serif; }
.km-countries__cx { width: min(calc(100% - 48px), 1280px); margin-inline: auto; }
.km-countries__head { text-align: center; margin-bottom: 48px; }
.km-countries__eyebrow { display: inline-block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .20em; color: #A71B47; margin-bottom: 16px; }
.km-countries__h2 { margin: 0 0 14px; font-size: clamp(26px, 3vw, 44px); font-weight: 700; line-height: 1.15; letter-spacing: -1px; color: #0F4650; }
.km-countries__h2 em { font-style: italic; color: #A71B47; }
.km-countries__sub { margin: 0 auto; max-width: 560px; font-size: 15px; color: #4a5568; line-height: 1.7; }

/* Grid */
.km-countries__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 16px;
}

/* Card */
.km-countries__item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 20px 12px 16px;
    border-radius: 16px;
    background: #fff;
    border: 1px solid rgba(15, 70, 80, 0.08);
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    transition: transform .25s ease, box-shadow .25s ease;
    text-decoration: none;
    color: inherit;
}
.km-countries__item:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 28px rgba(15, 70, 80, 0.12);
}

/* Flag */
.km-countries__flag-wrap {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
    flex-shrink: 0;
    background: #e2e8f0;
}
.km-countries__flag-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.km-countries__name {
    font-size: 12px;
    font-weight: 700;
    color: #0F4650;
    text-align: center;
    line-height: 1.3;
}
.km-countries__currency {
    font-size: 11px;
    color: #A71B47;
    font-weight: 600;
    background: rgba(167, 27, 71, 0.08);
    padding: 2px 8px;
    border-radius: 999px;
}

/* CTA */
.km-countries__cta {
    margin-top: 48px;
    text-align: center;
}
.km-countries__cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 32px;
    border-radius: 10px;
    background: #0F4650;
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    text-decoration: none;
    transition: background .2s;
}
.km-countries__cta-btn:hover { background: #A71B47; color: #fff; }

@media (max-width: 640px) {
    .km-countries { padding: 56px 0; }
    .km-countries__grid { grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 12px; }
}
</style>
@endpush

<section class="km-countries" id="countries">
    <div class="km-countries__cx">

        <div class="km-countries__head">
            <span class="km-countries__eyebrow">— Where We Operate</span>
            <h2 class="km-countries__h2">Send money to <em>countries worldwide</em></h2>
            <p class="km-countries__sub">Fast, secure transfers to family and friends across the globe — with competitive rates and low fees.</p>
        </div>

        <div class="km-countries__grid">
            @forelse($receivingCountries as $country)
                @php
                    $countryCode = strtolower($country->code ?? '');
                    $flagUrl = $countryCode
                        ? 'https://flagcdn.com/w80/' . $countryCode . '.png'
                        : asset('assets/images/flag/blank.webp');
                @endphp
                <div class="km-countries__item">
                    <div class="km-countries__flag-wrap">
                        <img src="{{ $flagUrl }}"
                             alt="{{ $country->name }} flag"
                             loading="lazy"
                             onerror="this.src='{{ asset('assets/images/flag/blank.webp') }}';this.onerror=null;">
                    </div>
                    <span class="km-countries__name">{{ $country->name }}</span>
                    @if($country->currency)
                        <span class="km-countries__currency">{{ $country->currency }}</span>
                    @endif
                </div>
            @empty
                @php
                    $staticCountries = [
                        ['code'=>'ng','name'=>'Nigeria','currency'=>'NGN'],
                        ['code'=>'gh','name'=>'Ghana','currency'=>'GHS'],
                        ['code'=>'ke','name'=>'Kenya','currency'=>'KES'],
                        ['code'=>'ph','name'=>'Philippines','currency'=>'PHP'],
                        ['code'=>'in','name'=>'India','currency'=>'INR'],
                        ['code'=>'pk','name'=>'Pakistan','currency'=>'PKR'],
                        ['code'=>'bd','name'=>'Bangladesh','currency'=>'BDT'],
                        ['code'=>'np','name'=>'Nepal','currency'=>'NPR'],
                        ['code'=>'mx','name'=>'Mexico','currency'=>'MXN'],
                        ['code'=>'vn','name'=>'Vietnam','currency'=>'VND'],
                        ['code'=>'ug','name'=>'Uganda','currency'=>'UGX'],
                        ['code'=>'tz','name'=>'Tanzania','currency'=>'TZS'],
                    ];
                @endphp
                @foreach($staticCountries as $c)
                    <div class="km-countries__item">
                        <div class="km-countries__flag-wrap">
                            <img src="https://flagcdn.com/w80/{{ $c['code'] }}.png"
                                 alt="{{ $c['name'] }} flag"
                                 loading="lazy">
                        </div>
                        <span class="km-countries__name">{{ $c['name'] }}</span>
                        <span class="km-countries__currency">{{ $c['currency'] }}</span>
                    </div>
                @endforeach
            @endforelse
        </div>

        <div class="km-countries__cta">
            <a href="{{ route('user.register') }}" class="km-countries__cta-btn">
                Start Sending Money
                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
            </a>
        </div>

    </div>
</section>
