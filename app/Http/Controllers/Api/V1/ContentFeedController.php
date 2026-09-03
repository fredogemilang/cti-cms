<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\SeoMeta;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Content Feed API — Flat, LLM-friendly JSON of all published content.
 *
 * Designed for AI chatbot consumption without crawling.
 * Only returns published content. Supports incremental sync via ?since= parameter.
 */
class ContentFeedController extends Controller
{
    public function __invoke(Request $request, string $locale = 'en'): JsonResponse
    {
        $since = $request->query('since');
        $availableLocales = function_exists('available_locales') ? available_locales() : ['en', 'id'];

        if (! in_array($locale, $availableLocales, true)) {
            return response()->json([
                'error' => "Invalid locale '{$locale}'. Available: ".implode(', ', $availableLocales),
            ], 400);
        }

        // Validate 'since' timestamp format if provided
        $sinceDate = null;
        if ($since) {
            try {
                $sinceDate = Carbon::parse($since);
            } catch (\Throwable) {
                return response()->json([
                    'error' => 'Invalid "since" date format. Use ISO-8601 (e.g. 2026-09-01 or 2026-09-01T00:00:00Z).',
                ], 400);
            }
        }

        app()->setLocale($locale);

        $allTimestamps = collect();

        // 1. Pages (published only, sensible limit)
        $pagesQuery = Page::published()->with('allBlocks');
        if ($sinceDate) {
            $pagesQuery->where('updated_at', '>=', $sinceDate);
        }
        $pages = $pagesQuery->orderBy('menu_order')->take(200)->get();

        foreach ($pages as $p) {
            if ($p->updated_at) {
                $allTimestamps->push($p->updated_at);
            }
        }

        $pagesData = $pages->map(function (Page $page) use ($locale) {
            $blocks = $page->allBlocks
                ->where('is_active', true)
                ->mapWithKeys(function (PageBlock $block) use ($locale) {
                    $value = $block->localizedValue($locale);

                    // Flatten complex values to text for LLM consumption
                    if (is_array($value)) {
                        $value = $this->flattenToText($value);
                    }

                    // Strip HTML for clean text
                    if (is_string($value)) {
                        $value = strip_tags($value);
                        $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
                        $value = preg_replace('/\s+/', ' ', trim($value));
                    }

                    return [$block->name => $value];
                })
                // Preserve false and 0 (do not use empty() which discards 0 and false)
                ->filter(fn ($v) => $v !== null && $v !== '' && $v !== [])
                ->toArray();

            return [
                'title' => $page->getTranslation('title', $locale, true),
                'slug' => $page->slug,
                'url' => $page->getUrl(),
                'template' => $page->template,
                'content' => $blocks,
                'seo' => $this->resolveSeo($page, $locale),
                'updated_at' => $page->updated_at?->toIso8601String(),
            ];
        });

        // 2. CPT entries (published only)
        $cpts = CustomPostType::where('is_active', true)->where('publicly_queryable', true)->get();
        $cptData = [];

        foreach ($cpts as $cpt) {
            $entriesQuery = CptEntry::where('post_type_id', $cpt->id)->published();
            if ($sinceDate) {
                $entriesQuery->where('updated_at', '>=', $sinceDate);
            }

            $entries = $entriesQuery->orderBy('menu_order')->take(200)->get();

            if ($entries->isEmpty()) {
                continue;
            }

            foreach ($entries as $e) {
                if ($e->updated_at) {
                    $allTimestamps->push($e->updated_at);
                }
            }

            $cptData[$cpt->slug] = $entries->map(function (CptEntry $entry) use ($locale) {
                // Flatten meta for LLM
                $meta = $entry->meta ?? [];
                unset($meta['_translations']); // Remove internal translation metadata

                $flatMeta = [];
                foreach ($meta as $key => $value) {
                    if (is_array($value)) {
                        $flatMeta[$key] = $this->flattenToText($value);
                    } elseif (is_string($value)) {
                        $cleaned = strip_tags($value);
                        $cleaned = html_entity_decode($cleaned, ENT_QUOTES, 'UTF-8');
                        $flatMeta[$key] = preg_replace('/\s+/', ' ', trim($cleaned));
                    } else {
                        $flatMeta[$key] = $value;
                    }
                }

                $content = $entry->getTranslation('content', $locale, true);
                if ($content) {
                    $content = strip_tags($content);
                    $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
                    $content = preg_replace('/\s+/', ' ', trim($content));
                }

                return [
                    'title' => $entry->getTranslation('title', $locale, true),
                    'slug' => $entry->slug,
                    'url' => $entry->getUrl(),
                    'excerpt' => $entry->getTranslation('excerpt', $locale, true),
                    'content' => $content,
                    // Preserve false and 0
                    'meta' => array_filter($flatMeta, fn ($v) => $v !== null && $v !== '' && $v !== []),
                    'seo' => $this->resolveSeo($entry, $locale),
                    'updated_at' => $entry->updated_at?->toIso8601String(),
                ];
            })->values();
        }

        // 3. Posts (plugin check)
        $postsData = [];
        if (function_exists('is_plugin_active') && is_plugin_active('posts')) {
            try {
                $postModel = app('Plugins\\Posts\\Models\\Post');
                $postsQuery = $postModel::published();
                if ($sinceDate) {
                    $postsQuery->where('updated_at', '>=', $sinceDate);
                }
                $posts = $postsQuery->latest('published_at')->take(100)->get();

                foreach ($posts as $pst) {
                    if ($pst->updated_at) {
                        $allTimestamps->push($pst->updated_at);
                    }
                }

                $postsData = $posts->map(function ($post) use ($locale) {
                    $content = method_exists($post, 'getTranslation')
                        ? $post->getTranslation('content', $locale, true)
                        : $post->content;

                    if ($content) {
                        $content = strip_tags($content);
                        $content = preg_replace('/\s+/', ' ', trim($content));
                    }

                    return [
                        'title' => method_exists($post, 'getTranslation')
                            ? $post->getTranslation('title', $locale, true)
                            : $post->title,
                        'slug' => $post->slug,
                        'url' => method_exists($post, 'getUrl') ? $post->getUrl() : url("/blog/{$post->slug}"),
                        'excerpt' => $post->excerpt ?? '',
                        'content' => $content,
                        'updated_at' => $post->updated_at?->toIso8601String(),
                    ];
                })->values();
            } catch (\Throwable) {
                // Plugin not available, skip gracefully
            }
        }

        // Compute lastModified from loaded entities without re-querying the database
        $lastModified = $allTimestamps->filter()->max();

        return response()->json([
            'site' => [
                'name' => setting('site_name', config('app.name')),
                'url' => config('app.url'),
                'locale' => $locale,
                'available_locales' => $availableLocales,
            ],
            'sync' => [
                'since' => $since,
                'last_modified' => $lastModified?->toIso8601String(),
                'generated_at' => now()->toIso8601String(),
            ],
            'pages' => $pagesData,
            'cpt' => $cptData,
            'posts' => $postsData,
        ]);
    }

    /**
     * Resolve localized SEO metadata for a model.
     */
    protected function resolveSeo(mixed $entity, string $locale): array
    {
        $seoableType = get_class($entity);
        $seoableId = $entity->getKey();

        // 1. Check dedicated SeoMeta record for the requested locale
        $meta = SeoMeta::where('seoable_type', $seoableType)
            ->where('seoable_id', $seoableId)
            ->where('locale', $locale)
            ->first();

        // 2. Fallback to default locale SeoMeta
        if (! $meta) {
            $meta = SeoMeta::where('seoable_type', $seoableType)
                ->where('seoable_id', $seoableId)
                ->where(fn ($q) => $q->where('locale', '')->orWhereNull('locale'))
                ->first();
        }

        if ($meta && (! empty($meta->title) || ! empty($meta->description))) {
            return [
                'title' => $meta->title ?: ($entity->title ?? ''),
                'description' => $meta->description ?: ($entity->excerpt ?? ''),
            ];
        }

        // 3. Fallback to model's JSON seo column
        $seo = $entity->seo ?? [];
        $title = $seo['meta_title'] ?? $entity->title ?? '';
        $desc = $seo['meta_description'] ?? $entity->excerpt ?? '';

        // Check if translations array has localized SEO
        $translations = $entity->translations ?? [];
        if (! empty($translations[$locale]['seo']['meta_title'])) {
            $title = $translations[$locale]['seo']['meta_title'];
        }
        if (! empty($translations[$locale]['seo']['meta_description'])) {
            $desc = $translations[$locale]['seo']['meta_description'];
        }

        return [
            'title' => $title,
            'description' => $desc,
        ];
    }

    /**
     * Recursively flatten an array to human-readable text for LLM consumption.
     */
    protected function flattenToText(array $data, int $depth = 0): string
    {
        if ($depth > 5) {
            return '';
        }

        $parts = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $nested = $this->flattenToText($value, $depth + 1);
                if ($nested) {
                    $parts[] = is_numeric($key) ? $nested : "{$key}: {$nested}";
                }
            } elseif (is_string($value) || is_numeric($value) || is_bool($value)) {
                $clean = is_string($value) ? trim(strip_tags($value)) : (string) $value;
                if ($clean !== '') {
                    $parts[] = is_numeric($key) ? $clean : "{$key}: {$clean}";
                }
            }
        }

        return implode(' | ', $parts);
    }
}
