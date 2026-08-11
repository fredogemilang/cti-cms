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

    {{-- SEO --}}
    @stack('meta')
    @if(isset($seo) && is_array($seo))
        <meta name="description" content="{{ $seo['meta_description'] ?? '' }}">
        <meta name="keywords" content="{{ $seo['meta_keywords'] ?? '' }}">
    @endif

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />

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

    @livewireStyles
    @stack('styles')
</head>

<body>

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

    @livewireScripts

    {{-- SweetAlert2 (Local Asset) --}}
    <script src="{{ theme_asset('js/sweetalert2.all.min.js') }}"></script>

    {{-- Theme Javascript --}}
    <script src="{{ theme_asset('js/theme.js') }}"></script>

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