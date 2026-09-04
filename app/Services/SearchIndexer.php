<?php

namespace App\Services;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Page;
use App\Models\SearchIndex;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plugins\Posts\Models\Post;

class SearchIndexer
{
    public function __construct(
        protected ContentTextExtractor $extractor
    ) {}

    /**
     * Index or update an entity across all available locales.
     */
    public function index(Model $entity): void
    {
        if (! $this->isIndexable($entity)) {
            $this->unindex($entity);

            return;
        }

        $locales = function_exists('available_locales') ? available_locales() : ['en', 'id'];

        foreach ($locales as $locale) {
            $extracted = $this->extractor->extract($entity, $locale);

            if (empty($extracted['title']) && empty($extracted['body'])) {
                SearchIndex::where('searchable_type', $entity->getMorphClass())
                    ->where('searchable_id', $entity->getKey())
                    ->where('locale', $locale)
                    ->delete();

                continue;
            }

            SearchIndex::updateOrCreate(
                [
                    'searchable_type' => $entity->getMorphClass(),
                    'searchable_id' => $entity->getKey(),
                    'locale' => $locale,
                ],
                [
                    'title' => $extracted['title'],
                    'excerpt' => $extracted['excerpt'] ?: null,
                    'body' => $extracted['body'] ?: null,
                    'url' => $extracted['url'],
                    'indexed_at' => now(),
                ]
            );
        }
    }

    /**
     * Remove an entity from the search index.
     */
    public function unindex(Model|string $entityOrType, ?int $id = null): void
    {
        if ($entityOrType instanceof Model) {
            $type = $entityOrType->getMorphClass();
            $id = $entityOrType->getKey();
        } else {
            $type = $entityOrType;
        }

        if (! $type || ! $id) {
            return;
        }

        SearchIndex::where('searchable_type', $type)
            ->where('searchable_id', $id)
            ->delete();
    }

    /**
     * Determine if an entity is currently eligible for public indexing.
     */
    public function isIndexable(Model $entity): bool
    {
        if (in_array(SoftDeletes::class, class_uses_recursive($entity), true) && method_exists($entity, 'trashed') && $entity->trashed()) {
            return false;
        }

        if ($entity instanceof Page) {
            return $entity->status === 'published';
        }

        if ($entity instanceof CptEntry) {
            if ($entity->status !== 'published') {
                return false;
            }

            $postType = $entity->postType ?? CustomPostType::find($entity->post_type_id);

            return $postType && $postType->is_active && $postType->publicly_queryable;
        }

        if ($this->isPostModel($entity)) {
            return $entity->getAttribute('status') === 'published';
        }

        return $entity->getAttribute('status') === 'published';
    }

    /**
     * Reindex all public content.
     */
    public function reindexAll(?string $type = null, ?Closure $progress = null): int
    {
        $indexed = 0;

        // 1. Pages
        if (! $type || in_array(strtolower($type), ['page', 'pages'], true)) {
            $pages = Page::where('status', 'published')->with('allBlocks')->get();
            foreach ($pages as $page) {
                $this->index($page);
                $indexed++;
                if ($progress) {
                    $progress('Page: '.$page->title);
                }
            }
        }

        // 2. CPT Entries
        if (! $type || in_array(strtolower($type), ['cpt', 'cpts', 'cpt_entries', 'entry', 'entries'], true)) {
            $cpts = CustomPostType::where('is_active', true)->where('publicly_queryable', true)->get();
            foreach ($cpts as $cpt) {
                $entries = CptEntry::where('post_type_id', $cpt->id)->where('status', 'published')->get();
                foreach ($entries as $entry) {
                    $this->index($entry);
                    $indexed++;
                    if ($progress) {
                        $progress("{$cpt->singular_label}: {$entry->title}");
                    }
                }
            }
        }

        // 3. Posts
        if ((! $type || in_array(strtolower($type), ['post', 'posts'], true)) && class_exists(Post::class)) {
            try {
                $posts = Post::where('status', 'published')->get();
                foreach ($posts as $post) {
                    $this->index($post);
                    $indexed++;
                    if ($progress) {
                        $progress('Post: '.$post->title);
                    }
                }
            } catch (\Throwable) {
                // Ignore if table/plugin not installed
            }
        }

        return $indexed;
    }

    protected function isPostModel(Model $entity): bool
    {
        return class_exists(Post::class) && $entity instanceof Post;
    }
}
