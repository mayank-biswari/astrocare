@extends('layouts.app')

@section('title', 'Book ' . $service->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-center mb-8">Book {{ $service->name }}</h1>

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-6">
                @if($service->short_description)
                    <p class="text-gray-600 mb-4">{{ $service->short_description }}</p>
                @endif
                @php
                    // Determine which tier to display as selected
                    $oldTierId = old('tier_id', $selectedTier->id ?? ($service->tiers->first()->id ?? null));
                    $displayTier = $service->tiers->firstWhere('id', $oldTierId) ?? $selectedTier ?? $service->tiers->first();
                @endphp
                <div class="text-3xl font-bold text-indigo-600 mb-4 text-center" id="price-display">{{ formatPrice($displayTier->price ?? $service->base_price) }}</div>
            </div>

            <form action="{{ route('consultations.book') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="service_id" value="{{ $service->id }}">

                {{-- Tier Selection --}}
                @if($service->has_tiers && $service->tiers->count() > 0)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Session Duration</label>
                    <div class="space-y-3">
                        @foreach($service->tiers as $tier)
                        @php
                            $isSelected = ($oldTierId == $tier->id);
                        @endphp
                        <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all tier-option {{ $isSelected ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300' }}">
                            <input type="radio" name="tier_id" value="{{ $tier->id }}"
                                   class="tier-radio mr-3"
                                   data-price="{{ $tier->price }}"
                                   data-name="{{ $tier->name }}"
                                   {{ $isSelected ? 'checked' : '' }}>
                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-gray-800">{{ $tier->name }}</span>
                                    <span class="font-bold text-indigo-600">{{ formatPrice($tier->price) }}</span>
                                </div>
                                @if($tier->description)
                                    <p class="text-sm text-gray-500 mt-1">{{ $tier->description }}</p>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('tier_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @else
                <input type="hidden" name="tier_id" value="">
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Consultation Type</label>
                    <select name="type" id="consultation-type"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="phone" {{ old('type', $type ?? '') == 'phone' ? 'selected' : '' }}>📞 Phone Consultation</option>
                        <option value="video" {{ old('type', $type ?? '') == 'video' ? 'selected' : '' }}>📹 Video Call</option>
                        <option value="chat" {{ old('type', $type ?? '') == 'chat' ? 'selected' : '' }}>💬 Chat Consultation</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Date & Time</label>
                    @php
                        $oldScheduledAt = old('scheduled_at', '');
                        $oldDate = '';
                        $oldTime = '';
                        if ($oldScheduledAt) {
                            $parts = explode(' ', $oldScheduledAt);
                            $oldDate = $parts[0] ?? '';
                            $oldTime = $parts[1] ?? '';
                        }
                    @endphp
                    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <input type="date" id="date-input" required value="{{ $oldDate }}"
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" style="flex: 1;">
                        <input type="time" id="time-input" required value="{{ $oldTime }}"
                               class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" style="flex: 1;">
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-top: 8px; padding: 10px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                        @php
                            $timeSlots = ['10:00' => '10:00 AM', '10:30' => '10:30 AM', '11:00' => '11:00 AM', '11:30' => '11:30 AM', '14:00' => '2:00 PM', '14:30' => '2:30 PM', '15:00' => '3:00 PM', '15:30' => '3:30 PM', '16:00' => '4:00 PM', '16:30' => '4:30 PM', '17:00' => '5:00 PM', '17:30' => '5:30 PM'];
                        @endphp
                        @foreach($timeSlots as $timeValue => $timeLabel)
                        <div style="padding: 8px 4px; text-align: center; border: 1px solid {{ $oldTime == $timeValue ? '#4f46e5' : '#d1d5db' }}; border-radius: 6px; cursor: pointer; font-size: 12px; background: {{ $oldTime == $timeValue ? '#4f46e5' : 'white' }}; color: {{ $oldTime == $timeValue ? 'white' : 'inherit' }};" class="time-slot" data-time="{{ $timeValue }}">{{ $timeLabel }}</div>
                        @endforeach
                    </div>
                    <input type="hidden" name="scheduled_at" id="scheduled_at" required value="{{ $oldScheduledAt }}">
                    @error('scheduled_at')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                    <textarea name="notes" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                              placeholder="Any specific questions or concerns...">{{ old('notes') }}</textarea>
                </div>

                <!-- Captcha -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Security Check</label>
                    <div class="flex items-center space-x-4 mb-2">
                        <img src="{{ captcha_src() }}" alt="Captcha" class="border rounded">
                        <button type="button" onclick="this.previousElementSibling.src='{{ captcha_src() }}?'+Math.random()"
                                class="text-indigo-600 hover:text-indigo-800 text-sm">Refresh</button>
                    </div>
                    <input type="text" name="captcha" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                           placeholder="Enter captcha">
                    @error('captcha')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" id="book-button"
                        class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg font-bold hover:bg-indigo-700">
                    Book Consultation & Pay <span id="btn-price">{{ formatPrice($displayTier->price ?? $service->base_price) }}</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
const currencySymbol = '{{ currencySymbol() }}';

document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('date-input');
    const timeInput = document.getElementById('time-input');
    const scheduledAt = document.getElementById('scheduled_at');
    const timeSlots = document.querySelectorAll('.time-slot');
    const tierRadios = document.querySelectorAll('.tier-radio');
    const priceDisplay = document.getElementById('price-display');
    const btnPrice = document.getElementById('btn-price');

    // Set minimum date to today
    dateInput.min = new Date().toISOString().split('T')[0];

    // If we have old values, ensure scheduled_at is set
    if (dateInput.value && timeInput.value) {
        scheduledAt.value = dateInput.value + ' ' + timeInput.value;
    }

    // Handle tier selection - update price display
    tierRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            var price = parseFloat(this.dataset.price);
            var formatted = currencySymbol + new Intl.NumberFormat('en-IN', {minimumFractionDigits: 2}).format(price);
            priceDisplay.textContent = formatted;
            btnPrice.textContent = formatted;

            // Update visual selection
            document.querySelectorAll('.tier-option').forEach(function(opt) {
                opt.classList.remove('border-indigo-500', 'bg-indigo-50');
                opt.classList.add('border-gray-200');
            });
            this.closest('.tier-option').classList.remove('border-gray-200');
            this.closest('.tier-option').classList.add('border-indigo-500', 'bg-indigo-50');
        });
    });

    // Handle time slot selection
    timeSlots.forEach(function(slot) {
        slot.addEventListener('click', function() {
            timeSlots.forEach(function(s) {
                s.style.background = 'white';
                s.style.borderColor = '#d1d5db';
                s.style.color = 'inherit';
            });
            this.style.background = '#4f46e5';
            this.style.borderColor = '#4f46e5';
            this.style.color = 'white';
            timeInput.value = this.dataset.time;
            updateScheduledAt();
        });
    });

    // Handle manual time input
    timeInput.addEventListener('change', function() {
        timeSlots.forEach(function(s) {
            s.style.background = 'white';
            s.style.borderColor = '#d1d5db';
            s.style.color = 'inherit';
        });
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
