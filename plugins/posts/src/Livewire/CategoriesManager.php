<?php

namespace Plugins\Posts\Livewire;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Plugins\Posts\Models\Category;

class CategoriesManager extends Component
{
    use WithPagination;

    // Form Fields
    public $name = '';

    public $slug = '';

    public $parent_id = null;

    public $description = '';

    // Edit Mode
    public $editingCategory = null;

    public function render()
    {
        $allCategories = Category::with('parent')->withCount('posts')->orderBy('name')->get();
        $hierarchicalCategories = $this->flattenCategories($allCategories);

        // Manual pagination
        $currentPage = $this->getPage();
        $perPage = 20;
        $categories = new LengthAwarePaginator(
            $hierarchicalCategories->forPage($currentPage, $perPage),
            $hierarchicalCategories->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $parents = Category::where('parent_id', null)->orderBy('name')->get();

        return view('posts::livewire.categories-manager', [
            'categories' => $categories,
            'parents' => $parents,
        ]);
    }

    private function flattenCategories($categories, $parentId = null, $depth = 0)
    {
        $result = collect();
        $items = $categories->where('parent_id', $parentId);

        foreach ($items as $item) {
            $item->depth = $depth;
            $result->push($item);
            $result = $result->merge($this->flattenCategories($categories, $item->id, $depth + 1));
        }

        return $result;
    }

    // Multilingual State
    public string $activeLocale = 'en';

    public array $translations = [
        'en' => ['name' => '', 'slug' => '', 'description' => ''],
        'id' => ['name' => '', 'slug' => '', 'description' => ''],
    ];

    public function setLocale(string $locale): void
    {
        $this->translations[$this->activeLocale] = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
        ];

        $this->activeLocale = $locale;

        $this->name = $this->translations[$locale]['name'] ?? '';
        $this->slug = $this->translations[$locale]['slug'] ?? '';
        $this->description = $this->translations[$locale]['description'] ?? '';
    }

    public function updatedName($value)
    {
        $this->translations[$this->activeLocale]['name'] = $value;
        if (! $this->editingCategory && empty($this->slug)) {
            $this->slug = Str::slug($value);
            $this->translations[$this->activeLocale]['slug'] = $this->slug;
        }
    }

    public function store()
    {
        $this->translations[$this->activeLocale] = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
        ];

        $this->validate([
            'name' => 'required|min:2',
            'slug' => 'required|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        $defaultName = $this->translations['en']['name'] ?: ($this->translations['id']['name'] ?: $this->name);
        $defaultSlug = $this->translations['en']['slug'] ?: ($this->translations['id']['slug'] ?: $this->slug);
        $defaultDesc = $this->translations['en']['description'] ?: ($this->translations['id']['description'] ?: $this->description);

        $category = Category::create([
            'name' => $defaultName,
            'slug' => $defaultSlug,
            'parent_id' => $this->parent_id ?: null,
            'description' => $defaultDesc,
        ]);

        foreach (['en', 'id'] as $loc) {
            if (! empty($this->translations[$loc]['name'])) {
                $category->setTranslation('name', $loc, $this->translations[$loc]['name']);
            }
            if (! empty($this->translations[$loc]['slug'])) {
                $category->setTranslation('slug', $loc, $this->translations[$loc]['slug']);
            }
            if (! empty($this->translations[$loc]['description'])) {
                $category->setTranslation('description', $loc, $this->translations[$loc]['description']);
            }
        }
        $category->save();

        $this->cancelEdit();
        session()->flash('success', 'Category created successfully.');
    }

    public function edit($id)
    {
        $this->editingCategory = Category::find($id);
        if (! $this->editingCategory) {
            return;
        }

        $this->parent_id = $this->editingCategory->parent_id;

        foreach (['en', 'id'] as $loc) {
            $this->translations[$loc] = [
                'name' => $this->editingCategory->getTranslation('name', $loc, false) ?? ($loc === 'en' ? $this->editingCategory->name : ''),
                'slug' => $this->editingCategory->getTranslation('slug', $loc, false) ?? ($loc === 'en' ? $this->editingCategory->slug : ''),
                'description' => $this->editingCategory->getTranslation('description', $loc, false) ?? ($loc === 'en' ? $this->editingCategory->description : ''),
            ];
        }

        $this->name = $this->translations[$this->activeLocale]['name'] ?? '';
        $this->slug = $this->translations[$this->activeLocale]['slug'] ?? '';
        $this->description = $this->translations[$this->activeLocale]['description'] ?? '';
    }

    public function update()
    {
        $this->translations[$this->activeLocale] = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
        ];

        $this->validate([
            'name' => 'required|min:2',
            'slug' => 'required|unique:categories,slug,'.$this->editingCategory->id,
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        $defaultName = $this->translations['en']['name'] ?: ($this->translations['id']['name'] ?: $this->name);
        $defaultSlug = $this->translations['en']['slug'] ?: ($this->translations['id']['slug'] ?: $this->slug);
        $defaultDesc = $this->translations['en']['description'] ?: ($this->translations['id']['description'] ?: $this->description);

        $this->editingCategory->update([
            'name' => $defaultName,
            'slug' => $defaultSlug,
            'parent_id' => $this->parent_id ?: null,
            'description' => $defaultDesc,
        ]);

        foreach (['en', 'id'] as $loc) {
            if (! empty($this->translations[$loc]['name'])) {
                $this->editingCategory->setTranslation('name', $loc, $this->translations[$loc]['name']);
            }
            if (! empty($this->translations[$loc]['slug'])) {
                $this->editingCategory->setTranslation('slug', $loc, $this->translations[$loc]['slug']);
            }
            if (! empty($this->translations[$loc]['description'])) {
                $this->editingCategory->setTranslation('description', $loc, $this->translations[$loc]['description']);
            }
        }
        $this->editingCategory->save();

        $this->cancelEdit();
        session()->flash('success', 'Category updated successfully.');
    }

    public function cancelEdit()
    {
        $this->editingCategory = null;
        $this->activeLocale = 'en';
        $this->reset(['name', 'slug', 'parent_id', 'description']);
        $this->translations = [
            'en' => ['name' => '', 'slug' => '', 'description' => ''],
            'id' => ['name' => '', 'slug' => '', 'description' => ''],
        ];
    }

    public function delete($id)
    {
        $category = Category::find($id);

        if (! $category) {
            return;
        }

        if ($category->slug === 'uncategorized') {
            session()->flash('error', 'The default Uncategorized category cannot be deleted.');

            return;
        }

        // Get all posts associated with this category
        $posts = $category->posts()->get();

        $category->delete();

        // Ensure default Uncategorized category exists
        $uncategorized = Category::firstOrCreate(
            ['slug' => 'uncategorized'],
            ['name' => 'Uncategorized', 'description' => 'Default category']
        );

        // Reassign posts with no categories left
        foreach ($posts as $post) {
            if ($post->categories()->count() === 0) {
                $post->categories()->attach($uncategorized->id);
            }
        }

        session()->flash('success', 'Category deleted successfully.');
    }
}
