@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container mx-auto px-4 py-4 sm:py-8">
    <h1 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Shopping Cart</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(!empty($cart))
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-3 sm:p-6">
                @php $total = 0; @endphp
                @foreach($cart as $id => $item)
                    @php $total += $item['price'] * $item['quantity']; @endphp
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 border-b pb-4 mb-4">
                        <!-- Left side: Product image and name -->
                        <div class="flex items-center space-x-3 sm:space-x-4 flex-1 min-w-0">
                            @if(isset($item['image']) && $item['image'])
                                @if(isset($item['type']) && ($item['type'] === 'cms_page' || $item['type'] === 'cms_page_variant'))
                                    <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-14 h-14 sm:w-20 sm:h-20 rounded object-cover flex-shrink-0">
                                @else
                                    <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="w-14 h-14 sm:w-20 sm:h-20 rounded object-cover flex-shrink-0">
                                @endif
                            @else
                                <div class="w-14 h-14 sm:w-20 sm:h-20 bg-indigo-600 rounded flex items-center justify-center flex-shrink-0">
                                    <span class="text-white text-xs font-bold">{{ substr($item['name'], 0, 3) }}</span>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <h3 class="font-bold text-sm sm:text-base text-gray-800 truncate">{{ $item['name'] }}</h3>
                                <p class="text-indigo-600 font-medium text-xs sm:text-sm mt-1">
                                    @if(isset($item['currency']) && $item['currency'] === currencyCode())
                                        {{ currencySymbol() }}{{ number_format($item['price'], 2) }} each
                                    @else
                                        {{ formatPrice($item['price']) }} each
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Right side: Quantity, total price, remove -->
                        <div class="flex items-center gap-3 sm:gap-6">
                            <!-- Quantity controls -->
                            <form action="{{ route('cart.update') }}" method="POST" class="flex items-center gap-1">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $id }}">
                                <button type="submit" name="action" value="decrease" class="bg-gray-200 hover:bg-gray-300 w-7 h-7 sm:w-8 sm:h-8 rounded flex items-center justify-center text-gray-700 text-sm">-</button>
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="{{ $item['min_quantity'] ?? 1 }}" step="{{ $item['quantity_step'] ?? 1 }}" class="w-10 sm:w-12 text-center border rounded px-1 py-1 text-xs sm:text-sm" readonly>
                                <button type="submit" name="action" value="increase" class="bg-gray-200 hover:bg-gray-300 w-7 h-7 sm:w-8 sm:h-8 rounded flex items-center justify-center text-gray-700 text-sm">+</button>
                                @if(isset($item['quantity_unit']))
                                <span class="text-xs sm:text-sm text-gray-600">{{ $item['quantity_unit'] }}</span>
                                @endif
                            </form>

                            <!-- Item total -->
                            <p class="text-sm sm:text-lg font-bold text-gray-800 min-w-[60px] sm:min-w-[80px] text-right">
                                @if(isset($item['currency']) && $item['currency'] === currencyCode())
                                    {{ currencySymbol() }}{{ number_format($item['price'] * $item['quantity'], 2) }}
                                @else
                                    {{ formatPrice($item['price'] * $item['quantity']) }}
                                @endif
                            </p>

                            <!-- Remove button (trash icon) -->
                            <form action="{{ route('cart.remove') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $id }}">
                                <button type="submit" class="text-red-500 hover:text-red-700 transition-colors p-1 sm:p-2" title="Remove item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach

                <div class="border-t pt-4">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 sm:gap-4">
                        <a href="{{ route('shop.index') }}" class="w-full sm:w-auto text-center bg-gray-500 text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg hover:bg-gray-600 text-sm sm:text-base">Continue Shopping</a>
                        <div class="text-center sm:text-right w-full sm:w-auto">
                            <div class="text-lg sm:text-xl font-bold mb-3 sm:mb-4">Total:
                                @php
                                    $displayTotal = 0;
                                    foreach($cart as $item) {
                                        $displayTotal += $item['price'] * $item['quantity'];
                                    }
                                @endphp
                                {{ currencySymbol() }}{{ number_format($displayTotal, 2) }}
                            </div>
                            @auth
                                <form action="{{ route('checkout') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="w-full sm:w-auto bg-indigo-600 text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg hover:bg-indigo-700 text-sm sm:text-base">Proceed to Checkout</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="block w-full sm:w-auto sm:inline-block text-center bg-indigo-600 text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg hover:bg-indigo-700 text-sm sm:text-base">Login to Checkout</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <h3 class="text-2xl font-bold text-gray-600 mb-4">Your cart is empty</h3>
            <p class="text-gray-500 mb-6">Add some products to get started!</p>
            <a href="{{ route('shop.index') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">Start Shopping</a>
        </div>
    @endif
</div>
@endsection
