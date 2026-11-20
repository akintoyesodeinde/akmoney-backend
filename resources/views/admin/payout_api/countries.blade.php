@extends('admin.layouts.app')
@section('panel')

<div class="card">
    <div class="card-header">
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <h5 class="mb-0">@lang('Payout Countries') <span class="badge badge--primary">{{ $payoutCountries->total() }}</span></h5>
            <div class="d-flex gap-2 align-items-center">
                <form method="GET" action="{{ route('admin.payout.api.countries') }}" class="d-flex gap-1 mb-0">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control form-control-sm" style="max-width:220px"
                        placeholder="@lang('Search country or currency…')">
                    <button class="btn btn-sm btn--primary" type="submit"><i class="las la-search"></i></button>
                    @if(request('search'))
                        <a href="{{ route('admin.payout.api.countries') }}" class="btn btn-sm btn--dark"><i class="las la-times"></i></a>
                    @endif
                </form>
                <button type="button" class="btn btn-sm btn-outline--primary confirmationBtn"
                    data-action="{{ route('admin.payout.api.sync.active.conf') }}"
                    data-question="@lang('This will fetch the latest active correspondents from PawaPay and update services. Continue?')">
                    <i class="las la-sync"></i> @lang('Sync PawaPay Now')
                </button>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive--md table-responsive">
            <table class="table--light style--two table" id="countriesTable">
                <thead>
                    <tr>
                        <th>@lang('Country')</th>
                        <th>@lang('Code')</th>
                        <th>@lang('Currency')</th>
                        <th style="min-width:180px">@lang('Payout Provider')</th>
                        <th>@lang('Active Services')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payoutCountries as $pc)
                    <tr>
                        <td class="fw-bold">{{ $pc->country_name }}</td>
                        <td><span class="badge badge--primary">{{ strtoupper($pc->country_code) }}</span></td>
                        <td>{{ $pc->currency_code }}</td>

                        {{-- ── Provider dropdown ── --}}
                        <td>
                            <select class="form-control form-select-sm provider-select"
                                    data-url="{{ route('admin.payout.api.country.provider', $pc->id) }}"
                                    style="min-width:150px">
                                @foreach($providers as $value => $label)
                                <option value="{{ $value }}" {{ $pc->payout_provider === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                            <div class="provider-saving text-muted small mt-1" style="display:none">
                                <i class="las la-spinner la-spin"></i> @lang('Saving…')
                            </div>
                        </td>

                        {{-- ── Services / correspondents ── --}}
                        <td>
                            @if($pc->payout_provider === 'manual')
                                <span class="text-muted small">—</span>

                            @elseif($pc->payout_provider === 'pawapay')
                                @if($pc->methods->count())
                                <div class="methods-section">
                                    @foreach($pc->methods as $method)
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <div class="form-check form-switch mb-0">
                                            <input type="checkbox"
                                                class="form-check-input method-toggle"
                                                id="method_{{ $method->id }}"
                                                data-url="{{ route('admin.payout.api.method.toggle', $method->id) }}"
                                                @checked($method->is_active)>
                                            <label class="form-check-label {{ $method->is_active ? 'text--success' : 'text-muted' }}"
                                                   for="method_{{ $method->id }}"
                                                   style="cursor:pointer;min-width:140px">
                                                {{ $method->method_name }}
                                                <small class="d-block text-muted" style="font-size:10px;line-height:1.2">
                                                    {{ $method->pawapay_provider }}
                                                </small>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <span class="text-muted small">
                                    <i class="las la-info-circle"></i>
                                    @lang('No services — click') <strong>@lang('Sync PawaPay Now')</strong>
                                </span>
                                @endif

                            @elseif($pc->payout_provider === 'paga')
                                <span class="badge badge--success">@lang('Paga')</span>
                                <small class="text-muted d-block">@lang('Auto — no service config needed')</small>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($payoutCountries->hasPages())
        <div class="card-footer">
            {{ $payoutCountries->links() }}
        </div>
        @endif
    </div>
</div>

<x-confirmation-modal />

@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.payout.api.settings') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-cog"></i> @lang('API Settings')
    </a>
    <a href="{{ route('admin.payout.api.logs') }}" class="btn btn-sm btn-outline--info">
        <i class="las la-list"></i> @lang('Payout Logs')
    </a>
@endpush

@push('script')
<script>
$(document).on('change', '.provider-select', function () {
    const select = $(this);
    const url    = select.data('url');
    const saving = select.closest('td').find('.provider-saving');

    saving.show();
    select.prop('disabled', true);

    $.post(url, { _token: '{{ csrf_token() }}', payout_provider: select.val() }, function (res) {
        if (res.status === 'success') {
            iziToast.success({ message: res.message, position: 'topRight' });
        } else {
            iziToast.error({ message: res.message ?? 'Failed to save', position: 'topRight' });
        }
    }).fail(function () {
        iziToast.error({ message: 'Request failed', position: 'topRight' });
    }).always(function () {
        saving.hide();
        select.prop('disabled', false);
    });
});

$(document).on('change', '.method-toggle', function () {
    const cb    = this;
    const label = $(cb).closest('.d-flex').find('label');

    $.post($(cb).data('url'), { _token: '{{ csrf_token() }}' }, function (res) {
        if (res.status === 'success') {
            label.toggleClass('text--success', cb.checked).toggleClass('text-muted', !cb.checked);
            iziToast.success({ message: res.message, position: 'topRight' });
        } else {
            cb.checked = !cb.checked;
            iziToast.error({ message: res.message ?? 'Toggle failed', position: 'topRight' });
        }
    }).fail(function () {
        cb.checked = !cb.checked;
        iziToast.error({ message: 'Request failed', position: 'topRight' });
    });
});
</script>
@endpush
