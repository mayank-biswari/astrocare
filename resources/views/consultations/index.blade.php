@extends('layouts.app')

@section('title', 'Astrology Consultations')

@section('content')
<div class="bg-gradient-to-r from-indigo-900 to-purple-900 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-4">Astrology Consultations</h1>
        <p class="text-xl">Get personalized guidance from expert astrologers</p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <div class="grid md:grid-cols-3 gap-8">
        @foreach($services as $service)
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="text-4xl mb-4 text-center">
                @if(str_contains($service->name, 'Chat'))
                    💬
                @elseif(str_contains($service->name, 'Video'))
                    📹
                @else
                    📞
                @endif
            </div>
            <h3 class="text-xl font-bold mb-4">{{ $service->name }}</h3>
            <p class="text-gray-600 mb-4">{{ $service->description }}</p>
            <div class="text-2xl font-bold text-indigo-600 mb-4">{{ formatPrice($service->price) }}/session</div>
            <a href="{{ route('consultations.show', strtolower(str_replace(' ', '-', explode(' ', $service->name)[0]))) }}" 
               class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 block text-center">Book Now</a>
        </div>
        @endforeach
    </div>
</div>
@endsection