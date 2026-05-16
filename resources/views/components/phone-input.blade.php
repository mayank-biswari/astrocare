@php
    $countryCodes = \App\Models\CountryCode::getActive();
    $defaultCode = old('country_code', '+91');
    $phoneValue = old('phone', $value ?? '');
@endphp

<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $label ?? 'Phone' }} <span class="text-red-500">*</span></label>
    <div class="flex gap-2">
        <select name="country_code" id="country_code_{{ $id ?? 'default' }}" required
                class="w-32 px-2 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-{{ $ring ?? 'indigo' }}-500 text-sm">
            @foreach($countryCodes as $country)
                <option value="{{ $country->dial_code }}"
                        data-digits="{{ $country->phone_digits }}"
                        {{ $defaultCode == $country->dial_code ? 'selected' : '' }}>
                    {{ $country->flag }} {{ $country->dial_code }}
                </option>
            @endforeach
        </select>
        <input type="tel" name="phone" id="phone_input_{{ $id ?? 'default' }}" value="{{ $phoneValue }}" required
               placeholder="Mobile number"
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-{{ $ring ?? 'indigo' }}-500">
    </div>
    <p class="text-xs text-gray-500 mt-1" id="phone_hint_{{ $id ?? 'default' }}"></p>
    @error('phone')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
    @error('country_code')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('country_code_{{ $id ?? "default" }}');
    const input = document.getElementById('phone_input_{{ $id ?? "default" }}');
    const hint = document.getElementById('phone_hint_{{ $id ?? "default" }}');
    if (!select || !input || !hint) return;
    function update() {
        const digits = select.options[select.selectedIndex].getAttribute('data-digits');
        if (digits) { input.setAttribute('maxlength', digits); hint.textContent = 'Enter ' + digits + ' digit mobile number'; }
    }
    select.addEventListener('change', update);
    update();
});
</script>
