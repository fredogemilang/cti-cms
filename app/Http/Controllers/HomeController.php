<?php

namespace App\Http\Controllers;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Page;
use App\Services\ThemeLoader;

/**
 * Public homepage controller. Pulls the active theme's home view with:
 *   - $page          — Page (slug=home) with eager-loaded top-level blocks
 *   - $testimonials  — latest 6 published testimonial CPT entries
 *   - $partners      — all published "our-partners" CPT entries
 *
 * Themes can rely on these always being defined (empty collection if no data).
 */
class HomeController extends Controller
{
    public function index()
    {
        $theme = app(ThemeLoader::class)->getActiveTheme();
        $themeNamespace = $theme?->slug;

        // Resolve home view: active theme namespace first, then global pages.home, default theme, or welcome fallback
        $viewName = match (true) {
            ! empty($themeNamespace) && view()->exists("{$themeNamespace}::pages.home") => "{$themeNamespace}::pages.home",
            view()->exists('pages.home') => 'pages.home',
            view()->exists('default::pages.home') => 'default::pages.home',
            default => 'welcome',
        };

        $page = $this->loadHomePage();
        abort_if(! $page, 404, 'Homepage not found. Create a page with slug "home".');

        return view($viewName, [
            'testimonials' => $this->latestEntries('client-says'),
            'partners' => $this->partnerEntries(),
            'page' => $page,
        ]);
    }

    protected function latestEntries(string $cptSlug, ?int $limit = null)
    {
        $cpt = CustomPostType::where('slug', $cptSlug)->first();
        if (! $cpt) {
            return collect();
        }

        $q = CptEntry::with('author')
            ->where('post_type_id', $cpt->id)
            ->where('status', 'published')
            ->latest();

        if ($limit) {
            $q->take($limit);
        }

        return $q->get();
    }

    protected function partnerEntries()
    {
        $cpt = CustomPostType::where('slug', 'technology-alliance')->first();
        if (! $cpt) {
            return collect();
        }

        return CptEntry::where('post_type_id', $cpt->id)
            ->where('status', 'published')
            ->orderBy('title')
            ->get();
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
