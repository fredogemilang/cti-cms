@extends('kouventa::layouts.app')

@section('content')
<div class="kv-canvas-wrap">
<!-- ============ NAV + HERO ============ -->
<section style="background: #071C33; color: #FFFFFF; display: flex; flex-direction: column; position: relative">
<div style="position: absolute; inset: 0; overflow: hidden; pointer-events: none"><div style="position: absolute; right: -240px; top: -200px; width: 900px; height: 900px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07)"></div></div>
<div style="padding: 88px 80px 96px; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 18px; position: relative">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 13px; letter-spacing: 0.1em; text-transform: uppercase; color: #93A4B6">{{ t('Blog') }}</span>
<h1 style="margin: 0; max-width: 900px; font-size: 64px; line-height: 1.05; font-weight: 800; letter-spacing: -0.035em">{{ t('Explore the newest articles') }}<br><span style="color: #2BD46A">{{ t('in our blog.') }}</span></h1>
</div>
</section>

<!-- ============ ARTICLES ============ -->
@php
    $isPostsActive = is_plugin_active('posts');
    $categories = $isPostsActive ? \Plugins\Posts\Models\Category::where('slug', '!=', 'blog')->get() : collect();
    $posts = $isPostsActive ? \Plugins\Posts\Models\Post::published()->with('categories')->orderBy('published_at', 'desc')->get() : collect();
    $postsData = $posts->map(fn($p) => ['id' => $p->id, 'cats' => $p->categories->pluck('name')->toArray()]);
@endphp

<script>
function initInsightBlog() {
    return {
        currentCat: 'All',
        posts: {!! json_encode($postsData) !!},
        get isEmpty() {
            if (this.currentCat === 'All') return this.posts.length === 0;
            return !this.posts.some(p => p.cats.includes(this.currentCat));
        },
        isPostVisible(id) {
            if (this.currentCat === 'All') return true;
            const p = this.posts.find(x => x.id === id);
            return p ? p.cats.includes(this.currentCat) : false;
        }
    };
}
</script>

<section style="padding: 56px 80px 112px; display: flex; flex-direction: column; gap: 48px"
         x-data="initInsightBlog()">
<div role="group" aria-label="Filter by category" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 10px">
<button type="button"
        :aria-pressed="currentCat === 'All'"
        @click="currentCat = 'All'"
        :style="currentCat === 'All' ? 'min-height: 44px; padding: 10px 18px; border-radius: 999px; font-size: 14px; font-weight: 700; background: #071C33; color: #FFFFFF; border: 1px solid #071C33; cursor: pointer;' : 'min-height: 44px; padding: 10px 18px; border-radius: 999px; font-size: 14px; font-weight: 700; background: #FFFFFF; color: #0E1A24; border: 1px solid #DCE3E0; cursor: pointer;'">
    {{ t('All') }}
</button>
@foreach($categories as $category)
<button type="button"
        :aria-pressed="currentCat === '{{ $category->name }}'"
        @click="currentCat = '{{ $category->name }}'"
        :style="currentCat === '{{ $category->name }}' ? 'min-height: 44px; padding: 10px 18px; border-radius: 999px; font-size: 14px; font-weight: 700; background: #071C33; color: #FFFFFF; border: 1px solid #071C33; cursor: pointer;' : 'min-height: 44px; padding: 10px 18px; border-radius: 999px; font-size: 14px; font-weight: 700; background: #FFFFFF; color: #0E1A24; border: 1px solid #DCE3E0; cursor: pointer;'">
    {{ t($category->name) }}
</button>
@endforeach
</div>

<div style="min-height: 960px">
<div x-show="isEmpty" x-cloak style="border: 1.5px dashed #C3CDC8; border-radius: 20px; padding: 64px; text-align: center; font-size: 17px; color: #4A5866">
    {{ t('No articles in this category yet.') }}
</div>

@if($posts->isNotEmpty())
<div x-show="!isEmpty" style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); column-gap: 32px; row-gap: 48px">
@foreach($posts as $post)
@php
    $imgUrl = '';
    if ($post->featured_image) {
        if (str_starts_with($post->featured_image, 'images/')) {
            $imgUrl = theme_asset($post->featured_image);
        } else {
            $imgUrl = resolve_block_asset($post->featured_image);
        }
    }
    $readingMinutes = max(1, ceil(str_word_count(strip_tags($post->content)) / 200));
@endphp
<article x-show="isPostVisible({{ $post->id }})"
         style="display: flex; flex-direction: column; gap: 14px">
@if($imgUrl)
    <img src="{{ $imgUrl }}" alt="{{ $post->title }}" style="width: 100%; aspect-ratio: 3 / 1; object-fit: cover; border-radius: 16px; display: block; background: #DCE3E0">
@else
    <div style="width: 100%; aspect-ratio: 3 / 1; border-radius: 16px; display: flex; align-items: center; justify-content: center; background: #071C33; color: #2BD46A; font-weight: 700; font-size: 18px">Kouventa</div>
@endif
<div style="display: flex; flex-wrap: wrap; gap: 6px">
@foreach($post->categories as $tag)
<span style="font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 999px; background: #E6F8EC; color: #13803F">{{ t($tag->name) }}</span>
@endforeach
</div>
<h3 style="margin: 0; font-size: 19px; line-height: 1.4; font-weight: 800; letter-spacing: -0.01em">
    <a href="{{ $post->getUrl() }}" style="color: inherit; text-decoration: none;">{{ $post->title }}</a>
</h3>
<div style="display: flex; align-items: center; gap: 16px; font-size: 13px; color: #4A5866">
<span style="display: flex; align-items: center; gap: 6px"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 10h18"></path></svg><span>{{ $post->published_at ? $post->published_at->format('d F Y') : $post->created_at->format('d F Y') }}</span></span>
<span style="display: flex; align-items: center; gap: 6px"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg><span>{{ $readingMinutes }} {{ t('minutes reading') }}</span></span>
</div>
<a href="{{ $post->getUrl() }}" style="align-self: flex-start; display: flex; align-items: center; gap: 6px; font-size: 15px; font-weight: 700; color: #13803F; padding: 4px 0; text-decoration: none">
    <span>{{ t('Read more') }}</span>
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
</a>
</article>
@endforeach
</div>
@else
<div style="border: 1.5px dashed #C3CDC8; border-radius: 20px; padding: 64px; text-align: center; font-size: 17px; color: #4A5866">
    {{ t('No articles in this category yet.') }}
</div>
@endif
</div>

<nav aria-label="Pagination" style="display: flex; justify-content: center; gap: 8px">
<span aria-current="page" aria-label="Page 1" style="width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: #071C33; color: #FFFFFF; font-size: 15px; font-weight: 700">1</span>
</nav>
</section>

</div>
@endsection
