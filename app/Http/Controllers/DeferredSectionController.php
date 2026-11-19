<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\ThemeLoader;
use App\Support\Filter;
use Illuminate\Http\Request;

/**
 * Serves deferred HTML fragments for below-fold homepage sections.
 * These are loaded via AJAX after initial page load to reduce
 * the initial HTML document size.
 *
 * Renders `{theme}::partials.deferred.{section}`. Themes opt in per section:
 *   Filter::add('deferred_section.allowed', fn (array $sections) => [...$sections, 'testimonials']);
 *   Filter::add('deferred_section.data', fn (array $data, string $section) => $data + [...]);
 */
class DeferredSectionController extends Controller
{
    public function show(Request $request, string $section)
    {
        $allowed = (array) Filter::apply('deferred_section.allowed', []);
        if (! in_array($section, $allowed, true)) {
            abort(404);
        }

        // The AJAX URL /_deferred/{section} has no locale prefix, so the
        // SetLocale middleware falls back to the default locale.  Accept an
        // explicit ?locale= parameter from the JS caller so the correct
        // language is used when rendering the section.
        $locale = $request->query('locale');
        if ($locale && in_array($locale, available_locales(), true)) {
            app()->setLocale($locale);
        }

        $theme = app(ThemeLoader::class)->getActiveTheme();
        $themeNs = $theme?->slug ?? 'default';
        $view = "{$themeNs}::partials.deferred.{$section}";

        abort_unless(view()->exists($view), 404);

        $page = Page::with(['blocks' => fn ($q) => $q->whereNull('parent_block_id')->orderBy('order')])
            ->where('slug', 'home')
            ->first();

        $data = Filter::apply('deferred_section.data', ['page' => $page], $section, $page);

        return response(view($view, $data)->render())
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->header('X-LiteSpeed-Cache-Control', 'no-cache');
    }
}
