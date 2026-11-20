@php
    $kycInstruction = getContent('kyc_instruction_user.content', true);
@endphp
    <div id="kycNotification" class="fixed top-24 right-4 z-50">
        @if (auth()->user()->kv == Status::KYC_REJECTED)
            <div class="bg-card-white rounded-2xl shadow-lg border-l-4 border-brand-gold p-5 max-w-sm w-85 flex gap-4">
                <!-- Icon -->
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-brand-gold rounded-full flex items-center justify-center">
                        <i class="ri-shield-cross-line text-xl text-white"></i>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1">
                    <h3 class="font-bold text-text-primary mb-1">KYC Documents Rejected</h3>
                    @if(auth()->user()->kyc_rejection_reason)
                        <p class="text-sm text-brand-gold font-medium mb-1">Reason: {{ auth()->user()->kyc_rejection_reason }}</p>
                    @else
                        <p class="text-sm text-text-secondary mb-1">{{ __(@$kycInstruction->data_values->reject_instruction) }}</p>
                    @endif
                    <div class="flex gap-2 mt-2">
                        <a href="{{ route('user.kyc.form') }}" class="text-xs font-bold px-3 py-1.5 bg-brand-gold text-white rounded-lg hover:bg-brand-teal-dark transition-all">
                            Re-submit Documents
                        </a>
                        <button onclick="closeKYCNotification()" class="text-xs font-bold px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-all">
                            Dismiss
                        </button>
                    </div>
                </div>
                <!-- Close Button -->
                <button onclick="closeKYCNotification()" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>
        @elseif (auth()->user()->kv == Status::KYC_UNVERIFIED)
            <div class="bg-card-white rounded-2xl shadow-lg border-l-4 border-brand-teal p-5 max-w-sm w-85 flex gap-4">
                <!-- Icon -->
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-brand-teal rounded-full flex items-center justify-center">
                        <i class="ri-shield-check-line text-xl text-card-white"></i>
                    </div>
                </div>
    
                <!-- Content -->
                <div class="flex-1">
                    <h3 class="font-bold text-text-primary mb-1">KYC Verification Required</h3>
                    <p class="text-sm text-text-secondary mb-3">
                        {{ __(@$kycInstruction->data_values->verification_instruction) }}
                    </p>
                    <div class="flex gap-2">
                        <a href="{{ route('user.kyc.form') }}" class="text-xs font-bold px-3 py-1.5 bg-brand-gold text-white rounded-lg hover:bg-brand-teal-dark transition-all">
                            Start Verification
                        </a>
                        <button onclick="closeKYCNotification()" class="text-xs font-bold px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-all">
                            Dismiss
                        </button>
                    </div>
                </div>
            <!-- Close Button -->
            <button onclick="closeKYCNotification()" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                <i class="ri-close-line text-xl"></i>
            </button>
        </div>
        @elseif(auth()->user()->kv == Status::KYC_PENDING)
            <div class="bg-card-white rounded-2xl shadow-lg border-l-4 border-brand-teal p-5 max-w-sm w-85 flex gap-4">
                <!-- Icon -->
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-brand-teal rounded-full flex items-center justify-center">
                        <i class="ri-shield-check-line text-xl text-card-white"></i>
                    </div>
                </div>
    
                <!-- Content -->
                <div class="flex-1">
                    <h3 class="font-bold text-text-primary mb-1">KYC Verification Pending</h3>
                    <p class="text-sm text-text-secondary mb-3">
                        {{ __(@$kycInstruction->data_values->pending_instruction) }}
                    </p>
                    <div class="flex gap-2">
                        <a href="{{ route('user.kyc.data') }}" class="text-xs font-bold px-3 py-1.5 bg-brand-gold text-white rounded-lg hover:bg-brand-teal-dark transition-all">
                            View Submitted Info
                        </a>
                        <button onclick="closeKYCNotification()" class="text-xs font-bold px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-all">
                            Dismiss
                        </button>
                    </div>
                </div>
                <!-- Close Button -->
                <button onclick="closeKYCNotification()" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>
        @endif
    </div>

<script>
    // KYC Notification Functions
    function closeKYCNotification() {
        const notification = document.getElementById('kycNotification');
        if (notification) {
            // Add slide-out to right animation
            notification.style.animation = 'slideOutRight 0.4s ease-in-out forwards';
            setTimeout(() => {
                notification.remove();
            }, 400);
        }
    }
</script>