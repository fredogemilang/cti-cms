<?php

namespace App\Services;

use App\Models\Page;
use App\Models\SeoMeta;
use Illuminate\Database\Eloquent\Model;

class SchemaBuilder
{
    public function build(Model $entity, ?SeoMeta $meta = null): ?array
    {
        $type = $meta?->schema_type ?? $this->guessType($entity);
        if (! $type) {
            return null;
        }

        $base = [
            '@context' => 'https://schema.org',
            '@type' => $type,
        ];

        $custom = $meta?->schema_data ?? [];

        $derived = match ($type) {
            'Article', 'BlogPosting', 'NewsArticle' => $this->article($entity, $meta),
            'Event' => $this->event($entity, $meta),
            'WebPage' => $this->webPage($entity, $meta),
            'Organization' => $this->organization($meta),
            'FAQPage' => $this->faqPage($custom),
            default => [],
        };

        return array_merge($base, $derived, $custom);
    }

    public function breadcrumbList(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(fn ($item, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['name'],
                'item' => $item['url'] ?? null,
            ])->all(),
        ];
    }

    public function organization(?SeoMeta $meta = null): array
    {
        $siteName = (string) setting('site_name', config('app.name'));
        $altName = (string) setting('seo_site_alternate_name', '');

        $orgType = (string) setting('seo_org_type', 'Organization');
        $orgName = (string) setting('seo_org_name', '') ?: $siteName;
        $orgAltName = (string) setting('seo_org_alternate_name', '') ?: $altName;

        $logo = setting('seo_org_logo') ? url((string) setting('seo_org_logo')) : (setting('site_logo') ? url((string) setting('site_logo')) : (setting('seo_default_og_image') ? url((string) setting('seo_default_og_image')) : null));

        $schema = [
            '@type' => $orgType,
            'name' => $orgName,
            'url' => url('/'),
            'logo' => $logo,
            'description' => setting('seo_org_description') ?: null,
            'email' => setting('seo_org_email') ?: null,
            'telephone' => setting('seo_org_phone') ?: null,
            'legalName' => setting('seo_org_legal_name') ?: null,
            'sameAs' => array_values(array_filter([
                setting('seo_facebook_url') ?: null,
                ($tw = setting('seo_twitter_handle')) ? (str_starts_with((string) $tw, 'http') ? $tw : "https://x.com/{$tw}") : null,
                setting('seo_linkedin_url') ?: null,
                setting('seo_instagram_url') ?: null,
                setting('seo_youtube_url') ?: null,
                setting('seo_wikipedia_url') ?: null,
            ])),
        ];

        if ($orgAltName !== '') {
            $schema['alternateName'] = $orgAltName;
        }

        if ($pub = $this->getPageUrl((int) setting('seo_policy_publishing_principles'))) {
            $schema['publishingPrinciples'] = $pub;
        }
        if ($own = $this->getPageUrl((int) setting('seo_policy_ownership_funding'))) {
            $schema['ownershipFundingInfo'] = $own;
        }
        if ($corr = $this->getPageUrl((int) setting('seo_policy_corrections'))) {
            $schema['correctionsPolicy'] = $corr;
        }
        if ($eth = $this->getPageUrl((int) setting('seo_policy_ethics'))) {
            $schema['ethicsPolicy'] = $eth;
        }
        if ($div = $this->getPageUrl((int) setting('seo_policy_diversity'))) {
            $schema['diversityPolicy'] = $div;
        }

        return array_filter($schema, fn ($v) => $v !== null && $v !== '' && $v !== []);
    }

    protected function getPageUrl(int $pageId): ?string
    {
        if ($pageId <= 0) {
            return null;
        }

        $page = Page::find($pageId);

        return $page ? $page->getUrl() : null;
    }

    protected function article(Model $entity, ?SeoMeta $meta): array
    {
        $schema = array_filter([
            'headline' => $meta?->title ?: ($entity->title ?? null),
            'description' => $meta?->description ?? ($entity->excerpt ?? null),
            'datePublished' => optional($entity->published_at ?? $entity->created_at)?->toAtomString(),
            'dateModified' => optional($entity->updated_at)?->toAtomString(),
            'author' => $this->buildAuthorSchema($entity),
            'image' => $this->imageUrl($entity, $meta),
        ]);

        // GEO: Abstract — AI-friendly summary for citation
        if ($meta?->ai_summary) {
            $schema['abstract'] = $meta->ai_summary;
        }

        // GEO: Speakable — mark headline + description as speakable for voice AI
        $schema['speakable'] = [
            '@type' => 'SpeakableSpecification',
            'cssSelector' => ['h1', '.entry-content', '[data-speakable]'],
        ];

        return $schema;
    }

    /**
     * Build enriched author schema with E-E-A-T signals.
     * Falls back to simple Person if author has no extended profile.
     */
    protected function buildAuthorSchema(Model $entity): ?array
    {
        $author = $entity->author ?? null;
        if (! $author?->name) {
            return null;
        }

        $schema = [
            '@type' => 'Person',
            'name' => $author->name,
        ];

        // E-E-A-T enrichment (when User model has these fields)
        if (! empty($author->job_title)) {
            $schema['jobTitle'] = $author->job_title;
        }
        if (! empty($author->bio)) {
            $schema['description'] = $author->bio;
        }
        if (! empty($author->website_url)) {
            $schema['url'] = $author->website_url;
            $schema['sameAs'] = [$author->website_url];
        }

        return $schema;
    }

    protected function event(Model $entity, ?SeoMeta $meta): array
    {
        return array_filter([
            'name' => $meta?->title ?: ($entity->title ?? null),
            'description' => $meta?->description ?? ($entity->description ?? null),
            'startDate' => optional($entity->start_date ?? null)?->toAtomString(),
            'endDate' => optional($entity->end_date ?? null)?->toAtomString(),
            'location' => $entity->location ?? null,
            'image' => $this->imageUrl($entity, $meta),
        ]);
    }

    protected function webPage(Model $entity, ?SeoMeta $meta): array
    {
        $schema = array_filter([
            'name' => $meta?->title ?: ($entity->title ?? null),
            'description' => $meta?->description ?? null,
            'url' => method_exists($entity, 'getUrl') ? $entity->getUrl() : null,
        ]);

        // GEO: Abstract — AI-friendly summary for citation
        if ($meta?->ai_summary) {
            $schema['abstract'] = $meta->ai_summary;
        }

        return $schema;
    }

    protected function faqPage(array $custom): array
    {
        return [
            'mainEntity' => collect($custom['questions'] ?? [])->map(fn ($q) => [
                '@type' => 'Question',
                'name' => $q['q'] ?? '',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $q['a'] ?? '',
                ],
            ])->all(),
        ];
    }

    protected function imageUrl(Model $entity, ?SeoMeta $meta): ?string
    {
        if ($meta?->og_image_id && $meta->ogImage) {
            return url($meta->ogImage->path ?? '');
        }
        if (! empty($entity->featured_image)) {
            return url($entity->featured_image);
        }

        return null;
    }

    protected function guessType(Model $entity): ?string
    {
        $class = class_basename($entity);

        return match ($class) {
            'Post' => 'BlogPosting',
            'Event' => 'Event',
            'Page' => 'WebPage',
            default => 'WebPage',
        };
    }
}
