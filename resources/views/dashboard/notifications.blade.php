@extends('dashboard.layout')

@section('title', 'Notifications - Dashboard')

@section('dashboard-content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm mb-4 sm:mb-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <h1 class="text-xl sm:text-2xl font-bold">Notifications</h1>
    <p class="text-white/90 mt-1 text-sm sm:text-base">Stay updated on your orders and predictions</p>
</div>

<div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-3">
        <h2 class="text-lg sm:text-xl font-bold">All Notifications</h2>
        @if($notifications->where('is_read', false)->count() > 0)
            <form action="{{ route('dashboard.notifications.mark-all-read') }}" method="POST" id="markAllReadForm">
                @csrf
                <button type="button" onclick="confirmMarkAllRead()" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700">
                    Mark All as Read
                </button>
            </form>
        @endif
    </div>

    @if($notifications->count() > 0)
        <div class="space-y-3">
            @foreach($notifications as $notification)
                <div class="flex items-start gap-3 p-3 sm:p-4 rounded-lg border {{ $notification->is_read ? 'border-gray-100 bg-white' : 'border-indigo-200 bg-indigo-50 cursor-pointer' }}"
                     @if(!$notification->is_read) onclick="confirmMarkAsRead({{ $notification->id }})" @endif>
                    <div class="flex-shrink-0 mt-1">
                        @if($notification->type === 'prediction_status')
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-star text-purple-600 text-sm"></i>
                            </div>
                        @else
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-bell text-blue-600 text-sm"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="font-semibold text-sm sm:text-base text-gray-800">{{ $notification->title }}</h3>
                                <p class="text-xs sm:text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                            </div>
                            @if(!$notification->is_read)
                                <span class="flex-shrink-0 w-2 h-2 bg-indigo-500 rounded-full mt-2"></span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="text-center py-8">
            <div class="text-4xl mb-4">🔔</div>
            <h3 class="text-lg font-bold text-gray-600 mb-2">No Notifications</h3>
            <p class="text-gray-500">You're all caught up! Notifications will appear here when there are updates.</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
function confirmMarkAsRead(id) {
    Swal.fire({
        title: 'Mark as Read?',
        text: 'Do you want to mark this notification as read?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, mark as read',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/dashboard/notifications/' + id + '/read';
        }
    });
}

function confirmMarkAllRead() {
    Swal.fire({
        title: 'Mark All as Read?',
        text: 'This will mark all your notifications as read.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, mark all',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('markAllReadForm').submit();
        }
    });
}
</script>
@endpush
@endsection
