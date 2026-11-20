@php
    $appContent = getContent('app.content', true);
@endphp

@push('style')
<style>
.km-app{padding:80px 0;background:#fff;font-family:'Work Sans',sans-serif;}
.km-app__cx{width:min(calc(100% - 48px),1280px);margin-inline:auto;}
.km-app__card{display:grid;grid-template-columns:1fr 1fr;border-radius:32px;overflow:hidden;background:#1a4045;}
.km-app__img-wrap{position:relative;min-height:400px;}
.km-app__img-wrap img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;}
.km-app__content{display:flex;flex-direction:column;justify-content:center;padding:56px 40px;}
.km-app__eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.20em;color:#fff;margin-bottom:16px;}
.km-app__h2{margin:0;font-size:clamp(28px,2.8vw,44px);font-weight:600;line-height:1.15;letter-spacing:-1px;color:#fff;}
.km-app__h2 em{font-style:italic;color:#fff;}
.km-app__desc{margin:20px 0 0;max-width:400px;font-size:14px;line-height:1.75;color:rgba(255,255,255,.6);}
.km-app__btns{margin-top:32px;display:flex;flex-wrap:wrap;gap:12px;}
.km-app__store-btn{display:flex;align-items:center;gap:12px;border-radius:12px;border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.05);padding:12px 20px;text-decoration:none;transition:all .2s;}
.km-app__store-btn:hover{background:rgba(255,255,255,.10);border-color:rgba(255,255,255,.25);}
.km-app__store-btn svg{width:24px;height:24px;flex-shrink:0;}
.km-app__store-text p{margin:0;}
.km-app__store-text .small{font-size:10px;line-height:1;color:rgba(255,255,255,.5);}
.km-app__store-text .big{font-size:14px;font-weight:900;line-height:1.3;color:#fff;}
@media(max-width:1023px){.km-app__card{grid-template-columns:1fr;}.km-app__img-wrap{min-height:300px;}}
@media(max-width:639px){.km-app{padding:56px 0;}.km-app__content{padding:40px 28px;}}
</style>
@endpush

<section class="km-app" id="download-app">
    <div class="km-app__cx">
        <div class="km-app__card">
            <div class="km-app__img-wrap">
                <img src="{{ asset('assets/images/kinmoney/mobile-PJkhsLpI.jpeg') }}" alt="{{ gs('site_name') }} mobile app">
            </div>
            <div class="km-app__content">
                <span class="km-app__eyebrow">
                    <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="13" height="13"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                    Now on iOS and Android
                </span>
                <h2 class="km-app__h2">Send money home, <em>faster</em> than ever before.</h2>
                <p class="km-app__desc">The {!! siteNameHtml() !!} app puts international transfers in your pocket. Lock in live rates, track every delivery, and keep your family in the loop — all from one clean, simple screen.</p>
                <div class="km-app__btns">
                    <a href="{{ $appContent->data_values->app_store_url ?? '#' }}" target="_blank" rel="noopener" class="km-app__store-btn">
                        <svg viewBox="0 0 814 1000" class="fill-white" style="fill:#fff;"><path d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76 0-103.7 40.8-165.9 40.8s-105-42.3-150.3-109.2c-52.8-75.2-96.1-192.7-96.1-304.9 0-165.3 107.8-253 213.3-253 79.1 0 144.8 52.4 195.3 52.4 47.5 0 122.4-55.6 211.7-55.6zm-166.3-160.3c43.7-51.5 74-123.4 74-195.3 0-10-1-20.1-2.6-29.4-70.7 2.6-154.5 47.2-204.7 106.4-39.5 45.8-76.3 117.7-76.3 190.5 0 10.6 1.6 21.2 2.6 24.4 4.5.6 11.6 1.6 18.7 1.6 63.5 0 144.1-42.8 188.3-98.2z"/></svg>
                        <div class="km-app__store-text">
                            <p class="small">Download on the</p>
                            <p class="big">App Store</p>
                        </div>
                    </a>
                    <a href="{{ $appContent->data_values->play_store_url ?? '#' }}" target="_blank" rel="noopener" class="km-app__store-btn">
                        <svg viewBox="0 0 512 512" style="width:24px;height:24px;flex-shrink:0;"><path fill="#4285F4" d="M48 59.49v393a4.33 4.33 0 007.37 3.07L274 256 55.37 56.42A4.33 4.33 0 0048 59.49z"/><path fill="#34A853" d="M345.8 174L89.22 32.64l-.16-.09c-4.42-2.4-8.62 3.58-5 7.06L274 256z"/><path fill="#EA4335" d="M89.22 479.36c-3.64 3.48.56 9.46 5 7.06l.16-.09L345.8 338 274 256z"/><path fill="#FBBC05" d="M464 228.75l-74.3-41.94L345.8 174 274 256l71.8 82 114.2-62.08a4.33 4.33 0 000-7.17z"/></svg>
                        <div class="km-app__store-text">
                            <p class="small">GET IT ON</p>
                            <p class="big">Google Play</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
