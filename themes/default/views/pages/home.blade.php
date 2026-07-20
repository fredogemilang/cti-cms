@extends($activeTheme->slug . '::layouts.app')

@section('title', $page ? $page->title . ' — ' . setting('site_name', config('app.name')) : setting('site_name', config('app.name')))

@section('content')
    {{-- Hero Section --}}
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">
                    {{ $page?->block('hero_title') ?? setting('site_name', 'Welcome') }}
                </h1>
                <p class="hero-subtitle">
                    {{ $page?->block('hero_subtitle') ?? setting('site_tagline', 'Build something amazing with your new CMS.') }}
                </p>
                <div class="hero-actions">
                    <a href="#content" class="btn btn-primary">Explore</a>
                </div>
            </div>
            @if($heroImage = $page?->block('hero_image'))
                <div class="hero-image">
                    <img src="{{ asset('storage/' . $heroImage) }}" alt="Hero" loading="lazy">
                </div>
            @endif
        </div>
    </section>

    {{-- Page Blocks --}}
    <section class="section" id="content">
        <div class="container">
            @if($page && $page->blocks->count())
                @foreach($page->blocks as $block)
                    @if($block->is_active)
                        @include($activeTheme->slug . '::partials.block', ['block' => $block])
                    @endif
                @endforeach
            @else
                <div class="empty-state">
                    <h2>Your site is ready!</h2>
                    <p>Go to the admin panel to create your first page and customize this homepage.</p>
                    <a href="{{ url(config('cms.path', 'ctrlpanel')) }}" class="btn btn-outline">Open Admin Panel</a>
                </div>
            @endif
        </div>
    </section>
@endsection
