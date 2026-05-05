@extends('layouts.app')

@section('title', 'Pooja & Rituals Services')

@section('content')
<div class="bg-gradient-to-r from-orange-600 to-red-600 text-white py-8 sm:py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-xl sm:text-4xl font-bold mb-2 sm:mb-4">Pooja & Rituals</h1>
        <p class="text-base sm:text-xl">Sacred ceremonies and spiritual rituals for your well-being</p>
    </div>
</div>

<div class="container mx-auto px-4 py-6 sm:py-12">
    <!-- Temple Pooja -->
    <section class="mb-8 sm:mb-16">
        <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Temple Pooja Booking</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
            @foreach($poojas->where('category', 'temple') as $pooja)
            <div class="bg-white p-3 sm:p-6 rounded-lg shadow-lg text-center hover:shadow-xl transition">
                @if($pooja->image)
                <img src="{{ asset('storage/' . $pooja->image) }}" alt="{{ $pooja->title }}" class="w-full h-32 sm:h-40 object-cover rounded-lg mb-4">
                @endif
                <h3 class="text-base sm:text-xl font-bold mb-4">{{ $pooja->title }}</h3>
                <p class="text-sm sm:text-base text-gray-600 mb-4">{{ Str::limit(strip_tags($pooja->body), 80) }}</p>
                <div class="text-lg sm:text-2xl font-bold text-orange-600 mb-4">{{ formatPrice($pooja->price) }}</div>
                <a href="/pages/{{ $pooja->slug }}" class="bg-orange-600 text-white text-sm sm:text-base px-4 sm:px-6 py-2 sm:py-3 rounded hover:bg-orange-700 inline-block">View Details</a>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Home Pooja -->
    <section class="mb-8 sm:mb-16">
        <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Home Pooja Services</h2>
        <div class="bg-white p-4 sm:p-8 rounded-lg shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 items-center">
                <div>
                    <h3 class="text-lg sm:text-2xl font-bold mb-4">Bring Sacred Rituals to Your Home</h3>
                    <p class="text-sm sm:text-base text-gray-600 mb-6">Our experienced pandits will perform authentic Vedic rituals at your home with all necessary arrangements.</p>
                    <ul class="space-y-2 text-sm sm:text-base text-gray-600 mb-6">
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
                        <button type="submit" class="bg-orange-600 text-white text-sm sm:text-base px-4 sm:px-8 py-2 sm:py-3 rounded-lg hover:bg-orange-700">Book Home Pooja</button>
                    </form>
                </div>
                <div class="text-center">
                    <div class="w-full h-48 sm:h-64 bg-orange-600 rounded-lg shadow-lg flex items-center justify-center">
                        <span class="text-white text-base sm:text-xl font-bold">Home Pooja</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Jaap & Homam -->
    <section class="mb-8 sm:mb-16">
        <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Jaap & Homam</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-8">
            @foreach($poojas->where('category', 'jaap') as $pooja)
            <div class="bg-white p-3 sm:p-6 rounded-lg shadow-lg hover:shadow-xl transition">
                <h3 class="text-base sm:text-xl font-bold mb-4">{{ $pooja->title }}</h3>
                <p class="text-sm sm:text-base text-gray-600 mb-4">{{ Str::limit(strip_tags($pooja->body), 120) }}</p>
                <div class="text-lg sm:text-2xl font-bold text-orange-600 mb-4">{{ formatPrice($pooja->price) }}</div>
                <a href="/pages/{{ $pooja->slug }}" class="bg-orange-600 text-white text-sm sm:text-base px-4 sm:px-6 py-2 sm:py-3 rounded hover:bg-orange-700 inline-block">Book Now</a>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Special Occasion Pooja -->
    <section class="mb-8 sm:mb-16">
        <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Special Occasion Pooja</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 sm:gap-6">
            @foreach($poojas->where('category', 'special') as $pooja)
            <div class="bg-white p-3 sm:p-6 rounded-lg shadow-lg text-center hover:shadow-xl transition">
                <h4 class="font-bold text-sm sm:text-base mb-2">{{ $pooja->title }}</h4>
                <p class="text-xs sm:text-sm text-gray-600 mb-4">{{ Str::limit(strip_tags($pooja->body), 60) }}</p>
                <a href="/pages/{{ $pooja->slug }}" class="bg-orange-600 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded hover:bg-orange-700 text-xs sm:text-sm inline-block">Explore</a>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Pandit Booking -->
    <section>
        <h2 class="text-xl sm:text-3xl font-bold mb-4 sm:mb-8">Pandit Booking</h2>
        <div class="bg-gradient-to-r from-orange-100 to-red-100 p-4 sm:p-8 rounded-lg">
            <div class="text-center mb-6 sm:mb-8">
                <h3 class="text-lg sm:text-2xl font-bold mb-4">Book Experienced Pandits</h3>
                <p class="text-sm sm:text-base text-gray-600">Connect with certified and experienced pandits for all your spiritual needs</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
                <div class="bg-white p-3 sm:p-6 rounded-lg shadow text-center">
                    <div class="w-16 h-16 bg-orange-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="text-white text-xs font-bold">P</span>
                    </div>
                    <h4 class="font-bold text-sm sm:text-base mb-2">Pandit Rajesh Sharma</h4>
                    <p class="text-xs sm:text-sm text-gray-600 mb-2">25+ years experience</p>
                    <p class="text-xs sm:text-sm text-gray-600 mb-4">Specializes in Vedic rituals</p>
                    <button class="bg-orange-600 text-white text-sm sm:text-base px-4 py-2 rounded hover:bg-orange-700">Book Now</button>
                </div>
                <div class="bg-white p-3 sm:p-6 rounded-lg shadow text-center">
                    <div class="w-16 h-16 bg-orange-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="text-white text-xs font-bold">A</span>
                    </div>
                    <h4 class="font-bold text-sm sm:text-base mb-2">Acharya Vikram Singh</h4>
                    <p class="text-xs sm:text-sm text-gray-600 mb-2">20+ years experience</p>
                    <p class="text-xs sm:text-sm text-gray-600 mb-4">Expert in Havan ceremonies</p>
                    <button class="bg-orange-600 text-white text-sm sm:text-base px-4 py-2 rounded hover:bg-orange-700">Book Now</button>
                </div>
                <div class="bg-white p-3 sm:p-6 rounded-lg shadow text-center">
                    <div class="w-16 h-16 bg-orange-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <span class="text-white text-xs font-bold">P</span>
                    </div>
                    <h4 class="font-bold text-sm sm:text-base mb-2">Pandit Suresh Gupta</h4>
                    <p class="text-xs sm:text-sm text-gray-600 mb-2">30+ years experience</p>
                    <p class="text-xs sm:text-sm text-gray-600 mb-4">Marriage ceremony specialist</p>
                    <button class="bg-orange-600 text-white text-sm sm:text-base px-4 py-2 rounded hover:bg-orange-700">Book Now</button>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
