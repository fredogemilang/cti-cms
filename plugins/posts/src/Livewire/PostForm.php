<?php

namespace Plugins\Posts\Livewire;

use App\Models\CptEntry;
use App\Services\EditorialWorkflowService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Plugins\Posts\Models\Category;
use Plugins\Posts\Models\Post;
use Plugins\Posts\Models\PostAuthor;
use Plugins\Posts\Models\Setting;
use Plugins\Posts\Models\Tag;
use Plugins\Posts\Services\DocxParserService;

class PostForm extends Component
{
    use WithFileUploads;

    #[On('media-selected')]
    public function onMediaSelected($field, $mediaId, $mediaPath, $mediaUrl)
    {
        if ($field === 'featured_image') {
            $this->featured_image = $mediaPath;
        }
        // seo_og_image is handled by SeoMetaBox component
    }

    #[On('media-removed')]
    public function onMediaRemoved($field)
    {
        if ($field === 'featured_image') {
            $this->featured_image = null;
        }
    }

    public ?Post $post = null;

    public $postId = null;

    public $docxFile = null;

    // Form Fields
    public $title = '';

    public $slug = '';

    public $content = '';

    public $excerpt = '';

    public $status = 'draft';

    public $visibility = 'public';

    public $published_at = null;

    public $featured_image = null;

    public $is_featured = false;

    public $author_id;

    public $newAuthorName = '';

    // Editorial Workflow
    public bool $canApprove = false;

    public array $allowedStatuses = [];

    public bool $showChangeRequestModal = false;

    public string $changeRequestNote = '';

    public $addingAuthor = false;

    // Relationships
    public $selectedCategories = [];

    public $tags = ''; // Comma separated

    public $password = '';

    // CPT Relationships ("Related To")
    public array $selectedCptEntries = [];

    public bool $showCptModal = false;

    public string $cptSearch = '';

    public string $cptFilterSlug = 'all';

    public array $tempSelectedCptEntries = [];

    // === Translations state ===
    /** Locale currently shown in the form. */
    #[Url(as: 'lang', keep: true)]
    public string $editingLocale = '';

    /** Snapshots of translatable fields per non-default locale. */
    public array $localizedSnapshots = [];

    /** Available locales from Settings. */
    public array $availableLocales = [];

    public function mount($postId = null)
    {
        $this->availableLocales = available_locales();
        $this->editingLocale = Post::defaultLocale();

        $workflow = app(EditorialWorkflowService::class);
        $this->canApprove = $workflow->canApprove();
        $this->allowedStatuses = $workflow->allowedStatuses();

        $requestedLocale = request()->query('lang');
        if ($postId) {
            $this->postId = $postId;
            $this->post = Post::findOrFail($postId);

            $this->title = $this->post->title;
            $this->slug = $this->post->slug;
            $this->content = $this->post->content;
            $this->excerpt = $this->post->excerpt;
            $this->status = $this->post->status;
            $this->visibility = $this->post->visibility ?? 'public';
            $this->password = $this->post->password;
            $this->author_id = $this->post->author_id;
            $this->published_at = $this->post->published_at ? $this->post->published_at->format('Y-m-d\TH:i') : null;
            $this->featured_image = $this->post->featured_image;
            $this->is_featured = $this->post->is_featured;

            $this->selectedCategories = $this->post->categories->pluck('id')->toArray();
            $this->tags = $this->post->tags->pluck('name')->implode(', ');
            $this->selectedCptEntries = $this->post->cptEntries->pluck('id')->toArray();

            // Hydrate per-locale snapshots from translations JSON
            $this->hydrateTranslations();

            if ($requestedLocale && in_array($requestedLocale, $this->availableLocales, true) && $requestedLocale !== Post::defaultLocale()) {
                $this->switchLocale($requestedLocale);
            }
        } else {
            $this->status = 'draft';
            $this->visibility = 'public';
            if (auth()->check()) {
                $currentUser = auth()->user();
                $author = PostAuthor::firstOrCreate(
                    ['name' => $currentUser->name],
                    ['slug' => Str::slug($currentUser->name), 'email' => $currentUser->email]
                );
                $this->author_id = $author->id;
            }
        }
    }

    /** Load per-locale translations into localizedSnapshots. */
    protected function hydrateTranslations(): void
    {
        $defaultLocale = Post::defaultLocale();
        $this->localizedSnapshots[$defaultLocale] = [
            'title' => $this->post->title ?? '',
            'slug' => $this->post->slug ?? '',
            'excerpt' => $this->post->excerpt ?? '',
            'content' => $this->post->content ?? '',
        ];

        $translations = $this->post->translations ?? [];
        foreach ($translations as $locale => $fields) {
            if ($locale === $defaultLocale) {
                continue;
            }
            $this->localizedSnapshots[$locale] = [
                'title' => $fields['title'] ?? '',
                'slug' => $fields['slug'] ?? '',
                'excerpt' => $fields['excerpt'] ?? '',
                'content' => $fields['content'] ?? '',
            ];
        }
    }

    /** Switch the form between locale tabs. */
    public function switchLocale(string $newLocale): void
    {
        if ($newLocale === $this->editingLocale) {
            return;
        }
        if (! in_array($newLocale, $this->availableLocales, true)) {
            return;
        }

        $prevLocale = $this->editingLocale;
        $this->localizedSnapshots[$prevLocale] = $this->currentLocaleSnapshot();

        $next = $this->localizedSnapshots[$newLocale] ?? [];
        $this->title = $next['title'] ?? '';
        $this->slug = $next['slug'] ?? '';
        $this->excerpt = $next['excerpt'] ?? '';
        $this->content = $next['content'] ?? '';

        // Notify SeoMetaBox to switch locale
        $this->dispatch('seo-locale-switched', locale: $newLocale);
        $this->dispatch('tiptap-refresh-content');

        $this->editingLocale = $newLocale;
        $this->resetErrorBag();
    }

    protected function currentLocaleSnapshot(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
        ];
    }

    public function updatedTitle($value)
    {
        if (! $this->postId && empty($this->slug)) {
            $this->slug = $this->ensureUniqueSlug(Str::slug($value));
        }
    }

    public function updatedDocxFile()
    {
        $this->validate([
            'docxFile' => 'required|file|mimes:docx|max:10240', // 10MB
        ]);

        try {
            $parser = new DocxParserService;
            $result = $parser->parse($this->docxFile->getRealPath());

            $this->title = $result['title'];
            $this->content = $result['content'];

            if (empty($this->slug)) {
                $this->slug = $this->ensureUniqueSlug(Str::slug($this->title));
            }

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Word document imported successfully!',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to import Word document: '.$e->getMessage(),
            ]);
        }
    }

    protected function ensureUniqueSlug($slug)
    {
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $slugQuery = Post::withTrashed()->where('slug', $slug);

            if ($this->postId) {
                $slugQuery->where('id', '!=', $this->postId);
            }

            if (! $slugQuery->exists()) {
                break;
            }

            $counter++;
            $slug = $originalSlug.'-'.$counter;
        }

        return $slug;
    }

    public function saveAsDraft()
    {
        return $this->save('draft');
    }

    public function save($status = null)
    {
        // Snapshot current locale before validating/saving
        $this->localizedSnapshots[$this->editingLocale] = $this->currentLocaleSnapshot();

        try {
            $isDefault = $this->editingLocale === Post::defaultLocale();
            $this->validate([
                'title' => $isDefault ? 'required|min:3' : 'nullable|min:3',
                'slug' => $isDefault ? 'required' : 'nullable',
                'status' => 'required|in:draft,pending_review,published,scheduled,archived',
                'visibility' => 'required|in:public,private,password',
                'password' => 'required_if:visibility,password',
                'author_id' => 'required|exists:post_authors,id',
                'is_featured' => 'boolean',
            ]);
        } catch (ValidationException $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'There are validation errors. Please check the form.',
            ]);
            throw $e;
        }

        if ($status) {
            $this->status = $status;
        }

        $workflow = app(EditorialWorkflowService::class);
        $resolved = $workflow->resolveStatus($this->status);
        $this->status = $resolved['status'];
        $oldStatus = $this->postId && $this->post ? $this->post->getOriginal('status') : null;

        // Auto-generate excerpt if empty
        if (empty($this->excerpt) && ! empty($this->content)) {
            $cleaned = html_entity_decode($this->content);
            $cleaned = strip_tags($cleaned);
            $cleaned = preg_replace('/\s+/', ' ', $cleaned);
            $this->excerpt = Str::limit(trim($cleaned), 155);
        }

        // Handle Image - now using MediaPicker, path is set directly
        $imagePath = $this->featured_image;

        // Ensure slug is unique with auto-increment
        $this->slug = $this->ensureUniqueSlug($this->slug);

        // Build default-locale data from the default locale snapshot
        $defaultLocale = Post::defaultLocale();
        $defaultSnap = $this->localizedSnapshots[$defaultLocale] ?? $this->currentLocaleSnapshot();

        $data = [
            'title' => $defaultSnap['title'] ?? $this->title,
            'slug' => $defaultSnap['slug'] ?? $this->slug,
            'content' => $defaultSnap['content'] ?? $this->content,
            'excerpt' => $defaultSnap['excerpt'] ?? $this->excerpt,
            'status' => $this->status,
            'visibility' => $this->visibility,
            'password' => $this->visibility === 'password' ? $this->password : null,
            'author_id' => $this->author_id,
            'published_at' => $this->status === 'published' && ! $this->published_at ? now() : $this->published_at,
            'featured_image' => $imagePath,
            'is_featured' => $this->is_featured,
        ];

        // Build translations JSON from non-default locale snapshots
        $translations = [];
        foreach ($this->localizedSnapshots as $locale => $snap) {
            if ($locale === $defaultLocale) {
                continue;
            }
            $isPopulated = false;
            foreach (['title', 'slug', 'excerpt', 'content'] as $f) {
                if (! empty($snap[$f] ?? '')) {
                    $isPopulated = true;
                    break;
                }
            }
            if (! $isPopulated) {
                continue;
            }
            $translations[$locale] = [
                'title' => ($snap['title'] ?? '') ?: null,
                'slug' => ($snap['slug'] ?? '') ?: null,
                'excerpt' => ($snap['excerpt'] ?? '') ?: null,
                'content' => ($snap['content'] ?? '') ?: null,
            ];
        }
        $data['translations'] = $translations ?: null;

        $isNew = ! $this->postId;

        if ($this->postId) {
            $this->post->update($data);
            $post = $this->post;
        } else {
            $post = Post::create($data);
            $this->postId = $post->id;
            $this->post = $post;
        }

        // Notify SeoMetaBox to save/attach
        $this->dispatch('seo-attach', id: $post->id);

        // Sync Categories
        if (empty($this->selectedCategories)) {
            $uncategorized = Category::firstOrCreate(
                ['slug' => 'uncategorized'],
                ['name' => 'Uncategorized', 'description' => 'Default category']
            );
            $this->selectedCategories = [$uncategorized->id];
        }

        $post->categories()->sync($this->selectedCategories);

        // Sync Tags
        if ($this->tags) {
            $tagNames = array_map('trim', explode(',', $this->tags));
            $tagIds = [];
            foreach ($tagNames as $tagName) {
                if (empty($tagName)) {
                    continue;
                }
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        } else {
            $post->tags()->detach();
        }

        // Sync CPT Entries (Related To)
        if (! empty($this->selectedCptEntries)) {
            $cptEntryRecords = CptEntry::with('postType')
                ->whereIn('id', $this->selectedCptEntries)
                ->get();
            $pivotData = [];
            foreach ($cptEntryRecords as $cptEntry) {
                $pivotData[$cptEntry->id] = [
                    'cpt_slug' => $cptEntry->postType->slug ?? null,
                ];
            }
            $post->cptEntries()->sync($pivotData);
        } else {
            $post->cptEntries()->detach();
        }

        $queryParams = array_filter([
            'lang' => $this->editingLocale !== Post::defaultLocale() ? $this->editingLocale : null,
        ]);

        $workflow->handleTransition($post, $post->status, $oldStatus);

        if ($isNew) {
            $msg = $resolved['downgraded'] ? $resolved['message'] : 'Post created successfully.';
            session()->flash($resolved['downgraded'] ? 'info' : 'success', $msg);

            return redirect()->route('admin.posts.edit', array_merge(['id' => $post->id], $queryParams));
        } else {
            $msg = $resolved['downgraded'] ? $resolved['message'] : 'Post updated successfully.';
            $this->dispatch('notify', ['type' => $resolved['downgraded'] ? 'info' : 'success', 'message' => $msg]);

            return redirect()->route('admin.posts.edit', array_merge(['id' => $post->id], $queryParams));
        }
    }

    public function approveAndPublish()
    {
        $workflow = app(EditorialWorkflowService::class);
        abort_unless($workflow->canApprove(), 403);

        if (! $this->postId || ! $this->post) {
            return;
        }

        $oldStatus = $this->post->status;
        $this->post->update([
            'status' => 'published',
            'published_at' => $this->post->published_at ?? now(),
        ]);
        $this->status = 'published';
        $this->published_at = $this->post->published_at?->format('Y-m-d\TH:i');

        $workflow->handleTransition($this->post, 'published', $oldStatus);
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "'{$this->post->title}' approved and published successfully.",
        ]);
    }

    public function openChangeRequestModal()
    {
        $workflow = app(EditorialWorkflowService::class);
        abort_unless($workflow->canApprove(), 403);
        $this->changeRequestNote = '';
        $this->showChangeRequestModal = true;
    }

    public function closeChangeRequestModal()
    {
        $this->showChangeRequestModal = false;
    }

    public function submitChangeRequest()
    {
        $workflow = app(EditorialWorkflowService::class);
        abort_unless($workflow->canApprove(), 403);

        $this->validate([
            'changeRequestNote' => 'required|string|min:3|max:1000',
        ]);

        if (! $this->postId || ! $this->post) {
            return;
        }

        $oldStatus = $this->post->status;
        $this->post->update([
            'status' => 'draft',
        ]);
        $this->status = 'draft';

        $workflow->handleTransition($this->post, 'draft', $oldStatus, auth()->user(), $this->changeRequestNote);
        $this->showChangeRequestModal = false;
        $this->dispatch('notify', [
            'type' => 'warning',
            'message' => "Changes requested on '{$this->post->title}'. Post moved to draft.",
        ]);
    }

    public function openCptModal()
    {
        $this->tempSelectedCptEntries = array_map('intval', $this->selectedCptEntries);
        $this->cptSearch = '';
        $this->showCptModal = true;
    }

    public function toggleTempCptEntry($id)
    {
        $id = (int) $id;
        if (in_array($id, $this->tempSelectedCptEntries, true)) {
            $this->tempSelectedCptEntries = array_values(array_filter($this->tempSelectedCptEntries, fn ($v) => $v !== $id));
        } else {
            $this->tempSelectedCptEntries[] = $id;
        }
    }

    public function saveCptSelections()
    {
        $this->selectedCptEntries = array_values(array_unique($this->tempSelectedCptEntries));
        $this->showCptModal = false;
    }

    public function removeCptEntry($id)
    {
        $id = (int) $id;
        $this->selectedCptEntries = array_values(array_filter($this->selectedCptEntries, fn ($v) => $v !== $id));
    }

    public function delete()
    {
        if ($this->post) {
            $this->post->delete();
            session()->flash('success', 'Post moved to trash.');

            return redirect()->route('admin.posts.index');
        }
    }

    public function removeTag($tagName)
    {
        $tags = array_map('trim', explode(',', $this->tags));
        $tags = array_diff($tags, [$tagName]);
        $this->tags = implode(', ', $tags);
    }

    public function addCategory($name)
    {
        if (empty($name)) {
            return;
        }

        $category = Category::firstOrCreate(
            ['name' => $name],
            ['slug' => Str::slug($name)]
        );

        if (! in_array($category->id, $this->selectedCategories)) {
            $this->selectedCategories[] = $category->id;
        }
    }

    public function addAuthor($name)
    {
        if (empty($name)) {
            return;
        }

        $author = PostAuthor::firstOrCreate(
            ['name' => $name],
            ['slug' => Str::slug($name)]
        );

        $this->author_id = $author->id;
        $this->newAuthorName = '';
        $this->addingAuthor = false;

        $this->dispatch('notify', ['type' => 'success', 'message' => "Author '{$author->name}' created successfully."]);
    }

    public function addTag($name)
    {
        if (empty($name)) {
            return;
        }

        $currentTags = array_filter(array_map('trim', explode(',', $this->tags)));
        if (! in_array($name, $currentTags)) {
            $currentTags[] = $name;
            $this->tags = implode(', ', $currentTags);
        }
    }

    public function render()
    {
        $archiveSlug = Setting::get('archive_slug', 'blog');
        if (Schema::hasTable('settings')) {
            $coreBase = \App\Models\Setting::get('permalink_post_base');
            if (! empty($coreBase)) {
                $archiveSlug = $coreBase;
            }
        }

        $pairedSlugs = json_decode(Setting::get('paired_cpts', json_encode(['technology-alliance'])), true) ?: ['technology-alliance'];

        $pairedCptTypes = DB::table('custom_post_types')
            ->whereIn('slug', $pairedSlugs)
            ->orderBy('plural_label', 'asc')
            ->get();

        $attachedCptEntries = CptEntry::with('postType')
            ->whereIn('id', array_map('intval', $this->selectedCptEntries))
            ->get();

        $cptQuery = CptEntry::with('postType')
            ->whereHas('postType', fn ($q) => $q->whereIn('slug', $pairedSlugs));

        if ($this->cptFilterSlug !== 'all') {
            $cptQuery->whereHas('postType', fn ($q) => $q->where('slug', $this->cptFilterSlug));
        }

        if (! empty($this->cptSearch)) {
            $cptQuery->where('title', 'like', '%'.$this->cptSearch.'%');
        }

        $modalCptEntries = $cptQuery->orderBy('title', 'asc')->get();

        return view('posts::livewire.post-form', [
            'categories' => Category::orderBy('name')->get(),
            'authors' => PostAuthor::orderBy('name')->get(),
            'archiveSlug' => $archiveSlug,
            'pairedCptTypes' => $pairedCptTypes,
            'attachedCptEntries' => $attachedCptEntries,
            'modalCptEntries' => $modalCptEntries,
        ]);
    }
}
