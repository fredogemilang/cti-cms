<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  {{-- Global Attribution Tracking Script --}}
  @include('cdt::partials.attribution-tracker')

  @if(setting('site_favicon'))
    <link rel="icon" href="{{ resolve_block_asset(setting('site_favicon')) }}">
  @endif

  {{-- Critical Above-The-Fold CSS (Prevents FOUC) --}}
  <style>
    [x-cloak] { display: none !important; }
    html, body {
      margin: 0;
      padding: 0;
      overflow-x: hidden !important;
      max-width: 100% !important;
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background-color: #ffffff;
      color: #18181b;
    }
    ul, ol { list-style: none; margin: 0; padding: 0; }
    a { text-decoration: none; color: inherit; }
    picture { display: inline-block; max-width: 100%; }
    img { max-width: 100%; height: auto; }

    /* Layout & Header Critical Rules */
    .flex { display: flex; }
    .items-center { align-items: center; }
    .justify-between { justify-content: space-between; }
    .w-full { width: 100%; }
    .h-full { height: 100%; }
    .h-12 { height: 3rem; }
    .h-16 { height: 4rem; }
    .w-auto { width: auto; }
    .max-w-\[1400px\] { max-width: 1400px; }
    .mx-auto { margin-left: auto; margin-right: auto; }
    .px-4 { padding-left: 1rem; padding-right: 1rem; }
    .bg-white { background-color: #ffffff; }

    @media (min-width: 1024px) {
      .lg\:hidden { display: none !important; }
      .lg\:flex { display: flex !important; }
      .lg\:block { display: block !important; }
      header#main-header {
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 100;
        background-color: #ffffff;
        border-bottom: 1px solid #f4f4f5;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
      }
    }
    @media (max-width: 1023px) {
      .lg\:hidden { display: block; }
      .hidden.lg\:flex { display: none !important; }
    }

    /* Hero Section Above-The-Fold Layout */
    .hero-section {
      position: relative;
      height: 100vh;
      display: flex;
      align-items: center;
      overflow: hidden;
    }
  </style>

  {{-- Theme Preload & Non-blocking CSS/JS Assets --}}
  <link rel="preload" as="style" href="{{ asset('themes/cdt/assets/main-V6bxgVBt.css') }}">
  <link rel="stylesheet" href="{{ asset('themes/cdt/assets/main-V6bxgVBt.css') }}" media="print" onload="this.media='all'">
  <noscript><link rel="stylesheet" href="{{ asset('themes/cdt/assets/main-V6bxgVBt.css') }}"></noscript>
  {{-- Font Preloads --}}
  <link rel="preload" as="font" type="font/woff2" href="{{ asset('themes/cdt/assets/inter-latin-wght-normal-Dx4kXJAl.woff2') }}" crossorigin>
  <link rel="preload" as="font" type="font/woff2" href="{{ asset('themes/cdt/assets/prompt-latin-400-normal-BQ9zjSN8.woff2') }}" crossorigin>

  @livewireStyles
  @stack('styles')
</head>
<body class="font-body text-dark antialiased bg-white overflow-x-hidden w-full max-w-full relative">
  
  <div x-data="{ activeSheet: null, showMenu: false }" x-effect="const isSheetOpen = activeSheet !== null; document.documentElement.style.overflow = isSheetOpen ? 'hidden' : ''; document.body.style.overflow = isSheetOpen ? 'hidden' : '';">
    
    {{-- Header Partial --}}
    @include('cdt::partials.header')

    {{-- Main Content --}}
    <main class="overflow-x-hidden" @auth style="margin-top: 100px;" @endauth>
      @yield('content')
    </main>

    {{-- Footer Partial --}}
    @include('cdt::partials.footer')

    {{-- Mobile Bottom Navigation Partial --}}
    @include('cdt::partials.mobile-nav')

  </div>

  @include('cdt::partials.cookie-banner')

  @livewireScripts
  @stack('scripts')
  <script>
    // GSAP is already loaded & registered by theme JS (main-DY6Zr0uY.js).
    // This init runs after DOM ready, reading data-gsap attributes.
    document.addEventListener('DOMContentLoaded', () => {
      if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
      document.querySelectorAll('[data-gsap]').forEach(el => {
        const type = el.getAttribute('data-gsap');
        const delay = parseFloat(el.getAttribute('data-gsap-delay') || '0');
        const defaults = { duration: 0.8, ease: 'power2.out', delay };
        let from = {};
        switch(type) {
          case 'fade-up': from = { y: 40, opacity: 0 }; break;
          case 'fade-in': from = { opacity: 0 }; defaults.duration = 0.6; break;
          case 'fade-left': from = { x: -40, opacity: 0 }; break;
          case 'fade-right': from = { x: 40, opacity: 0 }; break;
          case 'line-grow':
            gsap.fromTo(el, { width: 0 }, { width: '3rem', duration: 0.6, ease: 'power2.out', delay,
              scrollTrigger: { trigger: el.parentElement, start: 'top 85%' } });
            return;
          default: return;
        }
        gsap.fromTo(el, from, { x: 0, y: 0, opacity: 1, ...defaults,
          scrollTrigger: { trigger: el, start: 'top 90%', toggleActions: 'play none none none' }
        });
      });
    });
  </script>
</body>
</html>
