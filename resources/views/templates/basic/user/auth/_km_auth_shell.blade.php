{{--
    Shared km-auth-shell CSS + sidebar partial.
    Include at the top of every auth page with:
      @include($activeTemplate . 'user.auth._km_auth_shell')
    Then use the .km-auth-shell, .km-sidebar, .km-panel structure.
--}}
@push('style')
<style>
/* ── AkMoney Auth Shell ── */
.km-auth-shell *, .km-auth-shell *::before, .km-auth-shell *::after {
    box-sizing: border-box; margin: 0; padding: 0;
}

.km-auth-shell {
    --teal-950: #0b1f1a;
    --teal-900: #0F4650;
    --teal-800: #163828;
    --teal-700: #1e4f35;
    --teal-600: #276645;
    --teal-400: #3d9b68;
    --teal-200: #8ecfae;
    --teal-100: #c3e8d4;
    --teal-50:  #eef8f2;
    --red-900:  #4a0d15;
    --red-800:  #6b1220;
    --red-700:  #8b1a2b;
    --red-600:  #A71B47;
    --red-500:  #c0253c;
    --red-400:  #d43050;
    --red-200:  #f0a0b0;
    --red-100:  #f8d5dc;
    --cream:    #f7f5f0;
    --white:    #ffffff;
    --ink:      #0d1a14;
    --ink-60:   rgba(13,26,20,0.6);
    --ink-30:   rgba(13,26,20,0.3);
    --ink-12:   rgba(13,26,20,0.12);
    --font-display: 'Instrument Serif', Georgia, serif;
    --font-body:    'DM Sans', system-ui, sans-serif;
    --radius-sm: 8px;
    --radius-md: 14px;
    --radius-lg: 22px;
    --shadow-card: 0 2px 24px rgba(13,26,20,0.10), 0 0 0 0.5px rgba(13,26,20,0.07);

    font-family: var(--font-body);
    background: var(--cream);
    color: var(--ink);
    min-height: 100vh;
    display: grid;
    grid-template-columns: 420px 1fr;
}

/* ── SIDEBAR ── */
.km-sidebar {
    background: var(--teal-900);
    padding: 40px 44px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
}
.km-sidebar::before {
    content: '';
    position: absolute;
    top: -120px; right: -120px;
    width: 420px; height: 420px;
    border-radius: 50%;
    border: 1.5px dashed rgba(255,255,255,0.08);
    pointer-events: none;
}
.km-sidebar::after {
    content: '';
    position: absolute;
    bottom: -80px; left: -80px;
    width: 300px; height: 300px;
    border-radius: 50%;
    border: 1px dashed rgba(255,255,255,0.05);
    pointer-events: none;
}

.km-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--white);
    font-family: var(--font-body);
    font-weight: 600;
    font-size: 17px;
    text-decoration: none;
    position: relative;
    z-index: 1;
}
.km-logo-img { width: 72px; height: 72px; object-fit: contain; }
.km-logo-text { display: none; }

.km-sidebar-hero { position: relative; z-index: 1; }
.km-sidebar-hero h2 {
    font-family: var(--font-display);
    font-size: 38px;
    line-height: 1.1;
    color: var(--white);
    font-weight: 400;
}
.km-sidebar-hero h2 span { color: var(--red-600); font-style: italic; }
.km-sidebar-hero p {
    margin-top: 14px;
    font-size: 14px;
    line-height: 1.65;
    color: var(--teal-200);
    max-width: 300px;
}

.km-feature-list {
    display: flex;
    flex-direction: column;
    gap: 18px;
    position: relative;
    z-index: 1;
}
.km-feature { display: flex; align-items: flex-start; gap: 14px; }
.km-feature-icon {
    width: 38px; height: 38px;
    background: rgba(255,255,255,0.07);
    border: 0.5px solid rgba(255,255,255,0.10);
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: var(--teal-200);
}
.km-feature-text strong {
    display: block;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--white);
    margin-bottom: 2px;
}
.km-feature-text span { font-size: 12.5px; color: var(--teal-200); line-height: 1.5; }

.km-testimonial {
    background: rgba(255,255,255,0.05);
    border: 0.5px solid rgba(255,255,255,0.08);
    border-radius: var(--radius-md);
    padding: 16px 18px;
    position: relative;
    z-index: 1;
}
.km-testimonial-quote {
    color: var(--red-600);
    font-size: 28px;
    font-family: var(--font-display);
    line-height: 1;
    margin-bottom: 6px;
}
.km-testimonial p { font-size: 13px; color: var(--teal-100); line-height: 1.55; font-style: italic; }
.km-testimonial-author { display: flex; align-items: center; gap: 10px; margin-top: 12px; }
.km-avatar {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: var(--teal-700);
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 600;
    color: var(--teal-100);
    flex-shrink: 0;
    border: 1.5px solid var(--teal-600);
}
.km-author-info strong { display: block; font-size: 13px; font-weight: 600; color: var(--white); }
.km-author-info span { font-size: 12px; color: var(--teal-200); }
.km-stars { color: var(--red-600); font-size: 11px; letter-spacing: 1px; }

/* ── RIGHT PANEL ── */
.km-panel {
    background: var(--white);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 48px;
    position: relative;
    overflow-y: auto;
}
.km-form-box { width: 100%; max-width: 480px; }

.km-panel-title {
    font-family: var(--font-display);
    font-size: 34px;
    font-weight: 400;
    color: var(--ink);
    line-height: 1.15;
    margin-bottom: 6px;
}
.km-panel-subtitle {
    font-size: 14px;
    color: var(--ink-60);
    margin-bottom: 32px;
    line-height: 1.5;
}
.km-panel-subtitle a, .km-link {
    color: var(--red-600);
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
}
.km-panel-subtitle a:hover, .km-link:hover { text-decoration: underline; }

/* ── FORM ELEMENTS ── */
.km-field { margin-bottom: 18px; }
.km-label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: var(--ink);
    margin-bottom: 7px;
    letter-spacing: 0.1px;
}
.km-input-wrap { position: relative; display: flex; align-items: center; }
.km-input-icon {
    position: absolute; left: 14px;
    color: var(--ink-30);
    pointer-events: none;
    display: flex; align-items: center;
}
.km-input-trail {
    position: absolute; right: 14px;
    color: var(--ink-30);
    cursor: pointer;
    background: none; border: none; padding: 0;
    line-height: 1;
    display: flex; align-items: center;
}
.km-input-trail:hover { color: var(--ink-60); }

.km-input {
    width: 100%;
    height: 52px;
    border: 1.5px solid var(--ink-12);
    border-radius: var(--radius-md);
    background: var(--cream);
    font-family: var(--font-body);
    font-size: 14.5px;
    color: var(--ink);
    padding: 0 16px;
    outline: none;
    transition: border-color 0.18s, background 0.18s;
    appearance: none;
}
.km-input.has-icon  { padding-left: 44px; }
.km-input.has-trail { padding-right: 44px; }
.km-input:focus { border-color: var(--teal-600); background: var(--white); }
.km-input::placeholder { color: var(--ink-30); }

textarea.km-input { height: auto; padding: 14px 16px; resize: vertical; }

.km-select {
    width: 100%;
    height: 52px;
    border: 1.5px solid var(--ink-12);
    border-radius: var(--radius-md);
    background: var(--cream);
    font-family: var(--font-body);
    font-size: 14.5px;
    color: var(--ink);
    padding: 0 16px;
    outline: none;
    cursor: pointer;
    transition: border-color 0.18s;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L6 6L11 1' stroke='%230d1a14' stroke-opacity='0.3' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
}
.km-select:focus { border-color: var(--teal-600); background-color: var(--white); }

/* OTP inputs */
.km-otp-row {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin: 8px 0 24px;
}
.km-otp-input {
    width: 56px; height: 64px;
    border: 1.5px solid var(--ink-12);
    border-radius: var(--radius-md);
    background: var(--cream);
    font-family: var(--font-display);
    font-size: 28px; font-weight: 400;
    color: var(--ink);
    text-align: center;
    outline: none;
    transition: border-color 0.18s, background 0.18s;
}
.km-otp-input:focus { border-color: var(--teal-600); background: var(--white); }

/* Info box */
.km-info-box {
    background: rgba(15,70,80,0.05);
    border: 1px solid rgba(15,70,80,0.12);
    border-radius: var(--radius-sm);
    padding: 12px 16px;
    font-size: 13px;
    color: var(--ink-60);
    line-height: 1.7;
}
.km-info-box strong { color: var(--ink); }

/* Checkbox */
.km-check-row {
    display: flex; align-items: flex-start;
    gap: 10px; margin-bottom: 22px; cursor: pointer;
}
.km-check-row input[type=checkbox] { margin-top: 2px; accent-color: var(--teal-700); }
.km-check-row span { font-size: 13px; color: var(--ink-60); line-height: 1.5; }

/* Forgot link */
.km-forgot {
    text-align: right;
    margin-top: -10px;
    margin-bottom: 20px;
    font-size: 13px;
}

/* BUTTONS */
.km-btn {
    display: flex; align-items: center; justify-content: center;
    gap: 8px; width: 100%; height: 54px;
    border-radius: var(--radius-md);
    font-family: var(--font-body);
    font-size: 15px; font-weight: 600;
    cursor: pointer;
    transition: opacity 0.15s, transform 0.1s;
    border: none; outline: none; text-decoration: none;
}
.km-btn:active { transform: scale(0.98); }
.km-btn-primary { background: var(--red-600); color: var(--white); margin-top: 6px; }
.km-btn-primary:hover { opacity: .88; color: var(--white); }
.km-btn-outline {
    background: transparent;
    border: 1.5px solid var(--ink-12);
    color: var(--ink); margin-top: 10px;
}
.km-btn-outline:hover { background: var(--cream); }

/* Divider */
.km-divider {
    display: flex; align-items: center;
    gap: 14px; margin: 20px 0;
    color: var(--ink-30); font-size: 13px;
}
.km-divider::before, .km-divider::after {
    content: ''; flex: 1; height: 1px;
    background: var(--ink-12);
}

/* SSO button */
.km-sso-btn {
    display: flex; align-items: center; justify-content: center;
    gap: 10px; width: 100%; height: 50px;
    border: 1.5px solid var(--ink-12);
    border-radius: var(--radius-md);
    background: var(--white);
    font-family: var(--font-body);
    font-size: 14px; font-weight: 500; color: var(--ink);
    cursor: pointer; transition: background 0.15s;
    margin-bottom: 10px; text-decoration: none;
}
.km-sso-btn:hover { background: var(--cream); color: var(--ink); }
.km-sso-icon { width: 20px; height: 20px; }

/* Security note */
.km-security-note {
    display: flex; align-items: center; justify-content: center;
    gap: 6px; margin-top: 22px;
    font-size: 12.5px; color: var(--ink-30);
}

/* Stepper */
.km-stepper { display: flex; align-items: center; gap: 0; margin-bottom: 32px; }
.km-step { display: flex; flex-direction: column; align-items: center; gap: 6px; flex: 1; position: relative; }
.km-step:not(:last-child)::after {
    content: '';
    position: absolute; top: 16px; left: 50%;
    width: 100%; height: 1.5px;
    background: var(--ink-12); z-index: 0;
}
.km-step.done::after { background: var(--teal-600); }
.km-step-dot {
    width: 32px; height: 32px;
    border-radius: 50%;
    border: 1.5px solid var(--ink-12);
    background: var(--white);
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 600;
    color: var(--ink-30);
    position: relative; z-index: 1;
    transition: all 0.2s;
}
.km-step.active .km-step-dot { background: var(--teal-900); border-color: var(--teal-900); color: var(--white); }
.km-step.done .km-step-dot   { background: var(--teal-600); border-color: var(--teal-600); color: var(--white); }
.km-step-label { font-size: 11px; color: var(--ink-30); text-align: center; white-space: nowrap; }
.km-step.active .km-step-label { color: var(--teal-900); font-weight: 600; }
.km-step.done .km-step-label   { color: var(--teal-600); }

/* Upload box */
.km-upload-box {
    border: 2px dashed var(--ink-12);
    border-radius: var(--radius-md);
    padding: 32px 20px; text-align: center;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
    margin-bottom: 14px; position: relative; overflow: hidden;
}
.km-upload-box:hover { border-color: var(--teal-400); background: var(--teal-50); }
.km-upload-icon {
    width: 48px; height: 48px;
    background: var(--cream);
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 10px; color: var(--teal-600);
}
.km-upload-text { font-size: 13.5px; color: var(--ink-60); line-height: 1.5; }
.km-upload-text strong { color: var(--teal-700); }

/* Method cards (radio/document-type picker) */
.km-method-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
    gap: 12px; margin-bottom: 24px;
}
.km-method-card {
    border: 1.5px solid var(--ink-12);
    border-radius: var(--radius-md);
    padding: 18px 14px; text-align: center;
    cursor: pointer; transition: all 0.18s;
    background: var(--white); position: relative;
}
.km-method-card:hover { border-color: var(--teal-400); background: var(--teal-50); }
.km-method-card.selected { border-color: var(--teal-700); background: var(--teal-50); }
.km-method-card-icon {
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 8px; color: var(--teal-700);
}
.km-method-card strong { display: block; font-size: 13px; font-weight: 600; color: var(--ink); margin-bottom: 3px; }
.km-method-card span   { font-size: 12px; color: var(--ink-60); }

/* Alert banners */
.km-alert {
    border-radius: var(--radius-sm); padding: 12px 16px;
    font-size: 13.5px; line-height: 1.5; margin-bottom: 20px;
    display: flex; align-items: flex-start; gap: 10px;
}
.km-alert-warn   { background: #fef9ec; border: 1px solid #f5d97a; color: #7a5c0a; }
.km-alert-info   { background: var(--teal-50); border: 1px solid var(--teal-100); color: var(--teal-800); }
.km-alert-danger { background: var(--red-100); border: 1px solid var(--red-200); color: var(--red-800); }
.km-alert-icon   { flex-shrink: 0; margin-top: 1px; display: flex; align-items: center; }

/* Device card */
.km-device-card {
    background: var(--cream); border: 1px solid var(--ink-12);
    border-radius: var(--radius-md);
    padding: 16px 18px; display: flex; align-items: center;
    gap: 14px; margin-bottom: 24px;
}
.km-device-icon { color: var(--teal-700); display: flex; align-items: center; }
.km-device-info strong { display: block; font-size: 14px; font-weight: 600; color: var(--ink); }
.km-device-info span   { font-size: 13px; color: var(--ink-60); }

/* Status pages (centered icon + text) */
.km-status-icon {
    width: 80px; height: 80px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
}
.km-status-icon.success { background: #eef8f2; color: var(--teal-600); }
.km-status-icon.error   { background: var(--red-100); color: var(--red-600); }
.km-status-icon.warning { background: #fef9ec; color: #b07d0a; }
.km-status-center { text-align: center; }
.km-status-center .km-panel-title { font-size: 28px; margin-bottom: 10px; }
.km-status-center .km-panel-subtitle { margin-bottom: 28px; max-width: 360px; margin-left: auto; margin-right: auto; }

/* ── RESPONSIVE ── */
@media(max-width: 900px) {
    .km-auth-shell { grid-template-columns: 1fr; }
    .km-sidebar { display: none; }
    .km-panel { padding: 40px 24px; }
    .km-method-grid { grid-template-columns: 1fr; }
}

/* hide header/footer on auth pages */
body.km-auth-page .km-header,
body.km-auth-page footer,
body.km-auth-page .ft-wrap {
    display: none !important;
}
body.km-auth-page { padding-bottom: 0 !important; padding-top: 0 !important; }
</style>
@endpush

@push('script')
<script>
// Password visibility toggle for km-auth-shell
function kmTogglePwd(btn) {
    var wrap  = btn.closest('.km-input-wrap');
    var input = wrap.querySelector('input');
    var icon  = btn.querySelector('svg');
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" fill="none"/><line x1="1" y1="1" x2="23" y2="23" stroke="currentColor" stroke-width="1.8"/>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" fill="none"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8" fill="none"/>';
    }
}

// OTP auto-advance
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.km-otp-input').forEach(function(inp, i, all){
        inp.addEventListener('input', function(){
            if (this.value.length >= 1 && i < all.length - 1) all[i+1].focus();
        });
        inp.addEventListener('keydown', function(e){
            if (e.key === 'Backspace' && !this.value && i > 0) all[i-1].focus();
        });
    });

    // Method card radio selection
    document.querySelectorAll('.km-method-card').forEach(function(card){
        card.addEventListener('click', function(){
            var radio = this.querySelector('input[type=radio]');
            if (radio) {
                radio.checked = true;
                var name = radio.name;
                document.querySelectorAll('.km-method-card').forEach(function(c){
                    var r = c.querySelector('input[name="' + name + '"]');
                    if (r) c.classList.toggle('selected', r.checked);
                });
            }
        });
    });
});

// Mark body so header/footer hide
document.body.classList.add('km-auth-page');
</script>
@endpush
