<?php

namespace Themes\Csi\Support;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Plugins\Posts\Models\Post;

class CsiSearchService
{
    /**
     * Synonym expansion pairs (bidirectional normalized tokens).
     *
     * @var array<string, array<int, string>>
     */
    protected static array $synonyms = [
        'ai' => ['artificial intelligence', 'gpu', 'mlops', 'machine learning', 'vector'],
        'ml' => ['machine learning', 'artificial intelligence', 'mlops'],
        'server' => ['compute', 'hci', 'rack', 'supermicro', 'lenovo', 'asrock', 'penguin'],
        'storage' => ['san', 'nas', 'nvme', 'all-flash', 'netapp', 'falconstor'],
        'network' => ['sd-wan', 'switching', 'fabric', '400gbe', 'extreme', 'ruijie', 'cambium', 'smartoptics', 'edgecore', 'cpacket', 'bluecat'],
        'backup' => ['recovery', 'disaster recovery', 'nakivo', 'falconstor'],
        'cloud' => ['iaas', 'paas', 'saas', 'multicloud', 'hybrid cloud', 'kubernetes', 'finops', 'alibaba', 'digitalocean', 'byteplus'],
        'security' => ['keamanan', 'waf', 'ndr', 'xdr', 'zero trust', 'soc', 'pam', 'thales', 'imperva', 'barracuda', 'menlo', 'axidian', 'versasec', 'swissbit'],
        'waf' => ['web application firewall', 'imperva', 'barracuda'],
        'data' => ['database', 'analytics', 'gis', 'teradata', 'oracle', 'couchbase', 'oceanbase', 'esri', 'easishare'],
        'wa' => ['whatsapp', 'converion care hub'],
    ];

    /**
     * Run categorized hybrid search across Solutions (+ modules), Products (Technology Alliances), and Articles.
     *
     * @return array{
     *     query: string,
     *     solutions: Collection<int, array<string, mixed>>,
     *     products: Collection<int, array<string, mixed>>,
     *     articles: Collection<int, array<string, mixed>>,
     *     total: int
     * }
     */
    public static function searchCategorized(string $rawQuery, ?string $locale = null): array
    {
        $locale ??= app()->getLocale();
        $query = trim((string) preg_replace('/\s+/', ' ', $rawQuery));

        if ($query === '' || mb_strlen($query) < 1) {
            return [
                'query' => '',
                'solutions' => collect(),
                'products' => collect(),
                'articles' => collect(),
                'total' => 0,
            ];
        }

        $tokens = static::tokenize($query);
        if (empty($tokens)) {
            return [
                'query' => $query,
                'solutions' => collect(),
                'products' => collect(),
                'articles' => collect(),
                'total' => 0,
            ];
        }

        $solutions = static::searchSolutions($query, $tokens, $locale);

        // Extract related vendor IDs linked inside matched solutions to boost contextual product results
        $boostedVendorIds = $solutions
            ->flatMap(fn (array $s) => $s['related_vendor_ids'] ?? [])
            ->unique()
            ->values()
            ->all();

        $products = static::searchProducts($query, $tokens, $locale, $boostedVendorIds);
        $articles = static::searchArticles($query, $tokens, $locale);

        return [
            'query' => $query,
            'solutions' => $solutions,
            'products' => $products,
            'articles' => $articles,
            'total' => $solutions->count() + $products->count() + $articles->count(),
        ];
    }

    /**
     * Compact JSON payload for live modal autocomplete dropdown.
     *
     * @return array<string, mixed>
     */
    public static function suggest(string $rawQuery, ?string $locale = null): array
    {
        $locale ??= app()->getLocale();
        $result = static::searchCategorized($rawQuery, $locale);

        return [
            'query' => $result['query'],
            'total' => $result['total'],
            'solutions' => $result['solutions']->take(4)->map(fn (array $item) => [
                'title' => $item['title'],
                'badge' => $item['badge'],
                'url' => $item['url'],
                'image' => $item['image'],
            ])->values()->all(),
            'products' => $result['products']->take(6)->map(fn (array $item) => [
                'name' => $item['name'],
                'logo' => $item['logo'],
                'url' => $item['url'],
            ])->values()->all(),
            'articles' => $result['articles']->take(3)->map(fn (array $item) => [
                'title' => $item['title'],
                'url' => $item['url'],
                'image' => $item['image'],
            ])->values()->all(),
        ];
    }

    /**
     * @param  array<int, string>  $tokens
     * @return Collection<int, array<string, mixed>>
     */
    protected static function searchSolutions(string $query, array $tokens, string $locale): Collection
    {
        $cpt = CustomPostType::where('slug', 'solutions')->where('is_active', true)->first();
        if (! $cpt) {
            return collect();
        }

        $entries = CptEntry::published()
            ->where('post_type_id', $cpt->id)
            ->orderBy('menu_order')
            ->orderBy('id')
            ->get();

        $coreLabel = $locale === 'id' ? 'Solusi Utama' : 'Core Solution';
        $results = collect();
        $seenKeys = [];

        foreach ($entries as $entry) {
            $pillarTitle = (string) ($entry->getTranslation('title', $locale) ?? $entry->title);
            $pillarSubtitle = (string) ($entry->getMeta('archive_summary') ?: $entry->getMeta('subtitle') ?: $entry->getMeta('hero_subtitle') ?: ($entry->getTranslation('excerpt', $locale) ?? ''));
            $pillarBullets = (string) $entry->getMeta('archive_bullets', '');
            $pillarUrl = (string) $entry->getUrl($locale);
            $defaultImgPath = (string) ($entry->getMeta('modules_image') ?: $entry->featured_image ?: '');
            $defaultImg = $defaultImgPath !== '' ? resolve_block_asset($defaultImgPath) : '';

            $pillarScore = static::scoreFields($query, $tokens, [
                [$pillarTitle, 100],
                [$pillarSubtitle, 35],
                [$pillarBullets, 20],
                [(string) $entry->getMeta('hero_subtitle', ''), 20],
            ]);

            $rawMeta = $entry->meta ?? [];
            $defaultModules = is_array($rawMeta['modules'] ?? null) ? $rawMeta['modules'] : [];
            $localizedModules = $entry->getMeta('modules', []);
            if (! is_array($localizedModules)) {
                $localizedModules = [];
            }

            $matchedModules = [];
            foreach ($localizedModules as $idx => $mod) {
                if (! is_array($mod) || trim((string) ($mod['title'] ?? '')) === '') {
                    continue;
                }

                $modTitle = trim((string) ($mod['title'] ?? ''));
                $modDesc = trim((string) ($mod['description'] ?? ''));
                $relIds = $mod['related_products'] ?? ($defaultModules[$idx]['related_products'] ?? []);
                if (! is_array($relIds)) {
                    $relIds = [];
                }
                $relIds = array_values(array_unique(array_filter(array_map('intval', $relIds))));

                $modScore = static::scoreFields($query, $tokens, [
                    [$modTitle, 90],
                    [$modDesc, 35],
                ]);

                if ($modScore > 0) {
                    $modImgPath = ! empty($mod['image']) ? (string) $mod['image'] : $defaultImgPath;
                    $matchedModules[] = [
                        'badge' => $pillarTitle,
                        'title' => $modTitle,
                        'description' => $modDesc,
                        'image' => $modImgPath !== '' ? resolve_block_asset($modImgPath) : $defaultImg,
                        'url' => $pillarUrl,
                        'related_vendor_ids' => $relIds,
                        'score' => $modScore + ($pillarScore > 0 ? 15 : 0),
                    ];
                }
            }

            // Emit top module cards if specific modules matched directly
            foreach ($matchedModules as $card) {
                $dedupKey = mb_strtolower($entry->slug.'::'.$card['title']);
                if (! isset($seenKeys[$dedupKey])) {
                    $seenKeys[$dedupKey] = true;
                    $results->push($card);
                }
            }

            // Emit the pillar card if pillar metadata matched directly (or when modules matched & pillar wasn't emitted)
            if ($pillarScore > 0) {
                $dedupKey = mb_strtolower($entry->slug.'::pillar');
                if (! isset($seenKeys[$dedupKey])) {
                    $seenKeys[$dedupKey] = true;
                    $allRelIds = collect($defaultModules)
                        ->flatMap(fn ($m) => is_array($m['related_products'] ?? null) ? $m['related_products'] : [])
                        ->map(fn ($id) => (int) $id)
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();

                    $results->push([
                        'badge' => $coreLabel,
                        'title' => $pillarTitle,
                        'description' => Str::limit(trim($pillarSubtitle), 160),
                        'image' => $defaultImg,
                        'url' => $pillarUrl,
                        'related_vendor_ids' => $allRelIds,
                        'score' => $pillarScore + (empty($matchedModules) ? 5 : 25),
                    ]);
                }
            }
        }

        return $results->sortByDesc('score')->values();
    }

    /**
     * @param  array<int, string>  $tokens
     * @param  array<int, int>  $boostedIds
     * @return Collection<int, array<string, mixed>>
     */
    protected static function searchProducts(string $query, array $tokens, string $locale, array $boostedIds = []): Collection
    {
        $cpt = CustomPostType::where('slug', 'technology-alliances')->where('is_active', true)->first();
        if (! $cpt) {
            return collect();
        }

        $entries = CptEntry::published()
            ->where('post_type_id', $cpt->id)
            ->orderBy('menu_order')
            ->orderBy('id')
            ->get();

        $results = collect();
        $boostSet = array_flip($boostedIds);

        foreach ($entries as $entry) {
            $name = (string) ($entry->getTranslation('title', $locale) ?? $entry->title);
            $category = (string) $entry->getMeta('alliance_category', '');
            $headline = (string) $entry->getMeta('hero_headline', '');
            $heroDesc = (string) $entry->getMeta('hero_description', '');
            $features = static::flattenArray($entry->getMeta('features_list', []));
            $why = static::flattenArray($entry->getMeta('why_cards', []));
            $solutions = static::flattenArray($entry->getMeta('solutions_items', []));
            $tabs = static::flattenArray($entry->getMeta('solutions_tabs', []));
            $excerpt = (string) ($entry->getTranslation('excerpt', $locale) ?? $entry->excerpt ?? '');

            $score = static::scoreFields($query, $tokens, [
                [$name, 120],
                [$category, 45],
                [$headline, 40],
                [$heroDesc, 25],
                [$excerpt, 25],
                [$features, 15],
                [$why, 15],
                [$tabs, 15],
                [$solutions, 15],
            ]);

            if (isset($boostSet[$entry->id])) {
                $score += $score > 0 ? 35 : 18;
            }

            if ($score <= 0) {
                continue;
            }

            $logoPath = $entry->getMeta('brand_logo') ?: $entry->featured_image;
            $logo = $logoPath ? resolve_block_asset((string) $logoPath) : '';

            $results->push([
                'id' => $entry->id,
                'name' => $name,
                'logo' => $logo,
                'url' => (string) $entry->getUrl($locale),
                'category' => $category,
                'score' => $score,
            ]);
        }

        return $results
            ->sort(function (array $a, array $b): int {
                if ($b['score'] !== $a['score']) {
                    return $b['score'] <=> $a['score'];
                }

                return strnatcasecmp($a['name'], $b['name']);
            })
            ->values();
    }

    /**
     * Search blog Articles safely (only if `posts` plugin is active; hides smoothly otherwise).
     *
     * @param  array<int, string>  $tokens
     * @return Collection<int, array<string, mixed>>
     */
    protected static function searchArticles(string $query, array $tokens, string $locale): Collection
    {
        if (
            ! function_exists('is_plugin_active')
            || ! is_plugin_active('posts')
            || ! class_exists(Post::class)
        ) {
            return collect();
        }

        try {
            $posts = Post::query()
                ->where('status', 'published')
                ->latest('published_at')
                ->get();
        } catch (\Throwable) {
            return collect();
        }

        $results = collect();

        foreach ($posts as $post) {
            $title = method_exists($post, 'getTranslation')
                ? (string) ($post->getTranslation('title', $locale) ?? $post->title)
                : (string) $post->title;
            $excerpt = method_exists($post, 'getTranslation')
                ? (string) ($post->getTranslation('excerpt', $locale) ?? $post->excerpt ?? '')
                : (string) ($post->excerpt ?? '');
            $content = method_exists($post, 'getTranslation')
                ? strip_tags((string) ($post->getTranslation('content', $locale) ?? $post->content ?? ''))
                : strip_tags((string) ($post->content ?? ''));

            $score = static::scoreFields($query, $tokens, [
                [$title, 100],
                [$excerpt, 40],
                [$content, 15],
            ]);

            if ($score <= 0) {
                continue;
            }

            $imgPath = (string) ($post->featured_image ?? '');
            $url = method_exists($post, 'getUrl')
                ? (string) $post->getUrl($locale)
                : url(($locale === 'id' ? '/id/blog/' : '/blog/').$post->slug);

            $results->push([
                'title' => $title,
                'excerpt' => Str::limit(trim(strip_tags($excerpt ?: $content)), 120),
                'image' => $imgPath !== '' ? resolve_block_asset($imgPath) : '',
                'url' => $url,
                'score' => $score,
            ]);
        }

        return $results->sortByDesc('score')->values();
    }

    /**
     * Weighted multi-field hybrid matcher with exact phrase, token prefix, word-boundary, and synonyms.
     *
     * @param  array<int, string>  $tokens
     * @param  array<int, array{0: string, 1: int}>  $weightedFields
     */
    protected static function scoreFields(string $query, array $tokens, array $weightedFields): int
    {
        $totalScore = 0;
        $needleLower = mb_strtolower($query);

        $expandedTokens = $tokens;
        foreach ($tokens as $tok) {
            if (isset(static::$synonyms[$tok])) {
                foreach (static::$synonyms[$tok] as $syn) {
                    $expandedTokens[] = $syn;
                }
            }
        }
        $expandedTokens = array_values(array_unique($expandedTokens));

        foreach ($weightedFields as [$fieldText, $weight]) {
            $haystack = mb_strtolower(trim(strip_tags((string) $fieldText)));
            if ($haystack === '') {
                continue;
            }

            // 1. Exact full query match
            if ($haystack === $needleLower) {
                $totalScore += $weight * 3;

                continue;
            }

            // 2. Direct phrase substring / prefix hit
            if (mb_strlen($needleLower) >= 3 && str_contains($haystack, $needleLower)) {
                $totalScore += $weight * 2;

                continue;
            }

            // 3. Primary user tokens check (all primary tokens must match for multi-word precision)
            $primaryMatched = 0;
            foreach ($tokens as $tok) {
                if (static::matchesToken($haystack, $tok)) {
                    $primaryMatched++;
                }
            }

            if ($primaryMatched > 0) {
                $ratio = $primaryMatched / count($tokens);
                $totalScore += (int) round($weight * ($ratio === 1.0 ? 1.5 : $ratio * 0.7));

                continue;
            }

            // 4. Synonym fallback boost (smaller weight so direct hits always win)
            foreach ($expandedTokens as $syn) {
                if (in_array($syn, $tokens, true)) {
                    continue;
                }
                if (static::matchesToken($haystack, $syn)) {
                    $totalScore += (int) max(5, round($weight * 0.35));
                    break;
                }
            }
        }

        return $totalScore;
    }

    protected static function matchesToken(string $haystack, string $token): bool
    {
        if ($token === '') {
            return false;
        }

        // Short token (<= 2 chars e.g. "ai", "wa") must match on word boundary so "ai" does not match "daily"
        if (mb_strlen($token) <= 2) {
            return (bool) preg_match('/\b'.preg_quote($token, '/').'\b/iu', $haystack);
        }

        // Words >= 3 chars match as word prefix or substring cleanly (e.g. "infra" matches "Infrastructure")
        return str_contains($haystack, $token);
    }

    /**
     * @return array<int, string>
     */
    protected static function tokenize(string $query): array
    {
        $parts = preg_split('/[^\p{L}\p{N}_\-]+/u', mb_strtolower($query), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return array_values(array_unique(array_filter($parts, fn (string $t) => mb_strlen($t) >= 2)));
    }

    /**
     * @param  mixed  $value
     */
    protected static function flattenArray($value): string
    {
        if (! is_array($value)) {
            return is_string($value) ? $value : '';
        }

        $chunks = [];
        array_walk_recursive($value, function ($v) use (&$chunks) {
            if (is_string($v) && trim($v) !== '' && ! str_starts_with(trim($v), 'media/')) {
                $chunks[] = $v;
            }
        });

        return implode(' ', $chunks);
    }
}
