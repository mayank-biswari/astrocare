{{-- Expert Card Template --}}
@php
    $gridClasses = $section['grid_classes'] ?? 'grid md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8';
@endphp

<div class="{{ $gridClasses }}">
    @foreach ($items as $item)
        @php
            $fields = $item->custom_fields ?? [];
            $status = $fields['status'] ?? 'offline';
            $rating = $fields['rating'] ?? 0;
            $experience = $fields['experience'] ?? 0;
            $languages = $fields['languages'] ?? '';
            $consultations = $fields['consultations'] ?? 0;
            $expertise = $fields['expertise'] ?? '';
            $languageList = array_filter(array_map('trim', explode(',', $languages)));
            $expertiseList = array_filter(array_map('trim', explode(',', $expertise)));
            $itemUrl = route('cms.show', ['slug' => $item->slug]);
            
            $product = $item->product;
            $currentCurrency = session('currency', \App\Models\Currency::getDefaultCurrency()->code);
            $callVariant = $product?->variants->where('is_active', true)->first(fn($v) => stripos($v->name, 'call') !== false);
            $chatVariant = $product?->variants->where('is_active', true)->first(fn($v) => stripos($v->name, 'chat') !== false);
        @endphp
        
        <div class="bg-white border rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow cursor-pointer" onclick="location.href='{{ $itemUrl }}'">
            <div class="overflow-hidden relative">
                <ul class="list-none flex mb-0 p-0 w-full">
                    <li class="w-24 relative flex-shrink-0 mr-3">
                        <div class="relative">
                            <img src="{{ asset('storage/' . $item->image) }}" class="w-20 h-20 rounded-full object-cover border-2 {{ $status === 'online' ? 'border-green-400' : 'border-gray-200' }} hover:border-orange-400 transition-colors" loading="lazy" alt="{{ $item->title }}">
                            <div class="absolute -top-0.5 -right-0.5 w-3 h-3 rounded-full {{ $status === 'online' ? 'bg-green-500' : ($status === 'busy' ? 'bg-yellow-500' : 'bg-gray-400') }} border-2 border-white" title="{{ ucfirst($status) }}"></div>
                        </div>
                    </li>
                    
                    <li class="flex-1 min-w-0">
                        <h3 class="font-semibold text-base truncate hover:text-orange-600 transition-colors">{{ $item->title }}</h3>
                        <div class="space-y-1">
                            @if($expertiseList)<p class="text-sm text-gray-700">{{ implode(', ', array_slice($expertiseList, 0, 2)) }}</p>@endif
                            @if($languageList)<p class="text-sm text-gray-600">{{ implode(', ', $languageList) }}</p>@endif
                            @if($experience)<p class="text-sm text-gray-700">Exp: {{ $experience }} Years</p>@endif
                            
                            @if($callVariant)
                                @php
                                    if ($callVariant->currency_prices && isset($callVariant->currency_prices[$currentCurrency])) {
                                        $vPrice = $callVariant->currency_prices[$currentCurrency]['price'] ?? $callVariant->price;
                                        $vSalePrice = $callVariant->currency_prices[$currentCurrency]['sale_price'] ?? null;
                                    } else {
                                        $vPrice = $callVariant->price;
                                        $vSalePrice = $callVariant->sale_price;
                                    }
                                @endphp
                                <div class="font-semibold flex items-center text-sm">
                                    <span class="flex items-center">
                                        {{ currencySymbol() }}
                                        @if($vSalePrice)<del class="text-gray-500 mx-1">{{ number_format($vPrice, 0) }}</del>@endif
                                        <span class="font-bold text-base">{{ number_format($vSalePrice ?? $vPrice, 0) }}/{{ $callVariant->quantity_unit ?? 'Min' }}</span>
                                    </span>
                                </div>
                            @endif
                        </div>
                    </li>
                </ul>
                
                <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                    <div class="flex-1">
                        @if($consultations)
                            <p class="m-0 p-0 text-xs text-gray-700">
                                <span class="m-0 p-0 block text-sm">Reviews: <span class="font-semibold text-sm">{{ $consultations }}</span></span>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300' }} ml-1 text-sm"></i>
                                @endfor
                            </p>
                        @endif
                    </div>
                    
                    <div class="flex flex-col gap-1 ml-4 min-w-[80px]">
                        @if($status === 'online' && $callVariant)
                            <form action="{{ route('cart.add') }}" method="POST" onclick="event.stopPropagation()">
                                @csrf
                                <input type="hidden" name="product_type" value="cms_page_variant">
                                <input type="hidden" name="product_id" value="{{ $item->id }}">
                                <input type="hidden" name="variant_id" value="{{ $callVariant->id }}">
                                <input type="hidden" name="quantity" value="{{ $callVariant->min_quantity ?? 1 }}">
                                <button type="submit" class="text-white hover:text-white text-sm font-semibold py-2 px-4 rounded-lg text-center bg-orange-500 hover:bg-orange-600 transition-all w-full inline-flex items-center justify-center">Call</button>
                            </form>
                        @elseif($status === 'busy')
                            <button class="text-orange-600 bg-orange-50 text-sm font-semibold py-2 px-4 rounded-lg text-center w-full" disabled>Busy</button>
                        @else
                            <button class="text-gray-600 bg-gray-50 text-sm font-semibold py-2 px-4 rounded-lg text-center w-full" disabled>Offline</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
