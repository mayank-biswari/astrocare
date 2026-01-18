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
                <div class="bg-white p-6 rounded-lg shadow-lg {{ $isClickable ? 'cursor-pointer hover:shadow-lg' : '' }}" @if($isClickable) onclick="location.href='{{ $itemUrl }}'" @endif>
                    <div class="flex items-center mb-4">
                        @if($item->custom_fields['testimonial_rating'] ?? null)
                            <div class="text-yellow-500 mr-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= ($item->custom_fields['testimonial_rating'] ?? 0))
                                        ★
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </div>
                        @endif
                    </div>
                    <p class="text-gray-600 mb-4 italic">"{{ Str::limit(strip_tags($item->body), 150) }}"</p>
                    <div class="border-t pt-4">
                        <h4 class="font-bold">{{ $item->custom_fields['client_name'] ?? 'Anonymous' }}</h4>
                        @if($item->custom_fields['client_location'] ?? null)
                            <p class="text-sm text-gray-500">{{ $item->custom_fields['client_location'] }}</p>
                        @endif
                        @if($item->custom_fields['service_type'] ?? null)
                            <p class="text-sm text-indigo-600">{{ $item->custom_fields['service_type'] }} Service</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
