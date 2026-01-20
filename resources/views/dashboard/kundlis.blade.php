@extends('dashboard.layout')

@section('title', 'My Kundlis - Dashboard')

@section('dashboard-content')
<div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-6 sm:mb-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">My Kundlis</h1>
</div>

<div class="flex justify-end mb-4 sm:mb-6">
    <a href="{{ route('kundli.create') }}" class="bg-green-600 text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-green-700 shadow-md hover:shadow-lg transition-all text-sm sm:text-base">
        <i class="fas fa-plus-circle mr-2"></i><span class="hidden sm:inline">Generate New Kundli</span><span class="sm:hidden">New Kundli</span>
    </a>
</div>

@if($kundlis->count() > 0)
    <!-- Kundlis List -->
    <div class="grid gap-4 sm:gap-6">
        @foreach($kundlis as $kundli)
            <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-4 gap-2">
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold">{{ $kundli->name }}'s Kundli</h3>
                        <p class="text-sm sm:text-base text-gray-600">Birth Chart Analysis</p>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs sm:text-sm self-start">Generated</span>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mb-4">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600">Date of Birth</p>
                        <p class="text-sm sm:text-base font-medium">{{ $kundli->birth_date ? $kundli->birth_date->format('M d, Y') : 'Not specified' }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600">Time of Birth</p>
                        <p class="text-sm sm:text-base font-medium">{{ $kundli->birth_time ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600">Place of Birth</p>
                        <p class="text-sm sm:text-base font-medium">{{ $kundli->birth_place ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600">Generated On</p>
                        <p class="text-sm sm:text-base font-medium">{{ $kundli->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-3 sm:p-4 mb-4">
                    <h4 class="text-sm sm:text-base font-bold mb-2">Key Details</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 sm:gap-4 text-xs sm:text-sm">
                        <div>
                            <span class="text-gray-600">Rashi:</span>
                            <span class="font-medium ml-2">{{ $kundli->rashi ?? 'Calculated' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Nakshatra:</span>
                            <span class="font-medium ml-2">{{ $kundli->nakshatra ?? 'Calculated' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Lagna:</span>
                            <span class="font-medium ml-2">{{ $kundli->lagna ?? 'Calculated' }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <a href="{{ route('kundli.show', $kundli->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-center text-sm sm:text-base">View Full Kundli</a>
                    <a href="{{ route('kundli.download', $kundli->id) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-center text-sm sm:text-base">Download PDF</a>
                    <a href="{{ route('consultations.index') }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 text-center text-sm sm:text-base">Get Consultation</a>
                </div>
            </div>
        @endforeach
    </div>
@else
    <!-- Empty State -->
    <div class="text-center py-8 sm:py-12 px-4">
        <div class="text-gray-400 mb-4">
            <svg class="mx-auto h-12 w-12 sm:h-16 sm:w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
        <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">No Kundlis Generated</h3>
        <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">You haven't generated any kundlis yet. Create your first birth chart analysis.</p>
        <a href="{{ route('kundli.create') }}" class="inline-block bg-indigo-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg hover:bg-indigo-700 text-sm sm:text-base">
            Generate Your Kundli
        </a>
    </div>
@endif
@endsection
