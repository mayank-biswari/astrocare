@extends('dashboard.layout')

@section('title', 'Expert Dashboard')

@section('dashboard-content')
@include('dashboard.expert.submenu')

<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm mb-4 sm:mb-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <h1 class="text-xl sm:text-2xl font-bold">Expert Dashboard</h1>
    <p class="text-white/90 mt-1 text-sm sm:text-base">Welcome back, {{ auth()->user()->name }}!</p>
</div>

<div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
    <h2 class="text-lg sm:text-xl font-bold mb-4 sm:mb-6">Quick Access</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        <a href="{{ route('expert.profile') }}" class="group border border-gray-200 p-4 sm:p-6 rounded-lg hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 text-lg sm:text-xl group-hover:bg-purple-600 group-hover:text-white transition">
                    <i class="fas fa-user-circle"></i>
                </div>
                <i class="fas fa-arrow-right text-gray-400 group-hover:text-indigo-600 transition"></i>
            </div>
            <h3 class="text-base sm:text-lg font-bold mb-1 sm:mb-2 text-gray-800">My Profile</h3>
            <p class="text-gray-600 text-xs sm:text-sm">Manage your astrologer profile and services</p>
        </a>

        <a href="{{ route('expert.availability') }}" class="group border border-gray-200 p-4 sm:p-6 rounded-lg hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-100 rounded-lg flex items-center justify-center text-orange-600 text-lg sm:text-xl group-hover:bg-orange-600 group-hover:text-white transition">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <i class="fas fa-arrow-right text-gray-400 group-hover:text-indigo-600 transition"></i>
            </div>
            <h3 class="text-base sm:text-lg font-bold mb-1 sm:mb-2 text-gray-800">Availability</h3>
            <p class="text-gray-600 text-xs sm:text-sm">Set your online availability calendar</p>
        </a>

        <a href="{{ route('expert.chats') }}" class="group border border-gray-200 p-4 sm:p-6 rounded-lg hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600 text-lg sm:text-xl group-hover:bg-green-600 group-hover:text-white transition">
                    <i class="fas fa-comments"></i>
                </div>
                <i class="fas fa-arrow-right text-gray-400 group-hover:text-indigo-600 transition"></i>
            </div>
            <h3 class="text-base sm:text-lg font-bold mb-1 sm:mb-2 text-gray-800">Chat Sessions</h3>
            <p class="text-gray-600 text-xs sm:text-sm">View and manage scheduled chat consultations</p>
        </a>

        <a href="{{ route('expert.calls') }}" class="group border border-gray-200 p-4 sm:p-6 rounded-lg hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 text-lg sm:text-xl group-hover:bg-blue-600 group-hover:text-white transition">
                    <i class="fas fa-phone"></i>
                </div>
                <i class="fas fa-arrow-right text-gray-400 group-hover:text-indigo-600 transition"></i>
            </div>
            <h3 class="text-base sm:text-lg font-bold mb-1 sm:mb-2 text-gray-800">Call Sessions</h3>
            <p class="text-gray-600 text-xs sm:text-sm">View and manage scheduled call consultations</p>
        </a>

        <a href="{{ route('dashboard.settings') }}" class="group border border-gray-200 p-4 sm:p-6 rounded-lg hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 rounded-lg flex items-center justify-center text-gray-600 text-lg sm:text-xl group-hover:bg-gray-600 group-hover:text-white transition">
                    <i class="fas fa-cog"></i>
                </div>
                <i class="fas fa-arrow-right text-gray-400 group-hover:text-indigo-600 transition"></i>
            </div>
            <h3 class="text-base sm:text-lg font-bold mb-1 sm:mb-2 text-gray-800">Account Settings</h3>
            <p class="text-gray-600 text-xs sm:text-sm">Manage your account preferences</p>
        </a>
    </div>
</div>
@endsection
