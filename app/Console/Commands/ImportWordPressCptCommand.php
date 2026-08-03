<?php

namespace App\Console\Commands;

use App\Models\CptEntry;
use App\Models\Media;
use App\Services\MediaUsageService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Plugins\Posts\Models\Category;
use Plugins\Posts\Models\Post;
use Plugins\Posts\Models\PostAuthor;
use Plugins\Posts\Models\Tag;

class ImportWordPressCptCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:import-wp
                            {--url= : WordPress Site Base URL (e.g. https://www.centraldatatech.com)}
                            {--wp-cpt=post : WordPress Post Type slug}
                            {--target=plugin_post : Target CMS Post Type ID or "plugin_post"}
                            {--download-featured-image=1 : Download featured images (1/0)}
                            {--download-content-images=1 : Download inline content images (1/0)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import WordPress posts/CPTs via REST API without HTTP execution timeouts';

    protected bool $downloadFeaturedImage = true;

    protected bool $downloadContentImages = true;

    protected string $wpUrl = '';

    protected string $wpCpt = '';

    protected string $target = '';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        set_time_limit(0);
        ini_set('memory_limit', '1G');

        $this->wpUrl = rtrim((string) $this->option('url'), '/');
        if (empty($this->wpUrl)) {
            $this->wpUrl = (string) $this->ask('Enter WordPress Site Base URL (e.g. https://www.centraldatatech.com)');
            $this->wpUrl = rtrim($this->wpUrl, '/');
        }

        if (Str::contains($this->wpUrl, '/wp-json')) {
            $this->wpUrl = Str::before($this->wpUrl, '/wp-json');
        }

        $this->wpCpt = (string) $this->option('wp-cpt');
        $this->target = (string) $this->option('target');
        $this->downloadFeaturedImage = (bool) $this->option('download-featured-image');
        $this->downloadContentImages = (bool) $this->option('download-content-images');

        $this->info("🚀 Starting WordPress CPT Import from: {$this->wpUrl}");
        $this->info("   WP CPT: {$this->wpCpt} -> Target: {$this->target}");

        // 1. Resolve WP REST API endpoint
        $restBase = $this->wpCpt;
        $cptResp = Http::timeout(30)->get($this->wpUrl.'/wp-json/wp/v2/types/'.$this->wpCpt);
        if ($cptResp->successful()) {
            $cptData = $cptResp->json();
            $restBase = $cptData['rest_base'] ?? $this->wpCpt;
        }

        // 2. Determine total pages
        $perPage = 20;
        $headResp = Http::timeout(30)->get($this->wpUrl.'/wp-json/wp/v2/'.$restBase, [
            'per_page' => $perPage,
            'page' => 1,
            '_embed' => true,
        ]);

        if ($headResp->failed()) {
            $this->error("❌ Failed to connect to REST API endpoint: {$this->wpUrl}/wp-json/wp/v2/{$restBase}");

            return self::FAILURE;
        }

        $totalPosts = (int) $headResp->header('X-WP-Total');
        $totalPages = max(1, (int) $headResp->header('X-WP-TotalPages'));

        $this->info("📊 Total items found: {$totalPosts} across {$totalPages} pages.");

        if ($totalPosts === 0) {
            $this->warn('⚠️ No items found to import.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($totalPosts);
        $bar->start();

        $success = 0;
        $skipped = 0;
        $failed = 0;

        for ($page = 1; $page <= $totalPages; $page++) {
            $resp = Http::timeout(60)->get($this->wpUrl.'/wp-json/wp/v2/'.$restBase, [
                'per_page' => $perPage,
                'page' => $page,
                '_embed' => true,
            ]);

            if ($resp->failed()) {
                continue;
            }

            $posts = $resp->json();
            foreach ($posts as $post) {
                $res = $this->importSinglePost($post);
                if ($res === 'success') {
                    $success++;
                } elseif ($res === 'skipped') {
                    $skipped++;
                } else {
                    $failed++;
                }
                $bar->advance();
            }
        }

        $bar->finish();

        $hierarchicalParentsResolved = $this->resolveParentRelationships();
        app(MediaUsageService::class)->clearCache();
        $this->newLine(2);
        $this->info('✅ Import finished!');
        $this->table(
            ['Result', 'Count'],
            [
                ['Success', $success],
                ['Skipped (Already exists)', $skipped],
                ['Failed', $failed],
                ['Hierarchical Parents Linked', $hierarchicalParentsResolved],
                ['Total', $totalPosts],
            ]
        );

        return self::SUCCESS;
    }

    protected function resolveParentRelationships(): int
    {
        if ($this->target === 'plugin_post') {
            return 0;
        }

        $targetCptId = (int) $this->target;
        $allEntries = CptEntry::where('post_type_id', $targetCptId)->get();
        if ($allEntries->isEmpty()) {
            return 0;
        }

        $wpToLocalMap = [];
        $entriesWithParent = [];

        foreach ($allEntries as $entry) {
            $meta = $entry->meta ?? [];
            if (is_array($meta)) {
                if (! empty($meta['wp_original_id'])) {
                    $wpToLocalMap[(int) $meta['wp_original_id']] = $entry->id;
                }
                if (! empty($meta['wp_original_ids']) && is_array($meta['wp_original_ids'])) {
                    foreach ($meta['wp_original_ids'] as $wid) {
                        $wpToLocalMap[(int) $wid] = $entry->id;
                    }
                }
                if (! empty($meta['wp_parent_id'])) {
                    $entriesWithParent[] = $entry;
                }
            }
        }

        if (empty($entriesWithParent)) {
            return 0;
        }

        $resolvedCount = 0;

        foreach ($entriesWithParent as $entry) {
            $wpParentId = (int) ($entry->meta['wp_parent_id'] ?? 0);
            if ($wpParentId > 0 && isset($wpToLocalMap[$wpParentId])) {
                $localParentId = (int) $wpToLocalMap[$wpParentId];
                if ($entry->parent_id !== $localParentId && $entry->id !== $localParentId) {
                    $entry->parent_id = $localParentId;
                    $entry->save();
                    $resolvedCount++;
                }
            }
        }

        return $resolvedCount;
    }

    protected function findExistingPolylangEntry(array $wpPost, string $slug, $targetCpt)
    {
        $wpId = (int) ($wpPost['id'] ?? 0);
        $polylangTranslations = $wpPost['translations'] ?? [];
        $relatedWpIds = array_values(array_unique(array_filter(array_merge(
            [$wpId],
            is_array($polylangTranslations) ? array_values($polylangTranslations) : []
        ))));

        if ($targetCpt === 'plugin_post') {
            if (class_exists(Post::class)) {
                $allPosts = Post::all();
                foreach ($allPosts as $p) {
                    $meta = $p->meta ?? [];
                    $origId = (int) ($meta['wp_original_id'] ?? 0);
                    $origIds = array_map('intval', is_array($meta['wp_original_ids'] ?? null) ? $meta['wp_original_ids'] : []);
                    if (($origId && in_array($origId, $relatedWpIds, true)) || array_intersect($origIds, $relatedWpIds)) {
                        return $p;
                    }
                }

                return Post::where('slug', $slug)->first();
            }

            return null;
        }

        $entries = CptEntry::where('post_type_id', $targetCpt)->get();
        foreach ($entries as $e) {
            $meta = $e->meta ?? [];
            $origId = (int) ($meta['wp_original_id'] ?? 0);
            $origIds = array_map('intval', is_array($meta['wp_original_ids'] ?? null) ? $meta['wp_original_ids'] : []);
            if (($origId && in_array($origId, $relatedWpIds, true)) || array_intersect($origIds, $relatedWpIds)) {
                return $e;
            }
        }

        return null;
    }

    protected function importSinglePost(array $wpPost): string
    {
        try {
            $title = html_entity_decode(strip_tags($wpPost['title']['rendered'] ?? ''), ENT_QUOTES, 'UTF-8');
            $slug = $wpPost['slug'] ?? Str::slug($title);
            $excerpt = strip_tags($wpPost['excerpt']['rendered'] ?? '');
            $excerpt = html_entity_decode(trim($excerpt), ENT_QUOTES, 'UTF-8');

            $rawLang = $wpPost['lang'] ?? null;
            $wpLang = $rawLang ? strtolower(explode('_', str_replace('-', '_', $rawLang))[0]) : CptEntry::defaultLocale();
            $cmsDefaultLocale = CptEntry::defaultLocale();
            $currentLang = $wpLang;

            $isPolylangPost = ! empty($wpPost['lang']) || ! empty($wpPost['translations']);

            if ($isPolylangPost) {
                $existing = $this->findExistingPolylangEntry($wpPost, $slug, $this->target);

                if ($existing) {
                    $transContent = $wpPost['content']['rendered'] ?? '';
                    if ($this->downloadContentImages) {
                        $transContent = $this->processContentImages($transContent);
                    }

                    $meta = $existing->meta ?? [];
                    if ($currentLang === $cmsDefaultLocale) {
                        // Current lang IS primary lang (e.g. English arrives after Indonesian created the initial row)
                        $existing->title = $title;
                        $existing->slug = $slug;
                        $existing->content = $transContent;
                        $existing->excerpt = $excerpt;
                    } else {
                        $existingTransTitle = method_exists($existing, 'hasTranslation')
                            ? $existing->hasTranslation('title', $currentLang)
                            : null;

                        if (! empty($existingTransTitle)) {
                            return 'skipped';
                        }

                        $transSlug = str_ends_with($slug, '-'.$currentLang) ? $slug : $slug.'-'.$currentLang;

                        if (method_exists($existing, 'setTranslation')) {
                            $existing->setTranslation('title', $currentLang, $title);
                            $existing->setTranslation('slug', $currentLang, $slug);
                            $existing->setTranslation('content', $currentLang, $transContent);
                        }
                    }

                    $wpOriginalIds = $meta['wp_original_ids'] ?? [$meta['wp_original_id'] ?? null];
                    if (! empty($wpPost['id'])) {
                        $wpOriginalIds[] = (int) $wpPost['id'];
                    }
                    if (! empty($wpPost['translations']) && is_array($wpPost['translations'])) {
                        foreach ($wpPost['translations'] as $tId) {
                            $wpOriginalIds[] = (int) $tId;
                        }
                    }
                    $meta['wp_original_ids'] = array_values(array_unique(array_filter($wpOriginalIds)));
                    $existing->meta = $meta;
                    $existing->save();

                    return 'translated';
                }
            } else {
                if ($this->target === 'plugin_post') {
                    if (Post::where('slug', $slug)->exists()) {
                        return 'skipped';
                    }
                } else {
                    if (CptEntry::where('slug', $slug)->where('post_type_id', $this->target)->exists()) {
                        return 'skipped';
                    }
                }
            }

            // Content
            $content = $wpPost['content']['rendered'] ?? '';
            if ($this->downloadContentImages) {
                $content = $this->processContentImages($content);
            }

            // Excerpt
            $excerpt = strip_tags($wpPost['excerpt']['rendered'] ?? '');
            $excerpt = html_entity_decode(trim($excerpt), ENT_QUOTES, 'UTF-8');

            // Date
            $publishedAt = Carbon::parse($wpPost['date'] ?? now());

            // Featured Image
            $featuredImage = null;
            if ($this->downloadFeaturedImage) {
                $featuredImage = $this->getFeaturedImage($wpPost);
            }

            $wpOriginalIds = [(int) $wpPost['id']];
            if (! empty($wpPost['translations']) && is_array($wpPost['translations'])) {
                foreach ($wpPost['translations'] as $tId) {
                    $wpOriginalIds[] = (int) $tId;
                }
            }

            // Import into Post Plugin or CPT Entry
            if ($this->target === 'plugin_post') {
                $authorId = null;
                if (class_exists(PostAuthor::class)) {
                    $author = PostAuthor::first();
                    if (! $author) {
                        $author = PostAuthor::create([
                            'name' => 'System Admin',
                            'slug' => 'system-admin',
                        ]);
                    }
                    $authorId = $author->id;
                }

                $isSticky = ! empty($wpPost['sticky']);

                $post = Post::create([
                    'title' => $title,
                    'slug' => $slug,
                    'content' => $content,
                    'excerpt' => $excerpt,
                    'featured_image' => $featuredImage,
                    'status' => 'published',
                    'published_at' => $publishedAt,
                    'author_id' => $authorId,
                    'is_featured' => $isSticky,
                    'meta' => [
                        'wp_original_id' => (int) $wpPost['id'],
                        'wp_original_ids' => array_values(array_unique(array_filter($wpOriginalIds))),
                    ],
                ]);

                $post->forceFill(['created_at' => $publishedAt])->save();

                $this->attachTaxonomies($post, $wpPost);

                return 'success';
            }

            $meta = [
                'wp_original_id' => $wpPost['id'],
                'wp_original_ids' => array_values(array_unique(array_filter($wpOriginalIds))),
                'wp_original_url' => $wpPost['link'] ?? null,
            ];

            if (! empty($wpPost['parent']) && (int) $wpPost['parent'] > 0) {
                $meta['wp_parent_id'] = (int) $wpPost['parent'];
            }

            // CPT Entry creation (populating primary columns to satisfy MySQL NOT NULL constraints)
            $entry = CptEntry::create([
                'post_type_id' => (int) $this->target,
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'excerpt' => $excerpt,
                'status' => 'published',
                'published_at' => $publishedAt,
                'author_id' => auth()->id() ?? 1,
                'featured_image' => $featuredImage,
                'meta' => $meta,
            ]);

            // If the initial post is in a non-default locale (e.g. Indonesian comes first), also store into translations JSON
            if ($wpLang !== $cmsDefaultLocale && method_exists($entry, 'setTranslation')) {
                $entry->setTranslation('title', $wpLang, $title);
                $entry->setTranslation('slug', $wpLang, $slug);
                $entry->setTranslation('content', $wpLang, $content);
                $entry->setTranslation('excerpt', $wpLang, $excerpt);
                $entry->save();
            }

            $entry->forceFill(['created_at' => $publishedAt])->save();

            return 'success';

            return 'success';

        } catch (\Exception $e) {
            Log::error('CLI WP Import error', ['post_id' => $wpPost['id'] ?? null, 'error' => $e->getMessage()]);

            return 'failed';
        }
    }

    protected function getFeaturedImage(array $wpPost): ?string
    {
        if (isset($wpPost['_embedded']['wp:featuredmedia'][0]['source_url'])) {
            return $this->downloadImage($wpPost['_embedded']['wp:featuredmedia'][0]['source_url']);
        }

        return null;
    }

    protected function processContentImages(string $content): string
    {
        if (empty($content)) {
            return $content;
        }

        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches);
        if (empty($matches[1])) {
            return $content;
        }

        $imageUrls = array_unique($matches[1]);
        $replacements = [];

        foreach ($imageUrls as $originalUrl) {
            try {
                $newPath = $this->downloadImage($originalUrl);
                if ($newPath && $newPath !== $originalUrl && ! Str::startsWith($newPath, 'http')) {
                    $replacements[$originalUrl] = '/storage/'.$newPath;
                }
            } catch (\Exception $e) {
                // ignore image fail
            }
        }

        foreach ($replacements as $oldUrl => $newUrl) {
            $content = str_replace($oldUrl, $newUrl, $content);
        }

        return $content;
    }

    protected function downloadImage(string $imageUrl): ?string
    {
        try {
            if (Str::startsWith($imageUrl, '//')) {
                $imageUrl = 'https:'.$imageUrl;
            }

            $response = Http::timeout(60)->withOptions(['verify' => false])->get($imageUrl);
            if (! $response->successful()) {
                return null;
            }

            $urlPath = parse_url($imageUrl, PHP_URL_PATH);
            $originalFilename = basename((string) $urlPath);
            $extension = strtolower(pathinfo((string) $urlPath, PATHINFO_EXTENSION));

            if (empty($extension) || ! in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                $extension = 'jpg';
            }

            $filename = 'wp-cpt-import-'.time().'-'.Str::random(8).'.'.$extension;
            $path = config('media.path', 'media').'/'.$filename;

            $disk = Storage::disk(config('media.disk', 'public'));
            $directory = dirname($path);
            if (! $disk->exists($directory)) {
                $disk->makeDirectory($directory);
            }

            $disk->put($path, $response->body());

            Media::create([
                'name' => pathinfo($originalFilename ?: $filename, PATHINFO_FILENAME),
                'filename' => $filename,
                'path' => $path,
                'mime_type' => (string) ($response->header('Content-Type') ?: 'image/jpeg'),
                'size' => strlen($response->body()),
                'title' => pathinfo($originalFilename ?: $filename, PATHINFO_FILENAME),
                'description' => 'Imported from WordPress CPT via CLI',
                'uploaded_by' => 1,
            ]);

            return $path;
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function attachTaxonomies($post, array $wpPost): void
    {
        $embeddedTerms = $wpPost['_embedded']['wp:term'] ?? [];
        if (empty($embeddedTerms)) {
            return;
        }

        $categoryIds = [];
        $tagIds = [];

        foreach ($embeddedTerms as $termGroup) {
            if (! is_array($termGroup)) {
                continue;
            }
            foreach ($termGroup as $term) {
                $taxonomy = $term['taxonomy'] ?? '';
                $name = html_entity_decode($term['name'] ?? '', ENT_QUOTES, 'UTF-8');
                $slug = $term['slug'] ?? Str::slug($name);

                if (empty($name)) {
                    continue;
                }

                if ($taxonomy === 'category') {
                    if (class_exists(Category::class)) {
                        $cat = Category::firstOrCreate(
                            ['slug' => $slug],
                            ['name' => $name]
                        );
                        $categoryIds[] = $cat->id;
                    }
                } elseif ($taxonomy === 'post_tag') {
                    if (class_exists(Tag::class)) {
                        $tag = Tag::firstOrCreate(
                            ['slug' => $slug],
                            ['name' => $name]
                        );
                        $tagIds[] = $tag->id;
                    }
                }
            }
        }

        if ($post instanceof Post) {
            if (! empty($categoryIds)) {
                $post->categories()->syncWithoutDetaching($categoryIds);
            }
            if (! empty($tagIds)) {
                $post->tags()->syncWithoutDetaching($tagIds);
            }
        }
    }
}
