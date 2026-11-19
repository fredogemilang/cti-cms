<?php

namespace App\Http\Middleware;

use App\Services\CacheManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets X-LiteSpeed-Cache-Control and X-LiteSpeed-Tag headers
 * so LiteSpeed Web Server can serve cached pages directly from server memory.
 */
class LSCacheHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $isLoggedIn = auth()->check();

        if ($isLoggedIn) {
            // Keep unencrypted cms_logged_in cookie active so LiteSpeed bypasses cache
            cookie()->queue('cms_logged_in', '1', 60 * 24 * 7, '/', null, null, false);
        } elseif ($request->hasCookie('cms_logged_in')) {
            // Clear the logged-in cookie if user is no longer authenticated
            cookie()->queue(cookie()->forget('cms_logged_in', '/', null));
        }

        $response = $next($request);

        if (CacheManager::isPurgeRequested()) {
            $response->headers->set('X-LiteSpeed-Purge', '*');
        }

        if (! $this->shouldCache($request, $response)) {
            $response->headers->set('X-LiteSpeed-Cache-Control', 'no-cache');

            return $response;
        }

        $ttl = (int) setting('page_cache_ttl', 3600);
        $response->headers->set('X-LiteSpeed-Cache-Control', "public,max-age={$ttl}");

        $tags = $this->buildTags($request);
        if (! empty($tags)) {
            $response->headers->set('X-LiteSpeed-Tag', implode(',', $tags));
        }

        return $response;
    }

    protected function shouldCache(Request $request, Response $response): bool
    {
        if (! setting('page_cache_enabled', false)) {
            return false;
        }

        if (! in_array($request->method(), ['GET', 'HEAD'], true)) {
            return false;
        }

        if (auth()->check()) {
            return false;
        }

        if ($response->getStatusCode() !== 200) {
            return false;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');
        if ($contentType !== '' && ! str_contains($contentType, 'text/html')) {
            return false;
        }

        // If response already has an explicit no-cache directive, respect it
        if (strtolower((string) $response->headers->get('X-LiteSpeed-Cache-Control')) === 'no-cache') {
            return false;
        }

        // Skip deferred AJAX fragment paths
        if (str_starts_with(ltrim($request->path(), '/'), '_deferred')) {
            return false;
        }

        // Skip admin paths
        $adminPath = trim((string) config('admin.path', 'admin'), '/');
        if ($adminPath !== '' && str_starts_with(ltrim($request->path(), '/'), $adminPath)) {
            return false;
        }

        // Skip excluded paths from settings
        $excluded = array_filter(array_map('trim', explode("\n", (string) setting('page_cache_excluded_paths', ''))));
        $path = '/'.ltrim($request->path(), '/');
        foreach ($excluded as $pattern) {
            $regex = '#^'.str_replace(['\*', '\?'], ['.*', '.'], preg_quote($pattern, '#')).'$#';
            if (preg_match($regex, $path) === 1) {
                return false;
            }
        }

        return true;
    }

    protected function buildTags(Request $request): array
    {
        $tags = ['public'];

        $path = trim($request->path(), '/');
        if ($path === '' || $path === '/') {
            $tags[] = 'home';
        } else {
            $firstSegment = explode('/', $path)[0];
            if ($firstSegment !== '') {
                $tags[] = 'route:'.$firstSegment;
            }
        }

        return array_values(array_unique($tags));
    }
}
