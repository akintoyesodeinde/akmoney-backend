@extends($activeTemplate . 'layouts.auth')
@section('title', trans('Forgot Password'))
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
            <h2>We'll get you <span>back in</span></h2>
            <p>Enter your email or username and we'll send you a reset code — it expires in 60 minutes.</p>
        </div>

        <div class="km-feature-list">
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>Check Your Inbox</strong>
                    <span>We'll email your reset code to your registered address.</span>
                </div>
            </div>
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>Valid for 60 Minutes</strong>
                    <span>For security, your reset code expires within 60 minutes.</span>
                </div>
            </div>
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>256-Bit Encrypted</strong>
                    <span>Your account recovery is protected with bank-level encryption.</span>
                </div>
            </div>
        </div>

        <div class="km-testimonial">
            <div class="km-testimonial-quote">"</div>
            <p>Reset was instant — I was back sending money in under two minutes.</p>
            <div class="km-testimonial-author">
                <div class="km-avatar">NB</div>
                <div class="km-author-info">
                    <strong>Ngozi B.</strong>
                    <span>Montreal, Canada</span>
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

            <p class="km-panel-title">Forgot password?</p>
            <p class="km-panel-subtitle">
                Enter your email or username and we'll send you a reset code.
            </p>

            <form class="disableSubmission" method="POST" action="{{ route('user.password.email') }}" style="margin-top:24px;">
                @csrf
                <div class="km-field">
                    <label class="km-label">Email or Username</label>
                    <div class="km-input-wrap">
                        <span class="km-input-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        </span>
                        <input type="text" class="km-input has-icon" name="value"
                            value="{{ old('value') }}"
                            placeholder="you@example.com or username"
                            required autofocus>
                    </div>
                    @error('value')<span style="color:#A71B47;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span>@enderror
                </div>

                <x-captcha />

                <button type="submit" class="km-btn km-btn-primary" style="margin-top:8px;">
                    Send Reset Code →
                </button>
            </form>

            <p class="km-panel-subtitle" style="margin-top:16px;text-align:center;">
                Remember your password?
                <a href="{{ route('user.login') }}" class="km-link">Back to Sign In</a>
            </p>

            <div class="km-security-note" style="margin-top:24px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Your data is encrypted and stored securely
            </div>

        </div>
    </main>

</div>

@endsection
