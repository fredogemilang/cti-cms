@props([
    /** Optional Page model to pull per-page SEO overrides from. */
    'page' => null,
    /** Override title (skip the page->seo lookup). */
    'title' => null,
    /** Override description. */
    'description' => null,
    /** Override OG image URL (absolute or relative). */
    'image' => null,
    /** Override canonical URL. */
    'url' => null,
])

@php
    // Per-page overrides from pages.seo json (cast to array).
    $pageSeo = $page?->seo ?? [];

    // Compose values: explicit prop > page override > SEO setting default
    $siteName   = setting('site_name', config('app.name', 'Web CMS'));
    $tagline    = setting('site_tagline', '');
    $pageTitleBase = $title ?? ($pageSeo['title'] ?? null) ?? ($page?->title ?? $siteName);
    $isHomepage = $page && $page->slug === 'home';

    // Homepage uses site name alone; other pages use the configured pattern.
    $pattern = setting('seo_title_pattern', '{page} | {site}');
    $resolvedTitle = ($isHomepage || $pageTitleBase === $siteName)
        ? $siteName
        : strtr($pattern, [
            '{page}'    => $pageTitleBase,
            '{site}'    => $siteName,
            '{tagline}' => $tagline,
        ]);

    $resolvedDesc  = $description
        ?? ($pageSeo['description'] ?? null)
        ?? setting('seo_default_description', '');

    $resolvedImage = $image
        ?? ($pageSeo['og_image'] ?? null)
        ?? setting('seo_default_og_image', '');
    if ($resolvedImage && !preg_match('/^https?:\/\//i', $resolvedImage)) {
        $resolvedImage = url($resolvedImage);
    }

    $canonical = $url ?? ($pageSeo['canonical'] ?? null) ?? url()->current();

    $allowIndex = (bool) setting('seo_allow_indexing', true);
    $pageNoIndex = (bool) ($pageSeo['no_index'] ?? false);

    $twitter   = ltrim((string) setting('seo_twitter_handle', ''), '@');
    $locale    = setting('seo_default_locale', 'id_ID');
    $googleVer = setting('seo_google_verification', '');
    $bingVer   = setting('seo_bing_verification', '');
    $fbUrl     = setting('seo_facebook_url', '');

    $orgName = setting('seo_org_name', '') ?: $siteName;
    $orgLogo = setting('seo_org_logo', '');
    if ($orgLogo && !preg_match('/^https?:\/\//i', $orgLogo)) {
        $orgLogo = url($orgLogo);
    }
@endphp

<title>{{ $resolvedTitle }}</title>

@if($resolvedDesc)
    <meta name="description" content="{{ $resolvedDesc }}">
@endif

<link rel="canonical" href="{{ $canonical }}">

@if(!$allowIndex || $pageNoIndex)
    <meta name="robots" content="noindex,nofollow">
@endif

{{-- hreflang alternates — emitted only if the page has translations defined --}}
@if($page && method_exists($page, 'translatedLocales') && count($page->translatedLocales()) > 1)
    @foreach($page->translatedLocales() as $altLocale)
        @php $altSlug = $page->getTranslation('slug', $altLocale, false); @endphp
        @if($altSlug)
            <link rel="alternate" hreflang="{{ $altLocale }}" href="{{ url('/' . ltrim($altSlug, '/')) }}">
        @endif
    @endforeach
    {{-- x-default points to the default locale --}}
    @php $defaultSlug = $page->getTranslation('slug', \App\Models\Page::defaultLocale(), false); @endphp
    @if($defaultSlug)
        <link rel="alternate" hreflang="x-default" href="{{ url('/' . ltrim($defaultSlug, '/')) }}">
    @endif
@endif

{{-- Open Graph --}}
<meta property="og:type" content="{{ $page && $page->slug !== 'home' ? 'article' : 'website' }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $resolvedTitle }}">
@if($resolvedDesc)
    <meta property="og:description" content="{{ $resolvedDesc }}">
@endif
<meta property="og:url" content="{{ $canonical }}">
@if($resolvedImage)
    <meta property="og:image" content="{{ $resolvedImage }}">
@endif
<meta property="og:locale" content="{{ $locale }}">
@if($fbUrl)
    <meta property="article:publisher" content="{{ $fbUrl }}">
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="{{ $resolvedImage ? 'summary_large_image' : 'summary' }}">
@if($twitter)
    <meta name="twitter:site" content="{{ '@' . $twitter }}">
@endif
<meta name="twitter:title" content="{{ $resolvedTitle }}">
@if($resolvedDesc)
    <meta name="twitter:description" content="{{ $resolvedDesc }}">
@endif
@if($resolvedImage)
    <meta name="twitter:image" content="{{ $resolvedImage }}">
@endif

{{-- Search engine verification --}}
@if($googleVer)
    <meta name="google-site-verification" content="{{ $googleVer }}">
@endif
@if($bingVer)
    <meta name="msvalidate.01" content="{{ $bingVer }}">
@endif

{{-- JSON-LD Organization schema --}}
<script type="application/ld+json">
@php
    $jsonLd = array_filter([
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => $orgName,
        'url'      => url('/'),
        'logo'     => $orgLogo ?: null,
        'sameAs'   => array_values(array_filter([
            $fbUrl ?: null,
            $twitter ? "https://twitter.com/{$twitter}" : null,
        ])),
    ], fn ($v) => $v !== null && $v !== '' && $v !== []);
@endphp
{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
