@extends('admin.layouts.app')

@push('style')
<style>
    .perm-table th, .perm-table td { font-size: 0.8rem; vertical-align: middle; }
    .perm-table .module-name { font-weight: 600; white-space: nowrap; }
    .role-header { writing-mode: horizontal-tb; font-size: 0.75rem; text-align: center; }
    .check-icon { color: var(--bs-success); }
    .cross-icon { color: #ccc; }
</style>
@endpush

@section('panel')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">@lang('Role Permissions Overview')</h5>
                <a href="{{ route('admin.admins.index') }}" class="btn btn-sm btn-outline--primary">
                    <i class="las la-users"></i> @lang('Manage Admins')
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table--light perm-table mb-0">
                        <thead>
                            <tr>
                                <th>@lang('Module')</th>
                                <th>@lang('Action')</th>
                                @foreach($roles as $roleKey => $roleLabel)
                                    @if($roleKey !== 'super_admin')
                                    <th class="text-center">
                                        <div class="role-header">{{ __($roleLabel) }}</div>
                                        <a href="{{ route('admin.roles.edit', $roleKey) }}" class="btn btn-xs btn-outline--primary mt-1" title="@lang('Edit')">
                                            <i class="las la-pen"></i>
                                        </a>
                                    </th>
                                    @endif
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $actions = ['can_view' => 'View', 'can_create' => 'Create', 'can_edit' => 'Edit', 'can_delete' => 'Delete', 'can_approve' => 'Approve'];
                                $nonSuperRoles = array_filter($roles, fn($k) => $k !== 'super_admin', ARRAY_FILTER_USE_KEY);
                            @endphp

                            @foreach($modules as $moduleKey => $moduleLabel)
                                @foreach($actions as $actionKey => $actionLabel)
                                <tr class="{{ $loop->first ? 'border-top-2' : '' }}">
                                    @if($loop->first)
                                    <td class="module-name" rowspan="{{ count($actions) }}">
                                        {{ __($moduleLabel) }}
                                    </td>
                                    @endif
                                    <td class="text--muted small">{{ __($actionLabel) }}</td>

                                    @foreach($nonSuperRoles as $roleKey => $roleLabel)
                                    <td class="text-center">
                                        @php
                                            $perm = $permissions[$roleKey][$moduleKey] ?? null;
                                            $hasAccess = $perm && $perm->$actionKey;
                                        @endphp
                                        @if($hasAccess)
                                            <i class="las la-check-circle check-icon fs-6"></i>
                                        @else
                                            <i class="las la-times-circle cross-icon fs-6"></i>
                                        @endif
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            @endforeach

                            {{-- Super Admin row --}}
                            <tr class="bg--dark text--white">
                                <td colspan="{{ count($nonSuperRoles) + 2 }}" class="text-center py-2">
                                    <i class="las la-crown"></i>
                                    <strong>@lang('Super Admin')</strong> — @lang('Full unrestricted access to all modules and actions')
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
