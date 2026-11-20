@extends($activeTemplate . 'layouts.master')
@section('activePage', 'send-money')
@section('content')

@php
    $sendMoney = $data->sendMoney ?? null;
    $recipient = $sendMoney?->recipient;
@endphp

<div class="max-w-2xl mx-auto py-6 md:py-8 px-4">

    {{-- Heading --}}
    <div class="mb-6">
        <a href="{{ route('user.send.money.history') }}"
           class="inline-flex items-center gap-1.5 text-xs text-text-secondary hover:text-text-primary mb-4 transition-colors">
            <i class="ri-arrow-left-line"></i> Back to Transactions
        </a>
        <h2 class="text-2xl font-extrabold text-text-primary">Manual Payment</h2>
        <p class="text-sm text-text-secondary mt-1">Complete your payment using the instructions below.</p>
    </div>

    {{-- Transfer summary --}}
    @if($sendMoney && $recipient)
    <div class="bg-card-white rounded-2xl border border-border-gray p-5 mb-4">
        <p class="text-[10px] font-bold text-text-muted uppercase tracking-widest mb-4">Transfer Summary</p>
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold text-white bg-brand-teal">
                    {{ strtoupper(substr($recipient->name ?? 'R', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="font-bold text-text-primary text-sm truncate">{{ $recipient->name ?? 'Recipient' }}</p>
                    @if(!empty($recipient->mobile))
                    <p class="text-xs text-text-secondary">{{ $recipient->mobile }}</p>
                    @endif
                </div>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-xl font-extrabold text-text-primary">
                    {{ showAmount($sendMoney->sending_amount, currencyFormat: false) }}
                    <span class="text-sm font-semibold text-text-secondary">{{ $sendMoney->sending_currency }}</span>
                </p>
                @if($sendMoney->sending_charge > 0)
                <p class="text-xs text-text-secondary mt-0.5">
                    incl. {{ showAmount($sendMoney->sending_charge, currencyFormat: false) }} {{ $sendMoney->sending_currency }} fee
                </p>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Payment instructions --}}
    <div class="bg-card-white rounded-2xl border border-border-gray overflow-hidden mb-4">
        <div class="border-b border-border-gray px-5 py-4 flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-brand-gold/10 flex items-center justify-center">
                <i class="ri-information-line text-brand-gold"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-text-primary">Payment Instructions</p>
                <p class="text-xs text-text-secondary">{{ $data->gateway->name ?? 'Manual Method' }}</p>
            </div>
        </div>
        <div class="px-5 py-4">
            <div class="flex items-center justify-between py-3 border-b border-border-gray">
                <span class="text-xs text-text-secondary uppercase tracking-wider">Amount to Pay</span>
                <span class="text-base font-extrabold text-brand-teal">
                    {{ showAmount($data->final_amount, currencyFormat: false) }} {{ __($data->method_currency) }}
                </span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-border-gray">
                <span class="text-xs text-text-secondary uppercase tracking-wider">Reference / TRX</span>
                <span class="text-sm font-mono font-semibold text-text-primary">{{ $data->trx }}</span>
            </div>
            @if($data->gateway->description ?? null)
            <div class="pt-4 prose prose-sm text-text-secondary max-w-none text-sm leading-relaxed">
                {!! $data->gateway->description !!}
            </div>
            @endif
        </div>
    </div>

    {{-- Upload proof form --}}
    <div class="bg-card-white rounded-2xl border border-border-gray overflow-hidden">
        <div class="border-b border-border-gray px-5 py-4 flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-brand-teal/10 flex items-center justify-center">
                <i class="ri-upload-cloud-2-line text-brand-teal"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-text-primary">Submit Payment Proof</p>
                <p class="text-xs text-text-secondary">Upload your payment confirmation screenshot or receipt</p>
            </div>
        </div>

        <form action="{{ route('user.deposit.manual.update') }}" method="POST"
              enctype="multipart/form-data" class="px-5 py-5 space-y-4">
            @csrf

            <x-viser-form identifier="id" identifierValue="{{ $gateway->form_id }}" />

            <button type="submit"
                class="w-full py-4 bg-brand-teal hover:bg-brand-teal/90 text-white rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition-all shadow-lg shadow-brand-teal/20">
                <i class="ri-send-plane-fill"></i>
                Submit Payment Proof
            </button>
        </form>
    </div>

</div>
@endsection
