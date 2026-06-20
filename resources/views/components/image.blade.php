@props([
    'media' => null,
    'size' => 'lg',
    'sizes' => '100vw',
    'alt' => null,
    'loading' => 'lazy',
    'class' => '',
])

@php
    $data = app(\App\Services\ResponsiveImageService::class)->build($media, $size, $sizes);
    if (! $data['src']) {
        return;
    }
    $alt = $alt ?? $data['alt'] ?? '';
    $objectPosition = sprintf('%.2f%% %.2f%%', $data['focal']['x'] * 100, $data['focal']['y'] * 100);
@endphp

<picture {{ $attributes->only(['style']) }}>
    @if ($data['webp_srcset'])
        <source type="image/webp" srcset="{{ $data['webp_srcset'] }}" sizes="{{ $data['sizes'] }}">
    @endif
    <img
        src="{{ $data['src'] }}"
        srcset="{{ $data['srcset'] }}"
        sizes="{{ $data['sizes'] }}"
        @if ($data['width']) width="{{ $data['width'] }}" @endif
        @if ($data['height']) height="{{ $data['height'] }}" @endif
        alt="{{ $alt }}"
        loading="{{ $loading }}"
        decoding="async"
        class="{{ $class }}"
        @if ($data['placeholder']) style="background-image:url('{{ $data['placeholder'] }}');background-size:cover;background-position:{{ $objectPosition }};object-position:{{ $objectPosition }}" @endif
    >
</picture>
