@extends($activeTemplate . 'layouts.auth')
@section('title', trans('Email Verification'))
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
            <h2>One step <span>away</span></h2>
            <p>Email verification protects your account and keeps your transfers secure.</p>
        </div>

        <div class="km-feature-list">
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>End-to-End Encrypted</strong>
                    <span>Your verification code is delivered over a secure, encrypted channel.</span>
                </div>
            </div>
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>Check Spam Too</strong>
                    <span>If you don't see the email, check your spam or junk folder.</span>
                </div>
            </div>
            <div class="km-feature">
                <div class="km-feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="km-feature-text">
                    <strong>Code Expires in 10 Min</strong>
                    <span>For security, your code is only valid for 10 minutes after it's sent.</span>
                </div>
            </div>
        </div>

        <div class="km-testimonial">
            <div class="km-testimonial-quote">"</div>
            <p>Quick and easy — I verified my email in seconds and was sending money right away!</p>
            <div class="km-testimonial-author">
                <div class="km-avatar">TO</div>
                <div class="km-author-info">
                    <strong>Tunde O.</strong>
                    <span>Edmonton, Canada</span>
                    <div class="km-stars">★★★★★</div>
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

            <p class="km-panel-title">Verify your email</p>
            <p class="km-panel-subtitle">
                We sent a 6-digit code to <strong style="color:#0d1a14;">{{ auth()->user()->email }}</strong>
            </p>

            <div class="km-info-box" style="margin-top:16px;margin-bottom:24px;">
                Check your inbox and spam folder. The code expires in <strong>10 minutes</strong>.
            </div>

            <form action="{{ route('user.verify.email') }}" method="POST" id="email-verify-form">
                @csrf
                <input type="hidden" name="code" id="email-code-hidden">

                <div class="km-otp-row">
                    @for($i = 0; $i < 6; $i++)
                    <input inputmode="numeric" maxlength="1" type="text" data-index="{{ $i }}"
                        class="km-otp-input" autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}">
                    @endfor
                </div>

                @error('code')
                <span style="color:#A71B47;font-size:12px;display:block;margin-bottom:12px;text-align:center;">{{ $message }}</span>
                @enderror

                <button type="submit" class="km-btn km-btn-primary">
                    Verify Email →
                </button>
            </form>

            <p class="km-panel-subtitle" style="margin-top:16px;text-align:center;">
                <span id="km-countdown-wrap" style="color:rgba(13,26,20,0.55);font-size:13px;">
                    Resend code in <strong id="countdown" style="color:#0d1a14;">--</strong>s
                </span>
                <a href="{{ route('user.send.verify.code', 'email') }}" class="km-link" id="km-resend-link" style="display:none;font-size:13px;">
                    Resend code
                </a>
            </p>

            <div class="km-security-note" style="margin-top:24px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Your verification is encrypted and secure
            </div>

        </div>
    </main>

</div>

@push('script')
<script>
(function () {
    var boxes  = document.querySelectorAll('.km-otp-input');
    var hidden = document.getElementById('email-code-hidden');
    var form   = document.getElementById('email-verify-form');

    function sync() { hidden.value = Array.from(boxes).map(function(b){ return b.value; }).join(''); }

    boxes.forEach(function(el, idx) {
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
            if (e.target.value && idx < boxes.length - 1) boxes[idx + 1].focus();
            sync();
        });
        el.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !el.value && idx > 0) { boxes[idx-1].focus(); boxes[idx-1].value = ''; sync(); }
            if (e.key === 'ArrowLeft'  && idx > 0) boxes[idx-1].focus();
            if (e.key === 'ArrowRight' && idx < boxes.length-1) boxes[idx+1].focus();
        });
        el.addEventListener('paste', function(e) {
            e.preventDefault();
            var p = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'').slice(0,6);
            p.split('').forEach(function(c,i){ if(boxes[i]) boxes[i].value = c; });
            var next = Math.min(p.length, boxes.length-1);
            if (boxes[next]) boxes[next].focus();
            sync();
        });
    });

    form.addEventListener('submit', function(e) {
        sync();
        if (hidden.value.length !== 6) { e.preventDefault(); boxes[0].focus(); }
    });

    if (boxes[0]) boxes[0].focus();
})();

// Countdown
var distance = Number("{{ @$user->ver_code_send_at ? ($user->ver_code_send_at->addMinutes(2)->timestamp - time()) : 0 }}");
if (distance > 0) {
    var countdownEl = document.getElementById('countdown');
    if (countdownEl) countdownEl.innerHTML = distance;
    var x = setInterval(function() {
        distance--;
        if (countdownEl) countdownEl.innerHTML = distance;
        if (distance <= 0) {
            clearInterval(x);
            document.getElementById('km-countdown-wrap').style.display = 'none';
            document.getElementById('km-resend-link').style.display = 'inline';
        }
    }, 1000);
} else {
    document.getElementById('km-countdown-wrap').style.display = 'none';
    document.getElementById('km-resend-link').style.display = 'inline';
}
</script>
@endpush

@endsection
