{{-- LMS Stats Card Partial --}}
{{-- Can be included via @include('lms.partials.stats-card', ['title' => '...', 'value' => ..., 'icon' => '...', 'color' => '...', 'link' => '...']) --}}
{{--
    Required variables:
    - $title (string): Card title
    - $value (string|int): The number/stat to display
    - $icon (string): Font Awesome icon class (e.g., 'fas fa-users')
    - $color (string): Color theme (e.g., 'indigo', 'green', 'red', 'yellow')

    Optional variables:
    - $link (string|null): URL to navigate to when clicked
--}}

@php
    $colorClasses = [
        'indigo' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'ring' => 'ring-indigo-200'],
        'green'  => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'ring' => 'ring-green-200'],
        'red'    => ['bg' => 'bg-red-100', 'text' => 'text-red-600', 'ring' => 'ring-red-200'],
        'yellow' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'ring' => 'ring-yellow-200'],
        'blue'   => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'ring' => 'ring-blue-200'],
        'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'ring' => 'ring-purple-200'],
        'gray'   => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'ring' => 'ring-gray-200'],
    ];
    $colors = $colorClasses[$color] ?? $colorClasses['indigo'];
    $tag = isset($link) && $link ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if($tag === 'a') href="{{ $link }}" @endif
    class="bg-white rounded-lg border border-gray-200 p-5 flex items-center space-x-4 transition-all hover:shadow-md {{ $tag === 'a' ? 'hover:border-gray-300 cursor-pointer' : '' }}"
>
    <div class="shrink-0 w-12 h-12 {{ $colors['bg'] }} rounded-lg flex items-center justify-center ring-1 {{ $colors['ring'] }}">
        <i class="{{ $icon }} {{ $colors['text'] }} text-lg"></i>
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-gray-500 truncate">{{ $title }}</p>
        <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
    </div>
    @if($tag === 'a')
        <div class="shrink-0">
            <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
        </div>
    @endif
</{{ $tag }}>
