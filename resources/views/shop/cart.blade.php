@extends('layouts.app')

@section('title', 'Shopping Cart - AstroServices')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Shopping Cart</h1>
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(!empty($cart))
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                @php $total = 0; @endphp
                @foreach($cart as $id => $item)
                    @php $total += $item['price'] * $item['quantity']; @endphp
                    <div class="flex items-center justify-between border-b pb-4 mb-4">
                        <div class="flex items-center space-x-4">
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
                        <div class="text-right">
                            <p class="text-xl font-bold">{{ formatPrice($item['price'] * $item['quantity']) }}</p>
                            <form action="{{ route('cart.remove') }}" method="POST" class="mt-2">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $id }}">
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
                
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center">
                        <a href="{{ route('shop.index') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600">Continue Shopping</a>
                        <div class="text-right">
                            <div class="text-xl font-bold mb-4">Total: {{ formatPrice($total) }}</div>
                            @auth
                                <form action="{{ route('checkout') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">Proceed to Checkout</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">Login to Checkout</a>
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