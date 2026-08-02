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
use Plugins\Posts\Models\Post;
use Plugins\Posts\Models\PostAuthor;

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
        app(MediaUsageService::class)->clearCache();
        $this->newLine(2);
        $this->info('✅ Import finished!');
        $this->table(
            ['Result', 'Count'],
            [
                ['Success', $success],
                ['Skipped (Already exists)', $skipped],
                ['Failed', $failed],
                ['Total', $totalPosts],
            ]
        );

        return self::SUCCESS;
    }

    protected function importSinglePost(array $wpPost): string
    {
        try {
            $title = html_entity_decode(strip_tags($wpPost['title']['rendered'] ?? ''), ENT_QUOTES, 'UTF-8');
            $slug = $wpPost['slug'] ?? Str::slug($title);

            // Check if already exists
            if ($this->target === 'plugin_post') {
                if (Post::where('slug', $slug)->exists()) {
                    return 'skipped';
                }
            } else {
                if (CptEntry::where('slug', $slug)->where('post_type_id', $this->target)->exists()) {
                    return 'skipped';
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
                ]);

                $post->forceFill(['created_at' => $publishedAt])->save();

                return 'success';
            }

            // CPT Entry fallback
            $entry = CptEntry::create([
                'post_type_id' => (int) $this->target,
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'excerpt' => $excerpt,
                'featured_image' => $featuredImage,
                'status' => 'published',
                'published_at' => $publishedAt,
                'author_id' => auth()->id() ?? 1,
                'meta' => [
                    'wp_original_id' => $wpPost['id'],
                    'wp_original_url' => $wpPost['link'] ?? null,
                ],
            ]);

            $entry->forceFill(['created_at' => $publishedAt])->save();

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
}
