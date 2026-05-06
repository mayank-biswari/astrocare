@extends('dashboard.layout')

@section('title', 'Order Details - Dashboard')

@section('dashboard-content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm mb-4 sm:mb-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <div class="flex items-center">
        <a href="{{ route('dashboard.orders') }}" class="text-white hover:text-white/80 mr-4">← Back to Orders</a>
        <h1 class="text-xl sm:text-2xl font-bold">Order Details - #{{ $order->id }}</h1>
    </div>
</div>

    <div class="grid md:grid-cols-2 gap-4 sm:gap-8 mb-4 sm:mb-8">
        <!-- Order Information -->
        <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
            <h2 class="text-lg sm:text-xl font-bold mb-4">Order Information</h2>

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
        <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
            <h2 class="text-lg sm:text-xl font-bold mb-4">Shipping Address</h2>

            <div class="text-gray-600">
                <p class="font-bold text-gray-800">{{ $order->user->name }}</p>
                @if(is_array($order->shipping_address))
                    @if(isset($order->shipping_address['address']))
                        <p>{{ $order->shipping_address['address'] }}</p>
                    @endif
                    @if(isset($order->shipping_address['phone']))
                        <p>Phone: {{ $order->shipping_address['phone'] }}</p>
                    @endif
                @else
                    <p>{{ $order->shipping_address }}</p>
                @endif
                <p>Email: {{ $order->user->email }}</p>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mb-4 sm:mb-8">
        <h2 class="text-lg sm:text-xl font-bold mb-4">Order Items</h2>

        @if($order->orderable_type === 'App\Models\Pooja' && $order->orderable)
            @php $items = is_array($order->items) ? $order->items : json_decode($order->items, true) ?? []; @endphp
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b pb-4">
                    <div class="flex items-center space-x-4">
                        @if(!empty($items) && isset($items[0]['image']) && $items[0]['image'])
                            <img src="{{ asset($items[0]['image']) }}" alt="{{ $order->orderable->name }}" class="w-20 h-20 rounded object-cover">
                        @else
                            <div class="w-20 h-20 bg-orange-500 rounded flex items-center justify-center">
                                <span class="text-white text-2xl">🕉️</span>
                            </div>
                        @endif
                        <div>
                            <h3 class="font-bold">{{ $order->orderable->name }}</h3>
                            <p class="text-gray-600">Type: {{ ucfirst($order->orderable->type) }}</p>
                            <p class="text-gray-600">Scheduled: {{ $order->orderable->scheduled_at }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold">{{ formatPrice($order->orderable->amount) }}</p>
                        <p class="text-sm px-2 py-1 rounded-full
                            @if($order->orderable->status == 'completed') bg-green-100 text-green-800
                            @elseif($order->orderable->status == 'booked') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($order->orderable->status) }}
                        </p>
                    </div>
                </div>
            </div>
        @else
            @php $items = is_array($order->items) ? $order->items : (json_decode($order->items, true) ?? []); @endphp
            @if(!empty($items))
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
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="mt-2">No items found in this order</p>
                </div>
            @endif
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
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
        <h2 class="text-lg sm:text-xl font-bold mb-4">Order Timeline</h2>

        <div class="space-y-4">
            @if($order->delivered_at)
            <div class="flex items-center space-x-4">
                <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                <div>
                    <p class="font-bold">Order Delivered</p>
                    <p class="text-gray-600 text-sm">{{ $order->delivered_at->format('M d, Y \a\t h:i A') }}</p>
                </div>
            </div>
            @endif

            @if($order->shipped_at)
            <div class="flex items-center space-x-4">
                <div class="w-4 h-4 bg-blue-500 rounded-full"></div>
                <div>
                    <p class="font-bold">Order Shipped</p>
                    <p class="text-gray-600 text-sm">{{ $order->shipped_at->format('M d, Y \a\t h:i A') }}</p>
                </div>
            </div>
            @endif

            @if($order->payment_status === 'paid')
            <div class="flex items-center space-x-4">
                <div class="w-4 h-4 bg-purple-500 rounded-full"></div>
                <div>
                    <p class="font-bold">Payment Confirmed</p>
                    <p class="text-gray-600 text-sm">{{ $order->updated_at->format('M d, Y \a\t h:i A') }}</p>
                </div>
            </div>
            @elseif($order->payment_status === 'pending' && $order->payment_method === 'cod')
            <div class="flex items-center space-x-4">
                <div class="w-4 h-4 bg-yellow-500 rounded-full"></div>
                <div>
                    <p class="font-bold">Payment on Delivery</p>
                    <p class="text-gray-600 text-sm">Pay when you receive the order</p>
                </div>
            </div>
            @endif

            @if($order->status === 'cancelled')
            <div class="flex items-center space-x-4">
                <div class="w-4 h-4 bg-red-500 rounded-full"></div>
                <div>
                    <p class="font-bold">Order Cancelled</p>
                    <p class="text-gray-600 text-sm">{{ $order->updated_at->format('M d, Y \a\t h:i A') }}</p>
                </div>
            </div>
            @endif

            <div class="flex items-center space-x-4">
                <div class="w-4 h-4 bg-gray-500 rounded-full"></div>
                <div>
                    <p class="font-bold">Order Placed</p>
                    <p class="text-gray-600 text-sm">{{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
