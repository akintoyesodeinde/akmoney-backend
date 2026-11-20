@php
    $socialIcons = getContent('social_icon.element', false, null, true);
@endphp

@push('style')
<style>
/* ── Footer ── */
.ft-wrap {
    background: #1c6070;
    font-family: 'Work Sans', sans-serif;
    position: relative;
    overflow: hidden;
}

/* ── Pre-footer CTA strip ── */
.ft-cta {
    position: relative;
    z-index: 1;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    padding: 40px 48px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    max-width: 1280px;
    margin: 0 auto;
    box-sizing: border-box;
}
@media(max-width:767px) {
    .ft-cta { flex-direction: column; align-items: flex-start; padding: 32px 20px; gap: 20px; }
}
.ft-cta-title {
    font-size: clamp(22px, 3vw, 32px);
    font-weight: 800;
    color: #fff;
    letter-spacing: -0.03em;
    margin: 0 0 6px;
    line-height: 1.15;
}
.ft-cta-title span { color: #fff; }
.ft-cta-sub {
    font-size: 14px;
    color: rgba(255,255,255,0.5);
    margin: 0;
}
.ft-cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #A71B47;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    padding: 14px 28px;
    border-radius: 10px;
    text-decoration: none;
    white-space: nowrap;
    flex-shrink: 0;
    transition: background 0.2s;
    font-family: 'Work Sans', sans-serif;
    box-shadow: 0 6px 20px rgba(167,27,71,.30);
}
.ft-cta-btn:hover { background: #8a1539; color: #fff; }
@media(max-width:767px) { .ft-cta-btn { width: 100%; justify-content: center; } }

/* ── Footer main grid ── */
.ft-main {
    position: relative;
    z-index: 1;
    max-width: 1280px;
    margin: 0 auto;
    padding: 56px 48px 48px;
    display: grid;
    grid-template-columns: 1.6fr 1fr 1fr 1fr 1fr;
    gap: 40px;
    box-sizing: border-box;
}
@media(max-width:1023px) {
    .ft-main { grid-template-columns: 1fr 1fr 1fr; padding: 48px 32px; }
    .ft-brand { grid-column: 1 / -1; }
}
@media(max-width:640px) {
    .ft-main { grid-template-columns: 1fr 1fr; padding: 40px 20px; gap: 32px; }
    .ft-brand { grid-column: 1 / -1; }
}

/* Brand column */
.ft-logo {
    display: inline-flex;
    align-items: center;
    text-decoration: none;
    font-family: 'Work Sans', sans-serif;
    font-size: 22px;
    font-weight: 800;
    color: #fff;
    letter-spacing: -0.02em;
    margin-bottom: 16px;
}
.ft-logo span { color: #fff; }
.ft-brand-desc {
    font-size: 13px;
    color: rgba(255,255,255,0.45);
    line-height: 1.8;
    margin: 0 0 24px;
    max-width: 240px;
}
.ft-store-btns {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.ft-store-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: transparent;
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 8px;
    padding: 8px 14px;
    color: #fff;
    text-decoration: none;
    transition: border-color 0.2s, background 0.2s;
    font-family: 'Work Sans', sans-serif;
}
.ft-store-btn:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.05); color: #fff; }
.ft-store-btn-text { display: flex; flex-direction: column; }
.ft-store-btn-sub  { font-size: 9px;  color: rgba(255,255,255,0.5); line-height: 1; }
.ft-store-btn-name { font-size: 12px; font-weight: 700; color: #fff; line-height: 1.3; }

/* Nav columns */
.ft-col-title {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.9);
    margin-bottom: 20px;
}
.ft-col ul {
    list-style: none;
    margin: 0; padding: 0;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.ft-col ul li a {
    font-size: 13px;
    color: rgba(255,255,255,0.5);
    text-decoration: none;
    transition: color 0.2s;
}
.ft-col ul li a:hover { color: #fff; }

/* ── Bottom bar ── */
.ft-bottom {
    position: relative;
    z-index: 1;
    border-top: 1px solid rgba(255,255,255,0.08);
    padding: 20px 48px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    max-width: 1280px;
    margin: 0 auto;
    box-sizing: border-box;
}
@media(max-width:767px) {
    .ft-bottom { flex-direction: column; align-items: flex-start; padding: 20px; gap: 10px; }
}
.ft-copy {
    font-size: 12px;
    color: rgba(255,255,255,0.35);
    margin: 0;
}
.ft-copy a { color: rgba(255,255,255,0.5); text-decoration: none; }
.ft-copy a:hover { color: #fff; }
.ft-status {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: rgba(255,255,255,0.45);
    white-space: nowrap;
}
.ft-status-dot {
    width: 7px; height: 7px;
    background: #22C55E;
    border-radius: 50%;
    flex-shrink: 0;
}
</style>
@endpush

<div class="ft-wrap">

    {{-- Watermark --}}
    <div style="pointer-events:none;position:absolute;inset:0;display:flex;align-items:center;justify-content:center;overflow:hidden;z-index:0;">
        <span style="select:none;font-size:clamp(80px,14vw,220px);font-weight:900;letter-spacing:0.2em;color:rgba(255,255,255,0.025);white-space:nowrap;user-select:none;">KINMONEY</span>
    </div>
    {{-- Cyan glow blob --}}
    <div style="pointer-events:none;position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);width:500px;height:500px;border-radius:50%;background:rgba(34,211,238,0.07);filter:blur(80px);z-index:0;"></div>

    {{-- ── Pre-footer CTA strip ── --}}
    <div class="ft-cta">
        <div>
            <p class="ft-cta-title">Send money in <span>minutes.</span></p>
            <p class="ft-cta-sub">Regulated. Transparent. Built for the diaspora.</p>
        </div>
        <a href="{{ route('user.register') }}" class="ft-cta-btn">
            Get Started Free
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
    </div>

    {{-- ── Footer main grid ── --}}
    <div class="ft-main">

        {{-- Brand --}}
        <div class="ft-brand">
            <a href="{{ url('/') }}" class="ft-logo">
                <img src="{{ siteLogo('dark') }}" alt="{{ gs('site_name') }}" style="max-height:36px;width:auto;max-width:160px;object-fit:contain;">
            </a>
            <p class="ft-brand-desc">Canada's regulated cross-border remittance platform. Send money to Africa, Middle East, and the USA with confidence.</p>
            <div class="ft-store-btns">
                <a href="{{ gs('play_store') ?? '#' }}" class="ft-store-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" opacity=".85"><path d="M3.18 23.76c.3.17.64.24.99.2l12.6-11.62-3.18-3.18L3.18 23.76zM.5 1.4C.19 1.75 0 2.28 0 2.96v18.08c0 .68.19 1.21.5 1.56l.08.08 10.13-10.13v-.24L.58 1.32.5 1.4zM20.1 10.5l-2.9-1.68-3.56 3.56 3.56 3.56 2.92-1.68c.83-.48.83-1.28-.02-1.76zM4.17.24l12.6 11.62-3.18 3.18L4.17 2.46c-.3-.17-.99-.2-.99.2l.99-.42z"/></svg>
                    <div class="ft-store-btn-text">
                        <span class="ft-store-btn-sub">Get it on</span>
                        <span class="ft-store-btn-name">Google Play</span>
                    </div>
                </a>
                <a href="{{ gs('app_store') ?? '#' }}" class="ft-store-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" opacity=".85"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                    <div class="ft-store-btn-text">
                        <span class="ft-store-btn-sub">Download on the</span>
                        <span class="ft-store-btn-name">App Store</span>
                    </div>
                </a>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="ft-col">
            <p class="ft-col-title">Quick Links</p>
            <ul>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ route('user.login') }}">Login</a></li>
                <li><a href="{{ route('user.register') }}">Sign Up</a></li>
            </ul>
        </div>

        {{-- Support --}}
        <div class="ft-col">
            <p class="ft-col-title">Support</p>
            <ul>
                <li><a href="{{ url('/contact') }}">Contact Us</a></li>
                <li><a href="{{ url('/contact') }}">Help Centre</a></li>
            </ul>
        </div>

        {{-- Company --}}
        <div class="ft-col">
            <p class="ft-col-title">Company</p>
            <ul>
                <li><a href="{{ url('/blog') }}">Blog</a></li>
                <li><a href="{{ url('/about') }}">About Us</a></li>
            </ul>
        </div>

        {{-- Legal --}}
        <div class="ft-col">
            <p class="ft-col-title">Legal</p>
            <ul>
                <li><a href="{{ route('policy.pages', 'privacy-policy') }}">Privacy Policy</a></li>
                <li><a href="{{ route('policy.pages', 'terms-of-service') }}">Terms of Service</a></li>
                <li><a href="{{ route('policy.pages', 'refund-policy') }}">Refund Policy</a></li>
                <li><a href="{{ route('policy.pages', 'compliance') }}">Compliance</a></li>
            </ul>
        </div>

    </div>

    {{-- ── Bottom bar ── --}}
    <div class="ft-bottom">
        <p class="ft-copy">
            &copy; {{ date('Y') }} <a href="{{ url('/') }}">{!! siteNameHtml() !!}</a>. All rights reserved.
        </p>
        <div class="ft-status">
            <span class="ft-status-dot"></span>
            All systems operational
        </div>
    </div>

</div>
