@extends('layouts.app')

@section('title', 'Order Details - Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-8">
        <a href="{{ route('dashboard.orders') }}" class="text-indigo-600 hover:text-indigo-800 mr-4">← Back to Orders</a>
        <h1 class="text-3xl font-bold">Order Details - #{{ $order->id }}</h1>
    </div>
    
    <div class="grid md:grid-cols-2 gap-8">
        <!-- Order Information -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Order Information</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Order Number:</span>
                    <span class="font-bold">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Order Date:</span>
                    <span>{{ $order->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="px-3 py-1 rounded-full text-sm
                        @if($order->status == 'delivered') bg-green-100 text-green-800
                        @elseif($order->status == 'shipped') bg-blue-100 text-blue-800
                        @elseif($order->status == 'processing') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Payment Method:</span>
                    <span>{{ ucfirst($order->payment_method) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Amount:</span>
                    <span class="text-xl font-bold text-indigo-600">{{ formatPrice($order->total_amount) }}</span>
                </div>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Shipping Address</h2>
            
            <div class="text-gray-600">
                <p class="font-bold text-gray-800">{{ $order->user->name }}</p>
                <p>{{ $order->shipping_address }}</p>
                <p>Phone: {{ $order->phone }}</p>
                <p>Email: {{ $order->user->email }}</p>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
        <h2 class="text-xl font-bold mb-4">Order Items</h2>
        
        @php $items = json_decode($order->items, true); @endphp
        @if($items)
            <div class="space-y-4">
                @foreach($items as $item)
                    <div class="flex items-center justify-between border-b pb-4">
                        <div class="flex items-center space-x-4">
                            @if(isset($item['image']) && $item['image'])
                                <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="w-20 h-20 rounded object-cover">
                            @else
                                <div class="w-20 h-20 bg-indigo-600 rounded flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">{{ substr($item['name'], 0, 3) }}</span>
                                </div>
                            @endif
                            <div>
                                <h3 class="font-bold">{{ $item['name'] }}</h3>
                                <p class="text-gray-600">Quantity: {{ $item['quantity'] }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold">{{ formatPrice($item['price']) }}</p>
                            <p class="text-gray-600">Qty: {{ $item['quantity'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Order Summary -->
        <div class="border-t pt-4 mt-4">
            <div class="flex justify-between items-center mb-2">
                <span>Subtotal:</span>
                <span>{{ formatPrice($order->total_amount) }}</span>
            </div>
            <div class="flex justify-between items-center mb-2">
                <span>Shipping:</span>
                <span class="text-green-600">Free</span>
            </div>
            <div class="flex justify-between items-center text-xl font-bold">
                <span>Total:</span>
                <span class="text-indigo-600">{{ formatPrice($order->total_amount) }}</span>
            </div>
        </div>
    </div>

    <!-- Order Timeline -->
    <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
        <h2 class="text-xl font-bold mb-4">Order Timeline</h2>
        
        <div class="space-y-4">
            <div class="flex items-center space-x-4">
                <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                <div>
                    <p class="font-bold">Order Delivered</p>
                    <p class="text-gray-600 text-sm">Dec 13, 2024 at 2:30 PM</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="w-4 h-4 bg-blue-500 rounded-full"></div>
                <div>
                    <p class="font-bold">Out for Delivery</p>
                    <p class="text-gray-600 text-sm">Dec 13, 2024 at 9:00 AM</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="w-4 h-4 bg-yellow-500 rounded-full"></div>
                <div>
                    <p class="font-bold">Order Shipped</p>
                    <p class="text-gray-600 text-sm">Dec 11, 2024 at 4:00 PM</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="w-4 h-4 bg-gray-500 rounded-full"></div>
                <div>
                    <p class="font-bold">Order Confirmed</p>
                    <p class="text-gray-600 text-sm">Dec 10, 2024 at 6:15 PM</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection