@extends('dashboard.layout')

@section('title', __('messages.my_consultations') . ' - ' . __('messages.dashboard'))

@section('dashboard-content')
@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 sm:px-6 py-4 rounded-lg mb-4 sm:mb-6 shadow-sm text-sm sm:text-base">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif

<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm mb-4 sm:mb-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <h1 class="text-xl sm:text-2xl font-bold">{{ __('messages.my_consultations') }}</h1>
    <p class="text-white/90 mt-1 text-sm sm:text-base">View and manage your astrology consultations</p>
</div>

<div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 sm:mb-6 gap-3">
        <h2 class="text-lg sm:text-xl font-bold">Consultations History</h2>
        <div class="flex gap-2">
            <select onchange="window.location.href='{{ route('dashboard.consultations') }}?status='+this.value" class="px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm sm:text-base w-full sm:w-auto">
                <option value="all" {{ !request('status') || request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>{{ __('messages.scheduled') }}</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('messages.completed') }}</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('messages.cancelled') }}</option>
            </select>
            <a href="{{ route('consultations.index') }}" class="px-3 sm:px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm sm:text-base whitespace-nowrap">
                <i class="fas fa-plus mr-1"></i><span class="hidden sm:inline">Book Now</span><span class="sm:inline hidden">Book</span>
            </a>
        </div>
    </div>

    @if($consultations->count() > 0)
        <div class="space-y-3 sm:space-y-4">
            @foreach($consultations as $consultation)
                <div class="border border-gray-200 rounded-lg p-3 sm:p-4 hover:shadow-md transition">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-2">
                                <span class="px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium
                                    {{ $consultation->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $consultation->status == 'scheduled' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $consultation->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ __('messages.' . $consultation->status) }}
                                </span>
                                <span class="text-xs sm:text-sm text-gray-500">{{ $consultation->type }}</span>
                            </div>
                            <p class="text-sm sm:text-base text-gray-700 mb-2">{{ $consultation->description ?? __('messages.astrology_consultation') }}</p>
                            <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-xs sm:text-sm text-gray-500">
                                <span><i class="fas fa-calendar mr-1"></i>{{ $consultation->scheduled_at ? \Carbon\Carbon::parse($consultation->scheduled_at)->format('M d, Y h:i A') : 'Not scheduled' }}</span>
                                <span><i class="fas fa-clock mr-1"></i>{{ $consultation->duration }} min</span>
                                <span><i class="fas fa-rupee-sign mr-1"></i>{{ number_format($consultation->amount, 2) }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 w-full sm:w-auto">
                            <a href="{{ route('dashboard.consultation.details', $consultation->id) }}" class="px-3 sm:px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm sm:text-base text-center">
                                View Details
                            </a>
                            @if($consultation->status == 'completed')
                                <a href="{{ route('dashboard.consultation.report', $consultation->id) }}" class="px-3 sm:px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm sm:text-base text-center">
                                    View Report
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8 sm:py-12 px-4">
            <i class="fas fa-calendar-alt text-5xl sm:text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-base sm:text-lg mb-4">{{ __('messages.no_items_found') }}</p>
            <a href="{{ route('consultations.index') }}" class="inline-block px-4 sm:px-6 py-2 sm:py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm sm:text-base">
                {{ __('messages.book_now') }}
            </a>
        </div>
    @endif
</div>
@endsection
