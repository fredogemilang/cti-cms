<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies Page Optimization + CDN URL rewriting to public HTML responses.
 * - HTML minify (whitespace + comments)
 * - Removes ?ver= query strings from asset URLs
 * - Rewrites configured local paths to a CDN base URL
 * - Adds loading="lazy" to <img> tags
 * - DNS prefetch injection for external domains
 * - Elide redundant boolean HTML attributes
 */
class OptimizeHtml
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldProcess($request, $response)) {
            return $response;
        }

        $originalHtml = (string) $response->getContent();
        if (strlen(trim($originalHtml)) === 0) {
            return $response;
        }

        $html = $originalHtml;

        if (setting('cdn_enabled', false) && setting('cdn_base_url', '')) {
            $html = $this->rewriteCdn($html);
        }

        if (setting('pageopt_remove_query_strings', false)) {
            $html = $this->stripQueryStrings($html);
        }

        if (setting('img_lazy_load', true)) {
            $html = $this->lazyLoadImages($html);
        }

        if (setting('pageopt_defer_external_scripts', false)) {
            $html = $this->deferExternalScripts($html);
        }

        if ($critical = trim((string) setting('pageopt_critical_css', ''))) {
            // When "Homepage Only" is on, skip critical CSS injection for non-homepage paths
            if (setting('pageopt_critical_css_homepage_only', false)) {
                $path = ltrim($request->path(), '/');
                $isHomepage = $path === ''
                    || $path === '/'
                    || (function_exists('available_locales') && in_array($path, available_locales(), true));

                if (! $isHomepage) {
                    $critical = null;
                }
            }

            if ($critical) {
                $html = $this->inlineCriticalCss($html, $critical);
                $html = $this->deferStylesheets($html);
            }
        }

        if (setting('pageopt_dns_prefetch', false)) {
            $html = $this->insertDnsPrefetch($html);
        }

        if (setting('pageopt_elide_attributes', false)) {
            $html = $this->elideAttributes($html);
        }

        if (setting('pageopt_minify_html', false)) {
            $html = $this->minify($html);
        }

        // Fail-safe: if transformed HTML is empty, preserve original HTML
        if (strlen(trim($html)) > 0) {
            $response->setContent($html);
        } else {
            $response->setContent($originalHtml);
        }

        return $response;
    }

    protected function deferExternalScripts(string $html): string
    {
        $excludes = array_filter(array_map('trim', explode("\n", (string) setting('pageopt_defer_exclude', ''))));

        return preg_replace_callback(
            '/<script\b([^>]*\bsrc=("|\')([^"\']+)\2[^>]*)>/i',
            function ($m) use ($excludes) {
                $attrs = $m[1];
                $src = $m[3];

                // Skip if already has defer/async
                if (preg_match('/\b(defer|async)\b/i', $attrs)) {
                    return $m[0];
                }

                // Skip if matches an exclude pattern
                foreach ($excludes as $pattern) {
                    if ($pattern !== '' && str_contains($src, $pattern)) {
                        return $m[0];
                    }
                }

                // Skip module scripts (already deferred by default)
                if (preg_match('/type=["\']module["\']/i', $attrs)) {
                    return $m[0];
                }

                return '<script'.$attrs.' defer>';
            },
            $html,
        ) ?? $html;
    }

    protected function inlineCriticalCss(string $html, string $css): string
    {
        // The whole point of critical CSS is to paint above-the-fold content
        // before the full stylesheet arrives. Do NOT hide <body> until the
        // deferred stylesheets load: that gate pushed FCP/LCP behind the slowest
        // CSS request (+2s failsafe) on mobile and cost ~20 Lighthouse points.
        // If the page flashes unstyled, the curated critical CSS is incomplete —
        // fix the CSS (Settings → Page Optimization), don't re-add a gate.
        $tag = '<style data-critical>'.$css.'</style>';

        if (stripos($html, '</head>') !== false) {
            return preg_replace('/<\/head>/i', $tag.'</head>', $html, 1) ?? $html;
        }

        return $tag.$html;
    }

    protected function deferStylesheets(string $html): string
    {
        $patterns = array_filter(array_map('trim', explode("\n", (string) setting('pageopt_deferred_stylesheets', ''))));
        if (empty($patterns)) {
            return $html;
        }

        // When the deferred stylesheet finally applies, every element whose
        // computed style differs from the critical-CSS state starts its CSS
        // transition at once (344 animations in one 200ms task on the CDT
        // homepage) and shifts layout. Suppress transitions while <html> carries
        // `css-pending`; the last deferred link's onload removes it. No-JS users
        // keep the class (transitions off) but still get the <noscript> sheet.
        $onload = 'this.onload=null;this.rel=\'stylesheet\';'
            .'if(!document.querySelector(\'link[data-deferred-css][rel=preload]\'))'
            .'document.documentElement.classList.remove(\'css-pending\')';

        $deferred = 0;
        $html = preg_replace_callback(
            '/<link\b([^>]*\brel=("|\')stylesheet\2[^>]*\bhref=("|\')([^"\']+)\3[^>]*)\/?>/i',
            function ($m) use ($patterns, $onload, &$deferred) {
                $href = $m[4];
                foreach ($patterns as $pat) {
                    if ($pat !== '' && str_contains($href, $pat)) {
                        $deferred++;
                        $rest = preg_replace('/\brel=("|\')stylesheet\1/', 'rel="preload" as="style" data-deferred-css onload="'.$onload.'"', $m[1]);

                        // Provide a <noscript> fallback for users without JS
                        return '<link'.$rest.'><noscript><link rel="stylesheet" href="'.htmlspecialchars($href, ENT_QUOTES).'"></noscript>';
                    }
                }

                return $m[0];
            },
            $html,
        ) ?? $html;

        if ($deferred === 0) {
            return $html;
        }

        $suppress = '<style data-critical-pending>html.css-pending *,html.css-pending *::before,html.css-pending *::after{transition:none!important}</style>';
        $html = preg_replace('/<\/head>/i', $suppress.'</head>', $html, 1) ?? $html;

        return preg_replace_callback('/<html\b([^>]*)>/i', function ($m) {
            $attrs = $m[1];
            if (preg_match('/\bclass=("|\')([^"\']*)\1/i', $attrs, $c)) {
                return '<html'.str_replace($c[0], 'class='.$c[1].trim($c[2].' css-pending').$c[1], $attrs).'>';
            }

            return '<html'.$attrs.' class="css-pending">';
        }, $html, 1) ?? $html;
    }

    protected function shouldProcess(Request $request, Response $response): bool
    {
        if ($response->getStatusCode() !== 200) {
            return false;
        }
        $contentType = (string) $response->headers->get('Content-Type', '');
        if ($contentType !== '' && ! str_contains($contentType, 'text/html')) {
            return false;
        }

        // Skip admin
        $adminPath = trim(config('admin.path', 'admin'), '/');
        if ($adminPath !== '' && str_starts_with(ltrim($request->path(), '/'), $adminPath)) {
            return false;
        }

        return true;
    }

    protected function rewriteCdn(string $html): string
    {
        $base = rtrim((string) setting('cdn_base_url', ''), '/');
        if ($base === '') {
            return $html;
        }

        $paths = array_filter(array_map('trim', explode("\n", (string) setting('cdn_paths_to_rewrite', ''))));
        $appUrl = rtrim((string) config('app.url'), '/');

        foreach ($paths as $path) {
            $needle = $appUrl.'/'.ltrim($path, '/');
            $html = str_replace($needle, $base.'/'.ltrim($path, '/'), $html);
            // Also rewrite root-relative
            $html = preg_replace(
                '#(["\'(])(/'.preg_quote(ltrim($path, '/'), '#').')#',
                '$1'.$base.'$2',
                $html,
            ) ?? $html;
        }

        return $html;
    }

    protected function stripQueryStrings(string $html): string
    {
        return preg_replace_callback(
            '/(href|src)=(["\'])([^"\']+\.(?:js|css))\?[^"\']*\2/i',
            fn ($m) => "{$m[1]}={$m[2]}{$m[3]}{$m[2]}",
            $html,
        ) ?? $html;
    }

    protected function lazyLoadImages(string $html): string
    {
        return preg_replace_callback(
            '/<img\b(?![^>]*\bloading=)([^>]*)>/i',
            fn ($m) => '<img'.$m[1].' loading="lazy">',
            $html,
        ) ?? $html;
    }

    protected function minify(string $html): string
    {
        // Protect <script>, <style>, <textarea>, <pre> contents from whitespace collapsing
        $placeholders = [];
        $html = preg_replace_callback('/<(script|style|textarea|pre)\b[^>]*>.*?<\/\1>/is', function ($m) use (&$placeholders) {
            $key = '___PROTECTED_TAG_'.count($placeholders).'___';
            $placeholders[$key] = $m[0];

            return $key;
        }, $html) ?? $html;

        // Protect Livewire/Alpine directive attributes from whitespace damage
        $html = preg_replace_callback('/\b(wire:snapshot|wire:effects|x-data|x-init)\s*=\s*(["\'])(.*?)\2/is', function ($m) use (&$placeholders) {
            $key = '___PROTECTED_TAG_'.count($placeholders).'___';
            $placeholders[$key] = $m[0];

            return $key;
        }, $html) ?? $html;

        // Strip HTML comments (preserve IE conditionals and Livewire markers)
        $html = preg_replace('/<!--(?!\[if|\s*\[if|\s*wire:).*?-->/s', '', $html) ?? $html;
        // Collapse whitespace between tags — keep ONE space for Livewire/Alpine.js compatibility
        $html = preg_replace('/> +</', '> <', $html) ?? $html;
        $html = preg_replace('/>\s*\n\s*</', '> <', $html) ?? $html;
        // Collapse runs of whitespace inside text nodes
        $html = preg_replace('/\s{2,}/', ' ', $html) ?? $html;

        // Restore protected tags
        if (! empty($placeholders)) {
            $html = strtr($html, $placeholders);
        }

        return trim($html);
    }

    /**
     * Inject <link rel="dns-prefetch"> for external domains found in the HTML.
     * Reduces DNS lookup latency for external resources.
     */
    protected function insertDnsPrefetch(string $html): string
    {
        // Extract URLs from HTML tag attributes (src, href)
        preg_match_all(
            '#<(?:link|img|a|iframe|video|audio|source|script)\s[^>]*\b(?:src|href)=["\']([^"\']+)["\']#i',
            $html,
            $matches
        );

        if (empty($matches[1])) {
            return $html;
        }

        // Filter to keep only external URLs (http:// or https://)
        $externalUrls = array_filter($matches[1], fn ($url) => preg_match('#^https?://#i', $url));

        if (empty($externalUrls)) {
            return $html;
        }

        // Extract unique domains and build prefetch links
        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        $domains = [];
        foreach ($externalUrls as $url) {
            $host = parse_url($url, PHP_URL_HOST);
            if ($host && $host !== $appHost && ! isset($domains[$host])) {
                $domains[$host] = true;
            }
        }

        if (empty($domains)) {
            return $html;
        }

        $prefetchTags = implode("\n", array_map(
            fn ($domain) => '<link rel="dns-prefetch" href="//'.$domain.'">',
            array_keys($domains)
        ));

        // Inject right after <head>
        if (stripos($html, '<head>') !== false) {
            return preg_replace('/<head>/i', '<head>'."\n".$prefetchTags, $html, 1) ?? $html;
        }

        return $html;
    }

    /**
     * Shorten redundant boolean HTML attributes and remove default method="get".
     * E.g.: disabled="disabled" → disabled, selected="selected" → selected
     */
    protected function elideAttributes(string $html): string
    {
        $replace = [
            '/ method=("get"|get)/i' => '',
            '/ disabled=[^ >]*/i' => ' disabled',
            '/ selected=[^ >]*/i' => ' selected',
            '/ readonly=[^ >]*/i' => ' readonly',
            '/ checked=[^ >]*/i' => ' checked',
            '/ required=[^ >]*/i' => ' required',
            '/ autofocus=[^ >]*/i' => ' autofocus',
            '/ autoplay=[^ >]*/i' => ' autoplay',
            '/ muted=[^ >]*/i' => ' muted',
            '/ loop=[^ >]*/i' => ' loop',
            '/ novalidate=[^ >]*/i' => ' novalidate',
            '/ multiple=[^ >]*/i' => ' multiple',
        ];

        return preg_replace(array_keys($replace), array_values($replace), $html) ?? $html;
    }
}
