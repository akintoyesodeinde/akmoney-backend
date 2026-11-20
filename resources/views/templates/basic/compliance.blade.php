@extends($activeTemplate . 'layouts.frontend')

@push('style')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    .cp-wrap *, .cp-wrap *::before, .cp-wrap *::after { box-sizing: border-box; margin: 0; padding: 0; }
    .cp-wrap {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: #fff; color: #0C0C0C;
    }
    .cp-wrap h1,.cp-wrap h2,.cp-wrap h3,.cp-wrap p,.cp-wrap span,
    .cp-wrap a,.cp-wrap div,.cp-wrap li,.cp-wrap button {
      font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    /* Hero */
    .cp-hero {
      padding: 100px 32px 64px;
      border-bottom: 1px solid #EBEBEB;
      position: relative; overflow: hidden;
    }
    .cp-hero::before {
      content: ''; position: absolute; inset: 0;
      background-image: radial-gradient(circle, #E5E7EB 1px, transparent 1px);
      background-size: 28px 28px; opacity: 0.5; pointer-events: none;
    }
    .cp-hero-glow {
      position: absolute; top: -100px; right: -100px;
      width: 360px; height: 360px; border-radius: 50%;
      background: radial-gradient(circle, rgba(22,163,74,0.06) 0%, transparent 70%);
      pointer-events: none;
    }
    .cp-hero-inner { max-width: 1200px; margin: 0 auto; position: relative; z-index: 1; }
    .cp-hero-badge {
      display: inline-flex; align-items: center; gap: 8px;
      background: rgba(22,163,74,0.08); border: 1px solid rgba(22,163,74,0.25);
      border-radius: 99px; padding: 5px 14px; margin-bottom: 24px;
      font-size: 11px; font-weight: 700; color: #16a34a;
      letter-spacing: 0.12em; text-transform: uppercase;
    }
    .cp-hero-badge-dot { width: 6px; height: 6px; border-radius: 50%; background: #16a34a; display: inline-block; }
    .cp-hero-grid {
      display: grid; grid-template-columns: 1fr auto;
      gap: 48px; align-items: end;
    }
    .cp-hero h1 {
      font-size: clamp(36px, 5vw, 64px);
      font-weight: 800; line-height: 1.05;
      margin: 0 0 16px; letter-spacing: -0.03em; color: #0C0C0C;
    }
    .cp-hero h1 span { color: #A71B47; }
    .cp-hero-desc { font-size: 16px; color: #0C0C0C; max-width: 580px; line-height: 1.75; margin: 0 0 32px; font-weight: 400; }
    .cp-score-label { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
    .cp-score-label-text { font-size: 13px; font-weight: 700; color: #0C0C0C; }
    .cp-score-pct { font-size: 14px; font-weight: 800; color: #A71B47; }
    .cp-score-bar { height: 8px; background: #F0F0F0; border-radius: 99px; overflow: hidden; max-width: 440px; }
    .cp-score-fill { height: 100%; width: 100%; background: linear-gradient(90deg, #A71B47, #FF4D5E); border-radius: 99px; }
    .cp-score-note { font-size: 11px; color: #0C0C0C; margin-top: 6px; font-weight: 500; }
    .cp-hero-stats {
      display: grid; grid-template-columns: 1fr 1fr; gap: 10px; flex-shrink: 0;
    }
    .cp-stat-card {
      background: #F7F7F7; border: 1px solid #EBEBEB;
      border-radius: 12px; padding: 12px 16px; text-align: center; min-width: 90px;
    }
    .cp-stat-num { font-size: 20px; font-weight: 800; color: #0C0C0C; letter-spacing: -0.03em; }
    .cp-stat-label { font-size: 10px; font-weight: 700; color: #0C0C0C; text-transform: uppercase; letter-spacing: 0.08em; margin-top: 2px; }

    /* Body */
    .cp-body { max-width: 1200px; margin: 0 auto; padding: 64px 32px 120px; }

    /* Section eyebrow */
    .cp-eyebrow { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 12px; }
    .cp-eyebrow-line { width: 24px; height: 2px; background: #A71B47; border-radius: 2px; display: inline-block; }
    .cp-eyebrow-text { font-size: 11px; font-weight: 700; color: #A71B47; letter-spacing: 0.14em; text-transform: uppercase; }
    .cp-section-h2 { font-size: 28px; font-weight: 800; margin: 0 0 8px; letter-spacing: -0.02em; color: #0C0C0C; }
    .cp-section-desc { font-size: 15px; color: #0C0C0C; margin: 0 0 36px; line-height: 1.6; }

    /* Frameworks */
    .cp-fw-section { margin-bottom: 80px; }
    .cp-fw-grid { display: grid; grid-template-columns: 280px 1fr; gap: 24px; align-items: start; }
    .cp-fw-list { display: flex; flex-direction: column; gap: 8px; }
    .cp-fw-btn {
      display: flex; align-items: center; gap: 14px;
      padding: 16px 18px; border-radius: 14px;
      border: 1.5px solid #EBEBEB; background: #fff;
      cursor: pointer; text-align: left; width: 100%;
      transition: border-color 0.2s, background 0.2s;
    }
    .cp-fw-btn.active {
      border-color: rgba(167,27,71,0.35);
      background: rgba(167,27,71,0.04);
    }
    .cp-fw-btn:hover:not(.active) {
      border-color: rgba(167,27,71,0.2);
      background: rgba(167,27,71,0.02);
    }
    .cp-fw-icon {
      width: 44px; height: 44px; border-radius: 12px;
      background: rgba(167,27,71,0.07); border: 1.5px solid rgba(167,27,71,0.15);
      display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .cp-fw-name { font-size: 15px; font-weight: 800; margin: 0 0 4px; color: #0C0C0C; letter-spacing: -0.01em; }
    .cp-fw-badge-active {
      font-size: 11px; font-weight: 700; color: #16a34a;
      background: rgba(22,163,74,0.08); border: 1px solid rgba(22,163,74,0.25);
      border-radius: 99px; padding: 2px 8px;
    }
    .cp-fw-badge-mandatory {
      font-size: 11px; font-weight: 700; color: #A71B47;
      background: rgba(167,27,71,0.06); border: 1px solid rgba(167,27,71,0.2);
      border-radius: 99px; padding: 2px 8px;
    }
    .cp-fw-chevron { margin-left: auto; flex-shrink: 0; }
    .cp-fw-detail {
      padding: 36px 40px; background: #F7F7F7;
      border: 1.5px solid #EBEBEB; border-radius: 20px;
      display: none;
    }
    .cp-fw-detail.active { display: block; }
    .cp-fw-detail-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 20px; }
    .cp-fw-detail-title { font-size: 32px; font-weight: 800; margin: 0 0 6px; color: #A71B47; letter-spacing: -0.025em; }
    .cp-fw-detail-subtitle { font-size: 14px; color: #0C0C0C; margin: 0 0 14px; font-weight: 500; }
    .cp-fw-detail-status {
      display: inline-flex; align-items: center; gap: 6px;
      background: rgba(22,163,74,0.08); border: 1px solid rgba(22,163,74,0.25);
      border-radius: 99px; padding: 5px 14px;
    }
    .cp-fw-detail-status-dot { width: 6px; height: 6px; border-radius: 50%; background: #16a34a; }
    .cp-fw-detail-status-text { font-size: 12px; font-weight: 700; color: #16a34a; }
    .cp-fw-detail-icon {
      width: 56px; height: 56px; border-radius: 14px;
      background: rgba(167,27,71,0.08); border: 1.5px solid rgba(167,27,71,0.15);
      display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .cp-fw-detail-body { font-size: 15px; color: #0C0C0C; line-height: 1.8; margin: 0; font-weight: 400; }

    /* Security Measures */
    .cp-measures-section { margin-bottom: 64px; }
    .cp-measures-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
    .cp-measure-card {
      padding: 20px 22px; background: #fff;
      border: 1.5px solid #EBEBEB; border-radius: 14px;
      display: flex; gap: 14px; align-items: flex-start;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .cp-measure-card:hover {
      border-color: rgba(167,27,71,0.2);
      box-shadow: 0 4px 20px rgba(167,27,71,0.05);
    }
    .cp-measure-icon { font-size: 26px; flex-shrink: 0; line-height: 1; }
    .cp-measure-title { font-size: 14px; font-weight: 700; margin: 0 0 5px; color: #0C0C0C; }
    .cp-measure-desc { font-size: 13px; color: #0C0C0C; margin: 0; line-height: 1.6; }

    /* Contact box */
    .cp-contact-box {
      background: #0C0C0C; border-radius: 20px;
      padding: 40px 44px; position: relative; overflow: hidden;
    }
    .cp-contact-glow {
      position: absolute; top: -60px; right: -60px;
      width: 240px; height: 240px; border-radius: 50%;
      background: radial-gradient(circle, rgba(167,27,71,0.2) 0%, transparent 70%);
      pointer-events: none;
    }
    .cp-contact-dots {
      position: absolute; inset: 0;
      background-image: radial-gradient(circle, rgba(255,255,255,0.04) 1px, transparent 1px);
      background-size: 24px 24px; pointer-events: none;
    }
    .cp-contact-inner { position: relative; z-index: 1; }
    .cp-contact-badge {
      display: inline-flex; align-items: center; gap: 6px;
      background: rgba(167,27,71,0.15); border: 1px solid rgba(167,27,71,0.3);
      border-radius: 99px; padding: 4px 12px; margin-bottom: 16px;
      font-size: 11px; font-weight: 700; color: #A71B47;
      letter-spacing: 0.1em; text-transform: uppercase;
    }
    .cp-contact-box h3 { font-size: 22px; font-weight: 800; color: #fff; letter-spacing: -0.02em; margin-bottom: 10px; }
    .cp-contact-box p { font-size: 15px; color: #fff; margin-bottom: 24px; line-height: 1.75; }
    .cp-contact-box p a { color: #A71B47; font-weight: 600; text-decoration: none; }
    .cp-related-links { display: flex; gap: 10px; flex-wrap: wrap; }
    .cp-related-link {
      font-size: 13px; font-weight: 600; color: #fff; text-decoration: none;
      padding: 8px 16px; border: 1px solid rgba(255,255,255,0.2); border-radius: 8px;
      transition: all 0.15s;
    }
    .cp-related-link:hover { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.4); color: #fff; }

    @media (max-width: 1023px) {
      .cp-fw-grid { grid-template-columns: 1fr; }
      .cp-measures-grid { grid-template-columns: 1fr 1fr; }
      .cp-hero-grid { grid-template-columns: 1fr; }
      .cp-hero-stats { grid-template-columns: repeat(4, 1fr); }
    }
    @media (max-width: 640px) {
      .cp-hero { padding: 80px 20px 48px; }
      .cp-body { padding: 40px 20px 80px; }
      .cp-contact-box { padding: 28px 20px; }
      .cp-measures-grid { grid-template-columns: 1fr; }
      .cp-hero-stats { grid-template-columns: 1fr 1fr; }
      .cp-fw-detail { padding: 24px 20px; }
    }
</style>
@endpush

@section('content')
<div class="cp-wrap">

    {{-- Hero --}}
    <div class="cp-hero">
        <div class="cp-hero-glow"></div>
        <div class="cp-hero-inner">
            <div class="cp-hero-badge">
                <span class="cp-hero-badge-dot"></span>
                {{ __('Fully Compliant & Regulated') }}
            </div>
            <div class="cp-hero-grid">
                <div>
                    <h1>{{ __('Our Compliance') }} <span>{{ __('Framework') }}</span></h1>
                    <p class="cp-hero-desc">{!! siteNameHtml() !!} @lang('is built on a foundation of regulatory compliance. We meet or exceed every obligation imposed by Canadian federal and provincial law.')</p>
                    <div style="max-width:440px;">
                        <div class="cp-score-label">
                            <span class="cp-score-label-text">{{ __('Overall Compliance Score') }}</span>
                            <span class="cp-score-pct">100%</span>
                        </div>
                        <div class="cp-score-bar">
                            <div class="cp-score-fill"></div>
                        </div>
                        <p class="cp-score-note">{{ __('Based on FINTRAC, PIPEDA, AML/CTF, and internal audit results') }}</p>
                    </div>
                </div>
                <div class="cp-hero-stats">
                    <div class="cp-stat-card">
                        <div class="cp-stat-num">4</div>
                        <div class="cp-stat-label">{{ __('Frameworks') }}</div>
                    </div>
                    <div class="cp-stat-card">
                        <div class="cp-stat-num">7 yrs</div>
                        <div class="cp-stat-label">{{ __('Record Retention') }}</div>
                    </div>
                    <div class="cp-stat-card">
                        <div class="cp-stat-num">100%</div>
                        <div class="cp-stat-label">{{ __('KYC Verified') }}</div>
                    </div>
                    <div class="cp-stat-card">
                        <div class="cp-stat-num">24/7</div>
                        <div class="cp-stat-label">{{ __('Monitoring') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="cp-body">

        {{-- Regulatory Frameworks --}}
        <div class="cp-fw-section">
            <div class="cp-eyebrow">
                <span class="cp-eyebrow-line"></span>
                <span class="cp-eyebrow-text">{{ __('Frameworks') }}</span>
            </div>
            <h2 class="cp-section-h2">{{ __('Regulatory Frameworks') }}</h2>
            <p class="cp-section-desc">{{ __('Select a framework to learn more about our obligations and compliance status.') }}</p>

            <div class="cp-fw-grid">
                {{-- Framework selector --}}
                <div class="cp-fw-list">
                    <button class="cp-fw-btn active" onclick="cpShowFw('fintrac', this)">
                        <div class="cp-fw-icon">
                            <svg width="26" height="26" fill="none" stroke="#A71B47" stroke-width="1.6" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <div>
                            <p class="cp-fw-name">FINTRAC</p>
                            <span class="cp-fw-badge-active">{{ __('Active') }}</span>
                        </div>
                        <svg class="cp-fw-chevron" width="16" height="16" fill="none" stroke="#A71B47" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                    <button class="cp-fw-btn" onclick="cpShowFw('pipeda', this)">
                        <div class="cp-fw-icon">
                            <svg width="26" height="26" fill="none" stroke="#A71B47" stroke-width="1.6" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div>
                            <p class="cp-fw-name">PIPEDA</p>
                            <span class="cp-fw-badge-active">{{ __('Compliant') }}</span>
                        </div>
                    </button>
                    <button class="cp-fw-btn" onclick="cpShowFw('aml', this)">
                        <div class="cp-fw-icon">
                            <svg width="26" height="26" fill="none" stroke="#A71B47" stroke-width="1.6" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                        </div>
                        <div>
                            <p class="cp-fw-name">AML/CTF</p>
                            <span class="cp-fw-badge-active">{{ __('Enforced') }}</span>
                        </div>
                    </button>
                    <button class="cp-fw-btn" onclick="cpShowFw('kyc', this)">
                        <div class="cp-fw-icon">
                            <svg width="26" height="26" fill="none" stroke="#A71B47" stroke-width="1.6" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/><polyline points="16 11 17 13 21 9"/></svg>
                        </div>
                        <div>
                            <p class="cp-fw-name">KYC</p>
                            <span class="cp-fw-badge-mandatory">{{ __('Mandatory') }}</span>
                        </div>
                    </button>
                </div>

                {{-- Detail panels --}}
                <div>
                    <div class="cp-fw-detail active" id="cp-fw-fintrac">
                        <div class="cp-fw-detail-top">
                            <div>
                                <h2 class="cp-fw-detail-title">FINTRAC</h2>
                                <p class="cp-fw-detail-subtitle">{{ __('Financial Transactions and Reports Analysis Centre') }}</p>
                                <div class="cp-fw-detail-status">
                                    <span class="cp-fw-detail-status-dot"></span>
                                    <span class="cp-fw-detail-status-text">{{ __('Registered MSB') }}</span>
                                </div>
                            </div>
                            <div class="cp-fw-detail-icon">
                                <svg width="26" height="26" fill="none" stroke="#A71B47" stroke-width="1.6" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            </div>
                        </div>
                        <p class="cp-fw-detail-body">{{ __('Registered and compliant with Canada\'s financial intelligence unit. We file Suspicious Transaction Reports (STRs), Large Cash Transaction Reports (LCTRs), and Electronic Funds Transfer Reports (EFTRs) as required.') }}</p>
                    </div>

                    <div class="cp-fw-detail" id="cp-fw-pipeda">
                        <div class="cp-fw-detail-top">
                            <div>
                                <h2 class="cp-fw-detail-title">PIPEDA</h2>
                                <p class="cp-fw-detail-subtitle">{{ __('Personal Information Protection and Electronic Documents Act') }}</p>
                                <div class="cp-fw-detail-status">
                                    <span class="cp-fw-detail-status-dot"></span>
                                    <span class="cp-fw-detail-status-text">{{ __('Fully Compliant') }}</span>
                                </div>
                            </div>
                            <div class="cp-fw-detail-icon">
                                <svg width="26" height="26" fill="none" stroke="#A71B47" stroke-width="1.6" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </div>
                        </div>
                        <p class="cp-fw-detail-body">{{ __('We comply with Canada\'s federal private-sector privacy law. We collect, use, and disclose personal information only as necessary to provide our services. Customers have the right to access and correct their personal information held by us.') }}</p>
                    </div>

                    <div class="cp-fw-detail" id="cp-fw-aml">
                        <div class="cp-fw-detail-top">
                            <div>
                                <h2 class="cp-fw-detail-title">AML/CTF</h2>
                                <p class="cp-fw-detail-subtitle">{{ __('Anti-Money Laundering & Counter-Terrorist Financing') }}</p>
                                <div class="cp-fw-detail-status">
                                    <span class="cp-fw-detail-status-dot"></span>
                                    <span class="cp-fw-detail-status-text">{{ __('Program Active') }}</span>
                                </div>
                            </div>
                            <div class="cp-fw-detail-icon">
                                <svg width="26" height="26" fill="none" stroke="#A71B47" stroke-width="1.6" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                            </div>
                        </div>
                        <p class="cp-fw-detail-body">{{ __('We maintain a comprehensive AML/CTF compliance program including written policies and procedures, a designated compliance officer, ongoing employee training, risk assessments, and independent audits. Our program detects, deters, and reports suspicious financial activity.') }}</p>
                    </div>

                    <div class="cp-fw-detail" id="cp-fw-kyc">
                        <div class="cp-fw-detail-top">
                            <div>
                                <h2 class="cp-fw-detail-title">KYC</h2>
                                <p class="cp-fw-detail-subtitle">{{ __('Know Your Customer Program') }}</p>
                                <div class="cp-fw-detail-status">
                                    <span class="cp-fw-detail-status-dot"></span>
                                    <span class="cp-fw-detail-status-text">{{ __('Mandatory for All Users') }}</span>
                                </div>
                            </div>
                            <div class="cp-fw-detail-icon">
                                <svg width="26" height="26" fill="none" stroke="#A71B47" stroke-width="1.6" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/><polyline points="16 11 17 13 21 9"/></svg>
                            </div>
                        </div>
                        <p class="cp-fw-detail-body">{{ __('We apply robust KYC procedures to verify the identity of all customers before processing transactions. We verify government-issued photo identification and may use third-party identity verification services. Enhanced due diligence is applied to higher-risk customers and transactions.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Security Measures --}}
        <div class="cp-measures-section">
            <div class="cp-eyebrow">
                <span class="cp-eyebrow-line"></span>
                <span class="cp-eyebrow-text">{{ __('Security') }}</span>
            </div>
            <h2 class="cp-section-h2">{{ __('Security Measures') }}</h2>
            <p class="cp-section-desc">{{ __('Technical and operational controls that protect you and your funds.') }}</p>

            <div class="cp-measures-grid">
                <div class="cp-measure-card">
                    <span class="cp-measure-icon">🔐</span>
                    <div>
                        <p class="cp-measure-title">{{ __('256-bit TLS Encryption') }}</p>
                        <p class="cp-measure-desc">{{ __('All data in transit encrypted with TLS 1.3') }}</p>
                    </div>
                </div>
                <div class="cp-measure-card">
                    <span class="cp-measure-icon">🏦</span>
                    <div>
                        <p class="cp-measure-title">{{ __('Segregated Client Funds') }}</p>
                        <p class="cp-measure-desc">{{ __('Your funds held separately from operating capital in Canadian financial institutions') }}</p>
                    </div>
                </div>
                <div class="cp-measure-card">
                    <span class="cp-measure-icon">📊</span>
                    <div>
                        <p class="cp-measure-title">{{ __('Real-time Monitoring') }}</p>
                        <p class="cp-measure-desc">{{ __('Automated transaction screening against global sanctions lists 24/7') }}</p>
                    </div>
                </div>
                <div class="cp-measure-card">
                    <span class="cp-measure-icon">📋</span>
                    <div>
                        <p class="cp-measure-title">{{ __('7-Year Record Retention') }}</p>
                        <p class="cp-measure-desc">{{ __('Transaction records kept as mandated by FINTRAC regulations') }}</p>
                    </div>
                </div>
                <div class="cp-measure-card">
                    <span class="cp-measure-icon">🎓</span>
                    <div>
                        <p class="cp-measure-title">{{ __('Staff AML Training') }}</p>
                        <p class="cp-measure-desc">{{ __('All team members complete annual compliance and AML certification') }}</p>
                    </div>
                </div>
                <div class="cp-measure-card">
                    <span class="cp-measure-icon">🔍</span>
                    <div>
                        <p class="cp-measure-title">{{ __('Annual Independent Audit') }}</p>
                        <p class="cp-measure-desc">{{ __('Third-party compliance review conducted each fiscal year') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contact box --}}
        <div class="cp-contact-box">
            <div class="cp-contact-glow"></div>
            <div class="cp-contact-dots"></div>
            <div class="cp-contact-inner">
                <div class="cp-contact-badge">{{ __('Compliance Officer') }}</div>
                <h3>{{ __('Compliance Inquiries') }}</h3>
                <p>{{ __('Contact our Compliance Officer at') }} <a href="mailto:compliance@kinmoney.ca">compliance@kinmoney.ca</a>. {{ __('For law enforcement inquiries, please include your legal authority and badge number.') }}</p>
                <div class="cp-related-links">
                    <a class="cp-related-link" href="{{ route('policy.pages', 'privacy-policy') }}">{{ __('Privacy Policy') }} →</a>
                    <a class="cp-related-link" href="{{ route('policy.pages', 'terms-of-service') }}">{{ __('Terms of Service') }} →</a>
                    <a class="cp-related-link" href="{{ route('policy.pages', 'refund-policy') }}">{{ __('Refund Policy') }} →</a>
                </div>
            </div>
        </div>

    </div>

</div>

@push('script')
<script>
function cpShowFw(id, btn) {
    document.querySelectorAll('.cp-fw-detail').forEach(function(el) { el.classList.remove('active'); });
    document.querySelectorAll('.cp-fw-btn').forEach(function(el) {
        el.classList.remove('active');
        el.querySelector('.cp-fw-chevron') && (el.querySelector('.cp-fw-chevron').style.display = 'none');
    });
    document.getElementById('cp-fw-' + id).classList.add('active');
    btn.classList.add('active');
    if (btn.querySelector('.cp-fw-chevron')) btn.querySelector('.cp-fw-chevron').style.display = '';
}
// Init: hide chevrons on inactive buttons
document.querySelectorAll('.cp-fw-btn:not(.active) .cp-fw-chevron').forEach(function(el) { el.style.display = 'none'; });
</script>
@endpush

@endsection
