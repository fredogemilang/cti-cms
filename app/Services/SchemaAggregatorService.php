<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Support\Facades\Cache;

class SchemaAggregatorService
{
    public const CACHE_KEY = 'schema_aggregator_graph';

    public function getAggregatedGraph(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addDays(1), function () {
            return $this->buildGraph();
        });
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function buildGraph(): array
    {
        $baseUrl = config('app.url', 'http://localhost');
        $siteName = (string) setting('site_title', config('app.name', 'CTI CMS'));
        $siteDescription = (string) setting('site_description', '');

        $orgId = $baseUrl.'/#organization';
        $websiteId = $baseUrl.'/#website';

        // 1. Organization / Publisher Node
        $organization = [
            '@type' => 'Organization',
            '@id' => $orgId,
            'name' => (string) setting('seo_organization_name', $siteName),
            'url' => $baseUrl,
            'logo' => [
                '@type' => 'ImageObject',
                '@id' => $baseUrl.'/#logo',
                'url' => (string) setting('seo_organization_logo', ''),
            ],
            'description' => (string) setting('seo_organization_description', $siteDescription),
        ];

        $socials = setting('seo_organization_social_profiles', []);
        if (is_array($socials) && ! empty($socials)) {
            $organization['sameAs'] = array_values(array_filter(array_map('trim', $socials)));
        }

        // 2. WebSite Node
        $webSite = [
            '@type' => 'WebSite',
            '@id' => $websiteId,
            'url' => $baseUrl,
            'name' => $siteName,
            'description' => $siteDescription,
            'publisher' => [
                '@id' => $orgId,
            ],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => $baseUrl.'/?s={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];

        // 3. E-E-A-T & Publishing Principles Node
        $publishingPrinciples = [
            '@type' => 'PublishingPrinciples',
            '@id' => $baseUrl.'/#publishing-principles',
            'name' => 'Publishing Principles & Editorial Policy',
            'url' => $baseUrl.'/editorial-policy',
            'publishingPrinciples' => (string) setting('seo_publishing_principles', ''),
            'correctionsPolicy' => (string) setting('seo_corrections_policy', ''),
            'diversityPolicy' => (string) setting('seo_diversity_policy', ''),
            'ethicsPolicy' => (string) setting('seo_ethics_policy', ''),
        ];

        // 4. Published Entities (Pages & Posts) Nodes
        $pages = Page::query()
            ->where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->limit(100)
            ->get();

        $pageNodes = [];
        foreach ($pages as $page) {
            $pageUrl = url('/'.$page->slug);
            $pageNodes[] = [
                '@type' => 'WebPage',
                '@id' => $pageUrl.'/#webpage',
                'url' => $pageUrl,
                'name' => $page->title,
                'description' => $page->getMetaDescription() ?: $page->title,
                'datePublished' => $page->created_at ? $page->created_at->toIso8601String() : null,
                'dateModified' => $page->updated_at ? $page->updated_at->toIso8601String() : null,
                'isPartOf' => [
                    '@id' => $websiteId,
                ],
                'publisher' => [
                    '@id' => $orgId,
                ],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => array_merge(
                [$organization, $webSite, $publishingPrinciples],
                $pageNodes
            ),
        ];
    }
}
