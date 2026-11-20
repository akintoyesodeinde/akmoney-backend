@extends($activeTemplate . 'layouts.auth')
@section('title', trans('Verify Code'))
@section('content')

@include($activeTemplate . 'user.auth._km_auth_shell')

<div class="km-auth-shell">

    {{-- ── SIDEBAR ── --}}
    <aside class="km-sidebar">
        <a href="{{ url('/') }}" class="km-logo">
            <img src="{{ siteLogo() }}" alt="{{ gs('site_name') }}" class="km-logo-img">
            <span class="km-logo-text">{!! siteNameHtml() !!}</span>
        </a>

        <div class="km-sidebar-hero">
            <h2>Check your <span>inbox</span></h2>
            <p>We've sent a 6-digit verification code to your email address to confirm your identity.</p>
        </div>

        <div class="km-feature-list">
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>Step 2 of 3</strong>
                    <span>Confirm your identity — then you'll set your new password.</span>
                </div>
            </div>
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>Valid for 60 Minutes</strong>
                    <span>Your reset code is only valid for a limited time.</span>
                </div>
            </div>
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>Check Spam Too</strong>
                    <span>Didn't see it? Check your spam or junk mail folder.</span>
                </div>
            </div>
        </div>

        <div style="margin-top:auto;padding:20px 28px;background:rgba(255,255,255,0.06);border-radius:14px;border:1px solid rgba(255,255,255,0.1);">
            <div style="display:flex;gap:12px;align-items:center;margin-bottom:8px;">
                <div style="width:32px;height:32px;border-radius:50%;background:rgba(167,27,71,0.25);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#A71B47;">256</div>
                <div>
                    <div style="font-size:12px;font-weight:700;color:#fff;">Bit Encrypted</div>
                    <div style="font-size:11px;color:rgba(255,255,255,0.45);">Bank-level security</div>
                </div>
            </div>
        </div>
    </aside>

    {{-- ── RIGHT PANEL ── --}}
    <main class="km-panel">
        <div class="km-form-box">

            <div style="width:56px;height:56px;background:rgba(167,27,71,0.08);border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#A71B47" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
            </div>

            <p class="km-panel-title">Enter reset code</p>
            <p class="km-panel-subtitle">
                Sent to <strong style="color:#0d1a14;">{{ showEmailAddress($email) }}</strong>
            </p>

            <div class="km-info-box" style="margin-top:16px;margin-bottom:24px;">
                Check your inbox and spam folder. The code expires in <strong>60 minutes</strong>.
            </div>

            <form action="{{ route('user.password.verify.code') }}" method="POST" class="disableSubmission" id="pw-code-form">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <input type="hidden" name="code" id="pw-code-hidden">

                <div class="km-otp-row">
                    @for($i = 0; $i < 6; $i++)
                    <input inputmode="numeric" maxlength="1" type="text" data-index="{{ $i }}"
                        class="km-otp-input pw-digit" autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}">
                    @endfor
                </div>

                @error('code')
                <span style="color:#A71B47;font-size:12px;display:block;margin-bottom:12px;text-align:center;">{{ $message }}</span>
                @enderror

                <button type="submit" class="km-btn km-btn-primary">
                    Verify Code →
                </button>
            </form>

            <p class="km-panel-subtitle" style="margin-top:16px;text-align:center;">
                Didn't receive it?
                <a href="{{ route('user.password.request') }}" class="km-link">Try again</a>
            </p>

            <div class="km-security-note" style="margin-top:24px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Your data is encrypted and stored securely
            </div>

        </div>
    </main>

</div>

@push('script')
<script>
(function () {
    var digits = document.querySelectorAll('.pw-digit');
    var hidden = document.getElementById('pw-code-hidden');
    var form   = document.getElementById('pw-code-form');

    function sync() { hidden.value = Array.from(digits).map(function(d){ return d.value; }).join(''); }

    digits.forEach(function(el, idx) {
        el.addEventListener('focus', function() {
            el.style.borderColor = '#A71B47';
            el.style.background  = '#fff';
            el.style.boxShadow   = '0 0 0 3px rgba(167,27,71,0.12)';
        });
        el.addEventListener('blur', function() {
            el.style.borderColor = el.value ? '#A71B47' : 'var(--ink-12)';
            el.style.background  = 'var(--cream)';
            el.style.boxShadow   = 'none';
        });
        el.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g,'').slice(-1);
            if (e.target.value && idx < digits.length - 1) digits[idx + 1].focus();
            sync();
        });
        el.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !el.value && idx > 0) { digits[idx-1].focus(); digits[idx-1].value = ''; sync(); }
            if (e.key === 'ArrowLeft'  && idx > 0) digits[idx-1].focus();
            if (e.key === 'ArrowRight' && idx < digits.length-1) digits[idx+1].focus();
        });
        el.addEventListener('paste', function(e) {
            e.preventDefault();
            var p = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'').slice(0,6);
            p.split('').forEach(function(c,i){ if(digits[i]) digits[i].value = c; });
            var next = Math.min(p.length, digits.length-1);
            if (digits[next]) digits[next].focus();
            sync();
        });
    });

    form.addEventListener('submit', function(e) {
        sync();
        if (hidden.value.length !== 6) { e.preventDefault(); if (digits[0]) digits[0].focus(); }
    });

    if (digits[0]) digits[0].focus();
})();
</script>
@endpush

@endsection
