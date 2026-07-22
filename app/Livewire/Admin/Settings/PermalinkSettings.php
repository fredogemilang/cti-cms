<?php

namespace App\Livewire\Admin\Settings;

use App\Models\CustomPostType;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Component;

class PermalinkSettings extends Component
{
    // Posts
    public string $postBase = 'blog';

    public string $categoryBase = 'category';

    public string $tagBase = 'tag';

    // CPTs — keyed by CPT slug
    /** @var array<string, string> */
    public array $cptBases = [];

    // Taxonomy — keyed by taxonomy slug
    /** @var array<string, string> */
    public array $taxonomyBases = [];

    public function mount(): void
    {
        // Posts plugin settings
        $this->postBase = (string) Setting::get('permalink_post_base', Setting::get('archive_slug', 'blog'));
        $this->categoryBase = (string) Setting::get('permalink_category_base', 'category');
        $this->tagBase = (string) Setting::get('permalink_tag_base', 'tag');

        // CPT bases
        try {
            if (Schema::hasTable('custom_post_types')) {
                $cpts = CustomPostType::where('is_active', true)->get();
                foreach ($cpts as $cpt) {
                    $this->cptBases[$cpt->slug] = (string) Setting::get("permalink_cpt_{$cpt->slug}_base", $cpt->slug);
                }
            }
        } catch (\Throwable) {
            // Silence during migration
        }
    }

    public function save(): void
    {
        $this->validate([
            'postBase' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9\-]+$/'],
            'categoryBase' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9\-]+$/'],
            'tagBase' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9\-]+$/'],
            'cptBases.*' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9\-]+$/'],
        ], [
            'postBase.regex' => 'Only lowercase letters, numbers, and hyphens allowed.',
            'categoryBase.regex' => 'Only lowercase letters, numbers, and hyphens allowed.',
            'tagBase.regex' => 'Only lowercase letters, numbers, and hyphens allowed.',
            'cptBases.*.regex' => 'Only lowercase letters, numbers, and hyphens allowed.',
        ]);

        // Slugify inputs
        $this->postBase = Str::slug($this->postBase);
        $this->categoryBase = Str::slug($this->categoryBase);
        $this->tagBase = Str::slug($this->tagBase);

        // Save Post permalink settings
        Setting::set('permalink_post_base', $this->postBase, 'permalinks', 'text');
        Setting::set('archive_slug', $this->postBase, 'permalinks', 'text'); // Keep backward compat
        Setting::set('permalink_category_base', $this->categoryBase, 'permalinks', 'text');
        Setting::set('permalink_tag_base', $this->tagBase, 'permalinks', 'text');

        // Save CPT bases
        foreach ($this->cptBases as $slug => $base) {
            $base = Str::slug($base);
            $this->cptBases[$slug] = $base;
            Setting::set("permalink_cpt_{$slug}_base", $base, 'permalinks', 'text');
        }

        // Flush sitemap & route caches
        Cache::forget('sitemap.xml_index_v2');
        foreach (['page', 'post', 'taxonomy', 'all'] as $type) {
            Cache::forget("sitemap_type_{$type}_v2");
        }
        try {
            foreach (CustomPostType::where('is_active', true)->pluck('slug') as $cptSlug) {
                Cache::forget("sitemap_type_{$cptSlug}_v2");
            }
        } catch (\Throwable) {
            // Silence
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Permalink settings saved! Route cache has been cleared.',
        ]);
    }

    public function render()
    {
        $cpts = [];
        try {
            if (Schema::hasTable('custom_post_types')) {
                $cpts = CustomPostType::where('is_active', true)->get();
            }
        } catch (\Throwable) {
            // Silence
        }

        return view('livewire.admin.settings.permalink-settings', [
            'cpts' => $cpts,
            'siteUrl' => rtrim(config('app.url', 'https://example.com'), '/'),
        ]);
    }
}
