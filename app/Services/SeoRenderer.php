<?php

namespace App\Services;

use App\Models\CustomTaxonomy;
use App\Models\Media;
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

        $siteName = (string) setting('site_name', config('app.name'));
        $tagline = (string) setting('site_tagline', '');
        $titleSeparator = (string) setting('seo_title_separator', '-');

        $entityClass = $entity ? class_basename($entity) : '';
        $ctSlug = $this->getContentTypeSlug($entity);
        $taxSlug = $this->getTaxonomySlug($entity);

        // Title pattern resolution priority: per-content-type setting → legacy post/page pattern → global pattern
        $defaultPattern = null;
        if ($ctSlug && setting("seo_content_type_{$ctSlug}_title_pattern")) {
            $defaultPattern = (string) setting("seo_content_type_{$ctSlug}_title_pattern");
        } elseif ($taxSlug && setting("seo_taxonomy_{$taxSlug}_title_pattern")) {
            $defaultPattern = (string) setting("seo_taxonomy_{$taxSlug}_title_pattern");
        }

        if (! $defaultPattern) {
            $defaultPattern = match ($entityClass) {
                'Post' => setting('seo_post_title_pattern', '{title} {sep} {site}'),
                'Page' => setting('seo_page_title_pattern', '{title} {sep} {site}'),
                default => setting('seo_title_pattern', '{page} {sep} {site}'),
            };
        }

        $titleTemplate = (string) $defaultPattern;

        $metaTitle = ($meta && ! empty($meta->title) && trim((string) $meta->title) !== '') ? $meta->title : null;
        $entityTitle = null;
        if ($entity) {
            if (method_exists($entity, 'getTranslation')) {
                $trans = $entity->getTranslation('title');
                if (! empty($trans) && trim((string) $trans) !== '') {
                    $entityTitle = $trans;
                }
            }
            if (! $entityTitle && ! empty($entity->title) && trim((string) $entity->title) !== '') {
                $entityTitle = $entity->title;
            } elseif (! $entityTitle && ! empty($entity->name) && trim((string) $entity->name) !== '') {
                $entityTitle = $entity->name;
            }
        }

        $rawTitle = $overrides['title'] ?? $metaTitle ?? $entityTitle ?? $siteName;

        $termName = $entity->name ?? $rawTitle;
        $termDescription = $entity->description ?? '';

        if (strtolower(trim((string) $rawTitle)) === strtolower(trim((string) $siteName))) {
            $title = $siteName.($tagline ? " {$titleSeparator} {$tagline}" : '');
        } else {
            $title = strtr($titleTemplate, [
                '{page}' => $rawTitle,
                '{title}' => $rawTitle,
                '{term}' => $termName,
                '{site}' => $siteName,
                '{tagline}' => $tagline,
                '{sep}' => $titleSeparator,
                '{description}' => $termDescription,
            ]);
        }

        // Description fallback chain: override → seo_meta → auto-snippet → content-type pattern → site default
        $typePatternDesc = null;
        if ($ctSlug && setting("seo_content_type_{$ctSlug}_description_pattern")) {
            $typePatternDesc = (string) setting("seo_content_type_{$ctSlug}_description_pattern");
        } elseif ($taxSlug && setting("seo_taxonomy_{$taxSlug}_description_pattern")) {
            $typePatternDesc = (string) setting("seo_taxonomy_{$taxSlug}_description_pattern");
        }

        $metaDesc = ($meta && ! empty($meta->description) && trim((string) $meta->description) !== '') ? $meta->description : null;
        $description = $overrides['description']
            ?? $metaDesc
            ?? $this->autoDescription($entity)
            ?? $typePatternDesc
            ?? setting('seo_default_description');

        $canonical = $overrides['canonical']
            ?? $meta?->canonical_url
            ?? ($entity && method_exists($entity, 'getUrl') ? $entity->getUrl() : request()->fullUrl());

        // Robots Indexing Check (Content Type toggle / Taxonomy toggle / Global toggle)
        $isIndexed = true;
        if ($ctSlug) {
            $val = setting("seo_content_type_{$ctSlug}_index_enabled", true);
            $isIndexed = filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;
        } elseif ($taxSlug) {
            $val = setting("seo_taxonomy_{$taxSlug}_index_enabled", true);
            $isIndexed = filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;
        }

        $robots = $meta?->robots ?? ($isIndexed ? 'index,follow' : 'noindex,follow');
        if (! setting('seo_allow_indexing', true)) {
            $robots = 'noindex,nofollow';
        }

        // OG Image Fallback Chain:
        // 1. Custom OG Image uploaded for this specific page/entity in SEO settings
        // 2. Entity Featured Image ($entity->featured_image) or Page Block Image (hero_bg_image, image, banner_image) or CPT Meta Image
        // 3. Content-Type Social Image setting
        // 4. Taxonomy Social Image setting
        // 5. Site Default OG Image setting (seo_default_og_image)

        $metaOgPath = null;
        if ($meta?->ogImage && ! empty($meta->ogImage->path)) {
            $metaOgPath = (string) $meta->ogImage->path;
        } elseif ($meta && ! empty($meta->og_image_id)) {
            $mediaRecord = Media::find($meta->og_image_id);
            if ($mediaRecord && ! empty($mediaRecord->path)) {
                $metaOgPath = (string) $mediaRecord->path;
            }
        }

        $featuredImage = null;
        if ($entity) {
            if (! empty($entity->featured_image) && trim((string) $entity->featured_image) !== '') {
                $featuredImage = (string) $entity->featured_image;
            } elseif (method_exists($entity, 'getBlockValue')) {
                $blockImg = $entity->getBlockValue('hero_bg_image')
                    ?: ($entity->getBlockValue('image') ?: $entity->getBlockValue('banner_image'));
                if (! empty($blockImg) && trim((string) $blockImg) !== '') {
                    $featuredImage = (string) $blockImg;
                }
            } elseif (method_exists($entity, 'getMeta')) {
                $metaImg = $entity->getMeta('hero_image')
                    ?: ($entity->getMeta('banner_image') ?: ($entity->getMeta('image') ?: $entity->getMeta('banner_bg')));
                if (! empty($metaImg) && trim((string) $metaImg) !== '') {
                    $featuredImage = (string) $metaImg;
                }
            } elseif (isset($entity->meta) && is_array($entity->meta)) {
                $mImg = $entity->meta['hero_image'] ?? ($entity->meta['banner_image'] ?? ($entity->meta['image'] ?? null));
                if (! empty($mImg) && trim((string) $mImg) !== '') {
                    $featuredImage = (string) $mImg;
                }
            }
        }

        $ctSocialImage = $ctSlug ? (string) setting("seo_content_type_{$ctSlug}_social_image") : null;
        $taxSocialImage = $taxSlug ? (string) setting("seo_taxonomy_{$taxSlug}_social_image") : null;
        $defaultSocialImage = (string) setting('seo_default_og_image', '');

        $rawOgImage = (! empty($metaOgPath) && trim($metaOgPath) !== '') ? $metaOgPath : null;
        $rawOgImage ??= (! empty($featuredImage) && trim($featuredImage) !== '') ? $featuredImage : null;
        $rawOgImage ??= (! empty($ctSocialImage) && trim($ctSocialImage) !== '') ? $ctSocialImage : null;
        $rawOgImage ??= (! empty($taxSocialImage) && trim($taxSocialImage) !== '') ? $taxSocialImage : null;
        $rawOgImage ??= (! empty($defaultSocialImage) && trim($defaultSocialImage) !== '') ? $defaultSocialImage : null;

        $ogImageUrl = null;
        if ($rawOgImage) {
            if (str_starts_with($rawOgImage, 'http://') || str_starts_with($rawOgImage, 'https://')) {
                $ogImageUrl = $rawOgImage;
            } else {
                $ogImageUrl = resolve_block_asset($rawOgImage);
            }
        }

        $ogTitle = ($meta && ! empty($meta->og_title) && trim((string) $meta->og_title) !== '') ? $meta->og_title : $title;
        $ogDescription = ($meta && ! empty($meta->og_description) && trim((string) $meta->og_description) !== '') ? $meta->og_description : $description;

        $og = [
            'title' => $ogTitle,
            'description' => $ogDescription,
            'image' => $ogImageUrl,
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
        $hreflangs = $this->resolveHreflangs($entity, $canonical);

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'robots' => $robots,
            'og' => $og,
            'twitter' => $twitter,
            'schema' => $schema,
            'hreflangs' => $hreflangs,
        ];
    }

    /**
     * Resolve hreflang alternate URLs for available locales.
     */
    protected function resolveHreflangs(?Model $entity, ?string $canonical): array
    {
        $hreflangs = [];
        $availableLocales = function_exists('available_locales') ? available_locales() : ['en', 'id'];
        $defaultLocale = function_exists('setting') ? setting('default_locale', config('app.locale', 'en')) : config('app.locale', 'en');

        if (! $entity) {
            foreach ($availableLocales as $loc) {
                $hreflangs[$loc] = function_exists('current_page_localized_url') ? current_page_localized_url($loc) : url('/'.$loc);
            }
            $hreflangs['x-default'] = function_exists('current_page_localized_url') ? current_page_localized_url($defaultLocale) : url('/');

            return array_filter($hreflangs);
        }

        if (method_exists($entity, 'getUrl')) {
            foreach ($availableLocales as $loc) {
                $hreflangs[$loc] = $entity->getUrl($loc);
            }
            $hreflangs['x-default'] = $entity->getUrl($defaultLocale);
        }

        return array_filter($hreflangs);
    }

    /**
     * Resolve slug for a Content Type model (pages, posts, cpt).
     */
    protected function getContentTypeSlug(?Model $entity): ?string
    {
        if (! $entity) {
            return null;
        }

        $class = class_basename($entity);

        if ($class === 'TaxonomyTerm' || $class === 'Category' || $class === 'Tag') {
            return null;
        }

        if ($class === 'Page') {
            return 'pages';
        }

        if ($class === 'Post') {
            return 'posts';
        }

        if ($class === 'CptEntry') {
            return isset($entity->postType) && isset($entity->postType->slug)
                ? (string) $entity->postType->slug
                : null;
        }

        return strtolower($class).'s';
    }

    /**
     * Resolve slug for a Taxonomy model.
     */
    protected function getTaxonomySlug(?Model $entity): ?string
    {
        if (! $entity) {
            return null;
        }

        $class = class_basename($entity);

        if ($class === 'TaxonomyTerm' || $class === 'Category' || $class === 'Tag') {
            $taxObj = $entity->getAttribute('taxonomy');
            if (is_object($taxObj) && isset($taxObj->slug)) {
                return (string) $taxObj->slug;
            }
            if (isset($entity->taxonomy_slug)) {
                return (string) $entity->taxonomy_slug;
            }
            if (isset($entity->taxonomy_id)) {
                $tax = CustomTaxonomy::find($entity->taxonomy_id);
                if ($tax && ! empty($tax->slug)) {
                    return (string) $tax->slug;
                }
            }

            return match ($class) {
                'Category' => 'categories',
                'Tag' => 'tags',
                default => strtolower($class).'s',
            };
        }

        return null;
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

        $currentLocale = app()->getLocale();

        // 1. Try Excerpt (Localized, Direct Attribute, CPT Meta, or Page Blocks)
        $excerpt = null;
        if (method_exists($entity, 'getTranslation')) {
            $excerpt = $entity->getTranslation('excerpt', $currentLocale);
        }
        if (empty($excerpt) && ! empty($entity->excerpt)) {
            $excerpt = (string) $entity->excerpt;
        }
        if (empty($excerpt) && method_exists($entity, 'getMeta')) {
            $excerpt = $entity->getMeta('short_description')
                ?: ($entity->getMeta('excerpt') ?: $entity->getMeta('description'));
        }
        if (empty($excerpt) && method_exists($entity, 'getBlockValue')) {
            $excerpt = $entity->getBlockValue('hero_subtitle')
                ?: ($entity->getBlockValue('subtitle') ?: ($entity->getBlockValue('description') ?: $entity->getBlockValue('intro')));
        }

        if (! empty($excerpt)) {
            $text = strip_tags((string) $excerpt);
            $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
            $text = preg_replace('/\s+/', ' ', trim($text));

            if (mb_strlen($text) > 0) {
                return mb_strlen($text) > 160 ? mb_substr($text, 0, 157).'...' : $text;
            }
        }

        // 2. Fallback to Content (Localized, Direct Attribute, or Meta)
        $content = null;
        if (method_exists($entity, 'getTranslation')) {
            $content = $entity->getTranslation('content', $currentLocale);
        }
        if (empty($content) && ! empty($entity->content)) {
            $content = (string) $entity->content;
        }
        if (empty($content) && method_exists($entity, 'getMeta')) {
            $content = $entity->getMeta('content') ?: $entity->getMeta('overview');
        }

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
