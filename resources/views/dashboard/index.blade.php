@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">My Dashboard</h1>
    
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('dashboard.orders') }}" class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
            <div class="text-3xl mb-4">📦</div>
            <h3 class="text-xl font-bold mb-2">My Orders</h3>
            <p class="text-gray-600">View your product orders and delivery status</p>
        </a>
        
        <a href="{{ route('dashboard.consultations') }}" class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
            <div class="text-3xl mb-4">💬</div>
            <h3 class="text-xl font-bold mb-2">My Consultations</h3>
            <p class="text-gray-600">Track your astrology consultation sessions</p>
        </a>
        
        <a href="{{ route('dashboard.kundlis') }}" class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
            <div class="text-3xl mb-4">📊</div>
            <h3 class="text-xl font-bold mb-2">My Kundlis</h3>
            <p class="text-gray-600">Access your generated birth charts</p>
        </a>
        
        <a href="{{ route('dashboard.poojas') }}" class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
            <div class="text-3xl mb-4">🕉️</div>
            <h3 class="text-xl font-bold mb-2">My Poojas</h3>
            <p class="text-gray-600">View your booked pooja ceremonies</p>
        </a>
        
        <a href="{{ route('dashboard.reports') }}" class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
            <div class="text-3xl mb-4">📄</div>
            <h3 class="text-xl font-bold mb-2">My Reports</h3>
            <p class="text-gray-600">Download your astrology reports</p>
        </a>
        
        <a href="{{ route('dashboard.settings') }}" class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
            <div class="text-3xl mb-4">⚙️</div>
            <h3 class="text-xl font-bold mb-2">Account Settings</h3>
            <p class="text-gray-600">Manage your profile and preferences</p>
        </a>
    </div>
</div>
@endsection