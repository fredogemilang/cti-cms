@extends('xyora::layouts.app')

@section('title', $post->title)

@push('meta')
  <meta name="description" content="{{ $post->excerpt ?? Str::limit(strip_tags($post->content), 150) }}">
@endpush

@section('content')
<main class="artikel-detail-main" style="background: #ffffff; padding: 80px 0; font-family: var(--font-body); color: var(--text-dark); min-height: 60vh;">
  <div class="artikel-container" style="max-width: 900px; margin: 0 auto; padding: 0 1.5rem;">
    
    {{-- Breadcrumbs Component --}}
    <x-seo-breadcrumbs :entity="$post" class="text-gray-500 mb-6 font-medium text-sm" />

    {{-- Header Info --}}
    <header class="artikel-detail-header" style="margin-bottom: 2rem; margin-top: 1rem;">
      <h1 class="artikel-detail-title" style="font-family: var(--font-heading); font-size: 2.5rem; font-weight: 800; color: var(--primary-navy); line-height: 1.25; margin-bottom: 1.25rem;">
        {{ $post->getTranslation('title') }}
      </h1>
      
      <div class="artikel-meta" style="display: flex; align-items: center; gap: 1rem; color: var(--text-light); font-size: 0.9rem;">
        <span class="meta-item" style="display: flex; align-items: center; gap: 0.4rem;">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14" style="color: var(--accent-green);">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
          </svg>
          {{ $post->published_at ? $post->published_at->translatedFormat('d F Y') : '' }}
        </span>
      </div>
    </header>

    {{-- Featured Image --}}
    @if($post->featured_image)
      <div class="artikel-detail-image-wrapper" style="width: 100%; border-radius: 16px; overflow: hidden; margin-bottom: 2.5rem; box-shadow: 0 10px 30px -10px rgba(15, 44, 89, 0.1);">
        <img src="{{ resolve_block_asset($post->featured_image) }}" alt="{{ $post->title }}" style="width: 100%; height: auto; max-height: 500px; object-fit: cover; display: block;" />
      </div>
    @endif

    {{-- Content Body --}}
    <article class="artikel-detail-content" style="line-height: 1.8; font-size: 1.05rem; color: #334155;">
      {!! $post->getTranslation('content') !!}
    </article>

    {{-- Back Button --}}
    <div class="artikel-back-action" style="margin-top: 3.5rem; border-top: 1px solid var(--gray-border); padding-top: 2rem; display: flex; justify-content: flex-start;">
      <a href="{{ url('/blog') }}" class="btn-back-to-blog" style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--accent-green-dark); font-family: var(--font-heading); font-weight: 700; text-decoration: none; transition: color 0.2s ease;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
          <line x1="19" y1="12" x2="5" y2="12"></line>
          <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        {{ t('blog.back_to_blog', 'Kembali ke Artikel') }}
      </a>
    </div>

  </div>
</main>

<style>
  .artikel-detail-content p {
    margin-bottom: 1.5rem;
  }
  .artikel-detail-content h2, .artikel-detail-content h3 {
    font-family: var(--font-heading);
    color: var(--primary-navy);
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-weight: 700;
  }
  .artikel-detail-content h2 {
    font-size: 1.75rem;
  }
  .artikel-detail-content h3 {
    font-size: 1.4rem;
  }
  .artikel-detail-content ul, .artikel-detail-content ol {
    margin-bottom: 1.5rem;
    padding-left: 1.5rem;
  }
  .artikel-detail-content ul {
    list-style-type: disc;
  }
  .artikel-detail-content ol {
    list-style-type: decimal;
  }
  .artikel-detail-content li {
    margin-bottom: 0.5rem;
  }
  .btn-back-to-blog:hover {
    color: var(--primary-navy) !important;
  }
</style>
@endsection
