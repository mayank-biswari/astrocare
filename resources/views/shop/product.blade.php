@extends('layouts.app')

@section('title', $product->name . ' - Buy Online at AstroServices')

@push('meta')
<meta name="description" content="{{ Str::limit($product->description, 160) }} Buy authentic {{ $product->name }} online at AstroServices with free shipping.">
<meta name="keywords" content="{{ $product->name }}, {{ $product->category }}, astrology, spiritual, buy online">
<meta property="og:title" content="{{ $product->name }} - AstroServices">
<meta property="og:description" content="{{ Str::limit($product->description, 160) }}">
<meta property="og:type" content="product">
<meta property="product:price:amount" content="{{ $product->price }}">
<meta property="product:price:currency" content="{{ $product->currency }}">
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif
    <div class="grid md:grid-cols-2 gap-12">
        <!-- Product Image -->
        <div>
            @if($product->image)
                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full rounded-lg shadow-lg">
            @else
                <div class="w-full h-96 bg-indigo-600 rounded-lg shadow-lg flex items-center justify-center text-white text-2xl font-bold">
                    {{ $product->name }}
                </div>
            @endif
        </div>

        <!-- Product Details -->
        <div>
            <nav class="text-sm text-gray-500 mb-4">
                <a href="{{ route('shop.index') }}" class="hover:text-indigo-600">Shop</a> > 
                <a href="{{ route('shop.category', $product->category) }}" class="hover:text-indigo-600">{{ ucfirst($product->category) }}</a> > 
                {{ $product->name }}
            </nav>

            <h1 class="text-3xl font-bold mb-4">{{ $product->name }}</h1>
            
            <div class="text-3xl font-bold text-indigo-600 mb-6">{{ formatPrice($product->price) }}</div>

            <div class="mb-6">
                <h3 class="text-lg font-bold mb-2">Description</h3>
                <p class="text-gray-600">{{ $product->description }}</p>
            </div>

            @if($product->specifications)
            <div class="mb-6">
                <h3 class="text-lg font-bold mb-2">Specifications</h3>
                <ul class="text-gray-600 space-y-1">
                    @foreach($product->specifications as $spec)
                        <li>• {{ $spec }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="mb-6">
                <div class="flex items-center space-x-4 mb-4">
                    <span class="text-sm text-gray-600">Quantity:</span>
                    <select id="quantity-select" class="border border-gray-300 rounded px-3 py-2" onchange="updateQuantity()">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>

                @if($product->show_stock)
                    @if($product->stock_quantity > 0)
                        <p class="text-green-600 text-sm mb-4">✓ In Stock ({{ $product->stock_quantity }} available)</p>
                    @else
                        <p class="text-red-600 text-sm mb-4">✗ Out of Stock</p>
                    @endif
                @endif
            </div>

            <div class="space-y-4">
                @if($product->stock_quantity > 0)
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1" id="cart-quantity">
                        <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg font-bold hover:bg-indigo-700">
                            Add to Cart
                        </button>
                    </form>
                    @auth
                        <form action="{{ route('checkout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1" id="buy-quantity">
                            <input type="hidden" name="buy_now" value="1">
                            <button type="submit" class="w-full border border-indigo-600 text-indigo-600 py-3 px-6 rounded-lg font-bold hover:bg-indigo-50">
                                Buy Now
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="w-full border border-indigo-600 text-indigo-600 py-3 px-6 rounded-lg font-bold hover:bg-indigo-50 block text-center">
                            Login to Buy Now
                        </a>
                    @endauth
                @else
                    <button class="w-full bg-gray-400 text-white py-3 px-6 rounded-lg font-bold cursor-not-allowed" disabled>
                        Out of Stock
                    </button>
                @endif
            </div>

            @if($product->features)
            <div class="mt-8 border-t pt-6">
                <h3 class="text-lg font-bold mb-4">Product Features</h3>
                <div class="space-y-2 text-sm text-gray-600">
                    @foreach($product->features as $feature)
                        <p>✓ {{ $feature }}</p>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function updateQuantity() {
    const quantity = document.getElementById('quantity-select').value;
    document.getElementById('cart-quantity').value = quantity;
    document.getElementById('buy-quantity').value = quantity;
}
</script>
@endsection