@push('style')
<style>
.hiw-section {
    background: #fff;
    padding: 80px 0;
    font-family: 'Inter', sans-serif;
    overflow: hidden;
}
.hiw-inner {
    max-width: 1160px;
    margin: 0 auto;
    padding: 0 48px;
    box-sizing: border-box;
}
@media(max-width:767px) { .hiw-inner { padding: 0 20px; } }

/* ── Grid layout ── */
.hiw-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 64px;
    align-items: start;
}
@media(max-width:1023px) { .hiw-grid { grid-template-columns: 1fr; gap: 48px; } }

/* ── Left: image mosaic ── */
.hiw-images {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 220px 180px;
    gap: 12px;
}
@media(max-width:1023px) { .hiw-images { grid-template-rows: 200px 160px; } }
@media(max-width:480px)  { .hiw-images { grid-template-rows: 160px 130px; gap: 8px; } }

.hiw-img {
    border-radius: 16px;
    overflow: hidden;
    position: relative;
}
.hiw-img img {
    width: 100%; height: 100%;
    object-fit: cover; display: block;
    transition: transform 0.4s ease;
}
.hiw-img:hover img { transform: scale(1.04); }

/* Top-left spans full height (2 rows) */
.hiw-img-tl {
    grid-column: 1;
    grid-row: 1 / 3;
    border-radius: 16px;
}
.hiw-img-tr { grid-column: 2; grid-row: 1; }
.hiw-img-bl { display: none; } /* replaced by tl spanning */
.hiw-img-br { grid-column: 2; grid-row: 2; }

/* ── Right: steps ── */
.hiw-header { margin-bottom: 36px; }
.hiw-eyebrow {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: 11px; font-weight: 700; letter-spacing: 0.14em;
    text-transform: uppercase; color: #A71B47; margin-bottom: 14px;
}
.hiw-eyebrow-line { width: 20px; height: 2px; background: #A71B47; border-radius: 2px; }
.hiw-title {
    font-size: clamp(26px, 3.5vw, 40px);
    font-weight: 800; color: #0C0C0C;
    line-height: 1.1; letter-spacing: -0.025em; margin: 0;
}
.hiw-title span { color: #A71B47; }

/* Steps */
.hiw-steps { display: flex; flex-direction: column; gap: 0; }
.hiw-step {
    display: flex; gap: 20px;
    padding: 24px 0;
    border-bottom: 1px solid #F0F0F0;
    position: relative;
}
.hiw-step:last-child { border-bottom: none; padding-bottom: 0; }
.hiw-step:first-child { padding-top: 0; }

.hiw-step-num-col { flex-shrink: 0; display: flex; flex-direction: column; align-items: center; }
.hiw-step-badge {
    width: 40px; height: 40px;
    background: #A71B47; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 800; color: #fff;
    flex-shrink: 0; position: relative; z-index: 1;
}
.hiw-step-line {
    width: 2px; flex: 1; background: #F0F0F0;
    margin-top: 8px; min-height: 16px;
}
.hiw-step:last-child .hiw-step-line { display: none; }

.hiw-step-body { flex: 1; min-width: 0; }
.hiw-step-label {
    font-size: 10px; font-weight: 700; letter-spacing: 0.14em;
    text-transform: uppercase; color: #A71B47; margin-bottom: 6px;
}
.hiw-step-title {
    font-size: 17px; font-weight: 800; color: #0C0C0C;
    margin-bottom: 8px; letter-spacing: -0.015em; line-height: 1.3;
}
.hiw-step-desc {
    font-size: 14px; color: #555; line-height: 1.75; margin: 0;
}

/* ── CTA strip ── */
.hiw-cta {
    margin-top: 56px;
    background: #252525;
    border-radius: 20px;
    padding: 36px 48px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    position: relative;
    overflow: hidden;
}
.hiw-cta::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(255,255,255,0.05) 1px, transparent 1px);
    background-size: 24px 24px;
    pointer-events: none;
}
.hiw-cta-glow {
    position: absolute; top: -60px; right: -60px;
    width: 240px; height: 240px; border-radius: 50%;
    background: radial-gradient(circle, rgba(167,27,71,0.22) 0%, transparent 70%);
    pointer-events: none;
}
.hiw-cta-text { position: relative; z-index: 1; }
.hiw-cta-title {
    font-size: 20px; font-weight: 800; color: #fff;
    margin-bottom: 4px; letter-spacing: -0.02em;
}
.hiw-cta-sub { font-size: 13px; color: rgba(255,255,255,0.65); }
.hiw-cta-actions {
    display: flex; align-items: center; gap: 12px;
    flex-shrink: 0; position: relative; z-index: 1;
}
.hiw-cta-btn-primary {
    display: inline-flex; align-items: center; gap: 7px;
    background: #A71B47; color: #fff;
    font-size: 14px; font-weight: 700;
    padding: 13px 24px; border-radius: 10px;
    text-decoration: none; white-space: nowrap;
    transition: background 0.2s;
    font-family: 'Inter', sans-serif;
}
.hiw-cta-btn-primary:hover { background: #8a1539; color: #fff; }
.hiw-cta-btn-outline {
    display: inline-flex; align-items: center;
    background: transparent; color: #fff;
    font-size: 14px; font-weight: 600;
    padding: 13px 22px; border-radius: 10px;
    border: 1.5px solid rgba(255,255,255,0.3);
    text-decoration: none; white-space: nowrap;
    transition: border-color 0.2s, background 0.2s;
    font-family: 'Inter', sans-serif;
}
.hiw-cta-btn-outline:hover { border-color: rgba(255,255,255,0.6); background: rgba(255,255,255,0.05); color: #fff; }

@media(max-width:767px) {
    .hiw-section { padding: 60px 0; }
    .hiw-cta { padding: 28px 24px; flex-direction: column; align-items: flex-start; gap: 20px; }
    .hiw-cta-actions { width: 100%; flex-direction: column; }
    .hiw-cta-btn-primary, .hiw-cta-btn-outline { width: 100%; justify-content: center; }
}
</style>
@endpush

<section class="hiw-section" id="how-it-works">
    <div class="hiw-inner">

        <div class="hiw-grid">

            {{-- ── Left: image mosaic ── --}}
            <div class="hiw-images">
                <div class="hiw-img hiw-img-tl">
                    <img src="https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=800&q=80&auto=format&fit=crop"
                         alt="Person sending money">
                </div>
                <div class="hiw-img hiw-img-tr">
                    <img src="https://images.unsplash.com/photo-1580048915913-4f8f5cb481c4?w=600&q=80&auto=format&fit=crop"
                         alt="Mobile payment">
                </div>
                <div class="hiw-img hiw-img-br">
                    <img src="https://images.unsplash.com/photo-1521791136064-7986c2920216?w=600&q=80&auto=format&fit=crop"
                         alt="Customer support">
                </div>
            </div>

            {{-- ── Right: steps ── --}}
            <div>
                <div class="hiw-header">
                    <div class="hiw-eyebrow">
                        <span class="hiw-eyebrow-line"></span>
                        How It Works
                    </div>
                    <h2 class="hiw-title">Send Money Globally<br><span>in Minutes.</span></h2>
                </div>

                <div class="hiw-steps">
                    <div class="hiw-step">
                        <div class="hiw-step-num-col">
                            <div class="hiw-step-badge">01</div>
                            <div class="hiw-step-line"></div>
                        </div>
                        <div class="hiw-step-body">
                            <p class="hiw-step-label">Step 01</p>
                            <h3 class="hiw-step-title">Create Your Account</h3>
                            <p class="hiw-step-desc">Sign up in minutes using your email and basic information to start sending money globally. Verify your identity with a government issued ID. Simple verification done in under 3 minutes.</p>
                        </div>
                    </div>

                    <div class="hiw-step">
                        <div class="hiw-step-num-col">
                            <div class="hiw-step-badge">02</div>
                            <div class="hiw-step-line"></div>
                        </div>
                        <div class="hiw-step-body">
                            <p class="hiw-step-label">Step 02</p>
                            <h3 class="hiw-step-title">Choose Destination &amp; Amount</h3>
                            <p class="hiw-step-desc">Enter the country, currency and amount you want to send to dozens of countries we operate. Live exchange rates refreshed every 60 seconds. See exactly what the recipient receives before you confirm.</p>
                        </div>
                    </div>

                    <div class="hiw-step">
                        <div class="hiw-step-num-col">
                            <div class="hiw-step-badge">03</div>
                            <div class="hiw-step-line"></div>
                        </div>
                        <div class="hiw-step-body">
                            <p class="hiw-step-label">Step 03</p>
                            <h3 class="hiw-step-title">Send Money Instantly</h3>
                            <p class="hiw-step-desc">Confirm your details and choose to fund by any means with full transparency. Zero hidden fees. The rate you see is the rate you get — guaranteed.</p>
                        </div>
                    </div>

                    <div class="hiw-step">
                        <div class="hiw-step-num-col">
                            <div class="hiw-step-badge">04</div>
                            <div class="hiw-step-line"></div>
                        </div>
                        <div class="hiw-step-body">
                            <p class="hiw-step-label">Step 04</p>
                            <h3 class="hiw-step-title">Track &amp; Get Support</h3>
                            <p class="hiw-step-desc">Monitor your transfer and get 24/7 customer support from our dedicated global team. Real-time SMS and email notifications at every stage until delivery is complete.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── CTA strip ── --}}
        <div class="hiw-cta">
            <div class="hiw-cta-glow"></div>
            <div class="hiw-cta-text">
                <p class="hiw-cta-title">Ready to send money?</p>
                <p class="hiw-cta-sub">Join 50,000+ customers trusting us with confidence.</p>
            </div>
            <div class="hiw-cta-actions">
                <a href="{{ route('user.register') }}" class="hiw-cta-btn-primary">
                    Get Started Now
                </a>
                <a href="#features" class="hiw-cta-btn-outline">Learn More</a>
            </div>
        </div>

    </div>
</section>
