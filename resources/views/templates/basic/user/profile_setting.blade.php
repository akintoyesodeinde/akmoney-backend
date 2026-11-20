@extends($activeTemplate . 'layouts.master')
@section('activePage', 'account')
@php
    $referralContent = getContent('referral.content', true);
@endphp
@section('content')
<form action="{{route('user.profile.submit')}}" method='POST' enctype="multipart/form-data">
    @csrf
    <div class="flex flex-col md:flex-row gap-6 mb-8 items-center justify-between">
        <div class="flex items-center gap-5">
          <div class="relative group">
            <img src="{{ asset('assets/images/user/profile/'.$user->image) }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-white shadow-md overflow-hidden" alt="Avatar">
            <label class="absolute -bottom-2 -right-2 bg-brand-teal text-white p-1.5 rounded-lg cursor-pointer shadow-lg hover:scale-110 transition-transform">
              <i class="ri-camera-switch-line text-sm"></i>
              <input type="file" name="image" class="hidden" accept="image/*">
            </label>
          </div>
          <div>
            <h2 class="text-2xl font-bold text-text-primary">
              {{ $user->firstname }}</h2>
            <div class="flex items-center gap-2 mt-1">
              @if($user->kv == 1)
                <span class="text-xs font-bold px-2 py-0.5 rounded bg-green-100 text-green-600 flex items-center gap-1">
                  <i class="ri-checkbox-circle-fill"></i> KYC Verified
                </span>
              @else
                <span class="text-xs font-bold px-2 py-0.5 rounded bg-orange-100 text-orange-600 flex items-center gap-1">
                  <i class="ri-error-warning-fill"></i> KYC Pending
                </span>
              @endif
            </div>
          </div>
        </div>
        
        @if (gs('referral_system'))
        <div class="w-full md:w-auto bg-brand-teal rounded-2xl p-4 flex items-center gap-4 border border-brand-teal shadow-sm">
            <div class="flex-1">
                <p class="text-[10px] text-white/60 uppercase font-bold tracking-widest">
                    Referral Link
                </p>
        
                <input
                    id="referralLink"
                    type="text"
                    readonly
                    value="{{ route('home') }}?reference={{ $user->username }}"
                    class="w-full bg-transparent text-white text-sm font-medium focus:outline-none select-all"
                />
            </div>
        
            <button
                id="copyReferralBtn"
                type="button"
                class="bg-brand-gold text-brand-teal p-2 rounded-xl hover:bg-white transition-colors">
                <i class="ri-file-copy-line"></i>
            </button>
        </div>
        @endif
    </div>
    {{-- Onboarding Progress Bar --}}
    @if(!$onboardingProgress['is_complete'])
    <div class="bg-card-white rounded-3xl p-6 border border-border-gray shadow-sm mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h4 class="font-bold text-text-primary">Account Setup Progress</h4>
                <p class="text-xs text-text-secondary mt-0.5">
                    {{ $onboardingProgress['remaining'] }} {{ $onboardingProgress['remaining'] === 1 ? 'step' : 'steps' }} remaining to complete your account
                </p>
            </div>
            <div class="text-right">
                <span class="text-2xl font-bold text-brand-teal">{{ $onboardingProgress['percentage'] }}%</span>
                <p class="text-[10px] text-text-secondary uppercase font-bold tracking-widest">Completed</p>
            </div>
        </div>
        <div class="w-full bg-bg-main h-2.5 rounded-full overflow-hidden mb-5">
            <div class="bg-brand-teal h-full rounded-full transition-all duration-700"
                 style="width: {{ $onboardingProgress['percentage'] }}%"></div>
        </div>
        <div class="flex flex-wrap gap-3">
            @foreach($onboardingProgress['steps'] as $step)
            <div class="flex items-center gap-2 px-3 py-2 rounded-xl flex-1 min-w-[140px]
                {{ $step['completed'] ? 'bg-green-50 border border-green-100' : 'bg-bg-main border border-border-gray' }}">
                <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0
                    {{ $step['completed'] ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                    <i class="{{ $step['completed'] ? 'ri-check-line' : 'ri-time-line' }} text-[10px]"></i>
                </div>
                <span class="text-xs font-medium whitespace-nowrap
                    {{ $step['completed'] ? 'text-green-700' : 'text-text-secondary' }}">
                    {{ $step['label'] }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-7">
          <div class="bg-card-white rounded-3xl p-6 md:p-8 border border-border-gray shadow-sm">
            <div class="mb-8">
              <h3 class="text-lg font-bold">Personal Information</h3>
              <p class="text-text-secondary text-sm">Update your contact and residential details</p>
            </div>
            <div class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                  <label class="text-xs font-bold text-text-secondary uppercase ml-1">First Name</label>
                  <input type="text" name="firstname"
                                        value="{{ $user->firstname }}" required class="w-full px-4 py-3.5 bg-bg-main border border-border-gray rounded-2xl focus:border-brand-teal focus:ring-1 focus:ring-brand-teal outline-none transition-all text-sm font-medium">
                </div>
                <div class="space-y-1.5">
                  <label class="text-xs font-bold text-text-secondary uppercase ml-1">Last Name</label>
                  <input type="text" name="lastname"
                                        value="{{ $user->lastname }}" required class="w-full px-4 py-3.5 bg-bg-main border border-border-gray rounded-2xl focus:border-brand-teal focus:ring-1 focus:ring-brand-teal outline-none transition-all text-sm font-medium">
                </div>
                <div class="space-y-1.5 md:col-span-2">
                  <label class="text-xs font-bold text-text-secondary uppercase ml-1">Email Address</label>
                  <input type="email" value="{{ $user->email }}" readonly class="w-full px-4 py-3.5 bg-bg-main border border-border-gray rounded-2xl focus:border-brand-teal focus:ring-1 focus:ring-brand-teal outline-none transition-all text-sm font-medium">
                </div>
                <div class="space-y-1.5">
                  <label class="text-xs font-bold text-text-secondary uppercase ml-1">Phone Number</label>
                  <input type="tel" value="{{ $user->mobile }}" readonly class="w-full px-4 py-3.5 bg-bg-main border border-border-gray rounded-2xl focus:border-brand-teal focus:ring-1 focus:ring-brand-teal outline-none transition-all text-sm font-medium">
                </div>
                <!--<div class="space-y-1.5">-->
                <!--  <label class="text-xs font-bold text-text-secondary uppercase ml-1">Date of Birth</label>-->
                <!--  <input type="date" value="1995-05-12" class="w-full px-4 py-3.5 bg-bg-main border border-border-gray rounded-2xl focus:border-brand-teal focus:ring-1 focus:ring-brand-teal outline-none transition-all text-sm font-medium">-->
                <!--</div>-->
                <div class="space-y-1.5 md:col-span-2">
                  <label class="text-xs font-bold text-text-secondary uppercase ml-1">Residential Address</label>
                  <textarea rows="2" name="address" class="w-full px-4 py-3.5 bg-bg-main border border-border-gray rounded-2xl focus:border-brand-teal focus:ring-1 focus:ring-brand-teal outline-none transition-all text-sm font-medium resize-none">{{ @$user->address }}</textarea>
                </div>
              </div>

              <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-brand-gold text-white font-bold py-4 rounded-2xl shadow-lg shadow-brand-gold/20 hover:bg-opacity-90 transition-all">
                  Save Changes
                </button>
                <button type="button" class="px-8 bg-white text-text-secondary border border-border-gray font-bold py-4 rounded-2xl hover:bg-bg-main transition-all">
                  Cancel
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="lg:col-span-5 space-y-6">
          <div class="bg-card-white rounded-3xl p-6 border border-border-gray shadow-sm">
            <h4 class="font-bold mb-6">Usage Limits</h4>
            <div class="space-y-6 mb-6">
              <div>
                <div class="flex justify-between text-xs mb-2">
                  <span class="text-text-secondary font-medium">Daily Transfer</span>
                  <span class="font-bold text-brand-teal">{{showAmount($daily)}} / {{showAmount(gs('user_daily_send_money_limit'))}}</span>
                </div>
                <div class="w-full bg-bg-main h-1.5 rounded-full overflow-hidden">
                  <div class="bg-brand-teal h-full rounded-full transition-all duration-500" style="width: {{ $dailyPercent }}%"></div>
                </div>
              </div>
              <div>
                <div class="flex justify-between text-xs mb-2">
                  <span class="text-text-secondary font-medium">Monthly Limit</span>
                  <span class="font-bold text-brand-teal">{{showAmount($monthly)}} / {{showAmount(gs('user_monthly_send_money_limit'))}}</span>
                </div>
                <div class="w-full bg-bg-main h-1.5 rounded-full">
                  <div
                        class="bg-brand-teal h-full rounded-full transition-all duration-500"
                        style="width: {{ $monthlyPercent }}%">
                    </div>
                </div>
              </div>
            </div>
            <div class="space-y-3">
              <!-- Upgrade Limit Button - Only for verified users -->
              <?php if ($user->kv == 1): ?>
                
              <?php else: ?>
                <div class="w-full text-center py-3 bg-gray-100 text-gray-500 text-xs font-bold rounded-xl border border-gray-300 cursor-not-allowed">
                  <i class="ri-lock-line mr-2"></i>Verify KYC to Upgrade
                </div>
              <?php endif; ?>
            </div>
          </div>

          <div class="bg-card-white rounded-3xl border border-border-gray shadow-sm overflow-hidden">
            <div class="p-4 border-b border-border-gray bg-bg-main/50 text-[10px] font-bold text-text-secondary uppercase">Security</div>
            @if(auth()->user()->has_pin)
            <a href="{{ route('user.view_reset_pin') }}" class="flex items-center p-5 justify-between hover:bg-bg-main transition-colors">
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-brand-teal/5 flex items-center justify-center text-brand-teal">
                  <i class="ri-lock-password-line text-xl"></i>
                </div>
                <div>
                  <p class="text-sm font-bold">Transaction PIN</p>
                  <p class="text-[11px] text-text-secondary">Change your existing PIN</p>
                </div>
              </div>
              <i class="ri-arrow-right-s-line text-text-secondary"></i>
            </a>
            @else
            <a href="{{ route('user.view_set_pin') }}" class="flex items-center p-5 justify-between hover:bg-bg-main transition-colors">
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-brand-gold/10 flex items-center justify-center" style="color:#A71B47;">
                  <i class="ri-lock-password-line text-xl"></i>
                </div>
                <div>
                  <p class="text-sm font-bold">Set Transaction PIN</p>
                  <p class="text-[11px] text-text-secondary">Required to authorise transfers</p>
                </div>
              </div>
              <span class="text-[10px] font-bold px-2 py-1 rounded-full" style="background:#fef2f2;color:#A71B47;">Not set</span>
            </a>
            @endif
            <div class="flex items-center p-5 justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-brand-teal/5 flex items-center justify-center text-brand-teal">
                        <i class="ri-shield-keyhole-line text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold">2FA Authentication</p>
                        <p class="text-[11px] text-text-secondary">Extra layer for transfers</p>
                    </div>
                </div>
            
                <label class="relative inline-flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        id="twoFaToggle"
                        class="sr-only peer"
                        {{ $user->ts == 1 ? 'checked' : '' }}
                    >
                    <div class="w-10 h-5 bg-gray-200 rounded-full peer
                        peer-checked:bg-brand-teal
                        after:content-['']
                        after:absolute after:top-[2px] after:left-[2px]
                        after:bg-white after:border after:rounded-full
                        after:h-4 after:w-4 after:transition-all
                        peer-checked:after:translate-x-full">
                    </div>
                </label>
            </div>


            <!--<div class="flex items-center p-5 justify-between border-t border-border-gray">-->
            <!--  <div class="flex items-center gap-4">-->
            <!--    <div class="w-10 h-10 rounded-xl bg-brand-teal/5 flex items-center justify-center text-brand-teal">-->
            <!--      <i class="ri-fingerprint-line text-xl"></i>-->
            <!--    </div>-->
              <!--  <div>-->
              <!--    <p class="text-sm font-bold">Biometric Login</p>-->
              <!--    <p class="text-[11px] text-text-secondary">Face ID or Touch ID</p>-->
              <!--  </div>-->
              <!--</div>-->
          <!--    <label class="relative inline-flex items-center cursor-pointer">-->
          <!--      <input type="checkbox" class="sr-only peer">-->
          <!--      <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand-teal"></div>-->
          <!--    </label>-->
          <!--  </div>-->
          <!--</div>-->
          <!--<button class="w-full py-4 text-red-500 font-bold text-sm bg-red-50 rounded-2xl hover:bg-red-100 transition-colors">-->
          <!--  Delete Account-->
          <!--</button>-->
        </div>
      </div>
    </div>
</form>


  <script>
    // 6-Digit PIN Auto-focus
    const pins = document.querySelectorAll('.pin-field');
    pins.forEach((pin, index) => {
      pin.addEventListener('input', (e) => {
        if (e.target.value.length === 1 && index < pins.length - 1) pins[index + 1].focus();
      });
      pin.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !e.target.value && index > 0) pins[index - 1].focus();
      });
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
  <script>
    document.getElementById('copyReferralBtn').addEventListener('click', () => {
        const text = document.getElementById('referralLink').value;

        navigator.clipboard.writeText(text).then(() => {
            // Optional feedback
            const btn = document.getElementById('copyReferralBtn');
            btn.innerHTML = '<i class="ri-check-line"></i>';
            setTimeout(() => {
                btn.innerHTML = '<i class="ri-file-copy-line"></i>';
            }, 1500);
        }).catch(err => {
            console.error('Copy failed:', err);
        });
    });
</script>
<script>
document.getElementById('twoFaToggle').addEventListener('change', function () {
    if (this.checked) {
        // 🔐 Turning ON → go to setup page
        window.location.href = "{{ route('user.twofactor') }}";
    } else {
        window.location.href = "{{ route('user.twofactor') }}";
    }
});
</script>


@endsection