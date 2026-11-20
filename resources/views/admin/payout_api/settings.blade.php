@extends('admin.layouts.app')
@section('panel')
<div class="row">

    {{-- ── PawaPay Credentials ── --}}
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <span class="badge badge--primary px-3 py-2">PawaPay</span>
                <h5 class="card-title mb-0">@lang('PawaPay API Credentials')</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.payout.api.settings.save') }}" method="POST">
                    @csrf
                    {{-- Hidden Paga fields so the combined form still validates --}}
                    <input type="hidden" name="paga_environment" value="{{ $paga->environment }}">

                    <div class="form-group">
                        <label>@lang('Environment')</label>
                        <select name="pawapay_environment" class="form-control">
                            <option value="sandbox" @selected($pawapay->environment === 'sandbox')>Sandbox (api.sandbox.pawapay.cloud)</option>
                            <option value="live"    @selected($pawapay->environment === 'live')>Live (api.pawapay.cloud)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>@lang('API Token')</label>
                        <input type="text" class="form-control" name="pawapay_secret_key"
                               placeholder="Leave blank to keep current token"
                               value="" autocomplete="off" />
                        <small class="text-muted">
                            @if($pawapay->secret_key)
                                @lang('Token set') — @lang('enter a new value to replace it')
                            @else
                                @lang('No token saved yet')
                            @endif
                        </small>
                    </div>

                    <div class="form-group">
                        <label>@lang('Webhook Secret') <small class="text-muted">(@lang('optional'))</small></label>
                        <input type="text" class="form-control" name="pawapay_webhook_secret"
                               placeholder="Optional — for verifying webhook signatures"
                               value="" autocomplete="off" />
                        <small class="text-muted">
                            @lang('PawaPay callback URL:') <code>{{ route('ipn.payout.api') }}</code>
                        </small>
                    </div>

                    <button type="submit" class="btn btn--primary w-100 h-45">@lang('Save PawaPay Settings')</button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Paga Credentials ── --}}
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <span class="badge badge--warning px-3 py-2">Paga</span>
                <h5 class="card-title mb-0">@lang('Paga API Credentials')</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.payout.api.settings.save') }}" method="POST">
                    @csrf
                    {{-- Hidden PawaPay fields so the combined form still validates --}}
                    <input type="hidden" name="pawapay_environment" value="{{ $pawapay->environment }}">

                    <div class="form-group">
                        <label>@lang('Environment')</label>
                        <select name="paga_environment" class="form-control">
                            <option value="sandbox" @selected($paga->environment === 'sandbox')>Sandbox (beta.mypaga.com)</option>
                            <option value="live"    @selected($paga->environment === 'live')>Live (www.mypaga.com)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>@lang('Principal')</label>
                        <input type="text" class="form-control" name="paga_principal"
                               placeholder="Leave blank to keep current value"
                               value="" autocomplete="off" />
                        <small class="text-muted">
                            @if($paga->principal) @lang('Set') — @lang('enter a new value to replace') @else @lang('Not set') @endif
                        </small>
                    </div>

                    <div class="form-group">
                        <label>@lang('Credentials') <small class="text-muted">(@lang('password'))</small></label>
                        <input type="password" class="form-control" name="paga_credentials"
                               placeholder="Leave blank to keep current value"
                               value="" autocomplete="new-password" />
                    </div>

                    <div class="form-group">
                        <label>@lang('API Key') <small class="text-muted">(@lang('used in hash signature'))</small></label>
                        <input type="password" class="form-control" name="paga_api_key"
                               placeholder="Leave blank to keep current value"
                               value="" autocomplete="new-password" />
                        <small class="text-muted">
                            @lang('Paga callback URL:') <code>{{ route('ipn.paga') }}</code>
                        </small>
                    </div>

                    <button type="submit" class="btn btn--primary w-100 h-45">@lang('Save Paga Settings')</button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.payout.api.countries') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-globe"></i> @lang('Payout Countries')
    </a>
    <a href="{{ route('admin.payout.api.logs') }}" class="btn btn-sm btn-outline--info">
        <i class="las la-list"></i> @lang('Payout Logs')
    </a>
@endpush
