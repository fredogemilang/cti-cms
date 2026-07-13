@extends('iccom::layouts.app')

@section('title', $page->getMetaTitle())

@section('content')
    {{-- Page Header --}}
    <section class="page-header-section py-5">
        <div class="container">
            @if($page->featured_image)
                <img src="{{ asset('storage/' . $page->featured_image) }}"
                    alt="{{ $page->title }}"
                    class="w-100 rounded-4 mb-4" style="max-height: 400px; object-fit: cover;">
            @endif
            <h1 class="display-4 fw-bold">{{ $page->title }}</h1>
        </div>
    </section>

    {{-- Page Blocks --}}
    @foreach($blocks as $block)
        @if($block->is_active)
            <section class="block-section py-4" data-block-name="{{ $block->name }}">
                <div class="container">
                    @switch($block->type)
                        @case('text')
                            <p class="fs-5 text-body">{{ $block->localizedValue }}</p>
                            @break

                        @case('textarea')
                            <div class="text-body">
                                {!! nl2br(e($block->localizedValue)) !!}
                            </div>
                            @break

                        @case('wysiwyg')
                            <div class="wysiwyg-content">
                                {!! $block->localizedValue !!}
                            </div>
                            @break

                        @case('number')
                            <div class="text-center py-4">
                                <span class="display-3 fw-bold text-primary">
                                    {{ $block->getOption('prefix') }}{{ $block->value }}{{ $block->getOption('suffix') }}
                                </span>
                                @if($block->label)
                                    <p class="text-muted mt-2">{{ $block->label }}</p>
                                @endif
                            </div>
                            @break

                        @case('media')
                            @if($block->value)
                                <img src="{{ asset('storage/' . $block->value) }}"
                                    alt="{{ $block->label }}"
                                    class="img-fluid rounded-4">
                            @endif
                            @break

                        @case('gallery')
                            @php $images = $block->getDecodedValue() ?? []; @endphp
                            @if(count($images) > 0)
                                <div class="row g-3">
                                    @foreach($images as $image)
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <img src="{{ asset('storage/' . $image) }}"
                                                alt="Gallery image"
                                                class="img-fluid rounded-3 w-100" style="height: 200px; object-fit: cover;">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @break

                        @case('date')
                            <p class="text-muted">
                                <i class="material-icons align-middle me-1">calendar_month</i>
                                {{ \Carbon\Carbon::parse($block->value)->format('F j, Y') }}
                            </p>
                            @break

                        @case('time')
                            <p class="text-muted">
                                <i class="material-icons align-middle me-1">schedule</i>
                                {{ $block->value }}
                            </p>
                            @break

                        @case('datetime')
                            <p class="text-muted">
                                <i class="material-icons align-middle me-1">event_repeat</i>
                                {{ \Carbon\Carbon::parse($block->value)->format('F j, Y \a\t g:i A') }}
                            </p>
                            @break

                        @case('color')
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 border shadow-sm" style="width: 48px; height: 48px; background-color: {{ $block->value }};"></div>
                                <span class="font-monospace small">{{ $block->value }}</span>
                            </div>
                            @break

                        @case('icon')
                            <span class="material-symbols-outlined display-4 text-primary">{{ $block->value }}</span>
                            @break

                        @case('switcher')
                            @if($block->getDecodedValue())
                                <span class="badge bg-success fs-6">
                                    <i class="material-icons align-middle me-1" style="font-size: 16px;">check_circle</i> Enabled
                                </span>
                            @else
                                <span class="badge bg-secondary fs-6">
                                    <i class="material-icons align-middle me-1" style="font-size: 16px;">cancel</i> Disabled
                                </span>
                            @endif
                            @break

                        @case('select')
                        @case('radio')
                            <span class="text-body">{{ $block->value }}</span>
                            @break

                        @case('checkbox')
                            @php $values = $block->getDecodedValue() ?? []; @endphp
                            <ul class="list-unstyled">
                                @foreach($values as $value)
                                    <li class="mb-2">
                                        <i class="material-icons align-middle me-2 text-success">check_box</i>
                                        {{ $value }}
                                    </li>
                                @endforeach
                            </ul>
                            @break

                        @case('repeater')
                            @php
                                $rows = $block->localizedValue();
                                if (!is_array($rows)) $rows = [];
                            @endphp
                            @if(count($rows) > 0)
                                <div class="row g-4">
                                    @foreach($rows as $row)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="card border-0 shadow-sm h-100 rounded-4">
                                                <div class="card-body">
                                                    @foreach($block->childBlocks as $childBlock)
                                                        @if($childBlock->is_active && isset($row[$childBlock->name]))
                                                            <div class="mb-2">
                                                                @if($childBlock->type === 'media' && $row[$childBlock->name])
                                                                    <img src="{{ asset('storage/' . $row[$childBlock->name]) }}"
                                                                        alt="{{ $childBlock->label }}"
                                                                        class="img-fluid rounded-3 mb-2 w-100" style="height: 160px; object-fit: cover;">
                                                                @else
                                                                    <small class="text-muted d-block fw-bold text-uppercase">{{ $childBlock->label }}</small>
                                                                    <span class="text-body">{{ $row[$childBlock->name] }}</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @break

                        @case('posts')
                            @php
                                $postIds = $block->getDecodedValue() ?? [];
                                $posts = !empty($postIds)
                                    ? \Plugins\Posts\Models\Post::whereIn('id', $postIds)
                                        ->where('status', 'published')
                                        ->get()
                                    : collect();
                            @endphp
                            @if($posts->isNotEmpty())
                                <div class="row g-4">
                                    @foreach($posts as $post)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="card border-0 shadow-sm h-100 rounded-4">
                                                @if($post->featuredImage)
                                                    <img src="{{ $post->featuredImage->url }}"
                                                        alt="{{ $post->title }}"
                                                        class="card-img-top rounded-top-4" style="height: 200px; object-fit: cover;">
                                                @endif
                                                <div class="card-body">
                                                    <h5 class="fw-bold">{{ $post->title }}</h5>
                                                    <p class="text-muted small">{{ $post->excerpt }}</p>
                                                    <a href="{{ route('posts.show', $post->slug) }}" class="btn btn-outline-primary btn-sm rounded-pill">Read More</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @break

                        @default
                            <div class="text-muted fst-italic">{{ $block->value }}</div>
                    @endswitch
                </div>
            </section>
        @endif
    @endforeach
@endsection
