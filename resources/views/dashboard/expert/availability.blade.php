@extends('dashboard.layout')

@section('title', 'Manage Availability')

@section('dashboard-content')
@include('dashboard.expert.submenu')

<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm mb-4 sm:mb-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <h1 class="text-xl sm:text-2xl font-bold">Manage Availability</h1>
    <p class="text-white/90 mt-1 text-sm sm:text-base">Set your availability for the next 2 weeks</p>
</div>

<div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($dates as $item)
        <div class="border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $item['date']->format('l') }}</h3>
                    <p class="text-sm text-gray-600">{{ $item['date']->format('F j, Y') }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer availability-toggle" 
                           data-date="{{ $item['date']->format('Y-m-d') }}"
                           {{ $item['is_available'] ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                </label>
            </div>
            <p class="text-sm font-medium {{ $item['is_available'] ? 'text-green-600' : 'text-red-600' }}">
                <span class="status-text">{{ $item['is_available'] ? 'Available' : 'Not Available' }}</span>
            </p>
        </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.availability-toggle').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const date = this.dataset.date;
        const isAvailable = this.checked;
        const statusText = this.closest('.border').querySelector('.status-text');
        const statusPara = statusText.parentElement;
        
        fetch('{{ route('expert.availability.update') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ date, is_available: isAvailable })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                statusText.textContent = isAvailable ? 'Available' : 'Not Available';
                statusPara.className = 'text-sm font-medium ' + (isAvailable ? 'text-green-600' : 'text-red-600');
            }
        })
        .catch(error => console.error('Error:', error));
    });
});
</script>
@endpush
@endsection
