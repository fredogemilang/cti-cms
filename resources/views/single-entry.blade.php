@extends('layouts.app', ['title' => $entry->title . ' — ' . setting('site_name', config('app.name'))])

@section('content')
<div style="max-width: 720px; margin: 0 auto; padding: 2rem 1rem;">
    <nav style="font-size: 0.875rem; color: #888; margin-bottom: 1.5rem;">
        <a href="{{ url('/') }}" style="color: #888;">Home</a> /
        <a href="{{ $postType->getArchiveUrl() }}" style="color: #888;">{{ $postType->plural_label }}</a> /
        <span>{{ $entry->title }}</span>
    </nav>

    @if($entry->featured_image)
        <img src="{{ asset('storage/' . $entry->featured_image) }}"
             alt="{{ $entry->title }}"
             style="width: 100%; border-radius: 8px; margin-bottom: 1.5rem;">
    @endif

    <h1 style="margin: 0;">{{ $entry->title }}</h1>

    <div style="color: #888; font-size: 0.875rem; margin-top: 0.5rem;">
        @if($entry->author)
            By {{ $entry->author->name }}
        @endif
        @if($entry->published_at)
            &middot;
            <time datetime="{{ $entry->published_at->toDateString() }}">
                {{ $entry->published_at->format('F d, Y') }}
            </time>
        @endif
    </div>

    @if($entry->terms->count())
        <div style="margin-top: 0.75rem;">
            @foreach($entry->terms as $term)
                <a href="{{ $term->getUrl() }}"
                   style="display: inline-block; background: #f0f0f0; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; text-decoration: none; color: #555; margin-right: 0.25rem;">
                    {{ $term->name }}
                </a>
            @endforeach
        </div>
    @endif

    <hr style="margin: 2rem 0; border: none; border-top: 1px solid #eee;">

    @if($entry->content)
        <div style="line-height: 1.8;">
            {!! $entry->content !!}
        </div>
    @endif

    @if($previousEntry || $nextEntry)
        <hr style="margin: 2rem 0; border: none; border-top: 1px solid #eee;">
        <div style="display: flex; justify-content: space-between;">
            @if($previousEntry)
                <a href="{{ $previousEntry->getUrl() }}" style="color: #333;">&larr; {{ Str::limit($previousEntry->title, 40) }}</a>
            @else
                <span></span>
            @endif
            @if($nextEntry)
                <a href="{{ $nextEntry->getUrl() }}" style="color: #333;">{{ Str::limit($nextEntry->title, 40) }} &rarr;</a>
            @endif
        </div>
    @endif
</div>
@endsection
