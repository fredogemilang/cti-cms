@php
    $layoutView = view()->exists("{$activeTheme->slug}::layouts.app")
        ? "{$activeTheme->slug}::layouts.app"
        : (view()->exists('default::layouts.app') ? 'default::layouts.app' : 'layouts.app');
@endphp
@extends($layoutView)

@section('title', $page->getMetaTitle())

@section('content')
    {{-- Page Header --}}
    <section class="page-header">
        <div class="container">
            @if($page->featured_image)
                <img src="{{ asset('storage/' . $page->featured_image) }}"
                     alt="{{ $page->title }}"
                     class="page-hero-image"
                     loading="lazy">
            @endif
            <h1 class="page-title">{{ $page->title }}</h1>
        </div>
    </section>

    {{-- Page Blocks --}}
    <section class="section page-body">
        <div class="container container-narrow">
            @php
                $blockView = view()->exists("{$activeTheme->slug}::partials.block")
                    ? "{$activeTheme->slug}::partials.block"
                    : (view()->exists('default::partials.block') ? 'default::partials.block' : 'partials.block');
            @endphp
            @foreach($blocks as $block)
                @if($block->is_active)
                    @include($blockView, ['block' => $block])
                @endif
            @endforeach
        </div>
    </section>
@endsection
