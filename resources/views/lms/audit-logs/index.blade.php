@extends('lms.layouts.app')

@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')

@section('content')
    <section aria-label="Audit log entries">
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-clipboard-list text-indigo-500 mr-2"></i>
                    Lead Audit Logs
                </h2>
                <span class="text-sm text-gray-500">{{ $logs->total() }} {{ Str::plural('entry', $logs->total()) }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-5 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-5 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th scope="col" class="px-5 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            <th scope="col" class="px-5 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-5 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $log->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-5 py-3 text-gray-700 font-medium whitespace-nowrap">
                                    {{ $log->user->name ?? 'Deleted User' }}
                                </td>
                                <td class="px-5 py-3">
                                    @php
                                        $actionBadge = match($log->action) {
                                            'assignment' => 'bg-blue-100 text-blue-800',
                                            'deletion' => 'bg-red-100 text-red-800',
                                            'export' => 'bg-green-100 text-green-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $actionBadge }}">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-gray-600 max-w-md truncate">
                                    {{ $log->description }}
                                </td>
                                <td class="px-5 py-3">
                                    @if($log->metadata)
                                        <button type="button"
                                                onclick="toggleMetadata({{ $log->id }})"
                                                class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                                            <i class="fas fa-chevron-down mr-1" id="icon-{{ $log->id }}"></i>
                                            View
                                        </button>
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>
                            </tr>
                            @if($log->metadata)
                                <tr id="metadata-{{ $log->id }}" class="hidden">
                                    <td colspan="5" class="px-5 py-3 bg-gray-50">
                                        <pre class="text-xs text-gray-700 bg-gray-100 rounded-lg p-3 overflow-x-auto">{{ json_encode($log->metadata, JSON_PRETTY_PRINT) }}</pre>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-gray-500">
                                    <i class="fas fa-clipboard-list text-gray-300 text-3xl mb-2"></i>
                                    <p>No audit log entries yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-5 py-4 border-t border-gray-200">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
<script>
    function toggleMetadata(id) {
        const row = document.getElementById('metadata-' + id);
        const icon = document.getElementById('icon-' + id);

        if (row.classList.contains('hidden')) {
            row.classList.remove('hidden');
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            row.classList.add('hidden');
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    }
</script>
@endpush
