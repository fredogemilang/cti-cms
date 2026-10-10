<?php

namespace Themes\Cdt\Http\Controllers;

use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\PageController;
use App\Models\CptEntry;
use App\Models\CustomPostType;

/**
 * CDT short URLs:
 *   /{vendor}            → technology-alliance entry (falls back to a Page)
 *   /{vendor}/{product}  → tech-products entry
 *
 * Routes are registered by CdtThemeServiceProvider via the
 * `routes.before_catch_all` action.
 */
class ShortUrlController extends ArchiveController
{
    /**
     * Short Vendor Single — GET /{vendorSlug}
     */
    public function shortVendorSingle(string $vendorSlug)
    {
        return $this->localeShortVendorSingle(app()->getLocale(), $vendorSlug);
    }

    public function localeShortVendorSingle(string $locale, string $vendorSlug)
    {
        if (in_array($locale, available_locales(), true)) {
            app()->setLocale($locale);
        }

        $techAllianceCpt = CustomPostType::where('slug', 'technology-alliance')
            ->where('is_active', true)
            ->first();

        if ($techAllianceCpt) {
            $entry = CptEntry::findByLocalizedSlug($techAllianceCpt, $vendorSlug);
            if ($entry && $entry->status === 'published') {
                $entry->load(['author', 'postType', 'terms.taxonomy']);
                $this->shareEntry($entry);
                $taxonomies = $techAllianceCpt->taxonomies();
                $viewName = $this->resolveSingleView($techAllianceCpt->slug);

                return view($viewName, [
                    'postType' => $techAllianceCpt,
                    'entry' => $entry,
                    'seo' => $entry->seo ?? [],
                    'taxonomies' => $taxonomies,
                    'previousEntry' => $entry->getPreviousEntry(),
                    'nextEntry' => $entry->getNextEntry(),
                ]);
            }
        }

        // Fallback to regular Page Controller if vendor entry not found
        return app(PageController::class)->show($vendorSlug);
    }

    /**
     * Short Product Single — GET /{vendorSlug}/{productSlug}
     */
    public function shortProductSingle(string $vendorSlug, string $productSlug)
    {
        return $this->localeShortProductSingle(app()->getLocale(), $vendorSlug, $productSlug);
    }

    public function localeShortProductSingle(string $locale, string $vendorSlug, string $productSlug)
    {
        if (in_array($locale, available_locales(), true)) {
            app()->setLocale($locale);
        }

        $productCpt = CustomPostType::whereIn('slug', ['tech-products', 'products'])
            ->where('is_active', true)
            ->first();

        if ($productCpt) {
            $entry = CptEntry::findByLocalizedSlug($productCpt, $productSlug);
            if ($entry && $entry->status === 'published') {
                $entry->load(['author', 'postType', 'terms.taxonomy']);
                $this->shareEntry($entry);
                $taxonomies = $productCpt->taxonomies();
                $viewName = $this->resolveSingleView($productCpt->slug, true, 'technology-alliance');

                return view($viewName, [
                    'postType' => $productCpt,
                    'entry' => $entry,
                    'seo' => $entry->seo ?? [],
                    'taxonomies' => $taxonomies,
                    'previousEntry' => $entry->getPreviousEntry(),
                    'nextEntry' => $entry->getNextEntry(),
                    'parentVendorSlug' => $vendorSlug,
                ]);
            }
        }

        abort(404);
    }
}
