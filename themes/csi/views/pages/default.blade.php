@extends('csi::layouts.app')

@section('content')
<section class="py-16 sm:py-24 bg-white">
  <div class="max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px]">
    <x-seo-breadcrumbs :entity="$page" class="text-slate-500 mb-6" />
    <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight mb-6">
      {{ $page->getTranslation('title') ?? $page->title }}
    </h1>
    @foreach($page->blocks as $block)
      <div class="prose max-w-none text-slate-700 mb-6">
        @if(is_string($block->localizedValue()))
          {!! $block->localizedValue() !!}
        @endif
      </div>
    @endforeach
  </div>
</section>
@endsection
