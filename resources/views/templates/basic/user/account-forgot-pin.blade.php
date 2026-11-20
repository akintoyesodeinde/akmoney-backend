@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="max-w-md mx-auto py-6 md:py-10 px-2 animate-in slide-in-from-right-10 duration-500">
    <div class="bg-card-white rounded-[32px] md:rounded-[40px] p-6 md:p-10 border border-border-gray shadow-xl">

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-brand-gold/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ri-shield-user-fill text-brand-gold text-3xl"></i>
            </div>
            <h2 class="text-xl md:text-2xl font-extrabold text-text-primary">Reset Transaction PIN</h2>
            <p class="text-text-secondary text-xs md:text-sm mt-2">
                Verify your identity to reset your PIN
            </p>
        </div>

        @if(session()->has('notify'))
            @foreach(session('notify') as $notification)
                <div class="mb-4 p-3 {{ $notification[0] === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-600' }} border rounded-xl text-sm text-center">
                    {{ $notification[1] }}
                </div>
            @endforeach
        @endif

        @php $needs2fa = $requires2fa && !$tfa_verified; @endphp

        {{-- ======== STEP 1: 2FA Verification (only if user has 2FA and not yet verified) ======== --}}
        @if($needs2fa)
        <div id="step-2fa">
            <div class="p-4 mb-6 bg-amber-50 border border-amber-200 rounded-2xl text-sm text-amber-700 flex items-start gap-3">
                <i class="ri-shield-keyhole-line text-amber-500 text-lg flex-shrink-0 mt-0.5"></i>
                <span>Your account has 2FA enabled. We'll send a verification code to your registered email before you can reset your PIN.</span>
            </div>

            <div id="tfa-send-section">
                <button type="button" id="sendCodeBtn" onclick="sendPinResetCode()"
                    class="w-full bg-brand-gold text-white font-bold py-4 rounded-2xl text-sm hover:bg-brand-gold/90 transition-all shadow-lg shadow-brand-gold/20 flex items-center justify-center gap-2">
                    <i class="ri-mail-send-line"></i>
                    Send Verification Code
                </button>
            </div>

            <div id="tfa-verify-section" class="hidden space-y-4 mt-4">
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-text-secondary uppercase ml-1 tracking-widest">Verification Code</label>
                    <input type="text" id="tfa-code-input" inputmode="numeric" maxlength="6" placeholder="6-digit code"
                        class="w-full px-4 py-3.5 bg-bg-main border border-border-gray rounded-2xl text-sm text-text-primary placeholder:text-text-muted focus:border-brand-gold focus:ring-2 focus:ring-brand-gold/10 outline-none transition-all text-center tracking-widest font-bold text-lg">
                </div>
                <div id="tfa-error" class="hidden p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 text-center"></div>
                <button type="button" onclick="verifyPinResetCode()"
                    class="w-full bg-brand-gold text-white font-bold py-4 rounded-2xl text-sm hover:bg-brand-gold/90 transition-all shadow-lg shadow-brand-gold/20">
                    Verify Code
                </button>
                <button type="button" onclick="sendPinResetCode()" class="w-full text-text-secondary text-xs hover:text-brand-gold transition-colors">
                    Resend code
                </button>
            </div>
        </div>

        {{-- Security question form (hidden until 2FA verified) --}}
        <div id="step-security" class="hidden mt-6 pt-6 border-t border-border-gray">
        @else
        <div id="step-security">
        @endif

            <form action="{{ route('user.verify_security') }}" method="POST" class="space-y-6">
                @csrf

                {{-- User info --}}
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-text-secondary uppercase ml-1 tracking-widest">Account Email</label>
                    <div class="relative">
                        <input type="email" value="{{ $user->email }}" readonly
                            class="w-full px-4 py-4 bg-bg-main border border-border-gray rounded-2xl text-sm font-medium text-text-secondary cursor-not-allowed">
                        <i class="ri-lock-fill absolute right-4 top-1/2 -translate-y-1/2 text-text-secondary/40"></i>
                    </div>
                </div>

                {{-- Real security question --}}
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-text-secondary uppercase ml-1 tracking-widest">Security Question</label>
                    <div class="p-4 bg-bg-main/50 border border-border-gray rounded-2xl">
                        <p class="text-sm font-bold text-text-primary mb-3">
                            {{ $user->security_question_id ?: 'No security question set.' }}
                        </p>
                        @if($user->security_question_id)
                        <input type="text" placeholder="Your answer (not case sensitive)" name="security_answer"
                            class="w-full bg-transparent border-b border-border-gray py-2 focus:border-brand-gold outline-none transition-all text-sm">
                        @endif
                    </div>
                </div>

                @if($user->security_question_id)
                <div class="space-y-4 pt-2">
                    <button type="submit" class="w-full bg-brand-gold text-white font-bold py-4 rounded-2xl shadow-lg shadow-brand-gold/20 hover:bg-brand-gold/90 active:scale-[0.98] transition-all text-sm">
                        Verify &amp; Continue
                    </button>

                    <button type="button" onclick="window.history.back()"
                        class="w-full text-text-secondary font-bold text-xs uppercase tracking-widest hover:text-brand-gold transition-colors">
                        I remembered it
                    </button>
                </div>
                @else
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-sm text-amber-700 text-center">
                    You haven't set a security question. Please <a href="{{ route('ticket.index') }}" class="font-bold underline">contact support</a> to reset your PIN.
                </div>
                @endif
            </form>
        </div>

    </div>

    <div class="mt-8 text-center px-6">
        <p class="text-xs text-text-secondary">
            Still having issues? Contact <a href="{{ route('ticket.index') }}" class="text-brand-gold font-bold underline">Customer Support</a>
        </p>
    </div>
</div>

@push('script')
<script>
(function () {
    window.sendPinResetCode = function () {
        const btn = document.getElementById('sendCodeBtn');
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Sending…'; }

        fetch('{{ route("user.pin_reset.send_code") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: '{}',
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('tfa-send-section').classList.add('hidden');
                const verifySection = document.getElementById('tfa-verify-section');
                verifySection.classList.remove('hidden');
                // Show where the code was sent
                const sentMsg = document.createElement('p');
                sentMsg.className = 'text-xs text-text-secondary text-center mb-3';
                sentMsg.textContent = data.message || 'Code sent. Check your email.';
                verifySection.insertBefore(sentMsg, verifySection.firstChild);
                document.getElementById('tfa-code-input').focus();
            } else {
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ri-mail-send-line"></i> Send Verification Code'; }
                alert(data.message || 'Failed to send code.');
            }
        })
        .catch(() => {
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ri-mail-send-line"></i> Send Verification Code'; }
            alert('Request failed. Please try again.');
        });
    };

    window.verifyPinResetCode = function () {
        const code   = document.getElementById('tfa-code-input').value.trim();
        const errBox = document.getElementById('tfa-error');
        errBox.classList.add('hidden');

        if (code.length !== 6) {
            errBox.textContent = 'Please enter the 6-digit code.';
            errBox.classList.remove('hidden');
            return;
        }

        fetch('{{ route("user.pin_reset.verify_code") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ code }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('step-2fa').classList.add('hidden');
                document.getElementById('step-security').classList.remove('hidden');
            } else {
                errBox.textContent = data.message || 'Invalid code.';
                errBox.classList.remove('hidden');
            }
        })
        .catch(() => {
            errBox.textContent = 'Request failed. Please try again.';
            errBox.classList.remove('hidden');
        });
    };
})();
</script>
@endpush

@endsection
