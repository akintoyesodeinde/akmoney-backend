@php
    $policyPages = getContent('policy_pages.element', false, null, true);
@endphp

@push('style')
<style>
/* ── Footer wrapper ── */
.air-footer {
    background: #071318;
    color: #fff;
    font-family: 'Inter', sans-serif;
}

/* ── Footer grid ── */
.air-footer__grid {
    max-width: 1200px;
    margin: 0 auto;
    padding: 64px 48px 40px;
    display: grid;
    grid-template-columns: 1.4fr repeat(4, 1fr);
    gap: 40px;
}
@media(max-width: 1023px) {
    .air-footer__grid { grid-template-columns: 1fr 1fr; padding: 48px 28px 32px; gap: 32px; }
}
@media(max-width: 640px) {
    .air-footer__grid { grid-template-columns: 1fr 1fr; padding: 40px 20px 28px; gap: 24px; }
}

/* Brand column */
.air-footer__brand { grid-column: 1; }
@media(max-width: 1023px) { .air-footer__brand { grid-column: 1 / -1; } }

.air-footer__logo {
    display: inline-flex;
    align-items: center;
    text-decoration: none;
    margin-bottom: 16px;
}
.air-footer__logo img {
    height: 40px;
    width: auto;
    object-fit: contain;
    filter: brightness(0) invert(1);
    display: block;
}

.air-footer__desc {
    font-size: 13px;
    color: rgba(158,194,255,.75);
    line-height: 1.8;
    max-width: 240px;
    margin: 0 0 24px;
}

/* ── App + Socials card ── */
.air-apps-socials {
    display: flex;
    flex-direction: column;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 18px;
    overflow: hidden;
    max-width: 280px;
}

.air-store-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
}

.air-store-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 16px 10px;
    background: rgba(255,255,255,.04);
    text-decoration: none;
    transition: background .2s ease;
    border-bottom: 1px solid rgba(255,255,255,.1);
}
.air-store-btn:first-child { border-right: 1px solid rgba(255,255,255,.1); }
.air-store-btn:hover { background: rgba(255,255,255,.09); }

.air-store-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    background: white;
    display: grid;
    place-items: center;
    flex-shrink: 0;
}

.air-store-btn span {
    display: block;
    color: #9ec2ff;
    font-size: 10px;
    line-height: 1.2;
    text-align: center;
}
.air-store-btn strong {
    display: block;
    margin-top: 1px;
    color: white;
    font-size: 13px;
    font-weight: 800;
    text-align: center;
    line-height: 1.2;
}

.air-socials-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    background: rgba(255,255,255,.03);
}

.air-socials-label {
    font-size: 11px;
    color: #9ec2ff;
    white-space: nowrap;
    flex-shrink: 0;
}

.air-socials-icons { display: flex; gap: 6px; align-items: center; }

.air-social-btn {
    display: grid;
    place-items: center;
    width: 32px; height: 32px;
    border-radius: 8px;
    color: #9ec2ff;
    text-decoration: none;
    transition: all .18s ease;
}
.air-social-btn:hover { background: rgba(255,255,255,.1); color: white; }

/* Nav columns */
.air-footer__col h4 {
    font-size: 13px;
    font-weight: 700;
    color: #fff;
    margin: 0 0 18px;
    letter-spacing: .01em;
}

.air-footer__col ul {
    list-style: none;
    margin: 0; padding: 0;
    display: flex;
    flex-direction: column;
    gap: 11px;
}

.air-footer__col ul li a {
    font-size: 13px;
    color: rgba(158,194,255,.7);
    text-decoration: none;
    transition: color .2s;
}
.air-footer__col ul li a:hover { color: #fff; }

/* ── Bottom bar ── */
.air-footer__bottom {
    border-top: 1px solid rgba(255,255,255,.08);
    padding: 20px 48px;
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}
@media(max-width: 767px) {
    .air-footer__bottom { flex-direction: column; align-items: flex-start; padding: 20px; }
}

.air-footer__copy {
    font-size: 12px;
    color: rgba(255,255,255,.35);
    margin: 0;
}
.air-footer__copy a { color: rgba(255,255,255,.5); text-decoration: none; }
.air-footer__copy a:hover { color: #fff; }

.air-trust-badges { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }

.air-trust-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,.12);
    background: rgba(255,255,255,.05);
    font-size: 12px;
    color: #9ec2ff;
    white-space: nowrap;
}
.air-trust-badge svg { flex-shrink: 0; stroke: #60a5fa; }

@media(max-width: 640px) {
    .air-apps-socials { max-width: 100%; }
}
</style>
@endpush

<footer class="air-footer">

    <div class="air-footer__grid">

        {{-- Brand column --}}
        <div class="air-footer__brand">
            <a href="{{ url('/') }}" class="air-footer__logo" aria-label="{{ gs('site_name') }}">
                <img src="{{ siteLogo('dark') }}" alt="{{ gs('site_name') }}">
            </a>
            <p class="air-footer__desc">Making global money transfers simple, fast and secure. Canada's regulated remittance platform.</p>

            {{-- App store + social icons card --}}
            <div class="air-apps-socials">
                <div class="air-store-row">
                    <a href="{{ gs('play_store') ?? '#' }}" class="air-store-btn" aria-label="Get on Google Play">
                        <div class="air-store-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20">
                                <path fill="#EA4335" d="M325.3 234.3L104.6 13l280.8 161.2-60.1 60.1z"/>
                                <path fill="#FBBC04" d="M9.8 9C7.1 11.8 5.5 16 5.5 21.5v469c0 5.5 1.5 9.7 4.3 12.5l.7.6 262.8-262.8v-6.2L9.8 9z"/>
                                <path fill="#34A853" d="M345.7 304.4l-87.4-87.4v-6.2l87.4-87.4.7.4 103.5 58.8c29.5 16.8 29.5 44.3 0 61.1l-103.5 58.8-.7.4z"/>
                                <path fill="#EA4335" d="M9.8 503c2.7 2.9 7 4.6 12.3 4.6 4.3 0 8.2-.9 11.5-2.7l.7-.4 299.9-170.9-.5-.6-87.9-87.9L9.8 503z"/>
                            </svg>
                        </div>
                        <div>
                            <span>Get it on</span>
                            <strong>Google Play</strong>
                        </div>
                    </a>
                    <a href="{{ gs('app_store') ?? '#' }}" class="air-store-btn" aria-label="Download on App Store">
                        <div class="air-store-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 814 1000" width="18" height="22">
                                <path fill="#071318" d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76 0-103.7 40.8-165.9 40.8s-105-42.4-150.3-109.8c-52.3-78.5-96.7-200.1-96.7-316.1 0-183.3 119.2-280.4 236.4-280.4 63.1 0 115.6 41.5 155.3 41.5 37.9 0 97.9-43.5 170.4-43.5zm-58.2-216.2c31.6-37.9 54.3-90.9 54.3-143.9 0-7.5-.6-15-1.9-21.2-51.6 2-112.7 34.3-149.2 75.8-28.5 32.4-55.1 85.4-55.1 139.1 0 8.2 1.3 16.4 1.9 19 3.2.6 8.4 1.3 13.6 1.3 46.1 0 101.3-30.3 136.4-70.1z"/>
                            </svg>
                        </div>
                        <div>
                            <span>Download on the</span>
                            <strong>App Store</strong>
                        </div>
                    </a>
                </div>
                <div class="air-socials-row">
                    <span class="air-socials-label">Follow us</span>
                    <div class="air-socials-icons">
                        <a href="#" class="air-social-btn" aria-label="Facebook">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                        </a>
                        <a href="#" class="air-social-btn" aria-label="X / Twitter">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="#" class="air-social-btn" aria-label="Instagram">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        </a>
                        <a href="#" class="air-social-btn" aria-label="LinkedIn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
                        </a>
                        <a href="#" class="air-social-btn" aria-label="TikTok">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.16 8.16 0 0 0 4.77 1.52V6.74a4.85 4.85 0 0 1-1-.05z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="air-footer__col">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ route('user.login') }}">Login</a></li>
                <li><a href="{{ route('user.register') }}">Sign Up</a></li>
                <li><a href="{{ url('/contact') }}">Support</a></li>
            </ul>
        </div>

        {{-- Company --}}
        <div class="air-footer__col">
            <h4>Company</h4>
            <ul>
                <li><a href="{{ url('/about') }}">About Us</a></li>
                <li><a href="{{ url('/blog') }}">Blog</a></li>
                <li><a href="{{ url('/contact') }}">Contact Us</a></li>
            </ul>
        </div>

        {{-- Product --}}
        <div class="air-footer__col">
            <h4>Product</h4>
            <ul>
                <li><a href="{{ url('/compliance') }}">Compliance</a></li>
                <li><a href="{{ url('/contact') }}">Help Centre</a></li>
            </ul>
        </div>

        {{-- Legal --}}
        <div class="air-footer__col">
            <h4>Legal</h4>
            <ul>
                <li><a href="{{ url('/privacy-policy') }}">Privacy Policy</a></li>
                <li><a href="{{ url('/terms-of-service') }}">Terms of Service</a></li>
                <li><a href="{{ url('/refund-policy') }}">Refund Policy</a></li>
                <li><a href="{{ url('/compliance') }}">Compliance</a></li>
            </ul>
        </div>

    </div>

    {{-- Bottom bar --}}
    <div style="max-width:1200px;margin:0 auto;">
        <div class="air-footer__bottom">
            <p class="air-footer__copy">
                &copy; {{ date('Y') }} <a href="{{ url('/') }}">{!! siteNameHtml() !!}</a> &middot; FINTRAC Registered
            </p>
            <div class="air-trust-badges">
                <span class="air-trust-badge">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    SSL Secured
                </span>
                <span class="air-trust-badge">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    FINTRAC Regulated
                </span>
                <span class="air-trust-badge">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                    256-bit Encryption
                </span>
            </div>
        </div>
    </div>

</footer>
