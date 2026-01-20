@extends('dashboard.layout')

@section('title', __('messages.dashboard'))

@section('dashboard-content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm mb-4 sm:mb-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <h1 class="text-xl sm:text-2xl font-bold">{{ __('messages.dashboard') }}</h1>
    <p class="text-white/90 mt-1 text-sm sm:text-base">Welcome back, {{ auth()->user()->name }}!</p>
</div>

<div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
    <h2 class="text-lg sm:text-xl font-bold mb-4 sm:mb-6">Quick Access</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        <a href="{{ route('dashboard.orders') }}" class="group border border-gray-200 p-4 sm:p-6 rounded-lg hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 text-lg sm:text-xl group-hover:bg-blue-600 group-hover:text-white transition">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <i class="fas fa-arrow-right text-gray-400 group-hover:text-indigo-600 transition"></i>
            </div>
            <h3 class="text-base sm:text-lg font-bold mb-1 sm:mb-2 text-gray-800">{{ __('messages.my_orders') }}</h3>
            <p class="text-gray-600 text-xs sm:text-sm">{{ __('messages.orders_desc') }}</p>
        </a>

        <a href="{{ route('dashboard.consultations') }}" class="group border border-gray-200 p-4 sm:p-6 rounded-lg hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 text-lg sm:text-xl group-hover:bg-purple-600 group-hover:text-white transition">
                    <i class="fas fa-comments"></i>
                </div>
                <i class="fas fa-arrow-right text-gray-400 group-hover:text-indigo-600 transition"></i>
            </div>
            <h3 class="text-base sm:text-lg font-bold mb-1 sm:mb-2 text-gray-800">{{ __('messages.my_consultations') }}</h3>
            <p class="text-gray-600 text-xs sm:text-sm">{{ __('messages.consultations_desc') }}</p>
        </a>

        <a href="{{ route('dashboard.kundlis') }}" class="group border border-gray-200 p-4 sm:p-6 rounded-lg hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600 text-lg sm:text-xl group-hover:bg-green-600 group-hover:text-white transition">
                    <i class="fas fa-chart-line"></i>
                </div>
                <i class="fas fa-arrow-right text-gray-400 group-hover:text-indigo-600 transition"></i>
            </div>
            <h3 class="text-base sm:text-lg font-bold mb-1 sm:mb-2 text-gray-800">{{ __('messages.my_kundlis') }}</h3>
            <p class="text-gray-600 text-xs sm:text-sm">{{ __('messages.kundlis_desc') }}</p>
        </a>

        <a href="{{ route('dashboard.poojas') }}" class="group border border-gray-200 p-4 sm:p-6 rounded-lg hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-100 rounded-lg flex items-center justify-center text-orange-600 text-lg sm:text-xl group-hover:bg-orange-600 group-hover:text-white transition">
                    <i class="fas fa-om"></i>
                </div>
                <i class="fas fa-arrow-right text-gray-400 group-hover:text-indigo-600 transition"></i>
            </div>
            <h3 class="text-base sm:text-lg font-bold mb-1 sm:mb-2 text-gray-800">{{ __('messages.my_poojas') }}</h3>
            <p class="text-gray-600 text-xs sm:text-sm">{{ __('messages.poojas_desc') }}</p>
        </a>

        <a href="{{ route('dashboard.reports') }}" class="group border border-gray-200 p-4 sm:p-6 rounded-lg hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-pink-100 rounded-lg flex items-center justify-center text-pink-600 text-lg sm:text-xl group-hover:bg-pink-600 group-hover:text-white transition">
                    <i class="fas fa-file-alt"></i>
                </div>
                <i class="fas fa-arrow-right text-gray-400 group-hover:text-indigo-600 transition"></i>
            </div>
            <h3 class="text-base sm:text-lg font-bold mb-1 sm:mb-2 text-gray-800">{{ __('messages.my_reports') }}</h3>
            <p class="text-gray-600 text-xs sm:text-sm">{{ __('messages.reports_desc') }}</p>
        </a>

        <a href="{{ route('dashboard.settings') }}" class="group border border-gray-200 p-4 sm:p-6 rounded-lg hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 rounded-lg flex items-center justify-center text-gray-600 text-lg sm:text-xl group-hover:bg-gray-600 group-hover:text-white transition">
                    <i class="fas fa-cog"></i>
                </div>
                <i class="fas fa-arrow-right text-gray-400 group-hover:text-indigo-600 transition"></i>
            </div>
            <h3 class="text-base sm:text-lg font-bold mb-1 sm:mb-2 text-gray-800">{{ __('messages.account_settings') }}</h3>
            <p class="text-gray-600 text-xs sm:text-sm">{{ __('messages.settings_desc') }}</p>
        </a>
    </div>
</div>
@endsection
