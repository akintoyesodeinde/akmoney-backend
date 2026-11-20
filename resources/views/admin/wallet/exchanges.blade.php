@extends('admin.layouts.app')
@section('panel')

@push('topBar')
<div class="top-bar d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <h5 class="mb-0">@lang('Wallet Exchange History')</h5>
    <a href="{{ route('admin.wallet.index') }}" class="btn btn--primary btn--sm">
        <i class="las la-wallet"></i> @lang('All Wallets')
    </a>
</div>
@endpush

<div class="card">
    <div class="card-header">
        <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="@lang('Search TRX, currency or user')"
                class="form-control form-control-sm" style="max-width:240px">

            <select name="currency" class="form-select form-select-sm" style="max-width:140px" onchange="this.form.submit()">
                <option value="">@lang('All currencies')</option>
                @foreach($currencies as $cur)
                    <option value="{{ $cur }}" {{ request('currency') == $cur ? 'selected' : '' }}>{{ $cur }}</option>
                @endforeach
            </select>

            <button class="btn btn--primary btn--sm">@lang('Search')</button>
            @if(request()->hasAny(['search','currency']))
                <a href="{{ route('admin.wallet.exchanges') }}" class="btn btn--secondary btn--sm">@lang('Clear')</a>
            @endif
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive--sm table-responsive">
            <table class="table table--light style--two">
                <thead>
                    <tr>
                        <th>@lang('TRX / Date')</th>
                        <th>@lang('User')</th>
                        <th>@lang('From')</th>
                        <th>@lang('To')</th>
                        <th>@lang('Rate')</th>
                        <th>@lang('Status')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exchanges as $ex)
                    <tr>
                        <td>
                            <span class="fw-bold font-monospace small">{{ $ex->trx }}</span><br>
                            <small class="text--muted">
                                {{ showDateTime($ex->created_at) }}<br>
                                {{ diffForHumans($ex->created_at) }}
                            </small>
                        </td>
                        <td>
                            <span class="fw-bold">
                                {{ $ex->user->firstname }} {{ $ex->user->lastname }}
                            </span><br>
                            <small class="text--muted">
                                <a href="{{ route('admin.users.detail', $ex->user_id) }}">
                                    @{{ $ex->user->username }}
                                </a>
                            </small>
                        </td>
                        <td>
                            <span class="text--danger fw-bold">
                                -{{ showAmount($ex->from_amount, currencyFormat: false) }}
                            </span>
                            <span class="badge badge--secondary ms-1">{{ $ex->from_currency }}</span>
                        </td>
                        <td>
                            <span class="text--success fw-bold">
                                +{{ showAmount($ex->to_amount, currencyFormat: false) }}
                            </span>
                            <span class="badge badge--secondary ms-1">{{ $ex->to_currency }}</span>
                        </td>
                        <td class="small text--muted">
                            1 {{ $ex->from_currency }}<br>
                            = {{ number_format($ex->exchange_rate, 4) }} {{ $ex->to_currency }}
                        </td>
                        <td>
                            @if($ex->status == 1)
                                <span class="badge badge--success">@lang('Completed')</span>
                            @elseif($ex->status == 2)
                                <span class="badge badge--warning">@lang('Pending')</span>
                            @else
                                <span class="badge badge--danger">@lang('Failed')</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text--muted">@lang('No exchanges found')</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($exchanges->hasPages())
    <div class="card-footer">
        {{ paginateLinks($exchanges) }}
    </div>
    @endif
</div>
@endsection
