@extends('layouts.app')

@section('title', 'Astrological Predictions - Future Insights')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold mb-4">Astrological Predictions</h1>
            <p class="text-xl text-gray-600">Discover what the stars have in store for your future</p>
        </div>

        <!-- Prediction Types -->
        <div class="grid md:grid-cols-3 gap-8 mb-12">
            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-alt text-2xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Daily Predictions</h3>
                <p class="text-gray-600 mb-4">Get daily insights based on your zodiac sign</p>
                <div class="text-2xl font-bold text-blue-600 mb-4">Free</div>
                <a href="#daily" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">View Today's</a>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chart-line text-2xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Monthly Forecast</h3>
                <p class="text-gray-600 mb-4">Detailed monthly predictions and guidance</p>
                <div class="text-2xl font-bold text-green-600 mb-4">{{ formatPrice(299) }}</div>
                <a href="#monthly" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">Get Report</a>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-crystal-ball text-2xl text-purple-600"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Yearly Predictions</h3>
                <p class="text-gray-600 mb-4">Complete year ahead analysis and remedies</p>
                <div class="text-2xl font-bold text-purple-600 mb-4">{{ formatPrice(999) }}</div>
                <a href="#yearly" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700">Order Now</a>
            </div>
        </div>

        <!-- Daily Predictions Section -->
        <div id="daily" class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold mb-6 text-center">Today's Predictions</h2>
            <div class="grid md:grid-cols-4 gap-4">
                @php
                $signs = [
                    'aries' => 'Aries', 'taurus' => 'Taurus', 'gemini' => 'Gemini', 'cancer' => 'Cancer',
                    'leo' => 'Leo', 'virgo' => 'Virgo', 'libra' => 'Libra', 'scorpio' => 'Scorpio',
                    'sagittarius' => 'Sagittarius', 'capricorn' => 'Capricorn', 'aquarius' => 'Aquarius', 'pisces' => 'Pisces'
                ];
                @endphp
                @foreach($signs as $key => $sign)
                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                    <h3 class="font-bold text-center mb-2">{{ $sign }}</h3>
                    <p class="text-sm text-gray-600 text-center">Today brings positive energy and new opportunities in your career sector.</p>
                    <div class="text-center mt-2">
                        <span class="text-yellow-500">★★★★☆</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Monthly Forecast Form -->
        <div id="monthly" class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold mb-6 text-center">Monthly Forecast</h2>
            <form action="{{ route('predictions.monthly') }}" method="POST" class="max-w-2xl mx-auto">
                @csrf
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                        <input type="date" name="dob" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Time of Birth</label>
                        <input type="time" name="time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                </div>
                <div class="mt-6 text-center">
                    <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-green-700">
                        Get Monthly Forecast - {{ formatPrice(299) }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Yearly Predictions Form -->
        <div id="yearly" class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-6 text-center">Yearly Predictions</h2>
            <form action="{{ route('predictions.yearly') }}" method="POST" class="max-w-2xl mx-auto">
                @csrf
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                        <input type="date" name="dob" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Time of Birth</label>
                        <input type="time" name="time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Place of Birth</label>
                        <input type="text" name="place" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                <div class="mt-6 text-center">
                    <button type="submit" class="bg-purple-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-purple-700">
                        Get Yearly Predictions - {{ formatPrice(999) }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection