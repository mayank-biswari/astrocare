@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-center mb-8">Contact Us</h1>
        
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-center">
            <div class="w-full {{ $contactInfo['show_contact_info'] ? 'grid md:grid-cols-2 gap-8' : 'max-w-2xl' }}">
                <!-- Contact Form -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-bold mb-6">Send us a Message</h2>
                
                <form action="{{ route('contact.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" 
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-indigo-500 @error('name') border-red-500 @enderror" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" 
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-indigo-500 @error('email') border-red-500 @enderror" required>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Phone</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" 
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-indigo-500 @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Subject *</label>
                        <input type="text" name="subject" value="{{ old('subject') }}" 
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-indigo-500 @error('subject') border-red-500 @enderror" required>
                        @error('subject')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Message *</label>
                        <textarea name="message" rows="5" 
                                  class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-indigo-500 @error('message') border-red-500 @enderror" required>{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Captcha *</label>
                        <div class="flex items-center space-x-4">
                            <img src="{{ captcha_src() }}" alt="Captcha" class="border rounded">
                            <button type="button" onclick="this.previousElementSibling.src='{{ captcha_src() }}?'+Math.random()" 
                                    class="text-indigo-600 hover:text-indigo-800">Refresh</button>
                        </div>
                        <input type="text" name="captcha" 
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-indigo-500 mt-2 @error('captcha') border-red-500 @enderror" 
                               placeholder="Enter captcha" required>
                        @error('captcha')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition duration-200">
                        Send Message
                    </button>
                </form>
                </div>

                @if($contactInfo['show_contact_info'])
                <!-- Contact Information -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-bold mb-6">Get in Touch</h2>
                
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-map-marker-alt text-indigo-600 mt-1"></i>
                        <div>
                            <h4 class="font-bold">Address</h4>
                            <p class="text-gray-600">{!! nl2br(e($contactInfo['address'])) !!}</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <i class="fas fa-phone text-indigo-600 mt-1"></i>
                        <div>
                            <h4 class="font-bold">Phone</h4>
                            <p class="text-gray-600">{{ $contactInfo['phone'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <i class="fas fa-envelope text-indigo-600 mt-1"></i>
                        <div>
                            <h4 class="font-bold">Email</h4>
                            <p class="text-gray-600">{{ $contactInfo['email'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <i class="fas fa-clock text-indigo-600 mt-1"></i>
                        <div>
                            <h4 class="font-bold">Business Hours</h4>
                            <p class="text-gray-600">{!! nl2br(e($contactInfo['business_hours'])) !!}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="text-xl font-bold mb-4">Follow Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-indigo-600 hover:text-indigo-800">
                            <i class="fab fa-facebook-f text-2xl"></i>
                        </a>
                        <a href="#" class="text-indigo-600 hover:text-indigo-800">
                            <i class="fab fa-twitter text-2xl"></i>
                        </a>
                        <a href="#" class="text-indigo-600 hover:text-indigo-800">
                            <i class="fab fa-instagram text-2xl"></i>
                        </a>
                        <a href="#" class="text-indigo-600 hover:text-indigo-800">
                            <i class="fab fa-youtube text-2xl"></i>
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection