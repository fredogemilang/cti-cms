<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Page;
use App\Services\ContentTextExtractor;
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
    public function __construct(
        protected ContentTextExtractor $extractor
    ) {}

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
            return [
                'title' => $page->getTranslation('title', $locale, true),
                'slug' => $page->slug,
                'url' => $page->getUrl(),
                'template' => $page->template,
                'content' => $this->extractor->extractBlocks($page, $locale),
                'seo' => $this->extractor->resolveSeo($page, $locale),
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
                $rawContent = $entry->getTranslation('content', $locale, true) ?? $entry->content ?? '';
                $cleanContent = $this->extractor->cleanText($rawContent);

                return [
                    'title' => $entry->getTranslation('title', $locale, true),
                    'slug' => $entry->slug,
                    'url' => $entry->getUrl(),
                    'excerpt' => $entry->getTranslation('excerpt', $locale, true),
                    'content' => $cleanContent ?: null,
                    'meta' => $this->extractor->extractCptMeta($entry, $locale),
                    'seo' => $this->extractor->resolveSeo($entry, $locale),
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
                    $extracted = $this->extractor->extractPost($post, $locale);

                    return [
                        'title' => $extracted['title'],
                        'slug' => $post->slug,
                        'url' => $extracted['url'],
                        'excerpt' => $extracted['excerpt'],
                        'content' => $extracted['body'],
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
}
