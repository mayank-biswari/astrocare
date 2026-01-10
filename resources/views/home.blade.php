@extends('layouts.app')

@section('title', 'Home - Astrology Services')

@section('content')
<!-- Hero Slider -->
@if($heroPages->count() > 0)
<section class="hero-slider">
    @foreach($heroPages as $heroPage)
    @if($heroPage->custom_fields['link'])
        <a href="{{ $heroPage->custom_fields['link'] }}">
    @endif
    <div class="hero-slide relative bg-gradient-to-br from-sacred-maroon via-temple-red to-deep-saffron text-white py-52 overflow-hidden">
        @if($heroPage->image)
            <div class="absolute inset-0">
                <img src="{{ asset('storage/' . $heroPage->image) }}" alt="{{ $heroPage->title }}" class="w-full h-full object-cover">
            </div>
        @endif
    </div>
    @if($heroPage->custom_fields['link'])
        </a>
    @endif
    @endforeach
</section>
@else
<!-- Default Hero Section -->
<section class="relative bg-gradient-to-br from-sacred-maroon via-temple-red to-deep-saffron text-white py-20 overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 text-6xl text-divine-gold">🕉️</div>
        <div class="absolute top-20 right-20 text-4xl text-holy-yellow">⭐</div>
        <div class="absolute bottom-20 left-20 text-5xl text-divine-gold">🔯</div>
        <div class="absolute bottom-10 right-10 text-4xl text-holy-yellow">🌙</div>
    </div>

    <div class="container mx-auto px-4 text-center relative z-10">
        <div class="mb-6">
            <span class="text-6xl text-divine-gold mb-4 block">🕉️</span>
            <div class="w-24 h-1 bg-gradient-to-r from-divine-gold to-holy-yellow mx-auto mb-6"></div>
        </div>

        <h1 class="text-4xl md:text-6xl font-bold mb-6 bg-gradient-to-r from-divine-gold via-holy-yellow to-divine-gold bg-clip-text text-transparent">
            {{ __('messages.welcome_title') }}
        </h1>

        <p class="text-lg md:text-xl mb-8 text-orange-100 max-w-3xl mx-auto leading-relaxed">
            {{ __('messages.welcome_subtitle') }}
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="{{ route('consultations.index') }}" class="bg-gradient-to-r from-divine-gold to-holy-yellow text-temple-red px-8 py-4 rounded-lg font-bold hover:from-holy-yellow hover:to-divine-gold transition-all duration-300 transform hover:scale-105 divine-glow flex items-center">
                <i class="fas fa-star-and-crescent mr-2"></i>
                {{ __('messages.book_now') }}
            </a>
            <a href="{{ route('kundli.create') }}" class="border-2 border-divine-gold text-divine-gold px-8 py-4 rounded-lg font-bold hover:bg-divine-gold hover:text-temple-red transition-all duration-300 transform hover:scale-105 flex items-center">
                <i class="fas fa-chart-pie mr-2"></i>
                {{ __('messages.kundli_reading') }}
            </a>
        </div>
    </div>

    <!-- Bottom Wave -->
    <div class="absolute bottom-0 left-0 w-full">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="relative block w-full h-16">
            <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" fill="#FFD700"></path>
            <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" fill="#FFD700"></path>
            <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" fill="#FFD700"></path>
        </svg>
    </div>
</section>
@endif

<!-- Services Section -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-bold text-center mb-12">{{ __('messages.services') }}</h2>
        <div class="grid md:grid-cols-3 gap-8">
            @if(count($services) > 0)
                @foreach($services as $service)
                <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                    @if($service->image)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}" class="w-full h-40 mx-auto rounded-lg object">
                        </div>
                    @else
                        <div class="text-4xl mb-4">
                            @if(str_contains($service->title, 'Consultation'))
                                💬
                            @elseif(str_contains($service->title, 'Kundli'))
                                📊
                            @elseif(str_contains($service->title, 'Pooja'))
                                🕉️
                            @else
                                ⭐
                            @endif
                        </div>
                    @endif
                    <h3 class="text-2xl font-bold mb-4">{{ $service->title }}</h3>
                    <p class="text-gray-600 mb-6">{!! $service->body !!}</p>
                    <a href="{{ $service->custom_fields['service_link'] ?? '#' }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">{{ __('messages.learn_more') }}</a>
                </div>
                @endforeach
            @else
                <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                    <div class="text-4xl mb-4">💬</div>
                    <h3 class="text-2xl font-bold mb-4">Astrology Consultation</h3>
                    <p class="text-gray-600 mb-6">Get personalized guidance through chat, video, or phone consultations with expert astrologers.</p>
                    <a href="{{ route('consultations.index') }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">{{ __('messages.learn_more') }}</a>
                </div>
                <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                    <div class="text-4xl mb-4">📊</div>
                    <h3 class="text-2xl font-bold mb-4">Kundli Reading</h3>
                    <p class="text-gray-600 mb-6">Generate detailed birth charts and get comprehensive astrological analysis.</p>
                    <a href="{{ route('kundli.index') }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">{{ __('messages.learn_more') }}</a>
                </div>
                <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                    <div class="text-4xl mb-4">🕉️</div>
                    <h3 class="text-2xl font-bold mb-4">Pooja & Rituals</h3>
                    <p class="text-gray-600 mb-6">Book temple poojas, home ceremonies, and connect with experienced pandits.</p>
                    <a href="{{ route('pooja.index') }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">{{ __('messages.learn_more') }}</a>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Shop Section -->
<section class="bg-gray-100 py-16">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-bold text-center mb-12">Sacred Products</h2>
        <div class="grid md:grid-cols-4 gap-6">
            @if(count($products) > 0)
                @foreach($products as $product)
                <a href="{{ $product->custom_fields['product_link'] ?? '#' }}" class="bg-white p-6 rounded-lg shadow text-center hover:shadow-lg transition">
                    @if($product->image)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->title }}" class="w-12 h-12 mx-auto rounded object-cover">
                        </div>
                    @else
                        <div class="text-3xl mb-3">
                            @if(str_contains($product->title, 'Gemstones'))
                                💎
                            @elseif(str_contains($product->title, 'Rudraksha'))
                                📿
                            @elseif(str_contains($product->title, 'Yantras'))
                                🔯
                            @elseif(str_contains($product->title, 'Crystals'))
                                💎
                            @else
                                ⭐
                            @endif
                        </div>
                    @endif
                    <h4 class="font-bold mb-2">{{ $product->title }}</h4>
                    <p class="text-sm text-gray-600">{!! $product->body !!}</p>
                </a>
                @endforeach
            @else
                <a href="{{ route('shop.category', 'gemstones') }}" class="bg-white p-6 rounded-lg shadow text-center hover:shadow-lg transition">
                    <div class="text-3xl mb-3">💎</div>
                    <h4 class="font-bold mb-2">Gemstones</h4>
                    <p class="text-sm text-gray-600">Authentic precious stones</p>
                </a>
                <a href="{{ route('shop.category', 'rudraksha') }}" class="bg-white p-6 rounded-lg shadow text-center hover:shadow-lg transition">
                    <div class="text-3xl mb-3">📿</div>
                    <h4 class="font-bold mb-2">Rudraksha</h4>
                    <p class="text-sm text-gray-600">Sacred beads for meditation</p>
                </a>
                <a href="{{ route('shop.category', 'yantras') }}" class="bg-white p-6 rounded-lg shadow text-center hover:shadow-lg transition">
                    <div class="text-3xl mb-3">🔯</div>
                    <h4 class="font-bold mb-2">Yantras</h4>
                    <p class="text-sm text-gray-600">Mystical geometric designs</p>
                </a>
                <a href="{{ route('shop.category', 'crystals') }}" class="bg-white p-6 rounded-lg shadow text-center hover:shadow-lg transition">
                    <div class="text-3xl mb-3">💎</div>
                    <h4 class="font-bold mb-2">Crystals</h4>
                    <p class="text-sm text-gray-600">Healing crystal products</p>
                </a>
            @endif
        </div>
        <div class="text-center mt-8">
            <a href="{{ route('shop.index') }}" class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700">{{ __('messages.shop') }}</a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-4xl font-bold mb-6">Why Choose Us?</h2>
                <div class="space-y-4">
                    <div class="flex items-start space-x-4">
                        <div class="bg-indigo-100 p-2 rounded">
                            <i class="fas fa-star text-indigo-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold">Expert Astrologers</h4>
                            <p class="text-gray-600">Certified professionals with years of experience</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="bg-indigo-100 p-2 rounded">
                            <i class="fas fa-shield-alt text-indigo-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold">Authentic Products</h4>
                            <p class="text-gray-600">Genuine gemstones and spiritual items</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="bg-indigo-100 p-2 rounded">
                            <i class="fas fa-clock text-indigo-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold">24/7 Support</h4>
                            <p class="text-gray-600">Round-the-clock customer assistance</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <div class="w-full h-80 bg-indigo-600 rounded-lg shadow-lg flex items-center justify-center">
                    <span class="text-white text-2xl font-bold">Astrology Services</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
@if($testimonials->count() > 0)
<section class="bg-gray-100 py-16">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-bold text-center mb-12">What Our Clients Say</h2>
        <div class="grid md:grid-cols-3 gap-8">
            @foreach($testimonials as $testimonial)
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="flex items-center mb-4">
                    @if($testimonial->custom_fields['testimonial_rating'] ?? null)
                        <div class="text-yellow-500 mr-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= ($testimonial->custom_fields['testimonial_rating'] ?? 0))
                                    ★
                                @else
                                    ☆
                                @endif
                            @endfor
                        </div>
                    @endif
                </div>
                <p class="text-gray-600 mb-4 italic">"{{ Str::limit(strip_tags($testimonial->body), 150) }}"</p>
                <div class="border-t pt-4">
                    <h4 class="font-bold">{{ $testimonial->custom_fields['client_name'] ?? 'Anonymous' }}</h4>
                    @if($testimonial->custom_fields['client_location'] ?? null)
                        <p class="text-sm text-gray-500">{{ $testimonial->custom_fields['client_location'] }}</p>
                    @endif
                    @if($testimonial->custom_fields['service_type'] ?? null)
                        <p class="text-sm text-indigo-600">{{ $testimonial->custom_fields['service_type'] }} Service</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-8">
            <a href="{{ route('testimonials') }}" class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700">View All Testimonials</a>
        </div>
    </div>
</section>
@endif
@endsection

@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
@endpush

@push('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script>
$(document).ready(function(){
    $('.hero-slider').slick({
        dots: false,
        infinite: true,
        speed: 500,
        fade: true,
        cssEase: 'linear',
        autoplay: true,
        autoplaySpeed: 5000,
        arrows: true,
        prevArrow: '<button type="button" class="slick-prev absolute left-4 top-1/2 transform -translate-y-1/2 z-20 bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-full transition-all"><i class="fas fa-chevron-left"></i></button>',
        nextArrow: '<button type="button" class="slick-next absolute right-4 top-1/2 transform -translate-y-1/2 z-20 bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-full transition-all"><i class="fas fa-chevron-right"></i></button>'
    });
});
</script>
@endpush
