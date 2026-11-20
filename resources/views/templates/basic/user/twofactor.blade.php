@extends($activeTemplate . 'layouts.master')
@section('activePage', '2fa')
@section('content')

<div class="bg-card-white p-8 md:p-12 rounded-[2.5rem] shadow-sm border border-border-gray max-w-5xl mx-auto">

    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h3 class="text-2xl font-bold text-text-primary tracking-tight">Two-Factor Authentication</h3>
            <p class="text-text-secondary text-sm mt-1">Add an extra layer of security to your account.</p>
        </div>
        @if(auth()->user()->ts)
        <div class="flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            2FA: Enabled ({{ ucfirst(auth()->user()->two_fa_method ?? 'totp') }})
        </div>
        @else
        <div class="flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-600 rounded-full text-[10px] font-black uppercase tracking-widest">
            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
            2FA: Not Enabled
        </div>
        @endif
    </div>

    @if(!auth()->user()->ts || request()->get('setup') === 'totp')
    {{-- ======================================================= --}}
    {{-- SETUP: method tabs --}}
    {{-- ======================================================= --}}
    <div style="border-bottom:2px solid #F0F0F0;display:flex;gap:0;margin-bottom:32px;">
        <button onclick="switchTab('totp')" id="tab-totp"
            class="tfa-tab"
            style="padding:10px 20px;font-size:13px;font-weight:700;background:none;border:none;cursor:pointer;border-bottom:2px solid #A71B47;color:#A71B47;margin-bottom:-2px;">
            Authenticator App
        </button>
        @if(!auth()->user()->ts)
        <button onclick="switchTab('sms')" id="tab-sms"
            class="tfa-tab"
            style="padding:10px 20px;font-size:13px;font-weight:700;background:none;border:none;cursor:pointer;border-bottom:2px solid transparent;color:#888;margin-bottom:-2px;">
            SMS
        </button>
        <button onclick="switchTab('email')" id="tab-email"
            class="tfa-tab"
            style="padding:10px 20px;font-size:13px;font-weight:700;background:none;border:none;cursor:pointer;border-bottom:2px solid transparent;color:#888;margin-bottom:-2px;">
            Email
        </button>
        @endif
    </div>

    {{-- ---- TOTP tab ---- --}}
    <div id="panel-totp" class="tfa-panel">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            {{-- Left: QR Code --}}
            <div class="space-y-8 pr-0 lg:pr-8 lg:border-r border-border-light">
                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-brand-teal flex items-center gap-2">
                        <i class="ri-qr-code-line text-lg"></i> 1. Link Your Device
                    </h4>
                    <p class="text-xs text-text-secondary leading-relaxed">
                        Open your authenticator app (Google Authenticator, Authy, etc.) and scan the secure code below.
                    </p>
                </div>
                <div class="bg-bg-secondary p-6 rounded-3xl border border-border-gray flex flex-col items-center justify-center group transition-all hover:bg-card-white hover:shadow-xl hover:shadow-slate-200/50">
                    <div class="bg-card-white p-4 rounded-2xl shadow-sm border border-border-gray mb-6">
                        <img src="{{ $qrCodeUrl }}" alt="Security QR Code" class="w-40 h-40 opacity-90 group-hover:opacity-100 transition-opacity">
                    </div>
                    <div class="w-full space-y-2">
                        <label class="text-[10px] uppercase tracking-widest text-text-secondary font-bold block text-center">Or enter manual setup key</label>
                        <div class="relative">
                            <input readonly type="text" value="{{ $secret }}"
                                class="w-full bg-card-white border border-border-gray rounded-xl py-3 px-4 text-xs font-mono text-center text-text-primary outline-none referralURL">
                            <button type="button" id="copyBoard" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-brand-teal hover:text-brand-gold transition-colors">
                                <i class="ri-file-copy-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en"
                    class="inline-flex items-center gap-2 text-[11px] font-bold text-brand-teal hover:underline" target="_blank">
                    <i class="ri-information-line"></i> Having trouble scanning?
                </a>
            </div>
            {{-- Right: Enable form --}}
            <div class="space-y-8">
                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-brand-teal flex items-center gap-2">
                        <i class="ri-shield-keyhole-line text-lg"></i> 2. Verify Connection
                    </h4>
                    <p class="text-xs text-text-secondary leading-relaxed">
                        Enter the 6-digit verification code generated by your app to finalize the security link.
                    </p>
                </div>
                <div class="space-y-6 pt-4">
                    <form action="{{ route('user.twofactor.enable') }}" method="POST">
                        @csrf
                        <input type="hidden" name="key" value="{{ $secret }}">
                        <div class="space-y-3">
                            <label class="text-[11px] uppercase tracking-[0.15em] text-text-secondary font-bold ml-1">Authenticator OTP</label>
                            <div class="relative">
                                <input type="text" name="code" placeholder="000 000" maxlength="7"
                                    class="w-full px-6 py-3 bg-bg-secondary border border-border-gray rounded-2xl text-2xl tracking-[0.5em] font-light text-center text-brand-teal focus:bg-card-white focus:ring-4 focus:ring-brand-teal/5 focus:border-brand-teal outline-none transition-all placeholder:text-text-secondary placeholder:tracking-normal">
                            </div>
                        </div>
                        <div class="bg-brand-teal/5 p-4 rounded-2xl border border-brand-teal/10 mt-4">
                            <p class="text-[10px] text-brand-teal leading-relaxed">
                                <i class="ri-error-warning-line mr-1 font-bold"></i>
                                <strong>Important:</strong> Ensure your device time is synchronized with your local time zone to prevent code mismatch.
                            </p>
                        </div>
                        <button type="submit" class="mt-6 w-full bg-brand-gold text-brand-teal py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-brand-gold/20 hover:shadow-brand-gold/40 transform hover:-translate-y-1 transition-all active:scale-95">
                            Enable Authenticator 2FA
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ---- SMS tab ---- --}}
    <div id="panel-sms" class="tfa-panel hidden">
        <div class="max-w-md mx-auto text-center space-y-6 py-4">
            <div class="bg-bg-secondary rounded-3xl p-8 border border-border-gray">
                <div class="w-14 h-14 rounded-2xl bg-brand-gold/10 border border-brand-gold/20 flex items-center justify-center mx-auto mb-5">
                    <svg width="24" height="24" fill="none" stroke="#A71B47" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a2 2 0 012-2.18h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L9.91 14a16 16 0 006.09 6.09l.31-.31a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                </div>
                <h4 class="text-base font-bold text-text-primary mb-2">SMS Verification</h4>
                <p class="text-sm text-text-secondary leading-relaxed mb-1">When you log in, we'll send a one-time code to:</p>
                <p class="text-sm font-bold text-text-primary">+{{ auth()->user()->dial_code }}{{ auth()->user()->mobile }}</p>
            </div>
            <form action="{{ route('user.twofactor.enable.sms') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-brand-gold text-white py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-brand-gold/20 hover:shadow-brand-gold/40 transform hover:-translate-y-1 transition-all active:scale-95">
                    Enable SMS 2FA
                </button>
            </form>
        </div>
    </div>

    {{-- ---- Email tab ---- --}}
    <div id="panel-email" class="tfa-panel hidden">
        <div class="max-w-md mx-auto text-center space-y-6 py-4">
            <div class="bg-bg-secondary rounded-3xl p-8 border border-border-gray">
                <div class="w-14 h-14 rounded-2xl bg-brand-gold/10 border border-brand-gold/20 flex items-center justify-center mx-auto mb-5">
                    <svg width="24" height="24" fill="none" stroke="#A71B47" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <h4 class="text-base font-bold text-text-primary mb-2">Email Verification</h4>
                <p class="text-sm text-text-secondary leading-relaxed mb-1">When you log in, we'll send a one-time code to:</p>
                <p class="text-sm font-bold text-text-primary">{{ auth()->user()->email }}</p>
            </div>
            <form action="{{ route('user.twofactor.enable.email') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-brand-gold text-white py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-brand-gold/20 hover:shadow-brand-gold/40 transform hover:-translate-y-1 transition-all active:scale-95">
                    Enable Email 2FA
                </button>
            </form>
        </div>
    </div>

    @else
    {{-- ======================================================= --}}
    {{-- ENABLED STATE: change method + disable --}}
    {{-- ======================================================= --}}
    @php $activeMethod = auth()->user()->two_fa_method ?? 'totp'; @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        {{-- Left: current method info --}}
        <div class="space-y-8 pr-0 lg:pr-8 lg:border-r border-border-light">
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-start gap-3">
                <i class="ri-shield-check-fill text-emerald-500 text-xl mt-0.5"></i>
                <div>
                    <p class="text-xs font-bold text-emerald-700 mb-0.5">Two-Factor Authentication is Active</p>
                    <p class="text-[11px] text-emerald-600">Your account has an extra layer of security. You'll need to verify your identity each time you log in.</p>
                </div>
            </div>

            <div class="space-y-3">
                <p class="text-[11px] uppercase tracking-widest text-text-secondary font-bold">Active Method</p>
                @if($activeMethod === 'totp')
                <div class="flex items-center gap-3 p-4 bg-bg-secondary rounded-2xl border border-border-gray">
                    <div class="w-10 h-10 rounded-xl bg-brand-gold/10 border border-brand-gold/20 flex items-center justify-center flex-shrink-0">
                        <svg width="18" height="18" fill="none" stroke="#A71B47" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18" stroke-linecap="round"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-text-primary">Authenticator App</p>
                        <p class="text-xs text-text-secondary">Time-based one-time password (TOTP)</p>
                    </div>
                    <span class="ml-auto text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-1 rounded-full">Active</span>
                </div>
                @elseif($activeMethod === 'sms')
                <div class="flex items-center gap-3 p-4 bg-bg-secondary rounded-2xl border border-border-gray">
                    <div class="w-10 h-10 rounded-xl bg-brand-gold/10 border border-brand-gold/20 flex items-center justify-center flex-shrink-0">
                        <svg width="18" height="18" fill="none" stroke="#A71B47" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a2 2 0 012-2.18h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L9.91 14a16 16 0 006.09 6.09l.31-.31a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-text-primary">SMS Code</p>
                        <p class="text-xs text-text-secondary">Sent to +{{ auth()->user()->dial_code }}{{ auth()->user()->mobile }}</p>
                    </div>
                    <span class="ml-auto text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-1 rounded-full">Active</span>
                </div>
                @else
                <div class="flex items-center gap-3 p-4 bg-bg-secondary rounded-2xl border border-border-gray">
                    <div class="w-10 h-10 rounded-xl bg-brand-gold/10 border border-brand-gold/20 flex items-center justify-center flex-shrink-0">
                        <svg width="18" height="18" fill="none" stroke="#A71B47" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-text-primary">Email Code</p>
                        <p class="text-xs text-text-secondary">Sent to {{ auth()->user()->email }}</p>
                    </div>
                    <span class="ml-auto text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-1 rounded-full">Active</span>
                </div>
                @endif
            </div>

            {{-- Switch to another method --}}
            <div class="space-y-3">
                <p class="text-[11px] uppercase tracking-widest text-text-secondary font-bold">Switch Method</p>
                <p class="text-xs text-text-secondary">Instantly switch to a different 2FA method. You'll use the new method on your next login.</p>
                <div class="space-y-2 pt-1">
                    @if($activeMethod !== 'totp')
                    <div class="flex items-center justify-between p-3 bg-bg-secondary rounded-xl border border-border-gray">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white border border-border-gray flex items-center justify-center">
                                <svg width="14" height="14" fill="none" stroke="#555" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18" stroke-linecap="round"/></svg>
                            </div>
                            <span class="text-xs font-semibold text-text-primary">Authenticator App</span>
                        </div>
                        <a href="{{ route('user.twofactor') }}?setup=totp" style="background:#A71B47;color:#fff;border:none;padding:5px 14px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;display:inline-block;text-decoration:none;">Switch</a>
                    </div>
                    @endif
                    @if($activeMethod !== 'sms')
                    <div class="flex items-center justify-between p-3 bg-bg-secondary rounded-xl border border-border-gray">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white border border-border-gray flex items-center justify-center">
                                <svg width="14" height="14" fill="none" stroke="#555" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a2 2 0 012-2.18h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L9.91 14a16 16 0 006.09 6.09l.31-.31a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-text-primary">SMS</span>
                                <span class="block text-[10px] text-text-secondary">+{{ auth()->user()->dial_code }}{{ auth()->user()->mobile }}</span>
                            </div>
                        </div>
                        <form action="{{ route('user.twofactor.enable.sms') }}" method="POST">
                            @csrf
                            <button type="submit" style="background:#A71B47;color:#fff;border:none;padding:5px 14px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;">Switch</button>
                        </form>
                    </div>
                    @endif
                    @if($activeMethod !== 'email')
                    <div class="flex items-center justify-between p-3 bg-bg-secondary rounded-xl border border-border-gray">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white border border-border-gray flex items-center justify-center">
                                <svg width="14" height="14" fill="none" stroke="#555" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-text-primary">Email</span>
                                <span class="block text-[10px] text-text-secondary">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                        <form action="{{ route('user.twofactor.enable.email') }}" method="POST">
                            @csrf
                            <button type="submit" style="background:#A71B47;color:#fff;border:none;padding:5px 14px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;">Switch</button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: disable form --}}
        <div class="space-y-8">
            <div class="space-y-4">
                <h4 class="text-sm font-bold text-red-500 flex items-center gap-2">
                    <i class="ri-shield-cross-line text-lg"></i> Disable 2FA
                </h4>
                <p class="text-xs text-text-secondary leading-relaxed">
                    @if($activeMethod === 'totp')
                        Enter your current authenticator code to disable two-factor authentication.
                    @else
                        Click "Send Code" to receive a one-time code, then enter it below to disable 2FA.
                    @endif
                </p>
            </div>

            @if($activeMethod === 'totp')
            <div class="space-y-6 pt-2">
                <form action="{{ route('user.twofactor.disable') }}" method="POST">
                    @csrf
                    <input type="hidden" name="key" value="{{ $secret }}">
                    <div class="space-y-3">
                        <label class="text-[11px] uppercase tracking-[0.15em] text-text-secondary font-bold ml-1">Authenticator OTP</label>
                        <div class="relative">
                            <input type="text" name="code" placeholder="000 000" maxlength="7"
                                class="w-full px-6 py-3 bg-bg-secondary border border-border-gray rounded-2xl text-2xl tracking-[0.5em] font-light text-center text-text-primary focus:bg-card-white focus:ring-4 focus:ring-red-500/5 focus:border-red-400 outline-none transition-all placeholder:text-text-secondary placeholder:tracking-normal">
                        </div>
                    </div>
                    <button type="submit" class="mt-6 w-full bg-red-500 text-white py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-red-200 hover:bg-red-600 hover:shadow-red-300 transform hover:-translate-y-1 transition-all active:scale-95">
                        Disable 2FA
                    </button>
                </form>
            </div>
            @else
            <div class="space-y-4 pt-2">
                <button type="button" id="send-disable-btn" onclick="sendDisableCode()"
                    class="w-full border-2 border-red-400 text-red-500 py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-red-50 transition-all">
                    Send Verification Code
                </button>

                <form action="{{ route('user.twofactor.disable') }}" method="POST" id="disable-form" style="display:none;">
                    @csrf
                    <div class="space-y-3">
                        <label class="text-[11px] uppercase tracking-[0.15em] text-text-secondary font-bold ml-1">Enter Code</label>
                        <div class="relative">
                            <input type="text" name="code" placeholder="000 000" maxlength="7"
                                class="w-full px-6 py-3 bg-bg-secondary border border-border-gray rounded-2xl text-2xl tracking-[0.5em] font-light text-center text-text-primary focus:bg-card-white focus:ring-4 focus:ring-red-500/5 focus:border-red-400 outline-none transition-all placeholder:text-text-secondary placeholder:tracking-normal">
                        </div>
                    </div>
                    <button type="submit" class="mt-4 w-full bg-red-500 text-white py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-red-200 hover:bg-red-600 hover:shadow-red-300 transform hover:-translate-y-1 transition-all active:scale-95">
                        Disable 2FA
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
    @endif

</div>

@push('style')
<style>
    .copied::after {
        background-color: #{{ gs('base_color') }};
    }
    .tfa-tab { transition: color .15s, border-color .15s; }
</style>
@endpush

@push('script')
<script>
(function($) {
    "use strict";
    $('#copyBoard').on('click', function() {
        var copyText = document.getElementsByClassName("referralURL");
        copyText = copyText[0];
        if (!copyText) return;
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        copyText.blur();
        this.classList.add('copied');
        setTimeout(() => this.classList.remove('copied'), 1500);
    });
})(jQuery);

function switchTab(tab) {
    // Hide all panels
    document.querySelectorAll('.tfa-panel').forEach(function(p) {
        p.classList.add('hidden');
    });
    // Reset all tab styles
    document.querySelectorAll('.tfa-tab').forEach(function(t) {
        t.style.borderBottomColor = 'transparent';
        t.style.color = '#888';
    });
    // Show selected panel
    var panel = document.getElementById('panel-' + tab);
    if (panel) panel.classList.remove('hidden');
    // Highlight selected tab
    var activeTab = document.getElementById('tab-' + tab);
    if (activeTab) {
        activeTab.style.borderBottomColor = '#A71B47';
        activeTab.style.color = '#A71B47';
    }
}

function sendDisableCode() {
    var btn = document.getElementById('send-disable-btn');
    btn.disabled = true;
    btn.textContent = 'Sending…';
    fetch('{{ route("user.2fa.resend") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(function() {
        var form = document.getElementById('disable-form');
        if (form) form.style.display = 'block';
        btn.textContent = 'Code sent — resend';
        btn.disabled = false;
    })
    .catch(function() {
        btn.textContent = 'Send Verification Code';
        btn.disabled = false;
    });
}

window.addEventListener('DOMContentLoaded', function() {
    if (typeof window.initSidebar === 'function') window.initSidebar();
    var toggleBtn = document.getElementById('toggle-sidebar');
    if (toggleBtn && typeof window.toggleSidebar === 'function') {
        toggleBtn.addEventListener('click', function() { window.toggleSidebar(); });
    }
});
</script>
@endpush

@endsection
