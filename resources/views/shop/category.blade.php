@extends('layouts.app')

@section('title', ucfirst($category) . ' - Shop')

@section('content')
<div class="bg-gradient-to-r from-indigo-900 to-purple-900 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-4">{{ ucfirst(str_replace('_', ' ', $category)) }}</h1>
        <p class="text-xl">Authentic {{ strtolower(str_replace('_', ' ', $category)) }} for spiritual growth</p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    @if($products->count() > 0)
        <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                @if($product->image)
                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-indigo-600 flex items-center justify-center">
                        <span class="text-white font-bold">{{ $product->name }}</span>
                    </div>
                @endif
                <div class="p-4">
                    <h3 class="font-bold mb-2">{{ $product->name }}</h3>
                    <p class="text-gray-600 text-sm mb-2">{{ Str::limit($product->description, 80) }}</p>
                    <div class="flex justify-between items-center">
                        <span class="text-xl font-bold text-indigo-600">{{ formatPrice($product->price) }}</span>
                        <a href="{{ route('product.show', ['id' => $product->id, 'slug' => Str::slug($product->name)]) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">View Details</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <h3 class="text-2xl font-bold text-gray-600 mb-4">No products found</h3>
            <p class="text-gray-500">We're working on adding more {{ strtolower(str_replace('_', ' ', $category)) }} products.</p>
            <a href="{{ route('shop.index') }}" class="mt-4 inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">Browse All Products</a>
        </div>
    @endif
</div>
@endsection