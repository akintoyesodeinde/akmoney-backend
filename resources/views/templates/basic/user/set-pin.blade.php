@extends($activeTemplate . 'layouts.master')
@section('activePage', 'profile')
@section('content')

<div class="max-w-md mx-auto py-6 md:py-10 px-2">
    <div class="bg-card-white rounded-[32px] md:rounded-[40px] p-6 md:p-10 border border-border-gray shadow-xl">

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-brand-gold/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ri-lock-password-fill text-brand-gold text-3xl"></i>
            </div>
            <h2 class="text-xl md:text-2xl font-extrabold text-text-primary">Set Transaction PIN</h2>
            <p class="text-text-secondary text-xs md:text-sm mt-2">
                Your 6-digit PIN is required to authorise all transfers.
            </p>
        </div>

        <div id="pin-error" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 text-center"></div>
        <div id="pin-success" class="hidden mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700 text-center"></div>

        <form id="setPinForm" class="space-y-6">
            @csrf

            {{-- PIN --}}
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-text-secondary uppercase ml-1 tracking-widest">New PIN</label>
                <div class="flex gap-2 justify-between" id="pin-fields">
                    @for($i = 0; $i < 6; $i++)
                    <input type="password" inputmode="numeric" maxlength="1"
                        class="pin-digit w-10 h-12 text-center text-lg font-bold border border-border-gray rounded-xl bg-bg-main focus:border-brand-gold focus:ring-2 focus:ring-brand-gold/10 outline-none transition-all"
                        autocomplete="off">
                    @endfor
                </div>
                <input type="hidden" name="pin" id="pin-hidden">
            </div>

            {{-- Confirm PIN --}}
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-text-secondary uppercase ml-1 tracking-widest">Confirm PIN</label>
                <div class="flex gap-2 justify-between" id="confirm-fields">
                    @for($i = 0; $i < 6; $i++)
                    <input type="password" inputmode="numeric" maxlength="1"
                        class="pin-confirm-digit w-10 h-12 text-center text-lg font-bold border border-border-gray rounded-xl bg-bg-main focus:border-brand-gold focus:ring-2 focus:ring-brand-gold/10 outline-none transition-all"
                        autocomplete="off">
                    @endfor
                </div>
                <input type="hidden" name="pin_confirmation" id="pin-confirm-hidden">
            </div>

            {{-- Security Question --}}
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-text-secondary uppercase ml-1 tracking-widest">Security Question</label>
                <div class="relative">
                    <select name="security_question_id" id="security_question_id"
                        class="w-full px-4 py-3.5 bg-bg-main border border-border-gray rounded-2xl text-sm text-text-primary focus:border-brand-gold focus:ring-2 focus:ring-brand-gold/10 outline-none appearance-none cursor-pointer transition-all">
                        <option value="">Select a security question</option>
                        <option value="What is the name of your pet?">What is the name of your pet?</option>
                        <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                        <option value="What was the name of your first school?">What was the name of your first school?</option>
                        <option value="What is your favourite sports team?">What is your favourite sports team?</option>
                        <option value="What city were you born in?">What city were you born in?</option>
                    </select>
                    <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-text-secondary pointer-events-none"></i>
                </div>
            </div>

            {{-- Security Answer --}}
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-text-secondary uppercase ml-1 tracking-widest">Security Answer</label>
                <input type="text" name="security_answer" id="security_answer"
                    placeholder="Your answer (not case sensitive)"
                    class="w-full px-4 py-3.5 bg-bg-main border border-border-gray rounded-2xl text-sm text-text-primary placeholder:text-text-muted focus:border-brand-gold focus:ring-2 focus:ring-brand-gold/10 outline-none transition-all">
            </div>

            <button type="submit" id="submit-btn"
                class="w-full bg-brand-gold text-white font-bold py-4 rounded-2xl text-sm hover:bg-brand-gold/90 transition-all shadow-lg shadow-brand-gold/20 flex items-center justify-center gap-2">
                <i class="ri-lock-password-line"></i>
                Set PIN
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('user.profile.setting') }}" class="text-sm text-text-secondary hover:text-brand-gold transition-colors">
                <i class="ri-arrow-left-s-line"></i> Back to Account
            </a>
        </div>

    </div>
</div>

@push('script')
<script>
(function () {
    // Auto-advance PIN digit inputs
    function wireDigits(selector, hiddenId) {
        const digits = document.querySelectorAll(selector);
        const hidden = document.getElementById(hiddenId);
        digits.forEach((el, idx) => {
            el.addEventListener('input', e => {
                e.target.value = e.target.value.replace(/\D/g, '').slice(-1);
                if (e.target.value && idx < digits.length - 1) digits[idx + 1].focus();
                hidden.value = Array.from(digits).map(d => d.value).join('');
            });
            el.addEventListener('keydown', e => {
                if (e.key === 'Backspace' && !el.value && idx > 0) { digits[idx - 1].focus(); digits[idx - 1].value = ''; }
            });
        });
    }

    wireDigits('.pin-digit', 'pin-hidden');
    wireDigits('.pin-confirm-digit', 'pin-confirm-hidden');

    document.getElementById('setPinForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const pin     = document.getElementById('pin-hidden').value;
        const confirm = document.getElementById('pin-confirm-hidden').value;
        const question = document.getElementById('security_question_id').value;
        const answer   = document.getElementById('security_answer').value.trim();
        const errBox   = document.getElementById('pin-error');
        const okBox    = document.getElementById('pin-success');
        const btn      = document.getElementById('submit-btn');

        errBox.classList.add('hidden');
        okBox.classList.add('hidden');

        if (pin.length !== 6) { errBox.textContent = 'Please enter all 6 digits.'; errBox.classList.remove('hidden'); return; }
        if (pin !== confirm)  { errBox.textContent = 'PINs do not match.'; errBox.classList.remove('hidden'); return; }
        if (!question)        { errBox.textContent = 'Please select a security question.'; errBox.classList.remove('hidden'); return; }
        if (!answer)          { errBox.textContent = 'Please enter your security answer.'; errBox.classList.remove('hidden'); return; }

        btn.disabled = true;
        btn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Setting PIN…';

        fetch('{{ route("user.set_pin") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ pin, pin_confirmation: confirm, security_question_id: question, security_answer: answer }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                okBox.textContent = 'PIN set successfully! Redirecting…';
                okBox.classList.remove('hidden');
                setTimeout(() => { window.location.href = '{{ route("user.profile.setting") }}'; }, 1500);
            } else {
                errBox.textContent = data.message || 'Something went wrong.';
                errBox.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-lock-password-line"></i> Set PIN';
            }
        })
        .catch(() => {
            errBox.textContent = 'Request failed. Please try again.';
            errBox.classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-lock-password-line"></i> Set PIN';
        });
    });
})();
</script>
@endpush

@endsection
