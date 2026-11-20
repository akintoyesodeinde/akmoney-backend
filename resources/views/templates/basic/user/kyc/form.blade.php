@extends($activeTemplate . 'layouts.auth')
@php
    $user = auth()->user();
@endphp
@section('title', trans($pageTitle ?? 'KYC Verification'))
@section('content')

@include($activeTemplate . 'user.auth._km_auth_shell')

@push('style')
<style>
/* ── Restyle viser-form fields inside km-panel ── */
.km-viser-wrap .row { display: flex; flex-wrap: wrap; margin: 0 -6px; }
.km-viser-wrap [class*="col-"] { padding: 0 6px; box-sizing: border-box; }
.km-viser-wrap .col-md-12 { width: 100%; }
.km-viser-wrap .col-md-6  { width: 50%; }
.km-viser-wrap .col-md-4  { width: 33.333%; }

.km-viser-wrap .form-group { margin-bottom: 14px; }

.km-viser-wrap label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: rgba(13,26,20,0.55);
    letter-spacing: 0.07em;
    text-transform: uppercase;
    margin-bottom: 5px;
    font-family: 'DM Sans', sans-serif;
}

.km-viser-wrap input[type="text"],
.km-viser-wrap input[type="email"],
.km-viser-wrap input[type="url"],
.km-viser-wrap input[type="number"],
.km-viser-wrap input[type="date"],
.km-viser-wrap input[type="time"],
.km-viser-wrap input[type="datetime-local"],
.km-viser-wrap textarea,
.km-viser-wrap select,
.km-viser-wrap input[type="file"] {
    width: 100%;
    box-sizing: border-box;
    padding: 11px 14px;
    font-size: 13px;
    font-family: 'DM Sans', sans-serif;
    border: 1.5px solid rgba(13,26,20,0.12);
    border-radius: 10px;
    background: #f7f5f0;
    color: #0d1a14;
    outline: none;
    transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
    -webkit-appearance: none;
    appearance: none;
    display: block;
    margin-bottom: 0;
    /* override Tailwind utilities that may partially apply */
    max-width: 100%;
}

.km-viser-wrap input:focus,
.km-viser-wrap textarea:focus,
.km-viser-wrap select:focus {
    border-color: #0F4650;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(15,70,80,0.08);
}

.km-viser-wrap input::placeholder,
.km-viser-wrap textarea::placeholder { color: rgba(13,26,20,0.35); }

.km-viser-wrap textarea { min-height: 90px; resize: vertical; }

.km-viser-wrap select {
    background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L6 6L11 1' stroke='%230d1a14' stroke-opacity='0.3' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    padding-right: 36px;
}

.km-viser-wrap input[type="file"] {
    cursor: pointer;
    font-size: 12px;
    padding: 9px 14px;
}

.km-viser-wrap pre {
    font-size: 11px;
    color: rgba(13,26,20,0.40);
    margin: 4px 0 0;
    font-family: 'DM Sans', sans-serif;
    white-space: normal;
}

.km-viser-wrap .form-check { display: flex; align-items: center; gap: 6px; margin-bottom: 6px; }
.km-viser-wrap .form-check-input { width: 15px; height: 15px; accent-color: #0F4650; cursor: pointer; }
.km-viser-wrap .form-check-label { font-size: 13px; color: #0d1a14; font-family: 'DM Sans', sans-serif; text-transform: none; letter-spacing: 0; }
.km-viser-wrap .d-flex { display: flex; }
.km-viser-wrap .gap-3 { gap: 12px; }
.km-viser-wrap .flex-wrap { flex-wrap: wrap; }

/* select2 overrides */
.km-viser-wrap .select2-container { width: 100% !important; }
.km-viser-wrap .select2-container--default .select2-selection--single {
    height: 42px;
    border: 1.5px solid rgba(13,26,20,0.12);
    border-radius: 10px;
    background: #f7f5f0;
}
.km-viser-wrap .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 40px;
    padding-left: 14px;
    color: #0d1a14;
    font-size: 13px;
    font-family: 'DM Sans', sans-serif;
}
.km-viser-wrap .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px;
    right: 10px;
}

@media (max-width: 600px) {
    .km-viser-wrap .col-md-6,
    .km-viser-wrap .col-md-4 { width: 100%; }
}
</style>
@endpush

<div class="km-auth-shell">

    {{-- ── SIDEBAR ── --}}
    <aside class="km-sidebar">
        <a href="{{ url('/') }}" class="km-logo">
            <img src="{{ siteLogo() }}" alt="{{ gs('site_name') }}" class="km-logo-img">
            <span class="km-logo-text">{!! siteNameHtml() !!}</span>
        </a>

        <div class="km-sidebar-hero">
            <h2>Verify your <span>identity</span></h2>
            <p>Complete your KYC to unlock full transfer limits and keep your account compliant.</p>
        </div>

        <div class="km-feature-list">
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>FINTRAC Compliant</strong>
                    <span>We are a registered MSB in Canada — your data is handled lawfully.</span>
                </div>
            </div>
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>Quick Review</strong>
                    <span>Most verifications are reviewed within 1 business day.</span>
                </div>
            </div>
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>Encrypted &amp; Private</strong>
                    <span>Your documents are encrypted and only used for identity checks.</span>
                </div>
            </div>
        </div>

        <div class="km-testimonial">
            <div class="km-testimonial-quote">"</div>
            <p>Verification was fast and hassle-free. I was sending money home within hours.</p>
            <div class="km-testimonial-author">
                <div class="km-avatar">SI</div>
                <div class="km-author-info">
                    <strong>Samuel I.</strong>
                    <span>Winnipeg, Canada</span>
                    <div class="km-stars">★★★★★</div>
                </div>
            </div>
        </div>
    </aside>

    {{-- ── RIGHT PANEL ── --}}
    <main class="km-panel">
        <div class="km-form-box" style="max-width:600px;">

            <div style="width:56px;height:56px;background:rgba(167,27,71,0.08);border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#A71B47" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            </div>

            <p class="km-panel-title">{{ __($pageTitle ?? 'Verify your identity') }}</p>
            <p class="km-panel-subtitle">
                Please fill in the details below accurately. All fields marked with <span style="color:#A71B47;">*</span> are required.
            </p>

            <form action="{{ route('user.kyc.submit') }}" method="post" enctype="multipart/form-data" class="disableSubmission viser-form" style="margin-top:24px;">
                @csrf
                <div class="km-viser-wrap">
                    @if ($user->type == \App\Constants\Status::BUSINESS_USER)
                        <x-viser-form identifier="act" identifierValue="business-user.kyc" />
                    @else
                        <x-viser-form identifier="act" identifierValue="personal-user.kyc" />
                    @endif
                </div>

                <div style="margin-top:8px;">
                    <button type="submit" class="km-btn km-btn-primary">
                        Submit Verification →
                    </button>
                </div>
            </form>

            <div class="km-security-note" style="margin-top:24px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Your documents are encrypted and handled in compliance with FINTRAC regulations
            </div>

        </div>
    </main>

</div>

@endsection
