@extends('dashboard.layout')

@section('title', __('messages.dashboard'))

@section('dashboard-content')
<!-- Welcome Message -->
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome back, {{ auth()->user()->name }}!</h1>
    <p class="text-gray-600">Here's what's happening with your account today.</p>
</div>

<h2 class="text-2xl font-bold mb-6 text-gray-900">Quick Access</h2>

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    <a href="{{ route('dashboard.orders') }}" class="group bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 border border-blue-200 hover:scale-105">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 bg-blue-500 rounded-lg flex items-center justify-center text-white text-2xl group-hover:bg-blue-600 transition-colors">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <i class="fas fa-arrow-right text-blue-400 group-hover:text-blue-600 group-hover:translate-x-1 transition-all"></i>
        </div>
        <h3 class="text-xl font-bold mb-2 text-gray-800">{{ __('messages.my_orders') }}</h3>
        <p class="text-gray-600 text-sm">{{ __('messages.orders_desc') }}</p>
    </a>

    <a href="{{ route('dashboard.consultations') }}" class="group bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 border border-purple-200 hover:scale-105">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 bg-purple-500 rounded-lg flex items-center justify-center text-white text-2xl group-hover:bg-purple-600 transition-colors">
                <i class="fas fa-comments"></i>
            </div>
            <i class="fas fa-arrow-right text-purple-400 group-hover:text-purple-600 group-hover:translate-x-1 transition-all"></i>
        </div>
        <h3 class="text-xl font-bold mb-2 text-gray-800">{{ __('messages.my_consultations') }}</h3>
        <p class="text-gray-600 text-sm">{{ __('messages.consultations_desc') }}</p>
    </a>

    <a href="{{ route('dashboard.kundlis') }}" class="group bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 border border-green-200 hover:scale-105">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 bg-green-500 rounded-lg flex items-center justify-center text-white text-2xl group-hover:bg-green-600 transition-colors">
                <i class="fas fa-chart-line"></i>
            </div>
            <i class="fas fa-arrow-right text-green-400 group-hover:text-green-600 group-hover:translate-x-1 transition-all"></i>
        </div>
        <h3 class="text-xl font-bold mb-2 text-gray-800">{{ __('messages.my_kundlis') }}</h3>
        <p class="text-gray-600 text-sm">{{ __('messages.kundlis_desc') }}</p>
    </a>

    <a href="{{ route('dashboard.poojas') }}" class="group bg-gradient-to-br from-orange-50 to-orange-100 p-6 rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 border border-orange-200 hover:scale-105">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 bg-orange-500 rounded-lg flex items-center justify-center text-white text-2xl group-hover:bg-orange-600 transition-colors">
                <i class="fas fa-om"></i>
            </div>
            <i class="fas fa-arrow-right text-orange-400 group-hover:text-orange-600 group-hover:translate-x-1 transition-all"></i>
        </div>
        <h3 class="text-xl font-bold mb-2 text-gray-800">{{ __('messages.my_poojas') }}</h3>
        <p class="text-gray-600 text-sm">{{ __('messages.poojas_desc') }}</p>
    </a>

    <a href="{{ route('dashboard.reports') }}" class="group bg-gradient-to-br from-pink-50 to-pink-100 p-6 rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 border border-pink-200 hover:scale-105">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 bg-pink-500 rounded-lg flex items-center justify-center text-white text-2xl group-hover:bg-pink-600 transition-colors">
                <i class="fas fa-file-alt"></i>
            </div>
            <i class="fas fa-arrow-right text-pink-400 group-hover:text-pink-600 group-hover:translate-x-1 transition-all"></i>
        </div>
        <h3 class="text-xl font-bold mb-2 text-gray-800">{{ __('messages.my_reports') }}</h3>
        <p class="text-gray-600 text-sm">{{ __('messages.reports_desc') }}</p>
    </a>

    <a href="{{ route('dashboard.settings') }}" class="group bg-gradient-to-br from-gray-50 to-gray-100 p-6 rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-200 hover:scale-105">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 bg-gray-500 rounded-lg flex items-center justify-center text-white text-2xl group-hover:bg-gray-600 transition-colors">
                <i class="fas fa-cog"></i>
            </div>
            <i class="fas fa-arrow-right text-gray-400 group-hover:text-gray-600 group-hover:translate-x-1 transition-all"></i>
        </div>
        <h3 class="text-xl font-bold mb-2 text-gray-800">{{ __('messages.account_settings') }}</h3>
        <p class="text-gray-600 text-sm">{{ __('messages.settings_desc') }}</p>
    </a>
</div>
@endsection
