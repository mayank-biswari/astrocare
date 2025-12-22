@extends('layouts.app')

@section('title', 'My Orders - Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">My Orders</h1>
        <a href="{{ route('shop.index') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
            Continue Shopping
        </a>
    </div>

    <!-- Status Filter -->
    <div class="mb-6">
        <div class="flex space-x-4">
            <a href="{{ route('dashboard.orders') }}" class="px-4 py-2 rounded-lg {{ !request('status') || request('status') == 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">All</a>
            <a href="{{ route('dashboard.orders', ['status' => 'pending']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'pending' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Pending</a>
            <a href="{{ route('dashboard.orders', ['status' => 'processing']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'processing' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Processing</a>
            <a href="{{ route('dashboard.orders', ['status' => 'shipped']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'shipped' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Shipped</a>
            <a href="{{ route('dashboard.orders', ['status' => 'delivered']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'delivered' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Delivered</a>
        </div>
    </div>

    @if($orders->count() > 0)
        <!-- Orders List -->
        <div class="grid gap-6">
            @foreach($orders as $order)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold">Order #{{ $order->id }}</h3>
                            <p class="text-gray-600">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm
                            @if($order->status == 'delivered') bg-green-100 text-green-800
                            @elseif($order->status == 'shipped') bg-blue-100 text-blue-800
                            @elseif($order->status == 'processing') bg-yellow-100 text-yellow-800
                            @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Total Amount</p>
                            <p class="font-medium text-indigo-600">{{ formatPrice($order->total_amount) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Payment Status</p>
                            <p class="font-medium">{{ ucfirst($order->payment_status ?? 'pending') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Shipping Address</p>
                            <p class="font-medium">{{ $order->shipping_address ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Items</p>
                            <p class="font-medium">{{ $order->items_count ?? 1 }} item(s)</p>
                        </div>
                    </div>

                    @if($order->items)
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <h4 class="font-bold mb-2">Order Items</h4>
                            @php $items = json_decode($order->items, true); @endphp
                            @if($items)
                                <div class="space-y-3">
                                    @foreach($items as $item)
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                @if(isset($item['image']) && $item['image'])
                                                    <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="w-12 h-12 rounded object-cover">
                                                @else
                                                    <div class="w-12 h-12 bg-indigo-600 rounded flex items-center justify-center">
                                                        <span class="text-white text-xs font-bold">{{ substr($item['name'], 0, 2) }}</span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="font-medium text-sm">{{ $item['name'] }}</p>
                                                    <p class="text-xs text-gray-600">Quantity: {{ $item['quantity'] }}</p>
                                                </div>
                                            </div>
                                            <span class="text-indigo-600 font-medium">{{ formatPrice($item['price'] * $item['quantity']) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    <div class="flex space-x-3">
                        <a href="{{ route('dashboard.order.details', $order->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">View Details</a>
                        @if($order->status == 'delivered')
                            <a href="{{ route('dashboard.order.invoice', $order->id) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Download Invoice</a>
                        @elseif($order->status == 'shipped')
                            <a href="{{ route('dashboard.order.track', $order->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Track Order</a>
                        @elseif($order->status == 'processing')
                            <form action="{{ route('dashboard.order.cancel', $order->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600" onclick="return confirm('Are you sure you want to cancel this order?')">Cancel Order</button>
                            </form>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Orders Found</h3>
            <p class="text-gray-600 mb-6">You haven't placed any orders yet. Explore our spiritual products and accessories.</p>
            <a href="{{ route('shop.index') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
                Start Shopping
            </a>
        </div>
    @endif
</div>
@endsection