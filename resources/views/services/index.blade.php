@extends('layouts.app')

@section('title', 'Astrology Services')

@section('content')
<div class="bg-gradient-to-r from-indigo-900 to-purple-900 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-4">{{ __('messages.astrology_services') }}</h1>
        <p class="text-xl">{{ __('messages.discover_path') }}</p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <!-- Consultation Services -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold mb-8">{{ __('messages.astrology_consultation') }}</h2>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="text-4xl mb-4 text-center">💬</div>
                <h3 class="text-xl font-bold mb-4">{{ __('messages.chat_consultation') }}</h3>
                <p class="text-gray-600 mb-4">{{ __('messages.chat_desc') }}</p>
                <div class="text-2xl font-bold text-indigo-600 mb-4">₹299/{{ __('messages.session') }}</div>
                <a href="{{ route('consultations.show', 'chat') }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 block text-center">{{ __('messages.book_now') }}</a>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="text-4xl mb-4 text-center">📹</div>
                <h3 class="text-xl font-bold mb-4">{{ __('messages.video_consultation') }}</h3>
                <p class="text-gray-600 mb-4">{{ __('messages.video_desc') }}</p>
                <div class="text-2xl font-bold text-indigo-600 mb-4">₹599/{{ __('messages.session') }}</div>
                <a href="{{ route('consultations.show', 'video') }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 block text-center">{{ __('messages.book_now') }}</a>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="text-4xl mb-4 text-center">📞</div>
                <h3 class="text-xl font-bold mb-4">{{ __('messages.phone_consultation') }}</h3>
                <p class="text-gray-600 mb-4">{{ __('messages.phone_desc') }}</p>
                <div class="text-2xl font-bold text-indigo-600 mb-4">₹499/{{ __('messages.session') }}</div>
                <a href="{{ route('consultations.show', 'phone') }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 block text-center">{{ __('messages.book_now') }}</a>
            </div>
        </div>
    </section>

    <!-- Kundli Services -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold mb-8">{{ __('messages.kundli_reading') }}</h2>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold mb-4">{{ __('messages.basic_kundli') }}</h3>
                <p class="text-gray-600 mb-4">{{ __('messages.basic_kundli_desc') }}</p>
                <div class="text-2xl font-bold text-indigo-600 mb-4">₹299</div>
                <a href="{{ route('kundli.create') }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 block text-center">{{ __('messages.generate') }}</a>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold mb-4">{{ __('messages.detailed_kundli') }}</h3>
                <p class="text-gray-600 mb-4">{{ __('messages.detailed_kundli_desc') }}</p>
                <div class="text-2xl font-bold text-indigo-600 mb-4">₹599</div>
                <a href="{{ route('kundli.create') }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 block text-center">{{ __('messages.generate') }}</a>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold mb-4">{{ __('messages.premium_kundli') }}</h3>
                <p class="text-gray-600 mb-4">{{ __('messages.premium_kundli_desc') }}</p>
                <div class="text-2xl font-bold text-indigo-600 mb-4">₹999</div>
                <a href="{{ route('kundli.create') }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 block text-center">{{ __('messages.generate') }}</a>
            </div>
        </div>
    </section>

    <!-- Other Services -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold mb-8">{{ __('messages.other_services') }}</h2>
        <div class="grid md:grid-cols-2 gap-8">
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h3 class="text-2xl font-bold mb-4">{{ __('messages.horoscope_matching') }}</h3>
                <p class="text-gray-600 mb-6">{{ __('messages.horoscope_matching_desc') }}</p>
                <div class="text-2xl font-bold text-indigo-600 mb-4">₹399</div>
                <a href="{{ route('horoscope.matching') }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">{{ __('messages.check_compatibility') }}</a>
            </div>
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h3 class="text-2xl font-bold mb-4">{{ __('messages.ask_question') }}</h3>
                <p class="text-gray-600 mb-6">{{ __('messages.ask_question_desc') }}</p>
                <div class="text-2xl font-bold text-indigo-600 mb-4">₹199</div>
                <a href="{{ route('ask.question') }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">{{ __('messages.ask_now') }}</a>
            </div>
        </div>
    </section>

    <!-- Predictions -->
    <section>
        <h2 class="text-3xl font-bold mb-8">{{ __('messages.predictions') }}</h2>
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <div class="grid md:grid-cols-3 gap-6">
                <div class="text-center">
                    <h4 class="text-xl font-bold mb-2">{{ __('messages.monthly_predictions') }}</h4>
                    <p class="text-gray-600 mb-4">{{ __('messages.monthly_desc') }}</p>
                    <a href="{{ route('predictions.index') }}" class="text-indigo-600 hover:underline">{{ __('messages.read_more') }}</a>
                </div>
                <div class="text-center">
                    <h4 class="text-xl font-bold mb-2">{{ __('messages.yearly_predictions') }}</h4>
                    <p class="text-gray-600 mb-4">{{ __('messages.yearly_desc') }}</p>
                    <a href="{{ route('predictions.index') }}" class="text-indigo-600 hover:underline">{{ __('messages.read_more') }}</a>
                </div>
                <div class="text-center">
                    <h4 class="text-xl font-bold mb-2">{{ __('messages.zodiac_predictions') }}</h4>
                    <p class="text-gray-600 mb-4">{{ __('messages.zodiac_desc') }}</p>
                    <a href="{{ route('predictions.index') }}" class="text-indigo-600 hover:underline">{{ __('messages.read_more') }}</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection