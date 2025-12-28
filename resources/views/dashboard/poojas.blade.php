@extends('layouts.app')

@section('title', __('messages.my_poojas') . ' - ' . __('messages.dashboard'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">{{ __('messages.my_poojas') }}</h1>
        <a href="{{ route('pooja.index') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
            {{ __('messages.book_now') }}
        </a>
    </div>

    <!-- Status Filter -->
    <div class="mb-6">
        <div class="flex space-x-4">
            <a href="{{ route('dashboard.poojas') }}" class="px-4 py-2 rounded-lg {{ !request('status') || request('status') == 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">All</a>
            <a href="{{ route('dashboard.poojas', ['status' => 'booked']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'booked' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">{{ __('messages.scheduled') }}</a>
            <a href="{{ route('dashboard.poojas', ['status' => 'completed']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'completed' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">{{ __('messages.completed') }}</a>
            <a href="{{ route('dashboard.poojas', ['status' => 'cancelled']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'cancelled' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">{{ __('messages.cancelled') }}</a>
        </div>
    </div>

    @if($poojas->count() > 0)
        <!-- Poojas List -->
        <div class="grid gap-6">
            @foreach($poojas as $pooja)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold">{{ $pooja->name }}</h3>
                            <p class="text-gray-600">{{ ucfirst($pooja->type) }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm
                            @if($pooja->status == 'completed') bg-green-100 text-green-800
                            @elseif($pooja->status == 'booked' || $pooja->status == 'confirmed') bg-blue-100 text-blue-800
                            @elseif($pooja->status == 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ __('messages.' . $pooja->status) }}
                        </span>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">{{ __('messages.pooja_date') }}</p>
                            <p class="font-medium">{{ \Carbon\Carbon::parse($pooja->scheduled_at)->format('M d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('messages.temple') }}</p>
                            <p class="font-medium">{{ $pooja->location ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('messages.description') }}</p>
                            <p class="font-medium">{{ \Str::limit($pooja->description, 50) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('messages.amount') }}</p>
                            <p class="font-medium text-indigo-600">{{ formatPrice($pooja->amount) }}</p>
                        </div>
                    </div>
                    
                    <div class="flex space-x-3">
                        <a href="{{ route('dashboard.pooja.details', $pooja->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">{{ __('messages.view_details') }}</a>
                        @if($pooja->status == 'completed')
                            <a href="{{ route('pooja.index') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">{{ __('messages.book_now') }}</a>
                        @elseif($pooja->status == 'booked' || $pooja->status == 'confirmed')
                            <button class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">{{ __('messages.cancel') }}</button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('messages.no_items_found') }}</h3>
            <a href="{{ route('pooja.index') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
                {{ __('messages.pooja_rituals') }}
            </a>
        </div>
    @endif
</div>
@endsection