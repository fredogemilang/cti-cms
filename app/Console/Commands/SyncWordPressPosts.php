<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Models\SeoMeta;
use App\Services\MediaService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Plugins\Posts\Models\Category;
use Plugins\Posts\Models\Post;
use Plugins\Posts\Models\PostAuthor;
use Plugins\Posts\Models\Tag;

class SyncWordPressPosts extends Command
{
    protected $signature = 'posts:sync-wordpress
                            {--fresh : Wipe existing posts, relations, and post SEO meta before sync}
                            {--limit= : Limit number of clusters/articles to process}
                            {--url=https://www.centraldatatech.com : WordPress base URL}
                            {--skip-images : Skip downloading missing images}';

    protected $description = 'Pull, pair, and synchronize all blog posts from WordPress REST API with full Yoast SEO metadata';

    public function handle(): int
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        $baseUrl = rtrim($this->option('url'), '/');
        $isFresh = $this->option('fresh');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $skipImages = $this->option('skip-images');

        $this->info("========================================================");
        $this->info("  WordPress Posts & SEO Sync for Central Data Tech");
        $this->info("  Source: {$baseUrl}");
        $this->info("========================================================");

        if ($isFresh) {
            $this->warn("⚠️  --fresh option specified: Wiping existing posts and SEO metadata...");
            Schema::disableForeignKeyConstraints();
            DB::table('category_post')->truncate();
            DB::table('post_tag')->truncate();
            SeoMeta::where('seoable_type', Post::class)->delete();
            Post::withTrashed()->forceDelete();
            Schema::enableForeignKeyConstraints();
            $this->info("Existing posts, relations, and post SEO meta cleaned successfully.\n");
        }

        // 1. Fetch all posts from WordPress REST API
        $this->info("Fetching all posts from WordPress REST API...");
        $allWpPosts = [];
        $page = 1;

        while (true) {
            $this->output->write("  Fetching page {$page}... ");
            $response = Http::withoutVerifying()
                ->timeout(60)
                ->get("{$baseUrl}/wp-json/wp/v2/posts", [
                    'per_page' => 100,
                    'page' => $page,
                    '_embed' => 1,
                ]);

            if (! $response->successful()) {
                $this->output->writeln("<comment>End of pages or error (HTTP {$response->status()})</comment>");
                break;
            }

            $posts = $response->json();
            if (empty($posts)) {
                $this->output->writeln("<comment>No more posts.</comment>");
                break;
            }

            $count = count($posts);
            $this->output->writeln("<info>received {$count} posts</info>");

            foreach ($posts as $p) {
                $allWpPosts[$p['id']] = $p;
            }

            $totalPages = (int) $response->header('X-WP-TotalPages', 1);
            if ($page >= $totalPages) {
                break;
            }
            $page++;
        }

        $totalWpPosts = count($allWpPosts);
        $this->info("Total WordPress posts downloaded: {$totalWpPosts}\n");

        if ($totalWpPosts === 0) {
            $this->error("No posts found. Exiting.");
            return 1;
        }

        // 2. Cluster posts by Polylang translation pairings
        $this->info("Grouping posts by Polylang bilingual translations...");
        $clusters = [];
        $visited = [];

        // Sort posts descending by date so newest posts come first
        uasort($allWpPosts, function ($a, $b) {
            return strcmp($b['date'] ?? '', $a['date'] ?? '');
        });

        foreach ($allWpPosts as $wpId => $p) {
            if (isset($visited[$wpId])) {
                continue;
            }

            $tr = $p['translations'] ?? [];
            $lang = $p['lang'] ?? 'en';

            $enId = $tr['en'] ?? ($lang === 'en' ? $wpId : null);
            $idId = $tr['id'] ?? ($lang === 'id' ? $wpId : null);

            if ($enId) $visited[$enId] = true;
            if ($idId) $visited[$idId] = true;
            $visited[$wpId] = true;

            $clusters[] = [
                'en_id' => $enId,
                'id_id' => $idId,
                'primary_wp_id' => $enId ?: $idId,
            ];
        }

        $totalClusters = count($clusters);
        $this->info("Total unique articles (clusters) to process: {$totalClusters}");

        if ($limit && $limit < $totalClusters) {
            $clusters = array_slice($clusters, 0, $limit);
            $this->info("Limited to first {$limit} clusters as requested.\n");
        }

        // 3. Process each cluster and sync into database
        $bar = $this->output->createProgressBar(count($clusters));
        $bar->start();

        $successCount = 0;
        $authorCache = [];
        $mediaService = app(MediaService::class);

        foreach ($clusters as $cluster) {
            $enPost = $cluster['en_id'] && isset($allWpPosts[$cluster['en_id']]) ? $allWpPosts[$cluster['en_id']] : null;
            $idPost = $cluster['id_id'] && isset($allWpPosts[$cluster['id_id']]) ? $allWpPosts[$cluster['id_id']] : null;

            if (! $enPost && ! $idPost) {
                $bar->advance();
                continue;
            }

            // Determine primary and secondary data
            $hasEn = (bool) $enPost;
            $hasId = (bool) $idPost;

            $primaryPost = $hasEn ? $enPost : $idPost;

            // Title & Slug
            $primaryTitle = html_entity_decode($primaryPost['title']['rendered'] ?? '', ENT_QUOTES, 'UTF-8');
            $primarySlug = $primaryPost['slug'] ?? Str::slug($primaryTitle);
            $primaryContent = $primaryPost['content']['rendered'] ?? '';
            $primaryExcerpt = html_entity_decode(strip_tags($primaryPost['excerpt']['rendered'] ?? ''), ENT_QUOTES, 'UTF-8');
            $publishedAt = $primaryPost['date'] ?? now();

            // Handle author
            $authorName = $primaryPost['_embedded']['author'][0]['name'] ?? 'Admin';
            $authorSlug = $primaryPost['_embedded']['author'][0]['slug'] ?? Str::slug($authorName);
            if (! isset($authorCache[$authorSlug])) {
                $author = PostAuthor::firstOrCreate(
                    ['slug' => $authorSlug],
                    ['name' => $authorName, 'email' => "{$authorSlug}@centraldatatech.com"]
                );
                $authorCache[$authorSlug] = $author->id;
            }
            $authorId = $authorCache[$authorSlug];

            // Build translations array
            $translations = [];
            if ($hasId) {
                $idTitle = html_entity_decode($idPost['title']['rendered'] ?? '', ENT_QUOTES, 'UTF-8');
                $idSlug = $idPost['slug'] ?? Str::slug($idTitle);
                $idContent = $idPost['content']['rendered'] ?? '';
                $idExcerpt = html_entity_decode(strip_tags($idPost['excerpt']['rendered'] ?? ''), ENT_QUOTES, 'UTF-8');

                $translations['id'] = [
                    'title' => $idTitle,
                    'slug' => $idSlug,
                    'content' => $idContent,
                    'excerpt' => $idExcerpt,
                ];
            }

            // Featured Image Resolution
            $featuredImagePath = null;
            if (! $skipImages) {
                $featuredImageUrl = $enPost['_embedded']['wp:featuredmedia'][0]['source_url']
                    ?? ($idPost['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null);

                // Fallback: extract first image from content if no wp:featuredmedia
                if (! $featuredImageUrl) {
                    $combinedContent = ($primaryContent ?? '') . ' ' . ($idPost['content']['rendered'] ?? '');
                    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $combinedContent, $imgMatch)) {
                        $featuredImageUrl = $imgMatch[1];
                    }
                }

                if ($featuredImageUrl) {
                    $featuredImagePath = $this->resolveMedia($featuredImageUrl, $mediaService);
                }
            }

            // Create Post model
            $post = Post::create([
                'title' => $primaryTitle,
                'slug' => $this->ensureUniqueSlug($primarySlug),
                'content' => $primaryContent,
                'excerpt' => $primaryExcerpt,
                'featured_image' => $featuredImagePath,
                'author_id' => $authorId,
                'status' => 'published',
                'published_at' => $publishedAt,
                'created_at' => $publishedAt,
                'updated_at' => $primaryPost['modified'] ?? $publishedAt,
                'translations' => ! empty($translations) ? $translations : null,
                'meta' => [
                    'wp_original_id' => $primaryPost['id'],
                    'wp_translation_ids' => array_filter([$cluster['en_id'], $cluster['id_id']]),
                ],
            ]);

            // Save Yoast SEO Metadata
            $this->syncSeoMeta($post, $enPost, $idPost);

            // Sync Taxonomies (Categories & Tags)
            $this->syncTaxonomies($post, $enPost, $idPost);

            $successCount++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("========================================================");
        $this->info("  SYNC COMPLETE: Successfully synchronized {$successCount} articles!");
        $this->info("========================================================");

        return 0;
    }

    /**
     * Resolve and store media file using MediaService.
     */
    protected function resolveMedia(string $url, MediaService $mediaService): ?string
    {
        try {
            if (Str::startsWith($url, '//')) {
                $url = 'https:' . $url;
            }

            $urlPath = parse_url($url, PHP_URL_PATH) ?? '';
            $originalFilename = basename($urlPath);
            if (empty($originalFilename)) {
                return null;
            }

            // Check if already in Media table
            $existing = Media::where('original_filename', $originalFilename)
                ->orWhere('filename', $originalFilename)
                ->first();

            if ($existing && ! empty($existing->path)) {
                return $existing->path;
            }

            // Download file
            $response = Http::withoutVerifying()->timeout(30)->get($url);
            if (! $response->successful()) {
                return null;
            }

            $tmpDir = storage_path('app/tmp');
            if (! is_dir($tmpDir)) {
                mkdir($tmpDir, 0755, true);
            }

            $tmpPath = $tmpDir . '/' . uniqid('wp_img_') . '_' . $originalFilename;
            file_put_contents($tmpPath, $response->body());

            $mimeType = $response->header('Content-Type') ?: mime_content_type($tmpPath) ?: 'image/jpeg';
            $uploadedFile = new UploadedFile($tmpPath, $originalFilename, $mimeType, null, true);

            $media = $mediaService->upload($uploadedFile, [
                'title' => pathinfo($originalFilename, PATHINFO_FILENAME),
                'description' => 'Imported from WordPress centraldatatech.com',
                'alt_text' => pathinfo($originalFilename, PATHINFO_FILENAME),
            ]);

            @unlink($tmpPath);

            return $media->path;
        } catch (\Throwable $e) {
            Log::warning("Failed downloading media {$url}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Populate localized SeoMeta rows from Yoast SEO payloads.
     */
    protected function syncSeoMeta(Post $post, ?array $enPost, ?array $idPost): void
    {
        $yoastEn = $enPost['yoast_head_json'] ?? [];
        $yoastId = $idPost['yoast_head_json'] ?? [];

        $cleanText = function (?string $text): ?string {
            if (empty($text)) return null;
            $text = html_entity_decode(strip_tags($text), ENT_QUOTES, 'UTF-8');
            return preg_replace('/\s+/', ' ', trim($text));
        };

        $enTitle = $cleanText($yoastEn['title'] ?? null);
        $enDesc = $cleanText($yoastEn['description'] ?? null);
        $enOgTitle = $cleanText($yoastEn['og_title'] ?? $enTitle);
        $enOgDesc = $cleanText($yoastEn['og_description'] ?? $enDesc);

        $idTitle = $cleanText($yoastId['title'] ?? null);
        $idDesc = $cleanText($yoastId['description'] ?? null);
        $idOgTitle = $cleanText($yoastId['og_title'] ?? $idTitle);
        $idOgDesc = $cleanText($yoastId['og_description'] ?? $idDesc);

        // English SEO
        if ($enPost) {
            SeoMeta::updateOrCreate(
                ['seoable_type' => Post::class, 'seoable_id' => $post->id, 'locale' => 'en'],
                [
                    'title' => $enTitle,
                    'description' => $enDesc,
                    'og_title' => $enOgTitle,
                    'og_description' => $enOgDesc,
                ]
            );

            // Default fallback locale ''
            SeoMeta::updateOrCreate(
                ['seoable_type' => Post::class, 'seoable_id' => $post->id, 'locale' => ''],
                [
                    'title' => $enTitle,
                    'description' => $enDesc,
                    'og_title' => $enOgTitle,
                    'og_description' => $enOgDesc,
                ]
            );
        }

        // Indonesian SEO
        if ($idPost) {
            SeoMeta::updateOrCreate(
                ['seoable_type' => Post::class, 'seoable_id' => $post->id, 'locale' => 'id'],
                [
                    'title' => $idTitle,
                    'description' => $idDesc,
                    'og_title' => $idOgTitle,
                    'og_description' => $idOgDesc,
                ]
            );

            // If no English post, fallback is Indonesian
            if (! $enPost) {
                SeoMeta::updateOrCreate(
                    ['seoable_type' => Post::class, 'seoable_id' => $post->id, 'locale' => ''],
                    [
                        'title' => $idTitle,
                        'description' => $idDesc,
                        'og_title' => $idOgTitle,
                        'og_description' => $idOgDesc,
                    ]
                );
            }
        }
    }

    /**
     * Attach categories and tags from WordPress terms.
     */
    protected function syncTaxonomies(Post $post, ?array $enPost, ?array $idPost): void
    {
        $postsToCheck = array_filter([$enPost, $idPost]);
        $categoryIds = [];
        $tagIds = [];

        foreach ($postsToCheck as $wpItem) {
            $lang = $wpItem['lang'] ?? 'en';
            $termGroups = $wpItem['_embedded']['wp:term'] ?? [];

            foreach ($termGroups as $group) {
                if (! is_array($group)) continue;

                foreach ($group as $term) {
                    $taxonomy = $term['taxonomy'] ?? '';
                    $name = html_entity_decode(trim($term['name'] ?? ''), ENT_QUOTES, 'UTF-8');
                    $slug = $term['slug'] ?? Str::slug($name);

                    if (empty($name)) continue;

                    if ($taxonomy === 'category') {
                        $baseSlug = preg_replace('/-\d+$/', '', $slug);
                        $category = Category::where('slug', $slug)
                            ->orWhere('slug', $baseSlug)
                            ->orWhereRaw('LOWER(name) = ?', [strtolower($name)])
                            ->first();

                        if (! $category) {
                            $category = Category::create([
                                'name' => $name,
                                'slug' => $baseSlug,
                                'description' => $term['description'] ?? '',
                            ]);
                        }

                        $category->setTranslation('name', $lang, $name);
                        $category->setTranslation('slug', $lang, $slug);
                        $category->save();

                        $categoryIds[] = $category->id;
                    } elseif ($taxonomy === 'post_tag') {
                        $baseSlug = preg_replace('/-\d+$/', '', $slug);
                        $tag = Tag::where('slug', $slug)
                            ->orWhere('slug', $baseSlug)
                            ->orWhereRaw('LOWER(name) = ?', [strtolower($name)])
                            ->first();

                        if (! $tag) {
                            $tag = Tag::create([
                                'name' => $name,
                                'slug' => $baseSlug,
                            ]);
                        }

                        $tag->setTranslation('name', $lang, $name);
                        $tag->setTranslation('slug', $lang, $slug);
                        $tag->save();

                        $tagIds[] = $tag->id;
                    }
                }
            }
        }

        if (! empty($categoryIds)) {
            $post->categories()->syncWithoutDetaching(array_unique($categoryIds));
        }
        if (! empty($tagIds)) {
            $post->tags()->syncWithoutDetaching(array_unique($tagIds));
        }
    }

    protected function ensureUniqueSlug(string $slug): string
    {
        $original = $slug;
        $counter = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$counter}";
            $counter++;
        }
        return $slug;
    }
}
