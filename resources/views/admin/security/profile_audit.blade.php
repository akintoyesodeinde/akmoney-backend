@extends('admin.layouts.app')

@section('panel')

    <div class="row">
        <div class="col-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <div class="d-flex flex-wrap align-items-center justify-content-between p-3 gap-3">
                            <form class="d-flex flex-wrap gap-2" method="GET"
                                action="{{ route('admin.security.profile.audit') }}">
                                <input type="text" name="search" class="form-control" style="max-width:200px;"
                                    placeholder="@lang('Username / Email')" value="{{ request('search') }}">
                                <select name="action" class="form-control" style="max-width:200px;">
                                    <option value="">@lang('All Actions')</option>
                                    @foreach ($actions as $action)
                                        <option value="{{ $action }}"
                                            {{ request('action') === $action ? 'selected' : '' }}>
                                            {{ str_replace('_', ' ', ucfirst($action)) }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="date" name="date" class="form-control" style="max-width:160px;"
                                    value="{{ request('date') }}">
                                <button type="submit" class="btn btn--primary"><i class="las la-search"></i>
                                    @lang('Search')</button>
                                @if (request()->anyFilled(['search', 'action', 'date']))
                                    <a href="{{ route('admin.security.profile.audit') }}"
                                        class="btn btn--secondary">@lang('Reset')</a>
                                @endif
                            </form>
                        </div>

                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Action')</th>
                                    <th>@lang('Field')</th>
                                    <th>@lang('Change')</th>
                                    <th>@lang('IP')</th>
                                    <th>@lang('Date')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $log)
                                    <tr>
                                        <td>
                                            @if ($log->user)
                                                <a href="{{ route('admin.users.detail', $log->user->id) }}">
                                                    {{ $log->user->fullname }}
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ $log->user->username }}</small>
                                            @else
                                                <span class="text-muted">@lang('Deleted User')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge--primary">
                                                {{ str_replace('_', ' ', ucfirst($log->action)) }}
                                            </span>
                                        </td>
                                        <td>{{ $log->field ?? '—' }}</td>
                                        <td>
                                            @if ($log->old_value || $log->new_value)
                                                <span class="text-danger">{{ $log->old_value ?? '—' }}</span>
                                                <i class="las la-arrow-right text-muted mx-1"></i>
                                                <span class="text-success">{{ $log->new_value ?? '—' }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $log->ip_address ?? '—' }}</td>
                                        <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">@lang('No audit records found')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($logs->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($logs) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.security.login.history') }}" class="btn btn--secondary btn-sm">
        <i class="las la-sign-in-alt"></i> @lang('Login History')
    </a>
@endpush
