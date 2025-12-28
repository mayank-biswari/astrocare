@extends('layouts.app')

@section('title', __('messages.dashboard'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">{{ __('messages.my_dashboard') }}</h1>
    
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('dashboard.orders') }}" class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
            <div class="text-3xl mb-4">📦</div>
            <h3 class="text-xl font-bold mb-2">{{ __('messages.my_orders') }}</h3>
            <p class="text-gray-600">{{ __('messages.orders_desc') }}</p>
        </a>
        
        <a href="{{ route('dashboard.consultations') }}" class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
            <div class="text-3xl mb-4">💬</div>
            <h3 class="text-xl font-bold mb-2">{{ __('messages.my_consultations') }}</h3>
            <p class="text-gray-600">{{ __('messages.consultations_desc') }}</p>
        </a>
        
        <a href="{{ route('dashboard.kundlis') }}" class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
            <div class="text-3xl mb-4">📊</div>
            <h3 class="text-xl font-bold mb-2">{{ __('messages.my_kundlis') }}</h3>
            <p class="text-gray-600">{{ __('messages.kundlis_desc') }}</p>
        </a>
        
        <a href="{{ route('dashboard.poojas') }}" class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
            <div class="text-3xl mb-4">🕉️</div>
            <h3 class="text-xl font-bold mb-2">{{ __('messages.my_poojas') }}</h3>
            <p class="text-gray-600">{{ __('messages.poojas_desc') }}</p>
        </a>
        
        <a href="{{ route('dashboard.reports') }}" class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
            <div class="text-3xl mb-4">📄</div>
            <h3 class="text-xl font-bold mb-2">{{ __('messages.my_reports') }}</h3>
            <p class="text-gray-600">{{ __('messages.reports_desc') }}</p>
        </a>
        
        <a href="{{ route('dashboard.settings') }}" class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
            <div class="text-3xl mb-4">⚙️</div>
            <h3 class="text-xl font-bold mb-2">{{ __('messages.account_settings') }}</h3>
            <p class="text-gray-600">{{ __('messages.settings_desc') }}</p>
        </a>
    </div>
</div>
@endsection