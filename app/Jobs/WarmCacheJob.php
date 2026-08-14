<?php

namespace App\Jobs;

use App\Models\CustomPostType;
use App\Services\Sitemap\SitemapBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\Pool;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WarmCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 120;

    /**
     * @param  array<string>|null  $urls  Specific URLs to warm (targeted mode), or null for full sitemap.
     * @param  int|null  $concurrency  Number of concurrent requests (defaults to setting or 5).
     */
    public function __construct(
        public ?array $urls = null,
        public ?int $concurrency = null
    ) {}

    public function handle(): array
    {
        if (! setting('page_cache_enabled', false)) {
            Log::info('WarmCacheJob: Page cache is disabled in settings. Skipping warm.');

            return ['warmed' => 0, 'failed' => 0, 'total' => 0];
        }

        $targetUrls = $this->resolveUrls();
        if (empty($targetUrls)) {
            return ['warmed' => 0, 'failed' => 0, 'total' => 0];
        }

        $concurrency = $this->concurrency ?? (int) setting('page_cache_warm_concurrency', 5);
        $concurrency = max(1, min(20, $concurrency));

        $chunks = array_chunk($targetUrls, $concurrency);
        $warmedCount = 0;
        $failedCount = 0;

        foreach ($chunks as $chunk) {
            $responses = Http::pool(function (Pool $pool) use ($chunk) {
                foreach ($chunk as $url) {
                    $pool->as($url)
                        ->timeout(10)
                        ->withHeaders([
                            'User-Agent' => 'CdtCacheCrawler/1.0 (CDT Cache Preloader; +'.url('/').')',
                            'Accept' => 'text/html,application/xhtml+xml',
                        ])
                        ->get($url);
                }
            });

            foreach ($responses as $url => $response) {
                if ($response instanceof \Throwable) {
                    $failedCount++;
                    Log::warning("WarmCacheJob: Failed to warm {$url}: ".$response->getMessage());
                } elseif ($response->successful()) {
                    $warmedCount++;
                } else {
                    $failedCount++;
                }
            }
        }

        Log::info("WarmCacheJob: Completed warming. Warmed: {$warmedCount}, Failed: {$failedCount}, Total: ".count($targetUrls));

        return [
            'warmed' => $warmedCount,
            'failed' => $failedCount,
            'total' => count($targetUrls),
        ];
    }

    /**
     * Resolve and normalize URLs to be warmed, filtering excluded paths.
     */
    protected function resolveUrls(): array
    {
        $rawUrls = [];

        if (! empty($this->urls)) {
            foreach ($this->urls as $u) {
                $rawUrls[] = str_starts_with($u, 'http://') || str_starts_with($u, 'https://') ? $u : url($u);
            }
        } else {
            $rawUrls = $this->collectAllPublicUrls();
        }

        $rawUrls = array_values(array_unique(array_filter($rawUrls)));

        // Filter excluded paths and admin paths
        $filtered = [];
        foreach ($rawUrls as $fullUrl) {
            if ($this->shouldWarmUrl($fullUrl)) {
                $filtered[] = $fullUrl;
            }
        }

        return $filtered;
    }

    /**
     * Check if a specific URL should be warmed based on exclusions.
     */
    protected function shouldWarmUrl(string $fullUrl): bool
    {
        $path = parse_url($fullUrl, PHP_URL_PATH) ?? '/';
        $path = '/'.ltrim($path, '/');
        $trimmedPath = ltrim($path, '/');

        // Skip admin paths
        $adminPath = trim((string) config('admin.path', 'admin'), '/');
        if ($adminPath !== '' && str_starts_with($trimmedPath, $adminPath)) {
            return false;
        }
        if (str_starts_with($trimmedPath, 'admin') || str_starts_with($trimmedPath, 'ctrlpanel')) {
            return false;
        }

        // Skip excluded patterns from settings
        $excludedSetting = (string) setting('page_cache_excluded_paths', '');
        $excludedLines = array_filter(array_map('trim', explode("\n", str_replace("\r", '', $excludedSetting))));

        foreach ($excludedLines as $pattern) {
            $pattern = '/'.ltrim($pattern, '/');
            $regex = '#^'.str_replace(['\*', '\?'], ['.*', '.'], preg_quote($pattern, '#')).'$#i';
            if (preg_match($regex, $path) === 1) {
                return false;
            }
        }

        return true;
    }

    /**
     * Collect all public URLs across pages, posts, CPTs, and taxonomies.
     */
    protected function collectAllPublicUrls(): array
    {
        $urls = [
            url('/'),
        ];

        // Multi-locale root URLs
        if (function_exists('available_locales')) {
            $defaultLocale = setting('default_locale', config('app.locale', 'en'));
            foreach (available_locales() as $loc) {
                if ($loc !== $defaultLocale) {
                    $urls[] = url('/'.$loc);
                }
            }
        }

        /** @var SitemapBuilder $builder */
        $builder = app(SitemapBuilder::class);

        // 1. Pages
        foreach ($builder->getPageUrls() as $item) {
            if (! empty($item['loc'])) {
                $urls[] = $item['loc'];
            }
        }

        // 2. Posts
        foreach ($builder->getPostUrls() as $item) {
            if (! empty($item['loc'])) {
                $urls[] = $item['loc'];
            }
        }

        // 3. Custom Post Types
        $cpts = CustomPostType::where('is_active', true)->get();
        foreach ($cpts as $cpt) {
            foreach ($builder->getCptUrls($cpt->slug) as $item) {
                if (! empty($item['loc'])) {
                    $urls[] = $item['loc'];
                }
            }
        }

        // 4. Taxonomies
        foreach ($builder->getTaxonomyUrls() as $item) {
            if (! empty($item['loc'])) {
                $urls[] = $item['loc'];
            }
        }

        return $urls;
    }
}
