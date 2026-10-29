<?php

namespace Plugins\KouventaPages\Livewire;

use App\Http\Middleware\PageCache;
use App\Jobs\IndexSearchable;
use App\Services\CacheManager;
use App\Services\Sitemap\SitemapBuilder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Plugins\KouventaPages\Models\KouventaPage;
use Plugins\KouventaPages\Models\KouventaPageRevision;
use Plugins\KouventaPages\Schemas\PricingSchema;
use Plugins\KouventaPages\Schemas\ProductAiSchema;
use Plugins\KouventaPages\Schemas\ProductCeSchema;
use Plugins\KouventaPages\Schemas\SolutionAiSchema;
use Plugins\KouventaPages\Schemas\SolutionCeSchema;

class PageEditor extends Component
{
    public $pageId;

    public ?KouventaPage $page = null;

    #[Url]
    public string $activeTab = 'hero';

    #[Url]
    public string $editingLocale = 'en';

    public string $title = '';

    public string $slug = '';

    public string $status = 'published';

    public array $sections = [];

    public array $translations = [];

    public array $tabs = [];

    public array $revisions = [];

    public bool $isSaving = false;

    public function mount($pageId)
    {
        $this->pageId = $pageId;
        $this->page = KouventaPage::findOrFail($pageId);

        $this->title = (string) $this->page->getRawAttribute('title');
        $this->slug = (string) $this->page->slug;
        $this->status = (string) ($this->page->status ?? 'published');
        $this->translations = $this->page->translations ?? [];

        $this->tabs = $this->resolveTabs();
        $this->sections = $this->resolveSectionsForLocale($this->editingLocale);
        $this->refreshRevisions();

        if (empty($this->activeTab) || ! collect($this->tabs)->contains('id', $this->activeTab)) {
            $this->activeTab = $this->tabs[0]['id'] ?? 'hero';
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function setLocale(string $locale): void
    {
        if ($locale === $this->editingLocale) {
            return;
        }

        // 1. Stash current working sections into the active locale buffer
        $this->stashCurrentLocaleData();

        // 2. Switch locale
        $this->editingLocale = $locale;

        // 3. Load sections for the new locale
        $this->sections = $this->resolveSectionsForLocale($locale);

        // 4. Notify SEO box
        $this->dispatch('seo-locale-switched', locale: $locale);
    }

    /**
     * Stash current sections in memory before switching locale or saving.
     */
    protected function stashCurrentLocaleData(): void
    {
        if ($this->editingLocale === 'en') {
            // Default locale stored in primary sections array
            // Handled when saving
        } else {
            $this->translations[$this->editingLocale]['sections'] = $this->sections;
            $this->translations[$this->editingLocale]['title'] = $this->title;
        }
    }

    /**
     * Copy all section data from English to the current locale to speed up translation.
     */
    public function copyFromEn(): void
    {
        $enSections = $this->page->getRawAttribute('sections') ?? $this->getSchemaDefaults('en');
        $this->sections = $enSections;

        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'English content copied to current locale. Remember to translate and save.',
        ]);
    }

    /**
     * Reset a single section back to its schema defaults.
     */
    public function resetSection(string $sectionName): void
    {
        $defaults = $this->getSchemaDefaults($this->editingLocale);

        if (isset($defaults[$sectionName])) {
            $this->sections[$sectionName] = $defaults[$sectionName];

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Section '{$sectionName}' reset to default.",
            ]);
        }
    }

    /**
     * Restore a saved snapshot revision.
     */
    public function restoreRevision(int $revisionId): void
    {
        $rev = KouventaPageRevision::where('page_id', $this->pageId)->find($revisionId);
        if (! $rev) {
            return;
        }

        $this->page->sections = $rev->sections ?? [];
        $this->translations = $rev->translations ?? [];

        $this->sections = $this->editingLocale === 'en'
            ? ($rev->sections ?? [])
            : ($rev->translations[$this->editingLocale]['sections'] ?? $rev->sections ?? []);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Revision restored from {$rev->created_at->format('M d, H:i')}.",
        ]);
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'status' => 'required|in:published,draft',
        ]);

        $this->isSaving = true;

        // 1. Sync current locale data
        $this->stashCurrentLocaleData();

        // 2. Determine primary (EN) sections vs translated (ID) sections
        $enSections = ($this->editingLocale === 'en')
            ? $this->sections
            : ($this->page->getRawAttribute('sections') ?? $this->getSchemaDefaults('en'));

        // 3. Update database
        $this->page->title = $this->title;
        $this->page->slug = $this->slug;
        $this->page->status = $this->status;
        $this->page->sections = $enSections;
        $this->page->translations = $this->translations;
        $this->page->updated_by = auth()->id();
        $this->page->save();

        // 4. Save revision
        $this->page->saveRevision("Updated via admin editor ({$this->editingLocale})");
        $this->refreshRevisions();

        // 5. Purge Caches & reindex search
        try {
            PageCache::purgeAll();
            CacheManager::purgeAll();
            SitemapBuilder::clearCache();
            IndexSearchable::dispatch(KouventaPage::class, $this->page->id);
        } catch (\Throwable $e) {
            // cache purge shouldn't break saving
        }

        $this->isSaving = false;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Page updated successfully!',
        ]);
    }

    public function autosave(): void
    {
        $this->stashCurrentLocaleData();

        $enSections = ($this->editingLocale === 'en')
            ? $this->sections
            : ($this->page->getRawAttribute('sections') ?? []);

        $this->page->sections = $enSections;
        $this->page->translations = $this->translations;
        $this->page->save();
    }

    protected function resolveTabs(): array
    {
        if ($this->page->template === 'product-ai' || $this->page->slug === 'product/ai') {
            return ProductAiSchema::getTabs();
        }

        if ($this->page->template === 'product-ce' || $this->page->slug === 'product/customer-engagement') {
            return ProductCeSchema::getTabs();
        }

        if ($this->page->template === 'solution-ai' || $this->page->slug === 'solutions/ai') {
            return SolutionAiSchema::getTabs();
        }

        if ($this->page->template === 'solution-ce' || $this->page->slug === 'solutions/customer-engagement') {
            return SolutionCeSchema::getTabs();
        }

        if ($this->page->template === 'pricing' || $this->page->slug === 'pricing') {
            return PricingSchema::getTabs();
        }

        return [
            ['id' => 'hero', 'label' => 'Hero Section', 'icon' => 'home'],
            ['id' => 'seo', 'label' => 'SEO & Settings', 'icon' => 'travel_explore'],
        ];
    }

    protected function getSchemaDefaults(string $locale): array
    {
        if ($this->page->template === 'product-ai' || $this->page->slug === 'product/ai') {
            return ProductAiSchema::getDefaults($locale);
        }

        if ($this->page->template === 'product-ce' || $this->page->slug === 'product/customer-engagement') {
            return ProductCeSchema::getDefaults($locale);
        }

        if ($this->page->template === 'solution-ai' || $this->page->slug === 'solutions/ai') {
            return SolutionAiSchema::getDefaults($locale);
        }

        if ($this->page->template === 'solution-ce' || $this->page->slug === 'solutions/customer-engagement') {
            return SolutionCeSchema::getDefaults($locale);
        }

        if ($this->page->template === 'pricing' || $this->page->slug === 'pricing') {
            return PricingSchema::getDefaults($locale);
        }

        return [];
    }

    protected function resolveSectionsForLocale(string $locale): array
    {
        $defaults = $this->getSchemaDefaults($locale);

        if ($locale === 'en') {
            $raw = $this->page->getRawAttribute('sections') ?? [];
        } else {
            $raw = $this->translations[$locale]['sections'] ?? [];
        }

        // Recursively merge stored data over schema defaults to preserve any newly added schema keys
        return array_replace_recursive($defaults, $raw);
    }

    protected function refreshRevisions(): void
    {
        $this->revisions = $this->page->revisions()->take(8)->get()->map(fn ($r) => [
            'id' => $r->id,
            'summary' => $r->summary,
            'created_at' => $r->created_at->format('M d, Y H:i'),
            'user' => $r->user?->name ?? 'Admin',
        ])->toArray();
    }

    public function render()
    {
        return view('kouventa-pages::livewire.page-editor');
    }
}

