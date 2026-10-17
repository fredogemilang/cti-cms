@extends('kouventa::layouts.app')

@php
    $locale = app()->getLocale();
    $postTitle = $post->getTranslation('title', $locale) ?: $post->title;
    $rawContent = $post->getTranslation('content', $locale) ?: $post->content;
    $postExcerpt = $post->getTranslation('excerpt', $locale) ?: ($post->excerpt ?: Str::limit(strip_tags($rawContent), 160));
    $publishedDate = $post->published_at ? $post->published_at->format('d F Y') : $post->created_at->format('d F Y');
    $authorName = $post->author?->name ?? 'Kouventa Editorial';
    $readingMinutes = max(1, (int) ceil(str_word_count(strip_tags($rawContent)) / 200));

    // Featured Image Resolution
    $imgUrl = '';
    if ($post->featured_image) {
        if (str_starts_with($post->featured_image, 'images/')) {
            $imgUrl = theme_asset($post->featured_image);
        } else {
            $imgUrl = resolve_block_asset($post->featured_image);
        }
    }

    // Extract Table of Contents (TOC) from H2 and H3
    $tocItems = [];
    $headingCount = 0;
    $processedContent = preg_replace_callback('/<h([23])([^>]*)>(.*?)<\/h[23]>/i', function ($matches) use (&$tocItems, &$headingCount) {
        $level = (int) $matches[1];
        $attributes = $matches[2];
        $titleRaw = $matches[3];
        $titleText = trim(strip_tags($titleRaw));
        
        $headingCount++;
        if (preg_match('/id=["\']([^"\']+)["\']/i', $attributes, $idMatch)) {
            $slug = $idMatch[1];
        } else {
            $slug = Str::slug($titleText) ?: ('heading-' . $headingCount);
            $attributes .= ' id="' . $slug . '"';
        }

        $tocItems[] = [
            'level' => $level,
            'slug' => $slug,
            'title' => $titleText,
        ];

        return "<h{$level}{$attributes}>{$titleRaw}</h{$level}>";
    }, $rawContent);

    // Related Posts (Rule 15 Plugin Fail-Safe)
    $relatedPosts = collect();
    $prevPost = null;
    $nextPost = null;

    if (is_plugin_active('posts') && class_exists(\Plugins\Posts\Models\Post::class)) {
        $relatedPosts = \Plugins\Posts\Models\Post::published()
            ->where('id', '!=', $post->id)
            ->with('categories')
            ->latest('published_at')
            ->take(3)
            ->get();

        $currentTimestamp = $post->published_at ?? $post->created_at;

        // Previous post (older)
        $prevPost = \Plugins\Posts\Models\Post::published()
            ->where(function ($query) use ($currentTimestamp, $post) {
                $query->where('published_at', '<', $currentTimestamp)
                      ->orWhere(function ($q) use ($currentTimestamp, $post) {
                          $q->where('published_at', '=', $currentTimestamp)
                            ->where('id', '<', $post->id);
                      });
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->first();

        // Next post (newer)
        $nextPost = \Plugins\Posts\Models\Post::published()
            ->where(function ($query) use ($currentTimestamp, $post) {
                $query->where('published_at', '>', $currentTimestamp)
                      ->orWhere(function ($q) use ($currentTimestamp, $post) {
                          $q->where('published_at', '=', $currentTimestamp)
                            ->where('id', '>', $post->id);
                      });
            })
            ->orderBy('published_at', 'asc')
            ->orderBy('id', 'asc')
            ->first();
    }

    $currentUrl = request()->url();
@endphp

@section('content')
<div class="kv-canvas-wrap" style="background: #F4F6F5; min-height: 100vh;">

    <!-- ============ MAIN ARTICLE SECTION ============ -->
    <article class="kv-single-container">
        
        <!-- Breadcrumbs (Mandatory SEO Rule) -->
        <div class="kv-single-breadcrumbs">
            <x-seo-breadcrumbs :entity="$post" class="text-xs text-[#64748B]" />
        </div>

        <!-- Hero Featured Image (Native 2560 x 871 Aspect Ratio) -->
        @if($imgUrl)
        <div class="kv-single-cover-wrap">
            <img src="{{ $imgUrl }}" alt="{{ $postTitle }}" class="kv-single-cover-img" fetchpriority="high">
        </div>
        @endif

        <!-- Article Header -->
        <header class="kv-single-header">
            <!-- Category Badges -->
            <div class="kv-single-tags">
                @foreach($post->categories as $category)
                    <a href="{{ localized_url('/insight') }}" class="kv-tag-pill">
                        {{ t($category->name) }}
                    </a>
                @endforeach
            </div>

            <!-- Main Title -->
            <h1 class="kv-single-title">
                {{ $postTitle }}
            </h1>

            <!-- Meta Row -->
            <div class="kv-single-meta-bar">
                <div class="kv-meta-author">
                    <div class="kv-author-avatar">
                        {{ strtoupper(substr($authorName, 0, 2)) }}
                    </div>
                    <div class="kv-author-info">
                        <span class="kv-author-name">{{ $authorName }}</span>
                        <span class="kv-meta-date-row">
                            <span class="kv-meta-date">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 10h18"></path></svg>
                                {{ $publishedDate }}
                            </span>
                            <span class="kv-meta-dot">•</span>
                            <span class="kv-meta-reading">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg>
                                {{ $readingMinutes }} {{ t('minutes reading') }}
                            </span>
                        </span>
                    </div>
                </div>

                <!-- Quick Share Action -->
                <div class="kv-share-capsule" x-data="{ copied: false }">
                    <span class="kv-share-label">{{ t('Share') }}:</span>
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($postTitle . ' ' . $currentUrl) }}" target="_blank" rel="noopener noreferrer" class="kv-share-btn" title="Share on WhatsApp" aria-label="Share on WhatsApp">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($currentUrl) }}" target="_blank" rel="noopener noreferrer" class="kv-share-btn" title="Share on LinkedIn" aria-label="Share on LinkedIn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                    </a>
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($postTitle) }}&url={{ urlencode($currentUrl) }}" target="_blank" rel="noopener noreferrer" class="kv-share-btn" title="Share on X" aria-label="Share on X">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path></svg>
                    </a>
                    <button type="button" @click="navigator.clipboard.writeText('{{ $currentUrl }}'); copied = true; setTimeout(() => copied = false, 2500)" class="kv-share-btn" title="Copy Link" aria-label="Copy Link">
                        <svg x-show="!copied" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                        <svg x-show="copied" x-cloak width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- ============ TWO-COLUMN CONTENT BODY ============ -->
        <div class="kv-single-layout">
            
            <!-- Left Main Column: Prose Content -->
            <main class="kv-single-main">
                <div class="kv-article-prose">
                    {!! $processedContent !!}
                </div>

                <!-- Post Tags -->
                @if($post->tags->isNotEmpty())
                <div class="kv-post-tags-wrap">
                    <span class="kv-post-tags-label">{{ t('Tags') }}:</span>
                    @foreach($post->tags as $tag)
                        <span class="kv-post-tag-item">#{{ $tag->name }}</span>
                    @endforeach
                </div>
                @endif

                <!-- Previous & Next Article Navigation -->
                <nav class="kv-post-nav-grid" aria-label="Article navigation">
                    @if($prevPost)
                    <a href="{{ $prevPost->getUrl() }}" class="kv-post-nav-card kv-post-nav-prev">
                        <div class="kv-post-nav-label">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                            <span>{{ t('Previous Article') }}</span>
                        </div>
                        <h4 class="kv-post-nav-title">{{ $prevPost->title }}</h4>
                    </a>
                    @else
                    <a href="{{ localized_url('/insight') }}" class="kv-post-nav-card kv-post-nav-prev">
                        <div class="kv-post-nav-label">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                            <span>{{ t('All Articles') }}</span>
                        </div>
                        <h4 class="kv-post-nav-title">{{ t('Back to Insight & Blog') }}</h4>
                    </a>
                    @endif

                    @if($nextPost)
                    <a href="{{ $nextPost->getUrl() }}" class="kv-post-nav-card kv-post-nav-next">
                        <div class="kv-post-nav-label">
                            <span>{{ t('Next Article') }}</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                        <h4 class="kv-post-nav-title">{{ $nextPost->title }}</h4>
                    </a>
                    @else
                    <a href="{{ localized_url('/insight') }}" class="kv-post-nav-card kv-post-nav-next">
                        <div class="kv-post-nav-label">
                            <span>{{ t('All Articles') }}</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                        <h4 class="kv-post-nav-title">{{ t('Back to Insight & Blog') }}</h4>
                    </a>
                    @endif
                </nav>
            </main>

            <!-- Right Column: Sticky Sidebar with TOC & CTA -->
            <aside class="kv-single-sidebar">
                
                <!-- Table of Contents Card -->
                @if(!empty($tocItems))
                <nav class="kv-toc-card" aria-label="Table of Contents">
                    <div class="kv-toc-header">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#13803F" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        <h3 class="kv-toc-title">{{ t('Table of Contents') }}</h3>
                    </div>
                    <ul class="kv-toc-list">
                        @foreach($tocItems as $item)
                        <li class="kv-toc-item {{ $item['level'] === 3 ? 'kv-toc-sub' : '' }}">
                            <a href="#{{ $item['slug'] }}" class="kv-toc-link">
                                {{ $item['title'] }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </nav>
                @endif

                <!-- Enterprise AI CTA Card -->
                <div class="kv-sidebar-cta-card">
                    <div class="kv-cta-glow" aria-hidden="true"></div>
                    <span class="kv-cta-pill">KOUVENTA AI</span>
                    <h4 class="kv-cta-heading">
                        {{ t('kouventa.cta_sidebar_title', 'Scale your enterprise workflows with Agentic AI') }}
                    </h4>
                    <p class="kv-cta-desc">
                        {{ t('kouventa.cta_sidebar_desc', 'Connect systems, automate finance, HR, and customer engagement with verifiable control.') }}
                    </p>
                    <a href="{{ localized_url('/book-demo') }}" class="kv-cta-button">
                        <span>{{ t('Book a Demo') }}</span>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
                    </a>
                </div>

            </aside>

        </div>

    </article>

    <!-- ============ RELATED ARTICLES ============ -->
    @if($relatedPosts->isNotEmpty())
    <section class="kv-related-section">
        <div class="kv-related-container">
            <div class="kv-related-header">
                <div>
                    <span class="kv-related-eyebrow">{{ t('READ NEXT') }}</span>
                    <h2 class="kv-related-title">{{ t('Related Articles') }}</h2>
                </div>
                <a href="{{ localized_url('/insight') }}" class="kv-related-viewall">
                    <span>{{ t('View all insights') }}</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
                </a>
            </div>

            <div class="kv-related-grid">
                @foreach($relatedPosts as $relPost)
                @php
                    $relImg = '';
                    if ($relPost->featured_image) {
                        if (str_starts_with($relPost->featured_image, 'images/')) {
                            $relImg = theme_asset($relPost->featured_image);
                        } else {
                            $relImg = resolve_block_asset($relPost->featured_image);
                        }
                    }
                    $relReading = max(1, ceil(str_word_count(strip_tags($relPost->content)) / 200));
                @endphp
                <article class="kv-rel-card">
                    <a href="{{ $relPost->getUrl() }}" class="kv-rel-img-wrap" tabindex="-1" aria-hidden="true">
                        @if($relImg)
                            <img src="{{ $relImg }}" alt="{{ $relPost->title }}" class="kv-rel-img" loading="lazy">
                        @else
                            <div class="kv-rel-placeholder">Kouventa</div>
                        @endif
                    </a>
                    <div class="kv-rel-body">
                        <div class="kv-rel-tags">
                            @foreach($relPost->categories->take(2) as $relCat)
                                <span class="kv-tag-badge">{{ t($relCat->name) }}</span>
                            @endforeach
                        </div>
                        <h3 class="kv-rel-card-title">
                            <a href="{{ $relPost->getUrl() }}">
                                {{ $relPost->title }}
                            </a>
                        </h3>
                        <div class="kv-rel-footer">
                            <div class="kv-rel-meta">
                                <span>{{ $relPost->published_at ? $relPost->published_at->format('d M Y') : $relPost->created_at->format('d M Y') }}</span>
                                <span>•</span>
                                <span>{{ $relReading }} {{ t('minutes reading') }}</span>
                            </div>
                            <a href="{{ $relPost->getUrl() }}" class="kv-rel-readmore">
                                <span>{{ t('Read More') }}</span>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
                            </a>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

</div>

<!-- ============================================================
     KOUVENTA SINGLE POST CSS STYLING
     ============================================================ -->
<style>
/* Main Container */
.kv-single-container {
    max-width: 1240px;
    margin: 0 auto;
    padding: 36px 24px 80px;
}

.kv-single-breadcrumbs {
    margin-bottom: 24px;
}

/* Featured Image Cover (Native 2560 x 871) */
.kv-single-cover-wrap {
    width: 100%;
    aspect-ratio: 2560 / 871;
    border-radius: 20px;
    overflow: hidden;
    position: relative;
    background: #071C33;
    box-shadow: 0 12px 36px -8px rgba(7, 28, 51, 0.08), 0 2px 8px rgba(7, 28, 51, 0.04);
    margin-bottom: 36px;
    border: 1px solid rgba(226, 232, 240, 0.8);
}

.kv-single-cover-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* Header Area */
.kv-single-header {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 40px;
    padding-bottom: 32px;
    border-bottom: 1px solid #E2E8F0;
}

.kv-single-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.kv-tag-pill {
    display: inline-block;
    font-size: 12px;
    font-weight: 700;
    padding: 4px 14px;
    border-radius: 999px;
    background: #E8FAF0;
    color: #13803F;
    border: 1px solid rgba(43, 212, 106, 0.3);
    text-decoration: none;
    transition: all 0.2s ease;
}
.kv-tag-pill:hover {
    background: #13803F;
    color: #FFFFFF;
}

.kv-single-title {
    margin: 0;
    font-size: clamp(28px, 4vw, 44px);
    line-height: 1.22;
    font-weight: 800;
    letter-spacing: -0.025em;
    color: #0E1A24;
}

/* Meta Bar */
.kv-single-meta-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    padding-top: 8px;
}

.kv-meta-author {
    display: flex;
    align-items: center;
    gap: 12px;
}

.kv-author-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #071C33;
    color: #2BD46A;
    font-size: 14px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(7, 28, 51, 0.15);
}

.kv-author-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.kv-author-name {
    font-size: 15px;
    font-weight: 700;
    color: #0E1A24;
}

.kv-meta-date-row {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #64748B;
}

.kv-meta-date, .kv-meta-reading {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.kv-meta-dot {
    color: #CBD5E1;
}

/* Social Share Capsule */
.kv-share-capsule {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 999px;
    padding: 6px 14px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
}

.kv-share-label {
    font-size: 12.5px;
    font-weight: 600;
    color: #64748B;
}

.kv-share-btn {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #475569;
    background: transparent;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease;
}
.kv-share-btn:hover {
    background: #F1F5F9;
    color: #13803F;
}

/* Layout: Content + Sidebar */
.kv-single-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 340px;
    gap: 48px;
    align-items: start;
}

.kv-single-main {
    min-width: 0;
}

/* Prose Typography */
.kv-article-prose {
    font-size: 17px;
    line-height: 1.85;
    color: #334155;
}

.kv-article-prose p {
    margin-bottom: 24px;
}

.kv-article-prose h2 {
    font-size: 26px;
    line-height: 1.35;
    font-weight: 800;
    color: #0E1A24;
    margin: 44px 0 18px;
    letter-spacing: -0.02em;
    scroll-margin-top: 100px;
}

.kv-article-prose h3 {
    font-size: 20px;
    line-height: 1.4;
    font-weight: 700;
    color: #0E1A24;
    margin: 32px 0 14px;
    letter-spacing: -0.015em;
    scroll-margin-top: 100px;
}

.kv-article-prose strong {
    font-weight: 700;
    color: #0E1A24;
}

.kv-article-prose ul {
    margin: 20px 0 24px 24px;
    list-style-type: disc;
}

.kv-article-prose ol {
    margin: 20px 0 24px 24px;
    list-style-type: decimal;
}

.kv-article-prose li {
    margin-bottom: 10px;
}

.kv-article-prose blockquote {
    margin: 32px 0;
    padding: 20px 24px;
    background: #F0FDF4;
    border-left: 4px solid #13803F;
    border-radius: 0 14px 14px 0;
    color: #166534;
    font-style: italic;
    font-size: 18px;
    line-height: 1.7;
}

.kv-article-prose img {
    max-width: 100%;
    height: auto;
    border-radius: 14px;
    margin: 28px 0;
    box-shadow: 0 4px 16px rgba(0,0,0,0.06);
}

/* Post Tags */
.kv-post-tags-wrap {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 48px;
    padding-top: 24px;
    border-top: 1px solid #E2E8F0;
}

.kv-post-tags-label {
    font-size: 13px;
    font-weight: 700;
    color: #64748B;
}

.kv-post-tag-item {
    font-size: 12.5px;
    color: #475569;
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    padding: 4px 12px;
    border-radius: 999px;
}

/* ============================================================
   PREVIOUS & NEXT ARTICLE NAVIGATION
   ============================================================ */
.kv-post-nav-grid {
    margin-top: 40px;
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
}

.kv-post-nav-card {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 18px;
    padding: 22px 24px;
    display: flex;
    flex-direction: column;
    text-decoration: none;
    box-shadow: 0 3px 12px -2px rgba(14, 26, 36, 0.03);
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease;
    position: relative;
    overflow: hidden;
}

.kv-post-nav-card:hover {
    transform: translateY(-3px);
    border-color: rgba(43, 212, 106, 0.5);
    box-shadow: 0 14px 28px -6px rgba(14, 26, 36, 0.08);
}

.kv-post-nav-next {
    text-align: right;
    align-items: flex-end;
}

.kv-post-nav-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #13803F;
    margin-bottom: 10px;
    transition: gap 0.2s ease;
}

.kv-post-nav-prev:hover .kv-post-nav-label {
    gap: 11px;
}

.kv-post-nav-next:hover .kv-post-nav-label {
    gap: 11px;
}

.kv-post-nav-label svg {
    flex-shrink: 0;
}

.kv-post-nav-title {
    margin: 0;
    font-size: 15px;
    line-height: 1.45;
    font-weight: 700;
    color: #0E1A24;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.2s ease;
}

.kv-post-nav-card:hover .kv-post-nav-title {
    color: #13803F;
}

/* Sticky Sidebar */
.kv-single-sidebar {
    position: sticky;
    top: 104px;
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* Table of Contents Card */
.kv-toc-card {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 18px;
    padding: 22px 24px;
    box-shadow: 0 4px 16px -2px rgba(14, 26, 36, 0.04);
}

.kv-toc-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #F1F5F9;
}

.kv-toc-title {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: #0E1A24;
}

.kv-toc-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.kv-toc-item {
    font-size: 13.5px;
    line-height: 1.5;
}

.kv-toc-sub {
    padding-left: 14px;
    font-size: 12.5px;
}

.kv-toc-link {
    color: #64748B;
    text-decoration: none;
    transition: color 0.2s ease;
    display: block;
}
.kv-toc-link:hover {
    color: #13803F;
    font-weight: 600;
}

/* Sidebar CTA Card */
.kv-sidebar-cta-card {
    background: #071C33;
    border-radius: 18px;
    padding: 26px 24px;
    color: #FFFFFF;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    gap: 12px;
    box-shadow: 0 10px 30px rgba(7, 28, 51, 0.2);
}

.kv-cta-glow {
    position: absolute;
    right: -40px;
    bottom: -40px;
    width: 140px;
    height: 140px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(43, 212, 106, 0.3) 0%, transparent 70%);
    pointer-events: none;
}

.kv-cta-pill {
    align-self: flex-start;
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.12em;
    padding: 3px 8px;
    border-radius: 6px;
    background: rgba(43, 212, 106, 0.15);
    color: #2BD46A;
    border: 1px solid rgba(43, 212, 106, 0.3);
}

.kv-cta-heading {
    margin: 0;
    font-size: 18px;
    line-height: 1.35;
    font-weight: 800;
    color: #FFFFFF;
}

.kv-cta-desc {
    margin: 0;
    font-size: 13.5px;
    line-height: 1.55;
    color: #B9C6D3;
}

.kv-cta-button {
    margin-top: 6px;
    align-self: flex-start;
    background: #2BD46A;
    color: #06251A;
    font-size: 13.5px;
    font-weight: 700;
    padding: 10px 18px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: all 0.2s ease;
}
.kv-cta-button:hover {
    background: #22B559;
    gap: 9px;
}

/* Related Articles Section */
.kv-related-section {
    background: #FFFFFF;
    border-top: 1px solid #E5E9EB;
    padding: 64px 24px 88px;
}

.kv-related-container {
    max-width: 1240px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 36px;
}

.kv-related-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}

.kv-related-eyebrow {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.12em;
    color: #13803F;
    display: block;
    margin-bottom: 6px;
}

.kv-related-title {
    margin: 0;
    font-size: 32px;
    font-weight: 800;
    letter-spacing: -0.02em;
    color: #0E1A24;
}

.kv-related-viewall {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 700;
    color: #13803F;
    text-decoration: none;
    transition: gap 0.2s ease;
}
.kv-related-viewall:hover {
    gap: 9px;
}

/* Related Grid: 3 columns desktop, 2 columns mobile */
.kv-related-grid {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    column-gap: 28px;
    row-gap: 36px;
}

.kv-rel-card {
    background: #FFFFFF;
    border: 1px solid #E5E9EB;
    border-radius: 18px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 3px 12px -2px rgba(14, 26, 36, 0.04);
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.35s ease;
}
.kv-rel-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 18px 36px -8px rgba(14, 26, 36, 0.08);
    border-color: rgba(43, 212, 106, 0.45);
}

.kv-rel-img-wrap {
    display: block;
    width: 100%;
    aspect-ratio: 2560 / 871;
    overflow: hidden;
    position: relative;
    background: #0E1A24;
}

.kv-rel-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}
.kv-rel-card:hover .kv-rel-img {
    transform: scale(1.04);
}

.kv-rel-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #071C33;
    color: #2BD46A;
    font-weight: 700;
}

.kv-rel-body {
    padding: 18px 20px 20px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.kv-rel-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 10px;
}

.kv-rel-card-title {
    margin: 0 0 12px;
    font-size: 16.5px;
    line-height: 1.4;
    font-weight: 700;
    color: #0E1A24;
    letter-spacing: -0.015em;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 46px;
}
.kv-rel-card-title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.2s ease;
}
.kv-rel-card:hover .kv-rel-card-title a {
    color: #13803F;
}

.kv-rel-footer {
    margin-top: auto;
    padding-top: 12px;
    border-top: 1px solid #F0F4F2;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.kv-rel-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #64748B;
}

.kv-rel-readmore {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 13.5px;
    font-weight: 700;
    color: #13803F;
    text-decoration: none;
    transition: gap 0.2s ease;
}
.kv-rel-card:hover .kv-rel-readmore {
    gap: 8px;
}

/* ============================================================
   RESPONSIVE MEDIA QUERIES
   ============================================================ */
@media (max-width: 1023px) {
    .kv-single-layout {
        grid-template-columns: minmax(0, 1fr) !important;
        gap: 40px !important;
    }
    .kv-single-sidebar {
        position: static !important;
    }
    .kv-related-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        column-gap: 18px !important;
        row-gap: 28px !important;
    }
}

@media (max-width: 640px) {
    .kv-single-container {
        padding: 20px 14px 48px !important;
    }
    .kv-single-cover-wrap {
        border-radius: 12px !important;
        margin-bottom: 24px !important;
    }
    .kv-single-title {
        font-size: 22px !important;
        line-height: 1.3 !important;
    }
    .kv-single-meta-bar {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 14px !important;
    }
    .kv-article-prose {
        font-size: 15.5px !important;
        line-height: 1.75 !important;
    }
    .kv-article-prose h2 {
        font-size: 21px !important;
        margin: 32px 0 14px !important;
    }
    .kv-article-prose h3 {
        font-size: 18px !important;
        margin: 24px 0 10px !important;
    }
    .kv-post-nav-grid {
        grid-template-columns: 1fr !important;
        gap: 12px !important;
        margin-top: 32px !important;
    }
    .kv-post-nav-card {
        padding: 16px 18px !important;
        border-radius: 14px !important;
    }
    .kv-post-nav-next {
        text-align: left !important;
        align-items: flex-start !important;
    }
    .kv-post-nav-title {
        font-size: 14px !important;
    }
    .kv-related-section {
        padding: 40px 14px 64px !important;
    }
    .kv-related-title {
        font-size: 24px !important;
    }
    .kv-related-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        column-gap: 10px !important;
        row-gap: 16px !important;
    }
    .kv-rel-body {
        padding: 10px 10px 12px !important;
    }
    .kv-rel-card-title {
        font-size: 12.5px !important;
        line-height: 1.35 !important;
        min-height: 34px !important;
    }
    .kv-rel-footer {
        padding-top: 8px !important;
        gap: 6px !important;
    }
    .kv-rel-meta {
        font-size: 10px !important;
        gap: 4px !important;
        flex-wrap: wrap !important;
    }
    .kv-rel-readmore {
        font-size: 11.5px !important;
    }
}
</style>
@endsection
