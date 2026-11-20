@extends('admin.layouts.app')

@push('style')
<style>
    .permission-matrix th { font-size: 0.82rem; }
    .permission-matrix td { vertical-align: middle; }
    .module-label { font-weight: 600; }
    .toggle-all-row { cursor: pointer; user-select: none; }
</style>
@endpush

@section('panel')
<div class="row justify-content-center">
    <div class="col-12">
        <form action="{{ route('admin.roles.update', $role) }}" method="POST" id="permissionForm">
            @csrf
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="card-title mb-0">@lang('Edit Permissions') — {{ __($pageTitle) }}</h5>
                        <small class="text--muted">@lang('Configure what this role can access across all modules.')</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" id="selectAll" class="btn btn-sm btn-outline--success">
                            <i class="las la-check-double"></i> @lang('Select All')
                        </button>
                        <button type="button" id="clearAll" class="btn btn-sm btn-outline--danger">
                            <i class="las la-times"></i> @lang('Clear All')
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table--light permission-matrix mb-0">
                            <thead>
                                <tr>
                                    <th style="width:200px">@lang('Module')</th>
                                    <th class="text-center">@lang('View')</th>
                                    <th class="text-center">@lang('Create')</th>
                                    <th class="text-center">@lang('Edit')</th>
                                    <th class="text-center">@lang('Delete')</th>
                                    <th class="text-center">@lang('Approve')</th>
                                    <th class="text-center">@lang('Toggle Row')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($modules as $moduleKey => $moduleLabel)
                                @php
                                    $perm = $permissions[$moduleKey] ?? null;
                                    $actions = [
                                        'view'    => $perm?->can_view    ?? false,
                                        'create'  => $perm?->can_create  ?? false,
                                        'edit'    => $perm?->can_edit    ?? false,
                                        'delete'  => $perm?->can_delete  ?? false,
                                        'approve' => $perm?->can_approve ?? false,
                                    ];
                                @endphp
                                <tr data-module="{{ $moduleKey }}">
                                    <td class="module-label">{{ __($moduleLabel) }}</td>
                                    @foreach($actions as $actionKey => $checked)
                                    <td class="text-center">
                                        <input type="checkbox"
                                            name="permissions[{{ $moduleKey }}][{{ $actionKey }}]"
                                            value="1"
                                            class="form-check-input perm-checkbox"
                                            data-module="{{ $moduleKey }}"
                                            {{ $checked ? 'checked' : '' }}>
                                    </td>
                                    @endforeach
                                    <td class="text-center">
                                        <button type="button" class="btn btn-xs btn-outline--secondary toggle-row"
                                            data-module="{{ $moduleKey }}" title="@lang('Toggle all in row')">
                                            <i class="las la-exchange-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex gap-2 align-items-center justify-content-between flex-wrap">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn--primary">
                            <i class="las la-save"></i> @lang('Save Permissions')
                        </button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn--secondary">@lang('Cancel')</a>
                    </div>
                    <button type="button" class="btn btn--warning confirmationBtn"
                        data-question="@lang('Reset permissions to system defaults for this role?')"
                        data-action="{{ route('admin.roles.reset', $role) }}">
                        <i class="las la-undo"></i> @lang('Reset to Default')
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<x-confirmation-modal />
@endsection

@push('script')
<script>
"use strict";

// Toggle all checkboxes in a single row
$('.toggle-row').on('click', function() {
    const module = $(this).data('module');
    const boxes = $(`.perm-checkbox[data-module="${module}"]`);
    const allChecked = boxes.filter(':checked').length === boxes.length;
    boxes.prop('checked', !allChecked);
});

// Select all
$('#selectAll').on('click', function() {
    $('.perm-checkbox').prop('checked', true);
});

// Clear all
$('#clearAll').on('click', function() {
    $('.perm-checkbox').prop('checked', false);
});
</script>
@endpush
