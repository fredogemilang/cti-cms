<?php

namespace App\Livewire\Admin;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;

class WordPressCptMigration extends Component
{
    // URL Input
    public $wpUrl = '';

    public $isValidUrl = false;

    // WordPress CPT Discovery
    public $availableCpts = []; // List of CPTs from WordPress

    public $selectedWpCpt = ''; // Selected WordPress CPT slug

    public $wpCptFields = []; // Sample fields from WordPress CPT

    // CMS CPT Selection
    public $cmsCpts = []; // Available CPTs in CMS

    public $selectedCmsCpt = ''; // Selected CMS CPT ID

    public $cmsCptFields = []; // Available fields in CMS CPT

    // Field Mappings
    public $fieldMappings = [];

    // Import Options
    public $downloadFeaturedImage = true;

    public $downloadContentImages = true;

    // Basic Info
    public $totalPosts = 0;

    public $totalPages = 0;

    public $perPage = 10;

    public $previewPosts = [];

    // Polylang / Bilingual State
    public $isPolylang = false;

    public $polylangLanguages = []; // e.g. ['en' => 19, 'id' => 17]

    public $defaultImportLocale = 'en'; // Which language goes to primary columns

    // Post Selection State (Step 3)
    public $selectedLanguages = []; // e.g. ['en', 'id'] — which languages to import

    public $selectedPostIds = []; // Individual post IDs to import

    public $selectAllPosts = true; // Select all by default

    public $fetchAllDone = false;

    // Import State
    public $step = 1; // 1: Input URL, 2: Select CPT, 3: Preview & Select, 4: Field Mapping, 5: Results

    public $isLoading = false;

    public $importProgress = 0;

    public $currentPageImporting = 0;

    public $importResults = [];

    public $errorMessage = '';

    public function mount()
    {
        // Load available CMS CPTs
        $this->cmsCpts = CustomPostType::active()->get()->map(function ($cpt) {
            return [
                'id' => $cpt->id,
                'name' => $cpt->name,
                'slug' => $cpt->slug,
                'singular_label' => $cpt->singular_label,
            ];
        })->toArray();
    }

    public function validateUrl()
    {
        $this->validate([
            'wpUrl' => 'required|url',
        ]);

        // Normalize URL
        $url = rtrim($this->wpUrl, '/');

        // Remove any path after domain
        if (Str::contains($url, '/wp-json')) {
            $url = Str::before($url, '/wp-json');
        }

        $this->wpUrl = $url;
        $this->isValidUrl = true;
    }

    public function fetchCptTypes()
    {
        $this->isLoading = true;
        $this->errorMessage = '';

        try {
            $this->validateUrl();

            // Fetch available post types from WordPress
            $response = Http::timeout(30)->get($this->wpUrl.'/wp-json/wp/v2/types');

            if ($response->failed()) {
                throw new \Exception('Failed to fetch post types from WordPress API.');
            }

            $types = $response->json();
            $this->availableCpts = [];

            // Filter out built-in types, keep only custom ones
            $builtInTypes = ['post', 'page', 'attachment', 'revision', 'nav_menu_item', 'wp_block', 'wp_template', 'wp_template_part', 'wp_navigation'];

            foreach ($types as $slug => $type) {
                // Include posts and pages too, plus any custom types
                if (! in_array($slug, ['attachment', 'revision', 'nav_menu_item', 'wp_block', 'wp_template', 'wp_template_part', 'wp_navigation'])) {
                    $this->availableCpts[] = [
                        'slug' => $slug,
                        'name' => $type['name'] ?? $slug,
                        'rest_base' => $type['rest_base'] ?? $slug,
                        'description' => $type['description'] ?? '',
                    ];
                }
            }

            if (empty($this->availableCpts)) {
                $this->errorMessage = 'No importable post types found.';
            } else {
                $this->step = 2;
            }

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }

        $this->isLoading = false;
    }

    public function selectWpCpt()
    {
        $this->isLoading = true;
        $this->errorMessage = '';

        try {
            if (empty($this->selectedWpCpt)) {
                throw new \Exception('Please select a WordPress post type.');
            }

            // Get the rest_base for the selected CPT
            $selectedCpt = collect($this->availableCpts)->firstWhere('slug', $this->selectedWpCpt);
            $restBase = $selectedCpt['rest_base'] ?? $this->selectedWpCpt;

            // Fetch sample posts to discover fields
            $response = Http::timeout(30)->get($this->wpUrl.'/wp-json/wp/v2/'.$restBase, [
                'per_page' => 10,
                '_embed' => true,
            ]);

            if ($response->failed()) {
                throw new \Exception('Failed to fetch sample data from WordPress.');
            }

            $posts = $response->json();
            $this->totalPosts = (int) $response->header('X-WP-Total', count($posts));
            // Recalculate totalPages using our perPage for efficient API calls
            $this->totalPages = (int) ceil($this->totalPosts / $this->perPage);

            // Detect Polylang: check if posts have 'lang' field across multiple samples
            $this->detectPolylang($posts);

            // Discover available fields from sample post
            $this->wpCptFields = [];
            if (! empty($posts[0])) {
                $samplePost = $posts[0];
                $this->discoverFields($samplePost);
            }

            // Initialize default field mappings
            $this->initializeFieldMappings();

            // Fetch all posts for preview selection
            $this->fetchAllPostsForPreview($restBase);
            $this->step = 3;

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }

        $this->isLoading = false;
    }

    public function fetchAllPostsForPreview(string $restBase): void
    {
        $this->fetchAllDone = false;
        $this->previewPosts = [];

        // Fetch all posts across all pages
        for ($page = 1; $page <= $this->totalPages; $page++) {
            $response = Http::timeout(60)->get($this->wpUrl.'/wp-json/wp/v2/'.$restBase, [
                'per_page' => $this->perPage,
                'page' => $page,
                '_embed' => true,
            ]);

            if ($response->failed()) {
                continue;
            }

            foreach ($response->json() as $post) {
                $this->previewPosts[] = [
                    'id' => $post['id'],
                    'title' => html_entity_decode(strip_tags($post['title']['rendered'] ?? ''), ENT_QUOTES, 'UTF-8'),
                    'slug' => $post['slug'] ?? '',
                    'lang' => $post['lang'] ?? null,
                    'featured_media' => $post['featured_media'] ?? null,
                    'has_image' => ! empty($post['_embedded']['wp:featuredmedia'][0]['source_url']) || ! empty($post['featured_media']),
                ];
            }
        }

        // Auto-select all languages if Polylang detected
        if ($this->isPolylang) {
            $this->selectedLanguages = array_keys($this->polylangLanguages);
        }

        $this->selectAllPosts = true;
        $this->selectedPostIds = array_column($this->previewPosts, 'id');
        $this->fetchAllDone = true;
    }

    public function toggleLanguage($lang): void
    {
        if (in_array($lang, $this->selectedLanguages)) {
            $this->selectedLanguages = array_values(array_diff($this->selectedLanguages, [$lang]));
        } else {
            $this->selectedLanguages[] = $lang;
        }
        $this->updateSelectedPostIds();
    }

    public function toggleAllPosts(): void
    {
        $this->selectAllPosts = ! $this->selectAllPosts;
        if ($this->selectAllPosts) {
            $this->selectedPostIds = array_column($this->previewPosts, 'id');
        } else {
            $this->selectedPostIds = [];
        }
        $this->updateSelectedPostIds();
    }

    public function togglePost($postId): void
    {
        if (in_array($postId, $this->selectedPostIds)) {
            $this->selectedPostIds = array_values(array_diff($this->selectedPostIds, [$postId]));
        } else {
            $this->selectedPostIds[] = $postId;
        }
        $this->selectAllPosts = count($this->selectedPostIds) === count($this->previewPosts);
    }

    protected function updateSelectedPostIds(): void
    {
        if (! $this->isPolylang || empty($this->selectedLanguages)) {
            // No language filter — select all or none
            return;
        }

        $this->selectedPostIds = collect($this->previewPosts)
            ->filter(fn ($p) => in_array($p['lang'], $this->selectedLanguages))
            ->pluck('id')
            ->toArray();
    }

    protected function discoverFields($samplePost, $prefix = '')
    {
        $ignoredFields = ['_links', '_embedded', 'guid', 'type', 'link', 'template'];

        foreach ($samplePost as $key => $value) {
            if (in_array($key, $ignoredFields)) {
                continue;
            }

            $fieldPath = $prefix ? $prefix.'.'.$key : $key;

            if (is_array($value) && ! empty($value)) {
                // Check if it's an associative array with 'rendered' key (like title, content)
                if (isset($value['rendered'])) {
                    $this->wpCptFields[] = [
                        'path' => $fieldPath.'.rendered',
                        'label' => ucfirst(str_replace('_', ' ', $key)).' (rendered)',
                        'sample' => Str::limit(strip_tags($value['rendered']), 50),
                    ];
                } elseif ($key === 'acf' || $key === 'meta') {
                    // Recursively discover ACF/meta fields
                    $this->discoverFields($value, $fieldPath);
                }
            } elseif (is_scalar($value)) {
                $this->wpCptFields[] = [
                    'path' => $fieldPath,
                    'label' => ucfirst(str_replace('_', ' ', $key)),
                    'sample' => Str::limit((string) $value, 50),
                ];
            }
        }
    }

    protected function initializeFieldMappings()
    {
        // Default mappings for common fields
        $this->fieldMappings = [
            'title' => 'title.rendered',
            'slug' => 'slug',
            'content' => 'content.rendered',
            'excerpt' => 'excerpt.rendered',
            'featured_image' => 'featured_media',
            'published_at' => 'date',
        ];

        // Get CMS CPT meta fields
        $this->loadCmsCptFields();
    }

    public function loadCmsCptFields()
    {
        $this->cmsCptFields = [
            ['key' => 'title', 'label' => 'Title'],
            ['key' => 'slug', 'label' => 'Slug'],
            ['key' => 'content', 'label' => 'Content'],
            ['key' => 'excerpt', 'label' => 'Excerpt'],
            ['key' => 'featured_image', 'label' => 'Featured Image'],
            ['key' => 'published_at', 'label' => 'Published Date'],
        ];

        // Add meta fields from selected CMS CPT
        if ($this->selectedCmsCpt) {
            $cpt = CustomPostType::find($this->selectedCmsCpt);
            if ($cpt && $cpt->metaFields) {
                foreach ($cpt->metaFields as $metaField) {
                    $this->cmsCptFields[] = [
                        'key' => 'meta.'.$metaField->name,
                        'label' => $metaField->label ?? $metaField->name,
                    ];
                }
            }
        }
    }

    public function updatedSelectedCmsCpt()
    {
        $this->loadCmsCptFields();
    }

    public function updateFieldMapping($cmsField, $wpField)
    {
        $this->fieldMappings[$cmsField] = $wpField;
    }

    protected function detectPolylang(array $samplePosts): void
    {
        $this->isPolylang = false;
        $this->polylangLanguages = [];

        // Check if posts have a 'lang' field (Polylang adds this to REST response)
        foreach ($samplePosts as $post) {
            if (! empty($post['lang'])) {
                $lang = $post['lang'];
                $this->polylangLanguages[$lang] = ($this->polylangLanguages[$lang] ?? 0) + 1;
            }
        }

        if (count($this->polylangLanguages) > 1) {
            $this->isPolylang = true;
            // Default import locale: use CMS default or first language found
            $defaultLocale = CptEntry::defaultLocale();
            $this->defaultImportLocale = isset($this->polylangLanguages[$defaultLocale])
                ? $defaultLocale
                : array_key_first($this->polylangLanguages);
        }
    }

    public function importAllPosts()
    {
        $this->isLoading = true;
        $this->importProgress = 0;
        $this->currentPageImporting = 0;
        $this->importResults = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
            'translated' => 0,
            'skipped_posts' => [],
            'errors' => [],
        ];

        try {
            if (empty($this->selectedCmsCpt)) {
                throw new \Exception('Please select a target CMS post type.');
            }

            if (empty($this->selectedPostIds)) {
                throw new \Exception('Please select at least one post to import.');
            }

            // Get the rest_base for the selected CPT
            $selectedCpt = collect($this->availableCpts)->firstWhere('slug', $this->selectedWpCpt);
            $restBase = $selectedCpt['rest_base'] ?? $this->selectedWpCpt;

            if ($this->isPolylang) {
                $this->importPolylangPosts($restBase);
            } else {
                $this->importMonolingualPosts($restBase);
            }

        } catch (\Exception $e) {
            $this->errorMessage = 'Import failed: '.$e->getMessage();
            Log::error('WordPress CPT import failed', ['error' => $e->getMessage()]);
        }

        $this->step = 5;
        $this->isLoading = false;
    }

    protected function importMonolingualPosts(string $restBase): void
    {
        $selectedIds = array_flip($this->selectedPostIds);

        for ($page = 1; $page <= $this->totalPages; $page++) {
            $this->currentPageImporting = $page;

            $response = Http::timeout(60)->get($this->wpUrl.'/wp-json/wp/v2/'.$restBase, [
                'per_page' => $this->perPage,
                'page' => $page,
                '_embed' => true,
            ]);

            if ($response->failed()) {
                continue;
            }

            foreach ($response->json() as $wpPost) {
                if (! isset($selectedIds[$wpPost['id']])) {
                    continue; // Not selected by user
                }
                $this->importOrSkip($wpPost);
            }

            $this->importProgress = round(($page / $this->totalPages) * 100);
        }
    }

    protected function importPolylangPosts(string $restBase): void
    {
        $selectedIds = array_flip($this->selectedPostIds);

        // Fetch ALL posts (filtered by selected IDs) to pair them by language
        $allPosts = [];
        for ($page = 1; $page <= $this->totalPages; $page++) {
            $response = Http::timeout(60)->get($this->wpUrl.'/wp-json/wp/v2/'.$restBase, [
                'per_page' => $this->perPage,
                'page' => $page,
                '_embed' => true,
            ]);
            if (! $response->failed()) {
                foreach ($response->json() as $post) {
                    if (isset($selectedIds[$post['id']])) {
                        $allPosts[] = $post;
                    }
                }
            }
            $this->importProgress = round(($page / $this->totalPages) * 50); // 0–50%: fetching
        }

        // Group posts: keyed by slug (or title) → collect language variants
        $groups = [];
        foreach ($allPosts as $post) {
            $key = $post['slug'] ?? Str::slug($post['title']['rendered'] ?? '');
            if (! isset($groups[$key])) {
                $groups[$key] = [];
            }
            $groups[$key][] = $post;
        }

        $this->currentPageImporting = 0;
        $total = count($groups);
        $done = 0;

        foreach ($groups as $slugKey => $variants) {
            $done++;
            $this->currentPageImporting = $done;

            // Find the default-locale post first
            $defaultPost = null;
            $otherPosts = [];
            foreach ($variants as $post) {
                if (($post['lang'] ?? '') === $this->defaultImportLocale) {
                    $defaultPost = $post;
                } else {
                    $otherPosts[] = $post;
                }
            }
            // If no default-locale post found, use first available
            if (! $defaultPost) {
                $defaultPost = array_shift($variants);
                $otherPosts = $variants;
            }

            // Import the default post as primary entry
            $result = $this->importOrSkip($defaultPost);
            if ($result === 'success' && ! empty($otherPosts)) {
                // Fetch the newly created entry by slug
                $entry = CptEntry::where('slug', $defaultPost['slug'] ?? Str::slug($defaultPost['title']['rendered'] ?? ''))
                    ->where('post_type_id', $this->selectedCmsCpt)
                    ->first();

                if ($entry) {
                    // Add translations for other languages
                    foreach ($otherPosts as $otherPost) {
                        $otherLang = $otherPost['lang'] ?? 'other';
                        $transTitle = html_entity_decode(strip_tags($this->getWpFieldValue($otherPost, 'title.rendered') ?? ''), ENT_QUOTES, 'UTF-8');
                        $transSlug = $otherPost['slug'] ?? Str::slug($transTitle);
                        $transContent = $this->getWpFieldValue($otherPost, 'content.rendered') ?? '';

                        // Append locale to slug to avoid conflicts
                        $transSlug = $this->localizeSlug($transSlug, $otherLang);

                        $entry->setTranslation('title', $otherLang, $transTitle);
                        $entry->setTranslation('slug', $otherLang, $transSlug);
                        $entry->setTranslation('content', $otherLang, $transContent);
                        $entry->save();

                        $this->importResults['translated']++;
                    }
                }
            }

            $this->importProgress = 50 + round(($done / $total) * 50); // 50–100%: processing
        }
    }

    /**
     * Append locale to slug to ensure uniqueness across translations.
     */
    protected function localizeSlug(string $slug, string $locale): string
    {
        if (str_ends_with($slug, '-'.$locale)) {
            return $slug;
        }

        return $slug.'-'.$locale;
    }

    protected function importOrSkip(array $wpPost): string
    {
        try {
            $result = $this->importSinglePost($wpPost);

            if ($result === 'success') {
                $this->importResults['success']++;
            } elseif ($result === 'skipped') {
                $this->importResults['skipped']++;
                $this->importResults['skipped_posts'][] = [
                    'title' => $this->getWpFieldValue($wpPost, 'title.rendered') ?? 'Unknown',
                    'slug' => $wpPost['slug'] ?? '',
                    'reason' => 'Slug already exists',
                ];
            }

            return $result;
        } catch (\Exception $e) {
            $this->importResults['failed']++;
            $this->importResults['errors'][] = [
                'title' => $this->getWpFieldValue($wpPost, 'title.rendered') ?? 'Unknown',
                'error' => $e->getMessage(),
            ];

            return 'failed';
        }
    }

    protected function importSinglePost($wpPost)
    {
        // Get mapped values
        $title = html_entity_decode(strip_tags($this->getWpFieldValue($wpPost, $this->fieldMappings['title'] ?? 'title.rendered') ?? ''), ENT_QUOTES, 'UTF-8');
        $slug = $this->getWpFieldValue($wpPost, $this->fieldMappings['slug'] ?? 'slug') ?? Str::slug($title);

        // Check if entry with same slug already exists
        if (CptEntry::where('slug', $slug)->where('post_type_id', $this->selectedCmsCpt)->exists()) {
            return 'skipped';
        }

        // Get content
        $content = $this->getWpFieldValue($wpPost, $this->fieldMappings['content'] ?? 'content.rendered') ?? '';

        // Process content images if enabled
        if ($this->downloadContentImages) {
            $content = $this->processContentImages($content);
        }

        // Get excerpt
        $excerpt = strip_tags($this->getWpFieldValue($wpPost, $this->fieldMappings['excerpt'] ?? 'excerpt.rendered') ?? '');
        $excerpt = html_entity_decode(trim($excerpt), ENT_QUOTES, 'UTF-8');

        // Handle published date
        $publishedAt = Carbon::parse($this->getWpFieldValue($wpPost, $this->fieldMappings['published_at'] ?? 'date') ?? now());

        // Handle featured image
        $featuredImage = null;
        if ($this->downloadFeaturedImage) {
            $featuredImage = $this->getFeaturedImage($wpPost);
        }

        // Build meta data from mapped meta fields
        $meta = [
            'wp_original_id' => $wpPost['id'],
            'wp_original_url' => $wpPost['link'] ?? null,
        ];

        foreach ($this->fieldMappings as $cmsField => $wpField) {
            if (Str::startsWith($cmsField, 'meta.')) {
                $metaKey = Str::after($cmsField, 'meta.');
                $meta[$metaKey] = $this->getWpFieldValue($wpPost, $wpField);
            }
        }

        // Create the CPT entry
        $entry = CptEntry::create([
            'post_type_id' => $this->selectedCmsCpt,
            'title' => $title,
            'slug' => $this->ensureUniqueSlug($slug),
            'content' => $content,
            'excerpt' => $excerpt,
            'featured_image' => $featuredImage,
            'status' => 'published',
            'published_at' => $publishedAt,
            'author_id' => auth()->id(),
            'meta' => $meta,
        ]);

        // Force set created_at to preserve original date
        $entry->created_at = $publishedAt;
        $entry->save();

        return 'success';
    }

    protected function getWpFieldValue($wpPost, $fieldPath)
    {
        if (empty($fieldPath)) {
            return null;
        }

        $parts = explode('.', $fieldPath);
        $value = $wpPost;

        foreach ($parts as $part) {
            if (is_array($value) && isset($value[$part])) {
                $value = $value[$part];
            } else {
                return null;
            }
        }

        return $value;
    }

    protected function processContentImages($content)
    {
        // Remove srcset and sizes attributes
        $content = preg_replace('/\s+srcset=["\'][^"\']*["\']/', '', $content);
        $content = preg_replace('/\s+sizes=["\'][^"\']*["\']/', '', $content);

        // Find all img tags and their src attributes
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches);

        if (empty($matches[1])) {
            return $content;
        }

        $replacements = [];

        foreach ($matches[1] as $originalUrl) {
            if (Str::startsWith($originalUrl, '/storage/') || Str::startsWith($originalUrl, '/media/')) {
                continue;
            }

            if (Str::startsWith($originalUrl, '/') && ! Str::startsWith($originalUrl, '//')) {
                continue;
            }

            if (Str::startsWith($originalUrl, 'data:')) {
                continue;
            }

            try {
                $newPath = $this->downloadImage($originalUrl);

                if ($newPath && $newPath !== $originalUrl && ! Str::startsWith($newPath, 'http')) {
                    $newUrl = '/storage/'.$newPath;
                    $replacements[$originalUrl] = $newUrl;
                }
            } catch (\Exception $e) {
                Log::warning('Failed to download content image: '.$originalUrl);
            }
        }

        foreach ($replacements as $oldUrl => $newUrl) {
            $content = str_replace($oldUrl, $newUrl, $content);
        }

        return $content;
    }

    protected function getFeaturedImage($wpPost)
    {
        // Try to get from embedded data first
        if (isset($wpPost['_embedded']['wp:featuredmedia'][0]['source_url'])) {
            $imageUrl = $wpPost['_embedded']['wp:featuredmedia'][0]['source_url'];

            return $this->downloadImage($imageUrl);
        }

        // If no embedded media, try to fetch it
        if (! empty($wpPost['featured_media'])) {
            try {
                $response = Http::timeout(10)->get($this->wpUrl.'/wp-json/wp/v2/media/'.$wpPost['featured_media']);
                if ($response->successful()) {
                    $media = $response->json();
                    $imageUrl = $media['source_url'] ?? null;

                    if ($imageUrl) {
                        return $this->downloadImage($imageUrl);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch featured media: '.$e->getMessage());
            }
        }

        return null;
    }

    protected function downloadImage($imageUrl)
    {
        try {
            if (Str::startsWith($imageUrl, '//')) {
                $imageUrl = 'https:'.$imageUrl;
            }

            $response = Http::timeout(60)->withOptions([
                'verify' => false,
            ])->get($imageUrl);

            if (! $response->successful()) {
                return null;
            }

            $urlPath = parse_url($imageUrl, PHP_URL_PATH);
            $originalFilename = basename($urlPath);
            $extension = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));

            if (empty($extension) || ! in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                $contentType = $response->header('Content-Type');
                $extension = $this->getExtensionFromMimeType($contentType) ?? 'jpg';
            }

            $filename = 'wp-cpt-import-'.time().'-'.Str::random(8).'.'.$extension;
            $path = config('media.path', 'media').'/'.$filename;

            $disk = Storage::disk(config('media.disk', 'public'));

            $directory = dirname($path);
            if (! $disk->exists($directory)) {
                $disk->makeDirectory($directory);
            }

            $disk->put($path, $response->body());

            if (! $disk->exists($path)) {
                return null;
            }

            $fullPath = $disk->path($path);
            $imageInfo = @getimagesize($fullPath);
            $fileSize = $disk->size($path);

            $mimeType = $imageInfo['mime'] ?? $this->getMimeTypeFromExtension($extension);

            Media::create([
                'filename' => $filename,
                'original_filename' => $originalFilename ?: $filename,
                'mime_type' => $mimeType,
                'file_extension' => $extension,
                'size' => $fileSize,
                'path' => $path,
                'width' => $imageInfo[0] ?? null,
                'height' => $imageInfo[1] ?? null,
                'alt_text' => null,
                'title' => pathinfo($originalFilename ?: $filename, PATHINFO_FILENAME),
                'description' => 'Imported from WordPress CPT',
                'uploaded_by' => auth()->id(),
            ]);

            return $path;

        } catch (\Exception $e) {
            Log::error('WordPress CPT image download failed', ['url' => $imageUrl, 'error' => $e->getMessage()]);

            return null;
        }
    }

    protected function getExtensionFromMimeType($mimeType)
    {
        $map = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
        ];

        return $map[$mimeType] ?? null;
    }

    protected function getMimeTypeFromExtension($extension)
    {
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
        ];

        return $mimeTypes[$extension] ?? 'image/jpeg';
    }

    protected function ensureUniqueSlug($slug)
    {
        $originalSlug = $slug;
        $counter = 1;

        while (CptEntry::where('slug', $slug)->where('post_type_id', $this->selectedCmsCpt)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function resetMigration()
    {
        $this->step = 1;
        $this->wpUrl = '';
        $this->availableCpts = [];
        $this->selectedWpCpt = '';
        $this->wpCptFields = [];
        $this->selectedCmsCpt = '';
        $this->fieldMappings = [];
        $this->totalPosts = 0;
        $this->totalPages = 0;
        $this->previewPosts = [];
        $this->selectedLanguages = [];
        $this->selectedPostIds = [];
        $this->selectAllPosts = true;
        $this->fetchAllDone = false;
        $this->isPolylang = false;
        $this->polylangLanguages = [];
        $this->importProgress = 0;
        $this->currentPageImporting = 0;
        $this->importResults = [];
        $this->errorMessage = '';
    }

    public function goBack()
    {
        if ($this->step > 1) {
            if ($this->step === 3) {
                $this->step = 2;
                $this->previewPosts = [];
                $this->fetchAllDone = false;
            } else {
                $this->step--;
            }
        }
    }

    public function continueToFieldMapping()
    {
        $this->step = 4;
    }

    public function render()
    {
        return view('livewire.admin.wordpress-cpt-migration');
    }
}
