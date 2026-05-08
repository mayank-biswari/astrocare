@extends('lms.layouts.app')

@section('title', 'Edit Lead: ' . $lead->full_name)
@section('page-title', 'Edit Lead')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Edit Lead</h2>
            <p class="text-sm text-gray-500 mt-1">Update the lead details below.</p>
        </div>

        <form action="{{ route('lms.leads.update', $lead) }}" method="POST" class="px-6 py-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Required Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Full Name -->
                <div class="md:col-span-2">
                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $lead->full_name) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5 border {{ $errors->has('full_name') ? 'border-red-500' : '' }}"
                           placeholder="Enter full name" required>
                    @error('full_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email', $lead->email) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5 border {{ $errors->has('email') ? 'border-red-500' : '' }}"
                           placeholder="email@example.com" required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $lead->phone_number) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5 border {{ $errors->has('phone_number') ? 'border-red-500' : '' }}"
                           placeholder="+1 234 567 8900" required>
                    @error('phone_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Optional Fields -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-sm font-medium text-gray-700 mb-4">Additional Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Date of Birth -->
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">
                            Date of Birth
                        </label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $lead->date_of_birth?->format('Y-m-d')) }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5 border {{ $errors->has('date_of_birth') ? 'border-red-500' : '' }}">
                        @error('date_of_birth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Place of Birth -->
                    <div>
                        <label for="place_of_birth" class="block text-sm font-medium text-gray-700 mb-1">
                            Place of Birth
                        </label>
                        <input type="text" name="place_of_birth" id="place_of_birth" value="{{ old('place_of_birth', $lead->place_of_birth) }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5 border {{ $errors->has('place_of_birth') ? 'border-red-500' : '' }}"
                               placeholder="City, Country">
                        @error('place_of_birth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Source -->
                    <div>
                        <label for="source" class="block text-sm font-medium text-gray-700 mb-1">
                            Source
                        </label>
                        <input type="text" name="source" id="source" value="{{ old('source', $lead->source) }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5 border {{ $errors->has('source') ? 'border-red-500' : '' }}"
                               placeholder="e.g., website, referral, campaign">
                        @error('source')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Message -->
                <div class="mt-6">
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                        Message
                    </label>
                    <textarea name="message" id="message" rows="4"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5 border {{ $errors->has('message') ? 'border-red-500' : '' }}"
                              placeholder="Any additional notes or message from the lead...">{{ old('message', $lead->message) }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('lms.leads.show', $lead) }}"
                   class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="fas fa-save mr-1"></i> Update Lead
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
