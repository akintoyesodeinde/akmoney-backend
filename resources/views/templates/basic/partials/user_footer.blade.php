<style>
/* ── Dashboard Footer ── */
.uf-wrap {
    background: #071318;
    font-family: 'Inter', 'Poppins', sans-serif;
    margin-top: auto;
    border-top: 1px solid rgba(255,255,255,0.07);
}
.uf-main {
    max-width: 1280px;
    margin: 0 auto;
    padding: 40px 40px 40px;
    display: grid;
    grid-template-columns: 1.6fr 1fr 1fr 1fr 1fr;
    gap: 40px;
    box-sizing: border-box;
}
@media(max-width:1023px) {
    .uf-main { grid-template-columns: 1fr 1fr 1fr; padding: 40px 24px; }
    .uf-brand { grid-column: 1 / -1; }
}
@media(max-width:640px) {
    .uf-main { grid-template-columns: 1fr 1fr; padding: 32px 16px; gap: 28px; }
    .uf-brand { grid-column: 1 / -1; }
}

/* Brand */
.uf-logo {
    display: inline-flex; align-items: center;
    text-decoration: none; margin-bottom: 14px;
}
.uf-brand-desc {
    font-size: 13px; color: rgba(255,255,255,0.45);
    line-height: 1.8; margin: 0 0 20px; max-width: 240px;
}
.uf-store-btns { display: flex; gap: 8px; flex-wrap: wrap; }
.uf-store-btn {
    display: inline-flex; align-items: center; gap: 8px;
    background: transparent; border: 1px solid rgba(255,255,255,0.18);
    border-radius: 8px; padding: 7px 12px; color: #fff;
    text-decoration: none; transition: border-color 0.2s, background 0.2s;
    font-family: inherit;
}
.uf-store-btn:hover { border-color: rgba(255,255,255,0.45); background: rgba(255,255,255,0.05); color: #fff; }
.uf-store-btn-text { display: flex; flex-direction: column; }
.uf-store-btn-sub  { font-size: 9px;  color: rgba(255,255,255,0.5); line-height: 1; }
.uf-store-btn-name { font-size: 12px; font-weight: 700; color: #fff; line-height: 1.3; }

/* Nav columns */
.uf-col-title {
    font-size: 10px; font-weight: 700;
    letter-spacing: 0.14em; text-transform: uppercase;
    color: rgba(255,255,255,0.9); margin-bottom: 16px;
}
.uf-col ul { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 10px; }
.uf-col ul li a {
    font-size: 13px; color: rgba(255,255,255,0.5);
    text-decoration: none; transition: color 0.2s;
}
.uf-col ul li a:hover { color: #fff; }

/* Bottom bar */
.uf-bottom {
    border-top: 1px solid rgba(255,255,255,0.08);
    padding: 18px 40px;
    display: flex; align-items: center; justify-content: space-between;
    gap: 16px; max-width: 1280px; margin: 0 auto; box-sizing: border-box;
}
@media(max-width:767px) { .uf-bottom { flex-direction: column; align-items: flex-start; padding: 16px; gap: 8px; } }
.uf-copy { font-size: 12px; color: rgba(255,255,255,0.35); margin: 0; }
.uf-copy a { color: rgba(255,255,255,0.5); text-decoration: none; }
.uf-copy a:hover { color: #fff; }
.uf-status { display: flex; align-items: center; gap: 6px; font-size: 12px; color: rgba(255,255,255,0.45); white-space: nowrap; }
.uf-status-dot { width: 7px; height: 7px; background: #22C55E; border-radius: 50%; flex-shrink: 0; }
</style>

<div class="uf-wrap">
    <div style="border-bottom:1px solid rgba(255,255,255,0.07);"></div>

    <div class="uf-main">

        {{-- Brand --}}
        <div class="uf-brand">
            <a href="{{ url('/') }}" class="uf-logo">
                <img src="{{ siteLogo('dark') }}" alt="{{ gs('site_name') }}" style="max-height:34px;width:auto;max-width:150px;object-fit:contain;">
            </a>
            <p class="uf-brand-desc">Canada's regulated cross-border remittance platform. Send money to Africa, Middle East, and the USA with confidence.</p>
            <div class="uf-store-btns">
                <a href="{{ gs('play_store') ?? '#' }}" class="uf-store-btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" opacity=".85"><path d="M3.18 23.76c.3.17.64.24.99.2l12.6-11.62-3.18-3.18L3.18 23.76zM.5 1.4C.19 1.75 0 2.28 0 2.96v18.08c0 .68.19 1.21.5 1.56l.08.08 10.13-10.13v-.24L.58 1.32.5 1.4zM20.1 10.5l-2.9-1.68-3.56 3.56 3.56 3.56 2.92-1.68c.83-.48.83-1.28-.02-1.76zM4.17.24l12.6 11.62-3.18 3.18L4.17 2.46c-.3-.17-.99-.2-.99.2l.99-.42z"/></svg>
                    <div class="uf-store-btn-text">
                        <span class="uf-store-btn-sub">Get it on</span>
                        <span class="uf-store-btn-name">Google Play</span>
                    </div>
                </a>
                <a href="{{ gs('app_store') ?? '#' }}" class="uf-store-btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" opacity=".85"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                    <div class="uf-store-btn-text">
                        <span class="uf-store-btn-sub">Download on the</span>
                        <span class="uf-store-btn-name">App Store</span>
                    </div>
                </a>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="uf-col">
            <p class="uf-col-title">Quick Links</p>
            <ul>
                <li><a href="{{ route('user.home') }}">Dashboard</a></li>
                <li><a href="{{ route('user.send.money.now') }}">Send Money</a></li>
                <li><a href="{{ route('user.reps') }}">Recipients</a></li>
                <li><a href="{{ route('user.send.money.history') }}">Transactions</a></li>
            </ul>
        </div>

        {{-- Support --}}
        <div class="uf-col">
            <p class="uf-col-title">Support</p>
            <ul>
                <li><a href="{{ url('/contact') }}">Contact Us</a></li>
                <li><a href="{{ url('/contact') }}">Help Centre</a></li>
                <li><a href="{{ url('/blog') }}">Blog</a></li>
            </ul>
        </div>

        {{-- Company --}}
        <div class="uf-col">
            <p class="uf-col-title">Company</p>
            <ul>
                <li><a href="{{ url('/about') }}">About Us</a></li>
                <li><a href="{{ route('user.profile.setting') }}">Account Settings</a></li>
            </ul>
        </div>

        {{-- Legal --}}
        <div class="uf-col">
            <p class="uf-col-title">Legal</p>
            <ul>
                <li><a href="{{ route('policy.pages', 'privacy-policy') }}">Privacy Policy</a></li>
                <li><a href="{{ route('policy.pages', 'terms-of-service') }}">Terms of Service</a></li>
                <li><a href="{{ route('policy.pages', 'refund-policy') }}">Refund Policy</a></li>
                <li><a href="{{ route('policy.pages', 'compliance') }}">Compliance</a></li>
            </ul>
        </div>

    </div>

    {{-- Bottom bar --}}
    <div class="uf-bottom">
        <p class="uf-copy">
            &copy; {{ date('Y') }} <a href="{{ url('/') }}">{!! siteNameHtml() !!}</a>
        </p>
        <div class="uf-status">
            <span class="uf-status-dot"></span>
            All systems operational
        </div>
    </div>

</div>
