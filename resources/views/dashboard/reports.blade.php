@extends('dashboard.layout')

@section('title', __('messages.my_reports') . ' - ' . __('messages.dashboard'))

@section('dashboard-content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm mb-4 sm:mb-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <h1 class="text-xl sm:text-2xl font-bold">{{ __('messages.my_reports') }}</h1>
    <p class="text-white/90 mt-1 text-sm sm:text-base">View your activity statistics and reports</p>
</div>

<div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
    <h2 class="text-lg sm:text-xl font-bold mb-4 sm:mb-6">Activity Overview</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        <!-- Order Reports -->
        <div class="border border-gray-200 rounded-lg p-4 sm:p-6">
            <div class="flex items-center gap-3 mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                    <i class="fas fa-shopping-bag text-lg sm:text-xl"></i>
                </div>
                <h3 class="text-base sm:text-lg font-bold">{{ __('messages.orders') }}</h3>
            </div>
            <div class="space-y-2 sm:space-y-3 text-sm sm:text-base">
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.total') }}:</span>
                    <span class="font-bold">{{ $orderStats['total'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.completed') }}:</span>
                    <span class="text-green-600 font-bold">{{ $orderStats['completed'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.pending') }}:</span>
                    <span class="text-yellow-600 font-bold">{{ $orderStats['pending'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.cancelled') }}:</span>
                    <span class="text-red-600 font-bold">{{ $orderStats['cancelled'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Consultation Reports -->
        <div class="border border-gray-200 rounded-lg p-4 sm:p-6">
            <div class="flex items-center gap-3 mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600">
                    <i class="fas fa-comments text-lg sm:text-xl"></i>
                </div>
                <h3 class="text-base sm:text-lg font-bold">{{ __('messages.consultations') }}</h3>
            </div>
            <div class="space-y-2 sm:space-y-3 text-sm sm:text-base">
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.total') }}:</span>
                    <span class="font-bold">{{ $consultationStats['total'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.completed') }}:</span>
                    <span class="text-green-600 font-bold">{{ $consultationStats['completed'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.scheduled') }}:</span>
                    <span class="text-blue-600 font-bold">{{ $consultationStats['upcoming'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.total') }} {{ __('messages.amount') }}:</span>
                    <span class="font-bold">{{ formatPrice($consultationStats['total_spent'] ?? 0) }}</span>
                </div>
            </div>
        </div>

        <!-- Kundli Reports -->
        <div class="border border-gray-200 rounded-lg p-4 sm:p-6">
            <div class="flex items-center gap-3 mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                    <i class="fas fa-chart-line text-lg sm:text-xl"></i>
                </div>
                <h3 class="text-base sm:text-lg font-bold">{{ __('messages.my_kundlis') }}</h3>
            </div>
            <div class="space-y-2 sm:space-y-3 text-sm sm:text-base">
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.generated_on') }}:</span>
                    <span class="font-bold">{{ $kundliStats['generated'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.download') }}:</span>
                    <span class="text-green-600 font-bold">{{ $kundliStats['downloaded'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Pooja Reports -->
        <div class="border border-gray-200 rounded-lg p-4 sm:p-6">
            <div class="flex items-center gap-3 mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-100 rounded-lg flex items-center justify-center text-orange-600">
                    <i class="fas fa-om text-lg sm:text-xl"></i>
                </div>
                <h3 class="text-base sm:text-lg font-bold">{{ __('messages.my_poojas') }}</h3>
            </div>
            <div class="space-y-2 sm:space-y-3 text-sm sm:text-base">
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.total') }}:</span>
                    <span class="font-bold">{{ $poojaStats['total'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.completed') }}:</span>
                    <span class="text-green-600 font-bold">{{ $poojaStats['completed'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.scheduled') }}:</span>
                    <span class="text-blue-600 font-bold">{{ $poojaStats['upcoming'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.total') }} {{ __('messages.amount') }}:</span>
                    <span class="font-bold">{{ formatPrice($poojaStats['total_spent'] ?? 0) }}</span>
                </div>
            </div>
        </div>

        <!-- Question Reports -->
        <div class="border border-gray-200 rounded-lg p-4 sm:p-6">
            <div class="flex items-center gap-3 mb-3 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-pink-100 rounded-lg flex items-center justify-center text-pink-600">
                    <i class="fas fa-question-circle text-lg sm:text-xl"></i>
                </div>
                <h3 class="text-base sm:text-lg font-bold">Questions</h3>
            </div>
            <div class="space-y-2 sm:space-y-3 text-sm sm:text-base">
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.total') }}:</span>
                    <span class="font-bold">{{ $questionStats['total'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.completed') }}:</span>
                    <span class="text-green-600 font-bold">{{ $questionStats['completed'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.pending') }}:</span>
                    <span class="text-yellow-600 font-bold">{{ $questionStats['pending'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.total') }} {{ __('messages.amount') }}:</span>
                    <span class="font-bold">{{ formatPrice($questionStats['total_spent'] ?? 0) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
