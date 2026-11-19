<?php

namespace App\Services\Schema\Providers;

use App\Contracts\SchemaProviderInterface;

class OrganizationSchemaProvider implements SchemaProviderInterface
{
    public function getIdentifier(): string
    {
        return 'organization';
    }

    public function getLabel(): string
    {
        return 'Organization & Site Core Knowledge';
    }

    public function getNodes(?string $since = null, int $page = 1, int $perPage = 50): array
    {
        $baseUrl = config('app.url', 'http://localhost');
        $siteName = (string) setting('site_title', config('app.name', 'CTI CMS'));
        $siteDescription = (string) setting('site_description', '');

        $orgId = $baseUrl.'/#organization';
        $websiteId = $baseUrl.'/#website';

        // 1. Organization Node
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

        // 3. Publishing Principles Node
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

        return [$organization, $webSite, $publishingPrinciples];
    }

    public function getTotalCount(?string $since = null): int
    {
        return 3;
    }

    public function getMetadata(): array
    {
        return [
            'singleton' => true,
            'version' => '1.0',
        ];
    }
}
