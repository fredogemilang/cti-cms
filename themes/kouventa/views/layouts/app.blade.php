<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#071C33">
    
    @if($siteFavicon = setting('site_favicon'))
        <link rel="icon" href="{{ resolve_block_asset($siteFavicon) }}">
        <link rel="apple-touch-icon" href="{{ resolve_block_asset($siteFavicon) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif

    @php
        $seoCandidate = null;
        foreach ([$post ?? null, $entry ?? null, $page ?? null, $cpt ?? null, $postType ?? null, $entity ?? null, $taxonomyTerm ?? null, $term ?? null, $category ?? null, $tag ?? null] as $candidate) {
            if ($candidate instanceof \Illuminate\Database\Eloquent\Model) {
                $seoCandidate = $candidate;
                break;
            }
        }
    @endphp
    <x-seo.head :entity="$seoCandidate" />

    <!-- Kouventa Local Fonts & CSS -->
    <link rel="stylesheet" href="{{ theme_asset('css/kouventa.css') }}?v={{ time() }}">

    <!-- Local Alpine.js (Rule 10: No External CDN) -->
    <script defer src="{{ theme_asset('js/alpine.min.js') }}"></script>

    <!-- Local Motion & Scroll Reveal Engine (Rule 10: No External CDN) -->
    <script defer src="{{ theme_asset('js/kouventa-motion.js') }}?v={{ time() }}"></script>

    @if(app()->getLocale() === 'id')
        <!-- Local Localization Engine for Indonesian (Rule 10: No External CDN) -->
        <script defer src="{{ theme_asset('js/kouventa-i18n-id.js') }}?v={{ file_exists(theme_path('assets/js/kouventa-i18n-id.js')) ? filemtime(theme_path('assets/js/kouventa-i18n-id.js')) : time() }}"></script>
    @endif

    @stack('styles')
</head>
<body class="bg-[#F4F6F5] text-[#0E1A24] antialiased">
    <div id="wrap" class="min-h-screen flex flex-col justify-between"
         x-data="{ activeSheet: null, showMenu: true }"
         x-effect="const isSheetOpen = activeSheet !== null; document.documentElement.style.overflow = isSheetOpen ? 'hidden' : ''; document.body.style.overflow = isSheetOpen ? 'hidden' : '';">
        @include('kouventa::partials.header')

        <main class="flex-grow">
            @yield('content')
        </main>

        @include('kouventa::partials.footer')
        @include('kouventa::partials.mobile-nav')
    </div>

    @stack('scripts')
</body>
</html>
