@extends('layouts.app')

@section('title', 'About Us - Astrology Services')

@section('content')
<div class="bg-gradient-to-r from-indigo-900 to-purple-900 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-4">About AstroServices</h1>
        <p class="text-xl">Your trusted partner in spiritual guidance and astrology</p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <!-- Our Story -->
    <section class="mb-16">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl font-bold mb-6">Our Story</h2>
                <p class="text-gray-600 mb-4">
                    Founded with a vision to make authentic astrology services accessible to everyone, AstroServices has been guiding people on their spiritual journey for over a decade. We combine traditional Vedic wisdom with modern technology to provide accurate and meaningful insights.
                </p>
                <p class="text-gray-600 mb-4">
                    Our team of certified astrologers and spiritual experts are dedicated to helping you understand your life's purpose, overcome challenges, and make informed decisions based on cosmic guidance.
                </p>
                <p class="text-gray-600">
                    Whether you're seeking answers about love, career, health, or spiritual growth, we're here to illuminate your path with authentic astrological wisdom.
                </p>
            </div>
            <div class="text-center">
                <div class="w-full h-80 bg-indigo-600 rounded-lg shadow-lg flex items-center justify-center">
                    <span class="text-white text-2xl font-bold">Our Story</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Mission -->
    <section class="mb-16 bg-gray-50 p-8 rounded-lg">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold mb-4">Our Mission</h2>
            <p class="text-xl text-gray-600">To bridge ancient wisdom with modern life, providing authentic spiritual guidance for everyone</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-star text-indigo-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Authenticity</h3>
                <p class="text-gray-600">We provide genuine astrological services based on traditional Vedic principles and authentic spiritual practices.</p>
            </div>
            <div class="text-center">
                <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-heart text-indigo-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Compassion</h3>
                <p class="text-gray-600">Every consultation is conducted with empathy, understanding, and genuine care for your well-being and spiritual growth.</p>
            </div>
            <div class="text-center">
                <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-lightbulb text-indigo-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Guidance</h3>
                <p class="text-gray-600">We empower you with knowledge and insights to make informed decisions and navigate life's challenges with confidence.</p>
            </div>
        </div>
    </section>

    <!-- Our Services -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold text-center mb-12">What We Offer</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <div class="text-3xl mb-4">💬</div>
                <h3 class="font-bold mb-2">Consultations</h3>
                <p class="text-sm text-gray-600">Personal guidance through chat, video, and phone sessions</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <div class="text-3xl mb-4">📊</div>
                <h3 class="font-bold mb-2">Kundli Reading</h3>
                <p class="text-sm text-gray-600">Detailed birth chart analysis and life predictions</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <div class="text-3xl mb-4">🕉️</div>
                <h3 class="font-bold mb-2">Pooja Services</h3>
                <p class="text-sm text-gray-600">Temple and home rituals with experienced pandits</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <div class="text-3xl mb-4">💎</div>
                <h3 class="font-bold mb-2">Sacred Products</h3>
                <p class="text-sm text-gray-600">Authentic gemstones, rudraksha, and spiritual items</p>
            </div>
        </div>
    </section>

    <!-- Our Team -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold text-center mb-12">Our Expert Team</h2>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <div class="w-24 h-24 bg-indigo-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <span class="text-white text-xs font-bold">Guru Ji</span>
                </div>
                <h3 class="text-xl font-bold mb-2">Pandit Rajesh Sharma</h3>
                <p class="text-indigo-600 mb-2">Senior Astrologer</p>
                <p class="text-sm text-gray-600">25+ years experience in Vedic astrology and spiritual guidance</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <div class="w-24 h-24 bg-indigo-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <span class="text-white text-xs font-bold">Guru Ma</span>
                </div>
                <h3 class="text-xl font-bold mb-2">Dr. Priya Agarwal</h3>
                <p class="text-indigo-600 mb-2">Gemstone Expert</p>
                <p class="text-sm text-gray-600">PhD in Gemology, specializing in astrological gemstone therapy</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <div class="w-24 h-24 bg-indigo-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <span class="text-white text-xs font-bold">Pandit</span>
                </div>
                <h3 class="text-xl font-bold mb-2">Acharya Vikram Singh</h3>
                <p class="text-indigo-600 mb-2">Ritual Specialist</p>
                <p class="text-sm text-gray-600">Expert in Vedic rituals and traditional pooja ceremonies</p>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="bg-indigo-50 p-8 rounded-lg">
        <h2 class="text-3xl font-bold text-center mb-8">Why Choose AstroServices?</h2>
        <div class="grid md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <div class="flex items-start space-x-4">
                    <div class="bg-indigo-600 text-white p-2 rounded">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div>
                        <h4 class="font-bold">Certified Experts</h4>
                        <p class="text-gray-600">All our astrologers are certified and have years of experience</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="bg-indigo-600 text-white p-2 rounded">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h4 class="font-bold">100% Authentic</h4>
                        <p class="text-gray-600">Genuine products and services with quality guarantee</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="bg-indigo-600 text-white p-2 rounded">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div>
                        <h4 class="font-bold">Privacy Protected</h4>
                        <p class="text-gray-600">Your personal information is completely secure with us</p>
                    </div>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex items-start space-x-4">
                    <div class="bg-indigo-600 text-white p-2 rounded">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h4 class="font-bold">24/7 Support</h4>
                        <p class="text-gray-600">Round-the-clock customer service and guidance</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="bg-indigo-600 text-white p-2 rounded">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <h4 class="font-bold">Affordable Pricing</h4>
                        <p class="text-gray-600">Quality services at reasonable and transparent prices</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="bg-indigo-600 text-white p-2 rounded">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div>
                        <h4 class="font-bold">Easy Access</h4>
                        <p class="text-gray-600">Multiple consultation options - chat, video, phone</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection