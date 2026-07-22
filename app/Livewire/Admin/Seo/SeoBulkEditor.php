<?php

namespace App\Livewire\Admin\Seo;

use App\Models\Page;
use App\Models\SeoMeta;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;
use Plugins\Posts\Models\Post;

class SeoBulkEditor extends Component
{
    use WithPagination;

    public string $filterType = 'page'; // 'page' or 'post'

    public string $search = '';

    public int $perPage = 10;

    // Inline edit buffers
    public array $titles = [];

    public array $descriptions = [];

    public function setFilterType(string $type): void
    {
        $this->filterType = $type;
        $this->resetPage();
        $this->titles = [];
        $this->descriptions = [];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function saveSeoRow(string $modelType, int $modelId): void
    {
        $titleKey = "{$modelType}_{$modelId}";
        $title = $this->titles[$titleKey] ?? null;
        $description = $this->descriptions[$titleKey] ?? null;

        SeoMeta::updateOrCreate(
            [
                'seoable_type' => $modelType,
                'seoable_id' => $modelId,
                'locale' => '',
            ],
            [
                'title' => $title ?: null,
                'description' => $description ?: null,
            ]
        );

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'SEO Meta saved for entry #'.$modelId,
        ]);
    }

    public function render()
    {
        $items = collect();

        $pageCount = Page::count();
        $postCount = (class_exists(Post::class) && Schema::hasTable('posts')) ? Post::count() : 0;

        if ($this->filterType === 'page') {
            $query = Page::query();
            if ($this->search) {
                $query->where('title', 'like', '%'.$this->search.'%');
            }
            $pages = $query->latest()->paginate($this->perPage);

            foreach ($pages as $page) {
                $key = "App\\Models\\Page_{$page->id}";
                if (! isset($this->titles[$key])) {
                    $meta = SeoMeta::where('seoable_type', Page::class)->where('seoable_id', $page->id)->where('locale', '')->first();
                    $this->titles[$key] = $meta ? ($meta->title ?? '') : '';
                    $this->descriptions[$key] = $meta ? ($meta->description ?? '') : '';
                }
            }
            $items = $pages;
        } elseif ($this->filterType === 'post' && class_exists(Post::class)) {
            $query = Post::query();
            if ($this->search) {
                $query->where('title', 'like', '%'.$this->search.'%');
            }
            $posts = $query->latest()->paginate($this->perPage);

            foreach ($posts as $post) {
                $key = "Plugins\\Posts\\Models\\Post_{$post->id}";
                if (! isset($this->titles[$key])) {
                    $meta = SeoMeta::where('seoable_type', Post::class)->where('seoable_id', $post->id)->where('locale', '')->first();
                    $this->titles[$key] = $meta ? ($meta->title ?? '') : '';
                    $this->descriptions[$key] = $meta ? ($meta->description ?? '') : '';
                }
            }
            $items = $posts;
        }

        return view('livewire.admin.seo.seo-bulk-editor', [
            'items' => $items,
            'counts' => [
                'page' => $pageCount,
                'post' => $postCount,
            ],
        ])->layout('layouts.admin', [
            'title' => 'Bulk Title & Description Editor',
        ]);
    }
}
