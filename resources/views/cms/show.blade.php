@extends('layouts.app')

@section('title', $page->meta_title ?: $page->title)
@section('meta_description', $page->meta_description)
@section('meta_keywords', $page->meta_keywords)

@section('content')
<div class="container mx-auto px-4 py-8">
    <article class="max-w-4xl mx-auto">
        @if($page->image)
            <img src="{{ asset('storage/' . $page->image) }}" alt="{{ $page->title }}" class="w-full h-64 object-cover rounded-lg mb-8">
        @endif
        
        <header class="mb-8">
            <h1 class="text-4xl font-bold mb-4">{{ $page->title }}</h1>
            <div class="flex items-center justify-between text-gray-600">
                @if($page->pageType && $page->pageType->fields_config['show_posted_date'] ?? false)
                    <span>{{ $page->created_at->format('F d, Y') }}</span>
                @endif
                @if($page->pageType && $page->pageType->fields_config['show_rating'] ?? false && $page->rating > 0)
                    <div class="flex items-center">
                        <div class="text-yellow-500 mr-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $page->rating)
                                    ★
                                @else
                                    ☆
                                @endif
                            @endfor
                        </div>
                        <span>({{ number_format($page->rating, 1) }}/5 - {{ $page->rating_count }} reviews)</span>
                    </div>
                @endif
            </div>
            
            <!-- Custom Fields Display -->
            @if($page->custom_fields && $page->pageType)
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    @foreach($page->pageType->fields_config['custom_fields'] ?? [] as $fieldConfig)
                        @if(isset($page->custom_fields[$fieldConfig['name']]))
                            <div class="mb-2">
                                <span class="font-semibold text-gray-700">{{ $fieldConfig['label'] }}:</span>
                                <span class="text-gray-600 ml-2">{{ $page->custom_fields[$fieldConfig['name']] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </header>
        
        <div class="prose max-w-none mb-12">
            {!! $page->body !!}
        </div>
    </article>
    
    @if($page->pageType && $page->pageType->fields_config['show_comments'] ?? $page->allow_comments)
        <!-- Comments Section -->
        <div class="max-w-4xl mx-auto">
            <h3 class="text-2xl font-bold mb-6">Comments & Reviews</h3>
            
            <!-- Comment Form -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h4 class="text-lg font-bold mb-4">Leave a Comment</h4>
                
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                
                <form action="{{ route('cms.comment.store', $page->slug) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating (Optional)</label>
                        <select name="rating" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="" {{ old('rating') == '' ? 'selected' : '' }}>No Rating</option>
                            <option value="5" {{ old('rating') == '5' ? 'selected' : '' }}>★★★★★ (5/5)</option>
                            <option value="4" {{ old('rating') == '4' ? 'selected' : '' }}>★★★★☆ (4/5)</option>
                            <option value="3" {{ old('rating') == '3' ? 'selected' : '' }}>★★★☆☆ (3/5)</option>
                            <option value="2" {{ old('rating') == '2' ? 'selected' : '' }}>★★☆☆☆ (2/5)</option>
                            <option value="1" {{ old('rating') == '1' ? 'selected' : '' }}>★☆☆☆☆ (1/5)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Comment</label>
                        <textarea name="comment" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('comment') }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Security Check</label>
                        <div class="mb-2">
                            <img src="{{ route('captcha') }}" alt="captcha" class="border rounded" onclick="this.src='{{ route('captcha') }}?'+Math.random()" style="cursor: pointer;" title="Click to refresh">
                        </div>
                        <input type="text" name="captcha" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="Enter the characters shown above">
                        <p class="text-sm text-gray-600 mt-1">Click the image to refresh if hard to read</p>
                    </div>
                    
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                        Submit Comment
                    </button>
                </form>
            </div>
            
            <!-- Comments List -->
            @if($comments->count() > 0)
                <div class="space-y-6">
                    @foreach($comments as $comment)
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h5 class="font-bold">{{ $comment->name }}</h5>
                                    <span class="text-sm text-gray-600">{{ $comment->created_at->format('M d, Y') }}</span>
                                </div>
                                @if($comment->rating)
                                    <div class="text-yellow-500">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $comment->rating)
                                                ★
                                            @else
                                                ☆
                                            @endif
                                        @endfor
                                    </div>
                                @endif
                            </div>
                            <p class="text-gray-700">{{ $comment->comment }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600 text-center py-8">No comments yet. Be the first to comment!</p>
            @endif
        </div>
    @endif
</div>
@endsection