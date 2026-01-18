<div class="grid md:grid-cols-3 gap-8 mb-8">
    @foreach($items as $item)
        <div class="bg-white p-6 rounded-lg shadow-lg">
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
    @endforeach
</div>
