@extends('cdt::layouts.app')

@section('title', isset($page) && $page->title ? $page->getMetaTitle() : setting('site_name', 'Central Data Technology'))

@section('content')
  <!-- Page Hero Section -->
  <section class="relative h-[300px] md:h-[400px] flex items-center pt-20 overflow-hidden bg-gray-900 text-white">
    <div class="absolute inset-0 z-0">
      @if(isset($page) && $page->featured_image)
        <x-image :src="asset('storage/' . $page->featured_image)" class="w-full h-full object-cover" alt="{{ $page->title }}" />
      @else
        <x-image :src="asset('themes/cdt/assets/banner_hero-DHYDqbF8.jpg')" class="w-full h-full object-cover" alt="{{ $page->title ?? 'Page' }}" />
      @endif
      <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/90 to-transparent w-full lg:w-3/4"></div>
    </div>
    
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10 w-full">
      <div class="max-w-3xl text-white">
        @if(isset($page))
          <div class="mb-6 font-semibold text-xs text-white/70 [&_a]:text-white/70 [&_a:hover]:text-white [&_span]:text-white/40 [&_.breadcrumb-current]:text-white [&_.breadcrumb-current]:font-bold">
            <x-seo-breadcrumbs :entity="$page" />
          </div>
        @endif

        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold leading-tight">
          {{ $page->title ?? 'Page' }}
        </h1>
      </div>
    </div>
  </section>

  <!-- Page Content / Blocks Section -->
  <section class="py-16 md:py-24 bg-white relative">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      @if(isset($page) && $page->blocks->count())
        <div class="space-y-8">
          @foreach($page->blocks as $block)
            @if($block->is_active)
              {!! $block->render() !!}
            @endif
          @endforeach
        </div>
      @elseif(isset($page) && $page->content)
        <div class="prose max-w-none text-gray-700 text-base md:text-lg leading-relaxed">
          {!! $page->content !!}
        </div>
      @endif
    </div>
  </section>

  @include('cdt::partials.contact-section')
@endsection
