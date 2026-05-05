@extends('layouts.app')

@section('title', 'Client Testimonials')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-indigo-900 to-purple-900 text-white py-8 sm:py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-2xl sm:text-5xl font-bold mb-3 sm:mb-6">What Our Clients Say</h1>
        <p class="text-sm sm:text-xl mb-4 sm:mb-8">Read authentic experiences from our satisfied clients who found guidance through our astrology services</p>
    </div>
</section>

<!-- Testimonials Grid -->
<section class="py-8 sm:py-16">
    <div class="container mx-auto px-4">
        @if($testimonials->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-8">
                @foreach($testimonials as $testimonial)
                <div class="bg-white p-3 sm:p-6 rounded-lg shadow-lg">
                    <div class="flex items-center mb-4">
                        @if($testimonial->rating > 0)
                            <div class="text-yellow-500 mr-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $testimonial->rating)
                                        ★
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </div>
                            <span class="text-xs sm:text-sm text-gray-500">{{ number_format($testimonial->rating, 1) }}/5</span>
                        @endif
                    </div>
                    <p class="text-sm sm:text-base text-gray-600 mb-4 italic">"{{ Str::limit(strip_tags($testimonial->body), 150) }}"</p>
                    <div class="border-t pt-4">
                        <h4 class="font-bold text-sm sm:text-base text-indigo-900">{{ $testimonial->title }}</h4>
                        <p class="text-xs sm:text-sm text-gray-500">{{ $testimonial->created_at->format('M d, Y') }}</p>
                        @if(strlen($testimonial->body) > 150)
                            <a href="{{ route('cms.show', $testimonial->slug) }}" class="text-indigo-600 hover:text-indigo-800 text-xs sm:text-sm mt-2 inline-block">
                                Read Full Testimonial →
                            </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8 sm:mt-12">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                    <div class="text-sm sm:text-base text-gray-600">
                        Showing {{ $testimonials->firstItem() ?? 0 }} to {{ $testimonials->lastItem() ?? 0 }}
                        of {{ $testimonials->total() }} testimonials
                    </div>
                    <div class="flex space-x-2">
                        {{ $testimonials->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-8 sm:py-16">
                <div class="text-6xl mb-4">💬</div>
                <h3 class="text-lg sm:text-2xl font-bold text-gray-600 mb-2">No testimonials available</h3>
                <p class="text-sm sm:text-base text-gray-500">Check back later for client testimonials.</p>
            </div>
        @endif
    </div>
</section>

<!-- Call to Action Section -->
<section class="bg-gray-100 py-8 sm:py-16">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 sm:gap-12 items-center">
            <div>
                <h2 class="text-xl sm:text-4xl font-bold mb-4 sm:mb-6">Ready to Experience Our Services?</h2>
                <p class="text-sm sm:text-base text-gray-600 mb-6">Join thousands of satisfied clients who have found guidance through our astrology services. Book your consultation today and discover what the stars have in store for you.</p>
                <div class="space-y-4">
                    <div class="flex items-start space-x-4">
                        <div class="bg-indigo-100 p-2 rounded">
                            <i class="fas fa-star text-indigo-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm sm:text-base">Expert Astrologers</h4>
                            <p class="text-sm sm:text-base text-gray-600">Certified professionals with years of experience</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="bg-indigo-100 p-2 rounded">
                            <i class="fas fa-shield-alt text-indigo-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm sm:text-base">Authentic Guidance</h4>
                            <p class="text-sm sm:text-base text-gray-600">Personalized readings based on your birth chart</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="bg-indigo-100 p-2 rounded">
                            <i class="fas fa-clock text-indigo-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm sm:text-base">24/7 Support</h4>
                            <p class="text-sm sm:text-base text-gray-600">Round-the-clock customer assistance</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                    <a href="{{ route('consultations.index') }}" class="bg-indigo-600 text-white text-sm sm:text-base px-4 sm:px-8 py-2 sm:py-3 rounded-lg hover:bg-indigo-700 inline-block">
                        Book Consultation
                    </a>
                    <a href="{{ route('kundli.create') }}" class="border-2 border-indigo-600 text-indigo-600 text-sm sm:text-base px-4 sm:px-8 py-2 sm:py-3 rounded-lg hover:bg-indigo-600 hover:text-white inline-block">
                        Generate Kundli
                    </a>
                </div>
                <div class="mt-6 sm:mt-8">
                    <div class="w-full h-48 sm:h-80 bg-indigo-600 rounded-lg shadow-lg flex items-center justify-center">
                        <span class="text-white text-base sm:text-2xl font-bold">Trusted by 10,000+ Clients</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
