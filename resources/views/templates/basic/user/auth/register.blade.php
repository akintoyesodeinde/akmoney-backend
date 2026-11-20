@extends($activeTemplate . 'layouts.auth')
@section('title', trans('Register'))
@section('content')

@include($activeTemplate . 'user.auth._km_auth_shell')

@push('style')
<style>
.km-field-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}
@media(max-width: 600px) {
    .km-field-row { grid-template-columns: 1fr; }
}
.km-section-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .10em;
    text-transform: uppercase;
    color: rgba(13,26,20,0.4);
    margin: 20px 0 14px;
}
.km-panel-scrollable {
    padding: 40px 44px;
    overflow-y: auto;
    /* max-height: 100vh; */
}
@media(max-width: 900px) {
    .km-panel-scrollable { padding: 32px 20px; }
}
</style>
@endpush

<div class="km-auth-shell">

    {{-- ── SIDEBAR ── --}}
    <aside class="km-sidebar">
        <a href="{{ url('/') }}" class="km-logo">
            <img src="{{ siteLogo() }}" alt="{{ gs('site_name') }}" class="km-logo-img">
        </a>

        <div class="km-sidebar-hero">
            <h2>Start sending money <span>home today</span></h2>
            <p>Create your account in minutes and join customers who trust {!! siteNameHtml() !!} for fast, reliable transfers.</p>
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
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>Zero Hidden Fees</strong>
                    <span>See exactly what you pay before every transfer.</span>
                </div>
            </div>
        </div>

        <div class="km-testimonial">
            <div class="km-testimonial-quote">"</div>
            <p>Setting up was so quick. I had my account ready and sent money the same day!</p>
            <div class="km-testimonial-author">
                <div class="km-avatar">OB</div>
                <div class="km-author-info">
                    <strong>Oluwaseun B.</strong>
                    <span>Calgary, Canada</span>
                    <div class="km-stars">★★★★★</div>
                </div>
            </div>
        </div>
    </aside>

    {{-- ── RIGHT PANEL ── --}}
    <main class="km-panel km-panel-scrollable" style="align-items:flex-start;">
        <div class="km-form-box" style="max-width:560px;width:100%;">

            <p class="km-panel-title">Create your account</p>
            <p class="km-panel-subtitle">
                Already have an account?
                <a href="{{ route('user.login') }}">Sign in</a>
            </p>

            @if($errors->any())
            <div class="km-alert km-alert-danger" style="margin-bottom:16px;">
                <div class="km-alert-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                    <ul style="margin:0;padding-left:16px;font-size:13px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <form action="{{ route('user.register.submit') }}" method="post">
                @csrf
                <input name="type" type="hidden" value="0">

                <p class="km-section-label">Personal Information</p>

                <div class="km-field-row">
                    <div class="km-field">
                        <label class="km-label">First Name *</label>
                        <div class="km-input-wrap">
                            <span class="km-input-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg></span>
                            <input class="km-input has-icon" type="text" name="firstname" placeholder="First name" value="{{ old('firstname') }}" required>
                        </div>
                        @error('firstname')<span style="color:#A71B47;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span>@enderror
                    </div>
                    <div class="km-field">
                        <label class="km-label">Last Name *</label>
                        <div class="km-input-wrap">
                            <span class="km-input-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg></span>
                            <input class="km-input has-icon" type="text" name="lastname" placeholder="Last name" value="{{ old('lastname') }}" required>
                        </div>
                        @error('lastname')<span style="color:#A71B47;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="km-field-row">
                    <div class="km-field">
                        <label class="km-label">Username *</label>
                        <div class="km-input-wrap">
                            <span class="km-input-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 20c2-4 14-4 16 0"/><circle cx="12" cy="8" r="4"/></svg></span>
                            <input class="km-input has-icon checkUser" type="text" name="username" placeholder="Choose a username" value="{{ old('username') }}" required>
                        </div>
                        <span style="color:#A71B47;font-size:12px;" class="usernameExist"></span>
                        @error('username')<span style="color:#A71B47;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span>@enderror
                    </div>
                    <div class="km-field">
                        <label class="km-label">Email Address *</label>
                        <div class="km-input-wrap">
                            <span class="km-input-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg></span>
                            <input class="km-input has-icon checkUser" type="email" name="email" placeholder="you@example.com" value="{{ old('email') }}" required>
                        </div>
                        <span style="color:#A71B47;font-size:12px;" class="emailExist"></span>
                        @error('email')<span style="color:#A71B47;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span>@enderror
                    </div>
                </div>

                <p class="km-section-label">Mobile Number</p>

                <div class="km-field">
                    <div style="display:flex;gap:10px;">
                        <div style="flex:0 0 130px;">
                            <select id="phone_code_select" class="km-select" style="padding-right:10px;">
                                @foreach($countries as $iso2 => $item)
                                <option value="{{ $item->dial_code }}"
                                    data-iso2="{{ strtolower($iso2) }}"
                                    {{ (old('country_code', 'CA') == $iso2) ? 'selected' : '' }}>
                                    +{{ $item->dial_code }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div style="flex:1;">
                            <input id="mobile" name="mobile" type="tel" class="km-input" placeholder="(416) 555-0100" value="{{ old('mobile') }}">
                        </div>
                    </div>
                    <input type="hidden" name="mobile_code" id="phone_code" value="{{ old('mobile_code', '1') }}">
                    <input type="hidden" name="country_code" id="country_code" value="{{ old('country_code') ? strtoupper(old('country_code')) : 'CA' }}">
                    @error('mobile')<span style="color:#A71B47;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span>@enderror
                </div>

                <p class="km-section-label">Location</p>

                <div class="km-field-row">
                    <div class="km-field">
                        <label class="km-label">Country *</label>
                        <select name="country" class="km-select">
                            @foreach($countries as $key => $item)
                            <option value="{{ $item->country }}" data-mobile_code="{{ $item->dial_code }}" data-code="{{ $key }}" {{ old('country') == $item->country ? 'selected' : '' }}>{{ $item->country }}</option>
                            @endforeach
                        </select>
                        @error('country')<span style="color:#A71B47;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span>@enderror
                    </div>
                    <div class="km-field">
                        <label class="km-label">State / Province</label>
                        <input class="km-input" type="text" name="state" placeholder="Ontario" value="{{ old('state') }}">
                    </div>
                </div>

                <div class="km-field-row">
                    <div class="km-field">
                        <label class="km-label">City</label>
                        <input class="km-input" type="text" name="city" placeholder="Toronto" value="{{ old('city') }}">
                    </div>
                    <div class="km-field">
                        <label class="km-label">ZIP / Postal Code</label>
                        <input class="km-input" type="text" name="zip" placeholder="M5V 2T6" value="{{ old('zip') }}">
                    </div>
                </div>

                <div class="km-field-row">
                    <div class="km-field">
                        <label class="km-label">Date of Birth</label>
                        <input class="km-input" type="date" name="dob" max="{{ date('Y-m-d', strtotime('-18 years')) }}" value="{{ old('dob') }}">
                    </div>
                    <div class="km-field">
                        <label class="km-label">Residential Address</label>
                        <input class="km-input" type="text" name="address" placeholder="123 Main St" value="{{ old('address') }}">
                    </div>
                </div>

                <p class="km-section-label">Security</p>

                <div class="km-field-row">
                    <div class="km-field">
                        <label class="km-label">Password *</label>
                        <div class="km-input-wrap">
                            <span class="km-input-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                            <input class="km-input has-icon has-trail @if(gs('secure_password')) secure-password @endif" type="password" name="password" placeholder="Min 8 characters" required>
                            <button type="button" class="km-input-trail" onclick="kmTogglePwd(this)" aria-label="Toggle visibility">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        @error('password')<span style="color:#A71B47;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span>@enderror
                    </div>
                    <div class="km-field">
                        <label class="km-label">Confirm Password *</label>
                        <div class="km-input-wrap">
                            <span class="km-input-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                            <input class="km-input has-icon has-trail" type="password" name="password_confirmation" placeholder="Repeat password" required>
                            <button type="button" class="km-input-trail" onclick="kmTogglePwd(this)" aria-label="Toggle visibility">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                @if(gs('agree'))
                <label class="km-check-row" for="agree_checkbox">
                    <input type="checkbox" name="agree" id="agree_checkbox" style="margin-top:2px;accent-color:#276645;">
                    <span>I agree to the <a href="{{ route('policy.pages', 'terms-of-service') }}" target="_blank" style="color:#A71B47;font-weight:600;text-decoration:none;">Terms of Service</a> and <a href="{{ route('policy.pages', 'privacy-policy') }}" target="_blank" style="color:#A71B47;font-weight:600;text-decoration:none;">Privacy Policy</a></span>
                </label>
                @endif

                <x-recaptcha />

                <button type="submit" class="km-btn km-btn-primary" style="margin-top:8px;">
                    Create Account →
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
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Your data is encrypted and stored securely
            </div>

        </div>
    </main>

</div>

@push('js-lib')
@endpush
@push('script')
<script>
// Sync phone code select → hidden inputs
(function(){
    var sel = document.getElementById('phone_code_select');
    if (sel) {
        sel.addEventListener('change', function(){
            document.getElementById('phone_code').value    = this.value;
            var opt = this.options[this.selectedIndex];
            if (opt) document.getElementById('country_code').value = (opt.dataset.iso2 || '').toUpperCase();
        });
    }
})();
</script>
@endpush

@endsection
