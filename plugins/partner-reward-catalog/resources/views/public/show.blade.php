@extends($layout)

@section('content')
@php
    $isId = app()->getLocale() === 'id';
    $homeLabel = t('nav.home', $isId ? 'Beranda' : 'Home');
    $breadcrumbItems = [
        ['name' => $homeLabel, 'url' => localized_url('/')],
        ['name' => $pageTitle, 'url' => url()->current()],
    ];
    $emptyTitle = $isId ? 'Katalog Reward Partner Sedang Disiapkan' : 'Partner Reward Catalog Is Coming Soon';
    $emptyDesc = $isId
        ? 'Tim kami sedang memperbarui edisi katalog reward terbaru. Silakan kembali beberapa saat lagi atau hubungi tim kami.'
        : 'Our team is currently preparing the latest catalog edition. Please check back shortly or contact our partner team.';
@endphp

<div class="relative bg-white overflow-x-clip">
  {{-- Decorative Arc Lines & Lowered Subtle Ambient Glow (CSI Brand Design System) --}}
  <img src="{{ theme_asset('images/about/about-arc-left.svg') }}" alt="" class="hidden md:block pointer-events-none select-none absolute -left-[64px] top-[140px] w-[162px] h-[717px] rotate-180 z-0" />
  <img src="{{ theme_asset('images/about/about-arc-right.svg') }}" alt="" class="hidden md:block pointer-events-none select-none absolute -right-[50px] top-[40px] w-[162px] h-[717px] z-0" />
  <img src="{{ theme_asset('images/hero-glow-layer.png') }}" alt="" class="pointer-events-none select-none absolute left-1/2 -translate-x-1/2 top-[220px] w-[897px] max-w-none h-[427px] opacity-35 blur-[110px] -scale-y-100 rotate-180 z-0" />

  {{-- 1. HERO HEADER --}}
  <section id="catalog-hero" class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] pt-[56px] pb-[40px]">
    <div class="flex flex-col items-center text-center gap-[16px]">
      <x-seo-breadcrumbs :custom-items="$breadcrumbItems" class="mb-1 justify-center" />

      <h1 id="catalog-hero-title" class="text-[34px] sm:text-[42px] xl:text-[46px] font-bold leading-[1.2] pb-1 bg-gradient-to-r from-[#04B1C7] from-[40%] to-[#2296E4] bg-clip-text text-transparent">
        {{ $pageTitle }}
      </h1>

      @if(!empty($pageSubtitle))
        <p class="text-base sm:text-[18px] leading-[26px] text-black/80 font-normal max-w-[780px]">
          {{ $pageSubtitle }}
        </p>
      @endif
    </div>
  </section>

  {{-- 2. INTERACTIVE 3D PDF FLIPBOOK VIEWER --}}
  <section id="partner-reward-catalog-root"
           data-pdf-url="{{ $pdfUrl ?? '' }}"
           class="relative z-10 max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-12 xl:px-[80px] pb-[72px]">

    @if($pdfUrl)
      <div id="flipbook-shell"
           class="relative rounded-[24px] border border-slate-700/60 bg-[#161E29] text-white shadow-[0_24px_64px_rgba(15,23,42,0.28)] overflow-hidden flex flex-col">

        {{-- Top Interactive Toolbar --}}
        <div class="relative z-20 flex flex-wrap items-center justify-between gap-3 px-4 sm:px-6 py-3.5 bg-[#0F1722] border-b border-white/15">
          {{-- File badge --}}
          <div class="flex items-center gap-2.5 min-w-0">
            <span class="w-8 h-8 rounded-lg bg-[#04B1C7]/20 border border-[#04B1C7]/50 text-[#38DDF2] flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </span>
            <span class="text-xs sm:text-sm font-semibold text-white truncate max-w-[200px] sm:max-w-[300px]">
              {{ $config['original_filename'] ?: $pageTitle }}
            </span>
          </div>

          {{-- Page Controls Center --}}
          <div class="flex items-center gap-1.5 sm:gap-2">
            <button id="flip-btn-first" type="button" title="{{ $isId ? 'Halaman Pertama' : 'First Page' }}"
                    class="w-8 h-8 rounded-lg bg-white/15 border border-white/15 text-white hover:bg-[#04B1C7] hover:border-[#04B1C7] disabled:opacity-35 disabled:hover:bg-white/15 flex items-center justify-center transition-colors cursor-pointer">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
            </button>
            <button id="flip-btn-prev" type="button" title="{{ $isId ? 'Sebelumnya' : 'Previous Page' }}"
                    class="w-8 h-8 rounded-lg bg-white/15 border border-white/15 text-white hover:bg-[#04B1C7] hover:border-[#04B1C7] disabled:opacity-35 disabled:hover:bg-white/15 flex items-center justify-center transition-colors cursor-pointer">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </button>

            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-black/45 border border-white/20 text-xs font-medium text-white">
              <input id="flip-page-input" type="number" min="1" value="1" aria-label="Page number"
                     class="w-10 text-center bg-transparent text-white font-bold focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" />
              <span id="flip-page-total" class="text-white/75">/ 1</span>
            </div>

            <button id="flip-btn-next" type="button" title="{{ $isId ? 'Berikutnya' : 'Next Page' }}"
                    class="w-8 h-8 rounded-lg bg-white/15 border border-white/15 text-white hover:bg-[#04B1C7] hover:border-[#04B1C7] disabled:opacity-35 disabled:hover:bg-white/15 flex items-center justify-center transition-colors cursor-pointer">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
            <button id="flip-btn-last" type="button" title="{{ $isId ? 'Halaman Terakhir' : 'Last Page' }}"
                    class="w-8 h-8 rounded-lg bg-white/15 border border-white/15 text-white hover:bg-[#04B1C7] hover:border-[#04B1C7] disabled:opacity-35 disabled:hover:bg-white/15 flex items-center justify-center transition-colors cursor-pointer">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
            </button>
          </div>

          {{-- Right Utility Buttons: Zoom, Thumbnails, Fullscreen, Download --}}
          <div class="flex items-center gap-1.5 sm:gap-2">
            <div class="hidden sm:flex items-center rounded-lg bg-white/15 border border-white/15 overflow-hidden text-white">
              <button id="flip-zoom-out" type="button" title="{{ $isId ? 'Perkecil' : 'Zoom Out' }}" class="px-2.5 py-1.5 hover:bg-white/20 text-xs font-bold cursor-pointer">-</button>
              <button id="flip-zoom-label" type="button" title="Reset Zoom" class="px-2 py-1.5 text-xs font-semibold text-white hover:bg-white/15 min-w-[46px] text-center cursor-pointer">100%</button>
              <button id="flip-zoom-in" type="button" title="{{ $isId ? 'Perbesar' : 'Zoom In' }}" class="px-2.5 py-1.5 hover:bg-white/20 text-xs font-bold cursor-pointer">+</button>
            </div>

            <button id="flip-thumbs-toggle" type="button" title="{{ $isId ? 'Daftar Halaman' : 'Thumbnails' }}"
                    class="h-8 px-2.5 rounded-lg bg-white/15 border border-white/15 text-white hover:bg-[#04B1C7] hover:border-[#04B1C7] text-xs font-semibold flex items-center gap-1.5 transition-colors cursor-pointer">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
              <span class="hidden md:inline">{{ $isId ? 'Halaman' : 'Pages' }}</span>
            </button>

            <button id="flip-fullscreen" type="button" title="{{ $isId ? 'Layar Penuh' : 'Fullscreen' }}"
                    class="w-8 h-8 rounded-lg bg-white/15 border border-white/15 text-white hover:bg-[#04B1C7] hover:border-[#04B1C7] flex items-center justify-center transition-colors cursor-pointer">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5h-4m4 0v-4m0 4l-5-5"/></svg>
            </button>

            @if(!empty($config['allow_download']))
              <a href="{{ $pdfUrl }}"
                 download
                 class="h-8 px-3 rounded-lg bg-[#ED7F22] hover:bg-[#d86f1b] text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V3"/></svg>
                <span class="hidden sm:inline">{{ $isId ? 'Unduh PDF' : 'Download PDF' }}</span>
              </a>
            @endif
          </div>
        </div>

        {{-- Subtle Inconsistent Dimension Advisory Banner (only shown if mixed page sizes are detected) --}}
        <div id="flip-dimension-warning" class="hidden px-4 py-2 bg-amber-500/20 border-b border-amber-400/30 text-amber-200 text-xs flex items-center justify-center gap-2 text-center">
          <svg class="w-4 h-4 shrink-0 text-amber-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
          <span>{{ $isId ? 'Sebagian halaman PDF memiliki ukuran gambar/dimensi berbeda; konten disesuaikan agar tetap muat.' : 'Some PDF pages have differing dimensions; pages are letterboxed cleanly to stay aligned.' }}</span>
        </div>

        {{-- Main Stage Canvas --}}
        <div class="relative flex-1 min-h-[500px] sm:min-h-[600px] flex items-center justify-center px-6 py-8 sm:px-14 sm:py-10 overflow-auto bg-[#161E29]"
             style="background-image: radial-gradient(circle at top, rgba(4,177,199,0.16), transparent 60%), radial-gradient(circle at bottom right, rgba(237,127,34,0.12), transparent 55%);">

          {{-- Side Floating Prev Button --}}
          <button id="flip-side-prev" type="button" aria-label="Previous spread"
                  class="hidden lg:flex absolute left-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-[#0F1722]/90 hover:bg-[#04B1C7] border border-white/25 text-white items-center justify-center shadow-lg transition-all cursor-pointer disabled:opacity-25 disabled:pointer-events-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
          </button>

          {{-- Loading Progress Overlay --}}
          <div id="flipbook-loader" class="absolute inset-0 z-20 flex flex-col items-center justify-center gap-4 bg-[#161E29]/95 px-6 text-center">
            <div class="w-12 h-12 rounded-2xl bg-[#04B1C7]/20 border border-[#04B1C7]/40 flex items-center justify-center">
              <svg class="w-6 h-6 text-[#38DDF2] animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
              </svg>
            </div>
            <p id="flipbook-loader-text" class="text-sm font-semibold text-white">
              {{ $isId ? 'Memuat Katalog PDF...' : 'Loading Catalog PDF...' }}
            </p>
            <div class="w-64 max-w-full h-2 rounded-full bg-white/15 overflow-hidden">
              <div id="flipbook-loader-bar" class="h-full w-0 bg-gradient-to-r from-[#04B1C7] to-[#2296E4] transition-all duration-200"></div>
            </div>
          </div>

          {{-- Error Fallback --}}
          <div id="flipbook-error" class="hidden absolute inset-0 z-20 flex flex-col items-center justify-center gap-3 bg-[#161E29] px-6 text-center">
            <p class="text-base font-bold text-red-300">{{ $isId ? 'Gagal memuat tampilan flipbook.' : 'Unable to render flipbook preview.' }}</p>
            <a href="{{ $pdfUrl }}" target="_blank" rel="noopener noreferrer" class="px-5 py-2.5 rounded-full bg-[#ED7F22] hover:bg-[#d86f1b] text-white text-xs font-bold transition-colors">
              {{ $isId ? 'Buka File PDF Langsung' : 'Open PDF Directly' }}
            </a>
          </div>

          {{-- Zoomable Book Stage --}}
          <div id="flipbook-viewport" class="w-full max-w-[1040px] flex items-center justify-center opacity-0 transition-[opacity,transform] duration-300 origin-center">
            <div id="flipbook-stage-book" class="mx-auto"></div>
          </div>

          {{-- Side Floating Next Button --}}
          <button id="flip-side-next" type="button" aria-label="Next spread"
                  class="hidden lg:flex absolute right-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-[#0F1722]/90 hover:bg-[#04B1C7] border border-white/25 text-white items-center justify-center shadow-lg transition-all cursor-pointer disabled:opacity-25 disabled:pointer-events-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </button>
        </div>

        {{-- Slide-Up Thumbnail Strip --}}
        <div id="flip-thumbs-drawer" class="hidden border-t border-white/15 bg-[#0F1722] px-4 py-3 flex items-center gap-3 overflow-x-auto"></div>

        {{-- Subtle Tip Footer --}}
        <div class="px-6 py-2.5 bg-[#0F1722] border-t border-white/15 flex flex-wrap items-center justify-between gap-2 text-[11px] text-white/75">
          <span>{{ $isId ? 'Tips: Tarik sudut halaman, klik panah, atau gunakan tombol panah keyboard (← / →) untuk membalik halaman.' : 'Tip: Drag a page corner, click arrows, or press keyboard arrows (← / →) to flip pages.' }}</span>
          <span>Converion Interactive Catalog</span>
        </div>
      </div>
    @else
      {{-- Graceful Empty State When No PDF Has Been Uploaded Yet --}}
      <div class="max-w-[720px] mx-auto rounded-[24px] border border-[#E4EBFA] bg-[#F7FAFF] px-8 py-14 text-center flex flex-col items-center gap-4 shadow-sm">
        <div class="w-16 h-16 rounded-2xl bg-[#04B1C7]/10 border border-[#04B1C7]/25 text-[#04B1C7] flex items-center justify-center">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
        </div>
        <h2 class="text-xl sm:text-2xl font-bold text-slate-900">{{ $emptyTitle }}</h2>
        <p class="text-sm sm:text-base text-slate-600 max-w-[520px] leading-relaxed">{{ $emptyDesc }}</p>
        <a href="#contact" class="mt-2 inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#ED7F22] hover:bg-[#d86f1b] text-white text-sm font-bold shadow-sm transition-colors">
          <span>{{ t('nav.contact_us', $isId ? 'Hubungi Kami' : 'Contact Us') }}</span>
        </a>
      </div>
    @endif
  </section>
</div>

{{-- Shared Contact Section --}}
@if(view()->exists('csi::partials.contact-section'))
  @include('csi::partials.contact-section')
@endif

@push('scripts')
  @if($pdfUrl)
    @vite(['plugins/partner-reward-catalog/resources/js/flipbook.js'])
  @endif
@endpush
@endsection
