@extends($activeTemplate . 'layouts.master')
@section('activePage', 'account')
@section('content')
<div class="max-w-md mx-auto py-6 md:py-10 px-2 animate-in zoom-in-95 duration-300">
    <div class="bg-card-white rounded-[32px] md:rounded-[40px] p-6 md:p-10 border border-border-gray shadow-xl">

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-brand-gold/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ri-lock-password-fill text-brand-gold text-3xl"></i>
            </div>
            <h2 class="text-xl md:text-2xl font-extrabold text-text-primary">Set New PIN</h2>
            <p class="text-text-secondary text-xs md:text-sm mt-2">
                Create a new 6-digit transaction PIN.
            </p>
        </div>

        <div id="reset-error" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 text-center"></div>

        <form id="resetPinForm" action="{{ route('user.reset_pin') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="pin" id="reset-pin-hidden">
            <input type="hidden" name="pin_confirmation" id="reset-pin-confirm-hidden">

            {{-- New PIN --}}
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-text-secondary uppercase ml-1 tracking-widest">New PIN</label>
                <div class="flex gap-2 justify-between" id="reset-pin-fields">
                    @for($i = 0; $i < 6; $i++)
                    <input type="password" inputmode="numeric" maxlength="1"
                        class="reset-pin-digit w-10 h-12 text-center text-lg font-bold border border-border-gray rounded-xl bg-bg-main focus:border-brand-gold focus:ring-2 focus:ring-brand-gold/10 outline-none transition-all"
                        autocomplete="off">
                    @endfor
                </div>
            </div>

            {{-- Confirm PIN --}}
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-text-secondary uppercase ml-1 tracking-widest">Confirm New PIN</label>
                <div class="flex gap-2 justify-between" id="reset-confirm-fields">
                    @for($i = 0; $i < 6; $i++)
                    <input type="password" inputmode="numeric" maxlength="1"
                        class="reset-pin-confirm-digit w-10 h-12 text-center text-lg font-bold border border-border-gray rounded-xl bg-bg-main focus:border-brand-gold focus:ring-2 focus:ring-brand-gold/10 outline-none transition-all"
                        autocomplete="off">
                    @endfor
                </div>
            </div>

            <button type="submit"
                class="w-full bg-brand-gold text-white font-bold py-4 rounded-2xl text-sm hover:bg-brand-gold/90 transition-all shadow-lg shadow-brand-gold/20 flex items-center justify-center gap-2">
                <i class="ri-lock-password-line"></i>
                Update PIN
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('user.view_reset_pin') }}" class="text-sm text-text-secondary hover:text-brand-gold transition-colors">
                <i class="ri-arrow-left-s-line"></i> Back
            </a>
        </div>

    </div>
</div>

@push('script')
<script>
(function () {
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
                if (e.key === 'Backspace' && !el.value && idx > 0) {
                    digits[idx - 1].focus();
                    digits[idx - 1].value = '';
                }
            });
        });
    }

    wireDigits('.reset-pin-digit', 'reset-pin-hidden');
    wireDigits('.reset-pin-confirm-digit', 'reset-pin-confirm-hidden');

    document.getElementById('resetPinForm').addEventListener('submit', function (e) {
        const pin     = document.getElementById('reset-pin-hidden').value;
        const confirm = document.getElementById('reset-pin-confirm-hidden').value;
        const errBox  = document.getElementById('reset-error');

        errBox.classList.add('hidden');

        if (pin.length !== 6) {
            e.preventDefault();
            errBox.textContent = 'Please enter all 6 digits for your new PIN.';
            errBox.classList.remove('hidden');
            return;
        }
        if (pin !== confirm) {
            e.preventDefault();
            errBox.textContent = 'PINs do not match. Please try again.';
            errBox.classList.remove('hidden');
            return;
        }
    });
})();
</script>
@endpush

@endsection
