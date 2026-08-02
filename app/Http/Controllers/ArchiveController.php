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

    /**
     * Preview CPT Entry by ID for logged-in users — GET /admin/cpt-entries/{id}/preview
     */
    public function previewById(int $id)
    {
        abort_unless(auth()->check() && auth()->user()->can('cpt.entries.edit'), 403);
        $entry = CptEntry::with(['author', 'postType', 'terms.taxonomy'])->findOrFail($id);
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
        $postType = CustomPostType::where('slug', $cptSlug)
            ->where('is_active', true)
            ->where('has_archive', true)
            ->firstOrFail();

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
        $postType = CustomPostType::where('slug', $cptSlug)
            ->where('is_active', true)
            ->where('publicly_queryable', true)
            ->firstOrFail();

        $entry = CptEntry::findByLocalizedSlug($postType, $entrySlug);
        abort_if(! $entry, 404);

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
            $entry = CptEntry::where('post_type_id', $postType->id)
                ->where('slug', $entrySlug)
                ->where('status', 'published')
                ->first();
        }
        abort_if(! $entry, 404);

        $entry->load(['author', 'postType', 'terms.taxonomy']);

        $redirectUrl = apply_filters('cpt_entry.url_redirect', null, $entry);
        if ($redirectUrl) {
            return redirect($redirectUrl, 301);
        }

        $taxonomies = $postType->taxonomies();
        $previousEntry = $entry->getPreviousEntry();
        $nextEntry = $entry->getNextEntry();

        $postTypeSlug = $postType->slug;
        $viewName = $this->resolveSingleView($postTypeSlug, true);

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
     * Priority: {theme}::single-{cpt} → {theme}::single-entry → single-{cpt} → single-entry
     */
    protected function resolveSingleView(string $cptSlug, bool $isNested = false): string
    {
        $theme = app(ThemeLoader::class)->getActiveTheme();
        $ns = $theme?->slug;

        $candidates = [];

        if ($ns) {
            if ($isNested) {
                $candidates[] = "{$ns}::single-sub-{$cptSlug}";
            }
            $candidates[] = "{$ns}::single-{$cptSlug}";
            $candidates[] = "{$ns}::single-entry";
        }

        if ($isNested) {
            $candidates[] = "single-sub-{$cptSlug}";
        }
        $candidates[] = "single-{$cptSlug}";
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
