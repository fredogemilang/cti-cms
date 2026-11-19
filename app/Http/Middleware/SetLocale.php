<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Don't modify locale for admin requests
        $adminPath = config('admin.path', 'ctrlpanel');
        if ($request->is($adminPath) || $request->is($adminPath.'/*')) {
            return $next($request);
        }

        $structure = setting('locale_url_structure', 'prefix');
        $defaultLocale = setting('default_locale', config('app.locale', 'en'));
        $hideDefault = (bool) setting('locale_prefix_hide_default', true);

        // Check URL path prefix (e.g. /id/... or /en/...)
        $firstSegment = $request->segment(1);

        if ($firstSegment && static::isAllowed($firstSegment)) {
            session(['locale' => $firstSegment]);
            cookie()->queue('locale', $firstSegment, 60 * 24 * 365);
            app()->setLocale($firstSegment);
        } elseif ($request->has('locale')) {
            $candidate = $request->query('locale');
            if (static::isAllowed($candidate)) {
                session(['locale' => $candidate]);
                cookie()->queue('locale', $candidate, 60 * 24 * 365);
                app()->setLocale($candidate);
            }
        } else {
            if ($structure === 'prefix' && $hideDefault) {
                // When prefix structure is used and hideDefault is true,
                // a URL WITHOUT a locale prefix explicitly means the DEFAULT locale!
                session(['locale' => $defaultLocale]);
                cookie()->queue('locale', $defaultLocale, 60 * 24 * 365);
                app()->setLocale($defaultLocale);
            } else {
                // Precedence: session > cookie > stored default > app config
                $locale = session('locale')
                    ?? $request->cookie('locale')
                    ?? $defaultLocale;

                if (static::isAllowed($locale)) {
                    app()->setLocale($locale);
                }
            }
        }

        // Fallback locale comes from settings if set
        $fallback = setting('fallback_locale', null);
        if ($fallback && static::isAllowed($fallback)) {
            app()->setFallbackLocale($fallback);
        }

        return $next($request);
    }

    protected static function isAllowed(?string $candidate): bool
    {
        if (! $candidate) {
            return false;
        }

        return in_array($candidate, available_locales(), true);
    }
}
