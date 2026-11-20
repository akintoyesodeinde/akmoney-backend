@push('style')
<style>
.km-why{padding:80px 0;background:linear-gradient(135deg,#f9fafb 0%,#fff 100%);font-family:'Work Sans',sans-serif;}
.km-why__cx{width:min(calc(100% - 48px),1280px);margin-inline:auto;}
.km-why__eyebrow{display:inline-block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.20em;color:#A71B47;margin-bottom:24px;}
.km-why__hdr{display:grid;grid-template-columns:1fr 1fr;gap:32px;margin-bottom:56px;}
.km-why__h2{margin:0;font-size:clamp(28px,3.2vw,48px);font-weight:600;line-height:1.1;letter-spacing:-1px;color:#0F4650;}
.km-why__h2 em{font-style:italic;color:#A71B47;}
.km-why__desc{font-size:15px;line-height:1.75;color:#4b5563;margin:0;align-self:center;}
.km-why__grid{display:grid;grid-template-columns:repeat(5,1fr);gap:20px;}
.km-why__card{background:#fff;border-radius:10px;padding:24px 20px;box-shadow:0 2px 12px rgba(0,0,0,.06);transition:transform .25s ease,box-shadow .25s ease;}
.km-why__card:hover{transform:translateY(-4px);box-shadow:0 8px 28px rgba(0,0,0,.10);}
.km-why__icon{width:56px;height:56px;border-radius:12px;display:grid;place-items:center;margin-bottom:20px;}
.km-why__card h3{margin:0 0 8px;font-size:15px;font-weight:700;color:#111827;}
.km-why__card p{margin:0;font-size:13px;line-height:1.65;color:#6b7280;}
@media(max-width:1023px){.km-why__grid{grid-template-columns:repeat(3,1fr);}}
@media(max-width:767px){.km-why__hdr{grid-template-columns:1fr;}.km-why__grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:480px){.km-why__grid{grid-template-columns:1fr;}.km-why{padding:56px 0;}}
</style>
@endpush

<section class="km-why" id="why-choose-us">
    <div class="km-why__cx">
        <span class="km-why__eyebrow">— Why Choose Us</span>
        <div class="km-why__hdr">
            <div>
                <h2 class="km-why__h2">Cross-border transfers <em>made simple</em>.</h2>
            </div>
            <div>
                <p class="km-why__desc">Send money across borders with <strong style="color:#0F4650;">lightning-fast speed</strong>, <strong style="color:#A71B47;">industry-low fees</strong>, and <strong>unbeatable exchange rates</strong>. Built for simplicity, powered by <strong>24/7 customer support</strong> — {!! siteNameHtml() !!} makes global transfers feel effortless.</p>
            </div>
        </div>
        <div class="km-why__grid">
            <div class="km-why__card">
                <div class="km-why__icon" style="background:rgba(15,70,80,.10);">
                    <svg stroke="#0F4650" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3>Fast Transfer</h3>
                <p>Your money reaches in minutes, not days. Quick and reliable.</p>
            </div>
            <div class="km-why__card">
                <div class="km-why__icon" style="background:rgba(167,27,71,.10);">
                    <svg stroke="#A71B47" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <h3>Low Fees</h3>
                <p>We offer competitive rates and transparent fees. No hidden costs.</p>
            </div>
            <div class="km-why__card">
                <div class="km-why__icon" style="background:rgba(244,162,97,.15);">
                    <svg stroke="rgb(244,162,97)" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                </div>
                <h3>Great Rates</h3>
                <p>Get the best exchange rates in the market every time you send.</p>
            </div>
            <div class="km-why__card">
                <div class="km-why__icon" style="background:rgba(42,157,143,.10);">
                    <svg stroke="rgb(42,157,143)" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                </div>
                <h3>Ease of Use</h3>
                <p>Simple, intuitive interface designed for first-time and repeat users.</p>
            </div>
            <div class="km-why__card">
                <div class="km-why__icon" style="background:rgba(231,111,81,.10);">
                    <svg stroke="rgb(231,111,81)" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/></svg>
                </div>
                <h3>Customer Support</h3>
                <p>Our support team is here to help you anytime, anywhere.</p>
            </div>
        </div>
    </div>
</section>
