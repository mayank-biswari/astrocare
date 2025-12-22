@extends('layouts.app')

@section('title', 'Horoscope Matching - Kundli Milan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold mb-4">Horoscope Matching</h1>
            <p class="text-xl text-gray-600">Find your perfect match through Vedic astrology compatibility analysis</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-8">
            <form action="{{ route('horoscope.process') }}" method="POST" class="space-y-8">
                @csrf
                
                <!-- Male Details -->
                <div class="border-b pb-6">
                    <h2 class="text-2xl font-bold mb-6 text-indigo-600">Male Details</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" name="male_name" required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                            <input type="date" name="male_dob" required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Time of Birth</label>
                            <input type="time" name="male_time" required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Place of Birth</label>
                            <input type="text" name="male_place" required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Female Details -->
                <div class="border-b pb-6">
                    <h2 class="text-2xl font-bold mb-6 text-pink-600">Female Details</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" name="female_name" required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                            <input type="date" name="female_dob" required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Time of Birth</label>
                            <input type="time" name="female_time" required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Place of Birth</label>
                            <input type="text" name="female_place" required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                        </div>
                    </div>
                </div>

                <!-- Contact Details -->
                <div>
                    <h2 class="text-2xl font-bold mb-6">Contact Details</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" name="phone" required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-indigo-700 text-lg">
                        Get Compatibility Report - ₹999
                    </button>
                </div>
            </form>
        </div>

        <!-- Features -->
        <div class="mt-12 grid md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold mb-2">36 Guna Matching</h3>
                <p class="text-gray-600">Complete Ashtakoot analysis for perfect compatibility</p>
            </div>
            <div class="text-center">
                <div class="bg-pink-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold mb-2">Mangal Dosha Check</h3>
                <p class="text-gray-600">Detailed analysis of Mars placement and remedies</p>
            </div>
            <div class="text-center">
                <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold mb-2">Detailed Report</h3>
                <p class="text-gray-600">Comprehensive compatibility analysis with remedies</p>
            </div>
        </div>
    </div>
</div>
@endsection