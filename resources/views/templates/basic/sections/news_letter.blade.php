@push('style')
<style>
.km-cta{padding:80px 0;background:#fff;font-family:'Work Sans',sans-serif;}
.km-cta__cx{width:min(calc(100% - 48px),1280px);margin-inline:auto;}
.km-cta__card{position:relative;overflow:hidden;border-radius:40px;padding:56px;background:linear-gradient(135deg,#1c6070 0%,#174b55 100%);box-shadow:0 25px 50px rgba(0,0,0,.25);}
.km-cta__glow-tr{position:absolute;top:0;right:0;width:256px;height:256px;border-radius:50%;background:#A71B47;opacity:.10;filter:blur(60px);}
.km-cta__glow-bl{position:absolute;bottom:0;left:0;width:384px;height:384px;border-radius:50%;background:#A71B47;opacity:.10;filter:blur(80px);}
.km-cta__dots{position:absolute;inset:0;opacity:.05;background-image:radial-gradient(circle at 1px 1px,#fff 1px,transparent 1px);background-size:40px 40px;}
.km-cta__inner{position:relative;z-index:10;display:flex;align-items:center;justify-content:space-between;gap:32px;flex-wrap:wrap;}
.km-cta__badge{display:inline-flex;align-items:center;gap:8px;padding:6px 12px;border-radius:999px;background:rgba(255,255,255,.15);margin-bottom:24px;}
.km-cta__badge svg{color:#fff;}
.km-cta__badge span{font-size:11px;font-weight:500;text-transform:uppercase;letter-spacing:.15em;color:#fff;}
.km-cta__h2{margin:0;font-size:clamp(26px,3vw,48px);font-weight:600;line-height:1.15;letter-spacing:-1px;color:#fff;}
.km-cta__h2 em{font-style:italic;color:#fff;}
.km-cta__desc{margin:20px 0 0;max-width:600px;font-size:15px;line-height:1.75;color:rgba(255,255,255,.75);}
.km-cta__btns{margin-top:32px;display:flex;flex-wrap:wrap;gap:16px;}
.km-cta__btn{display:inline-flex;align-items:center;gap:8px;padding:14px 24px;border-radius:12px;font-size:14px;font-weight:600;text-decoration:none;transition:all .3s;}
.km-cta__btn--crimson{background:#A71B47;color:#fff;}
.km-cta__btn--crimson:hover{gap:12px;opacity:.90;color:#fff;}
.km-cta__btn--ghost{background:rgba(255,255,255,.10);color:#fff;}
.km-cta__btn--ghost:hover{gap:12px;background:rgba(255,255,255,.15);color:#fff;}
.km-cta__trust{margin-top:32px;display:flex;align-items:center;gap:24px;flex-wrap:wrap;}
.km-cta__trust-item{display:flex;align-items:center;gap:8px;font-size:12px;color:rgba(255,255,255,.6);}
.km-cta__trust-item svg{color:#fff;}
.km-cta__features{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;min-width:280px;}
.km-cta__feat{text-align:center;padding:16px;border-radius:12px;background:rgba(255,255,255,.05);transition:transform .3s;}
.km-cta__feat:hover{transform:scale(1.04);}
.km-cta__feat-icon{width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;background:rgba(255,255,255,.15);}
.km-cta__feat-icon svg{color:#fff;}
.km-cta__feat-title{font-size:13px;font-weight:600;color:#fff;margin:0 0 4px;}
.km-cta__feat-desc{font-size:11px;color:rgba(255,255,255,.6);margin:0;}
@media(max-width:1023px){.km-cta__features{grid-template-columns:repeat(3,1fr);}.km-cta__inner{flex-direction:column;}.km-cta__features{min-width:unset;width:100%;}.km-cta{padding:56px 0;}}
@media(max-width:639px){.km-cta__card{padding:40px 28px;border-radius:28px;}.km-cta__features{grid-template-columns:1fr;}}
</style>
@endpush

<section class="km-cta" id="cta">
    <div class="km-cta__cx">
        <div class="km-cta__card">
            <div class="km-cta__glow-tr"></div>
            <div class="km-cta__glow-bl"></div>
            <div class="km-cta__dots"></div>
            <div class="km-cta__inner">
                <div>
                    <div class="km-cta__badge">
                        <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                        <span>Send love home</span>
                    </div>
                    <h2 class="km-cta__h2">Send money to your <em>loved ones</em> in minutes</h2>
                    <p class="km-cta__desc">Join over 50,000 families who trust {!! siteNameHtml() !!} to send money home safely, quickly, and affordably. Zero hidden fees, real exchange rates, and fast delivery to 10+ countries.</p>
                    <div class="km-cta__btns">
                        <a href="{{ route('user.register') }}" class="km-cta__btn km-cta__btn--crimson">
                            Start sending now
                            <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </a>
                        <a href="{{ url('/about') }}" class="km-cta__btn km-cta__btn--ghost">
                            See how it works
                            <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </a>
                    </div>
                    <div class="km-cta__trust">
                        <div class="km-cta__trust-item">
                            <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            Fully regulated
                        </div>
                        <div class="km-cta__trust-item">
                            <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            24/7 support
                        </div>
                        <div class="km-cta__trust-item">
                            <span style="font-size:13px;font-weight:700;color:#F5A623;">★★★★★</span>
                            <span>4.8/5 from 10k+ reviews</span>
                        </div>
                    </div>
                </div>
                <div class="km-cta__features">
                    <div class="km-cta__feat">
                        <div class="km-cta__feat-icon">
                            <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        </div>
                        <p class="km-cta__feat-title">Fast Delivery</p>
                        <p class="km-cta__feat-desc">Most transfers arrive within minutes</p>
                    </div>
                    <div class="km-cta__feat">
                        <div class="km-cta__feat-icon">
                            <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <p class="km-cta__feat-title">Low Fees</p>
                        <p class="km-cta__feat-desc">From as low as $0.99 per transfer</p>
                    </div>
                    <div class="km-cta__feat">
                        <div class="km-cta__feat-icon">
                            <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        </div>
                        <p class="km-cta__feat-title">Global Coverage</p>
                        <p class="km-cta__feat-desc">Send to 10+ countries worldwide</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
