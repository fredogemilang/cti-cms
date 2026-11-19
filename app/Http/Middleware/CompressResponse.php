<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Compresses textual responses using Brotli (preferred) or Gzip when:
 *   - settings.pageopt_gzip_enabled is on
 *   - the client sent Accept-Encoding: br or gzip
 *   - the response is text-ish and >= 1KB
 *   - the response isn't already encoded
 *
 * Brotli typically achieves 15-25% better compression than Gzip.
 * Falls back to Gzip transparently when Brotli ext is not installed.
 *
 * Most production hosting (NGINX/Apache) does this at the web-server layer.
 * Enable this only when your host doesn't.
 */
class CompressResponse
{
    protected const MIN_BYTES = 1024;

    protected const COMPRESSIBLE_TYPES = [
        'text/html',
        'text/plain',
        'text/css',
        'text/xml',
        'application/json',
        'application/javascript',
        'application/xml',
        'image/svg+xml',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldCompress($request, $response)) {
            return $response;
        }

        $body = (string) $response->getContent();
        if (strlen($body) < self::MIN_BYTES) {
            return $response;
        }

        $acceptEncoding = (string) $request->header('Accept-Encoding', '');

        $compressed = null;
        $encoding = null;

        // Try Brotli first (typically 15-25% better compression than Gzip)
        if (function_exists('brotli_compress') && str_contains($acceptEncoding, 'br')) {
            $compressed = brotli_compress($body, 4); // Level 4 = balanced speed/ratio
            $encoding = 'br';
        }

        // Fallback to Gzip
        if ($compressed === null || $compressed === false) {
            if (str_contains($acceptEncoding, 'gzip')) {
                $compressed = gzencode($body, 6);
                $encoding = 'gzip';
            }
        }

        if ($compressed === null || $compressed === false) {
            return $response;
        }

        // Only use compressed version if it's actually smaller
        if (strlen($compressed) >= strlen($body)) {
            return $response;
        }

        $response->setContent($compressed);
        $response->headers->set('Content-Encoding', $encoding);
        $response->headers->set('Content-Length', (string) strlen($compressed));
        $response->headers->set('Vary', $this->mergeVary($response->headers->get('Vary'), 'Accept-Encoding'));

        return $response;
    }

    protected function shouldCompress(Request $request, Response $response): bool
    {
        if (! setting('pageopt_gzip_enabled', false)) {
            return false;
        }
        if (! str_contains((string) $request->header('Accept-Encoding', ''), 'gzip')
            && ! str_contains((string) $request->header('Accept-Encoding', ''), 'br')) {
            return false;
        }
        if ($response->headers->has('Content-Encoding')) {
            return false;
        }

        $type = (string) $response->headers->get('Content-Type', '');
        foreach (self::COMPRESSIBLE_TYPES as $compType) {
            if (str_contains($type, $compType)) {
                return true;
            }
        }

        return false;
    }

    protected function mergeVary(?string $existing, string $append): string
    {
        if (! $existing) {
            return $append;
        }
        $parts = array_map('trim', explode(',', $existing));
        if (! in_array(strtolower($append), array_map('strtolower', $parts), true)) {
            $parts[] = $append;
        }

        return implode(', ', $parts);
    }
}
