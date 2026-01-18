@php
    $gridClasses = $section['grid_classes'] ?? 'grid md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8';
@endphp

<div class="{{ $gridClasses }}">
    @foreach($items as $item)
        @php
            $isClickable = $section['make_clickable'] ?? true;
            $showReadMore = $section['show_read_more'] ?? true;
            $readMoreText = $section['read_more_text'] ?? 'Read More';
            $itemUrl = $item->custom_fields['product_link'];
        @endphp
        <a href="{{ $itemUrl }}" class="bg-white p-6 rounded-lg shadow text-center hover:shadow-lg transition">
            <div class="text-3xl mb-3 flex justify-center">
                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="h-20" />
            </div>
            <h4 class="font-bold mb-2">{{ $item->title }}</h4>
            <p class="text-sm text-gray-600"></p>{{ Str::limit(strip_tags($item->body), 150) }}<p></p>
        </a>
    @endforeach
</div>
