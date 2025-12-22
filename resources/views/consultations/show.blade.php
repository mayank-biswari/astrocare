@extends('layouts.app')

@section('title', 'Book ' . $service->name)



@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-center mb-8">Book {{ $service->name }}</h1>
            
            <div class="mb-6">
                <p class="text-gray-600 mb-4">{{ $service->description }}</p>
                <div class="text-3xl font-bold text-indigo-600 mb-4" id="price-display">{{ formatPrice($service->price) }}</div>
            </div>

            <form action="{{ route('consultations.book') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="service_id" value="{{ $service->id }}">
                <input type="hidden" name="type" value="{{ $type }}">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Session Duration</label>
                    <select name="duration" id="duration" required onchange="updatePrice()" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="30">30 minutes - {{ formatPrice($service->price) }}</option>
                        <option value="45">45 minutes - {{ formatPrice($service->price * 1.5) }}</option>
                        <option value="60">60 minutes - {{ formatPrice($service->price * 2) }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Date & Time</label>
                    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <input type="date" id="date-input" required 
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" style="flex: 1;">
                        <input type="time" id="time-input" required 
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
                    <input type="hidden" name="scheduled_at" id="scheduled_at" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                    <textarea name="notes" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                              placeholder="Any specific questions or concerns..."></textarea>
                </div>

                <button type="submit" id="book-button"
                        class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg font-bold hover:bg-indigo-700">
                    Book Consultation & Pay {{ formatPrice($service->price) }}
                </button>
            </form>
        </div>
    </div>
</div>

<script>
const basePrice = {{ $service->price }};
const currencySymbol = '{{ currencySymbol() }}';

function updatePrice() {
    const duration = document.getElementById('duration').value;
    let multiplier = 1;
    
    if (duration == '45') multiplier = 1.5;
    else if (duration == '60') multiplier = 2;
    
    const newPrice = {{ convertPrice(1) }} * basePrice * multiplier;
    document.getElementById('price-display').textContent = currencySymbol + new Intl.NumberFormat().format(newPrice.toFixed(2));
    document.getElementById('book-button').textContent = 'Book Consultation & Pay ' + currencySymbol + new Intl.NumberFormat().format(newPrice.toFixed(2));
}

document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('date-input');
    const timeInput = document.getElementById('time-input');
    const scheduledAt = document.getElementById('scheduled_at');
    const timeSlots = document.querySelectorAll('.time-slot');
    
    // Set minimum date to today
    dateInput.min = new Date().toISOString().split('T')[0];
    
    // Handle time slot selection
    timeSlots.forEach(slot => {
        slot.addEventListener('click', function() {
            timeSlots.forEach(s => s.classList.remove('selected'));
            this.classList.add('selected');
            timeInput.value = this.dataset.time;
            updateScheduledAt();
        });
    });
    
    // Handle manual time input
    timeInput.addEventListener('change', function() {
        timeSlots.forEach(s => s.classList.remove('selected'));
        updateScheduledAt();
    });
    
    dateInput.addEventListener('change', updateScheduledAt);
    
    function updateScheduledAt() {
        if (dateInput.value && timeInput.value) {
            scheduledAt.value = dateInput.value + ' ' + timeInput.value;
        }
    }
});
</script>
@endsection