@extends('lms.layouts.app')

@section('title', 'Lead: ' . $lead->full_name)
@section('page-title', 'Lead Details')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header with actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $lead->full_name }}</h2>
            <p class="text-sm text-gray-500 mt-1">Created {{ $lead->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('lms.leads.edit', $lead) }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            <a href="{{ route('lms.leads.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Lead Information Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Lead Information</h3>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                @switch($lead->status)
                    @case('new') bg-blue-100 text-blue-800 @break
                    @case('contacted') bg-yellow-100 text-yellow-800 @break
                    @case('qualified') bg-purple-100 text-purple-800 @break
                    @case('converted') bg-green-100 text-green-800 @break
                    @case('lost') bg-red-100 text-red-800 @break
                    @default bg-gray-100 text-gray-800
                @endswitch
            ">
                {{ ucfirst($lead->status) }}
            </span>
        </div>
        <div class="px-6 py-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $lead->full_name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <a href="mailto:{{ $lead->email }}" class="text-indigo-600 hover:text-indigo-800">{{ $lead->email }}</a>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Phone Number</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $lead->phone_number }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Source</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $lead->source ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $lead->date_of_birth ? $lead->date_of_birth->format('M d, Y') : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Place of Birth</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $lead->place_of_birth ?? '—' }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Message</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $lead->message ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $lead->created_at->format('M d, Y h:i A') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $lead->updated_at->format('M d, Y h:i A') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Status Change Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Update Status</h3>
        </div>
        <form action="{{ route('lms.leads.status', $lead) }}" method="POST" class="px-6 py-4">
            @csrf
            @method('PUT')
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">New Status</label>
                    <select name="status" id="status"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5 border"
                            onchange="toggleStatusNote(this.value)">
                        <option value="new" {{ $lead->status === 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ $lead->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="qualified" {{ $lead->status === 'qualified' ? 'selected' : '' }}>Qualified</option>
                        <option value="converted" {{ $lead->status === 'converted' ? 'selected' : '' }}>Converted</option>
                        <option value="lost" {{ $lead->status === 'lost' ? 'selected' : '' }}>Lost</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex-1" id="status-note-container" style="display: none;">
                    <label for="status_note" class="block text-sm font-medium text-gray-700 mb-1">
                        Reason Note <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="note" id="status_note"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5 border"
                           placeholder="Reason for status change">
                    @error('note')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-end">
                    <button type="submit"
                            class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        Update Status
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Notes Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Notes</h3>
        </div>

        <!-- Add Note Form -->
        <form action="{{ route('lms.leads.notes.store', $lead) }}" method="POST" class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            @csrf
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <label for="note_body" class="sr-only">Add a note</label>
                    <textarea name="body" id="note_body" rows="2"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5 border {{ $errors->has('body') ? 'border-red-500' : '' }}"
                              placeholder="Add a note about this lead...">{{ old('body') }}</textarea>
                    @error('body')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-start">
                    <button type="submit"
                            class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-plus mr-1"></i> Add Note
                    </button>
                </div>
            </div>
        </form>

        <!-- Notes List -->
        <div class="divide-y divide-gray-100">
            @forelse($lead->notes as $note)
                <div class="px-6 py-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-sm text-gray-900">{{ $note->body }}</p>
                            <div class="mt-2 flex items-center space-x-3 text-xs text-gray-500">
                                <span><i class="fas fa-user mr-1"></i> {{ $note->author->name ?? 'Unknown' }}</span>
                                <span><i class="fas fa-clock mr-1"></i> {{ $note->created_at->format('M d, Y h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-sm text-gray-500">
                    <i class="fas fa-sticky-note text-gray-300 text-2xl mb-2"></i>
                    <p>No notes yet. Add the first note above.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Follow-Ups Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Follow-Ups</h3>
        </div>

        <!-- Add Follow-Up Form -->
        <form action="{{ route('lms.leads.follow-ups.store', $lead) }}" method="POST" class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            @csrf
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <label for="follow_up_description" class="sr-only">Follow-up description</label>
                    <input type="text" name="description" id="follow_up_description"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5 border {{ $errors->has('description') ? 'border-red-500' : '' }}"
                           placeholder="Follow-up description..." value="{{ old('description') }}">
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="follow_up_date" class="sr-only">Scheduled date</label>
                    <input type="date" name="scheduled_date" id="follow_up_date"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5 border {{ $errors->has('scheduled_date') ? 'border-red-500' : '' }}"
                           value="{{ old('scheduled_date') }}" min="{{ date('Y-m-d') }}">
                    @error('scheduled_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-start">
                    <button type="submit"
                            class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-calendar-plus mr-1"></i> Schedule
                    </button>
                </div>
            </div>
        </form>

        <!-- Follow-Ups List -->
        <div class="divide-y divide-gray-100">
            @forelse($lead->followUps as $followUp)
                <div class="px-6 py-4 {{ $followUp->isOverdue() ? 'bg-red-50 border-l-4 border-red-400' : '' }}">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm text-gray-900 {{ $followUp->completed_at ? 'line-through text-gray-500' : '' }}">
                                {{ $followUp->description }}
                            </p>
                            <div class="mt-2 flex items-center space-x-3 text-xs text-gray-500">
                                <span>
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $followUp->scheduled_date->format('M d, Y') }}
                                </span>
                                <span><i class="fas fa-user mr-1"></i> {{ $followUp->author->name ?? 'Unknown' }}</span>
                                @if($followUp->isOverdue())
                                    <span class="text-red-600 font-semibold">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Overdue
                                    </span>
                                @endif
                                @if($followUp->completed_at)
                                    <span class="text-green-600">
                                        <i class="fas fa-check-circle mr-1"></i> Completed {{ $followUp->completed_at->format('M d, Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if(!$followUp->completed_at)
                            <form action="{{ route('lms.follow-ups.complete', $followUp) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit"
                                        class="px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 rounded-lg hover:bg-green-200 transition-colors"
                                        title="Mark as complete">
                                    <i class="fas fa-check mr-1"></i> Complete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-sm text-gray-500">
                    <i class="fas fa-calendar-alt text-gray-300 text-2xl mb-2"></i>
                    <p>No follow-ups scheduled. Add one above.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Delete Lead -->
    <div class="bg-white rounded-lg shadow-sm border border-red-200">
        <div class="px-6 py-4 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-red-800">Danger Zone</h3>
                <p class="text-xs text-red-600 mt-1">Deleting a lead will permanently remove all associated notes and follow-ups.</p>
            </div>
            <form action="{{ route('lms.leads.destroy', $lead) }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to delete this lead? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 transition-colors">
                    <i class="fas fa-trash mr-1"></i> Delete Lead
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleStatusNote(status) {
        const container = document.getElementById('status-note-container');
        if (status === 'converted' || status === 'lost') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleStatusNote(document.getElementById('status').value);
    });
</script>
@endpush
@endsection
