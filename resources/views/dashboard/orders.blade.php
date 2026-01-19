@extends('dashboard.layout')

@section('title', __('messages.my_orders') . ' - ' . __('messages.dashboard'))

@section('dashboard-content')
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h1 class="text-3xl font-bold text-gray-900">{{ __('messages.my_orders') }}</h1>
</div>

<div class="flex justify-end mb-6">
    <a href="{{ route('shop.index') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all">
        <i class="fas fa-shopping-bag mr-2"></i>{{ __('messages.shop') }}
    </a>
</div>

<!-- Status Filter -->
<div class="mb-6">
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('dashboard.orders') }}" class="px-4 py-2 rounded-lg transition-all {{ !request('status') || request('status') == 'all' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">All</a>
        <a href="{{ route('dashboard.orders', ['status' => 'pending']) }}" class="px-4 py-2 rounded-lg transition-all {{ request('status') == 'pending' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">{{ __('messages.pending') }}</a>
        <a href="{{ route('dashboard.orders', ['status' => 'processing']) }}" class="px-4 py-2 rounded-lg transition-all {{ request('status') == 'processing' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">{{ __('messages.processing') }}</a>
        <a href="{{ route('dashboard.orders', ['status' => 'shipped']) }}" class="px-4 py-2 rounded-lg transition-all {{ request('status') == 'shipped' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">{{ __('messages.shipped') }}</a>
        <a href="{{ route('dashboard.orders', ['status' => 'delivered']) }}" class="px-4 py-2 rounded-lg transition-all {{ request('status') == 'delivered' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">{{ __('messages.delivered') }}</a>
    </div>
</div>

@if($orders->count() > 0)
    <!-- Orders List -->
    <div class="grid gap-6">
        @foreach($orders as $order)
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 border border-gray-100">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ __('messages.order') }} #{{ $order->id }}</h3>
                        <p class="text-gray-500 text-sm mt-1"><i class="fas fa-calendar mr-1"></i>{{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                    <span class="px-4 py-2 rounded-full text-sm font-semibold shadow-sm
                        @if($order->status == 'delivered') bg-gradient-to-r from-green-100 to-green-200 text-green-800
                        @elseif($order->status == 'shipped') bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800
                        @elseif($order->status == 'processing') bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800
                        @elseif($order->status == 'cancelled') bg-gradient-to-r from-red-100 to-red-200 text-red-800
                        @else bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 @endif">
                        {{ __('messages.' . $order->status) }}
                    </span>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4 mb-4 bg-gray-50 rounded-lg p-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1"><i class="fas fa-dollar-sign mr-1"></i>{{ __('messages.total') }}</p>
                        <p class="font-bold text-indigo-600 text-lg">{{ formatPrice($order->total_amount) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1"><i class="fas fa-credit-card mr-1"></i>{{ __('messages.payment') }} {{ __('messages.status') }}</p>
                        <p class="font-semibold text-gray-900">{{ ucfirst($order->payment_status ?? 'pending') }}</p>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('dashboard.order.details', $order->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all"><i class="fas fa-eye mr-1"></i>{{ __('messages.view_details') }}</a>
                    @if($order->status == 'delivered')
                        <a href="{{ route('dashboard.order.invoice', $order->id) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-md hover:shadow-lg transition-all"><i class="fas fa-download mr-1"></i>{{ __('messages.download') }}</a>
                    @elseif($order->status == 'processing')
                        <form action="{{ route('dashboard.order.cancel', $order->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 shadow-md hover:shadow-lg transition-all"><i class="fas fa-times mr-1"></i>{{ __('messages.cancel') }}</button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@else
    <!-- Empty State -->
    <div class="text-center py-16 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl">
        <div class="w-20 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-shopping-bag text-3xl text-indigo-600"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('messages.no_items_found') }}</h3>
        <p class="text-gray-600 mb-6">Start shopping to see your orders here</p>
        <a href="{{ route('shop.index') }}" class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all">
            <i class="fas fa-shopping-bag mr-2"></i>{{ __('messages.shop') }}
        </a>
    </div>
@endif
@endsection
