<?php

namespace App\Services;

use App\Models\SeoMeta;
use Illuminate\Database\Eloquent\Model;

class SeoRenderer
{
    public function __construct(protected SchemaBuilder $schemaBuilder) {}

    /**
     * Resolve final SEO data for an entity (merged with site defaults).
     * Supports multi-locale: tries current locale first, then default (''), then legacy JSON.
     *
     * @return array{title:string,description:?string,canonical:?string,robots:string,og:array,twitter:array,schema:?array}
     */
    public function resolve(?Model $entity, array $overrides = []): array
    {
        $meta = $entity ? $this->resolveSeoMeta($entity) : null;

        $siteName = setting('site_name', config('app.name'));
        $tagline = setting('site_tagline', '');
        $titleTemplate = setting('seo_title_pattern', '{page} | {site}');

        $rawTitle = $overrides['title']
            ?? $meta?->title
            ?? ($entity->title ?? $siteName);

        $title = strtr($titleTemplate, [
            '{page}' => $rawTitle,
            '{site}' => $siteName,
            '{tagline}' => $tagline,
        ]);

        // Description fallback chain: seo_meta → entity excerpt → auto-snippet from content → site default
        $description = $overrides['description']
            ?? $meta?->description
            ?? $this->autoDescription($entity)
            ?? setting('seo_default_description');

        $canonical = $overrides['canonical']
            ?? $meta?->canonical_url
            ?? ($entity && method_exists($entity, 'getUrl') ? $entity->getUrl() : request()->fullUrl());

        $robots = $meta?->robots ?? 'index,follow';
        if (! setting('seo_allow_indexing', true)) {
            $robots = 'noindex,nofollow';
        }

        // OG Image fallback: seo_meta og_image → entity featured_image → site default
        $featuredImage = $entity && isset($entity->featured_image) ? (string) $entity->featured_image : null;
        $metaOgPath = $meta?->ogImage && isset($meta->ogImage->path) ? (string) $meta->ogImage->path : null;
        $ogImage = $metaOgPath
            ?? $featuredImage
            ?? setting('seo_default_og_image');

        $og = [
            'title' => $meta?->og_title ?: $title,
            'description' => $meta?->og_description ?: $description,
            'image' => $ogImage ? url($ogImage) : null,
            'type' => $this->ogType($entity),
            'url' => $canonical,
            'site_name' => $siteName,
        ];

        $twitter = [
            'card' => $meta?->twitter_card ?? 'summary_large_image',
            'title' => $og['title'],
            'description' => $og['description'],
            'image' => $og['image'],
        ];

        $schema = $entity ? $this->schemaBuilder->build($entity, $meta) : null;

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'robots' => $robots,
            'og' => $og,
            'twitter' => $twitter,
            'schema' => $schema,
        ];
    }

    /**
     * Resolve the best SeoMeta record for the current locale.
     * Priority: 1) current locale row, 2) default ('') row, 3) legacy JSON fallback.
     */
    protected function resolveSeoMeta(Model $entity): ?SeoMeta
    {
        if (! method_exists($entity, 'seoMeta')) {
            return null;
        }

        $currentLocale = app()->getLocale();
        $seoableType = get_class($entity);
        $seoableId = $entity->getKey();

        // Try current locale first
        $meta = SeoMeta::where('seoable_type', $seoableType)
            ->where('seoable_id', $seoableId)
            ->where('locale', $currentLocale)
            ->first();

        // Fallback to default locale
        if (! $meta) {
            $meta = SeoMeta::where('seoable_type', $seoableType)
                ->where('seoable_id', $seoableId)
                ->where('locale', '')
                ->first();
        }

        // Fallback to legacy JSON seo column (backward compat — pages/cpt_entries)
        if (! $meta && isset($entity->seo) && is_array($entity->seo) && ! empty($entity->seo)) {
            $seo = $entity->seo;
            $meta = new SeoMeta([
                'title' => $seo['meta_title'] ?? null,
                'description' => $seo['meta_description'] ?? null,
                'og_title' => $seo['og_title'] ?? null,
                'og_description' => $seo['og_description'] ?? null,
            ]);
        }

        // Fallback to legacy JSON meta column (backward compat — posts plugin)
        if (! $meta && isset($entity->meta) && is_array($entity->meta) && ! empty($entity->meta)) {
            $m = $entity->meta;
            $meta = new SeoMeta([
                'title' => $m['meta_title'] ?? null,
                'description' => $m['meta_description'] ?? null,
                'og_title' => $m['og_title'] ?? null,
                'og_description' => $m['og_description'] ?? null,
            ]);
        }

        return $meta;
    }

    /**
     * Auto-generate a meta description from entity excerpt or content.
     * Returns null if no suitable text is found.
     */
    protected function autoDescription(?Model $entity): ?string
    {
        if (! $entity) {
            return null;
        }

        // Try excerpt first
        if (! empty($entity->excerpt)) {
            $text = strip_tags((string) $entity->excerpt);
            $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
            $text = preg_replace('/\s+/', ' ', trim($text));

            return mb_strlen($text) > 160 ? mb_substr($text, 0, 157).'...' : $text;
        }

        // Fallback to content snippet
        $content = $entity->content ?? null;
        if (! empty($content)) {
            $text = strip_tags((string) $content);
            $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
            $text = preg_replace('/\s+/', ' ', trim($text));

            if (mb_strlen($text) > 0) {
                return mb_strlen($text) > 160 ? mb_substr($text, 0, 157).'...' : $text;
            }
        }

        return null;
    }

    protected function ogType(?Model $entity): string
    {
        if (! $entity) {
            return 'website';
        }
        $class = class_basename($entity);

        return match ($class) {
            'Post' => 'article',
            'Event' => 'event',
            default => 'website',
        };
    }
}
