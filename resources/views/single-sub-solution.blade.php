@extends('cdt::layouts.app', ['title' => ($entry->getTranslation('title', app()->getLocale()) ?? $entry->title) . ' — ' . setting('site_name', config('app.name', 'Central Data Technology'))])

@section('content')

@php
    $locale = app()->getLocale();
    $title = $entry->getTranslation('title', $locale) ?? $entry->title;
    $content = $entry->getTranslation('content', $locale) ?? $entry->content;
    $excerpt = $entry->getTranslation('excerpt', $locale) ?? $entry->excerpt;
    
    $metaDesc = trim($entry->getMeta('banner_description') ?? '');
    $excerptText = trim($excerpt ?? '');
    $contentText = trim(strip_tags($content ?? ''));

    $bannerDesc = !empty($metaDesc) ? $metaDesc : (!empty($excerptText) ? $excerptText : $contentText);
    $keyFeatures = $entry->getMeta('key_features_list') ?? [];
    $parent = $entry->parent;
    $parentTitle = $parent ? ($parent->getTranslation('title', $locale) ?? $parent->title) : t('Solutions');

    $heroImage = $entry->featured_image 
        ? asset('storage/' . $entry->featured_image) 
        : ($entry->getMeta('loop_image') 
            ? asset('storage/' . $entry->getMeta('loop_image')) 
            : '/assets/images/unsplash/photo-1551288049-bebda4e38f71-w2070.jpg');

    // Related products brand mapping dictionary
    $brandLogos = [
        'aws' => ['name' => 'AWS', 'logo' => asset('storage/media/logo-awspng-1785241172-yPnfNkus.webp'), 'url' => localized_url('/technology-alliance/aws')],
        'netgain-systems' => ['name' => 'NetGain Systems', 'logo' => asset('storage/media/onboarding_new_product_netgain_systems_0.png'), 'url' => localized_url('/technology-alliance/netgain-systems')],
        'zscaler' => ['name' => 'Zscaler', 'logo' => asset('storage/media/Zscaler-logo-1.svg'), 'url' => localized_url('/technology-alliance/zscaler')],
        'akamai' => ['name' => 'Akamai', 'logo' => asset('storage/media/new-akamai-logo-2025-e1767670370250-1785240199-VS6Sj1Im.png'), 'url' => localized_url('/technology-alliance/akamai')],
        'hitachi-vantara' => ['name' => 'Hitachi Vantara', 'logo' => asset('storage/media/hv-logo-rgb-web-black-scaled.png'), 'url' => localized_url('/technology-alliance/hitachi-vantara')],
        'f5' => ['name' => 'F5', 'logo' => asset('storage/media/logo-F5.png.webp'), 'url' => localized_url('/technology-alliance/f5')],
        'okta' => ['name' => 'Okta', 'logo' => asset('storage/media/Okta_Wordmark_Black_L-1024x537.webp'), 'url' => localized_url('/technology-alliance/okta')],
        'dynatrace' => ['name' => 'Dynatrace', 'logo' => asset('storage/media/Dynatrace_Logo_color_positive_vertical-1024x1024.png'), 'url' => localized_url('/technology-alliance/dynatrace')],
    ];

    // 1. Fetch dynamic related entries from CPT Entry Pivot Relationships
    $relatedProducts = [];
    $relEntries = $entry->relatedEntries()->where('status', 'published')->get();

    if ($relEntries->isNotEmpty()) {
        foreach ($relEntries as $rel) {
            $relTitle = $rel->getTranslation('title', $locale, fallback: true) ?? $rel->title;
            $relLogo = $rel->featured_image 
                ? asset('storage/' . $rel->featured_image) 
                : ($rel->getMeta('logo') ? asset('storage/' . $rel->getMeta('logo')) : null);
            $relUrl = $rel->getUrl($locale);

            $relatedProducts[] = [
                'name' => $relTitle,
                'logo' => $relLogo,
                'url' => $relUrl,
            ];
        }
    }

    // 2. If no pivot entries, check MetaFields ('related_products', 'technology_alliances', 'related_brands', 'products')
    if (empty($relatedProducts)) {
        $metaVal = $entry->getMeta('related_products') 
            ?? $entry->getMeta('technology_alliances') 
            ?? $entry->getMeta('related_brands') 
            ?? $entry->getMeta('products');

        if (!empty($metaVal)) {
            $keys = is_array($metaVal) ? $metaVal : array_filter(array_map('trim', explode(',', (string) $metaVal)));
            
            // Try fetching by CPT Entry IDs or slugs
            $fetchedEntries = \App\Models\CptEntry::whereIn('id', $keys)
                ->orWhereIn('slug', $keys)
                ->where('status', 'published')
                ->get();

            if ($fetchedEntries->isNotEmpty()) {
                foreach ($fetchedEntries as $rel) {
                    $relTitle = $rel->getTranslation('title', $locale, fallback: true) ?? $rel->title;
                    $relLogo = $rel->featured_image 
                        ? asset('storage/' . $rel->featured_image) 
                        : ($rel->getMeta('logo') ? asset('storage/' . $rel->getMeta('logo')) : null);
                    $relUrl = $rel->getUrl($locale);

                    $relatedProducts[] = [
                        'name' => $relTitle,
                        'logo' => $relLogo,
                        'url' => $relUrl,
                    ];
                }
            } else {
                // Check if keys match known brand keys in $brandLogos dictionary
                foreach ($keys as $key) {
                    if (isset($brandLogos[$key])) {
                        $relatedProducts[] = $brandLogos[$key];
                    }
                }
            }
        }
    }
@endphp

<!-- Hero V2: Full width dark immersive -->
<section class="relative h-[400px] md:h-[500px] flex items-center pt-20 overflow-hidden bg-gray-900 text-white">
  <!-- Immersive background -->
  <div class="absolute inset-0 z-0">
    <x-image :src="$heroImage" class="w-full h-full object-cover object-left opacity-30" alt="{{ $title }}" />
    <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-gray-900/90 to-transparent w-full lg:w-3/4"></div>
  </div>

  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10 w-full">
    <div class="max-w-3xl text-white">
      <!-- SEO CMS Breadcrumbs -->
      <div class="mb-8 font-semibold text-xs text-white/70 [&_a]:text-white/70 [&_a:hover]:text-white [&_span]:text-white/40 [&_.breadcrumb-current]:text-white [&_.breadcrumb-current]:font-bold">
          <x-seo-breadcrumbs :entity="$entry" />
      </div>

      <div class="overflow-hidden mb-6">
        <h1 class="text-4xl md:text-5xl lg:text-[54px] font-bold leading-tight">
          {{ $title }}
        </h1>
      </div>
      @if(!empty($bannerDesc))
      <div class="overflow-hidden mb-8">
        <p class="text-lg md:text-xl text-gray-300 font-light max-w-2xl">
          {{ strip_tags($bannerDesc) }}
        </p>
      </div>
      @endif

    </div>
  </div>
</section>

<!-- About Section -->
<section class="py-24 bg-white relative">
  <div class="mx-auto max-w-[1000px] px-4 sm:px-6 lg:px-8 text-center">
    <div class="mb-8 lg:mb-12">
      <h2 class="text-3xl md:text-5xl font-light mb-6 text-zinc-500">{{ t('about_prefix', 'About') }} <span class="font-bold text-gray-900">{{ $title }}</span></h2>
      <div class="w-24 h-1 bg-primary mx-auto"></div>
    </div>
    
    @if(!empty($content))
      <div class="text-gray-600 text-lg md:text-xl leading-relaxed font-light mb-10 prose max-w-none text-center">
        {!! $content !!}
      </div>
    @else
      <p class="text-gray-600 text-lg md:text-xl leading-relaxed font-light mb-10">
        {{ strip_tags($bannerDesc) }}
      </p>
    @endif

    <a href="#contact"
      class="inline-flex items-center justify-center bg-primary hover:bg-red-700 text-white px-8 py-4 font-bold uppercase tracking-wide transition-colors rounded-full shadow-lg hover:shadow-xl group">
      {{ t('btn.free_consultation', 'Free Consultation') }}
      <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
      </svg>
    </a>

    @if(!empty($relatedProducts))
    <!-- Integrated Related Product Card -->
    <div class="mt-16 bg-zinc-50 rounded-3xl p-8 md:p-12 border border-zinc-100 shadow-sm max-w-4xl mx-auto">
      <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-8 text-center">{{ t('related_products', 'Related Product') }}</h3>
      <div class="flex flex-wrap justify-center items-center gap-6 md:gap-8">
        @foreach($relatedProducts as $product)
          <a href="{{ $product['url'] }}" class="bg-white px-8 py-6 rounded-2xl shadow-sm border border-zinc-100 w-48 h-28 flex items-center justify-center hover:-translate-y-1 hover:shadow-md transition-all duration-300 group">
            @if(!empty($product['logo']))
              <x-image :src="$product['logo']" logo'] }}" alt="{{ $product['name'] }}" class="max-h-12 w-auto object-contain transition-transform group-hover:scale-105" />
            @else
              <span class="font-bold text-gray-800 text-sm group-hover:text-primary transition-colors">{{ $product['name'] }}</span>
            @endif
          </a>
        @endforeach
      </div>
    </div>
    @endif
  </div>
</section>

<!-- Key Features Section -->
@if(!empty($keyFeatures) && is_array($keyFeatures) && count($keyFeatures) > 0)
<section class="relative py-24 overflow-hidden bg-zinc-50 text-gray-900">
  <!-- Subtle Light Background Pattern -->
  <div class="absolute inset-0 z-0 pointer-events-none">
    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-primary/5 rounded-full blur-[100px] -translate-y-1/4 translate-x-1/4"></div>
    <div class="absolute bottom-0 left-1/4 w-[800px] h-[800px] bg-blue-500/5 rounded-full blur-[120px] translate-y-1/4"></div>
  </div>

  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="text-center mb-16 lg:mb-24">
      <h2 class="text-3xl md:text-5xl font-light mb-6 text-zinc-500">{{ t('features.prefix', 'Key') }} <span class="font-bold text-gray-900">{{ t('features.suffix', 'Features') }}</span></h2>
      <div class="w-24 h-1 bg-primary mx-auto"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
      @foreach($keyFeatures as $feature)
        @php
            $kfIcon = $feature['kf_icon'] ?? 'lucide:check-circle';
            $kfTitle = $feature['kf_title'] ?? '';
            $kfDesc = $feature['kf_description'] ?? '';
        @endphp
        <div class="group relative rounded-3xl bg-white border border-zinc-100 p-8 hover:border-primary/20 transition-all duration-300 transform hover:-translate-y-2 overflow-hidden shadow-sm hover:shadow-xl">
          <div class="w-16 h-16 rounded-2xl bg-red-50 flex items-center justify-center text-primary mb-8 group-hover:scale-110 group-hover:bg-primary group-hover:text-white transition-all duration-300">
            @if(function_exists('render_icon'))
                {!! render_icon($kfIcon, 'w-8 h-8') !!}
            @else
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            @endif
          </div>
          <h4 class="text-2xl font-bold mb-4 text-gray-900">{{ $kfTitle }}</h4>
          <p class="text-gray-600 leading-relaxed font-light">{{ $kfDesc }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- Why with CDT V2: Light Mode with Blurred Background -->
<section class="py-24 bg-zinc-50 relative overflow-hidden">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">

    <div class="text-center mb-16 lg:mb-24">
      <h2 class="text-3xl md:text-5xl font-light mb-6 text-zinc-500">{{ t('why.prefix', 'Why') }} <span class="font-bold text-gray-900">{{ t('why.suffix', 'with CDT?') }}</span></h2>
      <div class="w-24 h-1 bg-primary mx-auto"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
      
      <!-- Feature 1 -->
      <div class="group relative bg-white/90 backdrop-blur-md rounded-3xl p-10 border border-zinc-200 shadow-xl hover:border-primary/50 hover:shadow-2xl transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-primary/10 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="w-16 h-16 rounded-2xl bg-zinc-50 border border-zinc-200 flex items-center justify-center text-primary mb-8 group-hover:scale-110 transition-transform shadow-sm">
          <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
          </svg>
        </div>
        <h4 class="text-2xl font-bold mb-4 text-gray-900">{{ t('why.card1_title', 'Free Consultation') }}</h4>
        <p class="text-gray-600 leading-relaxed font-light">{{ t('why.card1_desc', 'Explore the right security strategy without upfront cost.') }}</p>
      </div>

      <!-- Feature 2 -->
      <div class="group relative bg-white/90 backdrop-blur-md rounded-3xl p-10 border border-zinc-200 shadow-xl hover:border-primary/50 hover:shadow-2xl transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-primary/10 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="w-16 h-16 rounded-2xl bg-zinc-50 border border-zinc-200 flex items-center justify-center text-primary mb-8 group-hover:scale-110 transition-transform shadow-sm">
          <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
          </svg>
        </div>
        <h4 class="text-2xl font-bold mb-4 text-gray-900">{{ t('why.card2_title', 'Certified IT Expert') }}</h4>
        <p class="text-gray-600 leading-relaxed font-light">{{ t('why.card2_desc', 'Work with professionals backed by global certifications.') }}</p>
      </div>

      <!-- Feature 3 -->
      <div class="group relative bg-white/90 backdrop-blur-md rounded-3xl p-10 border border-zinc-200 shadow-xl hover:border-primary/50 hover:shadow-2xl transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-primary/10 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="w-16 h-16 rounded-2xl bg-zinc-50 border border-zinc-200 flex items-center justify-center text-primary mb-8 group-hover:scale-110 transition-transform shadow-sm">
          <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3a9 9 0 00-9 9v3.75c0 1.243 1.007 2.25 2.25 2.25H6a1.5 1.5 0 001.5-1.5V13.5A1.5 1.5 0 006 12H4.5A7.5 7.5 0 0112 4.5a7.5 7.5 0 017.5 7.5H18a1.5 1.5 0 00-1.5 1.5v3a1.5 1.5 0 001.5 1.5h.75a2.25 2.25 0 002.25-2.25V12a9 9 0 00-9-9z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 16.5a3 3 0 01-3 3h-2.25" />
          </svg>
        </div>
        <h4 class="text-2xl font-bold mb-4 text-gray-900">{{ t('why.card3_title', 'Local Support') }}</h4>
        <p class="text-gray-600 leading-relaxed font-light">{{ t('why.card3_desc', "Reliable assistance that's always within your reach.") }}</p>
      </div>

    </div>
  </div>
</section>

<!-- Contact Form Section -->
@include('cdt::partials.contact-section')

@endsection
