@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="max-w-lg mx-auto py-8 px-4">

    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-brand-teal flex items-center justify-center flex-shrink-0">
            <i class="ri-bank-card-line text-white text-lg"></i>
        </div>
        <div>
            <h2 class="text-lg font-extrabold text-text-primary">Card Payment</h2>
            <p class="text-xs text-text-secondary">Secured with Stripe</p>
        </div>
        <i class="ri-lock-2-fill text-text-secondary ml-auto"></i>
    </div>

    <div class="bg-teal-50 border border-teal-200 rounded-2xl px-5 py-4 mb-6 flex items-center justify-between">
        <span class="text-sm text-teal-700">Paying</span>
        <span class="text-xl font-extrabold text-brand-teal">
            {{ number_format($deposit->final_amount, 2) }}
            <span class="text-sm font-semibold">{{ $deposit->method_currency }}</span>
        </span>
    </div>

    <div class="bg-card-white rounded-2xl border border-border-gray p-6 shadow-sm">
        <div class="card-wrapper mb-5 flex justify-center"></div>

        <form role="form" class="disableSubmission payment appPayment" id="payment-form"
            method="{{ $data->method }}" action="{{ $data->url }}">
            @csrf
            <input type="hidden" value="{{ $data->track }}" name="track">

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Name on Card</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autocomplete="off" autofocus
                        placeholder="Full name as on card"
                        class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Card Number</label>
                    <input type="tel" name="cardNumber" autocomplete="off" value="{{ old('cardNumber') }}" required
                        placeholder="1234 5678 9012 3456"
                        class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent tracking-wider">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">Expiry Date</label>
                        <input type="tel" name="cardExpiry" value="{{ old('cardExpiry') }}" placeholder="MM / YY" autocomplete="off" required
                            class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-secondary uppercase tracking-wide mb-1.5">CVC</label>
                        <input type="tel" name="cardCVC" value="{{ old('cardCVC') }}" placeholder="123" autocomplete="off" required
                            class="w-full px-4 py-3 border border-border-gray rounded-xl text-sm text-text-primary focus:outline-none focus:ring-2 focus:ring-brand-teal bg-transparent">
                    </div>
                </div>
                <button type="submit"
                    class="w-full py-3.5 bg-brand-teal text-white font-bold rounded-xl hover:bg-opacity-90 transition text-sm flex items-center justify-center gap-2 mt-2">
                    <i class="ri-lock-2-line"></i>
                    Pay {{ number_format($deposit->final_amount, 2) }} {{ $deposit->method_currency }}
                </button>
            </div>
        </form>
    </div>

    <p class="text-xs text-text-secondary text-center mt-4">
        <i class="ri-shield-check-line mr-1"></i>
        Payments are processed securely by Stripe.
    </p>
</div>
@endsection

@push('script')
<script src="{{ asset('assets/global/js/card.js') }}"></script>
<script>
(function() {
    "use strict";
    new Card({
        form: '#payment-form',
        container: '.card-wrapper',
        formSelectors: {
            numberInput: 'input[name="cardNumber"]',
            expiryInput: 'input[name="cardExpiry"]',
            cvcInput:    'input[name="cardCVC"]',
            nameInput:   'input[name="name"]',
        }
    });

    @if ($deposit->from_api)
    document.getElementById('payment-form').addEventListener('submit', function () {
        this.querySelector('[type=submit]').innerHTML = '<i class="ri-loader-4-line animate-spin mr-1"></i> Processing…';
    });
    @endif
})();
</script>
@endpush
