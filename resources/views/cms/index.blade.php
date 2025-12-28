@extends('layouts.app')

@section('title', 'Pages')

@section('content')
<div class="bg-gradient-to-r from-indigo-900 to-purple-900 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-4">Pages</h1>
        <p class="text-xl">Explore our content and resources</p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($pages as $page)
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            @if($page->image)
                <img src="{{ asset('storage/' . $page->image) }}" alt="{{ $page->title }}" class="w-full h-48 object-cover">
            @else
                <div class="w-full h-48 bg-indigo-600 flex items-center justify-center">
                    <span class="text-white text-xl font-bold">{{ substr($page->title, 0, 2) }}</span>
                </div>
            @endif
            
            <div class="p-6">
                <h3 class="text-xl font-bold mb-2">{{ $page->title }}</h3>
                <p class="text-gray-600 mb-4">{{ Str::limit(strip_tags($page->body), 100) }}</p>
                
                <div class="flex justify-between items-center mb-4">
                    @if($page->rating > 0)
                        <div class="flex items-center">
                            <div class="text-yellow-500">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $page->rating)
                                        ★
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </div>
                            <span class="text-sm text-gray-600 ml-2">({{ $page->rating_count }})</span>
                        </div>
                    @endif
                    <span class="text-sm text-gray-500">{{ $page->created_at->format('M d, Y') }}</span>
                </div>
                
                <a href="{{ route('cms.show', $page->slug) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Read More
                </a>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="mt-8">
        {{ $pages->links() }}
    </div>
</div>
@endsection