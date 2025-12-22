@extends('layouts.app')

@section('title', 'Pooja Details - Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-8">
        <a href="{{ route('dashboard.poojas') }}" class="text-indigo-600 hover:text-indigo-800 mr-4">← Back to Poojas</a>
        <h1 class="text-3xl font-bold">Pooja Details</h1>
    </div>
    
    <div class="grid md:grid-cols-2 gap-8">
        <!-- Pooja Information -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Pooja Information</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Pooja Name:</span>
                    <span class="font-bold">{{ $pooja->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Type:</span>
                    <span>{{ ucfirst($pooja->type) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Scheduled Date:</span>
                    <span>{{ \Carbon\Carbon::parse($pooja->scheduled_at)->format('M d, Y h:i A') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="px-3 py-1 rounded-full text-sm
                        @if($pooja->status == 'completed') bg-green-100 text-green-800
                        @elseif($pooja->status == 'booked' || $pooja->status == 'confirmed') bg-blue-100 text-blue-800
                        @elseif($pooja->status == 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($pooja->status) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Amount:</span>
                    <span class="text-xl font-bold text-orange-600">{{ formatPrice($pooja->amount) }}</span>
                </div>
            </div>
        </div>

        <!-- Additional Details -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Additional Details</h2>
            
            <div class="space-y-3">
                @if($pooja->location)
                <div>
                    <span class="text-gray-600 block mb-1">Location:</span>
                    <span class="font-medium">{{ $pooja->location }}</span>
                </div>
                @endif
                
                <div>
                    <span class="text-gray-600 block mb-1">Description:</span>
                    <p class="text-gray-800">{{ $pooja->description }}</p>
                </div>
                
                @if($pooja->special_requirements)
                <div>
                    <span class="text-gray-600 block mb-1">Special Requirements:</span>
                    <p class="text-gray-800">{{ $pooja->special_requirements }}</p>
                </div>
                @endif
                
                <div>
                    <span class="text-gray-600 block mb-1">Booked On:</span>
                    <span class="font-medium">{{ $pooja->created_at->format('M d, Y h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
        <h2 class="text-xl font-bold mb-4">Actions</h2>
        <div class="flex space-x-3">
            @if($pooja->status == 'completed')
                <a href="{{ route('pooja.index') }}" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">Book Again</a>
            @elseif($pooja->status == 'booked' || $pooja->status == 'confirmed')
                <button class="px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600">Cancel Booking</button>
            @endif
            <a href="{{ route('dashboard.poojas') }}" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Back to List</a>
        </div>
    </div>
</div>
@endsection
