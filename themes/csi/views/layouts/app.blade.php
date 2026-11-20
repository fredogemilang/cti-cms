<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="google" content="notranslate" />

  @php
    $seoCandidate = null;
    foreach ([$post ?? null, $entry ?? null, $page ?? null, $cpt ?? null, $postType ?? null, $entity ?? null, $taxonomyTerm ?? null, $term ?? null, $category ?? null, $tag ?? null] as $candidate) {
        if ($candidate instanceof \Illuminate\Database\Eloquent\Model) {
            $seoCandidate = $candidate;
            break;
        }
    }
    $searchActionUrl = function_exists('localized_url') ? localized_url('/search') : url('/search');
    $searchSuggestUrl = function_exists('trailing_slash_url') ? trailing_slash_url(url('/_csi/search-suggest')) : url('/_csi/search-suggest').'/';
    $isIdLocale = app()->getLocale() === 'id';
  @endphp

  <x-seo.head :entity="$seoCandidate" />

  @if(setting('site_favicon'))
    <link rel="icon" href="{{ resolve_block_asset(setting('site_favicon'), 'thumb') }}" />
  @endif

  @if(view()->exists('google-site-kit::partials.tracking-snippet'))
    @include('google-site-kit::partials.tracking-snippet')
  @endif

  @vite(['themes/csi/assets/css/theme.css', 'themes/csi/assets/js/theme.js'])

  @stack('head')
  @stack('styles')
</head>
<body class="font-sans text-slate-900 bg-white antialiased selection:bg-[#04B1C7] selection:text-white pb-20 lg:pb-0 {{ auth()->check() ? 'has-admin-bar' : '' }}">

  @auth
    @include('csi::partials.admin-bar')
  @endauth

  @include('csi::partials.header')

  <main id="main-content">
    @yield('content')
  </main>

  @include('csi::partials.footer')

  {{-- Search Modal Dialog --}}
  <div id="search-modal" data-lenis-prevent class="fixed inset-0 z-[100] hidden flex items-start justify-center pt-24 sm:pt-32 px-4 bg-slate-900/40 backdrop-blur-sm transition-opacity duration-200 opacity-0" aria-modal="true" role="dialog">
    <div id="search-modal-card" class="w-full max-w-[620px] bg-white rounded-[24px] shadow-[0_25px_60px_-15px_rgba(0,0,0,0.3)] border border-slate-100 overflow-hidden transform scale-95 transition-all duration-200">
      <div class="flex items-center px-5 py-4 gap-3 border-b border-slate-100">
        <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input id="search-modal-input" type="text" autocomplete="off" placeholder="{{ t('search.placeholder', $isIdLocale ? 'Cari solusi, produk, atau artikel...' : 'Search solutions, products, or articles...') }}" class="w-full text-base sm:text-lg text-slate-800 placeholder:text-slate-400 focus:outline-none bg-transparent" />
        <button id="search-modal-close" type="button" class="p-1 text-slate-400 hover:text-slate-700 transition-colors rounded-full hover:bg-slate-100 cursor-pointer" aria-label="{{ t('search.close', 'Close search') }}">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      {{-- Live Instant Autocomplete Results Container --}}
      <div id="search-modal-suggestions" class="hidden max-h-[55vh] overflow-y-auto px-5 py-4 space-y-4 bg-white border-b border-slate-100"></div>

      <div class="bg-[#F8FAFC] px-6 py-3.5 flex items-center justify-between">
        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono text-slate-400 bg-white border border-slate-200 shadow-xs">
          Esc
        </span>
        <button id="search-modal-submit" type="button" class="bg-[#ED7F22] hover:bg-[#d86f1b] text-white text-xs font-semibold px-5 py-2.5 rounded-full flex items-center gap-1.5 transition-all shadow-sm active:scale-95 cursor-pointer">
          <span>{{ t('search.submit', 'Search') }}</span>
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  @include('csi::partials.mobile-nav')

  <script>
    window.__CSI_DATA__ = Object.assign(window.__CSI_DATA__ || {}, {
      searchUrl: @json($searchActionUrl),
      searchSuggestUrl: @json($searchSuggestUrl),
      searchLocale: @json(app()->getLocale()),
      searchLabels: {
        solutions: @json($isIdLocale ? 'Solusi' : 'Solutions'),
        products: @json($isIdLocale ? 'Produk & Mitra Teknologi' : 'Products & Alliances'),
        articles: @json($isIdLocale ? 'Artikel' : 'Articles'),
        viewAll: @json($isIdLocale ? 'Lihat semua hasil untuk' : 'View all results for'),
        empty: @json($isIdLocale ? 'Tidak ada hasil instan. Tekan Enter untuk pencarian lengkap.' : 'No instant matches. Press Enter for full search.'),
      },
    });
  </script>

  @stack('scripts')
</body>
</html>
