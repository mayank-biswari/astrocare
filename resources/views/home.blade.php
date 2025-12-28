@extends('layouts.app')

@section('title', 'Home - Astrology Services')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-indigo-900 to-purple-900 text-white py-20">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-5xl font-bold mb-6">{{ __('messages.welcome_title') }}</h1>
        <p class="text-xl mb-8">{{ __('messages.welcome_subtitle') }}</p>
        <div class="space-x-4">
            <a href="{{ route('consultations.index') }}" class="bg-yellow-500 text-indigo-900 px-8 py-3 rounded-lg font-bold hover:bg-yellow-400">{{ __('messages.book_now') }}</a>
            <a href="{{ route('kundli.create') }}" class="border-2 border-white px-8 py-3 rounded-lg font-bold hover:bg-white hover:text-indigo-900">{{ __('messages.kundli_reading') }}</a>
        </div>
    </div>
</section>

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
                    <p class="text-gray-600 mb-6">{{ $service->body }}</p>
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
                    <p class="text-sm text-gray-600">{{ $product->body }}</p>
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
