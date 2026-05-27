@extends('layouts.app')

@section('title', $service->meta_title ?? 'Astrology Consultations')
@section('meta_description', $service->meta_description ?? 'Get personalized guidance from expert astrologers')
@section('meta_keywords', $service->meta_keywords ?? '')

@section('content')
<div class="bg-gradient-to-r from-indigo-900 to-purple-900 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        @if($service->image)
            <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}" class="mx-auto mb-6 rounded-lg shadow-lg" style="max-height: 200px;">
        @elseif($service->icon)
            <div class="text-6xl mb-4"><i class="{{ $service->icon }}"></i></div>
        @endif
        <h1 class="text-4xl font-bold mb-4">{{ $service->name }}</h1>
        <p class="text-xl">{{ $service->short_description }}</p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    {{-- Service Description --}}
    @if($service->description)
    <div class="max-w-3xl mx-auto mb-12 text-center">
        <div class="text-gray-700 leading-relaxed">{!! $service->description !!}</div>
    </div>
    @endif

    {{-- Pricing Tiers --}}
    @if($service->has_tiers && $service->tiers->count() > 0)
    <h2 class="text-3xl font-bold text-center mb-8">Choose Your Session</h2>
    <div class="grid md:grid-cols-{{ min($service->tiers->where('is_active', true)->count(), 3) }} gap-8 max-w-5xl mx-auto mb-12">
        @foreach($service->tiers->where('is_active', true)->sortBy('sort_order') as $tier)
        <div class="bg-white p-6 rounded-lg shadow-lg border-2 border-transparent hover:border-indigo-500 transition-all">
            <h3 class="text-xl font-bold mb-2 text-center">{{ $tier->name }}</h3>
            @if($tier->description)
                <p class="text-gray-600 mb-4 text-center">{{ $tier->description }}</p>
            @endif
            <div class="text-3xl font-bold text-indigo-600 mb-4 text-center">{{ formatPrice($tier->price) }}</div>
            @if($tier->features && count($tier->features) > 0)
                <ul class="text-sm text-gray-600 mb-6 space-y-2">
                    @foreach($tier->features as $feature)
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
            @endif
            <a href="{{ route('consultations.show', 'consultation') }}?tier={{ $tier->id }}"
               class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 block text-center font-semibold">
                Book Now
            </a>
        </div>
        @endforeach
    </div>
    @else
    {{-- Single price display --}}
    <div class="text-center mb-12">
        <div class="text-3xl font-bold text-indigo-600 mb-4">{{ formatPrice($service->base_price) }}/session</div>
        <a href="{{ route('consultations.show', 'consultation') }}"
           class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 inline-block font-semibold">
            Book Now
        </a>
    </div>
    @endif

    {{-- Features --}}
    @if($service->features && count($service->features) > 0)
    <div class="max-w-3xl mx-auto mb-12">
        <h2 class="text-2xl font-bold text-center mb-6">What You Get</h2>
        <div class="grid md:grid-cols-2 gap-4">
            @foreach($service->features as $feature)
            <div class="flex items-start p-4 bg-white rounded-lg shadow">
                <svg class="w-6 h-6 text-indigo-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="text-gray-700">{{ $feature }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- FAQ --}}
    @if($service->faq && count($service->faq) > 0)
    <div class="max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold text-center mb-6">Frequently Asked Questions</h2>
        <div class="space-y-4">
            @foreach($service->faq as $item)
            <div class="bg-white rounded-lg shadow p-4">
                <button class="faq-toggle w-full text-left flex justify-between items-center font-semibold text-gray-800" onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('svg').classList.toggle('rotate-180')">
                    <span>{{ $item['question'] }}</span>
                    <svg class="w-5 h-5 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="hidden mt-3 text-gray-600 border-t pt-3">
                    {{ $item['answer'] }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Delivery Time --}}
    @if($service->delivery_time)
    <div class="text-center mt-8 text-gray-500">
        <i class="fas fa-clock mr-1"></i> {{ $service->delivery_time }}
    </div>
    @endif
</div>
@endsection
