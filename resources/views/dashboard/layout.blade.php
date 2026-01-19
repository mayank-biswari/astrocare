@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg">
        <div class="p-6">
            <div class="flex items-center space-x-3 mb-6">
                @if(auth()->user()->profile_photo)
                    <img src="{{ auth()->user()->profile_photo }}" alt="Profile" class="w-12 h-12 rounded-full object-cover">
                @else
                    <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <h3 class="font-bold text-gray-900">{{ auth()->user()->name }}</h3>
                    <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') && !request()->routeIs('dashboard.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-home w-5"></i>
                    <span>{{ __('messages.dashboard') }}</span>
                </a>
                <a href="{{ route('dashboard.orders') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('dashboard.orders*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-shopping-bag w-5"></i>
                    <span>{{ __('messages.my_orders') }}</span>
                </a>
                <a href="{{ route('dashboard.consultations') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('dashboard.consultations*') || request()->routeIs('dashboard.consultation.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-comments w-5"></i>
                    <span>{{ __('messages.my_consultations') }}</span>
                </a>
                <a href="{{ route('dashboard.kundlis') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('dashboard.kundlis*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-chart-line w-5"></i>
                    <span>{{ __('messages.my_kundlis') }}</span>
                </a>
                <a href="{{ route('dashboard.questions') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('dashboard.questions*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-question-circle w-5"></i>
                    <span>My Questions</span>
                </a>
                <a href="{{ route('dashboard.poojas') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('dashboard.poojas*') || request()->routeIs('dashboard.pooja.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-om w-5"></i>
                    <span>{{ __('messages.my_poojas') }}</span>
                </a>
                <a href="{{ route('dashboard.reports') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('dashboard.reports*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-file-alt w-5"></i>
                    <span>{{ __('messages.my_reports') }}</span>
                </a>
                <a href="{{ route('dashboard.settings') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('dashboard.settings*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-cog w-5"></i>
                    <span>{{ __('messages.account_settings') }}</span>
                </a>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8">
        @yield('dashboard-content')
    </main>
</div>
@endsection
