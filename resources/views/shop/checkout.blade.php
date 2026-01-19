@extends('layouts.app')

@section('title', 'Checkout - AstroServices')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Checkout</h1>
    
    <div class="grid md:grid-cols-2 gap-8">
        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Order Summary</h2>
            
            @if(isset($product))
                <!-- Single Product Checkout -->
                <div class="flex items-center space-x-4 border-b pb-4 mb-4">
                    @if($product->image)
                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-20 h-20 rounded object-cover">
                    @else
                        <div class="w-20 h-20 bg-indigo-600 rounded flex items-center justify-center">
                            <span class="text-white text-xs font-bold">{{ substr($product->name, 0, 3) }}</span>
                        </div>
                    @endif
                    <div>
                        <h3 class="font-bold">{{ $product->name }}</h3>
                        <p class="text-gray-600">Quantity: {{ request('quantity', 1) }}</p>
                        <p class="text-indigo-600 font-bold">{{ formatPrice($product->price) }} each</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold">Total: {{ formatPrice($total) }}</p>
                </div>
            @else
                <!-- Cart Checkout -->
                @php $cartTotal = 0; @endphp
                @foreach($cart as $id => $item)
                    @php $cartTotal += $item['price'] * $item['quantity']; @endphp
                    <div class="flex items-center space-x-4 border-b pb-4 mb-4">
                        @if(isset($item['image']) && $item['image'])
                            <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="w-20 h-20 rounded object-cover">
                        @else
                            <div class="w-20 h-20 bg-indigo-600 rounded flex items-center justify-center">
                                <span class="text-white text-xs font-bold">{{ substr($item['name'], 0, 3) }}</span>
                            </div>
                        @endif
                        <div>
                            <h3 class="font-bold">{{ $item['name'] }}</h3>
                            <p class="text-gray-600">Quantity: {{ $item['quantity'] }}</p>
                            <p class="text-indigo-600 font-bold">{{ formatPrice($item['price']) }} each</p>
                        </div>
                    </div>
                @endforeach
                <div class="text-right">
                    <p class="text-2xl font-bold">Total: {{ formatPrice($cartTotal) }}</p>
                </div>
            @endif
        </div>

        <!-- Checkout Form -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Shipping Details</h2>
            
            <form action="{{ route('order.place') }}" method="POST" class="space-y-4">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address <span class="text-red-500">*</span></label>
                    <textarea name="address" rows="3" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ auth()->user()->address ?? '' }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">City <span class="text-red-500">*</span></label>
                        <input type="text" name="city" value="{{ auth()->user()->city ?? '' }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pincode <span class="text-red-500">*</span></label>
                        <input type="text" name="pincode" value="{{ auth()->user()->pincode ?? '' }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="save_address" value="1" class="mr-2">
                        <span class="text-sm text-gray-700">Save this address for future orders</span>
                    </label>
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

                <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg font-bold hover:bg-indigo-700">
                    Place Order
                </button>
            </form>
        </div>
    </div>
</div>
@endsection