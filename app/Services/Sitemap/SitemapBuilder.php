<?php

namespace App\Services\Sitemap;

use App\Events\BuildSitemap;
use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\CustomTaxonomy;
use App\Models\Page;
use App\Models\TaxonomyTerm;
use Illuminate\Support\Carbon;

class SitemapBuilder
{
    public function getIndexSitemaps(): array
    {
        $baseUrl = config('app.url', 'http://localhost');
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

        // 2. Custom Post Types sitemaps
        $cpts = CustomPostType::where('is_active', true)->get();
        foreach ($cpts as $cpt) {
            $lastCptMod = CptEntry::where('post_type_id', $cpt->id)->where('status', 'published')->max('updated_at');
            $sitemaps[] = [
                'loc' => url("/{$cpt->slug}-sitemap.xml"),
                'lastmod' => $lastCptMod ? Carbon::parse($lastCptMod)->toAtomString() : now()->toAtomString(),
                'type' => $cpt->name,
            ];
        }

        // 3. Taxonomies sitemap
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

    public function getCptUrls(string $cptSlug): array
    {
        $cpt = CustomPostType::where('slug', $cptSlug)->where('is_active', true)->first();
        if (! $cpt) {
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
        $terms = TaxonomyTerm::with('taxonomy')->get();
        foreach ($terms as $term) {
            if ($term->taxonomy instanceof CustomTaxonomy) {
                $urls[] = [
                    'loc' => url('/'.$term->taxonomy->slug.'/'.$term->slug),
                    'lastmod' => $term->updated_at ? $term->updated_at->toAtomString() : null,
                    'changefreq' => 'monthly',
                    'priority' => 0.4,
                    'type' => 'Taxonomy',
                ];
            }
        }

        return $urls;
    }

    public function getAllUrls(): array
    {
        $urls = array_merge(
            $this->getPageUrls(),
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
}
