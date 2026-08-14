@extends('xyora::layouts.app')

@section('title', $page->title)

@section('content')
<main class="page-default-main py-16" style="min-height: 50vh; background: #fafafa;">
  <div class="page-container max-w-5xl mx-auto px-4 sm:px-6">
    <h1 class="page-title text-4xl font-bold mb-8" style="font-family: 'Outfit', sans-serif; color: #1a1a1a;">
      {{ $page->getTranslation('title') }}
    </h1>

    <div class="page-content prose max-w-none leading-relaxed" style="font-family: 'Inter', sans-serif; color: #4a4a4a;">
      {!! $page->getTranslation('content') !!}
    </div>
  </div>
</main>
@endsection
