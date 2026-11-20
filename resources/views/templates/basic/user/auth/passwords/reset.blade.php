@extends($activeTemplate . 'layouts.auth')
@section('title', trans('Reset Password'))
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
            <h2>Almost <span>there</span></h2>
            <p>Create a strong, unique password. All existing sessions will be signed out for your security.</p>
        </div>

        <div class="km-feature-list">
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>Step 3 of 3</strong>
                    <span>Last step — set a new password and you're back in.</span>
                </div>
            </div>
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>256-Bit Encrypted</strong>
                    <span>Your password is stored with bank-level encryption — never plain text.</span>
                </div>
            </div>
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>Min 8 Characters</strong>
                    <span>Use a mix of letters, numbers, and symbols for a stronger password.</span>
                </div>
            </div>
        </div>

        <div class="km-testimonial">
            <div class="km-testimonial-quote">"</div>
            <p>Back in my account in minutes. The process is smooth and I always feel secure.</p>
            <div class="km-testimonial-author">
                <div class="km-avatar">EO</div>
                <div class="km-author-info">
                    <strong>Emeka O.</strong>
                    <span>Calgary, Canada</span>
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

            <p class="km-panel-title">Set new password</p>
            <p class="km-panel-subtitle">
                Make it strong — don't reuse an old password.
            </p>

            <form method="POST" action="{{ route('user.password.update') }}" class="disableSubmission" style="margin-top:24px;">
                @csrf
                <input name="email" type="hidden" value="{{ $email }}">
                <input name="token" type="hidden" value="{{ $token }}">

                <div class="km-field">
                    <label class="km-label">New Password</label>
                    <div class="km-input-wrap">
                        <span class="km-input-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <input class="km-input has-icon has-trail @if(gs('secure_password')) secure-password @endif"
                            name="password" type="password" placeholder="Min 8 characters" required>
                        <button type="button" class="km-input-trail" onclick="kmTogglePwd(this)" aria-label="Show/hide password">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    @error('password')<span style="color:#A71B47;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span>@enderror
                </div>

                <div class="km-field">
                    <label class="km-label">Confirm Password</label>
                    <div class="km-input-wrap">
                        <span class="km-input-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <input class="km-input has-icon has-trail"
                            name="password_confirmation" type="password" placeholder="Repeat password" required>
                        <button type="button" class="km-input-trail" onclick="kmTogglePwd(this)" aria-label="Show/hide password">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="km-btn km-btn-primary" style="margin-top:8px;">
                    Reset Password →
                </button>
            </form>

            <p class="km-panel-subtitle" style="margin-top:16px;text-align:center;">
                <a href="{{ route('user.login') }}" class="km-link">Back to Sign In</a>
            </p>

            <div class="km-security-note" style="margin-top:24px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Your data is encrypted and stored securely
            </div>

        </div>
    </main>

</div>

@if(gs('secure_password'))
@push('script-lib')
    <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
@endpush
@endif

@endsection
