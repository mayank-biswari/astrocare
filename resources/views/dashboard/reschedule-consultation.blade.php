@extends('layouts.app')

@section('title', 'Reschedule Consultation')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold">Reschedule Consultation</h1>
                <a href="{{ route('dashboard.consultation.details', $consultation->id) }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Back
                </a>
            </div>
            
            <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                <h3 class="font-bold text-blue-800">Current Booking</h3>
                <p class="text-blue-700">{{ ucfirst($consultation->type) }} Consultation - {{ $consultation->duration }} minutes</p>
                <p class="text-blue-700">Currently scheduled: {{ $consultation->scheduled_at ? $consultation->scheduled_at->format('M d, Y \a\t g:i A') : 'Not scheduled' }}</p>
            </div>

            <form action="{{ route('dashboard.consultation.reschedule.update', $consultation->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Date & Time</label>
                    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <input type="date" id="date-input" name="date" required 
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" style="flex: 1;">
                        <input type="time" id="time-input" name="time" required 
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" style="flex: 1;">
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-top: 8px; padding: 10px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                        <div style="padding: 8px 4px; text-align: center; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; font-size: 12px; background: white;" class="time-slot" data-time="10:00">10:00 AM</div>
                        <div style="padding: 8px 4px; text-align: center; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; font-size: 12px; background: white;" class="time-slot" data-time="10:30">10:30 AM</div>
                        <div style="padding: 8px 4px; text-align: center; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; font-size: 12px; background: white;" class="time-slot" data-time="11:00">11:00 AM</div>
                        <div style="padding: 8px 4px; text-align: center; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; font-size: 12px; background: white;" class="time-slot" data-time="11:30">11:30 AM</div>
                        <div style="padding: 8px 4px; text-align: center; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; font-size: 12px; background: white;" class="time-slot" data-time="14:00">2:00 PM</div>
                        <div style="padding: 8px 4px; text-align: center; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; font-size: 12px; background: white;" class="time-slot" data-time="14:30">2:30 PM</div>
                        <div style="padding: 8px 4px; text-align: center; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; font-size: 12px; background: white;" class="time-slot" data-time="15:00">3:00 PM</div>
                        <div style="padding: 8px 4px; text-align: center; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; font-size: 12px; background: white;" class="time-slot" data-time="15:30">3:30 PM</div>
                        <div style="padding: 8px 4px; text-align: center; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; font-size: 12px; background: white;" class="time-slot" data-time="16:00">4:00 PM</div>
                        <div style="padding: 8px 4px; text-align: center; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; font-size: 12px; background: white;" class="time-slot" data-time="16:30">4:30 PM</div>
                        <div style="padding: 8px 4px; text-align: center; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; font-size: 12px; background: white;" class="time-slot" data-time="17:00">5:00 PM</div>
                        <div style="padding: 8px 4px; text-align: center; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; font-size: 12px; background: white;" class="time-slot" data-time="17:30">5:30 PM</div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rescheduling (Optional)</label>
                    <textarea name="reschedule_reason" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                              placeholder="Please let us know why you need to reschedule..."></textarea>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg font-bold hover:bg-indigo-700">
                    Confirm Reschedule
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('date-input');
    const timeInput = document.getElementById('time-input');
    const timeSlots = document.querySelectorAll('.time-slot');
    
    // Set minimum date to today
    dateInput.min = new Date().toISOString().split('T')[0];
    
    // Handle time slot selection
    timeSlots.forEach(slot => {
        slot.addEventListener('click', function() {
            timeSlots.forEach(s => s.classList.remove('selected'));
            this.classList.add('selected');
            timeInput.value = this.dataset.time;
        });
    });
    
    // Handle manual time input
    timeInput.addEventListener('change', function() {
        timeSlots.forEach(s => s.classList.remove('selected'));
    });
});
</script>
@endsection