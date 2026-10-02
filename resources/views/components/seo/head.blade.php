@props(['entity' => null, 'overrides' => []])

@php
    /** @var \App\Services\SeoRenderer $renderer */
    $renderer = app(\App\Services\SeoRenderer::class);
    $targetEntity = $entity ?? $category ?? $tag ?? $term ?? $taxonomyTerm ?? null;
    $seo = $renderer->resolve($targetEntity, $overrides);
@endphp

<title>{{ $seo['title'] }}</title>
@if ($seo['description'])
    <meta name="description" content="{{ $seo['description'] }}">
@endif
<meta name="robots" content="{{ $seo['robots'] }}">
@if ($seo['canonical'])
    <link rel="canonical" href="{{ $seo['canonical'] }}">
@endif
@if (! empty($seo['hreflangs']))
    @foreach ($seo['hreflangs'] as $lang => $url)
        <link rel="alternate" hreflang="{{ $lang }}" href="{{ $url }}">
    @endforeach
@endif

{{-- Open Graph --}}
<meta property="og:type" content="{{ $seo['og']['type'] }}">
<meta property="og:title" content="{{ $seo['og']['title'] }}">
@if ($seo['og']['description'])
    <meta property="og:description" content="{{ $seo['og']['description'] }}">
@endif
@if ($seo['og']['image'])
    <meta property="og:image" content="{{ $seo['og']['image'] }}">
@endif
@if ($seo['og']['url'])
    <meta property="og:url" content="{{ $seo['og']['url'] }}">
@endif
<meta property="og:site_name" content="{{ $seo['og']['site_name'] }}">

{{-- Twitter --}}
<meta name="twitter:card" content="{{ $seo['twitter']['card'] }}">
<meta name="twitter:title" content="{{ $seo['twitter']['title'] }}">
@if ($seo['twitter']['description'])
    <meta name="twitter:description" content="{{ $seo['twitter']['description'] }}">
@endif
@if ($seo['twitter']['image'])
    <meta name="twitter:image" content="{{ $seo['twitter']['image'] }}">
@endif

{{-- Google Search Console verification --}}
@if ($gsc = setting('seo_google_verification'))
    <meta name="google-site-verification" content="{{ $gsc }}">
@endif

{{-- JSON-LD --}}
@if (! empty($seo['schema']))
    <script type="application/ld+json">{!! json_encode($seo['schema'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
