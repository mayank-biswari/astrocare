@extends('dashboard.layout')

@section('title', 'Consultation Details - Dashboard')

@section('dashboard-content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm mb-4 sm:mb-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <div class="flex items-center">
        <a href="{{ route('dashboard.consultations') }}" class="text-white hover:text-white/80 mr-4">← Back to Consultations</a>
        <h1 class="text-xl sm:text-2xl font-bold">Consultation Details</h1>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
    <div class="grid md:grid-cols-2 gap-4 sm:gap-8">
                <!-- Consultation Info -->
                <div>
                    <h2 class="text-lg sm:text-xl font-bold mb-4">Consultation Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Type</label>
                            <p class="text-lg">{{ ucfirst($consultation->type) }} Consultation</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Status</label>
                            <span class="px-3 py-1 rounded-full text-sm
                                @if($consultation->status == 'completed') bg-green-100 text-green-800
                                @elseif($consultation->status == 'scheduled') bg-blue-100 text-blue-800
                                @elseif($consultation->status == 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($consultation->status) }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Date & Time</label>
                            <p class="text-lg">{{ $consultation->date ? $consultation->date->format('M d, Y') : 'Not scheduled' }} {{ $consultation->time ? 'at ' . $consultation->time : '' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Duration</label>
                            <p class="text-lg">{{ $consultation->duration }} minutes</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Amount Paid</label>
                            <p class="text-lg font-bold text-indigo-600">₹{{ number_format($consultation->amount) }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Contact & Notes -->
                <div>
                    <h2 class="text-lg sm:text-xl font-bold mb-4">Contact Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Phone</label>
                            <p class="text-lg">{{ $consultation->phone ?? 'Not provided' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Email</label>
                            <p class="text-lg">{{ $consultation->email ?? auth()->user()->email }}</p>
                        </div>
                        
                        @if($consultation->notes)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Additional Notes</label>
                            <div class="bg-gray-50 rounded-lg p-4 mt-2">
                                <p class="text-gray-700">{{ $consultation->notes }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($consultation->reschedule_reason)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Reschedule Reason</label>
                            <div class="bg-yellow-50 rounded-lg p-4 mt-2">
                                <p class="text-yellow-800">{{ $consultation->reschedule_reason }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($consultation->cancellation_reason)
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Cancellation Reason</label>
                            <div class="bg-red-50 rounded-lg p-4 mt-2">
                                <p class="text-red-800">{{ $consultation->cancellation_reason }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="border-t pt-4 sm:pt-6 mt-4 sm:mt-8">
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    @if($consultation->status == 'completed')
                        <a href="{{ route('dashboard.consultation.report', $consultation->id) }}" class="px-4 sm:px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-center text-sm sm:text-base">
                            Download Report
                        </a>
                        <a href="{{ route('consultations.index') }}" class="px-4 sm:px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center text-sm sm:text-base">
                            Book Follow-up
                        </a>
                    @elseif($consultation->status == 'scheduled')
                        <a href="{{ route('dashboard.consultation.reschedule', $consultation->id) }}" class="px-4 sm:px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 text-center text-sm sm:text-base">
                            Reschedule
                        </a>
                        <button type="button" class="px-4 sm:px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm sm:text-base" onclick="document.getElementById('cancelModal').style.display='block'">
                            Cancel Consultation
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div id="cancelModal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">
    <div style="background-color:#fff; margin:15% auto; padding:20px; border-radius:8px; width:80%; max-width:500px;">
        <h3 style="margin-top:0;">Cancel Consultation</h3>
        <form method="POST" action="{{ route('dashboard.consultation.cancel', $consultation->id) }}">
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
@endsection