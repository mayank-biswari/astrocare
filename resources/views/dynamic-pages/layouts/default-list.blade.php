<div class="space-y-4 mb-8">
    @foreach($items as $item)
        @php
            $isClickable = $section['make_clickable'] ?? true;
            $showReadMore = $section['show_read_more'] ?? true;
            $readMoreText = $section['read_more_text'] ?? (isset($item->name) ? 'View Details' : 'Read More');
            $itemUrl = isset($item->name) ? route('product.show', ['id' => $item->id, 'slug' => $item->slug]) : route('cms.show', $item->slug);
        @endphp
        <div class="bg-white rounded-lg shadow-md p-6 flex items-center space-x-4 {{ $isClickable ? 'cursor-pointer hover:shadow-lg' : '' }}" @if($isClickable) onclick="location.href='{{ $itemUrl }}'" @endif>
            @if($item->image ?? $item->image)
                <img src="{{ $item->image ?? asset('storage/' . $item->image) }}"
                     alt="{{ $item->name ?? $item->title }}"
                     class="w-16 h-16 object-cover rounded">
            @else
                <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                    <span class="text-gray-400">{{ $item->name ?? $item->title }}</span>
                </div>
            @endif
            <div class="flex-1">
                <h3 class="font-bold text-lg">{{ $item->name ?? $item->title }}</h3>
                <p class="text-gray-600">{{ Str::limit($item->description ?? strip_tags($item->body), 100) }}</p>
            </div>
            <div class="text-right">
                @if(isset($item->price))
                    <div class="text-xl font-bold text-indigo-600 mb-2">{{ formatPrice($item->price) }}</div>
                @endif
                @if($showReadMore && !$isClickable)
                    <a href="{{ $itemUrl }}" class="inline-block bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 text-sm">
                        {{ $readMoreText }}
                    </a>
                @endif
            </div>
        </div>
    @endforeach
</div>
