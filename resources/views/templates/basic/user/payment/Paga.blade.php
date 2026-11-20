@extends($activeTemplate . 'layouts.master')
@section('content')

<div class="max-w-md mx-auto py-10 px-4">
    <div class="bg-card-white rounded-2xl border border-border-gray shadow-sm p-8">

        {{-- ── Phone entry form ── --}}
        @if(!empty($data->needs_phone))

        <div class="text-center mb-6">
            <div class="w-16 h-16 rounded-full bg-teal-50 border-2 border-brand-teal flex items-center justify-center mx-auto mb-4">
                <i class="ri-smartphone-line text-3xl text-brand-teal"></i>
            </div>
            <h2 class="text-xl font-bold text-text-primary mb-1">Paga Mobile Payment</h2>
            <p class="text-sm text-text-secondary">
                Enter your Paga-registered phone number (Nigeria). You will receive a payment approval notification.
            </p>
        </div>

        <div class="bg-bg-main rounded-xl p-4 text-xs space-y-2 mb-6">
            <div class="flex justify-between">
                <span class="text-text-secondary">Amount</span>
                <span class="font-semibold">{{ $data->currency ?? 'NGN' }} {{ showAmount($deposit->final_amount, currencyFormat: false) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-text-secondary">Reference</span>
                <span class="font-mono">{{ $deposit->trx }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('user.deposit.confirm') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">
                    Paga Phone Number
                </label>
                <div class="flex items-center border border-border-gray rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-brand-teal">
                    <span class="px-3 text-sm text-text-secondary border-r border-border-gray bg-bg-main py-3">+</span>
                    <input type="tel" name="payer_phone" required autofocus
                           placeholder="e.g. 2348012345678"
                           class="flex-1 px-4 py-3 text-sm focus:outline-none bg-transparent text-text-primary">
                </div>
                <p class="text-[11px] text-text-secondary mt-1">
                    Include country code without +. Example: 2348012345678 for a Nigerian number.
                </p>
            </div>
            <button type="submit"
                class="w-full py-3 bg-brand-teal text-white font-bold rounded-xl hover:bg-opacity-90 transition text-sm">
                Send Payment Request
            </button>
        </form>

        {{-- ── Request sent ── --}}
        @elseif(!empty($data->success))

        <div class="text-center mb-6">
            <div class="w-16 h-16 rounded-full bg-teal-50 border-2 border-brand-teal flex items-center justify-center mx-auto mb-4">
                <i class="ri-smartphone-line text-3xl text-brand-teal"></i>
            </div>
            <h2 class="text-xl font-bold text-text-primary mb-1">Paga Request Sent</h2>
            <p class="text-sm text-text-secondary">
                A payment request has been sent to your Paga wallet at
                <strong class="text-text-primary">+{{ $data->phone ?? '—' }}</strong>.
                Open your Paga app or respond to the notification to approve.
            </p>
        </div>

        <div class="bg-bg-main rounded-xl p-4 text-xs space-y-2 mb-6">
            <div class="flex justify-between">
                <span class="text-text-secondary">Amount</span>
                <span class="font-semibold">{{ $deposit->method_currency }} {{ showAmount($deposit->final_amount, currencyFormat: false) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-text-secondary">Phone</span>
                <span class="font-mono">+{{ $data->phone }}</span>
            </div>
            @if(!empty($data->transaction_id))
            <div class="flex justify-between">
                <span class="text-text-secondary">Paga Ref</span>
                <span class="font-mono">{{ $data->transaction_id }}</span>
            </div>
            @endif
            <div class="flex justify-between">
                <span class="text-text-secondary">Status</span>
                <span class="font-semibold text-amber-600">Awaiting approval</span>
            </div>
        </div>

        <p class="text-xs text-center text-text-secondary">
            Not receiving the request? Verify your Paga account is active and your balance is sufficient.
        </p>

        {{-- ── Error ── --}}
        @else

        <div class="text-center">
            <div class="w-16 h-16 rounded-full bg-red-50 border-2 border-red-300 flex items-center justify-center mx-auto mb-4">
                <i class="ri-error-warning-line text-3xl text-red-500"></i>
            </div>
            <h2 class="text-xl font-bold text-text-primary mb-2">Payment Failed</h2>
            <p class="text-sm text-red-600 mb-6">{{ $data->error ?? 'An error occurred. Please try again.' }}</p>
            <a href="{{ $deposit->failed_url ?? url('/') }}"
               class="inline-block px-6 py-3 bg-brand-teal text-white font-bold rounded-xl text-sm hover:bg-opacity-90 transition">
                Go Back
            </a>
        </div>

        @endif

    </div>
</div>
@endsection
