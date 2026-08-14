@extends('xyora::layouts.app')

@section('title', t('products.title', 'XYORA - Semua Produk'))

@section('content')
<!-- Screen reader only H1 for SEO compliance -->
<h1 class="sr-only">
  {{ t('products.h1_seo', 'XYORA - Semua Produk Jaringan Premium dengan Desain Estetis') }}
</h1>

@php
  $productCategoryTax = \App\Models\CustomTaxonomy::where('slug', 'product_category')->first();
  $categories = $productCategoryTax
      ? $productCategoryTax->terms()->orderBy('order')->get()
      : collect();

  $selectedCat = request('category');

  $productsQuery = \App\Models\CptEntry::whereHas('postType', function($q) {
      $q->where('slug', 'products');
  })->where('status', 'published');

  if ($selectedCat) {
      $productsQuery->whereHas('terms', function($q) use ($selectedCat) {
          $q->where('taxonomy_terms.id', $selectedCat);
      });
  }

  $products = $productsQuery->orderBy('menu_order')->paginate(12);
@endphp

<!-- Main Content Area -->
<main class="products-page-main" style="padding-top: 80px; padding-bottom: 80px;">
  <div class="products-page-container">
    <!-- Page Title -->
    <h1 class="products-page-title">{{ t('products.heading', 'Semua Produk') }}</h1>

    <div class="products-layout-wrapper">
      <!-- Sidebar Filter -->
      <aside class="products-sidebar">
        <div class="filter-card">
          <h2 class="filter-title">{{ t('products.filter', 'Filter') }}</h2>
          <div class="filter-group">
            <button class="filter-group-header" aria-expanded="true">
              <span>{{ t('products.categories', 'Kategori') }}</span>
              <svg class="chevron-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="14" height="14">
                <path d="m6 9 6 6 6-6" />
              </svg>
            </button>
            <ul class="filter-checkbox-list">
              <li>
                <label class="checkbox-label">
                  <input type="checkbox" onclick="location.href='{{ url('/products') }}'" {{ !$selectedCat ? 'checked' : '' }} />
                  <span class="custom-checkbox"></span>
                  {{ t('products.all', 'Semua Kategori') }}
                </label>
              </li>
              @foreach($categories as $cat)
                <li>
                  <label class="checkbox-label">
                    <input type="checkbox" onclick="location.href='{{ url('/products?category=' . $cat->id) }}'" {{ $selectedCat == $cat->id ? 'checked' : '' }} />
                    <span class="custom-checkbox"></span>
                    {{ $cat->name }}
                  </label>
                </li>
              @endforeach
            </ul>
          </div>
        </div>
      </aside>

      <!-- Products Grid -->
      <section class="products-grid-section">
        @if($products->isEmpty())
          <div class="no-products-message" style="text-align: center; padding: 40px; color: #999;">
            {{ t('products.no_products', 'Tidak ada produk yang cocok dengan filter.') }}
          </div>
        @else
          <div class="products-grid">
            @foreach($products as $prod)
              @php
                $meta = $prod->meta ?? [];
                $modelCode = $meta['model_code'] ?? '';
                $prodImg = $prod->featured_image;
                if (! $prodImg) {
                    // Try placeholder based on slug
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
              <a href="{{ $prod->getUrl() }}" class="product-item-card">
                <div class="product-img-holder">
                  <x-image :src="$prodImg" alt="{{ $prod->getTranslation('title') }}" class="w-full h-full object-cover" sizes="100vw" />
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

          <!-- Pagination -->
          @if($products->hasPages())
            <div class="pagination-wrapper" style="margin-top: 40px; display: flex; justify-content: center; gap: 8px;">
              {{ $products->links() }}
            </div>
          @endif
        @endif
      </section>
    </div>
  </div>
</main>
@endsection
