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
        </div>
    </div>
</section>

<!-- Shop Section -->
<section class="bg-gray-100 py-16">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-bold text-center mb-12">Sacred Products</h2>
        <div class="grid md:grid-cols-4 gap-6">
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
@endsection