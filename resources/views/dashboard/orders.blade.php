@extends('dashboard.layout')

@section('title', __('messages.my_orders') . ' - ' . __('messages.dashboard'))

@section('dashboard-content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm mb-4 sm:mb-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <h1 class="text-xl sm:text-2xl font-bold">{{ __('messages.my_orders') }}</h1>
    <p class="text-white/90 mt-1 text-sm sm:text-base">Track and manage your shop orders</p>
</div>

<div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 sm:mb-6 gap-3">
        <h2 class="text-lg sm:text-xl font-bold">Orders History</h2>
        <div class="flex gap-2">
            <select onchange="window.location.href='{{ route('dashboard.orders') }}?status='+this.value" class="px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm sm:text-base w-full sm:w-auto">
                <option value="all" {{ !request('status') || request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>{{ __('messages.processing') }}</option>
                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>{{ __('messages.shipped') }}</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>{{ __('messages.delivered') }}</option>
            </select>
            <a href="{{ route('shop.index') }}" class="px-3 sm:px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm sm:text-base whitespace-nowrap">
                <i class="fas fa-shopping-bag mr-1"></i><span class="hidden sm:inline">Shop Now</span><span class="sm:inline hidden">Shop</span>
            </a>
        </div>
    </div>

    @if($orders->count() > 0)
        <div class="space-y-3 sm:space-y-4">
            @foreach($orders as $order)
                <div class="border border-gray-200 rounded-lg p-3 sm:p-4 hover:shadow-md transition">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-2">
                                <span class="px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium
                                    {{ $order->status == 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $order->status == 'shipped' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $order->status == 'processing' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $order->status == 'pending' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $order->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ __('messages.' . $order->status) }}
                                </span>
                                <span class="text-xs sm:text-sm text-gray-500">Order #{{ $order->id }}</span>
                            </div>
                            <p class="text-sm sm:text-base text-gray-700 mb-2">{{ __('messages.payment') }}: {{ ucfirst($order->payment_status ?? 'pending') }}</p>
                            <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-xs sm:text-sm text-gray-500">
                                <span><i class="fas fa-calendar mr-1"></i>{{ $order->created_at->format('M d, Y') }}</span>
                                <span><i class="fas fa-rupee-sign mr-1"></i>{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 w-full sm:w-auto">
                            <a href="{{ route('dashboard.order.details', $order->id) }}" class="px-3 sm:px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm sm:text-base text-center">
                                View Details
                            </a>
                            @if($order->status == 'delivered')
                                <a href="{{ route('dashboard.order.invoice', $order->id) }}" class="px-3 sm:px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm sm:text-base text-center">
                                    Download Invoice
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8 sm:py-12 px-4">
            <i class="fas fa-shopping-bag text-5xl sm:text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-base sm:text-lg mb-4">{{ __('messages.no_items_found') }}</p>
            <a href="{{ route('shop.index') }}" class="inline-block px-4 sm:px-6 py-2 sm:py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm sm:text-base">
                <i class="fas fa-shopping-bag mr-2"></i>{{ __('messages.shop') }}
            </a>
        </div>
    @endif
</div>
@endsection
