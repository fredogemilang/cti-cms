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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SitemapBuilder
{
    public function getIndexSitemaps(): array
    {
        $sitemaps = [];

        // 1. Pages sitemap
        if (setting('seo_content_type_pages_index_enabled', true)) {
            $lastPageMod = Page::where('status', 'published')->max('updated_at');
            $sitemaps[] = [
                'loc' => url('/page-sitemap.xml'),
                'lastmod' => $lastPageMod ? Carbon::parse($lastPageMod)->toAtomString() : now()->toAtomString(),
                'type' => 'Pages',
            ];
        }

        // 2. Posts plugin sitemap (if plugin is active and indexing enabled)
        if ($this->isPostsPluginActive() && setting('seo_content_type_posts_index_enabled', true)) {
            $postModel = $this->getPostModelClass();
            $lastPostMod = $postModel::where('status', 'published')->max('updated_at');
            $sitemaps[] = [
                'loc' => url('/post-sitemap.xml'),
                'lastmod' => $lastPostMod ? Carbon::parse($lastPostMod)->toAtomString() : now()->toAtomString(),
                'type' => 'Posts',
            ];
        }

        // 3. Custom Post Types sitemaps (respect index_enabled)
        $cpts = CustomPostType::where('is_active', true)->get();
        foreach ($cpts as $cpt) {
            if (! setting("seo_content_type_{$cpt->slug}_index_enabled", true)) {
                continue;
            }
            $lastCptMod = CptEntry::where('post_type_id', $cpt->id)->where('status', 'published')->max('updated_at');
            $sitemaps[] = [
                'loc' => url("/{$cpt->slug}-sitemap.xml"),
                'lastmod' => $lastCptMod ? Carbon::parse($lastCptMod)->toAtomString() : now()->toAtomString(),
                'type' => $cpt->name,
            ];
        }

        // 4. Taxonomies sitemap
        $lastTaxMod = TaxonomyTerm::max('updated_at');
        if ($lastTaxMod) {
            $sitemaps[] = [
                'loc' => url('/taxonomy-sitemap.xml'),
                'lastmod' => Carbon::parse($lastTaxMod)->toAtomString(),
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

        foreach (Page::where('status', 'published')->orderBy('updated_at', 'desc')->get() as $page) {
            $urls[] = [
                'loc' => $page->slug === 'home' ? url('/') : url('/'.$page->slug),
                'lastmod' => $page->updated_at ? $page->updated_at->toAtomString() : null,
                'changefreq' => 'weekly',
                'priority' => $page->slug === 'home' ? 1.0 : 0.8,
                'type' => 'Page',
            ];
        }

        return $urls;
    }

    public function getPostUrls(): array
    {
        if (! $this->isPostsPluginActive() || ! setting('seo_content_type_posts_index_enabled', true)) {
            return [];
        }

        $postModel = $this->getPostModelClass();
        $archiveSlug = (string) Setting::get('permalink_post_base', Setting::get('archive_slug', 'blog'));

        $urls = [];

        // Archive page
        $urls[] = [
            'loc' => url('/'.$archiveSlug),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => 0.8,
            'type' => 'Post Archive',
        ];

        $posts = $postModel::where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($posts as $post) {
            $urls[] = [
                'loc' => url('/'.$archiveSlug.'/'.$post->slug),
                'lastmod' => $post->updated_at ? $post->updated_at->toAtomString() : null,
                'changefreq' => 'weekly',
                'priority' => 0.6,
                'type' => 'Post',
            ];
        }

        return $urls;
    }

    public function getCptUrls(string $cptSlug): array
    {
        $cpt = CustomPostType::where('slug', $cptSlug)->where('is_active', true)->first();
        if (! $cpt) {
            return [];
        }

        if (! setting("seo_content_type_{$cpt->slug}_index_enabled", true)) {
            return [];
        }

        $urls = [];
        // Archive url
        $urls[] = [
            'loc' => url('/'.$cpt->slug),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => 0.8,
            'type' => $cpt->name.' Archive',
        ];

        $entries = CptEntry::where('post_type_id', $cpt->id)
            ->where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($entries as $entry) {
            $urls[] = [
                'loc' => url('/'.$cpt->slug.'/'.$entry->slug),
                'lastmod' => $entry->updated_at ? $entry->updated_at->toAtomString() : null,
                'changefreq' => 'weekly',
                'priority' => 0.6,
                'type' => $cpt->name,
            ];
        }

        return $urls;
    }

    public function getTaxonomyUrls(): array
    {
        $urls = [];

        // 1. Custom Taxonomy Terms (from CPT system)
        $terms = TaxonomyTerm::with('taxonomy')->get();
        foreach ($terms as $term) {
            if ($term->taxonomy instanceof CustomTaxonomy) {
                // Respect SEO taxonomy indexing setting
                $taxSlug = $term->taxonomy->slug;
                if (! setting("seo_taxonomy_{$taxSlug}_index_enabled", true)) {
                    continue;
                }

                $urls[] = [
                    'loc' => url('/'.$term->taxonomy->slug.'/'.$term->slug),
                    'lastmod' => $term->updated_at ? $term->updated_at->toAtomString() : null,
                    'changefreq' => 'monthly',
                    'priority' => 0.4,
                    'type' => 'Taxonomy',
                ];
            }
        }

        // 2. Posts plugin Categories (if plugin is active and indexing enabled)
        if ($this->isPostsPluginActive() && setting('seo_taxonomy_categories_index_enabled', true)) {
            $archiveSlug = (string) Setting::get('permalink_post_base', Setting::get('archive_slug', 'blog'));
            $categoryBase = (string) Setting::get('permalink_category_base', 'category');
            $categories = DB::table('categories')->select('slug', 'updated_at')->get();
            foreach ($categories as $category) {
                $urls[] = [
                    'loc' => url('/'.$archiveSlug.'/'.$categoryBase.'/'.$category->slug),
                    'lastmod' => $category->updated_at ? Carbon::parse($category->updated_at)->toAtomString() : null,
                    'changefreq' => 'weekly',
                    'priority' => 0.4,
                    'type' => 'Category',
                ];
            }
        }

        // 3. Posts plugin Tags (if plugin is active and indexing enabled)
        if ($this->isPostsPluginActive() && setting('seo_taxonomy_tags_index_enabled', true)) {
            $archiveSlug ??= (string) Setting::get('permalink_post_base', Setting::get('archive_slug', 'blog'));
            $tagBase = (string) Setting::get('permalink_tag_base', 'tag');
            $tags = DB::table('tags')->select('slug', 'updated_at')->get();
            foreach ($tags as $tag) {
                $urls[] = [
                    'loc' => url('/'.$archiveSlug.'/'.$tagBase.'/'.$tag->slug),
                    'lastmod' => $tag->updated_at ? Carbon::parse($tag->updated_at)->toAtomString() : null,
                    'changefreq' => 'weekly',
                    'priority' => 0.3,
                    'type' => 'Tag',
                ];
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
     * @return class-string
     */
    protected function getPostModelClass(): string
    {
        return 'Plugins\\Posts\\Models\\Post';
    }
}
