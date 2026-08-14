@extends('xyora::layouts.app')

@section('title', t('blog.title', 'XYORA - Artikel'))

@section('content')
<!-- Screen reader only H1 for SEO compliance -->
<h1 class="sr-only">
  {{ t('blog.h1_seo', 'XYORA - Solusi Jaringan Wi-Fi Premium dengan Desain Estetis') }}
</h1>

<!-- Main Content Area -->
<main>
  <!-- Artikel Section -->
  <section class="artikel-section" id="artikel" aria-label="Baca Lebih Lanjut" style="background: #ffffff; padding-top: 80px; padding-bottom: 80px;">
    <div class="artikel-container">
      <h2 class="artikel-title">{{ t('blog.title', 'Baca Lebih Lanjut') }}</h2>
      <p class="artikel-subtitle">
        {{ t('blog.subtitle', 'Temukan berbagai artikel terbaru tentang teknologi jaringan, mulai dari gateway, switch, dan wireless access point hingga tips memilih perangkat terbaik sesuai kebutuhan rumah hingga bisnis Anda.') }}
      </p>

      @if(request('q'))
        <div class="search-results-info" style="margin-bottom: 30px; font-size: 16px; color: #666;">
          {!! t('blog.search_results', 'Menampilkan hasil pencarian untuk: <strong>:query</strong>', ['query' => e(request('q'))]) !!}
          <a href="{{ url(request()->path()) }}" style="margin-left: 10px; color: #89C55C; text-decoration: underline;">{{ t('blog.clear_search', 'Reset') }}</a>
        </div>
      @endif

      @if($posts->isEmpty())
        <div class="no-articles-message" style="text-align: center; padding: 60px 20px; color: #999; font-size: 18px;">
          {{ t('blog.no_articles', 'Belum ada artikel yang tersedia saat ini.') }}
        </div>
      @else
        <div class="artikel-grid">
          @foreach($posts as $post)
            <!-- Article Card -->
            <div class="artikel-card">
              <div class="artikel-img-wrapper">
                @if($post->featured_image)
                  <x-image :src="$post->featured_image" alt="{{ $post->title }}" class="artikel-image w-full h-full object-cover" sizes="100vw" />
                @else
                  <div class="artikel-img-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="64" height="64">
                      <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                      <circle cx="8.5" cy="8.5" r="1.5"></circle>
                      <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                  </div>
                @endif
              </div>
              <div class="artikel-content">
                <div class="artikel-meta">
                  <span class="meta-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="12" height="12">
                      <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                      <line x1="16" y1="2" x2="16" y2="6"></line>
                      <line x1="8" y1="2" x2="8" y2="6"></line>
                      <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    {{ $post->published_at ? $post->published_at->translatedFormat('d F Y') : '' }}
                  </span>
                </div>
                <h3 class="artikel-card-title">
                  {{ $post->getTranslation('title') }}
                </h3>
                <p class="artikel-excerpt" style="font-size: 14px; color: #666; line-height: 1.6; margin-bottom: 15px;">
                  {{ Str::limit(strip_tags($post->getTranslation('content')), 120) }}
                </p>
                <a href="{{ $post->getUrl() }}" class="artikel-link">
                  {{ t('blog.read_more', 'Baca lebih lanjut') }}
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">
                    <polyline points="9 18 15 12 9 6"></polyline>
                  </svg>
                </a>
              </div>
            </div>
          @endforeach
        </div>

        <!-- Pagination Controls -->
        @if($posts->hasPages())
          <div class="pagination-container">
            {{-- Previous Page Link --}}
            @if ($posts->onFirstPage())
              <span class="page-btn disabled" aria-label="Previous">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
              </span>
            @else
              <a href="{{ $posts->previousPageUrl() }}" class="page-btn" aria-label="Previous">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
              </a>
            @endif

            {{-- Page Numbers --}}
            <div class="page-numbers">
              @php
                $currentPage = $posts->currentPage();
                $lastPage = $posts->lastPage();
                
                $start = max(1, $currentPage - 1);
                $end = min($lastPage, $currentPage + 1);

                if ($start > 1) {
                    $start = max(1, $end - 2);
                }
                if ($end < $lastPage) {
                    $end = min($lastPage, $start + 2);
                }
              @endphp

              {{-- First page --}}
              @if($start > 1)
                <a href="{{ $posts->url(1) }}" class="page-num">1</a>
                @if($start > 2)
                  <span class="page-dots">...</span>
                @endif
              @endif

              {{-- Main page numbers --}}
              @for($page = $start; $page <= $end; $page++)
                @if($page == $currentPage)
                  <span class="page-num active">{{ $page }}</span>
                @else
                  <a href="{{ $posts->url($page) }}" class="page-num">{{ $page }}</a>
                @endif
              @endfor

              {{-- Last page --}}
              @if($end < $lastPage)
                @if($end < $lastPage - 1)
                  <span class="page-dots">...</span>
                @endif
                <a href="{{ $posts->url($lastPage) }}" class="page-num">{{ $lastPage }}</a>
              @endif
            </div>

            {{-- Next Page Link --}}
            @if ($posts->hasMorePages())
              <a href="{{ $posts->nextPageUrl() }}" class="page-btn" aria-label="Next">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
              </a>
            @else
              <span class="page-btn disabled" aria-label="Next">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
              </span>
            @endif
          </div>
        @endif
      @endif
    </div>
  </section>
</main>
@endsection
