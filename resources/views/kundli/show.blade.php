@extends('layouts.app')

@section('title', 'View Kundli - Astrology Services')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        @php
            $kundli = \App\Models\Kundli::findOrFail($id);
        @endphp

        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $kundli->name }}'s Kundli</h1>
                    <p class="text-gray-600">{{ ucfirst($kundli->type) }} Birth Chart Analysis</p>
                </div>
                <span class="px-4 py-2 rounded-full text-sm font-semibold
                    @if($kundli->status == 'completed') bg-green-100 text-green-800
                    @else bg-yellow-100 text-yellow-800 @endif">
                    {{ ucfirst($kundli->status) }}
                </span>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-bold text-lg mb-3">Birth Details</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date of Birth:</span>
                            <span class="font-medium">{{ $kundli->birth_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Time of Birth:</span>
                            <span class="font-medium">{{ $kundli->birth_time }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Place of Birth:</span>
                            <span class="font-medium">{{ $kundli->birth_place }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-bold text-lg mb-3">Kundli Information</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Type:</span>
                            <span class="font-medium">{{ ucfirst($kundli->type) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Generated On:</span>
                            <span class="font-medium">{{ $kundli->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Amount Paid:</span>
                            <span class="font-medium text-indigo-600">₹{{ number_format($kundli->amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($kundli->status == 'completed')
                <div class="bg-indigo-50 border-l-4 border-indigo-500 p-6 rounded mb-6">
                    <h3 class="font-bold text-lg mb-4">Birth Chart Analysis</h3>
                    <div class="grid md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <span class="text-sm text-gray-600">Rashi (Moon Sign):</span>
                            <p class="font-bold text-indigo-600">{{ $kundli->rashi ?? 'Calculated' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Nakshatra:</span>
                            <p class="font-bold text-indigo-600">{{ $kundli->nakshatra ?? 'Calculated' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Lagna (Ascendant):</span>
                            <p class="font-bold text-indigo-600">{{ $kundli->lagna ?? 'Calculated' }}</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600">
                        Your detailed kundli report includes planetary positions, dasha periods, and personalized predictions based on your birth chart.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all">
                        <i class="fas fa-download mr-2"></i>Download PDF
                    </button>
                    <button class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-md hover:shadow-lg transition-all">
                        <i class="fas fa-share-alt mr-2"></i>Share
                    </button>
                    <a href="{{ route('consultations.index') }}" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 shadow-md hover:shadow-lg transition-all">
                        <i class="fas fa-user-astronaut mr-2"></i>Book Consultation
                    </a>
                </div>
            @else
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded">
                    <p class="text-yellow-800">
                        <i class="fas fa-clock mr-2"></i>
                        Your kundli is being generated. It will be available shortly after payment confirmation.
                    </p>
                </div>
            @endif
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('dashboard.kundlis') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to My Kundlis
            </a>
        </div>
    </div>
</div>
@endsection
