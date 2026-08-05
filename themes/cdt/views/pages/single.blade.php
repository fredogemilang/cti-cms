@extends('cdt::layouts.app')

@section('title', isset($page) && $page->title ? $page->getMetaTitle() : setting('site_name', 'Central Data Technology'))

@section('content')
  @if(isset($page) && $page->featured_image)
    <!-- Page Hero Section with Featured Image -->
    <section class="relative h-[300px] md:h-[400px] flex items-center pt-20 overflow-hidden bg-zinc-900 text-white">
      <div class="absolute inset-0 z-0">
        <x-image :src="asset('storage/' . $page->featured_image)" class="w-full h-full object-cover" alt="{{ $page->title }}" />
        <div class="absolute inset-0 bg-gradient-to-r from-zinc-950/90 via-zinc-900/80 to-transparent w-full lg:w-3/4"></div>
      </div>
      
      <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10 w-full">
        <div class="max-w-3xl text-white">
          <div class="mb-6 font-semibold text-xs text-white/80 [&_a]:text-white/80 [&_a:hover]:text-white [&_span]:text-white/40 [&_.breadcrumb-current]:text-white [&_.breadcrumb-current]:font-bold">
            <x-seo-breadcrumbs :entity="$page" />
          </div>

          <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold leading-tight">
            {{ $page->title }}
          </h1>
        </div>
      </div>
    </section>
  @else
    <!-- Clean Page Header (No Featured Image) -->
    <section class="relative pt-32 pb-12 md:pt-40 md:pb-16 bg-zinc-50 border-b border-zinc-200/80">
      <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl">
          @if(isset($page))
            <div class="mb-6 font-semibold text-xs text-zinc-500 [&_a]:text-zinc-600 [&_a:hover]:text-primary [&_span]:text-zinc-400 [&_.breadcrumb-current]:text-zinc-900 [&_.breadcrumb-current]:font-bold">
              <x-seo-breadcrumbs :entity="$page" />
            </div>
          @endif

          <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-zinc-900 tracking-tight leading-tight">
            {{ $page->title ?? 'Page' }}
          </h1>
          <div class="h-1 bg-primary w-20 mt-6 rounded-full"></div>
        </div>
      </div>
    </section>
  @endif

  <!-- Page Content / Blocks Section -->
  <section class="py-12 md:py-20 bg-white relative">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      @if(isset($page) && $page->blocks->count())
        <div class="space-y-6">
          @foreach($page->blocks as $block)
            @if($block->is_active)
              @include('cdt::partials.block', ['block' => $block])
            @endif
          @endforeach
        </div>
      @elseif(isset($page) && $page->content)
        <div class="prose max-w-none text-zinc-800 text-base md:text-lg leading-relaxed [&_a]:text-red-600 hover:[&_a]:text-red-700 [&_a]:underline [&_a]:font-medium transition-colors">
          {!! $page->content !!}
        </div>
      @endif
    </div>
  </section>

  @include('cdt::partials.contact-section')
@endsection
