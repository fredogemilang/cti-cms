@php
    $layoutView = view()->exists("{$activeTheme->slug}::layouts.app")
        ? "{$activeTheme->slug}::layouts.app"
        : (view()->exists('default::layouts.app') ? 'default::layouts.app' : 'layouts.app');
@endphp
@extends($layoutView)

@section('title', $page ? $page->title . ' — ' . setting('site_name', config('app.name')) : setting('site_name', config('app.name')))

@section('content')
    {{-- Hero Section --}}
    <section class="hero">
        <div class="hero-bg-glow"></div>
        <div class="container hero-container">
            <div class="hero-content">
                <div class="hero-badge">
                    <span class="hero-badge-dot"></span>
                    <span class="hero-badge-text">Web CMS Platform</span>
                </div>
                <h1 class="hero-title">
                    {{ $page?->block('hero_title') ?? setting('site_name', 'Welcome to Centraldatatech') }}
                </h1>
                <p class="hero-subtitle">
                    {{ $page?->block('hero_subtitle') ?? setting('site_tagline', 'Build something amazing with your new CMS. Create pages, manage content, and scale effortlessly.') }}
                </p>
                <div class="hero-actions">
                    <a href="#content" class="btn btn-primary">
                        <span>Explore Content</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                    <a href="{{ url(config('cms.path', 'ctrlpanel')) }}" class="btn btn-outline">
                        <span>Admin Panel</span>
                    </a>
                </div>
            </div>
            @if($heroImage = $page?->block('hero_image'))
                <div class="hero-image">
                    <img src="{{ asset('storage/' . $heroImage) }}" alt="Hero" loading="lazy">
                </div>
            @endif
        </div>
    </section>

    {{-- Main Section / Page Blocks --}}
    <section class="section" id="content">
        <div class="container">
            @if($page && $page->blocks->count())
                @php
                    $blockView = view()->exists("{$activeTheme->slug}::partials.block")
                        ? "{$activeTheme->slug}::partials.block"
                        : (view()->exists('default::partials.block') ? 'default::partials.block' : 'partials.block');
                @endphp
                @foreach($page->blocks as $block)
                    @if($block->is_active)
                        @include($blockView, ['block' => $block])
                    @endif
                @endforeach
            @else
                <div class="empty-state-card">
                    <div class="empty-state-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                    </div>
                    <div class="empty-state-body">
                        <h2>Your site is ready!</h2>
                        <p>Go to the admin panel to create your first page and customize this homepage.</p>
                        <div class="empty-state-actions">
                            <a href="{{ url(config('cms.path', 'ctrlpanel')) }}" class="btn btn-primary">
                                <span>Open Admin Panel</span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14L21 3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
