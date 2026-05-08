@extends('lms.layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header with Mark All Read -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">All Notifications</h2>
        @if($notifications->where('read_at', null)->count() > 0)
            <form action="{{ route('lms.notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                    <i class="fas fa-check-double mr-2"></i>
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 divide-y divide-gray-100">
        @forelse($notifications as $notification)
            <div class="flex items-start p-4 {{ $notification->isRead() ? 'bg-white opacity-70' : 'bg-indigo-50/30' }}">
                <!-- Icon -->
                <div class="shrink-0 mr-4 mt-0.5">
                    @if($notification->type === 'new_lead')
                        <div class="w-9 h-9 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-plus text-green-600 text-sm"></i>
                        </div>
                    @elseif($notification->type === 'status_changed')
                        <div class="w-9 h-9 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-exchange-alt text-blue-600 text-sm"></i>
                        </div>
                    @elseif($notification->type === 'follow_up_overdue')
                        <div class="w-9 h-9 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-red-600 text-sm"></i>
                        </div>
                    @else
                        <div class="w-9 h-9 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-info-circle text-gray-500 text-sm"></i>
                        </div>
                    @endif
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-800 {{ !$notification->isRead() ? 'font-semibold' : '' }}">
                                @if($notification->lead_id)
                                    <a href="{{ route('lms.leads.show', $notification->lead_id) }}" class="hover:text-indigo-600 transition-colors">
                                        {{ $notification->title }}
                                    </a>
                                @else
                                    {{ $notification->title }}
                                @endif
                            </p>
                            <p class="text-sm text-gray-600 mt-0.5">{{ $notification->message }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>

                        <div class="flex items-center space-x-2 ml-4 shrink-0">
                            @if(!$notification->isRead())
                                <span class="w-2.5 h-2.5 bg-indigo-500 rounded-full" title="Unread"></span>
                                <form action="{{ route('lms.notifications.read', $notification) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="text-xs text-gray-500 hover:text-indigo-600 transition-colors" title="Mark as read">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="px-4 py-12 text-center">
                <i class="fas fa-bell-slash text-gray-300 text-4xl mb-3"></i>
                <p class="text-gray-500">No notifications yet.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
