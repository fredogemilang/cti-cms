<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $activeTheme->name ?? 'CMS')</title>

    {{-- SEO Meta --}}
    @stack('meta')

    {{-- Theme Stylesheet --}}
    <link rel="stylesheet" href="{{ asset('themes/' . ($activeTheme->slug ?? 'SLUG') . '/assets/css/theme.css') }}">

    {{-- Additional Styles --}}
    @stack('styles')

    @livewireStyles
</head>
<body class="@yield('body-class')">

    @include($activeTheme->slug . '::partials.header')

    <main>
        @yield('content')
    </main>

    @include($activeTheme->slug . '::partials.footer')

    @livewireScripts

    {{-- Additional Scripts --}}
    @stack('scripts')
</body>
</html>