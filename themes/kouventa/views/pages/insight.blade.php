@extends('kouventa::layouts.app')

@php
    $page = $page ?? (class_exists(\App\Models\Page::class) ? \App\Models\Page::where('slug', 'insight')->first() : null);
    $selectedCategory = $selectedCategory ?? null;
    $selectedTag = $selectedTag ?? null;
    $searchQuery = $searchQuery ?? null;
    $category = $category ?? null;
    $tag = $tag ?? null;

    $activeCategory = $category ?? ($selectedCategory ? (\Plugins\Posts\Models\Category::where('slug', $selectedCategory)->orWhere('id', $selectedCategory)->first()) : null);
    $activeTag = $tag ?? ($selectedTag ? (\Plugins\Posts\Models\Tag::where('slug', $selectedTag)->orWhere('id', $selectedTag)->first()) : null);

    if ($activeCategory) {
        $heroBadge = $activeCategory->name;
        $heroTitle = [
            'prefix' => t('kouventa.category_articles_prefix', 'Articles in category'),
            'main' => $activeCategory->name,
        ];
        $heroDescription = $activeCategory->description ?: ($page?->block('hero_desc', t('kouventa.insight_desc', 'Explore our latest thinking on Agentic AI, WhatsApp Business automation, and generative AI innovations.')));
    } elseif ($activeTag) {
        $heroBadge = $activeTag->name;
        $heroTitle = [
            'prefix' => t('kouventa.tag_articles_prefix', 'Articles tagged'),
            'main' => $activeTag->name,
        ];
        $heroDescription = $page?->block('hero_desc', t('kouventa.insight_desc', 'Explore our latest thinking on Agentic AI, WhatsApp Business automation, and generative AI innovations.'));
    } elseif (!empty($searchQuery)) {
        $heroBadge = t('Search');
        $heroTitle = [
            'prefix' => t('kouventa.search_results_prefix', 'Search results for'),
            'main' => '"' . e($searchQuery) . '"',
        ];
        $heroDescription = $page?->block('hero_desc', t('kouventa.insight_desc', 'Explore our latest thinking on Agentic AI, WhatsApp Business automation, and generative AI innovations.'));
    } else {
        $heroBadge = $page?->block('hero_badge', 'Blog') ?? 'BLOG';
        $heroTitle = $page?->titleBlock('hero_title', [
            'prefix' => 'Explore the newest articles',
            'main' => 'in our blog.',
        ]) ?? [
            'prefix' => 'Explore the newest articles',
            'main' => 'in our blog.',
        ];
        $heroDescription = $page?->block('hero_desc', t('kouventa.insight_desc', 'Explore our latest thinking on Agentic AI, WhatsApp Business automation, and generative AI innovations.'));
    }
@endphp

@section('content')
<div class="kv-canvas-wrap">
<!-- ============ NAV + HERO (Navy #071C33 Consistent with /product/ai/) ============ -->
<section class="kv-insight-hero">
    <!-- Decorative Circle Ring matching /product/ai/ & /solution/ce/ -->
    <div style="position: absolute; inset: 0; overflow: hidden; pointer-events: none">
        <div style="position: absolute; right: -240px; top: -200px; width: 900px; height: 900px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07)"></div>
    </div>
    <div class="kv-hero-container">
        <div class="kv-hero-badge">
            <span class="kv-badge-dot"></span>
            <span>{{ $heroBadge ?? 'BLOG' }}</span>
        </div>
        <h1 class="kv-hero-heading">
            {{ $heroTitle['prefix'] ?? 'Explore the newest articles' }}<br>
            <span style="color: #2BD46A">{{ $heroTitle['main'] ?? 'in our blog.' }}</span>
        </h1>
    </div>
</section>

<!-- ============ ARTICLES & CATEGORIES ============ -->
@php
    $isPostsActive = is_plugin_active('posts');
    $categories = $isPostsActive ? \Plugins\Posts\Models\Category::where('slug', '!=', 'blog')->get() : collect();
    $posts = $isPostsActive ? \Plugins\Posts\Models\Post::published()->with('categories')->orderBy('published_at', 'desc')->get() : collect();
    $postsData = $posts->map(fn($p) => ['id' => $p->id, 'cats' => $p->categories->pluck('name')->toArray()]);
    $initialCat = $activeCategory ? $activeCategory->name : 'All';
@endphp

<script>
function initInsightBlog(initialCat = 'All') {
    return {
        currentCat: initialCat,
        currentPage: 1,
        perPage: 9,
        posts: {!! json_encode($postsData) !!},
        get filteredPosts() {
            if (this.currentCat === 'All') return this.posts;
            return this.posts.filter(p => p.cats.includes(this.currentCat));
        },
        get totalPages() {
            return Math.ceil(this.filteredPosts.length / this.perPage) || 1;
        },
        get isEmpty() {
            return this.filteredPosts.length === 0;
        },
        isPostVisible(id) {
            const index = this.filteredPosts.findIndex(p => p.id === id);
            if (index === -1) return false;
            const start = (this.currentPage - 1) * this.perPage;
            const end = start + this.perPage;
            return index >= start && index < end;
        },
        setCategory(cat) {
            this.currentCat = cat;
            this.currentPage = 1;
        },
        goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
                const section = document.querySelector('.kv-insight-section');
                if (section) {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        }
    };
}
</script>

<style>
/* ============================================================
   KOUVENTA INSIGHT ARCHIVE — ULTRA-PREMIUM ENTERPRISE STYLING
   ============================================================ */

/* Hero: Navy #071C33 matching /product/ai/ and other pages */
.kv-insight-hero {
    position: relative;
    overflow: hidden;
    background: #071C33 !important;
    color: #FFFFFF !important;
    padding: 88px 80px 88px;
    display: flex;
    justify-content: center;
}

.kv-hero-container {
    position: relative;
    max-width: 900px;
    width: 100%;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 20px;
    z-index: 2;
}

.kv-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 18px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(8px);
    font-family: 'JetBrains Mono', monospace;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #93A4B6;
}

.kv-badge-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #2BD46A;
    box-shadow: 0 0 8px #2BD46A;
}

.kv-hero-heading {
    margin: 0;
    font-size: clamp(36px, 5vw, 64px);
    line-height: 1.05;
    font-weight: 800;
    letter-spacing: -0.035em;
    color: #FFFFFF;
}

.kv-hero-desc {
    margin: 0;
    max-width: 640px;
    font-size: 18px;
    line-height: 1.5;
    color: #B9C6D3;
}

/* Category Filter Bar: Navy #071C33 Subnav style matching Kouventa */
.kv-tab-bar-wrap {
    width: 100%;
    background: #071C33;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    position: sticky;
    top: 80px;
    z-index: 30;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

.kv-tab-bar {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.kv-tab-bar::-webkit-scrollbar {
    display: none;
}

.kv-tab-item {
    position: relative;
    padding: 16px 18px;
    font-size: 14.5px;
    font-weight: 600;
    color: #93A4B6;
    background: transparent;
    border: none;
    cursor: pointer;
    white-space: nowrap;
    transition: color 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.kv-tab-item:hover {
    color: #FFFFFF;
}
.kv-tab-item.is-active {
    color: #FFFFFF;
    font-weight: 700;
}
.kv-tab-underline {
    position: absolute;
    bottom: -1px;
    left: 16px;
    right: 16px;
    height: 2.5px;
    background: #2BD46A;
    border-radius: 999px;
    box-shadow: 0 0 10px rgba(43, 212, 106, 0.5);
}

/* Archive Grid Container */
.kv-insight-section {
    max-width: 1320px;
    margin: 0 auto;
    padding: 56px 32px 100px;
    display: flex;
    flex-direction: column;
    gap: 48px;
}

/* Strict Columns: 3 on Desktop, 2 on Mobile/Tablet */
.kv-insight-grid {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    column-gap: 28px;
    row-gap: 36px;
}

/* Card Design: Elevated White Card */
.kv-insight-card {
    background: #FFFFFF;
    border: 1px solid #E5E9EB;
    border-radius: 18px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 3px 12px -2px rgba(14, 26, 36, 0.04);
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.35s ease;
    position: relative;
}

.kv-insight-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 18px 36px -8px rgba(14, 26, 36, 0.08), 0 4px 14px -2px rgba(19, 128, 63, 0.06);
    border-color: rgba(43, 212, 106, 0.45);
}

/* Image Container: Native Support for 2560 x 871 Featured Images */
.kv-card-img-wrap {
    display: block;
    width: 100%;
    aspect-ratio: 2560 / 871;
    overflow: hidden;
    position: relative;
    background: #0E1A24;
    border-bottom: 1px solid #F0F4F2;
}

.kv-insight-card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

.kv-insight-card:hover .kv-insight-card-img {
    transform: scale(1.04);
}

.kv-card-img-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #071C33 0%, #0E2A4A 100%);
    color: #2BD46A;
}

/* Card Body */
.kv-card-body {
    padding: 20px 22px 22px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.kv-card-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 12px;
}

.kv-tag-badge {
    font-size: 11.5px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 999px;
    background: #E8FAF0;
    color: #13803F;
    border: 1px solid rgba(43, 212, 106, 0.25);
    letter-spacing: 0.01em;
}

.kv-card-title {
    margin: 0 0 14px;
    font-size: 17.5px;
    line-height: 1.4;
    font-weight: 700;
    color: #0E1A24;
    letter-spacing: -0.015em;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 48px;
}

.kv-card-title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.2s ease;
}

.kv-insight-card:hover .kv-card-title a {
    color: #13803F;
}

/* Card Footer: Stacked like Kouventa.ai */
.kv-card-footer {
    margin-top: auto;
    padding-top: 14px;
    border-top: 1px solid #F0F4F2;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.kv-card-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12.5px;
    color: #64748B;
    font-weight: 500;
}

.kv-meta-item {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.kv-meta-sep {
    color: #CBD5E1;
}

.kv-card-readmore {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 700;
    color: #13803F;
    text-decoration: none;
    transition: gap 0.2s ease, color 0.2s ease;
    align-self: flex-start;
}

.kv-insight-card:hover .kv-card-readmore {
    gap: 9px;
    color: #0E6531;
}

.kv-card-readmore svg {
    transition: transform 0.2s ease;
}
.kv-insight-card:hover .kv-card-readmore svg {
    transform: translateX(3px);
}

/* Pagination: Matching Live Kouventa Circular Style */
.kv-pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 16px;
}

.kv-page-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    background: #E2E8F0;
    color: #4A5866;
}

.kv-page-btn.is-active {
    background: #13803F;
    color: #FFFFFF;
    font-weight: 700;
    box-shadow: 0 4px 14px rgba(19, 128, 63, 0.35);
}

.kv-page-btn.is-prev,
.kv-page-btn.is-next {
    background: #13803F;
    color: #FFFFFF;
}

.kv-page-btn.is-prev:hover,
.kv-page-btn.is-next:hover {
    background: #0E6531;
    color: #FFFFFF;
}

.kv-page-btn:hover:not(.is-active) {
    background: #CBD5E1;
    color: #0E1A24;
}

/* Responsive Styles */
@media (max-width: 1023px) {
    .kv-insight-hero {
        padding: 64px 32px 64px !important;
    }
    .kv-insight-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        column-gap: 20px !important;
        row-gap: 28px !important;
    }
    .kv-insight-section {
        padding: 40px 20px 80px !important;
        gap: 36px !important;
    }
    .kv-tab-bar {
        justify-content: flex-start !important;
        padding: 0 16px !important;
    }
}

@media (max-width: 640px) {
    .kv-insight-hero {
        padding: 48px 16px 44px !important;
    }
    .kv-hero-desc {
        font-size: 15px !important;
    }
    .kv-tab-bar-wrap {
        top: 60px !important;
    }
    .kv-tab-item {
        padding: 12px 14px !important;
        font-size: 13.5px !important;
    }
    .kv-insight-section {
        padding: 24px 12px 64px !important;
        gap: 24px !important;
    }
    .kv-insight-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        column-gap: 10px !important;
        row-gap: 16px !important;
    }
    .kv-insight-card {
        border-radius: 14px !important;
    }
    .kv-card-body {
        padding: 12px 10px 14px !important;
    }
    .kv-card-tags {
        margin-bottom: 6px !important;
    }
    .kv-tag-badge {
        font-size: 9.5px !important;
        padding: 2px 7px !important;
    }
    .kv-card-title {
        font-size: 13px !important;
        line-height: 1.35 !important;
        min-height: 35px !important;
        margin-bottom: 8px !important;
    }
    .kv-card-footer {
        padding-top: 8px !important;
        gap: 6px !important;
    }
    .kv-card-meta {
        font-size: 10px !important;
        gap: 4px !important;
        flex-wrap: wrap !important;
    }
    .kv-card-meta svg {
        width: 11px !important;
        height: 11px !important;
    }
    .kv-card-readmore {
        font-size: 11.5px !important;
    }
}
</style>

<!-- Category Filter Sticky Bar & Archive Grid Container -->
<div x-data="initInsightBlog('{{ addslashes($initialCat) }}')">
<div class="kv-tab-bar-wrap">
    <div class="kv-tab-bar" role="group" aria-label="Filter by category">
        <button type="button"
                class="kv-tab-item"
                :class="{ 'is-active': currentCat === 'All' }"
                @click="setCategory('All')"
                :aria-pressed="currentCat === 'All'">
            <span>{{ t('All') }}</span>
            <span class="kv-tab-underline" x-show="currentCat === 'All'"></span>
        </button>
        @foreach($categories as $catItem)
        <button type="button"
                class="kv-tab-item"
                :class="{ 'is-active': currentCat === '{{ $catItem->name }}' }"
                @click="setCategory('{{ $catItem->name }}')"
                :aria-pressed="currentCat === '{{ $catItem->name }}'">
            <span>{{ t($catItem->name) }}</span>
            <span class="kv-tab-underline" x-show="currentCat === '{{ $catItem->name }}'"></span>
        </button>
        @endforeach
    </div>
</div>

<!-- Main Archive Section -->
<section class="kv-insight-section">
    <div style="min-height: 600px">
        <div x-show="isEmpty" x-cloak style="border: 1.5px dashed #CBD5E1; border-radius: 20px; padding: 64px 24px; text-align: center; font-size: 16px; color: #64748B; background: #FFFFFF;">
            {{ t('No articles in this category yet.') }}
        </div>

        @if($posts->isNotEmpty())
        <div x-show="!isEmpty" class="kv-insight-grid">
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
                 class="kv-insight-card">
            <a href="{{ $post->getUrl() }}" class="kv-card-img-wrap" tabindex="-1" aria-hidden="true">
                @if($imgUrl)
                    <img src="{{ $imgUrl }}" alt="{{ $post->title }}" class="kv-insight-card-img" loading="lazy">
                @else
                    <div class="kv-card-img-placeholder">
                        <span style="font-weight: 700; font-size: 18px">Kouventa</span>
                    </div>
                @endif
            </a>

            <div class="kv-card-body">
                <div class="kv-card-tags">
                    @foreach($post->categories->take(2) as $tag)
                        <span class="kv-tag-badge">{{ t($tag->name) }}</span>
                    @endforeach
                </div>

                <h3 class="kv-card-title">
                    <a href="{{ $post->getUrl() }}">
                        {{ $post->title }}
                    </a>
                </h3>

                <div class="kv-card-footer">
                    <div class="kv-card-meta">
                        <span class="kv-meta-item">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 10h18"></path></svg>
                            <span>{{ $post->published_at ? $post->published_at->format('d M Y') : $post->created_at->format('d M Y') }}</span>
                        </span>
                        <span class="kv-meta-sep">•</span>
                        <span class="kv-meta-item">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg>
                            <span>{{ $readingMinutes }} {{ t('minutes reading') }}</span>
                        </span>
                    </div>

                    <a href="{{ $post->getUrl() }}" class="kv-card-readmore" aria-label="Read {{ $post->title }}">
                        <span>{{ t('Read More') }}</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
                    </a>
                </div>
            </div>
        </article>
        @endforeach
        </div>
        @else
        <div style="border: 1.5px dashed #CBD5E1; border-radius: 20px; padding: 64px 24px; text-align: center; font-size: 16px; color: #64748B; background: #FFFFFF;">
            {{ t('No articles in this category yet.') }}
        </div>
        @endif
    </div>

    <!-- Pagination -->
    <nav aria-label="Pagination" class="kv-pagination" x-show="totalPages > 1" x-cloak>
        <button type="button"
                class="kv-page-btn is-prev"
                @click="goToPage(currentPage - 1)"
                x-show="currentPage > 1"
                aria-label="Previous page">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        </button>
        <template x-for="p in totalPages" :key="p">
            <button type="button"
                    class="kv-page-btn"
                    :class="{ 'is-active': currentPage === p }"
                    @click="goToPage(p)"
                    x-text="p"
                    :aria-current="currentPage === p ? 'page' : false">
            </button>
        </template>
        <button type="button"
                class="kv-page-btn is-next"
                @click="goToPage(currentPage + 1)"
                x-show="currentPage < totalPages"
                aria-label="Next page">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
        </button>
    </nav>
</section>
</div>
</div>
@endsection
