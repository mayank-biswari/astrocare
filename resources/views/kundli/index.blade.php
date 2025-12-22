@extends('layouts.app')

@section('title', 'Kundli Reading Services')

@section('content')
<div class="bg-gradient-to-r from-indigo-900 to-purple-900 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-4">Kundli Reading</h1>
        <p class="text-xl">Discover your destiny through detailed birth chart analysis</p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <div class="grid md:grid-cols-3 gap-8">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-bold mb-4">Basic Kundli</h3>
            <p class="text-gray-600 mb-4">Birth chart with planetary positions and basic predictions.</p>
            <div class="text-2xl font-bold text-indigo-600 mb-4">₹299</div>
            <a href="{{ route('kundli.create') }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 block text-center">Generate</a>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-bold mb-4">Detailed Kundli</h3>
            <p class="text-gray-600 mb-4">Comprehensive analysis with Dasha periods and remedies.</p>
            <div class="text-2xl font-bold text-indigo-600 mb-4">₹599</div>
            <a href="{{ route('kundli.create') }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 block text-center">Generate</a>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-bold mb-4">Premium Kundli</h3>
            <p class="text-gray-600 mb-4">Complete life analysis with yearly predictions and guidance.</p>
            <div class="text-2xl font-bold text-indigo-600 mb-4">₹999</div>
            <a href="{{ route('kundli.create') }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 block text-center">Generate</a>
        </div>
    </div>
</div>
@endsection