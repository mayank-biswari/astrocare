@extends('layouts.app')

@section('title', $page->custom_fields['name'] ?? $page->title)

@section('content')
@php
    $fields = $page->custom_fields ?? [];
    $name = $page->title ?? 'Astrologer';
    $title = $fields['title'] ?? '';
    $status = $fields['status'] ?? 'offline';
    $rating = $fields['rating'] ?? 0;
    $experience = $fields['experience'] ?? 0;
    $languages = $fields['languages'] ?? '';
    $consultations = $fields['consultations'] ?? 0;
    $callRate = $fields['call_rate'] ?? 0;
    $chatRate = $fields['chat_rate'] ?? 0;
    $expertise = $fields['expertise'] ?? '';
    $about = $page->body ?? '';
    $education = $fields['education'] ?? '';

    // Product data
    $product = $page->product;
    $hasProduct = $product !== null;
    $currentCurrency = session('currency', \App\Models\Currency::getDefaultCurrency()->code);

    if ($product && $product->currency_prices && is_array($product->currency_prices) && isset($product->currency_prices[$currentCurrency])) {
        $currencyPrice = $product->currency_prices[$currentCurrency];
        $price = $currencyPrice['price'] ?? $product->price;
        $salePrice = $currencyPrice['sale_price'] ?? null;
    } else {
        $price = $product->price ?? 0;
        $salePrice = $product->sale_price ?? null;
    }

    $effectivePrice = $salePrice ?? $price;
    $sku = $product->sku ?? '';
    $inStock = $product ? $product->isInStock() : false;
    $isFeatured = $product->is_featured ?? false;
    $minQuantity = $product->min_quantity ?? 1;
    $quantityStep = $product->quantity_step ?? 1;
    $quantityUnit = $product->quantity_unit ?? 'item';

    $expertiseList = array_filter(array_map('trim', explode(',', $expertise)));
    $languageList = array_filter(array_map('trim', explode(',', $languages)));
    $educationList = array_filter(array_map('trim', explode("\n", $education)));
@endphp

<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        <!-- Profile Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Profile Image -->
                <div class="flex-shrink-0">
                    <div class="relative">
                        <img src="{{ asset('storage/' . $page->image) }}" alt="{{ $name }}" class="w-32 h-32 rounded-full object-cover border-4 border-orange-500">
                        <span class="absolute bottom-0 right-0 w-6 h-6 rounded-full border-2 border-white {{ $status === 'online' ? 'bg-green-500' : ($status === 'busy' ? 'bg-yellow-500' : 'bg-gray-400') }}"></span>
                    </div>
                </div>

                <!-- Profile Info -->
                <div class="flex-1">
                    <div class="flex items-start justify-between flex-wrap gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800">{{ $name }}</h1>
                            <p class="text-lg text-gray-600 mt-1">{{ $title }}</p>

                            <!-- Rating -->
                            <div class="flex items-center gap-2 mt-2">
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-gray-600 font-semibold">{{ number_format($rating, 1) }}</span>
                            </div>

                            <!-- Stats -->
                            <div class="flex flex-wrap gap-4 mt-4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-gray-700"><strong>{{ $experience }}</strong> Years Exp.</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    @if($consultations)<span class="text-gray-700"><strong>{{ $consultations ?? 0 }}</strong> Consultations</span>@endif
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col gap-3">
                            @if($status === 'online' && $hasProduct && $product->variants && $product->variants->where('is_active', true)->count() > 0)
                                @foreach($product->variants->where('is_active', true) as $variant)
                                @php
                                    $vCurrentCurrency = session('currency', \App\Models\Currency::getDefaultCurrency()->code);
                                    if ($variant->currency_prices && is_array($variant->currency_prices) && isset($variant->currency_prices[$vCurrentCurrency])) {
                                        $vCurrencyPrice = $variant->currency_prices[$vCurrentCurrency];
                                        $vPrice = $vCurrencyPrice['price'] ?? $variant->price;
                                        $vSalePrice = $vCurrencyPrice['sale_price'] ?? null;
                                    } else {
                                        $vPrice = $variant->price;
                                        $vSalePrice = $variant->sale_price;
                                    }
                                    $vEffectivePrice = $vSalePrice ?? $vPrice;
                                    $isCall = stripos($variant->name, 'call') !== false;
                                @endphp
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_type" value="cms_page_variant">
                                    <input type="hidden" name="product_id" value="{{ $page->id }}">
                                    <input type="hidden" name="variant_id" value="{{ $variant->id }}">
                                    <input type="hidden" name="quantity" value="{{ $variant->min_quantity ?? 1 }}">
                                    <button type="submit" class="{{ $isCall ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-500 hover:bg-green-600' }} text-white px-6 py-3 rounded-lg font-semibold flex items-center gap-2 transition w-full">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($isCall)
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            @endif
                                        </svg>
                                        {{ $variant->name }} Now - {{ currencySymbol() }}{{ number_format($vEffectivePrice, 2) }}/{{ $variant->quantity_unit ?? 'min' }}
                                    </button>
                                </form>
                                @endforeach
                            @elseif($status === 'online')
                                <button class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold flex items-center gap-2 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    Call Now - {{ formatPrice($callRate) }}/min
                                </button>
                                <button class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold flex items-center gap-2 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    Chat Now - {{ formatPrice($chatRate) }}/min
                                </button>
                            @else
                                <button class="bg-gray-400 text-white px-6 py-3 rounded-lg font-semibold cursor-not-allowed" disabled>
                                    Currently {{ ucfirst($status) }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Expertise Section -->
                @if(count($expertiseList) > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                        Expertise
                    </h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($expertiseList as $item)
                            <span class="bg-orange-100 text-orange-700 px-4 py-2 rounded-full text-sm font-medium">{{ $item }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- About Section -->
                @if($about)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        About
                    </h2>
                    <div class="text-gray-700 leading-relaxed whitespace-pre-line">{!! $about !!}</div>
                </div>
                @endif

                <!-- Education Section -->
                @if(count($educationList) > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Education
                    </h2>
                    <ul class="space-y-2">
                        @foreach($educationList as $edu)
                            <li class="flex items-start gap-2 text-gray-700">
                                <svg class="w-5 h-5 text-orange-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $edu }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if(!empty($availability) && count($availability) > 0)
                <!-- Availability Calendar -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Check Online Availability
                    </h2>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($availability as $item)
                        <div class="text-center p-3 border rounded-lg {{ $item['is_available'] ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                            <div class="font-semibold text-gray-900 text-sm">{{ $item['date']->format('D') }}</div>
                            <div class="text-xs text-gray-600 mb-1">({{ $item['date']->format('M j') }})</div>
                            <div class="text-xs font-medium {{ $item['is_available'] ? 'text-green-600' : 'text-red-600' }}">
                                {{ $item['is_available'] ? 'Available' : 'Not Available' }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Languages -->
                @if(count($languageList) > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                        </svg>
                        Languages
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($languageList as $lang)
                            <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">{{ $lang }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Pricing Card -->
                {{-- @include('dynamic-pages.custom-templates.components.pricing-card') --}}

                <!-- Status Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Availability</h3>
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded-full {{ $status === 'online' ? 'bg-green-500' : ($status === 'busy' ? 'bg-yellow-500' : 'bg-gray-400') }}"></span>
                        <span class="text-lg font-semibold {{ $status === 'online' ? 'text-green-600' : ($status === 'busy' ? 'text-yellow-600' : 'text-gray-600') }}">
                            {{ ucfirst($status) }}
                        </span>
                    </div>
                    @if($status === 'online')
                        <p class="text-sm text-gray-600 mt-2">Available for consultation now</p>
                    @elseif($status === 'busy')
                        <p class="text-sm text-gray-600 mt-2">Currently in consultation</p>
                    @else
                        <p class="text-sm text-gray-600 mt-2">Will be available soon</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
