@extends('xyora::layouts.app')

@section('title', $entry->title)

@section('content')
@php
  $meta = $entry->meta ?? [];
  $modelCode = $meta['model_code'] ?? '';
  $badge = $meta['badge'] ?? '';
  
  $rawDatasheet = $meta['datasheet_link'] ?? '#';
  $datasheetLink = '#';
  if (!empty($rawDatasheet)) {
      if (str_starts_with($rawDatasheet, 'http') || str_starts_with($rawDatasheet, '/') || $rawDatasheet === '#') {
          $datasheetLink = $rawDatasheet;
      } else {
          $datasheetLink = resolve_block_asset($rawDatasheet);
      }
  }

  // Normalize features list (handles both raw array of strings and repeater objects)
  $rawFeatures = $meta['features'] ?? [];
  $features = [];
  if (is_array($rawFeatures)) {
      foreach ($rawFeatures as $feat) {
          if (is_array($feat)) {
              $features[] = $feat['feature'] ?? '';
          } else {
              $features[] = $feat;
          }
      }
  }

  // Specs table
  $specs = $meta['specs'] ?? [];

  // Normalize applications
  $rawApplications = $meta['applications'] ?? [];
  $applications = [];
  if (is_array($rawApplications)) {
      foreach ($rawApplications as $app) {
          $appImage = $app['image'] ?? 'images/ap7.png';
          $applications[] = [
              'title' => $app['title'] ?? '',
              'image' => resolve_block_asset($appImage)
          ];
      }
  }

  $mainImg = '';
  if ($entry->featured_image) {
      $mainImg = resolve_block_asset($entry->featured_image);
  } else {
      // Try placeholder based on slug
      if (str_contains($entry->slug, 'wifi') || str_contains($entry->slug, 'access-point')) {
          $mainImg = theme_asset('images/wifi1.png');
      } elseif (str_contains($entry->slug, 'gateway')) {
          $mainImg = theme_asset('images/smart-gateway.png');
      } else {
          $mainImg = theme_asset('images/switch.png');
      }
  }

  // Normalize gallery images
  $rawGallery = $meta['gallery'] ?? [];
  $galleryImages = [];
  if (!empty($mainImg)) {
      $galleryImages[] = $mainImg;
  }
  if (is_array($rawGallery)) {
      foreach ($rawGallery as $img) {
          $resolved = resolve_block_asset($img);
          if (!empty($resolved) && $resolved !== $mainImg) {
              $galleryImages[] = $resolved;
          }
      }
  }
@endphp

<!-- Screen reader only H1 for SEO compliance -->
<h1 class="sr-only">
  {{ $entry->getTranslation('title') }} - {{ $modelCode }}
</h1>

<!-- Product Detail Top Section -->
<section class="prod-detail-section" style="padding-top: 30px; padding-bottom: 60px;">
  <div class="prod-detail-container">
    {{-- Breadcrumbs Component (Mandatory SEO breadcrumbs rule) --}}
    <x-seo-breadcrumbs :entity="$entry" class="mb-6 font-medium text-sm text-gray-500" style="display: flex; gap: 8px; margin-bottom: 40px !important;" />

    <div class="prod-detail-grid">
      <!-- Left Column: Gallery -->
      <div class="prod-detail-gallery">
        <!-- Main Display Area -->
        <div class="gallery-main-wrapper">
          <div class="pedestal-base"></div>
          <img src="{{ $mainImg }}" alt="{{ $entry->getTranslation('title') }}" class="gallery-main-img" @if(str_contains($entry->slug, 'gateway')) style="max-width: 320px !important; height: auto !important;" @endif id="main-prod-image" />
        </div>

        <!-- Thumbnail Carousel -->
        <div class="gallery-thumbs-container">
          <button class="thumb-arrow arrow-left" aria-label="Previous image" onclick="prevImage()">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
              <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
          </button>
          <div class="gallery-thumbs" id="prod-thumbs">
            @foreach($galleryImages as $index => $imgUrl)
              <div class="gallery-thumb-item {{ $index === 0 ? 'active' : '' }}" onclick="changeImage('{{ $imgUrl }}', this)">
                <img src="{{ $imgUrl }}" alt="View {{ $index + 1 }}" style="width: 100%; height: 100%; object-fit: contain; padding: 4px;" />
              </div>
            @endforeach
          </div>
          <button class="thumb-arrow arrow-right" aria-label="Next image" onclick="nextImage()">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </button>
        </div>
      </div>

      <!-- Right Column: Info & Specs Checklist -->
      <div class="prod-detail-info">
        <div class="prod-header-row">
          <div class="prod-title-model">
            <h2 class="prod-detail-title">{{ $entry->getTranslation('title') }}</h2>
            @if($modelCode)
              <span class="prod-detail-model">{{ $modelCode }}</span>
            @endif
          </div>
          @if($badge)
            <span class="prod-badge-detail">{{ $badge }}</span>
          @endif
        </div>

        <hr class="prod-detail-divider" />

        <!-- Features Checklist -->
        @if(!empty($features))
          <div class="prod-features-grid">
            @foreach($features as $feat)
              <div class="prod-feature-item">
                <span class="feature-check-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                </span>
                <span>{{ $feat }}</span>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
</section>

<!-- Tabs and Table Section -->
<section class="prod-tabs-section" style="padding-bottom: 60px;">
  <div class="prod-detail-container">
    <div class="prod-tabs-buttons">
      <button class="tab-btn active" id="tab-specs" onclick="switchTab('specs')">
        {{ t('products.tab_specs', 'Spesifikasi') }}
      </button>
      <button class="tab-btn" id="tab-datasheet" onclick="switchTab('datasheet')">
        {{ t('products.tab_datasheet', 'Datasheet') }}
      </button>
    </div>

    <!-- Specs Table -->
    <div class="specs-table-wrapper" id="content-specs" style="display: block;">
      @if(!empty($specs))
        <table class="specs-table">
          <tbody>
            @foreach($specs as $spec)
              <tr>
                <td>{{ $spec['key'] ?? '' }}</td>
                <td>{!! nl2br(e($spec['value'] ?? '')) !!}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <div style="color: #888;">{{ t('products.no_specs', 'Spesifikasi belum tersedia.') }}</div>
      @endif
    </div>

    <!-- Datasheet Content -->
    <div class="datasheet-wrapper" id="content-datasheet" style="display: none;">
      <p style="font-size: 16px;">
        {{ t('products.download_datasheet_label', 'Unduh datasheet lengkap produk di sini:') }}
        <a href="{{ $datasheetLink }}" target="_blank" style="color: #89C55C; font-weight: bold; text-decoration: underline; margin-left: 5px;">
          {{ t('products.download_here', 'Download Here') }}
        </a>
      </p>
    </div>
  </div>
</section>

<!-- Product Application Section -->
@if(!empty($applications))
<section class="product-application-section" id="product-application" style="padding-bottom: 80px;">
  <div class="prod-detail-container">
    <h2 class="app-section-title">{{ t('products.application_title', 'Bagaimana Produk Ini Diaplikasikan?') }}</h2>
    
    <div class="app-grid">
      @foreach($applications as $app)
        @php
          $url = $app['url'] ?? '';
          if (empty($url)) {
              $lowerTitle = strtolower($app['title'] ?? '');
              if (str_contains($lowerTitle, 'hotel') || str_contains($lowerTitle, 'resort')) {
                  $url = url('/usecase-hotel-resort');
              } elseif (str_contains($lowerTitle, 'gedung') || str_contains($lowerTitle, 'kantor') || str_contains($lowerTitle, 'bertingkat')) {
                  $url = url('/usecase-gedung-bertingkat');
              } elseif (str_contains($lowerTitle, 'sekolah') || str_contains($lowerTitle, 'kampus')) {
                  $url = url('/usecase-sekolah-kampus');
              } else {
                  $url = '#';
              }
          } else {
              $url = url($url);
          }
        @endphp
        <a href="{{ $url }}" class="app-card">
          <div class="app-card-img-wrapper">
            <img src="{{ $app['image'] }}" alt="{{ $app['title'] ?? '' }}" />
          </div>
          <span class="app-card-title">{{ $app['title'] ?? '' }}</span>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

@push('scripts')
<script>
  function changeImage(src, element) {
      document.getElementById('main-prod-image').src = src;
      const items = document.querySelectorAll('.gallery-thumb-item');
      items.forEach(item => item.classList.remove('active'));
      element.classList.add('active');
  }

  function prevImage() {
      const activeThumb = document.querySelector('.gallery-thumb-item.active');
      const prevThumb = activeThumb.previousElementSibling;
      if (prevThumb && prevThumb.classList.contains('gallery-thumb-item')) {
          prevThumb.click();
      }
  }

  function nextImage() {
      const activeThumb = document.querySelector('.gallery-thumb-item.active');
      const nextThumb = activeThumb.nextElementSibling;
      if (nextThumb && nextThumb.classList.contains('gallery-thumb-item')) {
          nextThumb.click();
      }
  }

  function switchTab(tab) {
      const specsBtn = document.getElementById('tab-specs');
      const datasheetBtn = document.getElementById('tab-datasheet');
      const specsContent = document.getElementById('content-specs');
      const datasheetContent = document.getElementById('content-datasheet');

      if (tab === 'specs') {
          specsBtn.classList.add('active');
          datasheetBtn.classList.remove('active');
          
          specsContent.style.display = 'block';
          datasheetContent.style.display = 'none';
      } else {
          datasheetBtn.classList.add('active');
          specsBtn.classList.remove('active');

          specsContent.style.display = 'none';
          datasheetContent.style.display = 'block';
      }
  }
</script>
@endpush
@endsection
