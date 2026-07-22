@extends($activeTheme->slug . '::layouts.app')

@section('title', $postType->plural_label . ' — ' . setting('site_name', config('app.name')))

@section('content')
    {{-- Archive Header --}}
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">{{ $postType->plural_label }}</h1>
            @if($postType->description)
                <p class="archive-description">{{ $postType->description }}</p>
            @endif
        </div>
    </section>

    {{-- Archive Content --}}
    <section class="section">
        <div class="container">
            <div class="archive-layout {{ $taxonomies->count() > 0 ? 'archive-with-sidebar' : '' }}">
                {{-- Main Content --}}
                <div class="archive-main">
                    @if($entries->count())
                        <div class="archive-grid">
                            @foreach($entries as $entry)
                                <article class="entry-card">
                                    @if($entry->featured_image)
                                        <a href="{{ $entry->getUrl() }}" class="entry-card-image">
                                            <img src="{{ asset('storage/' . $entry->featured_image) }}"
                                                 alt="{{ $entry->title }}"
                                                 loading="lazy">
                                        </a>
                                    @endif
                                    <div class="entry-card-body">
                                        <div class="entry-card-meta">
                                            @if($entry->published_at)
                                                <time datetime="{{ $entry->published_at->toDateString() }}">
                                                    {{ $entry->published_at->format('M d, Y') }}
                                                </time>
                                            @endif
                                            @if($entry->author)
                                                <span class="meta-separator">&middot;</span>
                                                <span>{{ $entry->author->name }}</span>
                                            @endif
                                        </div>
                                        <h2 class="entry-card-title">
                                            <a href="{{ $entry->getUrl() }}">{{ $entry->title }}</a>
                                        </h2>
                                        @if($entry->excerpt)
                                            <p class="entry-card-excerpt">{{ Str::limit(strip_tags($entry->excerpt), 150) }}</p>
                                        @endif
                                        <div class="entry-card-terms">
                                            @foreach($entry->terms->take(3) as $term)
                                                <a href="{{ $term->getUrl() }}" class="term-badge">{{ $term->name }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="archive-pagination">
                            {{ $entries->links() }}
                        </div>
                    @else
                        <div class="archive-empty">
                            <p>No {{ strtolower($postType->plural_label) }} found.</p>
                        </div>
                    @endif
                </div>

                {{-- Sidebar: Taxonomy Filters --}}
                @if($taxonomies->count() > 0)
                    <aside class="archive-sidebar">
                        @foreach($taxonomies as $taxonomy)
                            <div class="sidebar-widget">
                                <h3 class="sidebar-widget-title">{{ $taxonomy->plural_label ?? $taxonomy->name }}</h3>
                                <ul class="sidebar-term-list">
                                    @foreach($taxonomy->terms()->get() as $term)
                                        <li>
                                            <a href="{{ $term->getUrl() }}">
                                                {{ $term->name }}
                                                <span class="term-count">({{ $term->entries()->where('status', 'published')->count() }})</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </aside>
                @endif
            </div>
        </div>
    </section>
@endsection
