@extends('layouts.app')

@section('title', 'Forgot Password - AstroServices')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-indigo-900 to-purple-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Forgot Password?</h2>
                <p class="text-gray-600">No problem. Just let us know your email address and we will email you a password reset link.</p>
            </div>

            @if (session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 mt-6">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg font-bold hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-200">
                    Email Password Reset Link
                </button>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Remember your password?
                        <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500 font-medium">Sign in</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
