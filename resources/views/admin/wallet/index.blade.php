@extends('admin.layouts.app')
@section('panel')

@push('topBar')
<div class="top-bar d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <h5 class="mb-0">@lang('Manage Wallets')</h5>
    <a href="{{ route('admin.wallet.exchanges') }}" class="btn btn--info btn--sm">
        <i class="las la-exchange-alt"></i> @lang('Exchange History')
    </a>
</div>
@endpush

{{-- Summary cards --}}
@if($totalBalance->count())
<div class="row g-3 mb-4">
    @foreach($totalBalance as $bal)
    <div class="col-6 col-md-3 col-xl-2">
        <div class="card text-center p-3 border">
            <div class="fw-bold text-muted small text-uppercase">{{ $bal->currency_code }}</div>
            <div class="fs-6 fw-bold mt-1">{{ showAmount($bal->total, currencyFormat: false) }}</div>
        </div>
    </div>
    @endforeach
</div>
@endif

<div class="card">
    <div class="card-header">
        <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="@lang('Search by user or currency')"
                class="form-control form-control-sm" style="max-width:240px">

            <select name="currency" class="form-select form-select-sm" style="max-width:140px" onchange="this.form.submit()">
                <option value="">@lang('All currencies')</option>
                @foreach($currencies as $cur)
                    <option value="{{ $cur }}" {{ request('currency') == $cur ? 'selected' : '' }}>{{ $cur }}</option>
                @endforeach
            </select>

            <div class="form-check form-check-inline mb-0">
                <input class="form-check-input" type="checkbox" name="remittance" id="remittanceFilter"
                    value="1" {{ request('remittance') ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="form-check-label small" for="remittanceFilter">@lang('Remittance only')</label>
            </div>

            <button class="btn btn--primary btn--sm">@lang('Search')</button>
            @if(request()->hasAny(['search','currency','remittance']))
                <a href="{{ route('admin.wallet.index') }}" class="btn btn--secondary btn--sm">@lang('Clear')</a>
            @endif
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive--sm table-responsive">
            <table class="table table--light style--two">
                <thead>
                    <tr>
                        <th>@lang('User')</th>
                        <th>@lang('Currency')</th>
                        <th>@lang('Balance')</th>
                        <th>@lang('Type')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Created')</th>
                        <th>@lang('Actions')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wallets as $wallet)
                    <tr>
                        <td>
                            <span class="fw-bold">
                                {{ $wallet->user->firstname }} {{ $wallet->user->lastname }}
                            </span><br>
                            <small class="text--muted">
                                <a href="{{ route('admin.users.detail', $wallet->user_id) }}">
                                    {{ $wallet->user->username }}
                                </a>
                            </small>
                        </td>
                        <td>
                            <span class="fw-bold">{{ $wallet->currency_code }}</span><br>
                            <small class="text--muted">{{ $wallet->currency_name }}</small>
                        </td>
                        <td class="fw-bold">
                            {{ showAmount($wallet->balance, currencyFormat: false) }}
                            <small class="text--muted">{{ $wallet->currency_symbol ?: $wallet->currency_code }}</small>
                        </td>
                        <td>
                            @if($wallet->is_default)
                                <span class="badge badge--primary">@lang('Default')</span>
                            @endif
                            @if($wallet->remittance_enabled)
                                <span class="badge badge--warning">@lang('Remittance')</span>
                            @endif
                        </td>
                        <td>
                            @if($wallet->status)
                                <span class="badge badge--success">@lang('Active')</span>
                            @else
                                <span class="badge badge--danger">@lang('Suspended')</span>
                            @endif
                        </td>
                        <td>
                            {{ showDateTime($wallet->created_at) }}<br>
                            <small class="text--muted">{{ diffForHumans($wallet->created_at) }}</small>
                        </td>
                        <td>
                            <button type="button"
                                class="btn btn--success btn--sm"
                                data-bs-toggle="modal"
                                data-bs-target="#creditModal"
                                data-id="{{ $wallet->id }}"
                                data-user="{{ $wallet->user->firstname }} {{ $wallet->user->lastname }}"
                                data-currency="{{ $wallet->currency_code }}"
                                data-balance="{{ number_format($wallet->balance, 2) }}">
                                <i class="las la-plus"></i> @lang('Credit')
                            </button>
                            <button type="button"
                                class="btn btn--danger btn--sm mt-1"
                                data-bs-toggle="modal"
                                data-bs-target="#debitModal"
                                data-id="{{ $wallet->id }}"
                                data-user="{{ $wallet->user->firstname }} {{ $wallet->user->lastname }}"
                                data-currency="{{ $wallet->currency_code }}"
                                data-balance="{{ number_format($wallet->balance, 2) }}">
                                <i class="las la-minus"></i> @lang('Debit')
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text--muted">@lang('No wallets found')</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($wallets->hasPages())
    <div class="card-footer">
        {{ paginateLinks($wallets) }}
    </div>
    @endif
</div>
{{-- Credit Modal --}}
<div class="modal fade" id="creditModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Credit Wallet')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="creditForm" action="">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        @lang('Adding funds to:') <strong id="creditUserLabel"></strong> &mdash; <span id="creditCurrencyLabel"></span><br>
                        @lang('Current balance:') <strong id="creditBalanceLabel"></strong>
                    </p>
                    <div class="form-group">
                        <label class="required">@lang('Amount')</label>
                        <input type="number" name="amount" class="form-control" min="0.01" step="any" required placeholder="0.00">
                    </div>
                    <div class="form-group mt-3">
                        <label>@lang('Remark (optional)')</label>
                        <input type="text" name="remark" class="form-control" maxlength="255" placeholder="@lang('e.g. Manual top-up by admin')">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--success">@lang('Credit Wallet')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Debit Modal --}}
<div class="modal fade" id="debitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Debit Wallet')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="debitForm" action="">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        @lang('Deducting funds from:') <strong id="debitUserLabel"></strong> &mdash; <span id="debitCurrencyLabel"></span><br>
                        @lang('Current balance:') <strong id="debitBalanceLabel"></strong>
                    </p>
                    <div class="form-group">
                        <label class="required">@lang('Amount')</label>
                        <input type="number" name="amount" class="form-control" min="0.01" step="any" required placeholder="0.00">
                    </div>
                    <div class="form-group mt-3">
                        <label>@lang('Remark (optional)')</label>
                        <input type="text" name="remark" class="form-control" maxlength="255" placeholder="@lang('e.g. Admin correction')">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--danger">@lang('Debit Wallet')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
<script>
(function () {
    var creditBase = "{{ route('admin.wallet.credit', '__ID__') }}";
    var debitBase  = "{{ route('admin.wallet.debit',  '__ID__') }}";

    document.getElementById('creditModal').addEventListener('show.bs.modal', function (e) {
        var btn = e.relatedTarget;
        document.getElementById('creditForm').action    = creditBase.replace('__ID__', btn.dataset.id);
        document.getElementById('creditUserLabel').textContent     = btn.dataset.user;
        document.getElementById('creditCurrencyLabel').textContent = btn.dataset.currency;
        document.getElementById('creditBalanceLabel').textContent  = btn.dataset.balance + ' ' + btn.dataset.currency;
    });

    document.getElementById('debitModal').addEventListener('show.bs.modal', function (e) {
        var btn = e.relatedTarget;
        document.getElementById('debitForm').action    = debitBase.replace('__ID__', btn.dataset.id);
        document.getElementById('debitUserLabel').textContent     = btn.dataset.user;
        document.getElementById('debitCurrencyLabel').textContent = btn.dataset.currency;
        document.getElementById('debitBalanceLabel').textContent  = btn.dataset.balance + ' ' + btn.dataset.currency;
    });
})();
</script>
@endpush

@endsection
