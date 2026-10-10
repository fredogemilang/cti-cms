<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#071C33">
    
    <x-seo-meta :model="$page ?? null" />

    <!-- Kouventa Local Fonts & CSS -->
    <link rel="stylesheet" href="{{ theme_asset('css/kouventa.css') }}">

    <!-- Local Alpine.js (Rule 10: No External CDN) -->
    <script defer src="{{ theme_asset('js/alpine.min.js') }}"></script>

    @stack('styles')
</head>
<body class="bg-[#F4F6F5] text-[#0E1A24] antialiased">
    <div id="wrap" class="min-h-screen flex flex-col justify-between">
        @include('kouventa::partials.header')

        <main class="flex-grow">
            @yield('content')
        </main>

        @include('kouventa::partials.footer')
    </div>

    @stack('scripts')
</body>
</html>
