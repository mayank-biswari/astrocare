@extends('layouts.app')

@section('title', 'Account Settings - Dashboard')

@section('content')
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Account Settings</h1>
    
    <div class="grid md:grid-cols-2 gap-8">
        <!-- Profile Information -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-6">Profile Information</h2>
            
            <form action="{{ route('dashboard.profile.update') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ auth()->user()->name }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ auth()->user()->email }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" name="phone" value="{{ auth()->user()->phone }}" placeholder="Enter phone number"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ auth()->user()->date_of_birth }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                    Update Profile
                </button>
            </form>
        </div>

        <!-- Change Password -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-6">Change Password</h2>
            
            <form action="{{ route('dashboard.password.update') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input type="password" name="current_password" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                    Change Password
                </button>
            </form>
        </div>
    </div>

    <!-- Preferences -->
    <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
        <h2 class="text-xl font-bold mb-6">Preferences</h2>
        
        <form action="{{ route('dashboard.preferences.update') }}" method="POST">
            @csrf
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h3 class="font-bold mb-4">Notifications</h3>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="email_notifications" value="1" 
                                   {{ auth()->user()->email_notifications ? 'checked' : '' }} class="mr-3">
                            <span>Email notifications for order updates</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="sms_notifications" value="1" 
                                   {{ auth()->user()->sms_notifications ? 'checked' : '' }} class="mr-3">
                            <span>SMS notifications for consultations</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="marketing_emails" value="1" 
                                   {{ auth()->user()->marketing_emails ? 'checked' : '' }} class="mr-3">
                            <span>Marketing emails</span>
                        </label>
                    </div>
                </div>
                
                <div>
                    <h3 class="font-bold mb-4">Language & Currency</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Language</label>
                            <select name="language" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <option {{ auth()->user()->language == 'English' ? 'selected' : '' }}>English</option>
                                <option {{ auth()->user()->language == 'Hindi' ? 'selected' : '' }}>Hindi</option>
                                <option {{ auth()->user()->language == 'Sanskrit' ? 'selected' : '' }}>Sanskrit</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                            <select name="currency" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <option {{ auth()->user()->currency == 'INR (₹)' ? 'selected' : '' }}>INR (₹)</option>
                                <option {{ auth()->user()->currency == 'USD ($)' ? 'selected' : '' }}>USD ($)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                    Save Preferences
                </button>
            </div>
        </form>
    </div>
</div>
@endsection