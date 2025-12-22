@extends('layouts.app')

@section('title', 'My Poojas - Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">My Pooja Bookings</h1>
        <a href="{{ route('pooja.index') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
            Book New Pooja
        </a>
    </div>

    <!-- Status Filter -->
    <div class="mb-6">
        <div class="flex space-x-4">
            <a href="{{ route('dashboard.poojas') }}" class="px-4 py-2 rounded-lg {{ !request('status') || request('status') == 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">All</a>
            <a href="{{ route('dashboard.poojas', ['status' => 'booked']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'booked' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Booked</a>
            <a href="{{ route('dashboard.poojas', ['status' => 'completed']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'completed' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Completed</a>
            <a href="{{ route('dashboard.poojas', ['status' => 'cancelled']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'cancelled' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Cancelled</a>
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
                            {{ ucfirst($pooja->status) }}
                        </span>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Date & Time</p>
                            <p class="font-medium">{{ \Carbon\Carbon::parse($pooja->scheduled_at)->format('M d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Location</p>
                            <p class="font-medium">{{ $pooja->location ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Description</p>
                            <p class="font-medium">{{ \Str::limit($pooja->description, 50) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Amount</p>
                            <p class="font-medium text-indigo-600">{{ formatPrice($pooja->amount) }}</p>
                        </div>
                    </div>
                    
                    <div class="flex space-x-3">
                        <a href="{{ route('dashboard.pooja.details', $pooja->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">View Details</a>
                        @if($pooja->status == 'completed')
                            <a href="{{ route('pooja.index') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Book Again</a>
                        @elseif($pooja->status == 'booked' || $pooja->status == 'confirmed')
                            <button class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Cancel</button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="mx-auto h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Pooja Bookings</h3>
            <p class="text-gray-600 mb-6">You haven't booked any poojas yet. Start by booking your first pooja.</p>
            <a href="{{ route('pooja.index') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
                Browse Pooja Services
            </a>
        </div>
    @endif
</div>
@endsection