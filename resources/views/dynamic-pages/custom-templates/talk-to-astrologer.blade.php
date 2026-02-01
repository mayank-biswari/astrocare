@extends('layouts.app')

@section('title', $page->meta_title ?? 'Talk to Astrologer - Connect with Expert Astrologers')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-yellow-50 to-red-50">
    <!-- Hero Section -->
    <div class="gradient-bg text-white py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">{{ $page->title ?? 'Talk to Astrologer' }}</h1>
            <p class="text-lg text-orange-100">Connect with India's best astrologers for instant guidance</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Expertise</label>
                    <select id="expertiseFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Expertise</option>
                        <option value="vedic">Vedic Astrology</option>
                        <option value="numerology">Numerology</option>
                        <option value="tarot">Tarot Reading</option>
                        <option value="vastu">Vastu Shastra</option>
                        <option value="palmistry">Palmistry</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                    <select id="languageFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Languages</option>
                        <option value="hindi">Hindi</option>
                        <option value="english">English</option>
                        <option value="tamil">Tamil</option>
                        <option value="telugu">Telugu</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Experience</label>
                    <select id="experienceFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Experience</option>
                        <option value="5">5+ Years</option>
                        <option value="10">10+ Years</option>
                        <option value="15">15+ Years</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select id="sortFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="rating">Highest Rated</option>
                        <option value="experience">Most Experienced</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Astrologers Grid -->
        <div id="astrologersGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
            $astrologers = [
                ['name' => 'Pandit Rajesh Sharma', 'expertise' => 'Vedic Astrology, Numerology', 'experience' => 15, 'rating' => 4.9, 'consultations' => 5000, 'languages' => 'Hindi, English', 'price' => 25, 'status' => 'online', 'image' => 'https://ui-avatars.com/api/?name=Rajesh+Sharma&size=200&background=FF9933&color=fff'],
                ['name' => 'Dr. Priya Gupta', 'expertise' => 'Tarot Reading, Palmistry', 'experience' => 12, 'rating' => 4.8, 'consultations' => 4200, 'languages' => 'Hindi, English, Tamil', 'price' => 30, 'status' => 'online', 'image' => 'https://ui-avatars.com/api/?name=Priya+Gupta&size=200&background=764ba2&color=fff'],
                ['name' => 'Acharya Vikram Singh', 'expertise' => 'Vedic Astrology, Vastu', 'experience' => 20, 'rating' => 4.9, 'consultations' => 8000, 'languages' => 'Hindi, English', 'price' => 40, 'status' => 'busy', 'image' => 'https://ui-avatars.com/api/?name=Vikram+Singh&size=200&background=DC143C&color=fff'],
                ['name' => 'Swami Anand Kumar', 'expertise' => 'Numerology, Gemology', 'experience' => 18, 'rating' => 4.7, 'consultations' => 6500, 'languages' => 'Hindi, English, Telugu', 'price' => 35, 'status' => 'online', 'image' => 'https://ui-avatars.com/api/?name=Anand+Kumar&size=200&background=FFD700&color=333'],
                ['name' => 'Jyotish Meera Devi', 'expertise' => 'Vedic Astrology, Palmistry', 'experience' => 10, 'rating' => 4.6, 'consultations' => 3500, 'languages' => 'Hindi, English', 'price' => 20, 'status' => 'online', 'image' => 'https://ui-avatars.com/api/?name=Meera+Devi&size=200&background=667eea&color=fff'],
                ['name' => 'Pandit Suresh Joshi', 'expertise' => 'Vastu Shastra, Feng Shui', 'experience' => 14, 'rating' => 4.8, 'consultations' => 4800, 'languages' => 'Hindi, English, Marathi', 'price' => 28, 'status' => 'offline', 'image' => 'https://ui-avatars.com/api/?name=Suresh+Joshi&size=200&background=FF6600&color=fff'],
            ];
            @endphp

            @foreach($astrologers as $astrologer)
            <div class="astrologer-card bg-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden" 
                 data-expertise="{{ strtolower($astrologer['expertise']) }}" 
                 data-experience="{{ $astrologer['experience'] }}" 
                 data-price="{{ $astrologer['price'] }}" 
                 data-rating="{{ $astrologer['rating'] }}">
                <div class="relative">
                    <img src="{{ $astrologer['image'] }}" alt="{{ $astrologer['name'] }}" class="w-full h-48 object-cover">
                    <div class="absolute top-3 right-3">
                        @if($astrologer['status'] === 'online')
                            <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full flex items-center">
                                <span class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></span>Online
                            </span>
                        @elseif($astrologer['status'] === 'busy')
                            <span class="px-3 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full">Busy</span>
                        @else
                            <span class="px-3 py-1 bg-gray-500 text-white text-xs font-bold rounded-full">Offline</span>
                        @endif
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $astrologer['name'] }}</h3>
                    <p class="text-sm text-gray-600 mb-2">{{ $astrologer['expertise'] }}</p>
                    <div class="flex items-center mb-3">
                        <div class="flex items-center text-yellow-500 mr-3">
                            <i class="fas fa-star"></i>
                            <span class="ml-1 text-gray-900 font-semibold">{{ $astrologer['rating'] }}</span>
                        </div>
                        <span class="text-sm text-gray-600">{{ number_format($astrologer['consultations']) }} consultations</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600 mb-2">
                        <i class="fas fa-briefcase mr-2 text-indigo-600"></i>
                        <span>{{ $astrologer['experience'] }} years experience</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600 mb-3">
                        <i class="fas fa-language mr-2 text-indigo-600"></i>
                        <span>{{ $astrologer['languages'] }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t">
                        <div>
                            <span class="text-2xl font-bold text-indigo-600">₹{{ $astrologer['price'] }}</span>
                            <span class="text-sm text-gray-600">/min</span>
                        </div>
                        @if($astrologer['status'] === 'online')
                            <button onclick="startConsultation('{{ $astrologer['name'] }}', {{ $astrologer['price'] }})" 
                                    class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all font-semibold">
                                <i class="fas fa-phone mr-2"></i>Call Now
                            </button>
                        @else
                            <button disabled class="px-4 py-2 bg-gray-300 text-gray-600 rounded-lg cursor-not-allowed">
                                Unavailable
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Why Choose Us -->
        <div class="mt-12 grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-3xl text-green-600"></i>
                </div>
                <h3 class="font-bold text-lg mb-2">100% Verified</h3>
                <p class="text-sm text-gray-600">All astrologers are verified and certified</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-lock text-3xl text-blue-600"></i>
                </div>
                <h3 class="font-bold text-lg mb-2">100% Private</h3>
                <p class="text-sm text-gray-600">Your conversations are completely confidential</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clock text-3xl text-purple-600"></i>
                </div>
                <h3 class="font-bold text-lg mb-2">24/7 Available</h3>
                <p class="text-sm text-gray-600">Connect anytime, anywhere</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-rupee-sign text-3xl text-orange-600"></i>
                </div>
                <h3 class="font-bold text-lg mb-2">Best Prices</h3>
                <p class="text-sm text-gray-600">Affordable rates starting from ₹20/min</p>
            </div>
        </div>
    </div>
</div>

<script>
function startConsultation(name, price) {
    @auth
        Swal.fire({
            title: 'Start Consultation?',
            html: `Connect with <strong>${name}</strong><br>Rate: ₹${price}/min`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Start Call',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route("consultations.index") }}';
            }
        });
    @else
        Swal.fire({
            title: 'Login Required',
            text: 'Please login to start consultation',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#667eea',
            confirmButtonText: 'Login Now'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route("login") }}';
            }
        });
    @endauth
}

// Filter functionality
document.getElementById('expertiseFilter').addEventListener('change', filterAstrologers);
document.getElementById('languageFilter').addEventListener('change', filterAstrologers);
document.getElementById('experienceFilter').addEventListener('change', filterAstrologers);
document.getElementById('sortFilter').addEventListener('change', sortAstrologers);

function filterAstrologers() {
    const expertise = document.getElementById('expertiseFilter').value.toLowerCase();
    const language = document.getElementById('languageFilter').value.toLowerCase();
    const experience = parseInt(document.getElementById('experienceFilter').value) || 0;
    
    document.querySelectorAll('.astrologer-card').forEach(card => {
        const cardExpertise = card.dataset.expertise.toLowerCase();
        const cardExperience = parseInt(card.dataset.experience);
        
        let show = true;
        
        if (expertise && !cardExpertise.includes(expertise)) show = false;
        if (experience && cardExperience < experience) show = false;
        
        card.style.display = show ? 'block' : 'none';
    });
}

function sortAstrologers() {
    const sortBy = document.getElementById('sortFilter').value;
    const grid = document.getElementById('astrologersGrid');
    const cards = Array.from(document.querySelectorAll('.astrologer-card'));
    
    cards.sort((a, b) => {
        switch(sortBy) {
            case 'rating':
                return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
            case 'experience':
                return parseInt(b.dataset.experience) - parseInt(a.dataset.experience);
            case 'price-low':
                return parseInt(a.dataset.price) - parseInt(b.dataset.price);
            case 'price-high':
                return parseInt(b.dataset.price) - parseInt(a.dataset.price);
            default:
                return 0;
        }
    });
    
    cards.forEach(card => grid.appendChild(card));
}
</script>
@endsection
