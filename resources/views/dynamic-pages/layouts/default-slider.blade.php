<div class="slider-container mb-8">
    <div class="dynamic-slider slider-{{ $loopIndex }}">
        @foreach($items as $item)
            @php
                $isClickable = $section['make_clickable'] ?? true;
                $showReadMore = $section['show_read_more'] ?? true;
                $readMoreText = $section['read_more_text'] ?? (isset($item->name) ? 'View Details' : 'Read More');
                $itemUrl = isset($item->name) ? route('product.show', ['id' => $item->id, 'slug' => $item->slug]) : route('cms.show', $item->slug);
            @endphp
            <div class="slide px-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden {{ $isClickable ? 'cursor-pointer hover:shadow-lg' : '' }}" @if($isClickable) onclick="location.href='{{ $itemUrl }}'" @endif>
                    @if($item->image ?? $item->image)
                        <img src="{{ $item->image ?? asset('storage/' . $item->image) }}"
                             alt="{{ $item->name ?? $item->title }}"
                             class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400">{{ $item->name ?? $item->title }}</span>
                        </div>
                    @endif
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-2">{{ $item->name ?? $item->title }}</h3>
                        <p class="text-gray-600 text-sm mb-2">{{ Str::limit($item->description ?? strip_tags($item->body), 100) }}</p>
                        @if($showReadMore && !$isClickable)
                            <a href="{{ $itemUrl }}" class="inline-block bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 text-sm">
                                {{ $readMoreText }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
