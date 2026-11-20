@extends($activeTemplate . 'layouts.master')
@section('activePage', 'password')
@section('content')
<!--<section class="dashboard-section body-collapse transaction">-->
<!--    <div class="overlay pt-120">-->
<!--        <div class="container-fruid">-->
<!--            <div class="row justify-content-center mt-4">-->
<!--                <div class="col-md-8">-->
<!--                    <div class="card custom--card">-->
<!--                        <div class="card-header">-->
<!--                            <h5 class="card-title">-->
<!--                                {{ __($pageTitle) }}-->
<!--                            </h5>-->
<!--                        </div>-->
<!--                        <div class="card-body">-->
<!--                            <form method="post">-->
<!--                                @csrf-->
<!--                                <div class="mb-3">-->
<!--                                    <label class="form-label sm-text t-heading-font heading-clr fw-md">-->
<!--                                        @lang('Current Password')-->
<!--                                    </label>-->
<!--                                    <div class="input-group">-->
<!--                                        <input class="form-control form--control" name="current_password" type="password"-->
<!--                                            required autocomplete="current-password">-->
<!--                                        <span class="input-group-text pass-toggle border-start-0">-->
<!--                                            <i class="las la-eye-slash"></i>-->
<!--                                        </span>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="mb-3">-->
<!--                                    <label class="form-label sm-text t-heading-font heading-clr fw-md">-->
<!--                                        @lang('Password')-->
<!--                                    </label>-->
<!--                                    <div class="input-group">-->
<!--                                        <input-->
<!--                                            class="form-control form--control @if (gs('secure_password')) secure-password @endif"-->
<!--                                            name="password" type="password" required autocomplete="current-password">-->
<!--                                        <span class="input-group-text pass-toggle border-start-0">-->
<!--                                            <i class="las la-eye-slash"></i>-->
<!--                                        </span>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="mb-3">-->
<!--                                    <label class="form-label sm-text t-heading-font heading-clr fw-md">-->
<!--                                        @lang('Confirm Password')-->
<!--                                    </label>-->
<!--                                    <div class="input-group">-->
<!--                                        <input class="form-control form--control" name="password_confirmation" type="password"-->
<!--                                            required autocomplete="current-password">-->
<!--                                        <span class="input-group-text pass-toggle border-start-0">-->
<!--                                            <i class="las la-eye-slash"></i>-->
<!--                                        </span>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="form-group">-->
<!--                                    <button class="btn btn--base w-100 btn--xl" type="submit">@lang('Submit')</button>-->
<!--                                </div>-->
<!--                            </form>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</section>-->
<div
                    class="bg-card-white p-8 md:p-12 rounded-[2.5rem] shadow-sm border border-border-gray max-w-4xl mx-auto mt-8">

                    <div class="mb-10 flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-brand-teal/5 rounded-2xl flex items-center justify-center text-text-secondary">
                            <i class="ri-lock-password-line text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-text-primary tracking-tight">Security Credentials</h3>
                            <p class="text-text-secondary text-xs uppercase tracking-widest mt-1 font-semibold">Update your
                                access key</p>
                        </div>
                    </div>

                    <form id="passwordForm" class="space-y-8">
                        <div class="grid grid-cols-1 gap-8">

                            <div class="space-y-3">
                                <label
                                    class="block text-xs font-semibold text-text-primary uppercase tracking-wider">Current
                                    Password</label>
                                <div class="relative group">
                                    <input type="password" id="currentPassword" required name="current_password"
                                        class="w-full px-5 py-4 bg-bg-secondary border border-border-gray rounded-2xl text-sm font-medium text-text-primary focus:bg-card-white focus:border-brand-teal outline-none transition-all pr-12">
                                    <button type="button" onclick="toggleVisibility('currentPassword')"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-text-secondary hover:text-brand-teal">
                                        <i class="ri-eye-line" id="currentPassword-icon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-3">
                                    <label
                                        class="block text-xs font-semibold text-text-secondary uppercase tracking-wider">New
                                        Password</label>
                                    <div class="relative">
                                        <input type="password"
<!--                                            name="password" id="newPassword" onkeyup="checkStrength()" required
                                            class="w-full px-5 py-4 bg-bg-secondary border border-border-gray rounded-2xl text-sm font-medium text-text-primary focus:bg-card-white focus:border-brand-teal outline-none transition-all pr-12">
                                        <button type="button" onclick="toggleVisibility('newPassword')"
                                            class="absolute right-4 top-1/2 -translate-y-1/2 text-text-secondary hover:text-brand-teal">
                                            <i class="ri-eye-line" id="newPassword-icon"></i>
                                        </button>
                                    </div>
                                    <div class="flex gap-1 px-1 mt-2">
                                        <div id="str-1" class="h-1 flex-1 bg-border-gray rounded-full transition-all">
                                        </div>
                                        <div id="str-2" class="h-1 flex-1 bg-border-gray rounded-full transition-all">
                                        </div>
                                        <div id="str-3" class="h-1 flex-1 bg-border-gray rounded-full transition-all">
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <label
                                        class="block text-xs font-semibold text-text-secondary uppercase tracking-wider">Confirm
                                        New Password</label>
                                    <input type="password" id="confirmPassword" required name="password_confirmation"
                                        class="w-full px-5 py-4 bg-bg-secondary border border-border-gray rounded-2xl text-sm font-medium text-text-primary focus:bg-card-white focus:border-brand-teal outline-none transition-all">
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex flex-col md:flex-row items-center justify-between gap-6 pt-6 border-t border-border-light">
                            <p class="text-[10px] text-text-secondary leading-relaxed max-w-xs">
                                Password must be at least 12 characters and include a mix of uppercase, numbers, and
                                symbols.
                            </p>
                            <button type="submit"
                                class="w-full md:w-auto bg-brand-gold text-white px-12 py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-brand-gold/20 hover:shadow-brand-gold/40 transition-all active:scale-95">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
          

    <script>
        // Toggle password visibility
        function toggleVisibility(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '-icon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('ri-eye-line', 'ri-eye-off-line');
            } else {
                input.type = 'password';
                icon.classList.replace('ri-eye-off-line', 'ri-eye-line');
            }
        }

        // Password Strength Meter
        function checkStrength() {
            const val = document.getElementById('newPassword').value;
            const bars = [document.getElementById('str-1'), document.getElementById('str-2'), document.getElementById('str-3')];

            // Reset bars
            bars.forEach(b => b.className = 'h-1 flex-1 bg-border-gray rounded-full transition-all');

            if (val.length > 0) {
                if (val.length < 6) {
                    bars[0].classList.add('bg-red-400'); // Weak
                } else if (val.length < 10) {
                    bars[0].classList.add('bg-brand-gold'); // Medium (Brand Gold)
                    bars[1].classList.add('bg-brand-gold');
                } else {
                    bars[0].classList.add('bg-emerald-400'); // Strong
                    bars[1].classList.add('bg-emerald-400');
                    bars[2].classList.add('bg-emerald-400');
                }
            }
        }

        // Toast notification function
        function showToast(message, type = 'error') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');

            const bgColor = type === 'error' ? 'bg-red-500' : type === 'success' ? 'bg-emerald-500' : 'bg-blue-500';
            const icon = type === 'error' ? 'ri-error-warning-line' : type === 'success' ? 'ri-checkbox-circle-line' : 'ri-information-line';

            toast.className = `${bgColor} text-white px-6 py-4 rounded-xl shadow-lg flex items-center gap-3 animate-slide-in max-w-sm`;
            toast.innerHTML = `
        <i class="${icon} text-xl"></i>
        <span class="text-sm font-medium">${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto text-white/80 hover:text-white">
            <i class="ri-close-line"></i>
        </button>
    `;

            container.appendChild(toast);

            // Auto-remove after 4 seconds
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.style.animation = 'slideOut 0.3s ease-out forwards';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 4000);
        }

        // Form Submission Logic
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const newPass = document.getElementById('newPassword').value;
            const confirmPass = document.getElementById('confirmPassword').value;

            if (newPass !== confirmPass) {
                showToast('Passwords do not match.', 'error');
                return;
            }

            // Replace with your actual API call
            console.log("Password change requested.");
            showToast('Password updated successfully!', 'success');
        });
    </script>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            // Initialize sidebar after page load
            window.initSidebar();

            // Initialize sidebar toggle button
            const toggleBtn = document.getElementById('toggle-sidebar');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    window.toggleSidebar();
                });
            }
        });
    </script>


@endsection

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
