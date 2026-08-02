<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? setting('site_name', config('app.name', 'Central Data Technology')) }}</title>

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
<body class="bg-white text-gray-900 font-sans antialiased selection:bg-red-500 selection:text-white flex flex-col min-h-screen">

    <!-- Header / Navbar -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-gray-900/90 backdrop-blur-md border-b border-white/10 text-white">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <span class="text-xl font-extrabold tracking-tight text-white">Central Data <span class="text-red-500">Technology</span></span>
            </a>
            
            <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold">
                <a href="{{ url('/') }}" class="text-gray-300 hover:text-white transition-colors">Home</a>
                <a href="{{ url('/solution/cloud') }}" class="text-gray-300 hover:text-white transition-colors">Solutions</a>
                <a href="{{ url('/about-us') }}" class="text-gray-300 hover:text-white transition-colors">About Us</a>
                <a href="{{ url('/contact') }}" class="text-gray-300 hover:text-white transition-colors">Contact</a>
            </nav>

            <div class="flex items-center gap-4">
                <x-locale-switcher />
                <a href="{{ url('/contact') }}" class="hidden sm:inline-flex items-center px-5 py-2.5 rounded-full bg-red-600 hover:bg-red-700 text-white text-xs font-bold uppercase tracking-wider transition-all shadow-lg shadow-red-600/20">
                    Get in Touch
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow pt-20">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-950 text-gray-400 py-16 border-t border-gray-900">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                <div class="md:col-span-2">
                    <h3 class="text-white text-lg font-bold mb-4">Central Data Technology</h3>
                    <p class="text-gray-400 text-sm max-w-md leading-relaxed">
                        Leading IT infrastructure and digital transformation solution provider delivering cutting-edge enterprise technologies across Southeast Asia.
                    </p>
                </div>
                <div>
                    <h4 class="text-white text-sm font-bold uppercase tracking-wider mb-4">Solutions</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ url('/solution/cloud') }}" class="hover:text-white transition-colors">Cloud</a></li>
                        <li><a href="{{ url('/solution/security') }}" class="hover:text-white transition-colors">Security</a></li>
                        <li><a href="{{ url('/solution/observability') }}" class="hover:text-white transition-colors">Observability</a></li>
                        <li><a href="{{ url('/solution/analytics') }}" class="hover:text-white transition-colors">Analytics</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white text-sm font-bold uppercase tracking-wider mb-4">Company</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ url('/about-us') }}" class="hover:text-white transition-colors">About Us</a></li>
                        <li><a href="{{ url('/career') }}" class="hover:text-white transition-colors">Careers</a></li>
                        <li><a href="{{ url('/contact') }}" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-gray-900 flex flex-col md:flex-row items-center justify-between text-xs text-gray-500">
                <p>&copy; {{ date('Y') }} Central Data Technology. All rights reserved.</p>
                <p class="mt-4 md:mt-0">Enterprise Technology Partner</p>
            </div>
        </div>
    </footer>

    <x-cookie-banner />

    @stack('scripts')
</body>
</html>
