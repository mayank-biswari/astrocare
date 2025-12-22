@extends('layouts.app')

@section('title', 'Pooja & Rituals Services')

@section('content')
<div class="bg-gradient-to-r from-orange-600 to-red-600 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-4">Pooja & Rituals</h1>
        <p class="text-xl">Sacred ceremonies and spiritual rituals for your well-being</p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <!-- Temple Pooja -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold mb-8">Temple Pooja Booking</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($poojas->where('category', 'temple') as $pooja)
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <div class="text-4xl mb-4">{{ $pooja->icon }}</div>
                <h3 class="text-xl font-bold mb-4">{{ $pooja->name }}</h3>
                <p class="text-gray-600 mb-4">{{ Str::limit($pooja->description, 50) }}</p>
                <div class="text-2xl font-bold text-orange-600 mb-4">{{ formatPrice($pooja->price) }}</div>
                <a href="{{ route('pooja.show', $pooja->slug) }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 w-full block text-center">View Details</a>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Home Pooja -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold mb-8">Home Pooja Services</h2>
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <div class="grid md:grid-cols-2 gap-8 items-center">
                <div>
                    <h3 class="text-2xl font-bold mb-4">Bring Sacred Rituals to Your Home</h3>
                    <p class="text-gray-600 mb-6">Our experienced pandits will perform authentic Vedic rituals at your home with all necessary arrangements.</p>
                    <ul class="space-y-2 text-gray-600 mb-6">
                        <li>✓ Experienced certified pandits</li>
                        <li>✓ All pooja materials included</li>
                        <li>✓ Flexible timing as per your convenience</li>
                        <li>✓ Authentic Vedic procedures</li>
                        <li>✓ Multilingual pandits available</li>
                    </ul>
                    <form action="{{ route('pooja.book') }}" method="POST">
                        @csrf
                        <input type="hidden" name="name" value="Home Pooja">
                        <input type="hidden" name="type" value="home">
                        <input type="hidden" name="amount" value="1500">
                        <input type="hidden" name="scheduled_at" value="{{ now()->addDays(7)->format('Y-m-d H:i:s') }}">
                        <button type="submit" class="bg-orange-600 text-white px-8 py-3 rounded-lg hover:bg-orange-700">Book Home Pooja</button>
                    </form>
                </div>
                <div class="text-center">
                    <div class="w-full h-64 bg-orange-600 rounded-lg shadow-lg flex items-center justify-center">
                        <span class="text-white text-xl font-bold">Home Pooja</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Jaap & Homam -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold mb-8">Jaap & Homam</h2>
        <div class="grid md:grid-cols-2 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold mb-4">Maha Mrityunjay Jaap</h3>
                <p class="text-gray-600 mb-4">Powerful mantra chanting for health, longevity, and protection from negative energies.</p>
                <div class="text-2xl font-bold text-orange-600 mb-4">{{ formatPrice(2100) }}</div>
                <ul class="text-sm text-gray-600 mb-4">
                    <li>• 1,25,000 mantra chanting</li>
                    <li>• Duration: 11 days</li>
                    <li>• Includes havan and prasad</li>
                </ul>
                <button class="bg-orange-600 text-white px-6 py-2 rounded hover:bg-orange-700 w-full">Book Now</button>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold mb-4">Kaal Sarp Dosh Puja</h3>
                <p class="text-gray-600 mb-4">Special ritual to neutralize the negative effects of Kaal Sarp Dosha in your horoscope.</p>
                <div class="text-2xl font-bold text-orange-600 mb-4">{{ formatPrice(3100) }}</div>
                <ul class="text-sm text-gray-600 mb-4">
                    <li>• Complete Kaal Sarp Dosh nivaran</li>
                    <li>• Rudrabhishek included</li>
                    <li>• Energized rudraksha provided</li>
                </ul>
                <button class="bg-orange-600 text-white px-6 py-2 rounded hover:bg-orange-700 w-full">Book Now</button>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold mb-4">Navgraha Shanti</h3>
                <p class="text-gray-600 mb-4">Comprehensive ritual to appease all nine planets and reduce their malefic effects.</p>
                <div class="text-2xl font-bold text-orange-600 mb-4">{{ formatPrice(5100) }}</div>
                <ul class="text-sm text-gray-600 mb-4">
                    <li>• All 9 planetary mantras</li>
                    <li>• Havan for each planet</li>
                    <li>• Gemstone recommendations</li>
                </ul>
                <button class="bg-orange-600 text-white px-6 py-2 rounded hover:bg-orange-700 w-full">Book Now</button>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold mb-4">Lakshmi Pooja</h3>
                <p class="text-gray-600 mb-4">Special worship of Goddess Lakshmi for wealth, prosperity, and financial stability.</p>
                <div class="text-2xl font-bold text-orange-600 mb-4">{{ formatPrice(2100) }}</div>
                <ul class="text-sm text-gray-600 mb-4">
                    <li>• Lakshmi mantra chanting</li>
                    <li>• Gold coin offering</li>
                    <li>• Prosperity yantra energized</li>
                </ul>
                <button class="bg-orange-600 text-white px-6 py-2 rounded hover:bg-orange-700 w-full">Book Now</button>
            </div>
        </div>
    </section>

    <!-- Special Occasion Pooja -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold mb-8">Special Occasion Pooja</h2>
        <div class="grid md:grid-cols-5 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <div class="text-3xl mb-3">🏥</div>
                <h4 class="font-bold mb-2">Health</h4>
                <p class="text-sm text-gray-600 mb-4">Healing rituals and health-focused poojas</p>
                <button class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700 text-sm">Explore</button>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <div class="text-3xl mb-3">💒</div>
                <h4 class="font-bold mb-2">Marriage</h4>
                <p class="text-sm text-gray-600 mb-4">Wedding ceremonies and marriage blessings</p>
                <button class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700 text-sm">Explore</button>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <div class="text-3xl mb-3">💰</div>
                <h4 class="font-bold mb-2">Wealth</h4>
                <p class="text-sm text-gray-600 mb-4">Prosperity and financial growth rituals</p>
                <button class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700 text-sm">Explore</button>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <div class="text-3xl mb-3">📚</div>
                <h4 class="font-bold mb-2">Education</h4>
                <p class="text-sm text-gray-600 mb-4">Academic success and knowledge enhancement</p>
                <button class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700 text-sm">Explore</button>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <div class="text-3xl mb-3">☮️</div>
                <h4 class="font-bold mb-2">Peace</h4>
                <p class="text-sm text-gray-600 mb-4">Harmony and spiritual peace rituals</p>
                <button class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700 text-sm">Explore</button>
            </div>
        </div>
    </section>

    <!-- Pandit Booking -->
    <section>
        <h2 class="text-3xl font-bold mb-8">Pandit Booking</h2>
        <div class="bg-gradient-to-r from-orange-100 to-red-100 p-8 rounded-lg">
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold mb-4">Book Experienced Pandits</h3>
                <p class="text-gray-600">Connect with certified and experienced pandits for all your spiritual needs</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="w-16 h-16 bg-orange-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="text-white text-xs font-bold">P</span>
                    </div>
                    <h4 class="font-bold mb-2">Pandit Rajesh Sharma</h4>
                    <p class="text-sm text-gray-600 mb-2">25+ years experience</p>
                    <p class="text-sm text-gray-600 mb-4">Specializes in Vedic rituals</p>
                    <button class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700">Book Now</button>
                </div>
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="w-16 h-16 bg-orange-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="text-white text-xs font-bold">A</span>
                    </div>
                    <h4 class="font-bold mb-2">Acharya Vikram Singh</h4>
                    <p class="text-sm text-gray-600 mb-2">20+ years experience</p>
                    <p class="text-sm text-gray-600 mb-4">Expert in Havan ceremonies</p>
                    <button class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700">Book Now</button>
                </div>
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="w-16 h-16 bg-orange-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="text-white text-xs font-bold">P</span>
                    </div>
                    <h4 class="font-bold mb-2">Pandit Suresh Gupta</h4>
                    <p class="text-sm text-gray-600 mb-2">30+ years experience</p>
                    <p class="text-sm text-gray-600 mb-4">Marriage ceremony specialist</p>
                    <button class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700">Book Now</button>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection