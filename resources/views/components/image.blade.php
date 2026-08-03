@props([
    'media' => null,
    'src' => null,
    'size' => 'lg',
    'sizes' => '100vw',
    'alt' => null,
    'loading' => 'lazy',
    'class' => '',
    'pictureClass' => '',
])

@php
    $target = $media ?: $src;
    $data = app(\App\Services\ResponsiveImageService::class)->build($target, $size, $sizes);
    
    $finalSrc = $data['src'] ?: $src;
    if (! $finalSrc) {
        return;
    }
    
    $alt = $alt ?? $data['alt'] ?? '';
    $objectPosition = isset($data['focal']) ? sprintf('%.2f%% %.2f%%', $data['focal']['x'] * 100, $data['focal']['y'] * 100) : '50% 50%';

    $pictureClasses = $pictureClass ?: (str_contains($class, 'w-full') && str_contains($class, 'h-full') ? 'w-full h-full flex items-center justify-center' : '');
@endphp

<picture @if(!empty($pictureClasses)) class="{{ $pictureClasses }}" @endif {{ $attributes->only(['style']) }}>
    @if (!empty($data['webp_srcset']))
        <source type="image/webp" srcset="{{ $data['webp_srcset'] }}" sizes="{{ $data['sizes'] }}">
    @endif
    <img
        src="{{ $finalSrc }}"
        @if (!empty($data['srcset'])) srcset="{{ $data['srcset'] }}" @endif
        @if (!empty($data['sizes'])) sizes="{{ $data['sizes'] }}" @endif
        @if (!empty($data['width'])) width="{{ $data['width'] }}" @endif
        @if (!empty($data['height'])) height="{{ $data['height'] }}" @endif
        alt="{{ $alt }}"
        loading="{{ $loading }}"
        decoding="async"
        class="{{ $class }}"
        @if (!empty($data['placeholder'])) style="background-image:url('{{ $data['placeholder'] }}');background-size:cover;background-position:{{ $objectPosition }};object-position:{{ $objectPosition }}" @endif
        {{ $attributes->except(['style']) }}
    >
</picture>
