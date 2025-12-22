@extends('layouts.app')

@section('title', 'My Consultations - Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">My Consultations</h1>
        <a href="{{ route('consultations.index') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
            Book New Consultation
        </a>
    </div>

    <!-- Status Filter -->
    <div class="mb-6">
        <div class="flex space-x-4">
            <a href="{{ route('dashboard.consultations') }}" class="px-4 py-2 rounded-lg {{ !request('status') || request('status') == 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">All</a>
            <a href="{{ route('dashboard.consultations', ['status' => 'scheduled']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'scheduled' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Scheduled</a>
            <a href="{{ route('dashboard.consultations', ['status' => 'completed']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'completed' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Completed</a>
            <a href="{{ route('dashboard.consultations', ['status' => 'cancelled']) }}" class="px-4 py-2 rounded-lg {{ request('status') == 'cancelled' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Cancelled</a>
        </div>
    </div>

    @if($consultations->count() > 0)
        <!-- Consultations List -->
        <div class="grid gap-6">
            @foreach($consultations as $consultation)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold">{{ $consultation->type }} Consultation</h3>
                            <p class="text-gray-600">{{ $consultation->description ?? 'Astrology Session' }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm
                            @if($consultation->status == 'completed') bg-green-100 text-green-800
                            @elseif($consultation->status == 'scheduled') bg-blue-100 text-blue-800
                            @elseif($consultation->status == 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($consultation->status) }}
                        </span>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Date & Time</p>
                            <p class="font-medium">{{ $consultation->date ? $consultation->date->format('M d, Y') : 'Not scheduled' }} {{ $consultation->time ? 'at ' . $consultation->time : '' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Duration</p>
                            <p class="font-medium">{{ $consultation->duration }} minutes</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Contact</p>
                            <p class="font-medium">{{ $consultation->phone ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Amount</p>
                            <p class="font-medium text-indigo-600">₹{{ number_format($consultation->amount) }}</p>
                        </div>
                    </div>

                    @if($consultation->notes)
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <h4 class="font-bold mb-2">Additional Notes</h4>
                            <p class="text-sm text-gray-700">{{ $consultation->notes }}</p>
                        </div>
                    @endif
                    
                    <div class="flex space-x-3">
                        <a href="{{ route('dashboard.consultation.details', $consultation->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">View Details</a>
                        @if($consultation->status == 'completed')
                            <a href="{{ route('dashboard.consultation.report', $consultation->id) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Download Report</a>
                            <a href="{{ route('consultations.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Book Follow-up</a>
                        @elseif($consultation->status == 'scheduled')
                            <a href="{{ route('dashboard.consultation.reschedule', $consultation->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">Reschedule</a>
                            <button type="button" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600" onclick="openCancelModal({{ $consultation->id }})">Cancel</button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="mx-auto h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0m6 0h6m-6 4h6m-6-8h6"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Consultations Booked</h3>
            <p class="text-gray-600 mb-6">You haven't booked any consultations yet. Get personalized astrological guidance.</p>
            <a href="{{ route('consultations.index') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
                Book Your First Consultation
            </a>
        </div>
    @endif
</div>

<!-- Cancel Modal -->
<div id="cancelModal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">
    <div style="background-color:#fff; margin:15% auto; padding:20px; border-radius:8px; width:80%; max-width:500px;">
        <h3 style="margin-top:0;">Cancel Consultation</h3>
        <form method="POST" id="cancelForm">
            @csrf
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Reason for Cancellation</label>
                <textarea name="cancellation_reason" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; resize:vertical;" rows="3" placeholder="Please provide reason for cancellation..."></textarea>
            </div>
            <div style="text-align:right;">
                <button type="button" onclick="document.getElementById('cancelModal').style.display='none'" style="padding:8px 16px; margin-right:10px; background:#6b7280; color:white; border:none; border-radius:4px; cursor:pointer;">Close</button>
                <button type="submit" style="padding:8px 16px; background:#dc2626; color:white; border:none; border-radius:4px; cursor:pointer;">Cancel Consultation</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCancelModal(consultationId) {
    document.getElementById('cancelForm').action = '/dashboard/consultation/' + consultationId + '/cancel';
    document.getElementById('cancelModal').style.display = 'block';
}
</script>
@endsection