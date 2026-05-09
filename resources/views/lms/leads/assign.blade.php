@extends('lms.layouts.app')

@section('title', 'Assign Lead: ' . $lead->full_name)
@section('page-title', 'Assign Lead')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Assign Lead: {{ $lead->full_name }}</h2>
            <p class="text-sm text-gray-500 mt-1">Update the owner and assignee for this lead.</p>
        </div>

        <form action="{{ route('lms.leads.assign.store', $lead) }}" method="POST" class="px-6 py-6 space-y-6">
            @csrf

            <!-- Owner -->
            <div>
                <label for="owner_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Owner
                </label>
                <select name="owner_id" id="owner_id"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5 border {{ $errors->has('owner_id') ? 'border-red-500' : '' }}">
                    <option value="">None (Clear Owner)</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('owner_id', $lead->owner_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                @error('owner_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Assigned To -->
            <div>
                <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">
                    Assigned To
                </label>
                <select name="assigned_to" id="assigned_to"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5 border {{ $errors->has('assigned_to') ? 'border-red-500' : '' }}">
                    <option value="">None (Clear Assignee)</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('assigned_to', $lead->assigned_to) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                @error('assigned_to')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('lms.leads.show', $lead) }}"
                   class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="fas fa-user-check mr-1"></i> Save Assignment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
