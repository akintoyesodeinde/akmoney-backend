@extends('admin.layouts.app')
@section('panel')

@push('topBar')
<div class="top-bar d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <h5 class="mb-0">@lang('Wallet Transfers')</h5>
    <a href="{{ route('admin.wallet.index') }}" class="btn btn--secondary btn--sm">
        <i class="las la-wallet"></i> @lang('All Wallets')
    </a>
</div>
@endpush

{{-- Summary cards --}}
@if($totalTransferred->count())
<div class="row g-3 mb-4">
    @foreach($totalTransferred as $stat)
    <div class="col-6 col-md-3 col-xl-2">
        <div class="card text-center p-3 border">
            <div class="fw-bold text-muted small text-uppercase">{{ $stat->currency_code }}</div>
            <div class="fs-6 fw-bold mt-1">{{ showAmount($stat->total, currencyFormat: false) }}</div>
            <div class="text-muted" style="font-size:11px">{{ $stat->count }} @lang('transfers')</div>
        </div>
    </div>
    @endforeach
</div>
@endif

<div class="card">
    <div class="card-header">
        <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="@lang('TRX, user, currency')"
                class="form-control form-control-sm" style="max-width:240px">

            <select name="currency" class="form-select form-select-sm" style="max-width:140px" onchange="this.form.submit()">
                <option value="">@lang('All currencies')</option>
                @foreach($currencies as $cur)
                    <option value="{{ $cur }}" {{ request('currency') == $cur ? 'selected' : '' }}>{{ $cur }}</option>
                @endforeach
            </select>

            <button class="btn btn--primary btn--sm">@lang('Search')</button>
            @if(request()->hasAny(['search','currency']))
                <a href="{{ route('admin.wallet.transfers') }}" class="btn btn--secondary btn--sm">@lang('Clear')</a>
            @endif
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive--sm table-responsive">
            <table class="table table--light style--two">
                <thead>
                    <tr>
                        <th>@lang('TRX')</th>
                        <th>@lang('Sender')</th>
                        <th>@lang('Receiver')</th>
                        <th>@lang('Amount')</th>
                        <th>@lang('Charge')</th>
                        <th>@lang('Credited')</th>
                        <th>@lang('Currency')</th>
                        <th>@lang('Date')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                    <tr>
                        <td>
                            <span class="fw-bold">{{ $transfer->trx }}</span>
                        </td>
                        <td>
                            <span class="fw-bold">{{ $transfer->sender->firstname }} {{ $transfer->sender->lastname }}</span><br>
                            <small class="text--muted">
                                <a href="{{ route('admin.users.detail', $transfer->sender_id) }}">@{{ $transfer->sender->username }}</a>
                            </small>
                        </td>
                        <td>
                            <span class="fw-bold">{{ $transfer->receiver->firstname }} {{ $transfer->receiver->lastname }}</span><br>
                            <small class="text--muted">
                                <a href="{{ route('admin.users.detail', $transfer->receiver_id) }}">@{{ $transfer->receiver->username }}</a>
                            </small>
                        </td>
                        <td class="fw-bold">
                            {{ showAmount($transfer->amount, currencyFormat: false) }}
                        </td>
                        <td>
                            @if($transfer->charge > 0)
                                <span class="text-danger fw-bold">{{ showAmount($transfer->charge, currencyFormat: false) }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="fw-bold text-success">
                            {{ showAmount($transfer->receiver_credited, currencyFormat: false) }}
                        </td>
                        <td>
                            <span class="badge badge--primary">{{ $transfer->currency_code }}</span>
                        </td>
                        <td>
                            {{ showDateTime($transfer->created_at) }}<br>
                            <small class="text--muted">{{ diffForHumans($transfer->created_at) }}</small>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text--muted">@lang('No wallet transfers found')</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($transfers->hasPages())
    <div class="card-footer">
        {{ paginateLinks($transfers) }}
    </div>
    @endif
</div>

@endsection
