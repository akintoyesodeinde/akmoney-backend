@extends('admin.layouts.app')
@section('panel')
    <div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg--primary d-flex justify-content-between">
                <h5 class="text-white">@lang('Automatic KYC Providers')</h5>
                <!--<button class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#providerModal">-->
                <!--    <i class="la la-plus"></i> @lang('Add Provider')-->
                <!--</button>-->
            </div>

            <div class="card-body">
                <p class="text-muted">
                    @lang('Manage automatic KYC providers. Manual KYC has been disabled.')
                </p>

    
    
    @foreach ($providers as $provider)
<div class="card mb-4 border">
    <form method="POST" action="{{ route('admin.kyc.providers.save') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ $provider->id }}">

        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="{{ $provider->logo ? asset('assets/images/providers/' . $provider->logo) : asset(siteFavicon()) }}"
                     width="40" class="me-2 rounded">
                <strong>{{ $provider->name }}</strong>
                <small class="text-muted ms-2">({{ $provider->slug }})</small>
            </div>

            <div class="form-check form-switch">
                <input class="form-check-input"
                       type="checkbox"
                       name="is_enabled"
                       {{ $provider->is_enabled ? 'checked' : '' }}>
            </div>
        </div>
                <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>@lang('Provider Name')</label>
                    <input type="text"
                           class="form-control"
                           name="name"
                           value="{{ $provider->name }}"
                           {{ $provider->name ? 'readonly' : '' }}>
                </div>

                <div class="col-md-6">
                    <label>@lang('Slug')</label>
                    <input type="text"
                           class="form-control"
                           name="slug"
                           value="{{ $provider->slug }}"
                           {{ $provider->id ? 'readonly' : '' }}>
                </div>
            </div>
            @if($provider->slug !== "manual")
            <div class="mb-3">
                    <label>API Key</label>
                    <input type="text" class="form-control" name="api_key" value="{{ $provider->api_key }}">
                </div>
                @endif
                @if($provider->slug !== "manual" && $provider->slug !== "yoti")
                <div class="mb-3">
                    <label>Secret Key</label>
                    <input type="text" class="form-control" name="secret_key" value="{{ $provider->secret_key }}">
                </div>
                @endif
            <!--<div class="mb-3">-->
            <!--    <label>@lang('Logo')</label>-->
            <!--    <input type="file" class="form-control" name="logo">-->
            <!--</div>-->
@if ($provider->help_text)
<div class="card-body alert-info alert" style="display:block">
    <h3>@lang('How to get credentials')</h3>
    {!! $provider->help_text !!}
</div>
@endif
            <div class="text-end mt-3">
                @if($provider->slug === "manual")
                <button type="button" class="btn btn-sm btn-outline-light btn--primary float-end form-generate-btn">
                    <i class="la la-fw la-plus"></i>@lang('Add New Form')
                </button>
                @endif
                @if($provider->slug !== "manual")
                <button class="btn btn--primary">
                    <i class="la la-save"></i> @lang('Save Changes')
                </button>
                @endif
            </div>
        </div>
    </form>
@if($provider->slug === "manual")
<div class="card-body">
    <form method="post">
        @csrf
        <x-generated-form :form=$form />
        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
    </form>
</div>
@endif
</div>
@endforeach

<!--<div class="modal fade" id="providerModal">-->
<!--    <div class="modal-dialog modal-lg">-->
<!--        <form method="POST" action="{{ route('admin.kyc.providers.save') }}">-->
<!--            @csrf-->
<!--            <div class="modal-content">-->
<!--                <div class="modal-header">-->
<!--                    <h5>@lang('Add KYC Provider')</h5>-->
<!--                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>-->
<!--                </div>-->

<!--                <div class="modal-body">-->
<!--                    <div class="mb-3">-->
<!--                        <label>@lang('Provider Name')</label>-->
<!--                        <input type="text" class="form-control" name="name" required>-->
<!--                    </div>-->

<!--                    <div class="mb-3">-->
<!--                        <label>@lang('Slug')</label>-->
<!--                        <input type="text" class="form-control" name="slug" required>-->
<!--                    </div>-->

<!--                    <div class="mb-3">-->
<!--                        <label>@lang('Help Text')</label>-->
<!--                        <textarea class="form-control" name="help_text"></textarea>-->
<!--                    </div>-->

<!--                    <div class="mb-3">-->
<!--                        <label>@lang('API KEY')</label>-->
<!--                        <textarea class="form-control" name="api_key" rows="5"-->
<!--                          placeholder='API KEY'></textarea>-->
<!--                    </div>-->
<!--                </div>-->

<!--                <div class="modal-footer">-->
<!--                    <button class="btn btn--primary">@lang('Create Provider')</button>-->
<!--                </div>-->
<!--            </div>-->
<!--        </form>-->
<!--    </div>-->
<!--</div>-->

<x-form-generator-modal />
@endsection
