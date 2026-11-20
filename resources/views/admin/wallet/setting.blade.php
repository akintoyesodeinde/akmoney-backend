@extends('admin.layouts.app')
@section('panel')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <form method="POST" action="{{ route('admin.wallet.setting.update') }}">
                @csrf
                <div class="card-body">
                    <h6 class="card-title mb-4 fw-bold">@lang('Wallet Module')</h6>

                    <ul class="list-group mb-4">

                        {{-- ── Wallet Module ON/OFF ── --}}
                        <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center">
                            <div>
                                <p class="fw-bold mb-0">@lang('Wallet Module')</p>
                                <p class="mb-0">
                                    <small>@lang('Master switch. If disabled, the wallet system is completely hidden from all users.')</small>
                                </p>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success"
                                    data-offstyle="-danger" data-bs-toggle="toggle" data-height="35"
                                    data-on="@lang('Enable')" data-off="@lang('Disable')" name="wallet_module"
                                    id="walletModuleToggle"
                                    @if(gs('wallet_module')) checked @endif>
                            </div>
                        </li>

                        {{-- ── Currency Mode ── --}}
                        <li class="list-group-item" id="currencyModeRow" @if(!gs('wallet_module')) style="display:none" @endif>
                            <div class="d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center mb-0">
                                <div>
                                    <p class="fw-bold mb-0">@lang('Currency Mode')</p>
                                    <p class="mb-0">
                                        <small>@lang('Choose how many currencies users can hold in their wallet.')</small>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-info"
                                        data-offstyle="-secondary" data-bs-toggle="toggle" data-height="35"
                                        data-on="@lang('Multi')" data-off="@lang('Single')" name="wallet_currency_mode"
                                        id="currencyModeToggle"
                                        @if(gs('wallet_currency_mode')) checked @endif>
                                </div>
                            </div>

                            {{-- Mode description cards --}}
                            <div class="row g-3 mt-3">
                                <div class="col-sm-6">
                                    <div id="singleModeCard" class="border rounded p-3 {{ gs('wallet_currency_mode') ? 'border-secondary opacity-50' : 'border-primary bg-light' }}">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="las la-wallet text-primary fs-5"></i>
                                            <span class="fw-bold">@lang('Single Currency')</span>
                                        </div>
                                        <ul class="mb-0 ps-3 small text-muted">
                                            <li>@lang('Users operate in') <strong>{{ gs('cur_text') }}</strong> @lang('only')</li>
                                            <li>@lang('Exchange function is hidden')</li>
                                            <li>@lang('Wallet shows full transaction history')</li>
                                            <li>@lang('Simpler, focused experience')</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div id="multiModeCard" class="border rounded p-3 {{ gs('wallet_currency_mode') ? 'border-info bg-light' : 'border-secondary opacity-50' }}">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="las la-exchange-alt text-info fs-5"></i>
                                            <span class="fw-bold">@lang('Multi Currency')</span>
                                        </div>
                                        <ul class="mb-0 ps-3 small text-muted">
                                            <li>@lang('Users can hold multiple currencies')</li>
                                            <li>@lang('Exchange between wallets enabled')</li>
                                            <li>@lang('Full multi-currency experience')</li>
                                            <li>@lang('Exchange charges apply')</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </li>

                        {{-- ── Wallet-to-Wallet Transfer ── --}}
                        <li class="list-group-item d-flex flex-wrap flex-sm-nowrap gap-2 justify-content-between align-items-center"
                            id="walletTransferRow" @if(!gs('wallet_module')) style="display:none" @endif>
                            <div>
                                <p class="fw-bold mb-0">@lang('Wallet-to-Wallet Transfer')</p>
                                <p class="mb-0">
                                    <small>@lang('Allow users to transfer funds from their wallet directly to another user\'s wallet.')</small>
                                </p>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success"
                                    data-offstyle="-danger" data-bs-toggle="toggle" data-height="35"
                                    data-on="@lang('Enable')" data-off="@lang('Disable')" name="wallet_transfer"
                                    @if(gs('wallet_transfer')) checked @endif>
                            </div>
                        </li>

                    </ul>

                    {{-- ── Transfer Lookup Methods (visible when module + transfer enabled) ── --}}
                    <div id="transferSection" @if(!gs('wallet_module') || !gs('wallet_transfer')) style="display:none" @endif>
                        <h6 class="fw-bold mt-2 mb-2">@lang('Transfer Lookup Methods')</h6>
                        <p class="text-muted small mb-3">
                            @lang('Choose which identifiers users can use to find a recipient when initiating a wallet transfer.')
                        </p>

                        @php $methods = gs()->wallet_transfer_methods ?? ['username','email','mobile']; @endphp

                        <div class="row g-3 mb-4">
                            <div class="col-sm-4">
                                <div class="border rounded p-3 d-flex align-items-start gap-3">
                                    <input class="form-check-input mt-1" type="checkbox" name="transfer_methods[]"
                                        value="username" id="method_username"
                                        @if(in_array('username', $methods)) checked @endif>
                                    <label class="form-check-label" for="method_username">
                                        <span class="fw-bold d-block">@lang('Username')</span>
                                        <small class="text-muted">@lang('Search recipient by their @username')</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="border rounded p-3 d-flex align-items-start gap-3">
                                    <input class="form-check-input mt-1" type="checkbox" name="transfer_methods[]"
                                        value="email" id="method_email"
                                        @if(in_array('email', $methods)) checked @endif>
                                    <label class="form-check-label" for="method_email">
                                        <span class="fw-bold d-block">@lang('Email')</span>
                                        <small class="text-muted">@lang('Search recipient by email address')</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="border rounded p-3 d-flex align-items-start gap-3">
                                    <input class="form-check-input mt-1" type="checkbox" name="transfer_methods[]"
                                        value="mobile" id="method_mobile"
                                        @if(in_array('mobile', $methods)) checked @endif>
                                    <label class="form-check-label" for="method_mobile">
                                        <span class="fw-bold d-block">@lang('Phone Number')</span>
                                        <small class="text-muted">@lang('Search recipient by mobile number')</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Transfer Charges ── --}}
                    <div id="transferChargeSection" @if(!gs('wallet_module')) style="display:none" @endif>
                        <h6 class="fw-bold mt-2 mb-3">@lang('Transfer Charges')</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">@lang('Fixed Charge')</label>
                                <div class="input-group">
                                    <input type="number" name="wallet_transfer_fixed_charge" step="0.01" min="0"
                                        value="{{ gs('wallet_transfer_fixed_charge') ?? 0 }}"
                                        class="form-control @error('wallet_transfer_fixed_charge') is-invalid @enderror">
                                    <span class="input-group-text">{{ gs('cur_text') }}</span>
                                    @error('wallet_transfer_fixed_charge')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">@lang('Percent Charge')</label>
                                <div class="input-group">
                                    <input type="number" name="wallet_transfer_percent_charge" step="0.01" min="0" max="100"
                                        value="{{ gs('wallet_transfer_percent_charge') ?? 0 }}"
                                        class="form-control @error('wallet_transfer_percent_charge') is-invalid @enderror">
                                    <span class="input-group-text">%</span>
                                    @error('wallet_transfer_percent_charge')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Allowed Currencies (multi-currency only) ── --}}
                    <div id="allowedCurrenciesSection" @if(!gs('wallet_module') || !gs('wallet_currency_mode')) style="display:none" @endif>
                        <h6 class="fw-bold mt-4 mb-1">@lang('Allowed Wallet Currencies')
                            <small class="text-muted fw-normal fs-6 ms-1">@lang('(Multi-currency only)')</small>
                        </h6>
                        <p class="text-muted small mb-3">
                            @lang('Select which currencies users are allowed to open wallets in. Leave empty to allow all currencies.')
                        </p>

                        @php $allowed = gs()->wallet_allowed_currencies ?? []; @endphp

                        <select name="wallet_allowed_currencies[]"
                                id="walletAllowedCurrencies"
                                class="form-control"
                                multiple="multiple"
                                style="width:100%">
                            @foreach($allCurrencies as $c)
                            <option value="{{ $c->currency }}"
                                {{ in_array($c->currency, $allowed) ? 'selected' : '' }}>
                                {{ $c->currency }} — {{ $c->name }}
                            </option>
                            @endforeach
                        </select>

                        <p class="text-muted small mt-2">
                            <i class="las la-info-circle"></i>
                            @lang('If nothing is selected, all currencies are available for wallet creation.')
                        </p>
                    </div>

                    {{-- ── Exchange Charges (multi-currency only) ── --}}
                    <div id="exchangeChargeSection" @if(!gs('wallet_module') || !gs('wallet_currency_mode')) style="display:none" @endif>
                        <h6 class="fw-bold mt-2 mb-3">@lang('Exchange Charges')
                            <small class="text-muted fw-normal fs-6 ms-1">@lang('(Multi-currency only)')</small>
                        </h6>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">@lang('Fixed Charge')</label>
                                <div class="input-group">
                                    <input type="number" name="wallet_exchange_fixed_charge" step="0.01" min="0"
                                        value="{{ gs('wallet_exchange_fixed_charge') ?? 0 }}"
                                        class="form-control @error('wallet_exchange_fixed_charge') is-invalid @enderror">
                                    <span class="input-group-text">{{ gs('cur_text') }}</span>
                                    @error('wallet_exchange_fixed_charge')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">@lang('Percent Charge')</label>
                                <div class="input-group">
                                    <input type="number" name="wallet_exchange_percent_charge" step="0.01" min="0" max="100"
                                        value="{{ gs('wallet_exchange_percent_charge') ?? 0 }}"
                                        class="form-control @error('wallet_exchange_percent_charge') is-invalid @enderror">
                                    <span class="input-group-text">%</span>
                                    @error('wallet_exchange_percent_charge')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45">@lang('Save Settings')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('style')
<style>
    .toggle.btn-lg { height: 37px !important; min-height: 37px !important; }
    .toggle-handle { width: 25px !important; padding: 0; }
    .form-group { width: 125px; margin-bottom: 0; flex-shrink: 0 }
    .list-group-item:hover { background-color: #F7F7F7 }
</style>
@endpush

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function () {

    function isModuleOn()  { return document.getElementById('walletModuleToggle').checked; }
    function isMultiMode() { return document.getElementById('currencyModeToggle').checked; }

    function syncVisibility() {
        const on    = isModuleOn();
        const multi = isMultiMode();

        ['currencyModeRow', 'walletTransferRow', 'transferChargeSection'].forEach(id => {
            document.getElementById(id).style.display = on ? '' : 'none';
        });

        // When the module is off, force the transfer toggle off so it cannot be submitted as enabled
        const transferToggle = document.querySelector('[name="wallet_transfer"]');
        if (!on && transferToggle && transferToggle.checked) {
            $(transferToggle).bootstrapToggle('off');
        }

        document.getElementById('exchangeChargeSection').style.display       = (on && multi) ? '' : 'none';
        document.getElementById('allowedCurrenciesSection').style.display   = (on && multi) ? '' : 'none';

        // Transfer section (lookup methods) only when module on AND transfer toggle checked
        const transferOn = transferToggle?.checked ?? false;
        document.getElementById('transferSection').style.display = (on && transferOn) ? '' : 'none';

        // Mode description cards
        document.getElementById('singleModeCard').classList.toggle('border-primary',  !multi);
        document.getElementById('singleModeCard').classList.toggle('bg-light',        !multi);
        document.getElementById('singleModeCard').classList.toggle('opacity-50',       multi);
        document.getElementById('singleModeCard').classList.toggle('border-secondary', multi);
        document.getElementById('multiModeCard').classList.toggle('border-info',       multi);
        document.getElementById('multiModeCard').classList.toggle('bg-light',          multi);
        document.getElementById('multiModeCard').classList.toggle('opacity-50',       !multi);
        document.getElementById('multiModeCard').classList.toggle('border-secondary', !multi);
    }

    // Bootstrap-toggle fires a native change event on the underlying checkbox
    document.getElementById('walletModuleToggle').addEventListener('change', syncVisibility);
    document.getElementById('currencyModeToggle').addEventListener('change', syncVisibility);
    document.querySelector('[name="wallet_transfer"]')?.addEventListener('change', syncVisibility);

    syncVisibility();

    // Select2 for currency picker
    $('#walletAllowedCurrencies').select2({
        placeholder: 'Search and select currencies…',
        allowClear: true,
        width: '100%',
    });
});
</script>
@endpush
