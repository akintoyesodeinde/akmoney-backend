@extends($activeTemplate . 'layouts.master')
@section('activePage', 'wallets')
@section('content')

<div class="max-w-3xl mx-auto py-6 px-2">

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h2 class="text-2xl font-extrabold text-text-primary">Transfer History</h2>
            <p class="text-sm text-text-secondary mt-1">Your sent and received wallet transfers.</p>
        </div>
        <a href="{{ route('user.wallets') }}"
            class="flex items-center gap-2 px-4 py-2 border border-border-gray rounded-xl text-sm text-text-secondary hover:bg-gray-100 transition">
            <i class="ri-arrow-left-line"></i> Back to wallets
        </a>
    </div>

    <div class="bg-card-white rounded-2xl border border-border-gray overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-border-gray bg-gray-50/60">
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">Date</th>
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">Type</th>
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">Counterparty</th>
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">Amount</th>
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">Fee</th>
                        <th class="px-4 py-3 text-xs font-semibold text-text-secondary uppercase tracking-wide">Currency</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $t)
                    @php $isSender = $t->sender_id === $user->id; @endphp
                    <tr class="border-t border-border-gray hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3.5 text-sm text-text-secondary">{{ showDateTime($t->created_at, 'd M Y') }}</td>
                        <td class="px-4 py-3.5">
                            @if($isSender)
                                <span class="text-xs font-bold px-2 py-0.5 bg-red-100 text-red-600 rounded-full">Sent</span>
                            @else
                                <span class="text-xs font-bold px-2 py-0.5 bg-teal-100 text-teal-700 rounded-full">Received</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-sm">
                            @if($isSender)
                                <span class="font-semibold text-text-primary">{{ $t->receiver->firstname }} {{ $t->receiver->lastname }}</span>
                                <span class="text-text-secondary text-xs ml-1">@{{ $t->receiver->username }}</span>
                            @else
                                <span class="font-semibold text-text-primary">{{ $t->sender->firstname }} {{ $t->sender->lastname }}</span>
                                <span class="text-text-secondary text-xs ml-1">@{{ $t->sender->username }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-sm font-bold {{ $isSender ? 'text-red-600' : 'text-teal-700' }}">
                            {{ $isSender ? '-' : '+' }}{{ number_format($isSender ? $t->sender_deducted : $t->receiver_credited, 2) }}
                        </td>
                        <td class="px-4 py-3.5 text-sm text-text-secondary">
                            {{ $t->charge > 0 ? number_format($t->charge, 2) : '—' }}
                        </td>
                        <td class="px-4 py-3.5 text-sm font-semibold text-text-primary">{{ $t->currency_code }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-sm text-text-secondary">No transfers yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transfers->hasPages())
        <div class="px-4 py-3 border-t border-border-gray">
            {{ $transfers->links() }}
        </div>
        @endif
    </div>
</div>

@endsection
