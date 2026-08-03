<?php

namespace Plugins\Posts\Livewire;

use App\Models\Media;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Plugins\Posts\Models\Category;
use Plugins\Posts\Models\Post;
use Plugins\Posts\Models\PostAuthor;
use Plugins\Posts\Models\Tag;

class WordPressMigration extends Component
{
    // URL Input
    public $wpUrl = '';

    public $isValidUrl = false;

    // Basic Info (NOT storing full posts data to avoid payload issues)
    public $totalPosts = 0;

    public $totalPages = 0;

    public $perPage = 10;

    // Preview posts (only first 5 for display, not stored in state)
    public $previewPosts = [];

    // Field Mappings
    public $fieldMappings = [
        'title' => true,
        'slug' => true,
        'content' => true,
        'excerpt' => true,
        'published_at' => true,
        'featured_image' => 'download',
        'content_images' => true,
        'categories' => true,
        'tags' => true,
    ];

    // Import State
    public $step = 1; // 1: Input URL, 2: Configure & Import, 3: Results

    public $isLoading = false;

    public $importProgress = 0;

    public $currentPageImporting = 0;

    public $importResults = [];

    public $errorMessage = '';

    // Scanner 1 & 2 State
    public $scanner1Completed = false;

    public $scanner1Results = null;

    public $scanner2Completed = false;

    public $scanner2Results = null;

    protected $rules = [
        'wpUrl' => 'required|url',
    ];

    public function validateUrl()
    {
        $this->validate();

        // Normalize URL
        $url = rtrim($this->wpUrl, '/');

        // Check if it's a WP REST API URL
        if (! Str::contains($url, '/wp-json/wp/v2/posts')) {
            if (Str::contains($url, '/wp-json')) {
                $url = Str::before($url, '/wp-json').'/wp-json/wp/v2/posts';
            } else {
                $url = $url.'/wp-json/wp/v2/posts';
            }
        }

        $this->wpUrl = $url;
        $this->isValidUrl = true;
    }

    // Discovered Taxonomies & Mapping State
    public array $discoveredTaxonomies = [];

    public array $taxonomyMappings = [];

    public function fetchPostsInfo()
    {
        $this->isLoading = true;
        $this->errorMessage = '';

        try {
            $this->validateUrl();

            // Fetch first page to get total count
            $response = Http::timeout(30)->get($this->wpUrl, [
                'per_page' => $this->perPage,
                'page' => 1,
                '_embed' => true,
            ]);

            if ($response->failed()) {
                throw new \Exception('Failed to fetch posts from WordPress API. Status: '.$response->status());
            }

            $posts = $response->json();
            $this->totalPosts = (int) $response->header('X-WP-Total', count($posts));
            $this->totalPages = (int) $response->header('X-WP-TotalPages', 1);

            // Store only preview posts (first 5 for display)
            $this->previewPosts = collect($posts)->take(5)->map(function ($post) {
                return [
                    'id' => $post['id'],
                    'title' => html_entity_decode(strip_tags($post['title']['rendered'] ?? ''), ENT_QUOTES, 'UTF-8'),
                    'slug' => $post['slug'] ?? '',
                    'date' => $post['date'] ?? null,
                    'status' => $post['status'] ?? 'publish',
                ];
            })->toArray();

            // Detect Taxonomies
            $this->discoverTaxonomies($posts);

            if ($this->totalPosts === 0) {
                $this->errorMessage = 'No posts found at this URL.';
            } else {
                $this->step = 2;
            }

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }

        $this->isLoading = false;
    }

    protected function discoverTaxonomies(array $posts): void
    {
        $this->discoveredTaxonomies = [];
        $this->taxonomyMappings = [];

        // 1. Try WP REST API taxonomies endpoint
        try {
            $baseUrl = Str::before($this->wpUrl, '/wp/v2/posts');
            $taxResponse = Http::timeout(15)->get($baseUrl.'/wp/v2/taxonomies');
            if ($taxResponse->successful()) {
                $taxData = $taxResponse->json();
                foreach ($taxData as $slug => $tax) {
                    $name = $tax['name'] ?? $slug;
                    $types = $tax['types'] ?? [];
                    if (empty($types) || in_array('post', $types)) {
                        $target = ($slug === 'category' ? 'category' : ($slug === 'post_tag' ? 'tag' : 'category'));
                        $this->discoveredTaxonomies[$slug] = [
                            'name' => $name,
                            'slug' => $slug,
                            'target' => $target,
                        ];
                        $this->taxonomyMappings[$slug] = $target;
                    }
                }
            }
        } catch (\Exception $e) {
            // Ignore API taxonomy fetch error, fallback to _embedded
        }

        // 2. Fallback / supplementary scan from _embedded wp:term in preview posts
        foreach ($posts as $post) {
            $termGroups = $post['_embedded']['wp:term'] ?? [];
            foreach ($termGroups as $group) {
                if (! is_array($group)) {
                    continue;
                }
                foreach ($group as $term) {
                    $slug = $term['taxonomy'] ?? '';
                    if (empty($slug)) {
                        continue;
                    }
                    if (! isset($this->discoveredTaxonomies[$slug])) {
                        $name = ucfirst(str_replace(['_', '-'], ' ', $slug));
                        $target = ($slug === 'category' ? 'category' : ($slug === 'post_tag' ? 'tag' : 'category'));
                        $this->discoveredTaxonomies[$slug] = [
                            'name' => $name,
                            'slug' => $slug,
                            'target' => $target,
                        ];
                        $this->taxonomyMappings[$slug] = $target;
                    }
                }
            }
        }

        // Always ensure category and post_tag are present
        if (! isset($this->discoveredTaxonomies['category'])) {
            $this->discoveredTaxonomies['category'] = ['name' => 'Categories', 'slug' => 'category', 'target' => 'category'];
            $this->taxonomyMappings['category'] = 'category';
        }
        if (! isset($this->discoveredTaxonomies['post_tag'])) {
            $this->discoveredTaxonomies['post_tag'] = ['name' => 'Tags', 'slug' => 'post_tag', 'target' => 'tag'];
            $this->taxonomyMappings['post_tag'] = 'tag';
        }
    }

    // Batch Import State
    public $batchSize = 12;

    public $isBatchImporting = false;

    public $currentBatchIndex = 0;

    public $totalBatchCount = 0;

    public $targetTotalPosts = 0;

    public $currentBatchStatus = '';

    public $importFinished = false;

    public function startBatchImport(int $limit = 0)
    {
        $this->targetTotalPosts = ($limit > 0) ? min($limit, $this->totalPosts) : $this->totalPosts;
        $this->totalBatchCount = (int) max(1, ceil($this->targetTotalPosts / $this->batchSize));
        $this->currentBatchIndex = 0;
        $this->importProgress = 0;
        $this->isBatchImporting = true;
        $this->importFinished = false;
        $this->step = 3;
        $this->isLoading = false;
        $this->currentBatchStatus = "Starting batch 1 of {$this->totalBatchCount} (12 posts/batch)...";

        $this->importResults = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
            'categories' => 0,
            'tags' => 0,
            'skipped_posts' => [],
            'errors' => [],
        ];

        // Immediately run first batch
        $this->processNextBatch();
    }

    public function processNextBatch()
    {
        if (! $this->isBatchImporting || $this->importFinished) {
            return;
        }

        if ($this->currentBatchIndex >= $this->totalBatchCount) {
            $this->isBatchImporting = false;
            $this->importFinished = true;
            $this->importProgress = 100;
            Storage::disk('public')->deleteDirectory('cache');

            return;
        }

        $batchPage = $this->currentBatchIndex + 1;
        $this->currentBatchStatus = "Processing batch {$batchPage} of {$this->totalBatchCount} (12 posts/batch)...";

        try {
            $response = Http::timeout(60)->withOptions(['verify' => false])->get($this->wpUrl, [
                'per_page' => $this->batchSize,
                'page' => $batchPage,
                '_embed' => true,
            ]);

            if ($response->successful()) {
                $posts = $response->json();
                foreach ($posts as $wpPost) {
                    try {
                        $result = $this->importSinglePost($wpPost);
                        if ($result === 'success') {
                            $this->importResults['success']++;
                        } elseif ($result === 'skipped') {
                            $this->importResults['skipped']++;
                            $this->importResults['skipped_posts'][] = [
                                'title' => html_entity_decode(strip_tags($wpPost['title']['rendered'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8'),
                                'slug' => $wpPost['slug'] ?? '',
                                'reason' => 'Slug already exists',
                            ];
                        }
                    } catch (\Exception $e) {
                        $this->importResults['failed']++;
                        $this->importResults['errors'][] = [
                            'title' => $wpPost['title']['rendered'] ?? 'Unknown',
                            'error' => $e->getMessage(),
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Batch import page '.$batchPage.' failed', ['error' => $e->getMessage()]);
        }

        $this->currentBatchIndex++;
        $this->importProgress = min(100, (int) round(($this->currentBatchIndex / $this->totalBatchCount) * 100));

        if ($this->currentBatchIndex >= $this->totalBatchCount) {
            $this->isBatchImporting = false;
            $this->importFinished = true;
            $this->importProgress = 100;
        }
    }

    public function importPosts(int $limit = 0)
    {
        $this->startBatchImport($limit);
    }

    public function importAllPosts()
    {
        $this->startBatchImport(0);
    }

    protected function importSinglePost($wpPost)
    {
        $wpId = $wpPost['id'];
        $lang = strtolower($wpPost['lang'] ?? ($wpPost['polylang_current_lang'] ?? 'en'));
        $polylangTranslations = $wpPost['polylang_translations'] ?? ($wpPost['translations'] ?? []);

        // Extract title
        $title = html_entity_decode(strip_tags($wpPost['title']['rendered'] ?? ''), ENT_QUOTES, 'UTF-8');

        // Extract slug
        $slug = $wpPost['slug'] ?? Str::slug($title);

        // Extract content
        $content = $wpPost['content']['rendered'] ?? '';
        if ($this->fieldMappings['content_images'] ?? false) {
            $content = $this->processContentImages($content);
        }

        // Extract excerpt
        $excerpt = '';
        if ($this->fieldMappings['excerpt']) {
            $excerpt = strip_tags($wpPost['excerpt']['rendered'] ?? '');
            $excerpt = html_entity_decode(trim($excerpt), ENT_QUOTES, 'UTF-8');
        }

        // Handle published date
        $publishedAt = now();
        if ($this->fieldMappings['published_at']) {
            $publishedAt = Carbon::parse($wpPost['date'] ?? now());
        }

        // Collect all related WP Post IDs in this translation group
        $relatedWpIds = array_values(array_unique(array_filter(array_merge([$wpId], array_values($polylangTranslations)))));

        // Search if a Post model for this translation group already exists
        $existingPost = null;
        foreach ($relatedWpIds as $relId) {
            $found = Post::where('meta->wp_original_id', $relId)
                ->orWhereJsonContains('meta->wp_translation_ids', $relId)
                ->first();
            if ($found) {
                $existingPost = $found;
                break;
            }
        }

        // Resolve featured image with strict priority (EN WP -> ID WP -> EN Content -> ID Content)
        $featuredImage = $this->resolveFeaturedImage($existingPost, $wpPost, $lang, $content);

        // Auto-clean top body image if featured image exists and setting enabled
        if (($this->fieldMappings['auto_clean_top_image'] ?? true) && ! empty($featuredImage)) {
            $content = $this->removeTopImageFromContent($content);
        }

        if ($existingPost) {
            $translations = $existingPost->translations ?? [];

            // If this is English, set as primary columns
            if ($lang === 'en') {
                $existingPost->title = $title;
                $existingPost->slug = $slug;
                $existingPost->excerpt = $excerpt;
                $existingPost->content = $content;
            } else {
                $translations[$lang] = [
                    'title' => $title,
                    'slug' => $slug,
                    'excerpt' => $excerpt,
                    'content' => $content,
                ];
            }

            $existingPost->translations = ! empty($translations) ? $translations : null;

            if ($featuredImage) {
                $existingPost->featured_image = $featuredImage;
            }

            // Update meta translation IDs
            $meta = $existingPost->meta ?? [];
            $metaTr = $meta['wp_translation_ids'] ?? [];
            $meta['wp_translation_ids'] = array_values(array_unique(array_merge($metaTr, $relatedWpIds)));
            if ($lang === 'en' && ! empty($featuredImage)) {
                $meta['has_en_featured_image'] = true;
            }
            $existingPost->meta = $meta;

            $existingPost->save();

            // Attach taxonomies to existing post
            $this->attachTaxonomies($existingPost, $wpPost);

            return 'success';
        }

        // Otherwise, check if a post with same exact primary slug exists
        if (Post::where('slug', $slug)->exists()) {
            return 'skipped';
        }

        // Handle author
        $authorId = null;
        if (auth()->check()) {
            $currentUser = auth()->user();
            $author = PostAuthor::firstOrCreate(
                ['name' => $currentUser->name],
                ['slug' => Str::slug($currentUser->name), 'email' => $currentUser->email ?? 'admin@example.com']
            );
            $authorId = $author->id;
        } else {
            $author = PostAuthor::firstOrCreate(
                ['name' => 'Admin'],
                ['slug' => 'admin', 'email' => 'admin@example.com']
            );
            $authorId = $author->id;
        }

        $translations = [];
        if ($lang !== 'en') {
            $translations[$lang] = [
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt,
                'content' => $content,
            ];
        }

        $post = Post::create([
            'title' => $title,
            'slug' => $this->ensureUniqueSlug($slug),
            'content' => $content,
            'excerpt' => $excerpt,
            'featured_image' => $featuredImage,
            'status' => 'published',
            'published_at' => $publishedAt,
            'author_id' => $authorId,
            'translations' => ! empty($translations) ? $translations : null,
            'meta' => [
                'wp_original_id' => $wpId,
                'wp_original_url' => $wpPost['link'] ?? null,
                'wp_translation_ids' => $relatedWpIds,
                'has_en_featured_image' => ($lang === 'en' && ! empty($featuredImage)),
            ],
        ]);

        // Force set created_at to preserve original date for SEO
        if ($this->fieldMappings['published_at']) {
            $post->created_at = $publishedAt;
        }
        $post->save();

        // Handle taxonomies
        $this->attachTaxonomies($post, $wpPost);

        return 'success';
    }

    /**
     * Process content to download and replace image URLs
     */
    protected function processContentImages($content)
    {
        if (empty($content)) {
            return $content;
        }

        // First, remove srcset and sizes attributes from all img tags
        $content = preg_replace('/\s+srcset=["\'][^"\']*["\']/', '', $content);
        $content = preg_replace('/\s+sizes=["\'][^"\']*["\']/', '', $content);

        // Find all img tags and their src attributes
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches);

        if (empty($matches[1])) {
            return $content;
        }

        $replacements = [];

        foreach ($matches[1] as $originalUrl) {
            $cleanUrl = html_entity_decode($originalUrl, ENT_QUOTES, 'UTF-8');

            // Skip if already a local URL
            if (Str::startsWith($cleanUrl, '/storage/') || Str::startsWith($cleanUrl, '/media/')) {
                continue;
            }

            if (Str::startsWith($cleanUrl, '/') && ! Str::startsWith($cleanUrl, '//')) {
                continue;
            }

            // Skip data URLs
            if (Str::startsWith($cleanUrl, 'data:')) {
                continue;
            }

            try {
                $newPath = $this->downloadImage($cleanUrl);

                if ($newPath && ! Str::startsWith($newPath, 'http')) {
                    $newUrl = '/storage/'.$newPath;
                    $replacements[$originalUrl] = $newUrl;
                    if ($cleanUrl !== $originalUrl) {
                        $replacements[$cleanUrl] = $newUrl;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to download content image: '.$cleanUrl);
            }
        }

        if (! empty($replacements)) {
            $content = strtr($content, $replacements);
        }

        return $content;
    }

    /**
     * Resolve featured image based on strict priority:
     * 1. WP Featured Image EN
     * 2. WP Featured Image ID
     * 3. First image in EN content body
     * 4. First image in ID content body
     */
    protected function resolveFeaturedImage($existingPost, $wpPost, $lang, $content): ?string
    {
        if (($this->fieldMappings['featured_image'] ?? 'download') === 'skip') {
            return null;
        }

        // 1. If existing post already has EN Featured Image, keep it!
        $currentFeatured = $existingPost ? $existingPost->featured_image : null;
        $hasEnFeatured = $existingPost ? ! empty($existingPost->meta['has_en_featured_image']) : false;

        if ($hasEnFeatured && ! empty($currentFeatured)) {
            return $currentFeatured;
        }

        // 2. Try WP Featured Image from current post (Priority 1 for EN, Priority 2 for ID)
        $wpFeatured = $this->getFeaturedImage($wpPost);

        if ($wpFeatured) {
            return $wpFeatured;
        }

        // 3. If post already has a featured image (e.g. from ID), and current post is not EN, keep it
        if (! empty($currentFeatured) && $lang !== 'en') {
            return $currentFeatured;
        }

        // 4. Priority 3 & 4: Extract first image from content if enabled
        if ($this->fieldMappings['auto_extract_featured'] ?? true) {
            $firstContentImage = $this->extractFirstImageUrl($content);
            if ($firstContentImage) {
                return $firstContentImage;
            }
        }

        return $currentFeatured;
    }

    /**
     * Get featured image path or URL
     */
    protected function getFeaturedImage($wpPost)
    {
        // Check if _embedded has featured media
        $featuredMedia = $wpPost['_embedded']['wp:featuredmedia'][0] ?? null;

        if (! $featuredMedia) {
            return null;
        }

        $imageUrl = $featuredMedia['source_url'] ?? null;

        if (! $imageUrl) {
            return null;
        }

        if ($this->fieldMappings['featured_image'] === 'url') {
            return $imageUrl;
        }

        // Download image to media library
        return $this->downloadImage($imageUrl);
    }

    /**
     * Download image to storage and register in Media Library
     */
    protected function downloadImage($url)
    {
        try {
            if (Str::startsWith($url, '//')) {
                $url = 'https:'.$url;
            }

            $response = Http::timeout(45)->withOptions([
                'verify' => false,
            ])->get($url);

            if (! $response->successful()) {
                return null;
            }

            // Generate filename
            $urlPath = parse_url($url, PHP_URL_PATH) ?? '';
            $originalFilename = basename($urlPath);
            $extension = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));

            if (empty($extension) || ! in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                $extension = 'jpg';
            }

            $filename = 'wp-import-'.time().'-'.Str::random(8).'.'.$extension;

            // Store file
            $path = 'media/'.$filename;
            Storage::disk('public')->put($path, $response->body());

            $userId = auth()->id() ?? (User::first()->id ?? 1);

            // Register in Media Library
            Media::create([
                'filename' => $filename,
                'original_filename' => $originalFilename ?: $filename,
                'mime_type' => $response->header('Content-Type') ?? 'image/jpeg',
                'file_extension' => $extension,
                'size' => strlen($response->body()),
                'path' => $path,
                'title' => pathinfo($originalFilename ?: $filename, PATHINFO_FILENAME),
                'description' => 'Imported from WordPress',
                'uploaded_by' => $userId,
            ]);

            return $path;

        } catch (\Exception $e) {
            Log::error('Failed to download image: '.$url, ['error' => $e->getMessage()]);

            return null;
        }
    }

    protected function ensureUniqueSlug($slug)
    {
        $originalSlug = $slug;
        $counter = 1;

        while (Post::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    protected function attachTaxonomies($post, $wpPost)
    {
        $termGroups = $wpPost['_embedded']['wp:term'] ?? [];
        if (! is_array($termGroups) || empty($termGroups)) {
            return;
        }

        $categoryIds = [];
        $tagIds = [];

        foreach ($termGroups as $group) {
            if (! is_array($group)) {
                continue;
            }
            foreach ($group as $term) {
                $taxonomySlug = $term['taxonomy'] ?? '';
                $targetAction = $this->taxonomyMappings[$taxonomySlug] ?? ($taxonomySlug === 'category' ? 'category' : ($taxonomySlug === 'post_tag' ? 'tag' : 'skip'));

                if ($targetAction === 'skip') {
                    continue;
                }

                $termName = html_entity_decode($term['name'] ?? '', ENT_QUOTES, 'UTF-8');
                $termSlug = $term['slug'] ?? Str::slug($termName);
                $termLang = strtolower($term['lang'] ?? ($wpPost['lang'] ?? 'en'));

                if (empty($termName)) {
                    continue;
                }

                if ($targetAction === 'category') {
                    // Search existing category by slug, base slug (strip WP -2 suffix), or translations
                    $baseSlug = preg_replace('/-\d+$/', '', $termSlug);
                    $category = Category::where('slug', $termSlug)
                        ->orWhere('slug', $baseSlug)
                        ->orWhere('translations->en->slug', $termSlug)
                        ->orWhere('translations->id->slug', $termSlug)
                        ->orWhere('translations->en->slug', $baseSlug)
                        ->orWhere('translations->id->slug', $baseSlug)
                        ->orWhereRaw('LOWER(name) = ?', [strtolower($termName)])
                        ->first();

                    if (! $category) {
                        $category = Category::create([
                            'name' => $termName,
                            'slug' => $baseSlug,
                            'description' => $term['description'] ?? '',
                        ]);
                    }

                    // Always save translation for current term's language
                    $category->setTranslation('name', $termLang, $termName);
                    $category->setTranslation('slug', $termLang, $termSlug);
                    if (! empty($term['description'])) {
                        $category->setTranslation('description', $termLang, $term['description']);
                    }
                    $category->save();

                    $categoryIds[] = $category->id;
                } elseif ($targetAction === 'tag') {
                    $baseSlug = preg_replace('/-\d+$/', '', $termSlug);
                    $tag = Tag::where('slug', $termSlug)
                        ->orWhere('slug', $baseSlug)
                        ->orWhere('translations->en->slug', $termSlug)
                        ->orWhere('translations->id->slug', $termSlug)
                        ->orWhere('translations->en->slug', $baseSlug)
                        ->orWhere('translations->id->slug', $baseSlug)
                        ->orWhereRaw('LOWER(name) = ?', [strtolower($termName)])
                        ->first();

                    if (! $tag) {
                        $tag = Tag::create([
                            'name' => $termName,
                            'slug' => $baseSlug,
                        ]);
                    }

                    $tag->setTranslation('name', $termLang, $termName);
                    $tag->setTranslation('slug', $termLang, $termSlug);
                    $tag->save();

                    $tagIds[] = $tag->id;
                }
            }
        }

        if (! empty($categoryIds)) {
            $post->categories()->syncWithoutDetaching($categoryIds);
        }
        if (! empty($tagIds)) {
            $post->tags()->syncWithoutDetaching($tagIds);
        }
    }

    public function runScanner1()
    {
        $this->isLoading = true;
        $this->errorMessage = '';

        try {
            $postsWithoutFeatured = Post::where(function ($q) {
                $q->whereNull('featured_image')->orWhere('featured_image', '');
            })->get();

            $scanned = $postsWithoutFeatured->count();
            $updated = 0;
            $skipped = 0;

            foreach ($postsWithoutFeatured as $post) {
                $content = $post->content ?? '';
                if (empty($content) && is_array($post->translations)) {
                    foreach ($post->translations as $tData) {
                        if (! empty($tData['content'])) {
                            $content = $tData['content'];
                            break;
                        }
                    }
                }

                $firstImageUrl = $this->extractFirstImageUrl($content);

                if ($firstImageUrl) {
                    $post->featured_image = $firstImageUrl;
                    $post->save();
                    $updated++;
                } else {
                    $skipped++;
                }
            }

            $this->scanner1Results = [
                'scanned' => $scanned,
                'updated' => $updated,
                'skipped' => $skipped,
            ];
            $this->scanner1Completed = true;
        } catch (\Exception $e) {
            $this->errorMessage = 'Scanner 1 Error: '.$e->getMessage();
            Log::error('Scanner 1 failed', ['error' => $e->getMessage()]);
        }

        $this->isLoading = false;
    }

    protected function extractFirstImageUrl(?string $content): ?string
    {
        if (empty($content)) {
            return null;
        }

        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches)) {
            $src = trim($matches[1]);
            if (! empty($src)) {
                if (Str::startsWith($src, '/storage/')) {
                    return Str::after($src, '/storage/');
                }

                return $src;
            }
        }

        return null;
    }

    public function runScanner2()
    {
        if (! $this->scanner1Completed) {
            $this->errorMessage = 'Scanner 2 can only be run after Scanner 1 is completed.';

            return;
        }

        $this->isLoading = true;
        $this->errorMessage = '';

        try {
            $postsWithFeatured = Post::whereNotNull('featured_image')
                ->where('featured_image', '!=', '')
                ->get();

            $scanned = $postsWithFeatured->count();
            $cleaned = 0;

            foreach ($postsWithFeatured as $post) {
                $wasModified = false;

                if (! empty($post->content)) {
                    $newContent = $this->removeTopImageFromContent($post->content);
                    if ($newContent !== $post->content) {
                        $post->content = $newContent;
                        $wasModified = true;
                    }
                }

                if (is_array($post->translations)) {
                    $translations = $post->translations;
                    foreach ($translations as $loc => $tData) {
                        if (! empty($tData['content'])) {
                            $newTContent = $this->removeTopImageFromContent($tData['content']);
                            if ($newTContent !== $tData['content']) {
                                $translations[$loc]['content'] = $newTContent;
                                $wasModified = true;
                            }
                        }
                    }
                    if ($wasModified) {
                        $post->translations = $translations;
                    }
                }

                if ($wasModified) {
                    $post->save();
                    $cleaned++;
                }
            }

            $this->scanner2Results = [
                'scanned' => $scanned,
                'cleaned' => $cleaned,
            ];
            $this->scanner2Completed = true;
        } catch (\Exception $e) {
            $this->errorMessage = 'Scanner 2 Error: '.$e->getMessage();
            Log::error('Scanner 2 failed', ['error' => $e->getMessage()]);
        }

        $this->isLoading = false;
    }

    protected function removeTopImageFromContent(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        $trimmed = trim($content);

        // Pattern 1: <p...><img.../></p> or <figure...><img.../></figure> at beginning
        $patternContainer = '/^\s*<(p|figure|div)[^>]*>\s*<img[^>]+>\s*<\/\1>/i';
        if (preg_match($patternContainer, $trimmed)) {
            return trim((string) preg_replace($patternContainer, '', $trimmed));
        }

        // Pattern 2: Naked <img ...> at beginning
        $patternNaked = '/^\s*<img[^>]+>/i';
        if (preg_match($patternNaked, $trimmed)) {
            return trim((string) preg_replace($patternNaked, '', $trimmed));
        }

        // Pattern 3: Top container with img after whitespace or empty tags
        $patternTopBlock = '/^(\s*(?:<p>\s*<\/p>|<br\s*\/?>|\s*)*)<(?:p|figure|div)[^>]*>\s*<img[^>]+>\s*<\/(?:p|figure|div)>/i';
        if (preg_match($patternTopBlock, $trimmed)) {
            return trim((string) preg_replace($patternTopBlock, '$1', $trimmed));
        }

        return $content;
    }

    public function resetMigration()
    {
        $this->step = 1;
        $this->wpUrl = '';
        $this->totalPosts = 0;
        $this->totalPages = 0;
        $this->previewPosts = [];
        $this->importProgress = 0;
        $this->currentPageImporting = 0;
        $this->importResults = [];
        $this->errorMessage = '';
        $this->scanner1Completed = false;
        $this->scanner1Results = null;
        $this->scanner2Completed = false;
        $this->scanner2Results = null;
    }

    public function render()
    {
        return view('posts::livewire.wordpress-migration');
    }
}
