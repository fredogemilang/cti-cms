<?php

namespace App\Livewire\Admin\Seo;

use App\Models\CptEntry;
use App\Models\Page;
use App\Models\SeoMeta;
use Livewire\Component;
use Plugins\Posts\Models\Post;

class SeoOverview extends Component
{
    public int $totalPages = 0;

    public int $totalPosts = 0;

    public int $totalCptEntries = 0;

    public int $configuredSeoCount = 0;

    public bool $allowIndexing = true;

    public bool $sitemapEnabled = true;

    public bool $llmsEnabled = true;

    public string $siteName = '';

    public function mount(): void
    {
        $this->totalPages = Page::count();
        $this->totalPosts = class_exists(Post::class) ? Post::count() : 0;
        $this->totalCptEntries = CptEntry::count();
        $this->configuredSeoCount = SeoMeta::where(function ($q) {
            $q->whereNotNull('title')->orWhereNotNull('description');
        })->count();

        $this->allowIndexing = (bool) setting('seo_allow_indexing', true);
        $this->sitemapEnabled = (bool) setting('seo_sitemap_enabled', true);
        $this->llmsEnabled = (bool) setting('seo_llms_txt_enabled', true);
        $this->siteName = (string) setting('site_name', config('app.name'));
    }

    public function render()
    {
        return view('livewire.admin.seo.seo-overview')
            ->layout('layouts.admin', [
                'title' => 'SEO Overview',
            ]);
    }
}
