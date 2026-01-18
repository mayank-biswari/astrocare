<div class="bg-white p-6 rounded-lg shadow-lg">
    <h3 class="font-bold text-xl mb-3">{{ $item->title ?? $item->name }}</h3>
    <p class="text-gray-600 mb-4">{{ Str::limit(strip_tags($item->body ?? $item->description), 150) }}</p>
    @if($item->image)
        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title ?? $item->name }}" class="w-full h-48 object-cover rounded">
    @endif
</div>
