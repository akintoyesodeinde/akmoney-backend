@php
    $corridorCountries = App\Models\Country::active()->receiving()->orderBy('name')->get();

    $staticCorridors = [
        ['code'=>'ph','name'=>'Philippines','currency'=>'PHP','rate'=>'1 CAD = 40.20 PHP'],
        ['code'=>'in','name'=>'India','currency'=>'INR','rate'=>'1 CAD = 61.12 INR'],
        ['code'=>'pk','name'=>'Pakistan','currency'=>'PKR','rate'=>'1 CAD = 205.45 PKR'],
        ['code'=>'bd','name'=>'Bangladesh','currency'=>'BDT','rate'=>'1 CAD = 81.50 BDT'],
        ['code'=>'np','name'=>'Nepal','currency'=>'NPR','rate'=>'1 CAD = 97.50 NPR'],
        ['code'=>'gh','name'=>'Ghana','currency'=>'GHS','rate'=>'1 CAD = 11.80 GHS'],
        ['code'=>'ng','name'=>'Nigeria','currency'=>'NGN','rate'=>'1 CAD = 1102.40 NGN'],
        ['code'=>'ke','name'=>'Kenya','currency'=>'KES','rate'=>'1 CAD = 95.40 KES'],
        ['code'=>'mx','name'=>'Mexico','currency'=>'MXN','rate'=>'1 CAD = 12.55 MXN'],
        ['code'=>'vn','name'=>'Vietnam','currency'=>'VND','rate'=>'1 CAD = 18640 VND'],
    ];
@endphp

@push('style')
<style>
.km-corr{padding:80px 0;background:#fff;font-family:'Work Sans',sans-serif;}
.km-corr__cx{width:min(calc(100% - 48px),1280px);margin-inline:auto;}
.km-corr__eyebrow{display:inline-block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.20em;color:#A71B47;margin-bottom:24px;}
.km-corr__hdr{display:grid;grid-template-columns:1fr 1fr;gap:32px;margin-bottom:48px;}
.km-corr__h2{margin:0;font-size:clamp(26px,3vw,44px);font-weight:700;line-height:1.1;letter-spacing:-1px;color:#0F4650;}
.km-corr__h2 em{font-style:italic;color:#A71B47;}
.km-corr__desc{font-size:15px;line-height:1.75;color:#4a5568;margin:0;align-self:center;}
.km-corr__card{background:#1a4045;border-radius:16px;padding:20px;margin-bottom:48px;}
.km-corr__grid{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:12px;}
.km-corr__country{display:flex;flex-direction:column;border-radius:12px;padding:12px;backdrop-filter:blur(8px);background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);transition:.3s;}
.km-corr__country:hover{background:rgba(255,255,255,.22);}
.km-corr__flag{width:40px;height:40px;border-radius:8px;object-fit:cover;margin-bottom:12px;box-shadow:0 2px 8px rgba(0,0,0,.2);}
.km-corr__cname{font-size:12px;font-weight:700;color:#fff;line-height:1.2;}
.km-corr__ccurr{font-size:11px;font-weight:500;color:rgba(255,255,255,.7);margin-top:2px;}
.km-corr__crate{font-size:10px;color:rgba(255,255,255,.6);margin-top:8px;line-height:1.4;}
.km-corr__cta-bar{background:rgba(15,70,80,.06);backdrop-filter:blur(8px);border:1px solid rgba(167,27,71,.15);border-radius:16px;padding:32px;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:24px;}
.km-corr__cta-left{display:flex;align-items:center;gap:12px;}
.km-corr__cta-left svg{color:#A71B47;flex-shrink:0;}
.km-corr__cta-title{font-size:18px;font-weight:700;color:#0F4650;margin:0 0 4px;}
.km-corr__cta-sub{font-size:14px;color:#4a5568;margin:0;}
.km-corr__cta-btns{display:flex;flex-wrap:wrap;gap:16px;}
.km-corr__btn{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;white-space:nowrap;transition:opacity .2s;}
.km-corr__btn:hover{opacity:.88;}
.km-corr__btn--crimson{background:#A71B47;color:#fff;}
.km-corr__btn--teal{background:#0F4650;color:#fff;}
@media(max-width:1023px){.km-corr__grid{grid-template-columns:repeat(3,1fr);}.km-corr__hdr{grid-template-columns:1fr;}}
@media(max-width:639px){.km-corr__grid{grid-template-columns:repeat(2,1fr);}.km-corr{padding:56px 0;}}
</style>
@endpush

<section class="km-corr" id="corridors">
    <div class="km-corr__cx">
        <span class="km-corr__eyebrow">— Supported corridors</span>
        <div class="km-corr__hdr">
            <div>
                <h2 class="km-corr__h2">Built for families, founders, and <em>frequent senders</em>.</h2>
            </div>
            <div>
                <p class="km-corr__desc">Send money to 10+ countries with competitive exchange rates, low fees, and fast delivery — all from one simple dashboard.</p>
            </div>
        </div>

        <div class="km-corr__card">
            @if($corridorCountries && count($corridorCountries) > 0)
                @php $chunks = $corridorCountries->chunk(5); @endphp
                @foreach($chunks as $row)
                <div class="km-corr__grid">
                    @foreach($row as $country)
                    <div class="km-corr__country">
                        <img src="https://flagcdn.com/w80/{{ strtolower($country->country_code ?? 'un') }}.png" alt="{{ $country->name }}" class="km-corr__flag" loading="lazy">
                        <span class="km-corr__cname">{{ $country->name }}</span>
                        <span class="km-corr__ccurr">{{ $country->currency ?? '' }}</span>
                    </div>
                    @endforeach
                </div>
                @endforeach
            @else
                @php
                    $rows = array_chunk($staticCorridors, 5);
                @endphp
                @foreach($rows as $row)
                <div class="km-corr__grid">
                    @foreach($row as $c)
                    <div class="km-corr__country">
                        <img src="https://flagcdn.com/w80/{{ $c['code'] }}.png" alt="{{ $c['name'] }}" class="km-corr__flag" loading="lazy">
                        <span class="km-corr__cname">{{ $c['name'] }}</span>
                        <span class="km-corr__ccurr">{{ $c['currency'] }}</span>
                        <span class="km-corr__crate">{{ $c['rate'] }}</span>
                    </div>
                    @endforeach
                </div>
                @endforeach
            @endif
        </div>

        <div class="km-corr__cta-bar">
            <div class="km-corr__cta-left">
                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="28" height="28" xmlns="http://www.w3.org/2000/svg" style="color:#A71B47;"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                <div>
                    <p class="km-corr__cta-title">More corridors coming soon</p>
                    <p class="km-corr__cta-sub">We're expanding. Sign up to get notified when your country goes live.</p>
                </div>
            </div>
            <div class="km-corr__cta-btns">
                <a href="{{ route('user.register') }}" class="km-corr__btn km-corr__btn--crimson">
                    Send Money Now
                    <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                </a>
                <a href="{{ url('/contact') }}" class="km-corr__btn km-corr__btn--teal">
                    Learn More
                    <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>
