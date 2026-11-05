<?php

namespace Themes\Kouventa\Handlers;

use App\Http\Controllers\PageController;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as BladeView;

class PricingRouteHandler
{
    /**
     * Pricing tab configurations.
     * Maps 'ai' and 'customer-engagement' to canonical slugs, aliases, and multilingual SEO metadata.
     */
    public static function getTabs(): array
    {
        return [
            'ai' => [
                'tab' => 'ai',
                'slugs' => ['en' => 'ai', 'id' => 'ai'],
                'aliases' => ['ai', 'artificial-intelligence'],
                'title' => [
                    'en' => 'Enterprise AI Pricing & Plans | Kouventa',
                    'id' => 'Harga & Paket Enterprise AI | Kouventa',
                ],
                'description' => [
                    'en' => 'Transparent pricing for autonomous enterprise AI agents. Choose from Starter, Grow, or Scale tiers with flexible custom integration.',
                    'id' => 'Harga transparan untuk agent AI enterprise otonom. Pilih dari paket Starter, Grow, atau Scale dengan integrasi kustom yang fleksibel.',
                ],
            ],
            'customer-engagement' => [
                'tab' => 'ce',
                'slugs' => ['en' => 'customer-engagement', 'id' => 'customer-engagement'],
                'aliases' => ['customer-engagement', 'ce', 'whatsapp', 'omnichannel', 'interaksi-pelanggan'],
                'title' => [
                    'en' => 'Customer Engagement Pricing & WhatsApp Calculator | Kouventa',
                    'id' => 'Harga Customer Engagement & Kalkulator WhatsApp | Kouventa',
                ],
                'description' => [
                    'en' => 'Omnichannel customer engagement pricing, WhatsApp conversation rate calculator, and unified 24/7 inbox tiers.',
                    'id' => 'Harga interaksi pelanggan omnichannel, kalkulator tarif percakapan WhatsApp, dan paket kotak masuk terpadu 24/7.',
                ],
            ],
        ];
    }

    /**
     * Generate localized clean URL for a pricing tab ('ai' or 'ce' / 'customer-engagement').
     */
    public static function url(string $tab = 'ai', ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $tabLower = strtolower(trim($tab));

        foreach (self::getTabs() as $canonicalKey => $config) {
            if ($tabLower === $canonicalKey || $tabLower === $config['tab'] || in_array($tabLower, $config['aliases'], true)) {
                $slug = $config['slugs'][$locale] ?? $config['slugs']['en'];
                return localized_url("/pricing/{$slug}", $locale);
            }
        }

        return localized_url('/pricing/ai', $locale);
    }

    /**
     * Get all pricing sitemap URLs for XML sitemap inclusion.
     */
    public static function getAllPricingSitemapUrls(): array
    {
        $items = [];
        $locales = available_locales();

        foreach (self::getTabs() as $config) {
            foreach ($locales as $loc) {
                $slug = $config['slugs'][$loc] ?? $config['slugs']['en'];
                $items[] = [
                    'loc' => localized_url("/pricing/{$slug}", $loc),
                    'lastmod' => now()->toDateString(),
                    'changefreq' => 'weekly',
                    'priority' => 0.8,
                    'type' => 'Page',
                    'images' => [],
                ];
            }
        }

        return $items;
    }

    /**
     * Handle base /pricing route.
     * Redirects legacy ?tab=X to clean SEO URLs, or redirects /pricing to /pricing/ai/.
     */
    public function showBase(Request $request, ?string $locale = null): RedirectResponse
    {
        $currentLocale = $locale ?? app()->getLocale();
        if ($locale && in_array($locale, available_locales(), true)) {
            app()->setLocale($locale);
        }

        $tab = $request->query('tab');
        if ($tab !== null) {
            $targetUrl = self::url($tab, $currentLocale);
            return redirect($targetUrl, 301);
        }

        // Default canonical redirect: /pricing -> /pricing/ai/
        return redirect(self::url('ai', $currentLocale), 301);
    }

    /**
     * Handle clean pricing tab routes: /pricing/{tab}
     */
    public function showTab(Request $request, ?string $locale = null, string $tab = ''): BladeView|RedirectResponse
    {
        $currentLocale = $locale ?? app()->getLocale();
        if ($locale && in_array($locale, available_locales(), true)) {
            app()->setLocale($locale);
        }

        $tabLower = strtolower(trim($tab));
        $matchedConfig = null;

        foreach (self::getTabs() as $canonicalKey => $config) {
            if ($tabLower === $canonicalKey || $tabLower === $config['tab'] || in_array($tabLower, $config['aliases'], true)) {
                $matchedConfig = $config;
                break;
            }
        }

        abort_if(! $matchedConfig, 404);

        $canonicalSlug = $matchedConfig['slugs'][$currentLocale] ?? $matchedConfig['slugs']['en'];

        // Enforce canonical slug redirect (e.g. /pricing/ce 301 redirects to /pricing/customer-engagement/)
        if ($tabLower !== strtolower($canonicalSlug)) {
            $targetUrl = localized_url("/pricing/{$canonicalSlug}", $currentLocale);
            return redirect($targetUrl, 301);
        }

        // Fetch base pricing page from KouventaPage or Core Page
        $basePage = null;
        if (class_exists(\Plugins\KouventaPages\Models\KouventaPage::class)) {
            $basePage = \Plugins\KouventaPages\Models\KouventaPage::where('slug', 'pricing')->first()
                ?: \Plugins\KouventaPages\Models\KouventaPage::findByLocalizedSlug('pricing');
        }
        if (! $basePage) {
            $basePage = Page::published()->where('slug', 'pricing')->first()
                ?: Page::findByLocalizedSlug('pricing');
        }
        abort_if(! $basePage, 404);

        if (method_exists($basePage, 'blocks')) {
            $basePage->load(['blocks' => function ($q) {
                $q->whereNull('parent_block_id')
                    ->with('childBlocks')
                    ->orderBy('order');
            }]);
        }

        // Build virtual Page for SEO injection and Breadcrumbs
        $virtualPage = clone $basePage;
        $virtualPage->id = null;
        $virtualPage->exists = false;
        $virtualPage->title = $matchedConfig['title'][$currentLocale] ?? $matchedConfig['title']['en'];
        $virtualPage->excerpt = $matchedConfig['description'][$currentLocale] ?? $matchedConfig['description']['en'];
        if (isset($virtualPage->seo) && is_array($virtualPage->seo)) {
            $virtualPage->seo = array_merge($virtualPage->seo, [
                'title' => $matchedConfig['title'][$currentLocale] ?? $matchedConfig['title']['en'],
                'description' => $matchedConfig['description'][$currentLocale] ?? $matchedConfig['description']['en'],
            ]);
        }
        $virtualPage->slug = "pricing/{$canonicalSlug}";
        $virtualPage->translations = [
            'title' => [
                'en' => $matchedConfig['title']['en'],
                'id' => $matchedConfig['title']['id'],
            ],
            'slug' => [
                'en' => "pricing/{$matchedConfig['slugs']['en']}",
                'id' => "pricing/{$matchedConfig['slugs']['id']}",
            ],
            'excerpt' => [
                'en' => $matchedConfig['description']['en'],
                'id' => $matchedConfig['description']['id'],
            ],
        ];

        View::share('page', $virtualPage);
        $request->attributes->set('page', $virtualPage);

        return view('kouventa::pages.pricing', [
            'page' => $virtualPage,
            'basePage' => $basePage,
            'blocks' => $basePage->blocks ?? collect(),
            'initialProduct' => $matchedConfig['tab'], // 'ai' or 'ce'
            'activeSlug' => $canonicalSlug,
        ]);
    }
}
