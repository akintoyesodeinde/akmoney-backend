@extends($activeTemplate . 'layouts.master')
@section('activePage', 'account')
@section('content')

<div class="max-w-md mx-auto py-12 px-4">
    <div class="bg-card-white rounded-3xl border border-border-gray shadow-sm p-8">

        {{-- Icon + Title --}}
        <div class="flex flex-col items-center mb-6">
            <div class="w-16 h-16 bg-brand-teal/10 rounded-2xl flex items-center justify-center mb-4">
                <i class="ri-mail-check-line text-3xl text-brand-teal"></i>
            </div>
            <h2 class="text-xl font-bold text-text-primary">Verify Your New Email</h2>
            <p class="text-sm text-text-secondary text-center mt-1">
                We sent a 6-digit code to
                <span class="font-semibold text-text-primary">{{ $user->pending_email }}</span>.
                Check your inbox and enter the code below.
            </p>
        </div>

        @if(session('info'))
            <div class="bg-blue-50 border border-blue-200 text-blue-700 text-sm px-4 py-3 rounded-xl mb-5">
                {{ session('info') }}
            </div>
        @endif

        @include($activeTemplate . 'partials.notify')

        {{-- OTP Form --}}
        <form action="{{ route('user.email.change.verify.submit') }}" method="POST">
            @csrf
            <div class="mb-5">
                <label class="block text-sm font-semibold text-text-primary mb-2">Verification Code</label>
                <input
                    type="text"
                    name="code"
                    inputmode="numeric"
                    maxlength="6"
                    class="w-full text-center text-2xl font-bold tracking-[0.5em] border border-border-gray rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-teal bg-bg-main text-text-primary"
                    placeholder="——————"
                    autocomplete="one-time-code"
                    autofocus
                    required
                >
                @error('code')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full py-3 bg-brand-teal text-white font-bold rounded-xl hover:opacity-90 transition-all">
                Confirm Email Change
            </button>
        </form>

        {{-- Resend --}}
        <div class="mt-5 text-center">
            <p class="text-sm text-text-secondary">
                Didn't receive the code?
                <a href="{{ route('user.email.change.resend') }}" class="text-brand-teal font-semibold hover:underline ml-1">
                    Resend Code
                </a>
            </p>
        </div>

        {{-- Security notice --}}
        <div class="mt-6 bg-orange-50 border border-orange-200 rounded-xl px-4 py-3 flex gap-3">
            <i class="ri-shield-flash-line text-orange-500 text-lg flex-shrink-0 mt-0.5"></i>
            <p class="text-xs text-orange-700">
                A security alert has been sent to your <strong>current</strong> email address.
                If you did not request this change, please contact support immediately.
            </p>
        </div>

        {{-- Cancel --}}
        <div class="mt-4 text-center">
            <a href="{{ route('user.profile.setting') }}" class="text-xs text-text-secondary hover:text-text-primary">
                Cancel &amp; return to profile
            </a>
        </div>

    </div>
</div>

@endsection
