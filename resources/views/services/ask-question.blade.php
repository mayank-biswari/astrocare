@extends('layouts.app')

@section('title', 'Ask a Question to Expert Astrologer | Get Personalized Astrological Guidance')
@section('meta_description', 'Get expert astrological guidance for your life questions. Ask about career, love, marriage, health, finance. Detailed written response within 24-48 hours. ₹499 only.')
@section('meta_keywords', 'ask astrologer, astrology question, astrological guidance, career astrology, love astrology, marriage prediction, astrology consultation')

@section('content')
<div class="bg-gradient-to-b from-orange-50 to-white min-h-screen py-8 sm:py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Hero Section -->
            <div class="text-center mb-8 sm:mb-12">
                <div class="inline-block bg-orange-100 rounded-full px-4 py-2 mb-4">
                    <span class="text-orange-600 font-semibold text-sm">✨ Expert Astrological Guidance</span>
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-4 bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent">Ask Your Question to Expert Astrologer</h1>
                <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto">Get personalized astrological insights and practical remedies for your life's most important questions</p>
            </div>

            <!-- Benefits Section -->
            <div class="grid sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-4 text-center border border-orange-100">
                    <div class="text-3xl mb-2">⏱️</div>
                    <h3 class="font-bold text-gray-800 mb-1">24-48 Hours</h3>
                    <p class="text-sm text-gray-600">Quick Response</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 text-center border border-orange-100">
                    <div class="text-3xl mb-2">👨‍🏫</div>
                    <h3 class="font-bold text-gray-800 mb-1">Expert Astrologers</h3>
                    <p class="text-sm text-gray-600">Verified Professionals</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 text-center border border-orange-100">
                    <div class="text-3xl mb-2">💯</div>
                    <h3 class="font-bold text-gray-800 mb-1">Detailed Analysis</h3>
                    <p class="text-sm text-gray-600">Personalized Remedies</p>
                </div>
            </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 border border-orange-100">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Submit Your Question</h2>
                <p class="text-gray-600">Fill in your details to receive personalized astrological guidance</p>
            </div>
            
            <form action="{{ route('ask.submit') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', session('question_data.name', auth()->user()->name ?? '')) }}" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', session('question_data.email', auth()->user()->email ?? '')) }}" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" value="{{ old('phone', session('question_data.phone', auth()->user()->phone ?? '')) }}" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth <span class="text-red-500">*</span></label>
                        <input type="date" name="dob" value="{{ old('dob', session('question_data.dob')) }}" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Time of Birth</label>
                        <input type="time" name="time" value="{{ old('time', session('question_data.time')) }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Place of Birth</label>
                        <input type="text" name="place" value="{{ old('place', session('question_data.place')) }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Question Category <span class="text-red-500">*</span></label>
                    <select name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="">Select Category</option>
                        <option value="career" {{ old('category', session('question_data.category')) == 'career' ? 'selected' : '' }}>Career & Business</option>
                        <option value="love" {{ old('category', session('question_data.category')) == 'love' ? 'selected' : '' }}>Love & Relationships</option>
                        <option value="marriage" {{ old('category', session('question_data.category')) == 'marriage' ? 'selected' : '' }}>Marriage & Family</option>
                        <option value="health" {{ old('category', session('question_data.category')) == 'health' ? 'selected' : '' }}>Health & Wellness</option>
                        <option value="finance" {{ old('category', session('question_data.category')) == 'finance' ? 'selected' : '' }}>Finance & Money</option>
                        <option value="education" {{ old('category', session('question_data.category')) == 'education' ? 'selected' : '' }}>Education & Studies</option>
                        <option value="general" {{ old('category', session('question_data.category')) == 'general' ? 'selected' : '' }}>General Life Guidance</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Your Question <span class="text-red-500">*</span></label>
                    <textarea name="question" rows="5" required 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                              placeholder="Please describe your question in detail. The more specific you are, the better guidance we can provide.">{{ old('question', session('question_data.question')) }}</textarea>
                </div>

                <div class="bg-gradient-to-r from-orange-50 to-purple-50 p-6 rounded-xl border border-orange-200">
                    <h3 class="font-bold text-orange-900 mb-3 flex items-center">
                        <span class="text-2xl mr-2">✨</span> What You'll Receive:
                    </h3>
                    <ul class="text-orange-800 space-y-2">
                        <li class="flex items-start">
                            <span class="text-green-600 mr-2">✓</span>
                            <span>Detailed written response within 24-48 hours</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-600 mr-2">✓</span>
                            <span>Personalized astrological analysis based on your birth chart</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-600 mr-2">✓</span>
                            <span>Practical remedies and actionable suggestions</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-600 mr-2">✓</span>
                            <span>Follow-up support via email for clarifications</span>
                        </li>
                    </ul>
                </div>

                <div class="text-center bg-gradient-to-r from-orange-600 to-red-600 rounded-xl p-6 text-white">
                    <div class="mb-4">
                        <span class="text-sm opacity-90">Special Offer Price</span>
                        <div class="text-4xl font-bold">₹499</div>
                        <span class="text-sm opacity-90">One-time payment</span>
                    </div>
                    <button type="submit" class="w-full bg-white text-orange-600 px-8 py-4 rounded-lg font-bold hover:bg-gray-100 text-lg shadow-lg transform hover:scale-105 transition">
                        Continue to Secure Checkout →
                    </button>
                    <p class="text-xs mt-3 opacity-75">🔒 Secure payment | 100% confidential</p>
                </div>
            </form>
        </div>

        <!-- FAQ Section -->
        <div class="mt-12">
            <h2 class="text-2xl sm:text-3xl font-bold text-center mb-8 text-gray-800">Frequently Asked Questions</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                    <div class="flex items-start">
                        <span class="text-2xl mr-3">⏰</span>
                        <div>
                            <h3 class="font-bold mb-2 text-gray-800">How long does it take to get an answer?</h3>
                            <p class="text-gray-600 text-sm">You will receive a detailed written response within 24-48 hours of submitting your question.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                    <div class="flex items-start">
                        <span class="text-2xl mr-3">❓</span>
                        <div>
                            <h3 class="font-bold mb-2 text-gray-800">Can I ask multiple questions?</h3>
                            <p class="text-gray-600 text-sm">Each submission covers one main question. For multiple questions, please submit separate forms.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                    <div class="flex items-start">
                        <span class="text-2xl mr-3">🕒</span>
                        <div>
                            <h3 class="font-bold mb-2 text-gray-800">Do I need exact birth time?</h3>
                            <p class="text-gray-600 text-sm">While exact birth time helps provide more accurate predictions, we can still provide guidance with approximate time or date only.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                    <div class="flex items-start">
                        <span class="text-2xl mr-3">🔒</span>
                        <div>
                            <h3 class="font-bold mb-2 text-gray-800">Is my information confidential?</h3>
                            <p class="text-gray-600 text-sm">Yes, all your personal information and questions are kept strictly confidential and secure.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
