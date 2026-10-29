<?php

namespace Plugins\KouventaPages\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Plugins\KouventaPages\Models\KouventaPage;

class KouventaPageController extends Controller
{
    /**
     * Display the specified Kouventa interactive page.
     */
    public function show(Request $request, ?string $locale = null, ?string $slug = null)
    {
        // Handle routes where locale is omitted: /product/{sub}, /solutions/{sub}, /pricing
        if ($slug === null) {
            $slug = $locale;
            $locale = null;
        }

        if ($locale && in_array($locale, available_locales(), true)) {
            app()->setLocale($locale);
        }

        $slug = trim($slug, '/');

        // Look up by primary slug or localized slug
        $page = KouventaPage::where('slug', $slug)->first();
        if (! $page) {
            $page = KouventaPage::findByLocalizedSlug($slug);
        }

        if (! $page) {
            abort(404);
        }

        // Draft preview access check
        if ($page->status !== 'published') {
            if (! $request->has('preview') || ! auth()->check()) {
                abort(404);
            }
        }

        // Set attributes for SEO and global middleware
        $request->attributes->set('page', $page);
        View::share('page', $page);

        // Resolve blade view from active theme
        $viewName = "kouventa::pages.{$page->template}";
        if (! View::exists($viewName)) {
            $viewName = "pages.{$page->template}";
        }

        return view($viewName, [
            'page' => $page,
        ]);
    }
}

