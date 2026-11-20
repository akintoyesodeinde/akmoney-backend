<?php
// PIN Reset Modal Component
// Usage: <?php include 'components/pin-reset-modal.php'; 
?>

<!-- PIN Reset Modal -->
<div id="pinResetModal" class="fixed inset-0 bg-black/30 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4">
    <div class="bg-card-white rounded-3xl shadow-xl max-w-md w-full p-8 animate-in fade-in duration-300">
        <!-- Security Question Verification -->
        <div id="resetSecuritySection">
            <h2 class="text-2xl font-bold text-text-primary mb-2">Reset Your PIN</h2>
            <p class="text-text-secondary text-sm mb-6">Answer your security question to verify your identity</p>

            <form id="resetSecurityForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-text-primary mb-2">Security Question</label>
                    <p id="displaySecurityQuestion" class="p-4 bg-bg-main rounded-xl text-sm text-text-primary mb-4">
                        <!-- Security question will be displayed here -->
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-text-primary mb-2">Your Answer</label>
                    <input type="text" id="resetSecurityAnswer" placeholder="Enter your answer" required class="w-full px-4 py-3 bg-bg-main border border-border-gray rounded-xl focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all text-sm">
                </div>

                <div id="securityAnswerError" class="hidden p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600">
                    Incorrect answer. Please try again.
                </div>

                <button type="button" onclick="verifySecurityAnswer()" class="w-full bg-brand-teal text-white font-bold py-3 rounded-xl hover:bg-opacity-90 transition-all">
                    Verify
                </button>

                <button type="button" onclick="closePINResetModal()" class="w-full bg-bg-main text-text-secondary font-bold py-3 rounded-xl hover:bg-gray-300 transition-all text-sm">
                    Cancel
                </button>
            </form>
        </div>

        <!-- New PIN Setup (after security question verification) -->
        <div id="resetPINSection" class="hidden">
            <h2 class="text-2xl font-bold text-text-primary mb-2">Create New PIN</h2>
            <p class="text-text-secondary text-sm mb-6">Enter your new 4-digit PIN</p>

            <form id="resetPINForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-text-primary mb-2">New PIN</label>
                    <div class="flex gap-2 justify-center mb-4">
                        <input type="text" class="pin-reset-digit w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                        <input type="text" class="pin-reset-digit w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                        <input type="text" class="pin-reset-digit w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                        <input type="text" class="pin-reset-digit w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-text-primary mb-2">Confirm New PIN</label>
                    <div class="flex gap-2 justify-center">
                        <input type="text" class="pin-reset-confirm w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                        <input type="text" class="pin-reset-confirm w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                        <input type="text" class="pin-reset-confirm w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                        <input type="text" class="pin-reset-confirm w-12 h-12 text-center text-2xl font-bold bg-bg-main border-2 border-border-gray rounded-lg focus:border-brand-teal focus:ring-2 focus:ring-brand-teal/10 outline-none transition-all" maxlength="1" inputmode="numeric">
                    </div>
                </div>

                <div id="resetPINMismatchError" class="hidden p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600">
                    PINs do not match. Please try again.
                </div>

                <button type="button" onclick="completePINReset()" class="w-full bg-brand-teal text-white font-bold py-3 rounded-xl hover:bg-opacity-90 transition-all">
                    Reset PIN
                </button>

                <button type="button" onclick="backToSecurityVerification()" class="w-full bg-bg-main text-text-secondary font-bold py-3 rounded-xl hover:bg-gray-300 transition-all text-sm">
                    Back
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // PIN Reset - Auto-focus next input
    document.querySelectorAll('.pin-reset-digit, .pin-reset-confirm').forEach((input, index, array) => {
        input.addEventListener('input', (e) => {
            if (e.target.value.length === 1 && index < array.length - 1) {
                array[index + 1].focus();
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                array[index - 1].focus();
            }
        });
    });

    // Get PIN value
    function getPinResetValue(selector) {
        const inputs = document.querySelectorAll(selector);
        return Array.from(inputs).map(input => input.value).join('');
    }

    // Verify security answer
    function verifySecurityAnswer() {
        const answer = document.getElementById('resetSecurityAnswer').value;

        if (!answer) {
            alert('Please enter your answer');
            return;
        }

        // TODO: Backend verification
        // fetch('/api/pin/verify-security-answer', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify({ answer: answer })
        // }).then(response => response.json())
        //   .then(data => {
        //       if (data.success) {
        //           proceedToResetPIN();
        //       } else {
        //           document.getElementById('securityAnswerError').classList.remove('hidden');
        //       }
        //   });

        // Mock verification for now
        if (answer.toLowerCase() === 'fluffy') {
            proceedToResetPIN();
        } else {
            document.getElementById('securityAnswerError').classList.remove('hidden');
        }
    }

    // Proceed to reset PIN
    function proceedToResetPIN() {
        document.getElementById('resetSecuritySection').classList.add('hidden');
        document.getElementById('resetPINSection').classList.remove('hidden');
        // Clear inputs
        document.querySelectorAll('.pin-reset-digit, .pin-reset-confirm').forEach(input => input.value = '');
        document.getElementById('resetPINMismatchError').classList.add('hidden');
    }

    // Back to security verification
    function backToSecurityVerification() {
        document.getElementById('resetPINSection').classList.add('hidden');
        document.getElementById('resetSecuritySection').classList.remove('hidden');
    }

    // Complete PIN reset
    function completePINReset() {
        const newPin = getPinResetValue('.pin-reset-digit');
        const confirmPin = getPinResetValue('.pin-reset-confirm');

        if (newPin.length !== 4 || confirmPin.length !== 4) {
            alert('Please enter a 4-digit PIN');
            return;
        }

        if (newPin !== confirmPin) {
            document.getElementById('resetPINMismatchError').classList.remove('hidden');
            return;
        }

        // TODO: Backend PIN update
        // fetch('/api/pin/reset', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify({ new_pin: newPin })
        // }).then(response => response.json())
        //   .then(data => {
        //       if (data.success) {
        //           alert('PIN reset successfully!');
        //           closePINResetModal();
        //       }
        //   });

        alert('PIN reset successfully!');
        closePINResetModal();
    }

    // Initialize PIN reset modal
    function openPINResetModal() {
        // Get security question from backend or stored data
        // For now, using a mock question
        document.getElementById('displaySecurityQuestion').textContent = 'What is the name of your pet?';

        document.getElementById('pinResetModal').classList.remove('hidden');
        document.getElementById('resetSecuritySection').classList.remove('hidden');
        document.getElementById('resetPINSection').classList.add('hidden');

        // Clear inputs
        document.getElementById('resetSecurityAnswer').value = '';
        document.getElementById('securityAnswerError').classList.add('hidden');
    }

    // Close reset modal
    function closePINResetModal() {
        document.getElementById('pinResetModal').classList.add('hidden');
        // Re-open verification modal
        initPINVerificationModal(true);
    }

    // Close modal when clicking outside
    document.getElementById('pinResetModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closePINResetModal();
        }
    });
</script>