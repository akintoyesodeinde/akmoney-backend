@extends($activeTemplate . 'layouts.auth')
@section('title', trans('2FA Verification'))
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
            <h2>One more step to <span>keep you safe</span></h2>
            <p>Two-factor authentication ensures only you can access your account and transfers.</p>
        </div>

        <div class="km-feature-list">
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>Two-Factor Protection</strong>
                    <span>Even if your password is compromised, your account stays secure.</span>
                </div>
            </div>
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>Bank-Level Security</strong>
                    <span>The same security standards used by financial institutions worldwide.</span>
                </div>
            </div>
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>Trusted by Thousands</strong>
                    <span>Customers across Canada trust us to keep their transfers safe.</span>
                </div>
            </div>
        </div>

        <div class="km-testimonial">
            <div class="km-testimonial-quote">"</div>
            <p>The 2FA setup was so simple and now I feel completely safe sending money abroad.</p>
            <div class="km-testimonial-author">
                <div class="km-avatar">KA</div>
                <div class="km-author-info">
                    <strong>Kofi A.</strong>
                    <span>Vancouver, Canada</span>
                    <div class="km-stars">★★★★★</div>
                </div>
            </div>
        </div>
    </aside>

    {{-- ── RIGHT PANEL ── --}}
    <main class="km-panel">
        <div class="km-form-box">

            <div style="width:56px;height:56px;background:rgba(167,27,71,0.08);border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#A71B47" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>

            <p class="km-panel-title">Two-factor auth</p>
            <p class="km-panel-subtitle">Choose your method and enter your 6-digit code</p>

            {{-- Method selector --}}
            @php $method = $method ?? 'totp'; @endphp

            <div style="display:flex;flex-direction:column;gap:8px;margin:20px 0;">

                <div style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:10px;border:1.5px solid {{ $method==='totp' ? '#A71B47' : 'rgba(13,26,20,0.12)' }};background:{{ $method==='totp' ? 'rgba(167,27,71,0.04)' : '#fafaf9' }};color:{{ $method==='totp' ? '#A71B47' : 'rgba(13,26,20,0.4)' }};font-weight:600;font-size:13px;font-family:'DM Sans',system-ui,sans-serif;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                    Authenticator App
                    @if($method==='totp')
                    <span style="margin-left:auto;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    @endif
                </div>

                <div style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:10px;border:1.5px solid {{ $method==='sms' ? '#A71B47' : 'rgba(13,26,20,0.12)' }};background:{{ $method==='sms' ? 'rgba(167,27,71,0.04)' : '#fafaf9' }};color:{{ $method==='sms' ? '#A71B47' : 'rgba(13,26,20,0.4)' }};font-weight:600;font-size:13px;font-family:'DM Sans',system-ui,sans-serif;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a2 2 0 012-2.18h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L9.91 14a16 16 0 006.09 6.09l.31-.31a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                    SMS Code
                    @if($method==='sms')
                    <span style="margin-left:auto;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    @endif
                </div>

                <div style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:10px;border:1.5px solid {{ $method==='email' ? '#A71B47' : 'rgba(13,26,20,0.12)' }};background:{{ $method==='email' ? 'rgba(167,27,71,0.04)' : '#fafaf9' }};color:{{ $method==='email' ? '#A71B47' : 'rgba(13,26,20,0.4)' }};font-weight:600;font-size:13px;font-family:'DM Sans',system-ui,sans-serif;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    Email Code
                    @if($method==='email')
                    <span style="margin-left:auto;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    @endif
                </div>

            </div>

            {{-- Helper text --}}
            @if($method === 'sms')
            <p class="km-panel-subtitle" style="margin-bottom:4px;">
                A code was sent to <strong style="color:#0d1a14;">+{{ $user->dial_code }}{{ $user->mobile }}</strong>
            </p>
            @elseif($method === 'email')
            <p class="km-panel-subtitle" style="margin-bottom:4px;">
                A code was sent to <strong style="color:#0d1a14;">{{ $user->email }}</strong>
            </p>
            @else
            <p class="km-panel-subtitle" style="margin-bottom:4px;">Enter your 6-digit authenticator code</p>
            @endif

            {{-- OTP form --}}
            @php
                $formAction = match($method) {
                    'sms'   => route('user.2fa.verify.sms'),
                    'email' => route('user.2fa.verify.email'),
                    default => route('user.2fa.verify'),
                };
            @endphp

            <form action="{{ $formAction }}" method="POST" id="tfa-form">
                @csrf
                <input type="hidden" name="code" id="tfa-code-hidden">

                <div class="km-otp-row" style="margin-top:16px;">
                    @for($i = 0; $i < 6; $i++)
                    <input inputmode="numeric" maxlength="1" type="text" data-index="{{ $i }}"
                        class="km-otp-input tfa-digit" autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}">
                    @endfor
                </div>

                @if($errors->has('code'))
                <span style="color:#A71B47;font-size:12px;display:block;margin-bottom:12px;text-align:center;">{{ $errors->first('code') }}</span>
                @endif

                <button type="submit" class="km-btn km-btn-primary">
                    Verify →
                </button>
            </form>

            @if(in_array($method, ['sms', 'email']))
            <p class="km-panel-subtitle" style="margin-top:14px;text-align:center;">
                Didn't receive a code?
                <button type="button" id="resend-btn" onclick="resend2faCode()" class="km-link" style="background:none;border:none;cursor:pointer;padding:0;font-size:13px;">
                    Resend
                </button>
            </p>
            @else
            <p class="km-panel-subtitle" style="margin-top:14px;text-align:center;">
                <a href="{{ route('user.login') }}" class="km-link">Back to Sign In</a>
            </p>
            @endif

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
    var digits = document.querySelectorAll('.tfa-digit');
    var hidden = document.getElementById('tfa-code-hidden');
    var form   = document.getElementById('tfa-form');

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

function resend2faCode() {
    var btn = document.getElementById('resend-btn');
    btn.disabled = true;
    btn.textContent = 'Sending…';
    fetch('{{ route("user.2fa.resend") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
    })
    .then(function(r){ return r.json(); })
    .then(function() {
        btn.textContent = 'Sent!';
        setTimeout(function() { btn.textContent = 'Resend'; btn.disabled = false; }, 30000);
    })
    .catch(function() { btn.textContent = 'Resend'; btn.disabled = false; });
}
</script>
@endpush

@endsection
