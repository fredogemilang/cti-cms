<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\ThemeLoader;
use App\Support\Filter;
use Illuminate\Support\Facades\View;

/**
 * Public homepage controller. Renders the active theme's home view with:
 *   - $page — Page (slug=home) with eager-loaded top-level blocks
 *
 * Themes add their own home data (testimonials, partners, ...) through the
 * `home.view_data` filter: Filter::add('home.view_data', fn (array $data, Page $page) => $data + [...]).
 */
class HomeController extends Controller
{
    public function index()
    {
        $theme = app(ThemeLoader::class)->getActiveTheme();
        $themeNamespace = $theme?->slug;

        $candidates = array_filter([
            ! empty($themeNamespace) ? "{$themeNamespace}::pages.home" : null,
            'pages.home',
            'default::pages.home',
        ]);

        $viewName = 'welcome';
        foreach ($candidates as $candidate) {
            if (view()->exists($candidate)) {
                $viewName = $candidate;
                break;
            }
        }

        $page = $this->loadHomePage();
        abort_if(! $page, 404, 'Homepage not found. Create a page with slug "home".');

        request()->attributes->set('page', $page);
        View::share('page', $page);

        $data = Filter::apply('home.view_data', ['page' => $page], $page);

        return view($viewName, $data);
    }

    protected function loadHomePage(): ?Page
    {
        return Page::with(['blocks' => function ($q) {
            $q->whereNull('parent_block_id')->orderBy('order');
        }])
            ->where('slug', 'home')
            ->first();
    }
}
