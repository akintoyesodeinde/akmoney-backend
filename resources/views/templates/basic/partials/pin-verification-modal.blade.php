
<div id="pinVerificationModal" class="fixed inset-0 bg-black/30 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4">
    <div class="bg-card-white rounded-3xl shadow-xl max-w-md w-full p-8 animate-in fade-in duration-300">
        @if(!auth()->user()->has_pin)
        <!-- New PIN Setup (if user doesn't have PIN) -->
        <div id="pinSetupSection" class="hidden">
            <h2 class="text-2xl font-bold text-text-primary mb-2">Create Transaction PIN</h2>
            <p class="text-text-secondary text-sm mb-6">This PIN will be used to authorize all transfers</p>

            <form id="pinSetupForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-text-primary mb-2">Create a 6-Digit PIN</label>
                    <div class="flex gap-2 justify-center mb-4">
                        <input type="text" class="pin-digit w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                        <input type="text" class="pin-digit w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                        <input type="text" class="pin-digit w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                        <input type="text" class="pin-digit w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                        <input type="text" class="pin-digit w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                        <input type="text" class="pin-digit w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-text-primary mb-2">Confirm PIN</label>
                    <div class="flex gap-2 justify-center">
                        <input type="text" class="pin-digit-confirm w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                        <input type="text" class="pin-digit-confirm w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                        <input type="text" class="pin-digit-confirm w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                        <input type="text" class="pin-digit-confirm w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                        <input type="text" class="pin-digit-confirm w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                        <input type="text" class="pin-digit-confirm w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-text-primary mb-2">Select a Security Question</label>
                    <select id="securityQuestion" required class="w-full px-4 py-3 bg-bg-main border border-border-gray rounded-xl focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all text-sm">
                        <option value="">Choose a question...</option>
                        <option value="What is the name of your pet?">What is the name of your pet?</option>
                        <option value="What city were you born in?">What city were you born in?</option>
                        <option value="What is the name of your primary school?">What is the name of your primary school?</option>
                        <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                        <option value="What is the name of the street you grew up on?">What is the name of the street you grew up on?</option>
                        <option value="Who was your favorite teacher?">Who was your favorite teacher?</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-text-primary mb-2">Your Answer</label>
                    <input type="text" id="securityAnswer" placeholder="Enter your answer" required class="w-full px-4 py-3 bg-bg-main border border-border-gray rounded-xl focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all text-sm">
                </div>
                <div id="pinMismatchError" class="hidden p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600">
                    PINs do not match. Please try again.
                </div>

                <button type="button" onclick="submitPinSetup()" class="w-full bg-brand-teal text-white font-bold py-3 rounded-xl hover:bg-opacity-90 transition-all">
                    Next
                </button>
            </form>
        </div>

        <!-- Security Question Setup (if user doesn't have PIN) -->
        @else

        <div id="pinVerificationSection" class="hidden">
            <h2 class="text-2xl font-bold text-text-primary mb-2">Enter Transaction PIN</h2>
            <p class="text-text-secondary text-sm mb-6">Enter your 6-digit PIN to authorize this transfer</p>

            <form id="pinVerificationForm" class="space-y-4">
                <div class="flex gap-2 justify-center mb-4">
                    <input type="text" class="pin-verify-digit w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                    <input type="text" class="pin-verify-digit w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                    <input type="text" class="pin-verify-digit w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                    <input type="text" class="pin-verify-digit w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                    <input type="text" class="pin-verify-digit w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                    <input type="text" class="pin-verify-digit w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                </div>

                <div id="pinVerifyError" class="hidden p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600">
                    Incorrect PIN. Please try again.
                </div>

                <button type="button" onclick="verifyPIN()" class="w-full bg-brand-teal text-white font-bold py-3 rounded-xl hover:bg-opacity-90 transition-all">
                    Verify
                </button>

                <button type="button" onclick="openPINResetModal()" class="w-full bg-white text-brand-teal font-bold py-3 rounded-xl border border-brand-teal hover:bg-brand-teal/5 transition-all text-sm">
                    Forgot PIN?
                </button>

                <button type="button" onclick="closePINVerificationModal()" class="w-full bg-bg-main text-text-secondary font-bold py-3 rounded-xl hover:bg-gray-300 transition-all text-sm">
                    Cancel
                </button>
            </form>
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const modal = document.getElementById('pinVerificationModal');
    if (!modal) return;

    // Auto-focus for PIN inputs
    const pinInputs = document.querySelectorAll('.pin-digit, .pin-digit-confirm, .pin-verify-digit');
    pinInputs.forEach((input, index, array) => {
        input.addEventListener('input', (e) => {
            if (e.target.value.length === 1 && index < array.length - 1) array[index + 1].focus();
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && index > 0) array[index - 1].focus();
        });
    });

    // Helper to get PIN from inputs
    function getPinValue(selector) {
        const inputs = document.querySelectorAll(selector);
        return Array.from(inputs).map(input => input.value).join('');
    }

    // Open modal
    window.initPINVerificationModal = function(userHasPin) {
        modal.classList.remove('hidden');
        document.getElementById('pinMismatchError')?.classList.add('hidden');
        document.getElementById('pinVerifyError')?.classList.add('hidden');

        if (userHasPin) {
            document.getElementById('pinVerificationSection')?.classList.remove('hidden');
            document.getElementById('pinSetupSection')?.classList.add('hidden');
            document.getElementById('securityQuestionSection')?.classList.add('hidden');
            document.querySelectorAll('.pin-verify-digit').forEach(input => input.value = '');
        } else {
            document.getElementById('pinSetupSection')?.classList.remove('hidden');
            document.getElementById('pinVerificationSection')?.classList.add('hidden');
            document.getElementById('securityQuestionSection')?.classList.remove('hidden'); // always show both
            document.querySelectorAll('.pin-digit, .pin-digit-confirm').forEach(input => input.value = '');
            document.getElementById('securityQuestion').value = '';
            document.getElementById('securityAnswer').value = '';
        }
    }

    // Close modal
    window.closePINVerificationModal = function() {
        modal.classList.add('hidden');
    }

    // Click outside modal closes it
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closePINVerificationModal();
    });

    // Single function to submit PIN setup + security question
    window.submitPinSetup = function() {
        const pin = getPinValue('.pin-digit');
        const confirmPin = getPinValue('.pin-digit-confirm');
        const question = document.getElementById('securityQuestion').value;
        const answer = document.getElementById('securityAnswer').value;

        // Validations
        if (pin.length !== 6 || confirmPin.length !== 6) {
            alert('Please enter a 6-digit PIN');
            return;
        }
        if (pin !== confirmPin) {
            document.getElementById('pinMismatchError')?.classList.remove('hidden');
            return;
        }
        if (!question || !answer) {
            alert('Please select a security question and provide an answer');
            return;
        }

        const payload = {
            pin: pin,
            pin_confirmation: confirmPin,
            security_question_id: question,
            security_answer: answer
        };

        fetch("{{ route('user.set_pin') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                closePINVerificationModal();
                // Call the form's pin-verified callback if available, else submit directly
                if (typeof window.onPinVerifiedSuccess === 'function') {
                    window.onPinVerifiedSuccess();
                } else {
                    processPendingTransfer();
                }
            } else {
                alert(data.message || 'Failed to set PIN');
            }
        })
        .catch(err => console.error(err));
    }

    window.getPinValue = function(selector) {
        return Array.from(document.querySelectorAll(selector))
            .map(input => input.value)
            .join('');
    }

    // Submit the pending send-money form
    window.processPendingTransfer = function() {
        const form = document.getElementById('sendMoneyForm');
        if (!form) return console.error('Send money form not found');
        form.submit();
    };

    window.verifyPIN = function() {
        const pin = getPinValue('.pin-verify-digit');

        if (pin.length !== 6) {
            alert('Please enter your 6-digit PIN');
            return;
        }

        document.getElementById('pinVerifyError').classList.add('hidden');

        fetch("{{ route('user.verify_pin') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ pin })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                closePINVerificationModal();
                if (typeof window.onPinVerifiedSuccess === 'function') {
                    window.onPinVerifiedSuccess();
                } else {
                    window.processPendingTransfer();
                }
            } else {
                document.getElementById('pinVerifyError').classList.remove('hidden');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Failed to verify PIN. Try again.');
        });
    }

});
</script>
