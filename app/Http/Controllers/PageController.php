<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\ThemeLoader;
use Illuminate\Support\Facades\View;

class PageController extends Controller
{
    public function show(string $slug)
    {
        // Locale-aware slug lookup — auto-switches app locale if matched on a translated slug.
        $page = Page::findByLocalizedSlug($slug);
        abort_if(! $page, 404);

        // Load blocks (shared across locales for now)
        $page->load(['blocks' => function ($q) {
            $q->whereNull('parent_block_id')
                ->with('childBlocks')
                ->orderBy('order');
        }]);

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
            $candidates[] = "{$themeNamespace}::pages.single";
        }

        // Default fallback candidates
        $candidates[] = "pages.{$slug}";
        $candidates[] = "pages.template-{$template}";
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

        $page = Page::with(['blocks' => function ($q) {
            $q->whereNull('parent_block_id')
                ->with('childBlocks')
                ->orderBy('order');
        }])->findOrFail($id);

        $viewName = $this->resolveTemplate($page->template, $page->slug);

        return view($viewName, [
            'page' => $page,
            'blocks' => $page->blocks,
            'isPreview' => true,
        ]);
    }
}
