@extends($activeTheme->slug . '::layouts.app')

@section('title', $entry->title . ' — ' . setting('site_name', config('app.name')))

@section('content')
    {{-- Entry Header --}}
    <section class="page-header">
        <div class="container">
            {{-- Breadcrumb --}}
            <nav class="breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <span class="breadcrumb-separator">/</span>
                <a href="{{ $postType->getArchiveUrl() }}">{{ $postType->plural_label }}</a>
                <span class="breadcrumb-separator">/</span>
                <span>{{ $entry->title }}</span>
            </nav>

            @if($entry->featured_image)
                <img src="{{ asset('storage/' . $entry->featured_image) }}"
                     alt="{{ $entry->title }}"
                     class="page-hero-image"
                     loading="lazy">
            @endif

            <h1 class="page-title">{{ $entry->title }}</h1>

            <div class="entry-meta">
                @if($entry->author)
                    <span class="entry-author">By {{ $entry->author->name }}</span>
                @endif
                @if($entry->published_at)
                    <span class="meta-separator">&middot;</span>
                    <time datetime="{{ $entry->published_at->toDateString() }}">
                        {{ $entry->published_at->format('F d, Y') }}
                    </time>
                @endif
            </div>

            {{-- Terms --}}
            @if($entry->terms->count())
                <div class="entry-terms">
                    @foreach($entry->terms as $term)
                        <a href="{{ $term->getUrl() }}" class="term-badge">{{ $term->name }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Entry Content --}}
    <section class="section page-body">
        <div class="container container-narrow">
            @if($entry->content)
                <div class="entry-content prose">
                    {!! $entry->content !!}
                </div>
            @endif

            {{-- Meta Fields --}}
            @if($entry->meta && count($entry->meta) > 0)
                <div class="entry-meta-fields">
                    @foreach($entry->meta as $key => $value)
                        @if(!empty($value))
                            <div class="meta-field">
                                <dt>{{ Str::title(str_replace('_', ' ', $key)) }}</dt>
                                <dd>{{ is_array($value) ? implode(', ', $value) : $value }}</dd>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Previous / Next Navigation --}}
    @if($previousEntry || $nextEntry)
        <section class="section entry-navigation">
            <div class="container container-narrow">
                <div class="entry-nav-grid">
                    @if($previousEntry)
                        <a href="{{ $previousEntry->getUrl() }}" class="entry-nav-link entry-nav-prev">
                            <span class="entry-nav-label">&larr; Previous</span>
                            <span class="entry-nav-title">{{ Str::limit($previousEntry->title, 60) }}</span>
                        </a>
                    @else
                        <div></div>
                    @endif
                    @if($nextEntry)
                        <a href="{{ $nextEntry->getUrl() }}" class="entry-nav-link entry-nav-next">
                            <span class="entry-nav-label">Next &rarr;</span>
                            <span class="entry-nav-title">{{ Str::limit($nextEntry->title, 60) }}</span>
                        </a>
                    @endif
                </div>
            </div>
        </section>
    @endif
@endsection
