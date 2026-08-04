@extends('cdt::layouts.app')

@section('title', (isset($postType) ? ($postType->getTranslation('plural_label', app()->getLocale()) ?: $postType->plural_label ?: $postType->name) : t('cs.customer_success', 'Customer Success')) . ' — ' . setting('site_name', 'Central Data Technology'))

@section('content')
<!-- Customer Success Hero Section -->
<section class="relative h-[400px] md:h-[500px] flex items-center pt-20 overflow-hidden bg-gray-900 text-white">
  <!-- Immersive background -->
  <div class="absolute inset-0 z-0">
    <x-image :src="asset('themes/cdt/assets/about-us-bg-DOuRQvF3.webp')" class="w-full h-full object-cover opacity-60" alt="Customer Success Background" />
    <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/90 to-transparent w-full lg:w-3/4"></div>
  </div>

  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10 w-full">
    <div class="max-w-3xl text-white">
      <!-- Breadcrumb -->
      <div class="mb-8 font-semibold text-xs text-white/70 [&_a]:text-white/70 [&_a:hover]:text-white [&_span]:text-white/40 [&_.breadcrumb-current]:text-white [&_.breadcrumb-current]:font-bold">
        <x-seo-breadcrumbs :entity="$postType ?? null" />
      </div>

      <div class="overflow-hidden mb-2">
        <p class="text-lg md:text-xl font-light text-white/90" data-gsap="fade-up">{{ t('cs.success_stories', 'Success Stories') }}</p>
      </div>
      <div class="overflow-hidden mb-6">
        <h1 class="text-4xl md:text-5xl lg:text-[54px] font-bold leading-tight" data-gsap="fade-up" data-gsap-delay="0.1">
          {{ t('cs.customer_success', 'Customer Success') }}
        </h1>
      </div>
    </div>
  </div>
</section>

<!-- Archive Grid Section -->
<section class="py-24 bg-zinc-50 relative">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
    
    <!-- Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      @forelse($entries as $entry)
        @php
          $locale = app()->getLocale();
          $title = $entry->getTranslation('title', $locale) ?? $entry->title;
          $excerpt = $entry->getTranslation('excerpt', $locale) ?? $entry->excerpt ?? strip_tags($entry->getTranslation('content', $locale) ?? $entry->content ?? '');
          $logoUrl = $entry->featured_image ? (str_starts_with($entry->featured_image, 'http') || str_starts_with($entry->featured_image, 'themes/') ? asset($entry->featured_image) : asset('storage/' . $entry->featured_image)) : asset('themes/cdt/assets/cropped-logo-cdt-D0j3NVKg.png');
        @endphp
        <!-- Card -->
        <div class="group flex flex-col bg-white rounded-3xl border border-zinc-200 overflow-hidden shadow-sm hover:shadow-2xl hover:border-primary/50 transition-all duration-300 transform hover:-translate-y-2 relative" data-gsap="fade-up">
          <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
          <div class="p-8 md:p-10 flex-grow flex flex-col relative z-10">
            <div class="h-16 flex items-center justify-start mb-6">
              <x-image :src="$logoUrl" alt="{{ $title }}" class="max-h-full max-w-[160px] object-contain" />
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-4 line-clamp-2">{{ $title }}</h2>
            <p class="text-gray-600 font-light leading-relaxed mb-10 flex-grow line-clamp-4">
              {{ Str::limit(strip_tags($excerpt), 180) }}
            </p>
            <a href="{{ $entry->getUrl($locale) }}" title="{{ $title }}" class="inline-flex items-center text-primary font-bold text-sm tracking-widest uppercase hover:text-red-700 transition-colors mt-auto w-max">
              {{ t('cs.read_more', 'READ MORE') }} <span class="ml-2 group-hover:translate-x-1 transition-transform">→</span>
            </a>
          </div>
        </div>
      @empty
        <div class="col-span-full py-12 text-center text-gray-500 font-light">
          {{ t('cs.no_entries', 'No customer success stories found.') }}
        </div>
      @endforelse
    </div>

    <!-- Pagination -->
    @if($entries->hasPages())
      <div class="mt-16 flex justify-center">
        {{ $entries->links('cdt::partials.pagination') }}
      </div>
    @endif

  </div>
</section>

@include('cdt::partials.contact-section')
@endsection
