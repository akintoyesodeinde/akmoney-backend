@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">@lang('All Admin Accounts')</h5>
                <a href="{{ route('admin.admins.create') }}" class="btn btn-sm btn-outline--primary">
                    <i class="las la-plus"></i> @lang('Add New Admin')
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Admin')</th>
                                <th>@lang('Username')</th>
                                <th>@lang('Role')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Created')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($admins as $item)
                            <tr>
                                <td>
                                    <span class="fw-bold">{{ $item->name }}</span><br>
                                    <span class="small text--muted">{{ $item->email }}</span>
                                </td>
                                <td>{{ $item->username }}</td>
                                <td>
                                    @php
                                        $badgeMap = [
                                            'super_admin'        => 'danger',
                                            'operations_manager' => 'primary',
                                            'compliance_officer' => 'warning',
                                            'finance_accountant' => 'success',
                                            'technical_support'  => 'info',
                                            'customer_support'   => 'primary',
                                            'agent_manager'      => 'dark',
                                            'security_admin'     => 'danger',
                                        ];
                                        $badge = $badgeMap[$item->role] ?? 'dark';
                                    @endphp
                                    <span class="badge badge--{{ $badge }}">{{ $item->roleLabel }}</span>
                                </td>
                                <td>
                                    @if($item->status == 1)
                                        <span class="badge badge--success">@lang('Active')</span>
                                    @else
                                        <span class="badge badge--danger">@lang('Inactive')</span>
                                    @endif
                                </td>
                                <td>{{ showDateTime($item->created_at) }}</td>
                                <td>
                                    <div class="button--group">
                                        @if(!$item->isSuperAdmin() || $item->id == auth('admin')->id())
                                        <a href="{{ route('admin.admins.edit', $item->id) }}" class="btn btn-sm btn-outline--primary">
                                            <i class="las la-pen"></i> @lang('Edit')
                                        </a>
                                        @endif

                                        @if($item->id != auth('admin')->id() && !$item->isSuperAdmin())
                                        <button class="btn btn-sm {{ $item->status == 1 ? 'btn-outline--warning' : 'btn-outline--success' }} confirmationBtn"
                                            data-question="{{ $item->status == 1 ? __('Deactivate this admin account?') : __('Activate this admin account?') }}"
                                            data-action="{{ route('admin.admins.toggle.status', $item->id) }}">
                                            <i class="las {{ $item->status == 1 ? 'la-ban' : 'la-check' }}"></i>
                                            {{ $item->status == 1 ? __('Deactivate') : __('Activate') }}
                                        </button>

                                        <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                            data-question="@lang('Delete this admin account permanently?')"
                                            data-action="{{ route('admin.admins.delete', $item->id) }}">
                                            <i class="las la-trash"></i> @lang('Delete')
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">{{ __($emptyMessage ?? 'No admin accounts found.') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($admins->hasPages())
            <div class="card-footer">
                {{ paginateLinks($admins) }}
            </div>
            @endif
        </div>
    </div>
</div>

<x-confirmation-modal />
@endsection
