@extends($activeTemplate . 'layouts.frontend')
@php
    $contact = getContent('contact.content', true);
    $faqElements = getContent('faq.element', false, null, true);
    $user = auth()->user();
@endphp

@push('style')
<style>
.km-contact *, .km-contact *::before, .km-contact *::after { box-sizing: border-box; }
.km-contact { font-family: 'Work Sans','Segoe UI',Arial,sans-serif; color: #0d1a14; }
.km-contact a { color: inherit; text-decoration: none; }
.km-cx { width: min(calc(100% - 48px), 1200px); margin-inline: auto; }

/* ── Hero ── */
.kmc-hero {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #0F4650 0%, #1a5c6b 100%);
    color: #fff;
    padding: 32px 0 72px;
    text-align: center;
}
.kmc-hero-blob1 { position: absolute; right: -80px; top: -80px; width: 350px; height: 350px; border-radius: 50%; background: #A71B47; opacity: .10; filter: blur(80px); pointer-events: none; }
.kmc-hero-blob2 { position: absolute; left: -80px; bottom: -80px; width: 350px; height: 350px; border-radius: 50%; background: #A71B47; opacity: .10; filter: blur(80px); pointer-events: none; }

.kmc-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 999px;
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(8px);
    font-size: 13px;
    color: rgba(255,255,255,0.90);
    margin-bottom: 24px;
}
.kmc-hero-badge svg { color: #fff; width: 16px; height: 16px; flex-shrink: 0; }

.kmc-hero h1 { margin: 0 0 16px; font-size: clamp(36px,5vw,60px); font-weight: 900; letter-spacing: -1.5px; color: #fff; }
.kmc-hero p { margin: 0 auto; max-width: 640px; font-size: 17px; color: rgba(255,255,255,0.78); line-height: 1.7; }

.kmc-stats { margin-top: 48px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; max-width: 640px; margin-left: auto; margin-right: auto; }
.kmc-stat-icon { color: #fff; display: flex; justify-content: center; margin-bottom: 8px; }
.kmc-stat-num { font-size: 26px; font-weight: 800; }
.kmc-stat-lbl { font-size: 13px; color: rgba(255,255,255,0.65); margin-top: 2px; }

/* ── Main Section ── */
.kmc-main { background: #fff; padding: 72px 0; }
.kmc-main-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; }

/* Left: channels + offices */
.kmc-channels { display: flex; flex-direction: column; gap: 28px; }

.kmc-chat-card {
    position: relative;
    overflow: hidden;
    border-radius: 16px;
    border: 1px solid rgba(167,27,71,0.18);
    background: linear-gradient(135deg, rgba(167,27,71,0.05), transparent);
    padding: 24px;
    transition: box-shadow .22s;
}
.kmc-chat-card:hover { box-shadow: 0 10px 32px rgba(167,27,71,0.10); }
.kmc-chat-icon-bg { position: absolute; right: -20px; top: -20px; opacity: .08; font-size: 80px; }

.kmc-ping-wrap { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; }
.kmc-ping { position: relative; width: 12px; height: 12px; }
.kmc-ping::before { content: ''; position: absolute; inset: 0; border-radius: 50%; background: #22c55e; opacity: .75; animation: kmcPing 1.5s cubic-bezier(0,0,.2,1) infinite; }
.kmc-ping::after  { content: ''; position: absolute; inset: 0; border-radius: 50%; background: #22c55e; }
@keyframes kmcPing { 75%,100% { transform: scale(2); opacity: 0; } }

.kmc-chat-card h3 { margin: 0 0 6px; font-size: 22px; font-weight: 800; color: #0F4650; }
.kmc-chat-card p  { margin: 0 0 16px; color: #475569; font-size: 14px; }

.kmc-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 10px;
    background: #A71B47;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    transition: background .18s;
    text-decoration: none;
    font-family: 'Work Sans', sans-serif;
}
.kmc-btn-primary:hover { background: #8a153c; color: #fff; }

.kmc-contact-cards { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.kmc-contact-card {
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    padding: 20px;
    transition: box-shadow .22s;
}
.kmc-contact-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.06); }
.kmc-contact-card-icon {
    width: 40px; height: 40px;
    border-radius: 999px;
    background: rgba(167,27,71,0.08);
    display: flex; align-items: center; justify-content: center;
    color: #A71B47;
    margin-bottom: 12px;
}
.kmc-contact-card h4 { margin: 0 0 4px; font-size: 15px; font-weight: 800; color: #0F4650; }
.kmc-contact-card .lbl { font-size: 13px; color: #64748b; }
.kmc-contact-card .val { font-size: 14px; font-weight: 700; color: #0d1a14; margin-top: 6px; }
.kmc-contact-card .sub { font-size: 12px; color: #94a3b8; margin-top: 3px; }

.kmc-offices h3 { font-size: 19px; font-weight: 800; color: #0F4650; margin: 0 0 16px; }
.kmc-office-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
}
.kmc-office-row:last-child { border-bottom: none; }
.kmc-office-city { font-weight: 700; color: #0F4650; font-size: 14px; }
.kmc-office-country { font-size: 12px; color: #94a3b8; margin-top: 2px; }
.kmc-office-hours { display: flex; align-items: center; gap: 5px; font-size: 13px; color: #64748b; }

.kmc-trust {
    border-radius: 14px;
    background: rgba(15,70,80,0.04);
    padding: 20px;
}
.kmc-trust-items { display: flex; flex-wrap: wrap; gap: 16px; }
.kmc-trust-item { display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #0F4650; }
.kmc-trust-item svg { color: #A71B47; flex-shrink: 0; }

/* Right: Form */
.kmc-form-wrap {
    position: relative;
}
.kmc-form-glow { position: absolute; right: -16px; top: -16px; width: 120px; height: 120px; border-radius: 50%; background: rgba(167,27,71,0.05); filter: blur(30px); pointer-events: none; }

.kmc-form {
    position: relative;
    border-radius: 20px;
    background: #fff;
    padding: 36px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.10);
    border: 1px solid rgba(0,0,0,0.05);
}
.kmc-form h2 { margin: 0 0 24px; font-size: 24px; font-weight: 900; color: #0F4650; }

.kmc-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.kmc-form-group { margin-bottom: 18px; }
.kmc-form-group.full { grid-column: 1 / -1; }

.kmc-form label { display: block; margin-bottom: 6px; font-size: 13px; font-weight: 700; color: #0F4650; }

.kmc-form input,
.kmc-form select,
.kmc-form textarea {
    width: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: #fff;
    padding: 13px 16px;
    font-size: 14px;
    font-family: 'Work Sans', sans-serif;
    color: #0d1a14;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.kmc-form input:focus,
.kmc-form select:focus,
.kmc-form textarea:focus {
    border-color: #A71B47;
    box-shadow: 0 0 0 3px rgba(167,27,71,0.10);
}
.kmc-form textarea { resize: none; min-height: 130px; }
.kmc-form select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 14px center; padding-right: 40px; }

.kmc-form .cp-error { color: #dc2626; font-size: 12px; margin-top: 4px; display: block; }

.kmc-submit {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 15px 24px;
    border-radius: 12px;
    background: #A71B47;
    color: #fff;
    font-size: 15px;
    font-weight: 800;
    border: none;
    cursor: pointer;
    transition: background .18s;
    font-family: 'Work Sans', sans-serif;
}
.kmc-submit:hover { background: #8a153c; }

.kmc-form-note { text-align: center; font-size: 12px; color: #94a3b8; margin-top: 14px; }
.kmc-form-note a { color: #A71B47; }

/* ── FAQ ── */
.kmc-faq { padding: 72px 0; background: #f8f9fa; }
.kmc-faq-head { text-align: center; max-width: 560px; margin: 0 auto 48px; }
.kmc-faq-eyebrow { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .18em; color: #A71B47; display: block; margin-bottom: 12px; }
.kmc-faq-head h2 { margin: 0; font-size: clamp(28px,3.5vw,40px); font-weight: 800; color: #0F4650; letter-spacing: -.6px; }
.kmc-faq-head p { margin: 12px 0 0; color: #64748b; font-size: 15px; line-height: 1.7; }

.kmc-faq-list { max-width: 760px; margin: 0 auto; display: flex; flex-direction: column; gap: 12px; }
.kmc-faq-item { border-radius: 14px; border: 1px solid #e2e8f0; background: #fff; overflow: hidden; }
.kmc-faq-q {
    width: 100%; padding: 18px 20px; border: none; background: transparent;
    display: flex; align-items: center; justify-content: space-between; gap: 14px;
    text-align: left; cursor: pointer; font-family: 'Work Sans', sans-serif;
    font-size: 15px; font-weight: 700; color: #0d1a14;
}
.kmc-faq-q svg { flex-shrink: 0; transition: transform .22s; color: #A71B47; }
.kmc-faq-q.open svg { transform: rotate(180deg); }
.kmc-faq-a { max-height: 0; overflow: hidden; transition: max-height .3s ease; }
.kmc-faq-a.open { max-height: 300px; }
.kmc-faq-a p { margin: 0; padding: 0 20px 18px; color: #475569; font-size: 14px; line-height: 1.75; }

/* Responsive */
@media(max-width: 900px) {
    .kmc-main-grid { grid-template-columns: 1fr; }
    .kmc-stats { grid-template-columns: repeat(2, 1fr); }
}
@media(max-width: 640px) {
    .kmc-hero { padding: 24px 0 52px; }
    .kmc-form-row { grid-template-columns: 1fr; }
    .kmc-contact-cards { grid-template-columns: 1fr; }
    .kmc-form { padding: 24px; }
}
</style>
@endpush

@section('content')
<div class="km-contact">

  {{-- ── Hero ── --}}
  <div class="kmc-hero">
    <div class="kmc-hero-blob1" aria-hidden="true"></div>
    <div class="kmc-hero-blob2" aria-hidden="true"></div>
    <div class="km-cx" style="position:relative;z-index:2;">
      <div class="kmc-hero-badge">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
        </svg>
        24/7 Global Support
      </div>
      <h1>We're here to help</h1>
      <p>Experience lightning-fast support for all your international transfer needs. Our average response time is under 2 hours.</p>

      <div class="kmc-stats">
        <div>
          <div class="kmc-stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          <div class="kmc-stat-num">50K+</div>
          <div class="kmc-stat-lbl">Happy Customers</div>
        </div>
        <div>
          <div class="kmc-stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
          </div>
          <div class="kmc-stat-num">120+</div>
          <div class="kmc-stat-lbl">Countries</div>
        </div>
        <div>
          <div class="kmc-stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
          </div>
          <div class="kmc-stat-num">4.9/5</div>
          <div class="kmc-stat-lbl">Support Rating</div>
        </div>
        <div>
          <div class="kmc-stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <div class="kmc-stat-num">&lt;2hrs</div>
          <div class="kmc-stat-lbl">Avg Response</div>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Main Section ── --}}
  <section class="kmc-main">
    <div class="km-cx">
      <div class="kmc-main-grid">

        {{-- Left: channels --}}
        <div class="kmc-channels">

          {{-- Live chat --}}
          <div class="kmc-chat-card">
            <div class="kmc-chat-icon-bg" aria-hidden="true">
              <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
            </div>
            <div class="kmc-ping-wrap">
              <div class="kmc-ping"></div>
              <span style="font-size:13px;font-weight:600;color:#16a34a;">Live Now</span>
            </div>
            <h3>Live Chat Support</h3>
            <p>Get instant answers from our support team</p>
            <button class="kmc-btn-primary" type="button">
              Start Chat
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
            </button>
          </div>

          {{-- Phone + Email --}}
          <div class="kmc-contact-cards">
            <div class="kmc-contact-card">
              <div class="kmc-contact-card-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15.05 5A5 5 0 0 1 19 8.95M15.05 1A9 9 0 0 1 23 8.94m-1 7.98v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              </div>
              <h4>Call Us</h4>
              <div class="lbl">Toll-free number</div>
              <div class="val">{{ @$contact->data_values->mobile ?? '+1 (800) 555-0123' }}</div>
              <div class="sub">24/7 Emergency Support</div>
            </div>
            <div class="kmc-contact-card">
              <div class="kmc-contact-card-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              </div>
              <h4>Email Us</h4>
              <div class="lbl">Support</div>
              <div class="val">{{ @$contact->data_values->email ?? 'help@kinmoney.ca' }}</div>
              <div class="sub">Partnerships: partners@kinmoney.ca</div>
            </div>
          </div>

          {{-- Global Offices --}}
          <div class="kmc-offices">
            <h3>Our Global Offices</h3>
            <div class="kmc-office-row">
              <div>
                <div class="kmc-office-city">{{ @$contact->data_values->address ? gs('site_name') : 'Toronto' }}</div>
                <div class="kmc-office-country">{{ @$contact->data_values->address ?? 'Canada · Head Office' }}</div>
              </div>
              <div class="kmc-office-hours">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                9am–6pm EST
              </div>
            </div>
            <div class="kmc-office-row">
              <div>
                <div class="kmc-office-city">London</div>
                <div class="kmc-office-country">United Kingdom</div>
              </div>
              <div class="kmc-office-hours">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                9am–5pm GMT
              </div>
            </div>
            <div class="kmc-office-row">
              <div>
                <div class="kmc-office-city">Lagos</div>
                <div class="kmc-office-country">Nigeria · Africa Hub</div>
              </div>
              <div class="kmc-office-hours">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                8am–5pm WAT
              </div>
            </div>
          </div>

          {{-- Trust badges --}}
          <div class="kmc-trust">
            <div class="kmc-trust-items">
              <div class="kmc-trust-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                SSL Secure
              </div>
              <div class="kmc-trust-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                FINTRAC Compliant
              </div>
              <div class="kmc-trust-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                24/7 Support
              </div>
              <div class="kmc-trust-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Money Back Guarantee
              </div>
            </div>
          </div>

        </div>

        {{-- Right: Contact Form --}}
        <div class="kmc-form-wrap">
          <div class="kmc-form-glow" aria-hidden="true"></div>
          <form class="kmc-form" method="post" action="{{ url('/contact') }}">
            @csrf
            <h2>Send us a message</h2>

            <div class="kmc-form-row">
              <div class="kmc-form-group">
                <label for="cnt-name">{{ __('Full Name') }} *</label>
                <input type="text" id="cnt-name" name="name" value="{{ old('name', @$user->fullname) }}" placeholder="{{ __('Your full name') }}" required>
                @error('name')<span class="cp-error">{{ $message }}</span>@enderror
              </div>
              <div class="kmc-form-group">
                <label for="cnt-email">{{ __('Email') }} *</label>
                <input type="email" id="cnt-email" name="email" value="{{ old('email', @$user->email) }}" placeholder="{{ __('Your email address') }}" required>
                @error('email')<span class="cp-error">{{ $message }}</span>@enderror
              </div>
            </div>

            <div class="kmc-form-group">
              <label for="cnt-inquiry">{{ __('Inquiry Type') }}</label>
              <select id="cnt-inquiry" name="inquiry_type">
                <option value="general">{{ __('General Inquiry') }}</option>
                <option value="transfer">{{ __('Transfer Support') }}</option>
                <option value="partnership">{{ __('Business Partnership') }}</option>
                <option value="complaint">{{ __('File a Complaint') }}</option>
              </select>
            </div>

            <div class="kmc-form-group">
              <label for="cnt-subject">{{ __('Subject') }} *</label>
              <input type="text" id="cnt-subject" name="subject" value="{{ old('subject') }}" placeholder="{{ __('How can we help?') }}" required>
              @error('subject')<span class="cp-error">{{ $message }}</span>@enderror
            </div>

            <div class="kmc-form-group">
              <label>{{ __('Captcha') }}</label>
              <x-captcha />
            </div>

            <div class="kmc-form-group">
              <label for="cnt-message">{{ __('Message') }} *</label>
              <textarea id="cnt-message" name="message" placeholder="{{ __('Write your message here...') }}" required>{{ old('message') }}</textarea>
              @error('message')<span class="cp-error">{{ $message }}</span>@enderror
            </div>

            <button type="submit" class="kmc-submit">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
              {{ __('Send Message') }}
            </button>

            <p class="kmc-form-note">
              By submitting, you agree to our <a href="{{ route('policy.pages', 'privacy-policy') }}">Privacy Policy</a>
            </p>
          </form>
        </div>

      </div>
    </div>
  </section>

  {{-- ── FAQ ── --}}
  <section class="kmc-faq">
    <div class="km-cx">
      <div class="kmc-faq-head">
        <span class="kmc-faq-eyebrow">Quick Answers</span>
        <h2>Frequently Asked Questions</h2>
        <p>Find quick answers about transfers, fees, security, and account support.</p>
      </div>

      <div class="kmc-faq-list">
        @if($faqElements && count($faqElements) > 0)
          @foreach($faqElements as $faq)
          <div class="kmc-faq-item">
            <button class="kmc-faq-q" onclick="kmcFaq(this)" type="button">
              <span>{{ __($faq->data_values->question ?? '') }}</span>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div class="kmc-faq-a"><p>{{ __($faq->data_values->answer ?? '') }}</p></div>
          </div>
          @endforeach
        @else
          @foreach([
            ['q'=>'How do you ensure the security of my transactions?','a'=>'We use secure account access, transaction checks, identity verification, and FINTRAC-compliant monitoring systems to protect every transfer.'],
            ['q'=>'What delivery options do you offer for international transfers?','a'=>'Delivery options include bank transfer, mobile money, and other supported payout channels depending on the destination country and corridor.'],
            ['q'=>'How can I track the status of my international money transfer?','a'=>'You can track your transfer through your account dashboard or contact support with your transaction reference for real-time assistance.'],
            ['q'=>'Are there loyalty programs or benefits for regular customers?','a'=>'We provide customer benefits and offers for active users. Contact support or subscribe to updates for current promotions.'],
            ['q'=>'What should I do if I encounter any issues with my transfer?','a'=>'Contact our support team through live chat, phone, or email. We respond in under 2 hours and are available 24/7 for urgent transfer issues.'],
          ] as $faq)
          <div class="kmc-faq-item">
            <button class="kmc-faq-q" onclick="kmcFaq(this)" type="button">
              <span>{{ __($faq['q']) }}</span>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div class="kmc-faq-a"><p>{{ __($faq['a']) }}</p></div>
          </div>
          @endforeach
        @endif
      </div>
    </div>
  </section>

</div>
@endsection

@push('script')
<script>
function kmcFaq(btn) {
  var item = btn.closest('.kmc-faq-item');
  var ans  = item.querySelector('.kmc-faq-a');
  var open = ans.classList.contains('open');
  document.querySelectorAll('.kmc-faq-a').forEach(function(el){ el.classList.remove('open'); });
  document.querySelectorAll('.kmc-faq-q').forEach(function(el){ el.classList.remove('open'); });
  if (!open) { ans.classList.add('open'); btn.classList.add('open'); }
}
</script>
@endpush
