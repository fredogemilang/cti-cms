@extends('layouts.app', ['title' => ($postType->plural_label ?? $taxonomy->name ?? 'Archive') . ' — ' . setting('site_name', config('app.name'))])

@section('content')
<div style="max-width: 960px; margin: 0 auto; padding: 2rem 1rem;">
    <h1>{{ $postType->plural_label ?? $taxonomy->name ?? 'Archive' }}</h1>

    @isset($term)
        <p>Filtered by: <strong>{{ $term->name }}</strong></p>
    @endisset

    @isset($postType)
        @if($postType->description)
            <p>{{ $postType->description }}</p>
        @endif
    @endisset

    @if($entries->count())
        <div style="display: grid; gap: 1.5rem; margin-top: 2rem;">
            @foreach($entries as $entry)
                <article style="border-bottom: 1px solid #eee; padding-bottom: 1.5rem;">
                    <h2 style="margin: 0;">
                        <a href="{{ $entry->getUrl() }}" style="text-decoration: none; color: #111;">
                            {{ $entry->title }}
                        </a>
                    </h2>
                    <div style="color: #888; font-size: 0.875rem; margin-top: 0.25rem;">
                        @if($entry->published_at)
                            <time datetime="{{ $entry->published_at->toDateString() }}">
                                {{ $entry->published_at->format('M d, Y') }}
                            </time>
                        @endif
                    </div>
                    @if($entry->excerpt)
                        <p style="color: #555; margin-top: 0.5rem;">{{ Str::limit(strip_tags($entry->excerpt), 200) }}</p>
                    @endif
                </article>
            @endforeach
        </div>

        <div style="margin-top: 2rem;">
            {{ $entries->links() }}
        </div>
    @else
        <p style="color: #888; margin-top: 2rem;">No entries found.</p>
    @endif
</div>
@endsection
