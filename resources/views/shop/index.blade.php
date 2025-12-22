@extends('layouts.app')

@section('title', 'Shop - Sacred Products')

@section('content')
<div class="bg-gradient-to-r from-indigo-900 to-purple-900 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-4">Sacred Products Shop</h1>
        <p class="text-xl">Authentic spiritual items for your journey</p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
            </span>
        </div>
    @endif
    
    <!-- Categories -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold mb-8">Shop by Category</h2>
        <div class="grid md:grid-cols-3 lg:grid-cols-6 gap-6">
            <a href="{{ route('shop.category', 'gemstones') }}" class="bg-white p-6 rounded-lg shadow-lg text-center hover:shadow-xl transition">
                <div class="text-4xl mb-3">💎</div>
                <h3 class="font-bold">Gemstones</h3>
                <p class="text-sm text-gray-600">Ruby, Emerald, Sapphire</p>
            </a>
            <a href="{{ route('shop.category', 'rudraksha') }}" class="bg-white p-6 rounded-lg shadow-lg text-center hover:shadow-xl transition">
                <div class="text-4xl mb-3">📿</div>
                <h3 class="font-bold">Rudraksha</h3>
                <p class="text-sm text-gray-600">1-21 Mukhi varieties</p>
            </a>
            <a href="{{ route('shop.category', 'yantras') }}" class="bg-white p-6 rounded-lg shadow-lg text-center hover:shadow-xl transition">
                <div class="text-4xl mb-3">🔯</div>
                <h3 class="font-bold">Yantras</h3>
                <p class="text-sm text-gray-600">Sacred geometric designs</p>
            </a>
            <a href="{{ route('shop.category', 'crystals') }}" class="bg-white p-6 rounded-lg shadow-lg text-center hover:shadow-xl transition">
                <div class="text-4xl mb-3">💎</div>
                <h3 class="font-bold">Crystals</h3>
                <p class="text-sm text-gray-600">Healing crystal products</p>
            </a>
            <a href="{{ route('shop.category', 'pooja_samagri') }}" class="bg-white p-6 rounded-lg shadow-lg text-center hover:shadow-xl transition">
                <div class="text-4xl mb-3">🕯️</div>
                <h3 class="font-bold">Pooja Kits</h3>
                <p class="text-sm text-gray-600">Complete ritual sets</p>
            </a>
            <a href="{{ route('shop.category', 'books') }}" class="bg-white p-6 rounded-lg shadow-lg text-center hover:shadow-xl transition">
                <div class="text-4xl mb-3">📚</div>
                <h3 class="font-bold">Books</h3>
                <p class="text-sm text-gray-600">Astrology & spirituality</p>
            </a>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold mb-8">Featured Products</h2>
        <div class="grid md:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <a href="{{ route('product.show', [$product->id, $product->slug]) }}">
                        @if($product->image)
                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover hover:opacity-90 transition">
                        @else
                            <div class="w-full h-48 bg-indigo-600 flex items-center justify-center text-white text-lg font-bold hover:opacity-90 transition">
                                {{ $product->name }}
                            </div>
                        @endif
                    </a>
                    <div class="p-4">
                        <a href="{{ route('product.show', [$product->id, $product->slug]) }}" class="hover:text-indigo-600">
                            <h3 class="font-bold mb-2">{{ $product->name }}</h3>
                        </a>
                        <p class="text-gray-600 text-sm mb-2">{{ Str::limit($product->description, 50) }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-xl font-bold text-indigo-600">{{ formatPrice($product->price) }}</span>
                            <form action="{{ route('cart.add') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Gemstones Section -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold mb-8">Premium Gemstones</h2>
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">Why Choose Our Gemstones?</h3>
                    <ul class="space-y-2 text-gray-600">
                        <li>✓ Lab certified authentic stones</li>
                        <li>✓ Astrologically recommended cuts</li>
                        <li>✓ Energized by expert astrologers</li>
                        <li>✓ 30-day return guarantee</li>
                        <li>✓ Free consultation on selection</li>
                    </ul>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 border rounded">
                        <div class="text-2xl mb-2">💎</div>
                        <h4 class="font-bold">Ruby</h4>
                        <p class="text-sm text-gray-600">For Sun</p>
                    </div>
                    <div class="text-center p-4 border rounded">
                        <div class="text-2xl mb-2">💚</div>
                        <h4 class="font-bold">Emerald</h4>
                        <p class="text-sm text-gray-600">For Mercury</p>
                    </div>
                    <div class="text-center p-4 border rounded">
                        <div class="text-2xl mb-2">💛</div>
                        <h4 class="font-bold">Yellow Sapphire</h4>
                        <p class="text-sm text-gray-600">For Jupiter</p>
                    </div>
                    <div class="text-center p-4 border rounded">
                        <div class="text-2xl mb-2">💙</div>
                        <h4 class="font-bold">Blue Sapphire</h4>
                        <p class="text-sm text-gray-600">For Saturn</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Special Offers -->
    <section>
        <h2 class="text-3xl font-bold mb-8">Special Offers</h2>
        <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white p-8 rounded-lg">
            <div class="text-center">
                <h3 class="text-2xl font-bold mb-4">Navratri Special - 20% Off</h3>
                <p class="mb-6">Get 20% discount on all Pooja Samagri Kits and Yantras</p>
                <a href="{{ route('shop.category', 'pooja_samagri') }}" class="bg-white text-orange-500 px-8 py-3 rounded-lg font-bold hover:bg-gray-100">Shop Now</a>
            </div>
        </div>
    </section>
</div>
@endsection