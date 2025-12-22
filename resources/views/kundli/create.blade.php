@extends('layouts.app')

@section('title', 'Generate Kundli - Astrology Services')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-center mb-8">Generate Your Kundli</h1>
            
            <form action="{{ route('kundli.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" id="name" name="name" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">Birth Date</label>
                        <input type="date" id="birth_date" name="birth_date" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="birth_time" class="block text-sm font-medium text-gray-700 mb-2">Birth Time</label>
                        <input type="time" id="birth_time" name="birth_time" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="birth_place" class="block text-sm font-medium text-gray-700 mb-2">Birth Place</label>
                    <input type="text" id="birth_place" name="birth_place" required 
                           placeholder="City, State, Country"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Kundli Type</label>
                    <select id="type" name="type" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Kundli Type</option>
                        <option value="basic">Basic Kundli - ₹299</option>
                        <option value="detailed">Detailed Kundli - ₹599</option>
                        <option value="premium">Premium Kundli - ₹999</option>
                    </select>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-bold mb-2">What's Included:</h3>
                    <div class="text-sm text-gray-600 space-y-1">
                        <div><strong>Basic:</strong> Birth chart, planetary positions, basic predictions</div>
                        <div><strong>Detailed:</strong> Everything in Basic + Dasha analysis, remedies, career guidance</div>
                        <div><strong>Premium:</strong> Everything in Detailed + Marriage compatibility, health analysis, yearly predictions</div>
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg font-bold hover:bg-indigo-700 transition duration-200">
                    Generate Kundli & Proceed to Payment
                </button>
            </form>
        </div>
    </div>
</div>
@endsection