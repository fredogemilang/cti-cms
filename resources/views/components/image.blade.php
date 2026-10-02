@props([
    'media' => null,
    'src' => null,
    'size' => 'lg',
    'sizes' => '100vw',
    'alt' => null,
    'loading' => 'lazy',
    'decoding' => 'async',
    'class' => '',
    'pictureClass' => '',
    'placeholder' => true,
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

    $pictureClasses = $pictureClass ?: (str_contains($class, 'w-full') && str_contains($class, 'h-full') ? 'w-full h-full flex items-center justify-center' : 'contents');

    $cleanPath = strtolower((string) parse_url((string) $finalSrc, PHP_URL_PATH));
    $ext = pathinfo($cleanPath, PATHINFO_EXTENSION);

    $isSvg = $ext === 'svg' || str_ends_with(strtolower((string) $finalSrc), '.svg');

    $finalClass = $class;
    if ($isSvg && ! preg_match('/(?<![a-z-])w-(full|auto|\[|\d+)/', $class)) {
        $finalClass = 'w-full '.$class;
    }

    $usePlaceholder = filter_var($placeholder, FILTER_VALIDATE_BOOLEAN) && !empty($data['placeholder']);
    if (in_array($ext, ['png', 'webp', 'gif', 'svg'], true)) {
        $usePlaceholder = false;
    }
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
        decoding="{{ $decoding }}"
        class="{{ $finalClass }}"
        @if ($usePlaceholder) style="background-image:url('{{ $data['placeholder'] }}');background-size:cover;background-position:{{ $objectPosition }};object-position:{{ $objectPosition }}" onload="this.style.backgroundImage='none'" @endif
        {{ $attributes->except(['style']) }}
    >
</picture>
