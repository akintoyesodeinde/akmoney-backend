@extends('admin.layouts.app')

@push('style')
<style>
.notify__area {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.notify-item-wrapper {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #fff;
    border: 1px solid #e7eaf3;
    border-radius: 0.5rem;
    padding: 0.5rem 0.75rem;
    transition: box-shadow 0.15s;
}

.notify-item-wrapper:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
}

.notify__item {
    flex: 1;
    min-width: 0;
    text-decoration: none;
    color: inherit;
    display: block;
    padding: 0.5rem 0;
}

.notify__item.unread--notification .title {
    font-weight: 700;
}

.notify__item .title {
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
    color: #344050;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.notify__item .date {
    font-size: 0.78rem;
    color: #8a97a7;
}

.notify__item .date i {
    margin-right: 3px;
}

.notify-delete-btn {
    flex-shrink: 0;
}

.empty-notification-list img {
    max-width: 160px;
    margin-bottom: 1rem;
}
</style>
@endpush

@section('panel')
    <div class="notify__area">
        @forelse($notifications as $notification)
            <div class="notify-item-wrapper">
                <a class="notify__item @if ($notification->is_read == Status::NO) unread--notification @endif" href="{{ route('admin.notification.read', $notification->id) }}">
                    <div class="notify__content">
                        <h6 class="title">{{ __($notification->title) }}</h6>
                        <span class="date"><i class="las la-clock"></i> {{ diffForHumans($notification->created_at) }}</span>
                        @if(auth('admin')->user()->isSuperAdmin() && $notification->triggeredBy)
                            <span class="d-block small text-muted mt-1">
                                <i class="las la-user-shield"></i>
                                @lang('Action by:') <strong>{{ $notification->triggeredBy->name }}</strong>
                                ({{ $notification->triggeredBy->roleLabel }})
                            </span>
                        @endif
                        @if(auth('admin')->user()->isSuperAdmin() && $notification->module)
                            <span class="badge badge--info mt-1" style="font-size:0.7rem">
                                {{ \App\Constants\AdminRole::MODULES[$notification->module] ?? $notification->module }}
                            </span>
                        @endif
                    </div>
                </a>
                <button type="button"
                    class="btn btn-sm btn-outline--danger notify-delete-btn confirmationBtn"
                    data-question="@lang('Are you sure to delete the notification?')"
                    data-action="{{ route('admin.notifications.delete.single', $notification->id) }}">
                    <i class="las la-trash me-0"></i>
                </button>
            </div>
        @empty
            <div class="card">
                <div class="card-body">
                    <div class="empty-notification-list text-center">
                        <img src="{{ getImage('assets/images/empty_list.png') }}" alt="empty">
                        <h5 class="text-muted">@lang('No notification found.')</h5>
                    </div>
                </div>
            </div>
        @endforelse
        <div class="mt-3">
            {{ paginateLinks($notifications) }}
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    @if ($hasUnread)
        <a href="{{ route('admin.notifications.read.all') }}" class="btn btn-sm btn-outline--primary">
            <i class="las la-check"></i> @lang('Mark All as Read')
        </a>
    @endif
    @if ($hasNotification)
        <button class="btn btn-sm btn-outline--danger confirmationBtn"
            data-action="{{ route('admin.notifications.delete.all') }}"
            data-question="@lang('Are you sure to delete all notifications?')">
            <i class="las la-trash"></i> @lang('Delete all Notifications')
        </button>
    @endif
@endpush
