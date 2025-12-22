@extends('layouts.app')

@section('title', 'Pooja Checkout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Pooja Checkout</h1>
    
    <div class="grid md:grid-cols-2 gap-8">
        <!-- Booking Summary -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Booking Summary</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Pooja Name:</span>
                    <span class="font-bold">{{ $booking['name'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Type:</span>
                    <span>{{ ucfirst($booking['type']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Scheduled Date:</span>
                    <span>{{ \Carbon\Carbon::parse($booking['scheduled_at'])->format('M d, Y h:i A') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Devotee Name:</span>
                    <span>{{ $booking['devotee_name'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Phone:</span>
                    <span>{{ $booking['phone'] }}</span>
                </div>
                @if($booking['email'])
                <div class="flex justify-between">
                    <span class="text-gray-600">Email:</span>
                    <span>{{ $booking['email'] }}</span>
                </div>
                @endif
                @if($booking['gotra'])
                <div class="flex justify-between">
                    <span class="text-gray-600">Gotra:</span>
                    <span>{{ $booking['gotra'] }}</span>
                </div>
                @endif
            </div>

            <div class="border-t mt-4 pt-4">
                <div class="flex justify-between items-center">
                    <span class="text-xl font-bold">Total Amount:</span>
                    <span class="text-2xl font-bold text-orange-600">{{ formatPrice($booking['amount']) }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Payment Method</h2>
            
            <form action="{{ route('pooja.order.place') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Payment Method</label>
                    <div class="space-y-3">
                        @foreach($paymentGateways as $gateway)
                            <label class="flex items-start p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_gateway" value="{{ $gateway->code }}" required class="mt-1 mr-3">
                                <div class="flex-1">
                                    <div class="font-semibold">{{ $gateway->name }}</div>
                                    <div class="text-sm text-gray-600">{{ $gateway->description }}</div>
                                    @if($gateway->is_test_mode)
                                        <span class="inline-block mt-1 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Test Mode</span>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="w-full bg-orange-600 text-white py-3 px-6 rounded-lg font-bold hover:bg-orange-700">
                    Complete Booking & Pay {{ formatPrice($booking['amount']) }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
