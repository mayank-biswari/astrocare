@extends('layouts.app')

@section('title', $page->meta_title ?? $page->title)
@section('meta_description', $page->meta_description ?? '')
@section('meta_keywords', $page->meta_keywords ?? '')

@section('content')
<div class="bg-gradient-to-b from-orange-50 to-white min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        <nav class="text-sm text-gray-600 mb-6 flex items-center gap-2">
            <a href="/" class="hover:text-orange-600 transition">Home</a>
            <span class="text-gray-400">›</span>
            <a href="/pooja" class="hover:text-orange-600 transition">Pooja & Rituals</a>
            <span class="text-gray-400">›</span>
            <span class="text-orange-600 font-medium">{{ $page->title }}</span>
        </nav>

        <div class="grid lg:grid-cols-5 gap-8">
            <!-- Left: Pooja Details -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Image Card -->
                @if($page->image)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <img src="{{ asset('storage/' . $page->image) }}" alt="{{ $page->title }}" class="w-full h-80 object-cover">
                </div>
                @endif
                
                <!-- Title & Price Card -->
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl shadow-lg p-8 text-white">
                    <h1 class="text-4xl font-bold mb-4">{{ $page->title }}</h1>
                    <div class="flex items-center gap-4">
                        <div class="bg-white/20 backdrop-blur-sm rounded-xl px-6 py-3">
                            <div class="text-sm opacity-90">Starting from</div>
                            <div class="text-3xl font-bold">{{ formatPrice($page->custom_fields['price'] ?? 0) }}</div>
                        </div>
                        @if($page->custom_fields['duration'] ?? null)
                        <div class="bg-white/20 backdrop-blur-sm rounded-xl px-6 py-3">
                            <div class="text-sm opacity-90">Duration</div>
                            <div class="text-2xl font-bold">{{ $page->custom_fields['duration'] }} min</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- About Section -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-book-open text-orange-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">About This Pooja</h3>
                    </div>
                    <div class="text-gray-700 leading-relaxed text-lg">{!! $page->body !!}</div>
                </div>

                <!-- What's Included -->
                @if($page->custom_fields['includes'] ?? null)
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">What's Included</h3>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach(explode(',', $page->custom_fields['includes']) as $item)
                            <div class="flex items-start gap-3 bg-green-50 rounded-lg p-3">
                                <span class="text-green-600 text-xl mt-0.5">✓</span>
                                <span class="text-gray-700">{{ trim($item) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Benefits -->
                @if($page->custom_fields['benefits'] ?? null)
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl shadow-lg p-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-star text-purple-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">Benefits</h3>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach(explode(',', $page->custom_fields['benefits']) as $benefit)
                            <div class="flex items-start gap-3 bg-white rounded-lg p-3 shadow-sm">
                                <span class="text-purple-600 text-xl mt-0.5">•</span>
                                <span class="text-gray-700">{{ trim($benefit) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Ritual Info -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl shadow-lg p-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-info-circle text-blue-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">Ritual Information</h3>
                    </div>
                    <p class="text-gray-700 leading-relaxed">Performed by experienced temple pandits with authentic Vedic procedures following traditional scriptures and mantras.</p>
                </div>
            </div>

            <!-- Right: Booking Form -->
            <div class="lg:col-span-2">
                <div class="sticky top-8">
                    <div class="bg-white rounded-2xl shadow-2xl p-8 border-2 border-orange-100">
                        <div class="text-center mb-6">
                            <div class="inline-block bg-orange-100 rounded-full p-4 mb-4">
                                <i class="fas fa-calendar-alt text-orange-600 text-3xl"></i>
                            </div>
                            <h2 class="text-3xl font-bold text-gray-800 mb-2">Book This Pooja</h2>
                            <p class="text-gray-600">Fill the form to proceed with booking</p>
                        </div>
                        
                        <form action="{{ route('pooja.book') }}" method="POST" class="space-y-5">
                            @csrf
                            <input type="hidden" name="name" value="{{ $page->title }}">
                            <input type="hidden" name="type" value="{{ $page->custom_fields['category'] ?? 'pooja' }}">
                            <input type="hidden" name="amount" value="{{ $page->custom_fields['price'] ?? 0 }}">

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Your Name</label>
                                <input type="text" name="devotee_name" value="{{ auth()->user()->name ?? '' }}" required
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" name="phone" value="{{ auth()->user()->phone ?? '' }}" required
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                                <input type="email" name="email" value="{{ auth()->user()->email ?? '' }}"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Preferred Date & Time</label>
                                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required
                                       min="{{ now()->format('Y-m-d\TH:i') }}"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Gotra (Optional)</label>
                                <input type="text" name="gotra" value="{{ old('gotra') }}"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Special Requests</label>
                                <textarea name="special_requirements" rows="3"
                                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
                                          placeholder="Any specific prayers or requirements...">{{ old('special_requirements') }}</textarea>
                            </div>

                            <!-- Captcha -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Security Check</label>
                                <div class="flex items-center gap-3 mb-3">
                                    <img src="{{ captcha_src() }}" alt="Captcha" class="border-2 border-gray-200 rounded-lg">
                                    <button type="button" onclick="this.previousElementSibling.src='{{ captcha_src() }}?'+Math.random()" 
                                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition font-medium">Refresh</button>
                                </div>
                                <input type="text" name="captcha" required 
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
                                       placeholder="Enter captcha">
                                @error('captcha')
                                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="bg-gradient-to-r from-orange-50 to-yellow-50 rounded-xl p-5 border-2 border-orange-200">
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-gray-700 font-semibold">Total Amount:</span>
                                    <span class="text-3xl font-bold text-orange-600">{{ formatPrice($page->custom_fields['price'] ?? 0) }}</span>
                                </div>
                                <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white py-4 px-6 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition transform hover:scale-105">
                                    Book Pooja & Pay {{ formatPrice($page->custom_fields['price'] ?? 0) }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
