@extends('layouts.app')

@section('title', $product->name . ' - Buy Online at AstroServices')

@push('meta')
<meta name="description" content="{{ Str::limit($product->description, 160) }} Buy authentic {{ $product->name }} online at AstroServices with free shipping.">
<meta name="keywords" content="{{ $product->name }}, {{ $product->category }}, astrology, spiritual, buy online">
<meta property="og:title" content="{{ $product->name }} - AstroServices">
<meta property="og:description" content="{{ Str::limit($product->description, 160) }}">
<meta property="og:type" content="product">
<meta property="product:price:amount" content="{{ $product->price }}">
<meta property="product:price:currency" content="{{ $product->currency }}">
@endpush

@section('content')
<style>
.product-slider,
.product-slider-nav,
.product-slider .slick-list,
.product-slider-nav .slick-list,
.product-slider .slick-track,
.product-slider-nav .slick-track {
    max-width: 100% !important;
}
.slick-prev, .slick-next {
    z-index: 10;
}
.slick-prev {
    left: 10px;
}
.slick-next {
    right: 10px;
}
.product-slider-nav .slick-current img {
    border-color: #FFD700 !important;
}
</style>
<!-- Slick Slider CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>

<div class="container mx-auto px-4 py-4 sm:py-8">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 sm:mb-6 text-sm sm:text-base">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 sm:mb-6 text-sm sm:text-base">
            {{ session('error') }}
        </div>
    @endif
    <div class="grid md:grid-cols-2 gap-6 sm:gap-8 lg:gap-12">
        <!-- Product Images -->
        <div>
            @php
                $allImages = [];
                if($product->image) {
                    $allImages[] = $product->image;
                }
                if($product->images) {
                    if(is_string($product->images)) {
                        $images = json_decode($product->images, true);
                    } else {
                        $images = $product->images;
                    }
                    if(is_array($images) && count($images) > 0) {
                        foreach($images as $img) {
                            $allImages[] = 'storage/' . $img;
                        }
                    }
                }
            @endphp

            @if(count($allImages) > 0)
                <!-- Mobile: Simple Image Display -->
                <div class="md:hidden mb-4">
                    <img src="{{ asset($allImages[0]) }}" alt="{{ $product->name }}" class="w-full h-64 object-cover rounded-lg shadow-lg cursor-pointer" onclick="openImageModal('{{ asset($allImages[0]) }}')">
                    @if(count($allImages) > 1)
                        <div class="grid grid-cols-3 gap-2 mt-2">
                            @foreach(array_slice($allImages, 1, 3) as $image)
                                <img src="{{ asset($image) }}" alt="{{ $product->name }}" class="w-full h-20 object-cover rounded cursor-pointer" onclick="openImageModal('{{ asset($image) }}')">
                            @endforeach
                        </div>
                    @endif
                </div>
                
                <!-- Desktop: Slider -->
                <div class="hidden md:block">
                    <div class="product-slider mb-4">
                        @foreach($allImages as $image)
                            <div>
                                <img src="{{ asset($image) }}" alt="{{ $product->name }}" class="w-full h-96 object-cover rounded-lg shadow-lg cursor-pointer" onclick="openImageModal('{{ asset($image) }}')">
                            </div>
                        @endforeach
                    </div>

                    @if(count($allImages) > 1)
                        <div class="product-slider-nav">
                            @foreach($allImages as $image)
                                <div class="px-1">
                                    <img src="{{ asset($image) }}" alt="{{ $product->name }}" class="w-full h-20 object-cover rounded cursor-pointer border-2 border-transparent hover:border-divine-gold transition-colors">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <div class="w-full h-64 sm:h-80 lg:h-96 bg-gradient-to-br from-sacred-maroon to-deep-saffron rounded-lg shadow-lg flex items-center justify-center text-white text-xl sm:text-2xl font-bold">
                    {{ $product->name }}
                </div>
            @endif
        </div>

        <!-- Product Details -->
        <div>
            <nav class="text-xs sm:text-sm text-gray-500 mb-3 sm:mb-4">
                <a href="{{ route('shop.index') }}" class="hover:text-indigo-600">{{ __('messages.shop') }}</a> >
                <a href="{{ route('shop.category', $product->category) }}" class="hover:text-indigo-600">{{ ucfirst($product->category) }}</a> >
                {{ $product->name }}
            </nav>

            <h1 class="text-2xl sm:text-3xl font-bold mb-3 sm:mb-4">{{ $product->name }}</h1>

            <div class="text-2xl sm:text-3xl font-bold text-indigo-600 mb-4 sm:mb-6">{{ formatPrice($product->price) }}</div>

            <div class="mb-4 sm:mb-6">
                <h3 class="text-base sm:text-lg font-bold mb-2">{{ __('messages.description') }}</h3>
                <p class="text-sm sm:text-base text-gray-600">{{ $product->description }}</p>
            </div>

            @if($product->specifications)
            <div class="mb-4 sm:mb-6">
                <h3 class="text-base sm:text-lg font-bold mb-2">{{ __('messages.specifications') }}</h3>
                <ul class="text-sm sm:text-base text-gray-600 space-y-1">
                    @foreach($product->specifications as $spec)
                        <li>• {{ $spec }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="mb-4 sm:mb-6">
                <div class="flex items-center space-x-4 mb-3 sm:mb-4">
                    <span class="text-sm text-gray-600">{{ __('messages.quantity') }}:</span>
                    <select id="quantity-select" class="border border-gray-300 rounded px-3 py-2" onchange="updateQuantity()">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>

                @if($product->show_stock)
                    @if($product->stock_quantity > 0)
                        <p class="text-green-600 text-xs sm:text-sm mb-3 sm:mb-4">✓ {{ __('messages.in_stock') }} ({{ $product->stock_quantity }} {{ __('messages.available') }})</p>
                    @else
                        <p class="text-red-600 text-xs sm:text-sm mb-3 sm:mb-4">✗ {{ __('messages.out_of_stock') }}</p>
                    @endif
                @endif
            </div>

            <div class="space-y-3 sm:space-y-4">
                @if($product->stock_quantity > 0)
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1" id="cart-quantity">
                        <button type="submit" class="w-full bg-indigo-600 text-white py-2.5 sm:py-3 px-4 sm:px-6 rounded-lg font-bold hover:bg-indigo-700 text-sm sm:text-base">
                            {{ __('messages.add_to_cart') }}
                        </button>
                    </form>
                    @auth
                        <form action="{{ route('checkout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1" id="buy-quantity">
                            <input type="hidden" name="buy_now" value="1">
                            <button type="submit" class="w-full border-2 border-indigo-600 text-indigo-600 py-2.5 sm:py-3 px-4 sm:px-6 rounded-lg font-bold hover:bg-indigo-50 text-sm sm:text-base">
                                {{ __('messages.buy_now') }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="w-full border-2 border-indigo-600 text-indigo-600 py-2.5 sm:py-3 px-4 sm:px-6 rounded-lg font-bold hover:bg-indigo-50 block text-center text-sm sm:text-base">
                            {{ __('messages.login_to_buy') }}
                        </a>
                    @endauth
                @else
                    <button class="w-full bg-gray-400 text-white py-2.5 sm:py-3 px-4 sm:px-6 rounded-lg font-bold cursor-not-allowed text-sm sm:text-base" disabled>
                        {{ __('messages.out_of_stock') }}
                    </button>
                @endif
            </div>

            @if($product->features)
            <div class="mt-6 sm:mt-8 border-t pt-4 sm:pt-6">
                <h3 class="text-base sm:text-lg font-bold mb-3 sm:mb-4">{{ __('messages.product_features') }}</h3>
                <div class="space-y-2 text-xs sm:text-sm text-gray-600">
                    @foreach($product->features as $feature)
                        <p>✓ {{ $feature }}</p>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center" onclick="closeImageModal()">
    <div class="relative max-w-4xl max-h-full p-4">
        <img id="modalImage" src="" alt="Product Image" class="max-w-full max-h-full rounded-lg">
        <button onclick="closeImageModal()" class="absolute top-2 right-2 text-white bg-black bg-opacity-50 rounded-full w-8 h-8 flex items-center justify-center hover:bg-opacity-75">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<script>
function updateQuantity() {
    const quantity = document.getElementById('quantity-select').value;
    document.getElementById('cart-quantity').value = quantity;
    document.getElementById('buy-quantity').value = quantity;
}

function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});

// Initialize Slick Slider
$(document).ready(function(){
    $('.product-slider').slick({
        arrows: true,
        fade: true,
        asNavFor: '.product-slider-nav',
        prevArrow: '<button type="button" class="slick-prev bg-divine-gold text-temple-red rounded-full w-10 h-10 flex items-center justify-center hover:bg-holy-yellow transition-colors"><i class="fas fa-chevron-left"></i></button>',
        nextArrow: '<button type="button" class="slick-next bg-divine-gold text-temple-red rounded-full w-10 h-10 flex items-center justify-center hover:bg-holy-yellow transition-colors"><i class="fas fa-chevron-right"></i></button>',
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    $('.product-slider-nav').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        asNavFor: '.product-slider',
        dots: false,
        arrows: false,
        centerMode: false,
        focusOnSelect: true,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 2
                }
            }
        ]
    });
});
</script>
@endpush

@push('styles')
<style>
.slick-prev, .slick-next {
    z-index: 10;
}
.slick-prev {
    left: 10px;
}
.slick-next {
    right: 10px;
}
.product-slider-nav .slick-current img {
    border-color: #FFD700 !important;
}
</style>
@endpush
