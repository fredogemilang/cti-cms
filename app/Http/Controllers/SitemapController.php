<?php

namespace App\Http\Controllers;

use App\Services\Sitemap\SitemapBuilder;
use App\Services\Sitemap\SitemapRenderer;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(SitemapBuilder $builder, SitemapRenderer $renderer): Response
    {
        abort_unless(setting('seo_sitemap_enabled', true), 404);

        $xml = Cache::remember('sitemap.xml_index_v2', now()->addHour(), function () use ($builder, $renderer) {
            $indexSitemaps = $builder->getIndexSitemaps();

            return $renderer->renderIndex($indexSitemaps);
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    public function showType(string $type, SitemapBuilder $builder, SitemapRenderer $renderer): Response
    {
        abort_unless(setting('seo_sitemap_enabled', true), 404);

        $cleanType = str_replace('-sitemap.xml', '', $type);
        $cleanType = str_replace('.xml', '', $cleanType);

        $cacheKey = "sitemap_type_{$cleanType}_v2";

        $xml = Cache::remember($cacheKey, now()->addHour(), function () use ($cleanType, $builder, $renderer) {
            $urls = match ($cleanType) {
                'page', 'pages' => $builder->getPageUrls(),
                'post', 'posts' => $builder->getPostUrls(),
                'taxonomy', 'taxonomies' => $builder->getTaxonomyUrls(),
                default => $builder->getCptUrls($cleanType),
            };

            if (empty($urls) && $cleanType === 'all') {
                $urls = $builder->getAllUrls();
            }

            return $renderer->renderUrlSet($urls);
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'X-Robots-Tag' => 'noindex',
        ]);
    }
}
