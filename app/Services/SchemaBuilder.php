<?php

namespace App\Services;

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
        return array_filter([
            'name' => setting('site_name', config('app.name')),
            'url' => url('/'),
            'logo' => setting('site_logo') ? url(setting('site_logo')) : null,
        ]);
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
