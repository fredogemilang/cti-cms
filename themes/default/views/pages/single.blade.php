@extends($activeTheme->slug . '::layouts.app')

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
            @foreach($blocks as $block)
                @if($block->is_active)
                    @include($activeTheme->slug . '::partials.block', ['block' => $block])
                @endif
            @endforeach
        </div>
    </section>
@endsection
