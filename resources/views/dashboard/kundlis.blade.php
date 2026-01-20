@extends('dashboard.layout')

@section('title', 'My Kundlis - Dashboard')

@section('dashboard-content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm mb-4 sm:mb-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <h1 class="text-xl sm:text-2xl font-bold">My Kundlis</h1>
    <p class="text-white/90 mt-1 text-sm sm:text-base">View and manage your birth chart analysis</p>
</div>

<div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 sm:mb-6 gap-3">
        <h2 class="text-lg sm:text-xl font-bold">Kundlis History</h2>
        <a href="{{ route('kundli.create') }}" class="px-3 sm:px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm sm:text-base text-center">
            <i class="fas fa-plus mr-1"></i><span class="hidden sm:inline">Generate New Kundli</span><span class="sm:inline hidden">New Kundli</span>
        </a>
    </div>

    @if($kundlis->count() > 0)
        <div class="space-y-3 sm:space-y-4">
            @foreach($kundlis as $kundli)
                <div class="border border-gray-200 rounded-lg p-3 sm:p-4 hover:shadow-md transition">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-2">
                                <span class="px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium bg-green-100 text-green-800">
                                    Generated
                                </span>
                                <span class="text-xs sm:text-sm text-gray-500">{{ $kundli->name }}'s Kundli</span>
                            </div>
                            <p class="text-sm sm:text-base text-gray-700 mb-2">Birth Chart Analysis</p>
                            <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-xs sm:text-sm text-gray-500">
                                <span><i class="fas fa-calendar mr-1"></i>{{ $kundli->birth_date ? $kundli->birth_date->format('M d, Y') : 'Not specified' }}</span>
                                <span><i class="fas fa-clock mr-1"></i>{{ $kundli->birth_time ?? 'Not specified' }}</span>
                                <span><i class="fas fa-map-marker-alt mr-1"></i>{{ $kundli->birth_place ?? 'Not specified' }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 w-full sm:w-auto">
                            <a href="{{ route('kundli.show', $kundli->id) }}" class="px-3 sm:px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm sm:text-base text-center">
                                View Full Kundli
                            </a>
                            <a href="{{ route('kundli.download', $kundli->id) }}" class="px-3 sm:px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm sm:text-base text-center">
                                Download PDF
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8 sm:py-12 px-4">
            <i class="fas fa-chart-line text-5xl sm:text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-base sm:text-lg mb-4">No Kundlis Generated</p>
            <a href="{{ route('kundli.create') }}" class="inline-block px-4 sm:px-6 py-2 sm:py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm sm:text-base">
                Generate Your Kundli
            </a>
        </div>
    @endif
</div>
@endsection
