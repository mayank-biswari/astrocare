@extends('layouts.app')

@section('title', 'Pooja Details - Book Now')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <nav class="text-sm text-gray-500 mb-6">
            <a href="{{ route('pooja.index') }}" class="hover:text-orange-600">Pooja & Rituals</a> > 
            {{ $pooja->name }}
        </nav>

        <div class="grid md:grid-cols-2 gap-12">
            <!-- Pooja Details -->
            <div>
                <div class="text-6xl mb-6 text-center">{{ $pooja->icon }}</div>
                <h1 class="text-3xl font-bold mb-6">{{ $pooja->name }}</h1>
                <div class="text-3xl font-bold text-orange-600 mb-6">{{ formatPrice($pooja->price) }}</div>
                
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3">About This Pooja</h3>
                    <p class="text-gray-600 mb-4">{{ $pooja->description }}</p>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3">What's Included</h3>
                    <ul class="text-gray-600 space-y-2">
                        @foreach($pooja->includes as $item)
                            <li>• {{ $item }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-3">Benefits</h3>
                    <ul class="text-gray-600 space-y-2">
                        @foreach($pooja->benefits as $benefit)
                            <li>• {{ $benefit }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="bg-orange-50 p-4 rounded-lg">
                    <h4 class="font-bold mb-2">Duration: {{ $pooja->duration }} minutes</h4>
                    <p class="text-sm text-gray-600">Performed by experienced temple pandits with authentic Vedic procedures</p>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold mb-6">Book This Pooja</h2>
                
                <form action="{{ route('pooja.book') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="name" value="{{ $pooja->name }}">
                    <input type="hidden" name="type" value="{{ $pooja->category }}">
                    <input type="hidden" name="amount" value="{{ $pooja->price }}">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Name</label>
                        <input type="text" name="devotee_name" value="{{ auth()->user()->name ?? '' }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="phone" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ auth()->user()->email ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Date & Time</label>
                        <input type="datetime-local" name="scheduled_at" required
                               min="{{ now()->format('Y-m-d\TH:i') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gotra (Optional)</label>
                        <input type="text" name="gotra" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Special Requests</label>
                        <textarea name="special_requirements" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                                  placeholder="Any specific prayers or requirements..."></textarea>
                    </div>

                    <!-- Captcha -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Security Check</label>
                        <div class="flex items-center space-x-4 mb-2">
                            <img src="{{ captcha_src() }}" alt="Captcha" class="border rounded">
                            <button type="button" onclick="this.previousElementSibling.src='{{ captcha_src() }}?'+Math.random()" 
                                    class="text-orange-600 hover:text-orange-800 text-sm">Refresh</button>
                        </div>
                        <input type="text" name="captcha" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                               placeholder="Enter captcha">
                        @error('captcha')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-lg">Total Amount:</span>
                            <span class="text-2xl font-bold text-orange-600">{{ formatPrice($pooja->price) }}</span>
                        </div>
                        <button type="submit" class="w-full bg-orange-600 text-white py-3 px-6 rounded-lg font-bold hover:bg-orange-700">
                            Book Pooja & Pay {{ formatPrice($pooja->price) }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection