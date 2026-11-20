@extends($activeTemplate . 'layouts.master')
@section('activePage', 'deposit-history')
@section('content')

<div class="max-w-5xl mx-auto py-6 px-2">

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h2 class="text-2xl font-extrabold text-text-primary">Deposit History</h2>
            <p class="text-sm text-text-secondary mt-1">All top-ups and deposits to your account.</p>
        </div>
    </div>

    {{-- Search --}}
    <form method="GET" class="mb-5">
        <div class="flex gap-2">
            <div class="relative flex-1 max-w-sm">
                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary text-sm"></i>
                <input name="search" value="{{ request('search') }}" placeholder="Search by transaction ID…"
                    class="w-full pl-9 pr-4 py-2.5 border border-border-gray rounded-xl text-sm bg-card-white text-text-primary focus:outline-none focus:ring-2 focus:ring-brand-teal">
            </div>
            <button type="submit"
                class="px-5 py-2.5 bg-brand-teal text-white text-sm font-semibold rounded-xl hover:bg-opacity-90 transition">Search</button>
            @if(request('search'))
            <a href="{{ route('user.deposit.history') }}"
                class="px-4 py-2.5 border border-border-gray text-sm text-text-secondary rounded-xl hover:bg-gray-100 transition">Clear</a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-card-white rounded-2xl border border-border-gray overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-border-gray bg-gray-50/60">
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">Date</th>
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">Transaction ID</th>
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">Method</th>
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">Amount</th>
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">Charge</th>
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">Payable</th>
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deposits as $deposit)
                    @php
                        $statusMap = [
                            0 => ['label' => 'Pending',   'class' => 'bg-amber-100 text-amber-700'],
                            1 => ['label' => 'Completed', 'class' => 'bg-teal-100 text-teal-700'],
                            2 => ['label' => 'Processing','class' => 'bg-blue-100 text-blue-700'],
                            3 => ['label' => 'Rejected',  'class' => 'bg-red-100 text-red-600'],
                        ];
                        $s = $statusMap[$deposit->status] ?? ['label' => 'Unknown', 'class' => 'bg-gray-100 text-gray-600'];
                    @endphp
                    <tr class="border-t border-border-gray hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3.5 text-sm text-text-secondary whitespace-nowrap">
                            {{ showDateTime($deposit->created_at, 'd M Y') }}
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="font-mono text-xs text-text-primary bg-gray-100 px-2 py-1 rounded-lg">{{ $deposit->trx }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-sm text-text-primary">
                            {{ $deposit->gateway?->name ?? $deposit->methodName() }}
                            <span class="text-text-secondary text-xs ml-1">{{ $deposit->method_currency }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-sm font-semibold text-text-primary">
                            {{ showAmount($deposit->amount) }}
                        </td>
                        <td class="px-4 py-3.5 text-sm text-text-secondary">
                            {{ showAmount($deposit->charge) }}
                        </td>
                        <td class="px-4 py-3.5 text-sm font-bold text-brand-teal">
                            {{ number_format($deposit->final_amount, 2) }}
                            <span class="text-xs font-normal text-text-secondary">{{ $deposit->method_currency }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $s['class'] }}">{{ $s['label'] }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-sm text-text-secondary">
                            <i class="ri-inbox-2-line text-3xl block mb-2 opacity-40"></i>
                            No deposit records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($deposits->hasPages())
    <div class="mt-4 flex justify-center">
        {{ $deposits->links() }}
    </div>
    @endif

</div>
@endsection
