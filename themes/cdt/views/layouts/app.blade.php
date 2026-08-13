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

  {{-- Theme assets via Vite: built from themes/cdt/assets/{css/js} sources.
       Prod reads public/build/manifest.json (committed), dev uses the Vite server. --}}
  @vite(['themes/cdt/assets/css/theme.css', 'themes/cdt/assets/js/theme.js'])
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

    /* ── Mobile Bottom Sheet Modals ── */
    @media (max-width: 767px) {
      .modal-sheet-backdrop {
        z-index: 10003 !important;
        touch-action: none;
      }
      .modal-sheet-wrapper {
        z-index: 10004 !important;
      }
      .modal-sheet-card {
        position: fixed !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        max-height: 85vh !important;
        border-radius: 1.5rem 1.5rem 0 0 !important;
        overflow: hidden !important;
        margin: 0 !important;
        display: flex !important;
        flex-direction: column !important;
      }
      .modal-sheet-card-full {
        position: fixed !important;
        inset: 0 !important;
        width: 100% !important;
        height: 100vh !important;
        max-height: 100vh !important;
        border-radius: 0 !important;
        overflow: hidden !important;
        margin: 0 !important;
        display: flex !important;
        flex-direction: column !important;
      }
      .modal-sheet-body {
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
        overscroll-behavior: contain !important;
      }
    }
  </style>


  @stack('head')
  @stack('styles')
</head>
<body class="font-body text-dark antialiased bg-white overflow-x-hidden">
  
  <div x-data="{ activeSheet: null, showMenu: false }" x-effect="const isSheetOpen = activeSheet !== null; document.documentElement.style.overflow = isSheetOpen ? 'hidden' : ''; document.body.style.overflow = isSheetOpen ? 'hidden' : '';">
    
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

  @stack('scripts')
</body>
</html>
