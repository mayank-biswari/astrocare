@extends('layouts.app')

@section('title', 'Login - ' . \App\Models\SiteSetting::get('site_name', 'AstroServices'))

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-indigo-900 to-purple-900 py-6 sm:py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6 sm:space-y-8">
        <div class="bg-white rounded-lg shadow-xl p-4 sm:p-8">
            <div class="text-center">
                <h2 class="text-xl sm:text-3xl font-bold text-gray-900 mb-2">Welcome Back</h2>
                <p class="text-sm sm:text-base text-gray-600">Sign in to your account</p>
            </div>

            @if (session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="mt-6 sm:mt-8 space-y-4 sm:space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1 sm:mb-2">Email Address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm sm:text-base">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1 sm:mb-2">Password</label>
                    <input id="password" name="password" type="password" required
                           class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm sm:text-base">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="remember_me" class="ml-2 block text-xs sm:text-sm text-gray-700">Remember me</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs sm:text-sm text-indigo-600 hover:text-indigo-500">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-2.5 sm:py-3 px-4 rounded-lg font-bold hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-200 text-sm sm:text-base">
                    Sign In
                </button>

                <div class="text-center">
                    <p class="text-xs sm:text-sm text-gray-600">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-500 font-medium">Sign up</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
