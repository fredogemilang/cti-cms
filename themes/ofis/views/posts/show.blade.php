@extends('ofis::layouts.app')

@section('content')
<article class="py-12 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Mandatory SEO Breadcrumbs Component -->
        <x-seo-breadcrumbs :entity="$post" class="text-gray-500 mb-8" />

        <!-- Header -->
        <header class="mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 leading-tight mb-4">
                {{ $post->title }}
            </h1>
            <div class="flex items-center gap-4 text-sm text-gray-500 pb-6 border-b border-slate-200">
                <span>Published {{ $post->published_at ? $post->published_at->format('F d, Y') : '' }}</span>
                @if(!empty($post->author))
                    <span>•</span>
                    <span>By {{ is_string($post->author) ? $post->author : ($post->author->name ?? 'OFIS Team') }}</span>
                @endif
            </div>
        </header>

        <!-- Featured Image -->
        @if($post->featured_image)
            <div class="mb-10 rounded-2xl overflow-hidden shadow-lg">
                <x-image :src="$post->featured_image" :alt="$post->title" class="w-full h-auto max-h-[480px] object-cover" />
            </div>
        @endif

        <!-- Article Body -->
        <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed mb-16">
            {!! Str::markdown($post->content) !!}
        </div>

        <!-- Back to Blog Button & CTA -->
        <div class="pt-8 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-6">
            <a href="{{ url('/blog') }}" class="text-sm font-bold text-ofis-teal hover:underline flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Back to All Articles</span>
            </a>

            <a href="{{ url('/#contact') }}" class="py-3 px-6 bg-[#fab54f] hover:bg-[#fab54f]/90 text-ofis-ink font-bold rounded-full text-sm shadow-md transition">
                Consult With OFIS Expert
            </a>
        </div>
    </div>
</article>
@endsection
