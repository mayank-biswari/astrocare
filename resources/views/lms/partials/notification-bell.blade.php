{{-- LMS Notification Bell Partial --}}
{{-- Can be included via @include('lms.partials.notification-bell') --}}
{{-- Displays a bell icon with unread count badge and dropdown with 5 recent notifications --}}

@php
    $bellUnreadCount = \App\Models\LmsNotification::where('user_id', auth()->id())->whereNull('read_at')->count();
    $bellRecentNotifications = \App\Models\LmsNotification::where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
@endphp

<div class="relative" id="notification-bell-partial">
    <!-- Bell Button -->
    <button type="button"
            onclick="toggleNotificationBellDropdown()"
            class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-full"
            aria-label="View notifications"
            aria-expanded="false"
            aria-haspopup="true">
        <i class="fas fa-bell text-lg"></i>
        @if($bellUnreadCount > 0)
            <span id="notification-bell-badge" class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full transform translate-x-1 -translate-y-1">
                {{ $bellUnreadCount > 9 ? '9+' : $bellUnreadCount }}
            </span>
        @else
            <span id="notification-bell-badge" class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full transform translate-x-1 -translate-y-1 hidden">
                0
            </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div id="notification-bell-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50" role="menu">
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Notifications</h3>
            @if($bellUnreadCount > 0)
                <form action="{{ route('lms.notifications.mark-all-read') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>

        <!-- Notification List -->
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
            @forelse($bellRecentNotifications as $notification)
                <a href="{{ $notification->lead_id ? route('lms.leads.show', $notification->lead_id) : '#' }}"
                   class="block px-4 py-3 hover:bg-gray-50 transition-colors {{ $notification->isRead() ? 'opacity-60' : '' }}">
                    <div class="flex items-start space-x-3">
                        <!-- Type Icon -->
                        <div class="shrink-0 mt-0.5">
                            @if($notification->type === 'new_lead')
                                <i class="fas fa-user-plus text-green-500"></i>
                            @elseif($notification->type === 'status_changed')
                                <i class="fas fa-exchange-alt text-blue-500"></i>
                            @elseif($notification->type === 'follow_up_overdue')
                                <i class="fas fa-clock text-red-500"></i>
                            @else
                                <i class="fas fa-info-circle text-gray-400"></i>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $notification->title }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $notification->message }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>

                        <!-- Unread Indicator -->
                        @if(!$notification->isRead())
                            <span class="w-2 h-2 bg-indigo-500 rounded-full shrink-0 mt-2"></span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="px-4 py-6 text-center text-sm text-gray-500">
                    <i class="fas fa-bell-slash text-gray-300 text-2xl mb-2"></i>
                    <p>No notifications yet</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 border-t border-gray-200">
            <a href="{{ route('lms.notifications.index') }}" class="block text-center text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                View All Notifications
            </a>
        </div>
    </div>
</div>

<script>
    function toggleNotificationBellDropdown() {
        const dropdown = document.getElementById('notification-bell-dropdown');
        dropdown.classList.toggle('hidden');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const container = document.getElementById('notification-bell-partial');
        const dropdown = document.getElementById('notification-bell-dropdown');
        if (container && dropdown && !container.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>
