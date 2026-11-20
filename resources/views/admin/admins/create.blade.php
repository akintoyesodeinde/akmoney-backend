@extends('admin.layouts.app')
@section('panel')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Create Admin Account')</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.admins.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">@lang('Full Name') <span class="text--danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="@lang('e.g. John Doe')">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Username') <span class="text--danger">*</span></label>
                            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required placeholder="@lang('alphanumeric, underscore')">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Email Address') <span class="text--danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="@lang('admin@example.com')">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Role / Portfolio') <span class="text--danger">*</span></label>
                            <select name="role" class="form-control select2" required>
                                <option value="">@lang('-- Select Role --')</option>
                                @foreach($roles as $value => $label)
                                    @if($value !== 'super_admin')
                                    <option value="{{ $value }}" {{ old('role') == $value ? 'selected' : '' }}>{{ __($label) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Password') <span class="text--danger">*</span></label>
                            <input type="password" name="password" class="form-control" required placeholder="@lang('Min 8 chars, mixed case + number')">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Confirm Password') <span class="text--danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required placeholder="@lang('Repeat password')">
                        </div>
                    </div>

                    <div class="mt-4 p-3 rounded bg--dark text--white small">
                        <strong><i class="las la-shield-alt"></i> @lang('Role Permissions Info')</strong>
                        <p class="mb-1 mt-2">@lang('Each role has pre-configured access permissions. You can customise them under')
                            <a href="{{ route('admin.roles.index') }}" class="text--warning">@lang('Role Permissions')</a>.
                        </p>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn--primary">
                            <i class="las la-user-plus"></i> @lang('Create Admin')
                        </button>
                        <a href="{{ route('admin.admins.index') }}" class="btn btn--secondary">@lang('Cancel')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
