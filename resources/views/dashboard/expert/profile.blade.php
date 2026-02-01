@extends('dashboard.layout')

@section('title', 'My Astrologer Profile')

@section('dashboard-content')
@include('dashboard.expert.submenu')

<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm mb-4 sm:mb-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <h1 class="text-xl sm:text-2xl font-bold">My Astrologer Profile</h1>
    <p class="text-white/90 mt-1 text-sm sm:text-base">Manage your professional profile and services</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

@if($page && $page->slug)
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-700 mb-1">Your profile is live at:</p>
            <a href="{{ url('/pages/' . $page->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium">
                {{ url('/pages/' . $page->slug) }}
            </a>
        </div>
        <a href="{{ url('/pages/' . $page->slug) }}" target="_blank" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-external-link-alt mr-2"></i>View Profile
        </a>
    </div>
</div>
@endif

<form action="{{ route('expert.profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mb-4">
        <h2 class="text-lg sm:text-xl font-bold mb-4">Basic Information</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                <input type="text" name="title" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" value="{{ $page->title ?? auth()->user()->name }}" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Title/Designation *</label>
                <input type="text" name="custom_fields[title]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" value="{{ $page->custom_fields['title'] ?? '' }}" placeholder="e.g., Vedic Astrologer" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Profile Page Image *</label>
                @if($page && $page->image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $page->image) }}" class="rounded-lg" style="max-height: 150px;">
                    </div>
                @endif
                <input type="file" name="image" class="w-full px-4 py-2 border border-gray-300 rounded-lg" accept="image/*" {{ !$page || !$page->image ? 'required' : '' }}>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="custom_fields[status]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="online" {{ ($page && ($page->custom_fields['status'] ?? '') == 'online') ? 'selected' : '' }}>Online</option>
                    <option value="busy" {{ ($page && ($page->custom_fields['status'] ?? '') == 'busy') ? 'selected' : '' }}>Busy</option>
                    <option value="offline" {{ (!$page || ($page->custom_fields['status'] ?? 'offline') == 'offline') ? 'selected' : '' }}>Offline</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                <input type="number" name="custom_fields[rating]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" value="{{ $page->custom_fields['rating'] ?? '' }}" min="0" max="5" step="0.1">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Years of Experience</label>
                <input type="number" name="custom_fields[experience]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" value="{{ $page->custom_fields['experience'] ?? '' }}" min="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Languages (comma separated)</label>
                <input type="text" name="custom_fields[languages]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" value="{{ $page->custom_fields['languages'] ?? '' }}" placeholder="e.g., Hindi, English, Sanskrit">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Total Consultations</label>
                <input type="text" name="custom_fields[consultations]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" value="{{ $page->custom_fields['consultations'] ?? '' }}" placeholder="e.g., 100+">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Expertise (comma separated)</label>
                <input type="text" name="custom_fields[expertise]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" value="{{ $page->custom_fields['expertise'] ?? '' }}" placeholder="e.g., Vedic Astrology, Numerology, Tarot">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Education</label>
                <input type="text" name="custom_fields[education]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" value="{{ $page->custom_fields['education'] ?? '' }}" placeholder="e.g., PhD in Astrology">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">About Me *</label>
                <div id="editor" style="height: 300px;"></div>
                <textarea name="body" id="body-content" style="display: none;" required>{{ $page->body ?? '' }}</textarea>
            </div>
        </div>
    </div>

    @if($pageType && $pageType->fields_config['custom_fields'])
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mb-4">
        <h2 class="text-lg sm:text-xl font-bold mb-4">Professional Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($pageType->fields_config['custom_fields'] as $field)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ $field['label'] }}{{ $field['required'] ? ' *' : '' }}</label>
                @if($field['type'] === 'select')
                    <select name="custom_fields[{{ $field['name'] }}]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" {{ $field['required'] ? 'required' : '' }}>
                        <option value="">Select...</option>
                        @foreach($field['options'] as $option)
                            <option value="{{ $option }}" {{ ($page->custom_fields[$field['name']] ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                @elseif($field['type'] === 'textarea')
                    <textarea name="custom_fields[{{ $field['name'] }}]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" rows="3" {{ $field['required'] ? 'required' : '' }}>{{ $page->custom_fields[$field['name']] ?? '' }}</textarea>
                @else
                    <input type="{{ $field['type'] }}" name="custom_fields[{{ $field['name'] }}]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" value="{{ $page->custom_fields[$field['name']] ?? '' }}" {{ $field['required'] ? 'required' : '' }}>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mb-4">
        <h2 class="text-lg sm:text-xl font-bold mb-4">Service Pricing</h2>
        <div class="space-y-4">
            @if($page && $page->product && $page->product->variants->count() > 0)
                @foreach($page->product->variants as $index => $variant)
                <div class="border border-gray-200 rounded-lg p-4">
                    <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Service Type *</label>
                            <input type="text" name="variants[{{ $index }}][name]" class="w-full px-4 py-2 border border-gray-300 rounded-lg" value="{{ $variant->name }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Price (INR) *</label>
                            <input type="number" name="variants[{{ $index }}][price]" class="w-full px-4 py-2 border border-gray-300 rounded-lg" step="0.01" value="{{ $variant->price }}" required>
                        </div>
                        @foreach($currencies as $currency)
                        @if($currency->code !== 'INR')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $currency->symbol }} Price</label>
                            <input type="number" name="variants[{{ $index }}][currency_prices][{{ $currency->code }}][price]" class="w-full px-4 py-2 border border-gray-300 rounded-lg" step="0.01" value="{{ $variant->currency_prices[$currency->code]['price'] ?? '' }}">
                        </div>
                        @endif
                        @endforeach
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Min Duration</label>
                            <input type="number" name="variants[{{ $index }}][min_quantity]" class="w-full px-4 py-2 border border-gray-300 rounded-lg" value="{{ $variant->min_quantity ?? 1 }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                            <input type="text" name="variants[{{ $index }}][quantity_unit]" class="w-full px-4 py-2 border border-gray-300 rounded-lg" value="{{ $variant->quantity_unit ?? 'min' }}">
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="variants[{{ $index }}][is_active]" value="1" {{ $variant->is_active ? 'checked' : '' }} class="mr-2" id="active{{ $index }}">
                            <label for="active{{ $index }}" class="text-sm font-medium text-gray-700">Active</label>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Service Type *</label>
                            <input type="text" name="variants[0][name]" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="e.g., Call, Chat" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Price (INR) *</label>
                            <input type="number" name="variants[0][price]" class="w-full px-4 py-2 border border-gray-300 rounded-lg" step="0.01" required>
                        </div>
                        @foreach($currencies as $currency)
                        @if($currency->code !== 'INR')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $currency->symbol }} Price</label>
                            <input type="number" name="variants[0][currency_prices][{{ $currency->code }}][price]" class="w-full px-4 py-2 border border-gray-300 rounded-lg" step="0.01">
                        </div>
                        @endif
                        @endforeach
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Min Duration</label>
                            <input type="number" name="variants[0][min_quantity]" class="w-full px-4 py-2 border border-gray-300 rounded-lg" value="1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                            <input type="text" name="variants[0][quantity_unit]" class="w-full px-4 py-2 border border-gray-300 rounded-lg" value="min">
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="variants[0][is_active]" value="1" checked class="mr-2" id="active0">
                            <label for="active0" class="text-sm font-medium text-gray-700">Active</label>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
            <i class="fas fa-save mr-2"></i>Save Profile
        </button>
        <a href="{{ route('expert.dashboard') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-times mr-2"></i>Cancel
        </a>
    </div>
</form>

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
var quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link', 'image'],
            ['clean']
        ]
    }
});

quill.root.innerHTML = {!! json_encode($page->body ?? '') !!};

quill.on('text-change', function() {
    document.querySelector('#body-content').value = quill.root.innerHTML;
});

document.querySelector('form').addEventListener('submit', function() {
    document.querySelector('#body-content').value = quill.root.innerHTML;
});
</script>
@endsection
