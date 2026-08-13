@extends('xyora::layouts.app')

@section('title', t('search.page_title', 'Hasil Pencarian - XYORA'))

@section('content')
  <!-- Screen reader only H1 for SEO compliance -->
  <h1 class="sr-only">
    {{ t('search.h1_seo', 'Hasil Pencarian Perangkat dan Artikel Jaringan XYORA') }}
  </h1>

  <main class="search-page-main" style="background: #ffffff; padding-top: 60px; padding-bottom: 80px;">
    <div class="search-page-container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">

      <!-- Breadcrumbs Component -->
      <x-seo-breadcrumbs :customItems="[
      ['name' => t('nav.home', 'Beranda'), 'url' => url('/')],
      ['name' => t('search.results_title', 'Hasil Pencarian'), 'url' => null]
    ]" class="mb-8" />

      <!-- Search Header -->
      <div class="search-page-header" style="margin-bottom: 50px; text-align: center;">
        <h2
          style="font-family: 'Outfit', sans-serif; font-size: 32px; font-weight: 700; color: #111e38; margin-bottom: 15px;">
          {{ t('search.title', 'Hasil Pencarian') }}
        </h2>
        <p style="font-family: 'Inter', sans-serif; font-size: 16px; color: #666; max-width: 600px; margin: 0 auto 30px;">
          @if(!empty($query))
            {!! t('search.results_for', 'Menampilkan hasil pencarian untuk: <strong>:query</strong>', ['query' => e($query)]) !!}
          @else
            {{ t('search.empty_query_desc', 'Masukkan kata kunci di bawah untuk mencari produk atau artikel.') }}
          @endif
        </p>

        <!-- Mid-page Search Form -->
        <form
          action="{{ app()->getLocale() !== setting('default_locale', 'en') ? url(app()->getLocale() . '/search') : url('/search') }}"
          method="GET"
          style="max-width: 600px; margin: 0 auto; display: flex; gap: 10px; box-shadow: 0 4px 20px rgba(17, 30, 56, 0.08); padding: 6px; border-radius: 50px; border: 1px solid #e1e8f0; background: #fff;">
          <input type="search" name="q" value="{{ $query }}"
            placeholder="{{ t('search.placeholder', 'Cari produk atau artikel...') }}" required
            style="flex: 1; border: none; outline: none; padding: 12px 24px; font-size: 15px; border-radius: 50px; font-family: 'Inter', sans-serif;" />
          <button type="submit"
            style="background: #89C55C; color: #fff; border: none; outline: none; padding: 12px 30px; font-size: 15px; font-weight: 600; border-radius: 50px; cursor: pointer; transition: background 0.2s ease; font-family: 'Outfit', sans-serif;">
            {{ t('search.button', 'Cari') }}
          </button>
        </form>
      </div>

      @if(empty($query))
        <div
          style="text-align: center; padding: 60px 20px; color: #999; font-size: 18px; font-family: 'Inter', sans-serif;">
          {{ t('search.no_query', 'Silakan masukkan kata kunci pencarian Anda.') }}
        </div>
      @elseif($blogResults->isEmpty() && $productResults->isEmpty())
        <div style="text-align: center; padding: 80px 20px; color: #666; font-family: 'Inter', sans-serif;">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5"
            stroke-linecap="round" stroke-linejoin="round" width="80" height="80" style="margin-bottom: 20px;">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.3-4.3" />
          </svg>
          <h3
            style="font-family: 'Outfit', sans-serif; font-size: 22px; font-weight: 600; color: #111e38; margin-bottom: 10px;">
            {{ t('search.no_results_title', 'Tidak Menemukan Hasil') }}
          </h3>
          <p style="font-size: 15px; color: #888;">
            {{ t('search.no_results_desc', 'Maaf, tidak ada produk atau artikel yang cocok dengan kata kunci tersebut. Coba kata kunci yang lain.') }}
          </p>
        </div>
      @else

        <!-- 1. Product Results (ON TOP) -->
        @if(!$productResults->isEmpty())
          <section class="search-product-results" style="margin-bottom: 70px;">
            <div
              style="border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 30px; display: flex; align-items: center; gap: 10px;">
              <span
                style="background: rgba(137, 197, 92, 0.1); color: #89C55C; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold;">
                🔌
              </span>
              <h3 style="font-family: 'Outfit', sans-serif; font-size: 24px; font-weight: 700; color: #111e38; margin: 0;">
                {{ t('search.products_section_title', 'Perangkat & Solusi Jaringan') }}
                <span
                  style="font-size: 16px; font-weight: 500; color: #888; margin-left: 8px;">({{ $productResults->count() }})</span>
              </h3>
            </div>

            <div class="products-grid"
              style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 30px;">
              @foreach($productResults as $prod)
                @php
                  $meta = $prod->meta ?? [];
                  $modelCode = $meta['model_code'] ?? '';
                  $prodImg = '';
                  if ($prod->featured_image) {
                    $prodImg = resolve_block_asset($prod->featured_image);
                  } else {
                    if (str_contains($prod->slug, 'wifi') || str_contains($prod->slug, 'access-point')) {
                      $prodImg = theme_asset('images/wifi1.png');
                    } elseif (str_contains($prod->slug, 'gateway')) {
                      $prodImg = theme_asset('images/smart-gateway.png');
                    } else {
                      $prodImg = theme_asset('images/switch.png');
                    }
                  }
                @endphp

                <!-- Product Card -->
                <a href="{{ $prod->getUrl() }}" class="product-item-card" style="text-decoration: none;">
                  <div class="product-img-holder">
                    <img src="{{ $prodImg }}" alt="{{ $prod->getTranslation('title') }}" />
                  </div>
                  <div class="product-text-holder">
                    <h3 class="product-name">
                      {!! $prod->getTranslation('title') !!}
                    </h3>
                    @if($modelCode)
                      <span class="product-model">{{ $modelCode }}</span>
                    @endif
                  </div>
                </a>
              @endforeach
            </div>
          </section>
        @endif

        <!-- 2. Blog Results (BELOW PRODUCTS) -->
        @if(!$blogResults->isEmpty())
          <section class="search-blog-results">
            <div
              style="border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 30px; display: flex; align-items: center; gap: 10px;">
              <span
                style="background: rgba(137, 197, 92, 0.1); color: #89C55C; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold;">
                📰
              </span>
              <h3 style="font-family: 'Outfit', sans-serif; font-size: 24px; font-weight: 700; color: #111e38; margin: 0;">
                {{ t('search.blog_section_title', 'Artikel') }}
                <span
                  style="font-size: 16px; font-weight: 500; color: #888; margin-left: 8px;">({{ $blogResults->count() }})</span>
              </h3>
            </div>

            <div class="artikel-grid">
              @foreach($blogResults as $post)
                <!-- Article Card -->
                <div class="artikel-card">
                  <div class="artikel-img-wrapper">
                    @if($post->featured_image)
                      <img src="{{ resolve_block_asset($post->featured_image) }}" alt="{{ $post->title }}" class="artikel-image"
                        style="width: 100%; height: 100%; object-fit: cover;" />
                    @else
                      <div class="artikel-img-placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                          stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="64" height="64">
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
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                          stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="12" height="12">
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
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">
                        <polyline points="9 18 15 12 9 6"></polyline>
                      </svg>
                    </a>
                  </div>
                </div>
              @endforeach
            </div>
          </section>
        @endif

      @endif

    </div>
  </main>
@endsection