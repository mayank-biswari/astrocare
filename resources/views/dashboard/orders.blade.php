@extends('layouts.app')

@section('title', __('messages.my_orders') . ' - ' . __('messages.dashboard'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">{{ __('messages.my_orders') }}</h1>
        <a href="{{ route('shop.index') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
            {{ __('messages.shop') }}
        </a>
    </div>

    <!-- Status Filter -->
    <div class="mb-6">
        <div class="flex space-x-4">
            <a href="{{ route('dashboard.orders') }}" class="px-4 py-2 rounded-lg {{ !request('status') || request('status') == 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">All</a>
            <a href="{{ route('dashboard.orders', ['status' => 'pending']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'pending' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">{{ __('messages.pending') }}</a>
            <a href="{{ route('dashboard.orders', ['status' => 'processing']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'processing' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">{{ __('messages.processing') }}</a>
            <a href="{{ route('dashboard.orders', ['status' => 'shipped']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'shipped' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">{{ __('messages.shipped') }}</a>
            <a href="{{ route('dashboard.orders', ['status' => 'delivered']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'delivered' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">{{ __('messages.delivered') }}</a>
        </div>
    </div>

    @if($orders->count() > 0)
        <!-- Orders List -->
        <div class="grid gap-6">
            @foreach($orders as $order)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold">{{ __('messages.order') }} #{{ $order->id }}</h3>
                            <p class="text-gray-600">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm
                            @if($order->status == 'delivered') bg-green-100 text-green-800
                            @elseif($order->status == 'shipped') bg-blue-100 text-blue-800
                            @elseif($order->status == 'processing') bg-yellow-100 text-yellow-800
                            @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ __('messages.' . $order->status) }}
                        </span>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">{{ __('messages.total') }}</p>
                            <p class="font-medium text-indigo-600">{{ formatPrice($order->total_amount) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('messages.payment') }} {{ __('messages.status') }}</p>
                            <p class="font-medium">{{ ucfirst($order->payment_status ?? 'pending') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex space-x-3">
                        <a href="{{ route('dashboard.order.details', $order->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">{{ __('messages.view_details') }}</a>
                        @if($order->status == 'delivered')
                            <a href="{{ route('dashboard.order.invoice', $order->id) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">{{ __('messages.download') }}</a>
                        @elseif($order->status == 'processing')
                            <form action="{{ route('dashboard.order.cancel', $order->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">{{ __('messages.cancel') }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('messages.no_items_found') }}</h3>
            <a href="{{ route('shop.index') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
                {{ __('messages.shop') }}
            </a>
        </div>
    @endif
</div>
@endsection