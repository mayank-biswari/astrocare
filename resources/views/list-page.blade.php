@extends('layouts.app')

@section('title', $list->page_title ?: $list->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">{{ $list->page_title ?: $list->name }}</h1>
            @if($list->page_description)
                <p class="text-lg text-gray-600">{{ $list->page_description }}</p>
            @endif
        </div>

        @if($items->count() > 0)
            @if($list->type === 'products')
                <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($items as $item)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        @if($item->image)
                            <img src="{{ $item->image }}" alt="{{ $item->name }}" class="w-full h-48 object-cover">
                        @endif
                        <div class="p-4">
                            <h3 class="font-bold text-lg mb-2">{{ $item->name }}</h3>
                            <p class="text-gray-600 text-sm mb-3">{{ Str::limit($item->description, 100) }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-indigo-600">{{ formatPrice($item->price) }}</span>
                                <a href="{{ route('product.show', ['id' => $item->id, 'slug' => $item->slug]) }}" 
                                   class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($items as $item)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        @if($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="w-full h-48 object-cover">
                        @endif
                        <div class="p-6">
                            <h3 class="font-bold text-xl mb-3">{{ $item->title }}</h3>
                            <p class="text-gray-600 mb-4">{{ Str::limit(strip_tags($item->body), 150) }}</p>
                            <a href="{{ route('cms.show', $item->slug) }}" 
                               class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                                Read More
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
            
            <div class="mt-8">
                {{ $items->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">📋</div>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No Items Found</h3>
                <p class="text-gray-500">This list doesn't contain any items at the moment.</p>
            </div>
        @endif
    </div>
</div>
@endsection