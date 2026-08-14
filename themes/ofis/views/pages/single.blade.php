@extends($activeTheme->slug . '::layouts.app')

@section('title', $page->title ?? 'Page')

@section('content')
<article class="page-content">
    <div class="container">
        <h1>{{ $page->title }}</h1>

        @if($page->blocks->count())
            @foreach($page->blocks as $block)
                <div class="block block--{{ $block->type }}">
                    {!! $block->rendered_content !!}
                </div>
            @endforeach
        @endif
    </div>
</article>
@endsection