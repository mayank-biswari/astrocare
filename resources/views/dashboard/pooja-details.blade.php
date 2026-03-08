@extends('dashboard.layout')

@section('title', 'Pooja Details - Dashboard')

@section('dashboard-content')
@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4" role="alert">
    <span class="block sm:inline">{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4" role="alert">
    <span class="block sm:inline">{{ session('error') }}</span>
</div>
@endif

<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm mb-4 sm:mb-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <div class="flex items-center">
        <a href="{{ route('dashboard.poojas') }}" class="text-white hover:text-white/80 mr-4">← Back to Poojas</a>
        <h1 class="text-xl sm:text-2xl font-bold">Pooja Details</h1>
    </div>
</div>
    
<div class="grid md:grid-cols-2 gap-4 sm:gap-8 mb-4 sm:mb-8">
    <!-- Pooja Information -->
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
        <h2 class="text-lg sm:text-xl font-bold mb-4">Pooja Information</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Pooja Name:</span>
                    @if($cmsPage)
                        <a href="{{ route('cms.show', $cmsPage->slug) }}" class="font-bold text-orange-600 hover:text-orange-700">{{ $pooja->name }}</a>
                    @else
                        <span class="font-bold">{{ $pooja->name }}</span>
                    @endif
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
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
        <h2 class="text-lg sm:text-xl font-bold mb-4">Additional Details</h2>
            
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
                
                @if($order && $order->shipping_address)
                <div>
                    <span class="text-gray-600 block mb-1">Contact Details:</span>
                    @if(isset($order->shipping_address['phone']))
                        <p class="text-gray-800">Phone: {{ $order->shipping_address['phone'] }}</p>
                    @endif
                    @if(isset($order->shipping_address['address']))
                        <p class="text-gray-800">Address: {{ $order->shipping_address['address'] }}</p>
                    @endif
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
<div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
    <h2 class="text-lg sm:text-xl font-bold mb-4">Actions</h2>
    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
        @if($pooja->status == 'completed')
            <a href="{{ route('pooja.index') }}" class="px-4 sm:px-6 py-2 sm:py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 text-center text-sm sm:text-base">Book Again</a>
        @elseif($pooja->status == 'booked' || $pooja->status == 'confirmed')
            <button class="px-4 sm:px-6 py-2 sm:py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm sm:text-base">Cancel Booking</button>
        @endif
        <a href="{{ route('dashboard.poojas') }}" class="px-4 sm:px-6 py-2 sm:py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-center text-sm sm:text-base">Back to List</a>
    </div>
</div>
@endsection
