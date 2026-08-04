@extends($activeTheme->slug . '::layouts.app')

@section('title', $page->title ?? 'Home')

@section('content')
<section class="hero">
    <div class="container">
        <h1>{{ $page?->block('hero_title') ?? 'Welcome' }}</h1>
        <p>{{ $page?->block('hero_subtitle') ?? 'Your website tagline goes here.' }}</p>
    </div>
</section>

<section class="content">
    <div class="container">
        @if($page && $page->blocks->count())
            @foreach($page->blocks as $block)
                <div class="block block--{{ $block->type }}">
                    {!! $block->rendered_content !!}
                </div>
            @endforeach
        @endif
    </div>
</section>
@endsection