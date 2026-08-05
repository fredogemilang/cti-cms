<?php

namespace App\Http\Controllers;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\CustomTaxonomy;
use App\Models\TaxonomyTerm;
use App\Services\ThemeLoader;
use Illuminate\Support\Facades\View;

class ArchiveController extends Controller
{
    /**
     * Localized CPT archive listing — GET /{locale}/{cpt-slug}
     */
    public function localeArchive(string $locale, string $cptSlug)
    {
        if (in_array($locale, available_locales())) {
            app()->setLocale($locale);
        }

        return $this->archive($cptSlug);
    }

    /**
     * Localized Single CPT entry — GET /{locale}/{cpt-slug}/{entry-slug}
     */
    public function localeSingle(string $locale, string $cptSlug, string $entrySlug)
    {
        if (in_array($locale, available_locales())) {
            app()->setLocale($locale);
        }

        return $this->single($cptSlug, $entrySlug);
    }

    /**
     * Localized Nested CPT entry — GET /{locale}/{cpt-slug}/{parentSlug}/{entrySlug}
     */
    public function localeNestedSingle(string $locale, string $cptSlug, string $parentSlug, string $entrySlug)
    {
        if (in_array($locale, available_locales())) {
            app()->setLocale($locale);
        }

        return $this->nestedSingle($cptSlug, $parentSlug, $entrySlug);
    }

    protected function shareEntry(CptEntry $entry): void
    {
        request()->attributes->set('cpt_entry', $entry);
        request()->attributes->set('entry', $entry);
        View::share('cpt_entry', $entry);
        View::share('entry', $entry);
    }

    /**
     * Preview CPT Entry by ID for logged-in users — GET /admin/cpt-entries/{id}/preview
     */
    public function previewById(int $id)
    {
        abort_unless(auth()->check() && auth()->user()->can('cpt.entries.edit'), 403);
        $entry = CptEntry::with(['author', 'postType', 'terms.taxonomy'])->findOrFail($id);
        $this->shareEntry($entry);
        $postType = $entry->postType;

        $viewName = $this->resolveSingleView($postType->slug);

        return view($viewName, [
            'postType' => $postType,
            'entry' => $entry,
            'seo' => $entry->seo ?? [],
            'taxonomies' => $postType->taxonomies(),
            'previousEntry' => null,
            'nextEntry' => null,
            'isPreview' => true,
        ]);
    }

    /**
     * Localized Taxonomy term archive — GET /{locale}/{taxonomy-slug}/{term-slug}
     */
    public function localeTermArchive(string $locale, string $taxonomySlug, string $termSlug)
    {
        if (in_array($locale, available_locales())) {
            app()->setLocale($locale);
        }

        return $this->termArchive($taxonomySlug, $termSlug);
    }

    /**
     * CPT archive listing — GET /{cpt-slug}
     */
    public function archive(string $cptSlug)
    {
        $currentLocale = app()->getLocale();

        $postType = CustomPostType::where(function ($q) use ($cptSlug) {
            $q->where('slug', $cptSlug)
                ->orWhereRaw('JSON_EXTRACT(translations, "$.id.slug") = ?', [$cptSlug])
                ->orWhereRaw('JSON_EXTRACT(translations, "$.en.slug") = ?', [$cptSlug]);
        })
            ->where('is_active', true)
            ->where('has_archive', true)
            ->firstOrFail();

        $targetSlug = $postType->getLocalizedSlug($currentLocale);
        if ($cptSlug !== $targetSlug) {
            $queryString = request()->getQueryString();

            return redirect($postType->getArchiveUrl($currentLocale).($queryString ? "?{$queryString}" : ''), 301);
        }

        $perPage = in_array($postType->slug, ['customer-success', 'client-says'], true) ? 6 : $this->getArchiveSetting('per_page', 12);

        $entries = CptEntry::with(['author', 'postType', 'terms.taxonomy'])
            ->where('post_type_id', $postType->id)
            ->published()
            ->latest('published_at')
            ->paginate($perPage);

        $taxonomies = $postType->taxonomies();

        $viewName = $this->resolveArchiveView($postType->slug);

        return view($viewName, [
            'postType' => $postType,
            'entries' => $entries,
            'taxonomies' => $taxonomies,
        ]);
    }

    /**
     * Single CPT entry — GET /{cpt-slug}/{entry-slug}
     */
    public function single(string $cptSlug, string $entrySlug)
    {
        $currentLocale = app()->getLocale();

        $postType = CustomPostType::where(function ($q) use ($cptSlug) {
            $q->where('slug', $cptSlug)
                ->orWhereRaw('JSON_EXTRACT(translations, "$.id.slug") = ?', [$cptSlug])
                ->orWhereRaw('JSON_EXTRACT(translations, "$.en.slug") = ?', [$cptSlug]);
        })
            ->where('is_active', true)
            ->where('publicly_queryable', true)
            ->firstOrFail();

        $entry = CptEntry::findByLocalizedSlug($postType, $entrySlug);
        abort_if(! $entry, 404);

        $this->shareEntry($entry);

        $canonicalUrl = $entry->getUrl($currentLocale);
        $currentUrl = request()->url();
        if ($currentUrl !== $canonicalUrl) {
            $queryString = request()->getQueryString();

            return redirect($canonicalUrl.($queryString ? "?{$queryString}" : ''), 301);
        }

        $entry->load(['author', 'postType', 'terms.taxonomy']);

        $redirectUrl = apply_filters('cpt_entry.url_redirect', null, $entry);
        if ($redirectUrl) {
            return redirect($redirectUrl, 301);
        }

        $taxonomies = $postType->taxonomies();
        $previousEntry = $entry->getPreviousEntry();
        $nextEntry = $entry->getNextEntry();

        $viewName = $this->resolveSingleView($postType->slug);

        return view($viewName, [
            'postType' => $postType,
            'entry' => $entry,
            'seo' => $entry->seo ?? [],
            'taxonomies' => $taxonomies,
            'previousEntry' => $previousEntry,
            'nextEntry' => $nextEntry,
        ]);
    }

    /**
     * Nested CPT entry — GET /{cpt-slug}/{parentSlug}/{entrySlug}
     */
    public function nestedSingle(string $cptSlug, string $parentSlug, string $entrySlug)
    {
        $postType = CustomPostType::where('slug', $cptSlug)
            ->where('is_active', true)
            ->where('publicly_queryable', true)
            ->firstOrFail();

        $entry = CptEntry::findByLocalizedSlug($postType, $entrySlug);
        if (! $entry) {
            $entry = CptEntry::where('slug', $entrySlug)
                ->where('status', 'published')
                ->first();
        }
        if (! $entry) {
            $entry = CptEntry::where(function ($q) use ($entrySlug) {
                $q->whereRaw('JSON_EXTRACT(translations, "$.id.slug") = ?', [$entrySlug])
                    ->orWhereRaw('JSON_EXTRACT(translations, "$.en.slug") = ?', [$entrySlug]);
            })
                ->where('status', 'published')
                ->first();
        }
        abort_if(! $entry, 404);

        $entry->load(['author', 'postType', 'terms.taxonomy']);
        $this->shareEntry($entry);

        $redirectUrl = apply_filters('cpt_entry.url_redirect', null, $entry);
        if ($redirectUrl) {
            return redirect($redirectUrl, 301);
        }

        $taxonomies = $postType->taxonomies();
        $previousEntry = $entry->getPreviousEntry();
        $nextEntry = $entry->getNextEntry();

        $postTypeSlug = $postType->slug;
        $entryCptSlug = ($entry->postType instanceof CustomPostType) ? $entry->postType->slug : $postTypeSlug;
        $viewName = $this->resolveSingleView($entryCptSlug, true, $postTypeSlug);

        return view($viewName, [
            'postType' => $postType,
            'entry' => $entry,
            'seo' => $entry->seo ?? [],
            'taxonomies' => $taxonomies,
            'previousEntry' => $previousEntry,
            'nextEntry' => $nextEntry,
        ]);
    }

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

    /**
     * Taxonomy term archive — GET /{taxonomy-slug}/{term-slug}
     */
    public function termArchive(string $taxonomySlug, string $termSlug)
    {
        $taxonomy = CustomTaxonomy::where('slug', $taxonomySlug)
            ->where('is_active', true)
            ->firstOrFail();

        $term = TaxonomyTerm::where('taxonomy_id', $taxonomy->id)
            ->where('slug', $termSlug)
            ->firstOrFail();

        $perPage = $this->getArchiveSetting('per_page', 12);

        $entries = $term->entries()
            ->with(['author', 'postType', 'terms.taxonomy'])
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->paginate($perPage);

        // Get sibling terms for navigation
        $terms = TaxonomyTerm::where('taxonomy_id', $taxonomy->id)
            ->withCount(['entries' => function ($q) {
                $q->where('status', 'published')->where('published_at', '<=', now());
            }])
            ->orderBy('order')
            ->get();

        $viewName = $this->resolveTermView($taxonomy->slug);

        return view($viewName, [
            'taxonomy' => $taxonomy,
            'term' => $term,
            'terms' => $terms,
            'entries' => $entries,
        ]);
    }

    /**
     * Resolve the view for a CPT archive page.
     *
     * Priority: {theme}::archive-{cpt} → {theme}::archive → archive-{cpt} → archive
     */
    protected function resolveArchiveView(string $cptSlug): string
    {
        $theme = app(ThemeLoader::class)->getActiveTheme();
        $ns = $theme?->slug;

        $candidates = [];

        if ($ns) {
            $candidates[] = "{$ns}::archive-{$cptSlug}";
            $candidates[] = "{$ns}::archive";
        }

        $candidates[] = "archive-{$cptSlug}";
        $candidates[] = 'archive';

        foreach ($candidates as $view) {
            if (View::exists($view)) {
                return $view;
            }
        }

        return 'archive';
    }

    /**
     * Resolve the view for a single CPT entry.
     *
     * Priority: {theme}::single-sub-{cpt} → {theme}::single-{cpt} → {theme}::single-entry → single-entry
     */
    protected function resolveSingleView(string $cptSlug, bool $isNested = false, ?string $fallbackCptSlug = null): string
    {
        $theme = app(ThemeLoader::class)->getActiveTheme();
        $ns = $theme?->slug;

        $candidates = [];

        if ($ns) {
            if ($isNested) {
                $candidates[] = "{$ns}::single-sub-{$cptSlug}";
                if ($fallbackCptSlug && $fallbackCptSlug !== $cptSlug) {
                    $candidates[] = "{$ns}::single-sub-{$fallbackCptSlug}";
                }
            }
            $candidates[] = "{$ns}::single-{$cptSlug}";
            if ($fallbackCptSlug && $fallbackCptSlug !== $cptSlug) {
                $candidates[] = "{$ns}::single-{$fallbackCptSlug}";
            }
            $candidates[] = "{$ns}::single-entry";
        }

        if ($isNested) {
            $candidates[] = "single-sub-{$cptSlug}";
            if ($fallbackCptSlug && $fallbackCptSlug !== $cptSlug) {
                $candidates[] = "single-sub-{$fallbackCptSlug}";
            }
        }
        $candidates[] = "single-{$cptSlug}";
        if ($fallbackCptSlug && $fallbackCptSlug !== $cptSlug) {
            $candidates[] = "single-{$fallbackCptSlug}";
        }
        $candidates[] = 'single-entry';

        foreach ($candidates as $view) {
            if (View::exists($view)) {
                return $view;
            }
        }

        return 'single-entry';
    }

    /**
     * Resolve the view for a taxonomy term archive.
     *
     * Priority: {theme}::taxonomy-{slug} → {theme}::archive → taxonomy-{slug} → archive
     */
    protected function resolveTermView(string $taxonomySlug): string
    {
        $theme = app(ThemeLoader::class)->getActiveTheme();
        $ns = $theme?->slug;

        $candidates = [];

        if ($ns) {
            $candidates[] = "{$ns}::taxonomy-{$taxonomySlug}";
            $candidates[] = "{$ns}::archive";
        }

        $candidates[] = "taxonomy-{$taxonomySlug}";
        $candidates[] = 'archive';

        foreach ($candidates as $view) {
            if (View::exists($view)) {
                return $view;
            }
        }

        return 'archive';
    }

    /**
     * Get archive setting from theme.json or fallback.
     */
    protected function getArchiveSetting(string $key, mixed $default = null): mixed
    {
        $theme = app(ThemeLoader::class)->getActiveTheme();
        if (! $theme) {
            return $default;
        }

        $config = $theme->loadConfig();
        $settings = $config['archive_settings'] ?? [];

        return $settings[$key] ?? $default;
    }
}
