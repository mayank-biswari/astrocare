@extends('layouts.app')

@section('title', 'Checkout - AstroServices')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Checkout</h1>
    
    <div class="grid md:grid-cols-2 gap-8">
        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Order Summary</h2>
            
            <div class="border-b pb-4 mb-4">
                <div class="flex items-center space-x-4">
                    <div class="w-20 h-20 bg-indigo-600 rounded flex items-center justify-center">
                        <i class="fas fa-question-circle text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold">Ask Your Question</h3>
                        <p class="text-gray-600">Category: {{ ucfirst($questionData['category']) }}</p>
                        <p class="text-indigo-600 font-bold">₹{{ number_format($questionData['amount'], 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold">Total: ₹{{ number_format($questionData['amount'], 2) }}</p>
            </div>
        </div>

        <!-- Checkout Form -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Contact Details</h2>
            
            <form action="{{ route('ask.order.place') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ auth()->user()->name ?? '' }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ auth()->user()->email ?? '' }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone" value="{{ auth()->user()->phone ?? '' }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method <span class="text-red-500">*</span></label>
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

                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                    <p class="text-sm text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        You will receive a detailed answer within 24-48 hours after payment confirmation.
                    </p>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg font-bold hover:bg-indigo-700">
                    Place Order
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
