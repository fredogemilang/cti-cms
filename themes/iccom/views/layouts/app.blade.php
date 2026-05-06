<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'iCCom - Indonesia Cloud Community')</title>

    <!-- Preconnect for external origins -->
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- DNS Prefetch for non-critical/deferred origins -->

    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://unpkg.com">

    <!-- Preload critical JS to start downloads immediately -->
    <link rel="preload" as="script" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js">

    <!-- Critical CSS (render-blocking - needed for layout) -->
    @if(config('app.debug'))
        {{-- Development: separate files for easy debugging --}}
        <link href="{{ asset('themes/iccom/assets/bootstrap.min.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('themes/iccom/assets/style.css') }}">
    @else
        {{-- Production: single bundled file (run: php build-css.php) --}}
        <link rel="stylesheet" href="{{ asset('themes/iccom/assets/theme.bundle.css') }}">
    @endif

    <!-- Google Fonts with preload -->
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet"></noscript>

    <!-- Non-critical CSS (deferred - not needed for initial render) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" media="print" onload="this.media='all'" />
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" /></noscript>

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"></noscript>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>

    @stack('livewire-styles')
    @stack('styles')

    <!-- JS moved to head with defer: downloads in parallel, executes in order after parsing -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" defer></script>
    <script src="{{ asset('themes/iccom/assets/bootstrap.bundle.min.js') }}" defer></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>
</head>
<body>

    @include('iccom::components.social-sidebar')
    @include('iccom::components.navigation')

    <main>
        @yield('content')
    </main>

    @include('iccom::components.footer')

    <!-- JS scripts moved to <head> with defer for parallel loading -->
    
    @stack('livewire-scripts')
    
    <!-- Sticky Nav (Vanilla JS – no Alpine dependency) -->
    <script>
        (function() {
            var lastScrollTop = 0;
            var navbar = document.querySelector('.navbar');
            if (!navbar) return;

            window.addEventListener('scroll', function() {
                var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                // Apply scrolled state (white background + shadow)
                if (scrollTop > 10) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }

                // Show/hide on scroll direction
                if (scrollTop > lastScrollTop && scrollTop > 100) {
                    navbar.classList.add('navbar-hidden');
                } else {
                    navbar.classList.remove('navbar-hidden');
                }

                lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
            }, { passive: true });
        })();
    </script>

    <!-- AOS Init (script loaded via defer in head) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {


            // Initialize AOS
            AOS.init({
                duration: 800, // values from 0 to 3000, with step 50ms
                easing: 'ease-out-cubic', // default easing for AOS animations
                once: true, // whether animation should happen only once - while scrolling down
                offset: 50, // offset (in px) from the original trigger point
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
