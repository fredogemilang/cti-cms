<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\ThemeLoader;
use Illuminate\Support\Facades\View;

class PageController extends Controller
{
    public function show(?string $localeOrSlug = null, ?string $slug = null)
    {
        $targetSlug = $slug ?? $localeOrSlug;
        if (! $targetSlug) {
            abort(404);
        }

        // Locale-aware slug lookup — auto-switches app locale if matched on a translated slug.
        $page = Page::findByLocalizedSlug($targetSlug);
        abort_if(! $page, 404);

        // Redirect homepage slug to canonical root URL to avoid duplicate content.
        // The homepage has its own dedicated controller (HomeController) at '/'.
        if ($page->slug === 'home') {
            $locale = app()->getLocale();
            $default = setting('default_locale', config('app.locale', 'en'));
            $target = $locale !== $default ? url("/{$locale}") : url('/');

            return redirect($target, 301);
        }

        // Enforce canonical localized URL redirect (e.g. /id/about-us -> /id/tentang-kami)
        $canonicalUrl = $page->getUrl(app()->getLocale());
        $currentUrl = request()->url();
        if ($currentUrl !== $canonicalUrl) {
            $queryString = request()->getQueryString();

            return redirect($canonicalUrl.($queryString ? "?{$queryString}" : ''), 301);
        }

        // Load blocks (shared across locales for now)
        $page->load(['blocks' => function ($q) {
            $q->whereNull('parent_block_id')
                ->with('childBlocks')
                ->orderBy('order');
        }]);

        View::share('page', $page);

        $viewName = $this->resolveTemplate($page->template, $page->slug);

        return view($viewName, [
            'page' => $page,
            'blocks' => $page->blocks,
        ]);
    }

    protected function resolveTemplate(string $template, string $slug): string
    {
        // Use active theme's slug as namespace (null if no active theme)
        $theme = app(ThemeLoader::class)->getActiveTheme();
        $themeNamespace = $theme?->slug;

        $candidates = [];

        // Theme-specific candidates (only if a theme is active)
        if ($themeNamespace) {
            $candidates[] = "{$themeNamespace}::pages.{$slug}";
            $candidates[] = "{$themeNamespace}::pages.template-{$template}";
            $candidates[] = "{$themeNamespace}::pages.{$template}";
            $candidates[] = "{$themeNamespace}::pages.single";
        }

        // Default fallback candidates
        $candidates[] = "pages.{$slug}";
        $candidates[] = "pages.template-{$template}";
        $candidates[] = "pages.{$template}";
        $candidates[] = 'pages.single';
        $candidates[] = 'layouts.page';

        foreach ($candidates as $view) {
            if (View::exists($view)) {
                return $view;
            }
        }

        // Fallback to a basic page layout
        return 'pages.single';
    }

    /**
     * Preview a page (for draft or scheduled pages)
     */
    public function preview(int $id)
    {
        // Only allow preview for authenticated users with permission
        if (! auth()->check() || ! auth()->user()->hasPermission('pages.edit')) {
            abort(403);
        }

        $locale = request()->query('lang', app()->getLocale());
        if (in_array($locale, available_locales(), true)) {
            app()->setLocale($locale);
        }

        $page = Page::with(['blocks' => function ($q) {
            $q->whereNull('parent_block_id')
                ->with('childBlocks')
                ->orderBy('order');
        }])->findOrFail($id);

        View::share('page', $page);

        $viewName = $this->resolveTemplate($page->template, $page->slug);

        return view($viewName, [
            'page' => $page,
            'blocks' => $page->blocks,
            'isPreview' => true,
        ]);
    }
}
