@extends($activeTemplate . 'layouts.master')
@section('content')

<div class="max-w-md mx-auto py-10 px-4">
    <div class="bg-card-white rounded-2xl border border-border-gray shadow-sm p-8">

        {{-- ── Phone entry form (first visit, no phone submitted yet) ── --}}
        @php
            $correspondentNames = [
                'MTN_MOMO_NGA'    => 'MTN Mobile Money Nigeria',
                'AIRTEL_MOMO_NGA' => 'Airtel Money Nigeria',
                'MTN_MOMO_GHA'    => 'MTN Mobile Money Ghana',
                'VODAFONE_GHA'    => 'Vodafone Cash Ghana',
                'MPESA_KEN'       => 'M-Pesa Kenya',
                'MTN_MOMO_CMR'    => 'MTN Mobile Money Cameroon',
                'ORANGE_CMR'      => 'Orange Money Cameroon',
                'MTN_MOMO_CIV'    => 'MTN Mobile Money Côte d\'Ivoire',
                'ORANGE_CIV'      => 'Orange Money Côte d\'Ivoire',
                'MPESA_DRC'       => 'M-Pesa DRC',
                'AIRTEL_DRC'      => 'Airtel Money DRC',
                'ORANGE_DRC'      => 'Orange Money DRC',
                'MTN_MOMO_ZMB'    => 'MTN Mobile Money Zambia',
                'AIRTEL_ZMB'      => 'Airtel Money Zambia',
                'MTN_MOMO_TZA'    => 'MTN Mobile Money Tanzania',
                'MPESA_TZA'       => 'M-Pesa Tanzania',
                'AIRTEL_TZA'      => 'Airtel Money Tanzania',
                'MTN_MOMO_RWA'    => 'MTN Mobile Money Rwanda',
                'AIRTEL_RWA'      => 'Airtel Money Rwanda',
                'MTN_MOMO_UGA'    => 'MTN Mobile Money Uganda',
                'AIRTEL_UGA'      => 'Airtel Money Uganda',
                'ORANGE_SEN'      => 'Orange Money Sénégal',
                'FREE_SEN'        => 'Free Money Sénégal',
                'ORANGE_MLI'      => 'Orange Money Mali',
                'MOOV_MLI'        => 'Moov Money Mali',
                'ORANGE_BFA'      => 'Orange Money Burkina Faso',
                'MOOV_BFA'        => 'Moov Money Burkina Faso',
            ];
            $correspondentCode  = $data->correspondent ?? '';
            $operatorName       = $correspondentNames[$correspondentCode] ?? ($correspondentCode ?: 'Mobile Money');
        @endphp

        @if(!empty($data->needs_phone))

        <div class="text-center mb-6">
            <div class="w-16 h-16 rounded-full bg-teal-50 border-2 border-brand-teal flex items-center justify-center mx-auto mb-4">
                <i class="ri-smartphone-line text-3xl text-brand-teal"></i>
            </div>
            <h2 class="text-xl font-bold text-text-primary mb-1">{{ $operatorName }}</h2>
            <p class="text-sm text-text-secondary">
                Enter your {{ $operatorName }} phone number.
                You will receive a payment prompt to approve.
            </p>
        </div>

        @if(!empty($data->error))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-4">
            {{ $data->error }}
        </div>
        @endif

        @php
            $gatewayAmount = round($deposit->final_amount * $deposit->rate, 2);
        @endphp
        <div class="bg-bg-main rounded-xl p-4 text-xs space-y-2 mb-6">
            <div class="flex justify-between">
                <span class="text-text-secondary">Amount</span>
                <span class="font-semibold">{{ $data->currency ?? '' }} {{ showAmount($gatewayAmount, currencyFormat: false) }}</span>
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
                    Mobile Money Phone Number
                </label>
                @php
                    $phoneExamples = [
                        'MTN_MOMO_NGA' => ['prefix' => '234', 'example' => '2348012345678', 'label' => 'Nigeria (MTN)'],
                        'AIRTEL_MOMO_NGA' => ['prefix' => '234', 'example' => '2348012345678', 'label' => 'Nigeria (Airtel)'],
                        'MTN_MOMO_GHA' => ['prefix' => '233', 'example' => '233500000000', 'label' => 'Ghana (MTN)'],
                        'VODAFONE_GHA' => ['prefix' => '233', 'example' => '233200000000', 'label' => 'Ghana (Vodafone)'],
                        'MPESA_KEN' => ['prefix' => '254', 'example' => '254712345678', 'label' => 'Kenya (M-Pesa)'],
                    ];
                    $correspondent = $data->correspondent ?? '';
                    $hint = $phoneExamples[$correspondent] ?? ['prefix' => '', 'example' => '', 'label' => ''];
                @endphp
                <div class="flex items-center border border-border-gray rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-brand-teal">
                    <span class="px-3 text-sm text-text-secondary border-r border-border-gray bg-bg-main py-3">+</span>
                    <input type="tel" name="payer_phone" required autofocus
                           placeholder="{{ $hint['example'] ?: 'e.g. 2348012345678' }}"
                           class="flex-1 px-4 py-3 text-sm focus:outline-none bg-transparent text-text-primary">
                </div>
                <p class="text-[11px] text-text-secondary mt-1">
                    Include country code, no leading +.
                    @if($hint['example'])
                        Example for {{ $hint['label'] }}: <span class="font-mono">{{ $hint['example'] }}</span>
                    @endif
                </p>
            </div>
            <button type="submit"
                class="w-full py-3 bg-brand-teal text-white font-bold rounded-xl hover:bg-opacity-90 transition text-sm">
                Send Payment Request
            </button>
        </form>

        {{-- ── Payment request sent, awaiting phone approval ── --}}
        @elseif(!empty($data->success))

        <div class="text-center mb-6">
            <div class="w-16 h-16 rounded-full bg-teal-50 border-2 border-brand-teal flex items-center justify-center mx-auto mb-4">
                <i class="ri-smartphone-line text-3xl text-brand-teal"></i>
            </div>
            <h2 class="text-xl font-bold text-text-primary mb-1">Check Your Phone</h2>
            <p class="text-sm text-text-secondary">
                A payment request has been sent to
                <strong class="text-text-primary">+{{ $data->phone ?? '—' }}</strong>
                via <strong class="text-text-primary">{{ $correspondentNames[$data->correspondent ?? ''] ?? ($data->correspondent ?? 'Mobile Money') }}</strong>.
                Approve it on your phone to complete the payment.
            </p>
        </div>

        <div class="bg-bg-main rounded-xl p-4 text-xs space-y-2 mb-6">
            <div class="flex justify-between">
                <span class="text-text-secondary">Amount</span>
                <span class="font-semibold">{{ $deposit->method_currency }} {{ showAmount(round($deposit->final_amount * $deposit->rate, 2), currencyFormat: false) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-text-secondary">Phone</span>
                <span class="font-mono">+{{ $data->phone }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-text-secondary">Reference</span>
                <span class="font-mono">{{ $deposit->trx }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-text-secondary">Status</span>
                <span class="font-semibold text-amber-600">Awaiting confirmation</span>
            </div>
        </div>

        <p class="text-xs text-center text-text-secondary">
            Did not receive the prompt? Check your mobile money app or USSD menu.
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
