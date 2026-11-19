<?php

namespace App\Services;

use App\Models\CptEntry;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\SeoMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Plugins\Posts\Models\Post;

class ContentTextExtractor
{
    /**
     * Extract normalized search and feed text for an entity in a given locale.
     *
     * @return array{title: string, excerpt: string, body: string, url: string}
     */
    public function extract(Model $entity, string $locale): array
    {
        if ($entity instanceof Page) {
            return $this->extractPage($entity, $locale);
        }

        if ($entity instanceof CptEntry) {
            return $this->extractCptEntry($entity, $locale);
        }

        if ($entity instanceof Post || ($this->isPostModel($entity))) {
            return $this->extractPost($entity, $locale);
        }

        return [
            'title' => $this->cleanText($entity->getAttribute('title') ?? ''),
            'excerpt' => $this->cleanText($entity->getAttribute('excerpt') ?? ''),
            'body' => $this->cleanText($entity->getAttribute('content') ?? ''),
            'url' => method_exists($entity, 'getUrl') ? (string) $entity->getUrl() : '',
        ];
    }

    /**
     * Extract Page model content.
     *
     * @return array{title: string, excerpt: string, body: string, url: string}
     */
    public function extractPage(Page $page, string $locale): array
    {
        $title = $page->getTranslation('title', $locale, true) ?? $page->title ?? '';

        $blocksData = $this->extractBlocks($page, $locale);
        $bodyParts = array_values($blocksData);
        $body = implode("\n\n", array_filter($bodyParts));

        $seo = $this->resolveSeo($page, $locale);
        $excerpt = $seo['description'] ?? '';

        if (empty($excerpt) && ! empty($body)) {
            $excerpt = Str::limit(preg_replace('/\s+/', ' ', $body), 160);
        }

        $url = $page->getUrl();
        if ($locale !== setting('default_locale', config('app.locale', 'en')) && method_exists($page, 'getLocalizedUrl')) {
            $url = $page->getLocalizedUrl($locale);
        }

        return [
            'title' => $this->cleanText($title),
            'excerpt' => $this->cleanText($excerpt),
            'body' => $this->cleanText($body),
            'url' => (string) $url,
        ];
    }

    /**
     * Extract active PageBlocks for a Page into structured key-value text pairs.
     *
     * @return array<string, string>
     */
    public function extractBlocks(Page $page, string $locale): array
    {
        $blocksCollection = $page->relationLoaded('allBlocks')
            ? $page->allBlocks
            : $page->allBlocks()->get();

        return $blocksCollection
            ->where('is_active', true)
            ->mapWithKeys(function (PageBlock $block) use ($locale) {
                $value = $block->localizedValue($locale);

                if (is_array($value)) {
                    $value = $this->flattenToText($value);
                } elseif (is_string($value)) {
                    $value = $this->cleanText($value);
                } elseif (is_numeric($value) || is_bool($value)) {
                    $value = (string) $value;
                } else {
                    $value = '';
                }

                return [$block->name => $value];
            })
            ->filter(fn ($v) => $v !== null && $v !== '' && $v !== [])
            ->toArray();
    }

    /**
     * Extract CptEntry content.
     *
     * @return array{title: string, excerpt: string, body: string, url: string}
     */
    public function extractCptEntry(CptEntry $entry, string $locale): array
    {
        $title = $entry->getTranslation('title', $locale, true) ?? $entry->title ?? '';
        $rawContent = $entry->getTranslation('content', $locale, true) ?? $entry->content ?? '';
        $rawExcerpt = $entry->getTranslation('excerpt', $locale, true) ?? $entry->excerpt ?? '';

        $cleanContent = $this->cleanText($rawContent);
        $cleanExcerpt = $this->cleanText($rawExcerpt);

        $metaData = $this->extractCptMeta($entry, $locale);
        $metaText = implode("\n", array_filter($metaData));

        $body = trim($cleanContent."\n\n".$metaText);

        if (empty($cleanExcerpt)) {
            $seo = $this->resolveSeo($entry, $locale);
            $cleanExcerpt = $seo['description'] ?? '';
        }

        if (empty($cleanExcerpt) && ! empty($body)) {
            $cleanExcerpt = Str::limit(preg_replace('/\s+/', ' ', $body), 160);
        }

        $url = $entry->getUrl();
        if ($locale !== setting('default_locale', config('app.locale', 'en')) && method_exists($entry, 'getLocalizedUrl')) {
            $url = $entry->getLocalizedUrl($locale);
        }

        return [
            'title' => $this->cleanText($title),
            'excerpt' => $cleanExcerpt,
            'body' => $body,
            'url' => (string) $url,
        ];
    }

    /**
     * Extract sanitized meta fields for a CptEntry.
     *
     * @return array<string, string>
     */
    public function extractCptMeta(CptEntry $entry, string $locale): array
    {
        $meta = $entry->meta ?? [];
        unset($meta['_translations']);

        $defaultLocale = setting('default_locale', config('app.locale', 'en'));
        $translations = $entry->meta['_translations'][$locale] ?? [];

        $flatMeta = [];
        foreach ($meta as $key => $value) {
            if (CptEntry::isMediaKey($key)) {
                continue;
            }

            if ($locale !== $defaultLocale && isset($translations[$key])) {
                $value = $translations[$key];
            }

            if (is_array($value)) {
                $flatText = $this->flattenToText($value, 0, true);
                if ($flatText !== '') {
                    $flatMeta[$key] = $flatText;
                }
            } elseif (is_string($value)) {
                $clean = $this->cleanText($value);
                if ($clean !== '' && ! $this->looksLikeAssetPath($clean)) {
                    $flatMeta[$key] = $clean;
                }
            } elseif (is_numeric($value) || is_bool($value)) {
                $flatMeta[$key] = (string) $value;
            }
        }

        return $flatMeta;
    }

    /**
     * Extract Post model content (Posts plugin).
     *
     * @return array{title: string, excerpt: string, body: string, url: string}
     */
    public function extractPost(Model $post, string $locale): array
    {
        $title = method_exists($post, 'getTranslation')
            ? ($post->getTranslation('title', $locale, true) ?? $post->getAttribute('title') ?? '')
            : ($post->getAttribute('title') ?? '');

        $rawContent = method_exists($post, 'getTranslation')
            ? ($post->getTranslation('content', $locale, true) ?? $post->getAttribute('content') ?? '')
            : ($post->getAttribute('content') ?? '');

        $rawExcerpt = method_exists($post, 'getTranslation')
            ? ($post->getTranslation('excerpt', $locale, true) ?? $post->getAttribute('excerpt') ?? '')
            : ($post->getAttribute('excerpt') ?? '');

        $cleanContent = $this->cleanText($rawContent);
        $cleanExcerpt = $this->cleanText($rawExcerpt);

        $body = $cleanContent;

        if (empty($cleanExcerpt)) {
            $seo = $this->resolveSeo($post, $locale);
            $cleanExcerpt = $seo['description'] ?? '';
        }

        if (empty($cleanExcerpt) && ! empty($body)) {
            $cleanExcerpt = Str::limit(preg_replace('/\s+/', ' ', $body), 160);
        }

        $url = method_exists($post, 'getUrl') ? (string) $post->getUrl() : '';

        return [
            'title' => $this->cleanText($title),
            'excerpt' => $cleanExcerpt,
            'body' => $body,
            'url' => $url,
        ];
    }

    /**
     * Resolve localized SEO metadata for an entity.
     *
     * @return array{title: string, description: string}
     */
    public function resolveSeo(Model $entity, string $locale): array
    {
        // 1. Check dedicated SeoMeta record
        $seoMeta = SeoMeta::where('seoable_type', get_class($entity))
            ->where('seoable_id', $entity->getKey())
            ->where('locale', $locale)
            ->first();

        if (! $seoMeta && $locale !== 'en') {
            $seoMeta = SeoMeta::where('seoable_type', get_class($entity))
                ->where('seoable_id', $entity->getKey())
                ->where('locale', 'en')
                ->first();
        }

        if ($seoMeta && ($seoMeta->title || $seoMeta->description)) {
            return [
                'title' => $this->cleanText($seoMeta->title),
                'description' => $this->cleanText($seoMeta->description),
            ];
        }

        // 2. Check model's JSON seo column
        $seo = $entity->getAttribute('seo') ?? [];
        $title = $seo['meta_title'] ?? $entity->getAttribute('title') ?? '';
        $desc = $seo['meta_description'] ?? $entity->getAttribute('excerpt') ?? '';

        $translations = $entity->getAttribute('translations') ?? [];
        if (! empty($translations[$locale]['seo']['meta_title'])) {
            $title = $translations[$locale]['seo']['meta_title'];
        }
        if (! empty($translations[$locale]['seo']['meta_description'])) {
            $desc = $translations[$locale]['seo']['meta_description'];
        }

        return [
            'title' => $this->cleanText($title),
            'description' => $this->cleanText($desc),
        ];
    }

    /**
     * Strip HTML tags, decode entities, and normalize whitespace.
     */
    public function cleanText(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $stripped = strip_tags($text);
        $decoded = html_entity_decode($stripped, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim((string) preg_replace('/\s+/', ' ', $decoded));
    }

    /**
     * Recursively flatten an array into human-readable text.
     */
    public function flattenToText(array $data, int $depth = 0, bool $filterMedia = true): string
    {
        if ($depth > 5) {
            return '';
        }

        $parts = [];

        foreach ($data as $key => $value) {
            $strKey = (string) $key;
            if ($filterMedia && CptEntry::isMediaKey($strKey)) {
                continue;
            }

            if (is_array($value)) {
                $nested = $this->flattenToText($value, $depth + 1, $filterMedia);
                if ($nested !== '') {
                    $parts[] = is_numeric($key) ? $nested : "{$key}: {$nested}";
                }
            } elseif (is_string($value)) {
                $clean = $this->cleanText($value);
                if ($clean !== '' && (! $filterMedia || ! $this->looksLikeAssetPath($clean))) {
                    $parts[] = is_numeric($key) ? $clean : "{$key}: {$clean}";
                }
            } elseif (is_numeric($value) || is_bool($value)) {
                $valStr = (string) $value;
                $parts[] = is_numeric($key) ? $valStr : "{$key}: {$valStr}";
            }
        }

        return implode(' | ', $parts);
    }

    /**
     * Check if a string looks like an asset/image path.
     */
    protected function looksLikeAssetPath(string $value): bool
    {
        if (str_starts_with($value, 'uploads/') || str_starts_with($value, 'media/') || str_starts_with($value, 'themes/')) {
            return true;
        }

        return (bool) preg_match('/\.(png|jpe?g|webp|gif|svg|ico|pdf|zip)$/i', $value);
    }

    /**
     * Safe class detection for Post model.
     */
    protected function isPostModel(Model $entity): bool
    {
        return class_exists(Post::class) && $entity instanceof Post;
    }
}
