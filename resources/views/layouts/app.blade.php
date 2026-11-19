<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? setting('site_name', config('app.name')) }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <!-- Tailwind CSS / Vite -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: '#F53003',
                        },
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
    @endif

    @stack('styles')
</head>
@php
    $siteName = (string) setting('site_name', config('app.name'));
    $siteTagline = (string) setting('site_tagline', '');
@endphp
<body class="bg-white text-gray-900 font-sans antialiased selection:bg-gray-900 selection:text-white flex flex-col min-h-screen">

    <!-- Header / Navbar -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-gray-900/90 backdrop-blur-md border-b border-white/10 text-white">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <span class="text-xl font-extrabold tracking-tight text-white">{{ $siteName }}</span>
            </a>

            <div class="flex items-center gap-6">
                <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold">
                    <a href="{{ url('/') }}" class="text-gray-300 hover:text-white transition-colors">{{ t('nav.home', 'Home') }}</a>
                </nav>
                <x-locale-switcher />
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow pt-20">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-950 text-gray-400 py-12 border-t border-gray-900">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h3 class="text-white text-lg font-bold mb-2">{{ $siteName }}</h3>
                @if($siteTagline !== '')
                    <p class="text-gray-400 text-sm max-w-md leading-relaxed">{{ $siteTagline }}</p>
                @endif
            </div>
            <div class="pt-8 border-t border-gray-900 text-xs text-gray-500">
                <p>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <x-cookie-banner />

    @stack('scripts')
</body>
</html>
