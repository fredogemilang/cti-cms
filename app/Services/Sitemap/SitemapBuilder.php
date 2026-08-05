<?php

namespace App\Services\Sitemap;

use App\Events\BuildSitemap;
use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\CustomTaxonomy;
use App\Models\Page;
use App\Models\Setting;
use App\Models\TaxonomyTerm;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Plugins\Posts\Models\Post;

class SitemapBuilder
{
    /**
     * Flush sitemap index cache and type caches.
     */
    public static function clearCache(?string $type = null): void
    {
        Cache::forget('sitemap.xml_index_v2');

        if ($type) {
            Cache::forget("sitemap_type_{$type}_v2");
            Cache::forget('sitemap_type_'.str_replace('_', '-', $type).'_v2');
            Cache::forget('sitemap_type_'.str_replace('-', '_', $type).'_v2');
        } else {
            foreach (['page', 'pages', 'post', 'posts', 'taxonomy', 'taxonomies', 'all'] as $t) {
                Cache::forget("sitemap_type_{$t}_v2");
            }
            try {
                $cptSlugs = CustomPostType::pluck('slug');
                foreach ($cptSlugs as $cptSlug) {
                    Cache::forget("sitemap_type_{$cptSlug}_v2");
                    Cache::forget('sitemap_type_'.str_replace('_', '-', $cptSlug).'_v2');
                    Cache::forget('sitemap_type_'.str_replace('-', '_', $cptSlug).'_v2');
                }
            } catch (\Throwable) {
                // Ignore DB errors during setup/migration
            }
        }
    }

    public function getIndexSitemaps(): array
    {
        $sitemaps = [];

        // 1. Pages sitemap
        if (setting('seo_content_type_pages_index_enabled', true)) {
            $pageUrls = $this->getPageUrls();
            if (! empty($pageUrls)) {
                $lastPageMod = Page::where('status', 'published')->max('updated_at');
                $sitemaps[] = [
                    'loc' => url('/page-sitemap.xml'),
                    'lastmod' => $lastPageMod ? Carbon::parse($lastPageMod)->toAtomString() : now()->toAtomString(),
                    'type' => 'Pages',
                ];
            }
        }

        // 2. Posts plugin sitemap (if plugin is active and indexing enabled)
        if ($this->isPostsPluginActive() && setting('seo_content_type_posts_index_enabled', true)) {
            $postUrls = $this->getPostUrls();
            if (! empty($postUrls)) {
                $postModel = $this->getPostModelClass();
                $lastPostMod = $postModel::where('status', 'published')->max('updated_at');
                $sitemaps[] = [
                    'loc' => url('/post-sitemap.xml'),
                    'lastmod' => $lastPostMod ? Carbon::parse($lastPostMod)->toAtomString() : now()->toAtomString(),
                    'type' => 'Posts',
                ];
            }
        }

        // 3. Custom Post Types sitemaps (respect index_enabled, has_archive, publicly_queryable, and non-empty URLs)
        $cpts = CustomPostType::where('is_active', true)->get();
        foreach ($cpts as $cpt) {
            if (! setting("seo_content_type_{$cpt->slug}_index_enabled", true)) {
                continue;
            }
            if (! $cpt->has_archive && ! $cpt->publicly_queryable) {
                continue;
            }
            $cptUrls = $this->getCptUrls($cpt->slug);
            if (empty($cptUrls)) {
                continue;
            }
            $lastCptMod = CptEntry::where('post_type_id', $cpt->id)->where('status', 'published')->max('updated_at');
            $sitemaps[] = [
                'loc' => url("/{$cpt->slug}-sitemap.xml"),
                'lastmod' => $lastCptMod ? Carbon::parse($lastCptMod)->toAtomString() : now()->toAtomString(),
                'type' => $cpt->name,
            ];
        }

        // 4. Taxonomies sitemap (only if taxonomy URLs are non-empty)
        $taxUrls = $this->getTaxonomyUrls();
        if (! empty($taxUrls)) {
            $lastTaxMod = TaxonomyTerm::max('updated_at');
            $sitemaps[] = [
                'loc' => url('/taxonomy-sitemap.xml'),
                'lastmod' => $lastTaxMod ? Carbon::parse($lastTaxMod)->toAtomString() : now()->toAtomString(),
                'type' => 'Taxonomies',
            ];
        }

        return $sitemaps;
    }

    public function getPageUrls(): array
    {
        $urls = [];
        if (! setting('seo_content_type_pages_index_enabled', true)) {
            return $urls;
        }

        $locales = available_locales();
        $defaultLocale = setting('default_locale', config('app.locale', 'en'));

        foreach (Page::with('allBlocks')->where('status', 'published')->orderBy('updated_at', 'desc')->get() as $page) {
            $blockValues = $page->allBlocks->pluck('value')->filter()->toArray();
            $images = $this->extractImages($page->featured_image ?? null, $blockValues);

            foreach ($locales as $loc) {
                if ($loc !== $defaultLocale && ! $page->hasTranslationForLocale($loc)) {
                    continue;
                }

                $urls[] = [
                    'loc' => $page->getUrl($loc),
                    'lastmod' => $page->updated_at ? $page->updated_at->toAtomString() : null,
                    'changefreq' => 'weekly',
                    'priority' => ($page->slug === 'home' && $loc === $defaultLocale) ? 1.0 : 0.8,
                    'type' => 'Page',
                    'images' => $images,
                ];
            }
        }

        return $urls;
    }

    public function getPostUrls(): array
    {
        if (! $this->isPostsPluginActive() || ! setting('seo_content_type_posts_index_enabled', true)) {
            return [];
        }

        $postModel = $this->getPostModelClass();
        $locales = available_locales();
        $defaultLocale = setting('default_locale', config('app.locale', 'en'));

        $urls = [];

        // Archive pages for each locale
        foreach ($locales as $loc) {
            $archiveSlug = class_exists(\Plugins\Posts\Models\Setting::class)
                ? \Plugins\Posts\Models\Setting::getArchiveSlug($loc)
                : (string) Setting::get('permalink_post_base', Setting::get('archive_slug', 'blog'));

            $archiveUrl = ($loc !== $defaultLocale && setting('locale_url_structure', 'prefix') === 'prefix')
                ? url('/'.$loc.'/'.$archiveSlug)
                : url('/'.$archiveSlug);

            $urls[] = [
                'loc' => $archiveUrl,
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => 0.8,
                'type' => 'Post Archive',
                'images' => [],
            ];
        }

        $posts = $postModel::where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($posts as $post) {
            /** @var Post $post */
            $images = $this->extractImages($post->featured_image ?? null, $post->content ?? null);

            foreach ($locales as $loc) {
                if ($loc !== $defaultLocale && ! $post->hasTranslationForLocale($loc)) {
                    continue;
                }

                $urls[] = [
                    'loc' => $post->getUrl($loc),
                    'lastmod' => $post->updated_at ? $post->updated_at->toAtomString() : null,
                    'changefreq' => 'weekly',
                    'priority' => 0.6,
                    'type' => 'Post',
                    'images' => $images,
                ];
            }
        }

        return $urls;
    }

    public function getCptUrls(string $cptSlug): array
    {
        $cpt = CustomPostType::where('slug', $cptSlug)->where('is_active', true)->first();
        if (! $cpt) {
            $altSlug = str_contains($cptSlug, '-') ? str_replace('-', '_', $cptSlug) : str_replace('_', '-', $cptSlug);
            $cpt = CustomPostType::where('slug', $altSlug)->where('is_active', true)->first();
        }

        if (! $cpt) {
            return [];
        }

        if (! setting("seo_content_type_{$cpt->slug}_index_enabled", true)) {
            return [];
        }

        $locales = available_locales();
        $defaultLocale = setting('default_locale', config('app.locale', 'en'));

        $urls = [];
        // Archive url (only if has_archive is enabled) for each locale
        if ($cpt->has_archive) {
            foreach ($locales as $loc) {
                $urls[] = [
                    'loc' => $cpt->getArchiveUrl($loc),
                    'lastmod' => now()->toAtomString(),
                    'changefreq' => 'daily',
                    'priority' => 0.8,
                    'type' => $cpt->name.' Archive',
                    'images' => [],
                ];
            }
        }

        // Single entry urls (only if publicly_queryable is enabled) for each locale
        if ($cpt->publicly_queryable) {
            $entries = CptEntry::where('post_type_id', $cpt->id)
                ->where('status', 'published')
                ->orderBy('updated_at', 'desc')
                ->get();

            foreach ($entries as $entry) {
                $images = $this->extractImages($entry->featured_image ?? null, $entry->content ?? null);

                foreach ($locales as $loc) {
                    if ($loc !== $defaultLocale && ! $entry->hasTranslationForLocale($loc)) {
                        continue;
                    }

                    $urls[] = [
                        'loc' => $entry->getUrl($loc),
                        'lastmod' => $entry->updated_at ? $entry->updated_at->toAtomString() : null,
                        'changefreq' => 'weekly',
                        'priority' => 0.6,
                        'type' => $cpt->name,
                        'images' => $images,
                    ];
                }
            }
        }

        return $urls;
    }

    public function getTaxonomyUrls(): array
    {
        $urls = [];
        $locales = available_locales();
        $defaultLocale = setting('default_locale', config('app.locale', 'en'));

        // 1. Custom Taxonomy Terms (from CPT system)
        $terms = TaxonomyTerm::with('taxonomy')->get();
        foreach ($terms as $term) {
            if ($term->taxonomy instanceof CustomTaxonomy) {
                // Respect SEO taxonomy indexing setting
                $taxSlug = $term->taxonomy->slug;
                if (! setting("seo_taxonomy_{$taxSlug}_index_enabled", true)) {
                    continue;
                }

                foreach ($locales as $loc) {
                    $prefix = ($loc !== $defaultLocale && setting('locale_url_structure', 'prefix') === 'prefix') ? '/'.$loc : '';
                    $tSlug = method_exists($term, 'getTranslation') ? ($term->getTranslation('slug', $loc, fallback: true) ?? $term->slug) : $term->slug;
                    $urls[] = [
                        'loc' => url($prefix.'/'.$term->taxonomy->slug.'/'.$tSlug),
                        'lastmod' => $term->updated_at ? $term->updated_at->toAtomString() : null,
                        'changefreq' => 'monthly',
                        'priority' => 0.4,
                        'type' => 'Taxonomy',
                    ];
                }
            }
        }

        // 2. Posts plugin Categories (if plugin is active and indexing enabled)
        if ($this->isPostsPluginActive() && setting('seo_taxonomy_categories_index_enabled', true)) {
            $categoryBase = (string) Setting::get('permalink_category_base', 'category');
            $categories = DB::table('categories')->select('slug', 'updated_at', 'translations')->get();
            foreach ($categories as $category) {
                foreach ($locales as $loc) {
                    $archiveSlug = class_exists(\Plugins\Posts\Models\Setting::class)
                        ? \Plugins\Posts\Models\Setting::getArchiveSlug($loc)
                        : 'blog';
                    $prefix = ($loc !== $defaultLocale && setting('locale_url_structure', 'prefix') === 'prefix') ? '/'.$loc : '';

                    $catSlug = $category->slug;
                    if (! empty($category->translations)) {
                        $trans = is_string($category->translations) ? json_decode($category->translations, true) : (array) $category->translations;
                        if (! empty($trans[$loc]['slug'])) {
                            $catSlug = $trans[$loc]['slug'];
                        }
                    }

                    $urls[] = [
                        'loc' => url($prefix.'/'.$archiveSlug.'/'.$categoryBase.'/'.$catSlug),
                        'lastmod' => $category->updated_at ? Carbon::parse($category->updated_at)->toAtomString() : null,
                        'changefreq' => 'weekly',
                        'priority' => 0.4,
                        'type' => 'Category',
                    ];
                }
            }
        }

        // 3. Posts plugin Tags (if plugin is active and indexing enabled)
        if ($this->isPostsPluginActive() && setting('seo_taxonomy_tags_index_enabled', true)) {
            $tagBase = (string) Setting::get('permalink_tag_base', 'tag');
            $tags = DB::table('tags')->select('slug', 'updated_at', 'translations')->get();
            foreach ($tags as $tag) {
                foreach ($locales as $loc) {
                    $archiveSlug = class_exists(\Plugins\Posts\Models\Setting::class)
                        ? \Plugins\Posts\Models\Setting::getArchiveSlug($loc)
                        : 'blog';
                    $prefix = ($loc !== $defaultLocale && setting('locale_url_structure', 'prefix') === 'prefix') ? '/'.$loc : '';

                    $tSlug = $tag->slug;
                    if (! empty($tag->translations)) {
                        $trans = is_string($tag->translations) ? json_decode($tag->translations, true) : (array) $tag->translations;
                        if (! empty($trans[$loc]['slug'])) {
                            $tSlug = $trans[$loc]['slug'];
                        }
                    }

                    $urls[] = [
                        'loc' => url($prefix.'/'.$archiveSlug.'/'.$tagBase.'/'.$tSlug),
                        'lastmod' => $tag->updated_at ? Carbon::parse($tag->updated_at)->toAtomString() : null,
                        'changefreq' => 'weekly',
                        'priority' => 0.3,
                        'type' => 'Tag',
                    ];
                }
            }
        }

        return $urls;
    }

    public function getAllUrls(): array
    {
        $urls = array_merge(
            $this->getPageUrls(),
            $this->getPostUrls(),
            $this->getTaxonomyUrls()
        );

        $cpts = CustomPostType::where('is_active', true)->get();
        foreach ($cpts as $cpt) {
            $urls = array_merge($urls, $this->getCptUrls($cpt->slug));
        }

        // Allow plugin injection
        $event = new BuildSitemap;
        event($event);
        foreach ($event->getUrls() as $entry) {
            $urls[] = $entry;
        }

        return $urls;
    }

    /**
     * Check if the Posts plugin is installed and active.
     */
    protected function isPostsPluginActive(): bool
    {
        if (! class_exists('Plugins\\Posts\\Models\\Post')) {
            return false;
        }

        try {
            return Schema::hasTable('posts');
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Get the Post model class string.
     *
     * @return class-string<Post>
     */
    protected function getPostModelClass(): string
    {
        return Post::class;
    }

    /**
     * Extract unique absolute image URLs from featured_image field, blocks, or content.
     */
    protected function extractImages(?string $featuredImage = null, mixed $content = null): array
    {
        $images = [];

        if (! empty($featuredImage)) {
            if ($formatted = $this->formatImageUrl($featuredImage)) {
                $images[] = $formatted;
            }
        }

        if (is_string($content) && ! empty($content)) {
            $this->extractImagesFromHtml($content, $images);
        } elseif (is_iterable($content)) {
            foreach ($content as $item) {
                if (is_string($item)) {
                    $this->extractImagesFromHtml($item, $images);
                } elseif (is_object($item) || is_array($item)) {
                    $this->extractImagesFromHtml(json_encode($item), $images);
                }
            }
        }

        return array_values(array_unique(array_filter($images)));
    }

    protected function extractImagesFromHtml(string $html, array &$images): void
    {
        if (preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $matches)) {
            foreach ($matches[1] as $src) {
                if ($formatted = $this->formatImageUrl($src)) {
                    $images[] = $formatted;
                }
            }
        }

        if (preg_match_all('/https?:\/\/[^\s"\']+\.(?:png|jpg|jpeg|gif|webp|svg)/i', $html, $urlMatches)) {
            foreach ($urlMatches[0] as $url) {
                if ($formatted = $this->formatImageUrl($url)) {
                    $images[] = $formatted;
                }
            }
        }
    }

    protected function formatImageUrl(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        if (str_starts_with($url, 'data:')) {
            return null;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        if (str_starts_with($url, '/')) {
            return url($url);
        }

        return url('/'.$url);
    }
}
