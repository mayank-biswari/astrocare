@extends('layouts.app')

@section('title', 'Reports - Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Reports & Analytics</h1>
    
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Order Reports -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Order Reports</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span>Total Orders:</span>
                    <span class="font-bold">{{ $orderStats['total'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Completed:</span>
                    <span class="text-green-600 font-bold">{{ $orderStats['completed'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Pending:</span>
                    <span class="text-yellow-600 font-bold">{{ $orderStats['pending'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Cancelled:</span>
                    <span class="text-red-600 font-bold">{{ $orderStats['cancelled'] }}</span>
                </div>
            </div>
        </div>

        <!-- Consultation Reports -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Consultations</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span>Total Sessions:</span>
                    <span class="font-bold">{{ $consultationStats['total'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Completed:</span>
                    <span class="text-green-600 font-bold">{{ $consultationStats['completed'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Upcoming:</span>
                    <span class="text-blue-600 font-bold">{{ $consultationStats['upcoming'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Total Spent:</span>
                    <span class="font-bold">{{ formatPrice($consultationStats['total_spent']) }}</span>
                </div>
            </div>
        </div>

        <!-- Kundli Reports -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Kundli Reports</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span>Generated:</span>
                    <span class="font-bold">{{ $kundliStats['generated'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Downloaded:</span>
                    <span class="text-green-600 font-bold">{{ $kundliStats['downloaded'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Shared:</span>
                    <span class="text-blue-600 font-bold">{{ $kundliStats['shared'] }}</span>
                </div>
            </div>
        </div>

        <!-- Pooja Reports -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Pooja Bookings</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span>Total Bookings:</span>
                    <span class="font-bold">{{ $poojaStats['total'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Completed:</span>
                    <span class="text-green-600 font-bold">{{ $poojaStats['completed'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Upcoming:</span>
                    <span class="text-blue-600 font-bold">{{ $poojaStats['upcoming'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Total Spent:</span>
                    <span class="font-bold">{{ formatPrice($poojaStats['total_spent']) }}</span>
                </div>
            </div>
        </div>

        <!-- Spending Summary -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Spending Summary</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span>This Month:</span>
                    <span class="font-bold">{{ formatPrice($spendingStats['this_month']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Last Month:</span>
                    <span class="font-bold">{{ formatPrice($spendingStats['last_month']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Total Lifetime:</span>
                    <span class="font-bold text-indigo-600">{{ formatPrice($spendingStats['lifetime']) }}</span>
                </div>
            </div>
        </div>

        <!-- Activity Summary -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Activity Summary</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span>Account Age:</span>
                    <span class="font-bold">{{ $activityStats['account_age'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Last Login:</span>
                    <span class="font-bold">{{ $activityStats['last_login'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Profile Completion:</span>
                    <span class="text-green-600 font-bold">{{ $activityStats['profile_completion'] }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
        <h2 class="text-xl font-bold mb-6">Recent Activity</h2>
        <div class="space-y-4">
            @forelse($recentActivities as $activity)
                <div class="flex items-center justify-between border-b pb-3">
                    <div>
                        <p class="font-medium">{{ $activity['title'] }}</p>
                        <p class="text-sm text-gray-600">{{ $activity['description'] }}</p>
                    </div>
                    <span class="text-sm text-gray-500">{{ $activity['date']->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">No recent activities found.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection