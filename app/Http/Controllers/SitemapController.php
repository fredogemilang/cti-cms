<?php

namespace App\Http\Controllers;

use App\Events\BuildSitemap;
use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\CustomTaxonomy;
use App\Models\Page;
use App\Models\TaxonomyTerm;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SitemapController extends Controller
{
    public function index(): Response
    {
        abort_unless(setting('seo_sitemap_enabled', true), 404);

        $xml = Cache::remember('sitemap.xml', now()->addHour(), function () {
            $urls = [];

            // Pages
            foreach (Page::where('status', 'published')->orderBy('updated_at', 'desc')->get() as $page) {
                $alternates = $this->buildAlternates($page);
                $urls[] = [
                    'loc' => $page->slug === 'home' ? url('/') : url('/'.$page->slug),
                    'lastmod' => $page->updated_at,
                    'changefreq' => 'weekly',
                    'priority' => $page->slug === 'home' ? 1.0 : 0.8,
                    'alternates' => $alternates,
                ];
            }

            // CPT Archive & Entries
            $this->addCptUrls($urls);

            // Let plugins inject their URLs
            $event = new BuildSitemap;
            event($event);
            foreach ($event->getUrls() as $entry) {
                $urls[] = $entry;
            }

            return $this->renderXml($urls);
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    protected function buildAlternates(Page $page): array
    {
        if (count($page->translatedLocales()) < 2) {
            return [];
        }

        $alts = [];
        foreach ($page->translatedLocales() as $locale) {
            $slug = $page->getTranslation('slug', $locale, false);
            if (! $slug) {
                continue;
            }
            $alts[] = ['hreflang' => $locale, 'href' => url('/'.ltrim($slug, '/'))];
        }
        // x-default
        $defaultSlug = $page->getTranslation('slug', Page::defaultLocale(), false);
        if ($defaultSlug) {
            $alts[] = ['hreflang' => 'x-default', 'href' => url('/'.ltrim($defaultSlug, '/'))];
        }

        return $alts;
    }

    protected function renderXml(array $urls): string
    {
        $hasAlternates = collect($urls)->contains(fn ($u) => ! empty($u['alternates']));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        if ($hasAlternates) {
            $xml .= ' xmlns:xhtml="http://www.w3.org/1999/xhtml"';
        }
        $xml .= ">\n";

        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.htmlspecialchars($u['loc'], ENT_XML1, 'UTF-8')."</loc>\n";

            if (! empty($u['lastmod'])) {
                $mod = $u['lastmod'] instanceof \DateTimeInterface
                    ? $u['lastmod']->toAtomString()
                    : (string) $u['lastmod'];
                $xml .= "    <lastmod>{$mod}</lastmod>\n";
            }
            if (! empty($u['changefreq'])) {
                $xml .= "    <changefreq>{$u['changefreq']}</changefreq>\n";
            }
            if (isset($u['priority'])) {
                $xml .= '    <priority>'.number_format((float) $u['priority'], 1, '.', '')."</priority>\n";
            }
            foreach (($u['alternates'] ?? []) as $alt) {
                $xml .= '    <xhtml:link rel="alternate" hreflang="'.htmlspecialchars($alt['hreflang'], ENT_XML1).'" href="'.htmlspecialchars($alt['href'], ENT_XML1)."\"/>\n";
            }

            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>'."\n";

        return $xml;
    }

    /**
     * Add CPT archive pages, entries, and taxonomy term archives to the sitemap.
     */
    protected function addCptUrls(array &$urls): void
    {
        if (! Schema::hasTable('custom_post_types')) {
            return;
        }

        $cpts = CustomPostType::withArchive()->get();

        foreach ($cpts as $cpt) {
            // CPT Archive page
            $urls[] = [
                'loc' => $cpt->getArchiveUrl(),
                'lastmod' => $cpt->updated_at,
                'changefreq' => 'daily',
                'priority' => 0.7,
            ];

            // Individual entries
            $entries = CptEntry::where('post_type_id', $cpt->id)
                ->published()
                ->orderByDesc('published_at')
                ->get();

            foreach ($entries as $entry) {
                $urls[] = [
                    'loc' => $entry->getUrl(),
                    'lastmod' => $entry->updated_at,
                    'changefreq' => 'weekly',
                    'priority' => 0.6,
                ];
            }
        }

        // Taxonomy term archives
        if (! Schema::hasTable('custom_taxonomies') || ! Schema::hasTable('taxonomy_terms')) {
            return;
        }

        $taxonomies = CustomTaxonomy::active()->get();

        foreach ($taxonomies as $taxonomy) {
            $terms = TaxonomyTerm::where('taxonomy_id', $taxonomy->id)->get();
            foreach ($terms as $term) {
                $urls[] = [
                    'loc' => $term->getUrl(),
                    'lastmod' => $term->updated_at,
                    'changefreq' => 'weekly',
                    'priority' => 0.5,
                ];
            }
        }
    }
}
