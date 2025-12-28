@extends('layouts.app')

@section('title', __('messages.my_reports') . ' - ' . __('messages.dashboard'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">{{ __('messages.my_reports') }}</h1>
    
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Order Reports -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">{{ __('messages.orders') }}</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span>{{ __('messages.total') }}:</span>
                    <span class="font-bold">{{ $orderStats['total'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span>{{ __('messages.completed') }}:</span>
                    <span class="text-green-600 font-bold">{{ $orderStats['completed'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span>{{ __('messages.pending') }}:</span>
                    <span class="text-yellow-600 font-bold">{{ $orderStats['pending'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span>{{ __('messages.cancelled') }}:</span>
                    <span class="text-red-600 font-bold">{{ $orderStats['cancelled'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Consultation Reports -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">{{ __('messages.consultations') }}</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span>{{ __('messages.total') }}:</span>
                    <span class="font-bold">{{ $consultationStats['total'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span>{{ __('messages.completed') }}:</span>
                    <span class="text-green-600 font-bold">{{ $consultationStats['completed'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span>{{ __('messages.scheduled') }}:</span>
                    <span class="text-blue-600 font-bold">{{ $consultationStats['upcoming'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span>{{ __('messages.total') }} {{ __('messages.amount') }}:</span>
                    <span class="font-bold">{{ formatPrice($consultationStats['total_spent'] ?? 0) }}</span>
                </div>
            </div>
        </div>

        <!-- Kundli Reports -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">{{ __('messages.my_kundlis') }}</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span>{{ __('messages.generated_on') }}:</span>
                    <span class="font-bold">{{ $kundliStats['generated'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span>{{ __('messages.download') }}:</span>
                    <span class="text-green-600 font-bold">{{ $kundliStats['downloaded'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Pooja Reports -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">{{ __('messages.my_poojas') }}</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span>{{ __('messages.total') }}:</span>
                    <span class="font-bold">{{ $poojaStats['total'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span>{{ __('messages.completed') }}:</span>
                    <span class="text-green-600 font-bold">{{ $poojaStats['completed'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span>{{ __('messages.scheduled') }}:</span>
                    <span class="text-blue-600 font-bold">{{ $poojaStats['upcoming'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span>{{ __('messages.total') }} {{ __('messages.amount') }}:</span>
                    <span class="font-bold">{{ formatPrice($poojaStats['total_spent'] ?? 0) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection