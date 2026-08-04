@extends('themes::ofis.layouts.app')

@section('content')
<section class="bg-slate-50 py-12 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Mandatory SEO Breadcrumbs -->
        <x-seo-breadcrumbs class="text-gray-500 mb-6" />

        <!-- Mandatory Single H1 -->
        <h1 class="text-4xl font-bold text-gray-900 mb-3">
            {{ t('blog.title', 'OFIS Blog & Smart Office Insights') }}
        </h1>
        <p class="text-gray-600 text-lg">
            {{ t('blog.subtitle', 'Latest articles, industry trends, and guidebooks on smart office transformation.') }}
        </p>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($posts as $post)
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="h-48 bg-slate-100 relative overflow-hidden">
                            @if($post->featured_image)
                                <x-image :src="$post->featured_image" :alt="$post->title" class="w-full h-full object-cover" />
                            @else
                                <img src="{{ theme_asset('introducing-ofis-maxresdefault-768x432.jpg-CywJQ2Qk.webp') }}" alt="{{ $post->title }}" class="w-full h-full object-cover" />
                            @endif
                        </div>
                        <div class="p-6">
                            <div class="flex items-center gap-3 text-xs text-gray-400 mb-3">
                                <span>{{ $post->published_at ? $post->published_at->format('M d, Y') : '' }}</span>
                                @if(!empty($post->author))
                                    <span>•</span>
                                    <span>By {{ is_string($post->author) ? $post->author : ($post->author->name ?? 'OFIS Team') }}</span>
                                @endif
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 hover:text-ofis-teal transition">
                                <a href="{{ url('/blog/' . $post->slug) }}">{{ $post->title }}</a>
                            </h2>
                            <p class="text-gray-600 text-sm line-clamp-3 mb-4">
                                {{ Str::limit(strip_tags($post->excerpt ?: $post->content), 130) }}
                            </p>
                        </div>
                    </div>

                    <div class="px-6 pb-6 pt-0">
                        <a href="{{ url('/blog/' . $post->slug) }}" class="text-sm font-bold text-ofis-teal hover:underline inline-flex items-center gap-1">
                            <span>Read Full Article</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-16 text-gray-500">
                    No articles published yet.
                </div>
            @endforelse
        </div>

        @if(method_exists($posts, 'links'))
            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
