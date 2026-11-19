<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', setting('site_name', config('app.name', 'XYORA')))</title>

    @if(setting('site_favicon'))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . setting('site_favicon')) }}">
    @endif

    {{-- SEO (handled by InjectSeoTags middleware) --}}
    @stack('meta')

    {{-- Fonts (Self-hosted local WOFF2, zero external CDN) --}}
    @include('xyora::partials.fonts')
    @stack('head')

    {{-- Theme CSS --}}
    <link rel="stylesheet" href="{{ theme_asset('css/theme.css') }}">

    <style>
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        /* Premium Breadcrumbs Styling */
        nav[aria-label="Breadcrumb"] {
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 40px !important;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        nav[aria-label="Breadcrumb"] a.breadcrumb-link {
            color: #555 !important;
            text-decoration: none;
            transition: color 0.2s ease;
            opacity: 1 !important;
        }

        nav[aria-label="Breadcrumb"] a.breadcrumb-link:hover {
            color: #89C55C !important;
        }

        nav[aria-label="Breadcrumb"] .breadcrumb-current {
            color: #888 !important;
            font-weight: 500 !important;
            opacity: 1 !important;
        }

        nav[aria-label="Breadcrumb"] svg {
            color: #aaa !important;
            opacity: 1 !important;
            margin: 0 4px;
        }
    </style>

    @if(View::hasSection('needs_livewire'))
        @livewireStyles
    @endif
    @stack('styles')
</head>

<body class="{{ auth()->check() ? 'has-admin-bar' : '' }}">
    <div id="header-scroll-sentinel" aria-hidden="true" style="position:absolute;top:0;left:0;width:1px;height:16px;pointer-events:none;"></div>

    {{-- Admin Top Bar (Only rendered for logged-in admin users) --}}
    <x-admin-bar />

    {{-- Header --}}
    @include('xyora::partials.header')

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('xyora::partials.footer')

    @if(View::hasSection('needs_livewire'))
        @livewireScripts
    @endif

    @if(session('success') || session('error') || $errors->any() || View::hasSection('needs_sweetalert'))
        {{-- SweetAlert2 (Local Asset, loaded only when needed) --}}
        <script src="{{ theme_asset('js/sweetalert2.all.min.js') }}" defer></script>
    @endif

    {{-- Theme Javascript --}}
    <script src="{{ theme_asset('js/theme.js') }}" defer></script>

    {{-- SweetAlert Flash Notifications --}}
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    confirmButtonColor: '#89C55C'
                });
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    icon: 'error',
                    confirmButtonColor: '#e11d48'
                });
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: 'Pengisian Belum Lengkap',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    icon: 'warning',
                    confirmButtonColor: '#e11d48'
                });
            });
        </script>
    @endif

    @stack('scripts')
</body>

</html>