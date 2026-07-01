@extends('layouts.app')

@section('title', 'Kundli Reading Services')

@section('content')
<div class="relative overflow-hidden text-white py-16 lg:py-32" style="background: url('{{ asset('images/kundali-service-bg.jpg') }}') center/cover no-repeat;">
    <div class="absolute inset-0 bg-indigo-900/70"></div>
    <div class="container mx-auto px-4 text-center relative z-10">
        <h1 class="text-4xl font-bold mb-4">Kundli Reading</h1>
        <p class="text-xl">Discover your destiny through detailed birth chart analysis</p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <div class="grid md:grid-cols-3 gap-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <img src="{{ asset('images/ku_ca_bg_hjk32h4kj23h424.jpg') }}" alt="Basic Kundli" class="w-full h-48 object-cover">
            <div class="p-6">
                <h3 class="text-xl font-bold mb-4">Basic Kundli</h3>
                <ul class="text-gray-600 mb-4 space-y-1">
                    <li class="flex items-center"><span class="text-green-500 mr-2">✔</span> Birth chart</li>
                    <li class="flex items-center"><span class="text-green-500 mr-2">✔</span> Planetary positions</li>
                    <li class="flex items-center"><span class="text-green-500 mr-2">✔</span> Basic predictions</li>
                    <li class="flex items-center"><span class="text-red-500 mr-2">✘</span> Dasha analysis</li>
                    <li class="flex items-center"><span class="text-red-500 mr-2">✘</span> Remedies</li>
                    <li class="flex items-center"><span class="text-red-500 mr-2">✘</span> Career guidance</li>
                    <li class="flex items-center"><span class="text-red-500 mr-2">✘</span> Marriage compatibility</li>
                    <li class="flex items-center"><span class="text-red-500 mr-2">✘</span> Health analysis</li>
                    <li class="flex items-center"><span class="text-red-500 mr-2">✘</span> Yearly predictions</li>
                </ul>
                <div class="text-2xl font-bold text-indigo-600 mb-4">{{ formatPrice(299) }}</div>
                <a href="{{ route('kundli.create') }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 block text-center">Generate</a>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <img src="{{ asset('images/ku_ca_bg_hjk32h4kj23h424.jpg') }}" alt="Detailed Kundli" class="w-full h-48 object-cover">
            <div class="p-6">
                <h3 class="text-xl font-bold mb-4">Detailed Kundli</h3>
                <ul class="text-gray-600 mb-4 space-y-1">
                    <li class="flex items-center"><span class="text-green-500 mr-2">✔</span> Birth chart</li>
                    <li class="flex items-center"><span class="text-green-500 mr-2">✔</span> Planetary positions</li>
                    <li class="flex items-center"><span class="text-green-500 mr-2">✔</span> Basic predictions</li>
                    <li class="flex items-center"><span class="text-green-500 mr-2">✔</span> Dasha analysis</li>
                    <li class="flex items-center"><span class="text-green-500 mr-2">✔</span> Remedies</li>
                    <li class="flex items-center"><span class="text-green-500 mr-2">✔</span> Career guidance</li>
                    <li class="flex items-center"><span class="text-red-500 mr-2">✘</span> Marriage compatibility</li>
                    <li class="flex items-center"><span class="text-red-500 mr-2">✘</span> Health analysis</li>
                    <li class="flex items-center"><span class="text-red-500 mr-2">✘</span> Yearly predictions</li>
                </ul>
                <div class="text-2xl font-bold text-indigo-600 mb-4">{{ formatPrice(599) }}</div>
                <a href="{{ route('kundli.create') }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 block text-center">Generate</a>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <img src="{{ asset('images/ku_ca_bg_hjk32h4kj23h424.jpg') }}" alt="Premium Kundli" class="w-full h-48 object-cover">
            <div class="p-6">
                <h3 class="text-xl font-bold mb-4">Premium Kundli</h3>
                <ul class="text-gray-600 mb-4 space-y-1">
                    <li class="flex items-center"><span class="text-green-500 mr-2">✔</span> Birth chart</li>
                    <li class="flex items-center"><span class="text-green-500 mr-2">✔</span> Planetary positions</li>
                    <li class="flex items-center"><span class="text-green-500 mr-2">✔</span> Basic predictions</li>
                    <li class="flex items-center"><span class="text-green-500 mr-2">✔</span> Dasha analysis</li>
                    <li class="flex items-center"><span class="text-green-500 mr-2">✔</span> Remedies</li>
                    <li class="flex items-center"><span class="text-green-500 mr-2">✔</span> Career guidance</li>
                    <li class="flex items-center"><span class="text-green-500 mr-2">✔</span> Marriage compatibility</li>
                    <li class="flex items-center"><span class="text-green-500 mr-2">✔</span> Health analysis</li>
                    <li class="flex items-center"><span class="text-green-500 mr-2">✔</span> Yearly predictions</li>
                </ul>
                <div class="text-2xl font-bold text-indigo-600 mb-4">{{ formatPrice(999) }}</div>
                <a href="{{ route('kundli.create') }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 block text-center">Generate</a>
            </div>
        </div>
    </div>
</div>
@endsection
