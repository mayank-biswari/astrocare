@extends('layouts.app')

@section('title', $page->meta_title ?: $page->title)

@if($page->meta_description)
@section('meta_description', $page->meta_description)
@endif

@if($page->meta_keywords)
@section('meta_keywords', $page->meta_keywords)
@endif

@section('content')
@if($page->custom_css)
<style>
{!! $page->custom_css !!}
</style>
@endif

@foreach($page->external_css ?? [] as $css)
    @if($css)
        <link rel="stylesheet" href="{{ $css }}">
    @endif
@endforeach

<div class="">
    @foreach($page->sections ?? [] as $section)

        @if($section['type'] === 'list' && !empty($section['list_id']))
        <div class="{{ $section['section_classes'] ?? '' }}">
            @php
                $items = $page->getSectionData($section);
                $layout = $section['layout'] ?? 'grid';
            @endphp

            @if($items->count() > 0)
                @if(!empty($section['custom_template']))
                    @if(str_starts_with($section['custom_template'], 'view:'))
                        @php
                            $viewName = 'dynamic-pages.custom-templates.' . substr($section['custom_template'], 5);
                        @endphp
                        @if(view()->exists($viewName))
                            @include($viewName, ['items' => $items, 'section' => $section, 'loopIndex' => $loop->index])
                        @else
                            <div class="alert alert-warning">Template view not found: {{ $viewName }}</div>
                        @endif
                    @else
                        @foreach($items as $item)
                            {!! str_replace(
                                ['{{item}}', '{{title}}', '{{body}}', '{{image}}', '{{slug}}', '{{url}}'],
                                [
                                    json_encode($item),
                                    $item->title ?? $item->name ?? '',
                                    $item->body ?? $item->description ?? '',
                                    $item->image ? asset('storage/' . $item->image) : ($item->image ?? ''),
                                    $item->slug ?? '',
                                    isset($item->name) ? route('product.show', ['id' => $item->id, 'slug' => $item->slug]) : route('cms.show', $item->slug)
                                ],
                                $section['custom_template']
                            ) !!}
                        @endforeach
                    @endif
                @elseif($layout === 'grid')
                    @include('dynamic-pages.layouts.default-grid', ['items' => $items, 'section' => $section])
                @elseif($layout === 'list')
                    @include('dynamic-pages.layouts.default-list', ['items' => $items, 'section' => $section])
                @elseif($layout === 'slider')
                    @include('dynamic-pages.layouts.default-slider', ['items' => $items, 'section' => $section, 'loopIndex' => $loop->index])
                @endif
            @endif
        </div>
        @elseif($section['type'] === 'html')
            {!! $section['html'] ?? '' !!}
        @endif

    @endforeach
</div>

@if($page->custom_js)
<script>
{!! $page->custom_js !!}
</script>
@endif

@foreach($page->external_js ?? [] as $js)
    @if($js)
        <script src="{{ $js }}"></script>
    @endif
@endforeach

@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
<style>
.slick-prev, .slick-next {
    font-size: 0;
    line-height: 0;
    position: absolute;
    top: 50%;
    display: block;
    width: 20px;
    height: 20px;
    padding: 0;
    transform: translate(0, -50%);
    cursor: pointer;
    color: transparent;
    border: none;
    outline: none;
    background: transparent;
}
.slick-prev:hover, .slick-next:hover {
    color: transparent;
    outline: none;
    background: transparent;
}
.slick-prev:before, .slick-next:before {
    font-family: 'slick';
    font-size: 20px;
    line-height: 1;
    opacity: .75;
    color: #000;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
.slick-prev {
    left: -25px;
}
.slick-prev:before {
    content: '←';
}
.slick-next {
    right: -25px;
}
.slick-next:before {
    content: '→';
}
</style>
@endpush

@push('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script>
$(document).ready(function(){
    @foreach($page->sections ?? [] as $index => $section)
        @if($section['type'] === 'list' && ($section['layout'] ?? '') === 'slider')
            console.log('Section {{ $index }}:', @json($section));
            $('.slider-{{ $index }}').slick({
                dots: {{ isset($section['show_dots']) ? ($section['show_dots'] ? 'true' : 'false') : 'true' }},
                arrows: {{ isset($section['show_arrows']) ? ($section['show_arrows'] ? 'true' : 'false') : 'true' }},
                autoplay: {{ isset($section['auto_rotate']) ? ($section['auto_rotate'] ? 'true' : 'false') : 'true' }},
                autoplaySpeed: {{ $section['duration'] ?? 3000 }},
                infinite: true,
                speed: 300,
                slidesToShow: {{ $section['slides_to_show'] ?? 3 }},
                slidesToScroll: {{ $section['slides_to_scroll'] ?? 1 }},
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: {{ min($section['slides_to_show'] ?? 3, 2) }},
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            });
        @endif
    @endforeach
});
</script>
@endpush
@endsection
