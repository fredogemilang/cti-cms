<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="google" content="notranslate">
  <x-seo.head :entity="$entity ?? $entry ?? $page ?? $category ?? $tag ?? $term ?? $taxonomyTerm ?? null" />

  @if(setting('site_favicon'))
    <link rel="icon" href="{{ resolve_block_asset(setting('site_favicon')) }}">
  @endif

  {{-- Theme CSS & JS Assets --}}
  <script type="module" crossorigin src="{{ asset('themes/cdt/assets/main-DY6Zr0uY.js') }}"></script>
  <link rel="stylesheet" crossorigin href="{{ asset('themes/cdt/assets/main-V6bxgVBt.css') }}">
  <style>
    .prose ul, .rich-content ul {
      list-style-type: disc !important;
      padding-left: 1.25rem !important;
      margin-top: 0.75rem !important;
      margin-bottom: 0.75rem !important;
    }
    .prose ul > li, .rich-content ul > li {
      margin-top: 0.125rem !important;
      margin-bottom: 0.125rem !important;
      padding-left: 0.25rem !important;
    }
    .prose ul > li > p, .rich-content ul > li > p {
      margin-top: 0 !important;
      margin-bottom: 0.125rem !important;
    }
    .prose ul > li::marker, .rich-content ul > li::marker {
      color: #e30613 !important;
    }
    .prose ol, .rich-content ol {
      list-style-type: decimal !important;
      padding-left: 1.25rem !important;
      margin-top: 0.75rem !important;
      margin-bottom: 0.75rem !important;
    }
    .prose ol > li, .rich-content ol > li {
      margin-top: 0.125rem !important;
      margin-bottom: 0.125rem !important;
      padding-left: 0.25rem !important;
    }
    .prose ol > li > p, .rich-content ol > li > p {
      margin-top: 0 !important;
      margin-bottom: 0.125rem !important;
    }
    .prose ol > li::marker, .rich-content ol > li::marker {
      color: #e30613 !important;
      font-weight: bold !important;
    }
  </style>

  @livewireStyles
  @stack('head')
  @stack('styles')
</head>
<body class="font-body text-dark antialiased bg-white overflow-x-hidden">
  
  <div x-data="{ activeSheet: null, showMenu: true }" x-effect="const isSheetOpen = activeSheet !== null; document.documentElement.style.overflow = isSheetOpen ? 'hidden' : ''; document.body.style.overflow = isSheetOpen ? 'hidden' : '';">
    
    {{-- Header Partial --}}
    @include('cdt::partials.header')

    {{-- Main Content --}}
    <main class="overflow-x-hidden" @auth style="margin-top: 100px;" @endauth>
      @yield('content')
    </main>

    {{-- Footer Partial --}}
    @include('cdt::partials.footer')

    {{-- Mobile Navigation & Bottom Sheets --}}
    @include('cdt::partials.mobile-nav')

  </div>

  <style>
    /* Prevent Flash of Unstyled Content for GSAP Animated Elements */
    [data-gsap="fade-up"] { opacity: 0; transform: translate3d(0, 45px, 0); }
    [data-gsap="fade-in"] { opacity: 0; }
    [data-gsap="fade-left"] { opacity: 0; transform: translate3d(-45px, 0, 0); }
    [data-gsap="fade-right"] { opacity: 0; transform: translate3d(45px, 0, 0); }
    [data-gsap="line-grow"] { width: 0 !important; }
  </style>

  @livewireScripts
  @stack('scripts')
  <script>
    // GSAP is already loaded & registered by theme JS (main-DY6Zr0uY.js).
    // This init runs after DOM ready, reading data-gsap attributes.
    document.addEventListener('DOMContentLoaded', () => {
      if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

      // Store initial line-grow widths before GSAP initialization
      document.querySelectorAll('[data-gsap="line-grow"]').forEach(el => {
        const computedWidth = window.getComputedStyle(el).width;
        if (computedWidth && computedWidth !== '0px') {
          el.setAttribute('data-target-width', computedWidth);
        }
      });

      document.querySelectorAll('[data-gsap]').forEach(el => {
        const type = el.getAttribute('data-gsap');
        const delay = parseFloat(el.getAttribute('data-gsap-delay') || '0');
        const duration = parseFloat(el.getAttribute('data-gsap-duration') || '0.9');

        if (type === 'line-grow') {
          const targetWidth = el.getAttribute('data-target-width') || '4rem';
          gsap.fromTo(el, 
            { width: 0 }, 
            { 
              width: targetWidth, 
              duration: 0.8, 
              ease: 'power3.out', 
              delay,
              scrollTrigger: { 
                trigger: el.parentElement || el, 
                start: 'top 88%',
                toggleActions: 'play none none none' 
              } 
            }
          );
          return;
        }

        let from = {};
        switch(type) {
          case 'fade-up': from = { y: 45, opacity: 0 }; break;
          case 'fade-in': from = { opacity: 0 }; break;
          case 'fade-left': from = { x: -45, opacity: 0 }; break;
          case 'fade-right': from = { x: 45, opacity: 0 }; break;
          default: return;
        }

        gsap.fromTo(el, from, {
          x: 0,
          y: 0,
          opacity: 1,
          duration,
          ease: 'power3.out',
          delay,
          scrollTrigger: { 
            trigger: el, 
            start: 'top 88%', 
            toggleActions: 'play none none none' 
          }
        });
      });

      // Refresh ScrollTrigger positions after all media finishes loading
      window.addEventListener('load', () => {
        setTimeout(() => {
          if (typeof ScrollTrigger !== 'undefined') {
            ScrollTrigger.refresh();
          }
        }, 200);
      });
    });
  </script>
</body>
</html>
