<div class="bg-white rounded-lg shadow-sm mb-4 overflow-x-auto">
    <nav class="flex border-b border-gray-200 min-w-max sm:min-w-0">
        <a href="{{ route('expert.dashboard') }}" class="px-3 sm:px-4 py-3 text-xs sm:text-sm font-medium whitespace-nowrap {{ request()->routeIs('expert.dashboard') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-900' }}">
            <i class="fas fa-home sm:mr-2"></i><span class="hidden sm:inline">Dashboard</span>
        </a>
        <a href="{{ route('expert.profile') }}" class="px-3 sm:px-4 py-3 text-xs sm:text-sm font-medium whitespace-nowrap {{ request()->routeIs('expert.profile*') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-900' }}">
            <i class="fas fa-user-circle sm:mr-2"></i><span class="hidden sm:inline">My Profile</span>
        </a>
        <a href="{{ route('expert.availability') }}" class="px-3 sm:px-4 py-3 text-xs sm:text-sm font-medium whitespace-nowrap {{ request()->routeIs('expert.availability*') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-900' }}">
            <i class="fas fa-calendar-check sm:mr-2"></i><span class="hidden sm:inline">Availability</span>
        </a>
        <a href="{{ route('expert.chats') }}" class="px-3 sm:px-4 py-3 text-xs sm:text-sm font-medium whitespace-nowrap {{ request()->routeIs('expert.chats') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-900' }}">
            <i class="fas fa-comments sm:mr-2"></i><span class="hidden sm:inline">Chat Sessions</span>
        </a>
        <a href="{{ route('expert.calls') }}" class="px-3 sm:px-4 py-3 text-xs sm:text-sm font-medium whitespace-nowrap {{ request()->routeIs('expert.calls') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-900' }}">
            <i class="fas fa-phone sm:mr-2"></i><span class="hidden sm:inline">Call Sessions</span>
        </a>
        <a href="{{ route('dashboard.settings') }}" class="px-3 sm:px-4 py-3 text-xs sm:text-sm font-medium whitespace-nowrap {{ request()->routeIs('dashboard.settings') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-900' }}">
            <i class="fas fa-cog sm:mr-2"></i><span class="hidden sm:inline">Settings</span>
        </a>
    </nav>
</div>

@php
    $page = \App\Models\CmsPage::where('created_by', auth()->id())
        ->whereHas('pageType', fn($q) => $q->where('name', 'LIKE', '%Astrologer%'))
        ->first();
    $status = $page->custom_fields['status'] ?? 'offline';
@endphp

<div class="mb-4 p-3 sm:p-4 rounded-lg {{ $status === 'online' ? 'bg-green-100 border border-green-400 text-green-700' : ($status === 'busy' ? 'bg-yellow-100 border border-yellow-400 text-yellow-700' : 'bg-gray-100 border border-gray-400 text-gray-700') }}">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0">
        <div class="flex items-center">
            <i class="fas fa-circle mr-2 text-xs {{ $status === 'online' ? 'text-green-500' : ($status === 'busy' ? 'text-yellow-500' : 'text-gray-500') }}"></i>
            <span class="font-medium text-sm sm:text-base">You are currently {{ ucfirst($status) }}</span>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            @if($status !== 'online')
            <button onclick="updateStatus('online')" class="flex-1 sm:flex-none px-3 sm:px-4 py-1.5 sm:py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-xs sm:text-sm font-medium">
                <i class="fas fa-check-circle mr-1"></i>Go Online
            </button>
            @endif
            @if($status !== 'busy')
            <button onclick="updateStatus('busy')" class="flex-1 sm:flex-none px-3 sm:px-4 py-1.5 sm:py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-xs sm:text-sm font-medium">
                <i class="fas fa-clock mr-1"></i>Go Busy
            </button>
            @endif
            @if($status !== 'offline')
            <button onclick="updateStatus('offline')" class="flex-1 sm:flex-none px-3 sm:px-4 py-1.5 sm:py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-xs sm:text-sm font-medium">
                <i class="fas fa-times-circle mr-1"></i>Go Offline
            </button>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateStatus(status) {
    fetch('{{ route('expert.status.update') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endpush
