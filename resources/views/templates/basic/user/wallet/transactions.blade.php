@extends($activeTemplate . 'layouts.master')
@section('activePage', 'wallets')
@section('content')

<div class="max-w-3xl mx-auto py-6 px-3">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('user.wallets') }}"
           class="w-9 h-9 flex items-center justify-center rounded-xl border border-border-gray text-text-secondary hover:bg-gray-100 transition flex-shrink-0">
            <i class="ri-arrow-left-line text-base"></i>
        </a>
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-11 h-11 rounded-full bg-teal-50 border border-teal-200 flex items-center justify-center font-bold text-sm text-brand-teal flex-shrink-0">
                {{ $wallet->currency_code }}
            </div>
            <div class="min-w-0">
                <h2 class="text-xl font-extrabold text-text-primary truncate">{{ $wallet->currency_name }}</h2>
                <p class="text-xs text-text-secondary">{{ $wallet->currency_code }} &middot; Transaction History</p>
            </div>
        </div>
        <div class="ml-auto text-right flex-shrink-0">
            <p class="text-xl font-extrabold text-text-primary">{{ number_format($wallet->balance, 2) }}</p>
            <p class="text-xs text-text-secondary">Current Balance</p>
        </div>
    </div>

    {{-- Transaction list --}}
    @if($items->isEmpty())
    <div class="bg-card-white rounded-2xl border border-border-gray p-14 text-center">
        <i class="ri-file-list-3-line text-4xl text-text-secondary mb-3 block"></i>
        <p class="text-text-secondary text-sm">No transactions yet for this wallet.</p>
    </div>
    @else

    <div class="bg-card-white rounded-2xl border border-border-gray overflow-hidden divide-y divide-border-gray">
        @foreach($items as $item)
        @php
            $isCredit = $item['sign'] === '+';
            $icon = match($item['type']) {
                'topup'    => 'ri-arrow-down-circle-line',
                'spend'    => 'ri-send-plane-line',
                'exchange' => 'ri-arrow-left-right-line',
                default    => 'ri-exchange-line',
            };
            $iconBg = match($item['type']) {
                'topup'    => 'bg-teal-50 text-brand-teal border-teal-200',
                'spend'    => 'bg-red-50 text-red-500 border-red-200',
                'exchange' => 'bg-amber-50 text-amber-600 border-amber-200',
                default    => 'bg-gray-50 text-text-secondary border-border-gray',
            };
            $amountColor = $isCredit ? 'text-teal-600' : 'text-red-500';
        @endphp
        <div class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50/60 transition">

            {{-- Icon --}}
            <div class="w-10 h-10 rounded-xl border {{ $iconBg }} flex items-center justify-center flex-shrink-0">
                <i class="{{ $icon }} text-base"></i>
            </div>

            {{-- Description + meta --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-text-primary truncate">{{ $item['description'] }}</p>
                <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                    <span class="text-xs text-text-secondary">{{ \Carbon\Carbon::parse($item['date'])->format('d M Y, h:i a') }}</span>
                    @if($item['trx'])
                    <span class="text-[10px] font-mono text-text-secondary bg-gray-100 px-1.5 py-0.5 rounded">{{ $item['trx'] }}</span>
                    @endif
                </div>
            </div>

            {{-- Amount --}}
            <div class="text-right flex-shrink-0">
                <p class="text-sm font-extrabold {{ $amountColor }}">
                    {{ $item['sign'] }}{{ number_format($item['amount'], 2) }}
                    <span class="text-xs font-normal">{{ $item['currency'] }}</span>
                </p>
                <p class="text-[10px] text-text-secondary capitalize mt-0.5">{{ $item['type'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    @endif

</div>

@endsection
