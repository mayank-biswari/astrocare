@php
    $gridClasses = $section['grid_classes'] ?? 'grid md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8';
@endphp

<div class="{{ $gridClasses }}">
    @foreach($items as $item)
        @php
            $isClickable = $section['make_clickable'] ?? true;
            $showReadMore = $section['show_read_more'] ?? true;
            $readMoreText = $section['read_more_text'] ?? 'Read More';
            $itemUrl = $item->custom_fields["service_link"];
        @endphp
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition {{ $isClickable ? 'cursor-pointer' : '' }}" @if($isClickable) onclick="location.href='{{ $itemUrl }}'" @endif>
            @if($item->image)
                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="w-full h-48 object-cover">
            @else
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                    <span class="text-gray-400">{{ $item->title }}</span>
                </div>
            @endif
            <div class="p-6">
                <h3 class="font-bold text-xl mb-3">{{ $item->title }}</h3>
                <p class="text-gray-600 mb-4">{{ Str::limit(strip_tags($item->body), 150) }}</p>
                @if($showReadMore && !$isClickable)
                    <a href="{{ $itemUrl }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        {{ $readMoreText }}
                    </a>
                @endif
            </div>
        </div>
    @endforeach
</div>
