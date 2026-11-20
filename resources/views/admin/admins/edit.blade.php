@extends('admin.layouts.app')
@section('panel')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">@lang('Edit Admin Account')</h5>
                <span class="badge badge--info">{{ $admin->roleLabel }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.admins.update', $admin->id) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">@lang('Full Name') <span class="text--danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $admin->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Username') <span class="text--danger">*</span></label>
                            <input type="text" name="username" class="form-control" value="{{ old('username', $admin->username) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Email Address') <span class="text--danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $admin->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Role / Portfolio') <span class="text--danger">*</span></label>
                            <select name="role" class="form-control select2" required
                                {{ $admin->id == auth('admin')->id() ? 'disabled' : '' }}>
                                @foreach($roles as $value => $label)
                                    @if($value !== 'super_admin' || $admin->isSuperAdmin())
                                    <option value="{{ $value }}" {{ old('role', $admin->role) == $value ? 'selected' : '' }}>
                                        {{ __($label) }}
                                    </option>
                                    @endif
                                @endforeach
                            </select>
                            @if($admin->id == auth('admin')->id())
                                <input type="hidden" name="role" value="{{ $admin->role }}">
                                <small class="text--warning">@lang('You cannot change your own role.')</small>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('New Password') <small class="text--muted">@lang('(leave blank to keep current)')</small></label>
                            <input type="password" name="password" class="form-control" placeholder="@lang('Min 8 chars, mixed case + number')">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Confirm New Password')</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="@lang('Repeat new password')">
                        </div>

                        @if($admin->id != auth('admin')->id() && !$admin->isSuperAdmin())
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" id="statusToggle"
                                    value="1" {{ $admin->status == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="statusToggle">@lang('Account Active')</label>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn--primary">
                            <i class="las la-save"></i> @lang('Save Changes')
                        </button>
                        <a href="{{ route('admin.admins.index') }}" class="btn btn--secondary">@lang('Cancel')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
