@extends('layouts.app')

@section('title', 'Blogs')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-4xl font-bold text-center mb-8">Our Blogs</h1>
        
        @if($blogs->count() > 0)
            <div class="grid md:grid-cols-3 gap-8">
                @foreach($blogs as $blog)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                    @if($blog->image)
                        <div class="h-48 overflow-hidden">
                            <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}" class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-3">{{ $blog->title }}</h3>
                        <p class="text-gray-600 mb-4">{{ Str::limit(strip_tags($blog->body), 120) }}</p>
                        <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
                            <span>{{ $blog->created_at->format('M d, Y') }}</span>
                            @if($blog->pageType && $blog->pageType->fields_config['show_author'] ?? false)
                                <span>By Admin</span>
                            @endif
                        </div>
                        <a href="{{ route('cms.show', $blog->slug) }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition duration-200">
                            Read More
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="mt-8 flex justify-center">
                {{ $blogs->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📝</div>
                <h2 class="text-2xl font-bold text-gray-600 mb-2">No Blogs Yet</h2>
                <p class="text-gray-500">Check back later for interesting articles and insights.</p>
            </div>
        @endif
    </div>
</div>
@endsection