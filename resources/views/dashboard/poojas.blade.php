@extends('dashboard.layout')

@section('title', __('messages.my_poojas') . ' - ' . __('messages.dashboard'))

@section('dashboard-content')
<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-6 sm:mb-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ __('messages.my_poojas') }}</h1>
</div>

<div class="flex justify-end mb-4 sm:mb-6">
    <a href="{{ route('pooja.index') }}" class="bg-orange-600 text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-orange-700 shadow-md hover:shadow-lg transition-all text-sm sm:text-base">
        <i class="fas fa-calendar-plus mr-2"></i><span class="hidden sm:inline">{{ __('messages.book_now') }}</span><span class="sm:hidden">Book</span>
    </a>
</div>

<!-- Status Filter -->
<div class="mb-4 sm:mb-6">
    <div class="flex flex-wrap gap-2 sm:gap-3">
        <a href="{{ route('dashboard.poojas') }}" class="px-3 sm:px-4 py-2 rounded-lg transition-all text-sm sm:text-base {{ !request('status') || request('status') == 'all' ? 'bg-orange-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">All</a>
        <a href="{{ route('dashboard.poojas', ['status' => 'booked']) }}" class="px-3 sm:px-4 py-2 rounded-lg transition-all text-sm sm:text-base {{ request('status') == 'booked' ? 'bg-orange-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">{{ __('messages.scheduled') }}</a>
        <a href="{{ route('dashboard.poojas', ['status' => 'completed']) }}" class="px-3 sm:px-4 py-2 rounded-lg transition-all text-sm sm:text-base {{ request('status') == 'completed' ? 'bg-orange-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">{{ __('messages.completed') }}</a>
        <a href="{{ route('dashboard.poojas', ['status' => 'cancelled']) }}" class="px-3 sm:px-4 py-2 rounded-lg transition-all text-sm sm:text-base {{ request('status') == 'cancelled' ? 'bg-orange-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">{{ __('messages.cancelled') }}</a>
    </div>
</div>

@if($poojas->count() > 0)
    <!-- Poojas List -->
    <div class="grid gap-4 sm:gap-6">
        @foreach($poojas as $pooja)
            <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-4 gap-2">
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold">{{ $pooja->name }}</h3>
                        <p class="text-sm sm:text-base text-gray-600">{{ ucfirst($pooja->type) }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs sm:text-sm self-start
                        @if($pooja->status == 'completed') bg-green-100 text-green-800
                        @elseif($pooja->status == 'booked' || $pooja->status == 'confirmed') bg-blue-100 text-blue-800
                        @elseif($pooja->status == 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ __('messages.' . $pooja->status) }}
                    </span>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mb-4">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600">{{ __('messages.pooja_date') }}</p>
                        <p class="text-sm sm:text-base font-medium">{{ \Carbon\Carbon::parse($pooja->scheduled_at)->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600">{{ __('messages.temple') }}</p>
                        <p class="text-sm sm:text-base font-medium">{{ $pooja->location ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600">{{ __('messages.description') }}</p>
                        <p class="text-sm sm:text-base font-medium">{{ \Str::limit($pooja->description, 50) }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600">{{ __('messages.amount') }}</p>
                        <p class="text-sm sm:text-base font-medium text-indigo-600">{{ formatPrice($pooja->amount) }}</p>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <a href="{{ route('dashboard.pooja.details', $pooja->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-center text-sm sm:text-base">{{ __('messages.view_details') }}</a>
                    @if($pooja->status == 'completed')
                        <a href="{{ route('pooja.index') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-center text-sm sm:text-base">{{ __('messages.book_now') }}</a>
                    @elseif($pooja->status == 'booked' || $pooja->status == 'confirmed')
                        <button class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-center text-sm sm:text-base">{{ __('messages.cancel') }}</button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@else
    <!-- Empty State -->
    <div class="text-center py-8 sm:py-12 px-4">
        <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">{{ __('messages.no_items_found') }}</h3>
        <a href="{{ route('pooja.index') }}" class="inline-block bg-indigo-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg hover:bg-indigo-700 text-sm sm:text-base">
            {{ __('messages.pooja_rituals') }}
        </a>
    </div>
@endif
@endsection
