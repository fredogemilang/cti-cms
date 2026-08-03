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

  {{-- Theme Preload & Non-blocking CSS/JS Assets --}}
  <link rel="stylesheet" crossorigin href="{{ asset('themes/cdt/assets/main-V6bxgVBt.css') }}">
  {{-- Font Preloads --}}
  <link rel="preload" as="font" type="font/woff2" href="{{ asset('themes/cdt/assets/inter-latin-wght-normal-Dx4kXJAl.woff2') }}" crossorigin>
  <link rel="preload" as="font" type="font/woff2" href="{{ asset('themes/cdt/assets/prompt-latin-400-normal-BQ9zjSN8.woff2') }}" crossorigin>

  @livewireStyles
  @stack('styles')
  <style>
    html, body {
      overflow-x: hidden !important;
      max-width: 100% !important;
    }
  </style>
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
