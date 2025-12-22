@extends('layouts.app')

@section('title', 'My Kundlis - Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">My Kundlis</h1>
        <a href="{{ route('kundli.create') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
            Generate New Kundli
        </a>
    </div>

    @if($kundlis->count() > 0)
        <!-- Kundlis List -->
        <div class="grid gap-6">
            @foreach($kundlis as $kundli)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold">{{ $kundli->name }}'s Kundli</h3>
                            <p class="text-gray-600">Birth Chart Analysis</p>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Generated</span>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Date of Birth</p>
                            <p class="font-medium">{{ $kundli->birth_date ? $kundli->birth_date->format('M d, Y') : 'Not specified' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Time of Birth</p>
                            <p class="font-medium">{{ $kundli->birth_time ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Place of Birth</p>
                            <p class="font-medium">{{ $kundli->birth_place ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Generated On</p>
                            <p class="font-medium">{{ $kundli->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <h4 class="font-bold mb-2">Key Details</h4>
                        <div class="grid md:grid-cols-3 gap-4 text-sm">
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
                    
                    <div class="flex space-x-3">
                        <a href="{{ route('kundli.show', $kundli->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">View Full Kundli</a>
                        <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Download PDF</button>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Share</button>
                        <button class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">Get Consultation</button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="mx-auto h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Kundlis Generated</h3>
            <p class="text-gray-600 mb-6">You haven't generated any kundlis yet. Create your first birth chart analysis.</p>
            <a href="{{ route('kundli.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
                Generate Your Kundli
            </a>
        </div>
    @endif
</div>
@endsection