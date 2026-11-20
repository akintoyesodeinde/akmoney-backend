@extends($activeTemplate . 'layouts.auth')
@section('title', trans('Login'))
@section('content')

@include($activeTemplate . 'user.auth._km_auth_shell')

<div class="km-auth-shell">

    {{-- ── SIDEBAR ── --}}
    <aside class="km-sidebar">
        <a href="{{ url('/') }}" class="km-logo">
            <img src="{{ siteLogo() }}" alt="{{ gs('site_name') }}" class="km-logo-img">
        </a>

        <div class="km-sidebar-hero">
            <h2>Welcome back to <span>{!! siteNameHtml() !!}</span></h2>
            <p>Log in to your account to continue sending money to your loved ones across the world.</p>
        </div>

        <div class="km-feature-list">
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>Secure &amp; Protected</strong>
                    <span>Bank-level security keeps your account and transactions safe.</span>
                </div>
            </div>
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>Fast &amp; Convenient</strong>
                    <span>Send money in minutes to countries worldwide.</span>
                </div>
            </div>
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>Trusted by Thousands</strong>
                    <span>Join customers who trust {!! siteNameHtml() !!} for global transfers.</span>
                </div>
            </div>
        </div>

        <div class="km-testimonial">
            <div class="km-testimonial-quote">"</div>
            <p>{!! siteNameHtml() !!} makes it so easy to send money home. Fast, secure and reliable!</p>
            <div class="km-testimonial-author">
                <div class="km-avatar">AK</div>
                <div class="km-author-info">
                    <strong>Amara K.</strong>
                    <span>Toronto, Canada</span>
                    <div class="km-stars">★★★★★</div>
                </div>
            </div>
        </div>
    </aside>

    {{-- ── RIGHT PANEL ── --}}
    <main class="km-panel">
        <div class="km-form-box">

            <p class="km-panel-title">Log in to your account</p>
            <p class="km-panel-subtitle">
                Don't have an account?
                <a href="{{ route('user.register') }}">Sign up</a>
            </p>

            <form action="{{ route('user.login') }}" method="post">
                @csrf

                <div class="km-field">
                    <label class="km-label">Email or Username</label>
                    <div class="km-input-wrap">
                        <span class="km-input-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        </span>
                        <input class="km-input has-icon" type="text" name="username"
                            placeholder="Enter your email or username"
                            value="{{ old('username') }}" autofocus>
                    </div>
                    @error('username')<span style="color:#A71B47;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span>@enderror
                </div>

                <div class="km-field" style="margin-bottom:8px;">
                    <label class="km-label">Password</label>
                    <div class="km-input-wrap">
                        <span class="km-input-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <input class="km-input has-icon has-trail" type="password" name="password"
                            placeholder="Enter your password">
                        <button type="button" class="km-input-trail" onclick="kmTogglePwd(this)" aria-label="Show/hide password">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    @error('password')<span style="color:#A71B47;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span>@enderror
                </div>

                <div class="km-forgot" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(13,26,20,0.6);cursor:pointer;">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} style="accent-color:#276645;">
                        Remember me
                    </label>
                    <a href="{{ route('user.password.request') }}" class="km-link">Forgot Password?</a>
                </div>

                <x-recaptcha />

                <button type="submit" class="km-btn km-btn-primary">
                    Log In →
                </button>

                @if(App\Models\Extension::where('act', 'google-auth')->where('status', 1)->exists())
                <div class="km-divider">or continue with</div>
                <a href="{{ route('user.redirectToGoogle') }}" class="km-sso-btn">
                    <svg class="km-sso-icon" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    Continue with Google
                </a>
                @endif

            </form>

            <div class="km-security-note">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Your data is encrypted and stored securely
            </div>

        </div>
    </main>

</div>

@endsection
