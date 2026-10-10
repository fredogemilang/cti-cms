@props(['entity' => null, 'overrides' => []])

@php
    /** @var \App\Services\SeoRenderer $renderer */
    $renderer = app(\App\Services\SeoRenderer::class);
    $targetEntity = ($entity instanceof \Illuminate\Database\Eloquent\Model) ? $entity : null;
    $seo = $renderer->resolve($targetEntity, $overrides);
@endphp

<title>{{ $seo['title'] }}</title>
