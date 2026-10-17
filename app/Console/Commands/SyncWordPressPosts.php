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
                            {--url= : WordPress base URL (required, e.g. https://www.example.com)}
                            {--skip-images : Skip downloading missing images}';

    protected $description = 'Pull, pair, and synchronize all blog posts and taxonomies from WordPress REST API with full Yoast SEO metadata and dual EN/ID localization';

    /**
     * Source WordPress host without "www." (used for placeholder author emails & media descriptions).
     */
    protected string $sourceHost = '';

    /**
     * Map WordPress category ID => CMS Category ID
     * @var array<int, int>
     */
    protected array $wpCategoryMap = [];

    /**
     * Map WordPress tag ID => CMS Tag ID
     * @var array<int, int>
     */
    protected array $wpTagMap = [];

    public function handle(): int
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        $baseUrl = rtrim((string) $this->option('url'), '/');
        if ($baseUrl === '' || ! filter_var($baseUrl, FILTER_VALIDATE_URL)) {
            $this->error('The --url option is required, e.g. --url=https://www.example.com');

            return self::FAILURE;
        }
        $this->sourceHost = (string) preg_replace('/^www\./i', '', (string) parse_url($baseUrl, PHP_URL_HOST));

        $isFresh = $this->option('fresh');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $skipImages = $this->option('skip-images');

        $this->info('========================================================');
        $this->info('  WordPress Posts & Taxonomies Sync');
        $this->info("  Source: {$baseUrl}");
        $this->info('========================================================');

        if ($isFresh) {
            $this->warn('⚠️  --fresh option specified: Wiping existing posts, post relations, and post SEO metadata...');
            Schema::disableForeignKeyConstraints();
            DB::table('category_post')->truncate();
            DB::table('post_tag')->truncate();
            SeoMeta::where('seoable_type', Post::class)->delete();
            Post::withTrashed()->forceDelete();
            Schema::enableForeignKeyConstraints();
            $this->info("Existing posts, relations, and post SEO meta cleaned successfully.\n");
        }

        // 1. Sync Taxonomies (Categories and Tags) directly from WordPress
        $this->syncTaxonomyTerms($baseUrl);

        // 2. Fetch all posts from WordPress REST API
        $this->info('Fetching all posts from WordPress REST API...');
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
                $this->output->writeln('<comment>No more posts.</comment>');
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
            $this->error('No posts found. Exiting.');

            return 1;
        }

        // 3. Cluster posts by Polylang translation pairings
        $this->info('Grouping posts by Polylang bilingual translations...');
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
            $lang = $p['lang'] ?? null;

            // If monolingual (e.g. kouventa.ai has only ID articles), clone to both EN and ID
            if (empty($tr) && empty($lang)) {
                $enId = $wpId;
                $idId = $wpId;
            } else {
                $enId = $tr['en'] ?? ($lang === 'en' ? $wpId : null);
                $idId = $tr['id'] ?? ($lang === 'id' ? $wpId : null);
            }

            if ($enId) {
                $visited[$enId] = true;
            }
            if ($idId) {
                $visited[$idId] = true;
            }
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

        // 4. Process each cluster and sync into database
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

            // Determine primary post (prefer ID since source is Indonesian)
            $primaryPost = $idPost ?: $enPost;

            // Title & Excerpt
            $idTitle = html_entity_decode($idPost['title']['rendered'] ?? ($primaryPost['title']['rendered'] ?? ''), ENT_QUOTES, 'UTF-8');
            $idSlug = $idPost['slug'] ?? ($primaryPost['slug'] ?? Str::slug($idTitle));
            $idContent = $idPost['content']['rendered'] ?? ($primaryPost['content']['rendered'] ?? '');
            $idExcerpt = html_entity_decode(strip_tags($idPost['excerpt']['rendered'] ?? ($primaryPost['excerpt']['rendered'] ?? '')), ENT_QUOTES, 'UTF-8');
            $idExcerpt = preg_replace('/\s*(?:Read more|Baca selengkapnya|&#8230;|\.{3}|…).*$/iu', '', $idExcerpt);
            $idExcerpt = preg_replace('/\s+/', ' ', trim($idExcerpt));

            // If EN post is separate polylang post, use it; otherwise clone ID content to EN
            $enTitle = $enPost && $enPost !== $idPost ? html_entity_decode($enPost['title']['rendered'] ?? $idTitle, ENT_QUOTES, 'UTF-8') : $idTitle;
            $enSlug = $enPost && $enPost !== $idPost ? ($enPost['slug'] ?? $idSlug) : $idSlug;
            $enContent = $enPost && $enPost !== $idPost ? ($enPost['content']['rendered'] ?? $idContent) : $idContent;
            $enExcerpt = $enPost && $enPost !== $idPost ? html_entity_decode(strip_tags($enPost['excerpt']['rendered'] ?? $idExcerpt), ENT_QUOTES, 'UTF-8') : $idExcerpt;
            $enExcerpt = preg_replace('/\s*(?:Read more|Baca selengkapnya|&#8230;|\.{3}|…).*$/iu', '', $enExcerpt);
            $enExcerpt = preg_replace('/\s+/', ' ', trim($enExcerpt));

            $publishedAt = $primaryPost['date'] ?? now();

            // Handle author
            $authorName = $primaryPost['_embedded']['author'][0]['name'] ?? 'Admin';
            $authorSlug = $primaryPost['_embedded']['author'][0]['slug'] ?? Str::slug($authorName);
            if (! isset($authorCache[$authorSlug])) {
                $author = PostAuthor::firstOrCreate(
                    ['slug' => $authorSlug],
                    ['name' => $authorName, 'email' => "{$authorSlug}@{$this->sourceHost}"]
                );
                $authorCache[$authorSlug] = $author->id;
            }
            $authorId = $authorCache[$authorSlug];

            // Featured Image Resolution
            $featuredImagePath = null;
            if (! $skipImages) {
                $featuredImageUrl = $primaryPost['_embedded']['wp:featuredmedia'][0]['source_url']
                    ?? ($enPost['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null);

                // Fallback: extract first image from content if no wp:featuredmedia
                if (! $featuredImageUrl) {
                    $combinedContent = ($idContent ?? '').' '.($enContent ?? '');
                    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $combinedContent, $imgMatch)) {
                        $featuredImageUrl = $imgMatch[1];
                    }
                }

                if ($featuredImageUrl) {
                    $featuredImagePath = $this->resolveMedia($featuredImageUrl, $mediaService);
                }
            }

            // Localize inline content images (Rule 9 & 10)
            if (! $skipImages) {
                $idContent = $this->localizeContentImages($idContent, $mediaService);
                $enContent = ($enPost && $enPost !== $idPost) ? $this->localizeContentImages($enContent, $mediaService) : $idContent;
            }

            // Strip leading redundant image if featured image exists
            if ($featuredImagePath) {
                $idContent = strip_leading_image($idContent);
                $enContent = strip_leading_image($enContent);
            }

            $primaryContent = $idContent;
            $primaryTitle = $idTitle;
            $uniqueSlug = $this->ensureUniqueSlug($idSlug);

            $translations = [
                'id' => [
                    'title' => $idTitle,
                    'slug' => $uniqueSlug,
                    'content' => $idContent,
                    'excerpt' => $idExcerpt,
                ],
                'en' => [
                    'title' => $enTitle,
                    'slug' => $uniqueSlug,
                    'content' => $enContent,
                    'excerpt' => $enExcerpt,
                ],
            ];

            // Create Post model
            $post = Post::create([
                'title' => $primaryTitle,
                'slug' => $uniqueSlug,
                'content' => $primaryContent,
                'excerpt' => $idExcerpt,
                'featured_image' => $featuredImagePath,
                'author_id' => $authorId,
                'status' => 'published',
                'published_at' => $publishedAt,
                'created_at' => $publishedAt,
                'updated_at' => $primaryPost['modified'] ?? $publishedAt,
                'translations' => $translations,
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

        $this->info('========================================================');
        $this->info("  SYNC COMPLETE: Successfully synchronized {$successCount} articles!");
        $this->info('========================================================');

        return 0;
    }

    /**
     * Fetch and synchronize all categories and tags from WordPress REST API.
     */
    protected function syncTaxonomyTerms(string $baseUrl): void
    {
        $this->info('Fetching and synchronizing Categories & Tags from WordPress...');

        // 1. Categories
        $catResponse = Http::withoutVerifying()
            ->timeout(60)
            ->get("{$baseUrl}/wp-json/wp/v2/categories", ['per_page' => 100]);

        if ($catResponse->successful()) {
            $categories = $catResponse->json();
            $this->info('  Found '.count($categories).' categories in WordPress.');

            foreach ($categories as $cat) {
                $wpId = $cat['id'];
                $name = html_entity_decode(trim($cat['name'] ?? ''), ENT_QUOTES, 'UTF-8');
                $slug = $cat['slug'] ?? Str::slug($name);
                $description = html_entity_decode(trim($cat['description'] ?? ''), ENT_QUOTES, 'UTF-8');

                if (empty($name)) {
                    continue;
                }

                $category = Category::where('slug', $slug)
                    ->orWhereRaw('LOWER(name) = ?', [strtolower($name)])
                    ->first();

                if (! $category) {
                    $category = new Category();
                }

                $category->name = $name;
                $category->slug = $slug;
                $category->description = $description ?: null;
                $category->translations = [
                    'id' => [
                        'name' => $name,
                        'slug' => $slug,
                        'description' => $description,
                    ],
                    'en' => [
                        'name' => $name,
                        'slug' => $slug,
                        'description' => $description,
                    ],
                ];
                $category->save();

                $this->wpCategoryMap[$wpId] = $category->id;
            }
        } else {
            $this->warn('  Could not fetch categories from WP endpoint.');
        }

        // 2. Tags
        $tagResponse = Http::withoutVerifying()
            ->timeout(60)
            ->get("{$baseUrl}/wp-json/wp/v2/tags", ['per_page' => 100]);

        if ($tagResponse->successful()) {
            $tags = $tagResponse->json();
            $this->info('  Found '.count($tags).' tags in WordPress.');

            foreach ($tags as $t) {
                $wpId = $t['id'];
                $name = html_entity_decode(trim($t['name'] ?? ''), ENT_QUOTES, 'UTF-8');
                $slug = $t['slug'] ?? Str::slug($name);

                if (empty($name)) {
                    continue;
                }

                $tag = Tag::where('slug', $slug)
                    ->orWhereRaw('LOWER(name) = ?', [strtolower($name)])
                    ->first();

                if (! $tag) {
                    $tag = new Tag();
                }

                $tag->name = $name;
                $tag->slug = $slug;
                $tag->translations = [
                    'id' => [
                        'name' => $name,
                        'slug' => $slug,
                    ],
                    'en' => [
                        'name' => $name,
                        'slug' => $slug,
                    ],
                ];
                $tag->save();

                $this->wpTagMap[$wpId] = $tag->id;
            }
        }
    }

    /**
     * Localize all inline <img> tags in HTML content using MediaService.
     */
    protected function localizeContentImages(string $html, MediaService $mediaService): string
    {
        if (empty($html)) {
            return $html;
        }

        return preg_replace_callback('/<img\b([^>]*?)\bsrc=["\']([^"\']+)["\']([^>]*?)>/i', function ($matches) use ($mediaService) {
            $prefix = $matches[1];
            $url = $matches[2];
            $suffix = $matches[3];

            if (Str::startsWith($url, ['http://', 'https://', '//'])) {
                $localPath = $this->resolveMedia($url, $mediaService);
                if ($localPath) {
                    $localUrl = str_starts_with($localPath, 'storage/') ? '/'.$localPath : '/storage/'.ltrim($localPath, '/');
                    $cleanPrefix = preg_replace('/\s*(?:srcset|sizes)=["\'][^"\']*["\']/i', '', $prefix);
                    $cleanSuffix = preg_replace('/\s*(?:srcset|sizes)=["\'][^"\']*["\']/i', '', $suffix);

                    return "<img{$cleanPrefix} src=\"{$localUrl}\"{$cleanSuffix}>";
                }
            }

            return $matches[0];
        }, $html);
    }

    /**
     * Resolve and store media file using MediaService.
     */
    protected function resolveMedia(string $url, MediaService $mediaService): ?string
    {
        try {
            if (Str::startsWith($url, '//')) {
                $url = 'https:'.$url;
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

            $tmpPath = $tmpDir.'/'.uniqid('wp_img_').'_'.$originalFilename;
            file_put_contents($tmpPath, $response->body());

            $mimeType = $response->header('Content-Type') ?: mime_content_type($tmpPath) ?: 'image/jpeg';
            $uploadedFile = new UploadedFile($tmpPath, $originalFilename, $mimeType, null, true);

            $media = $mediaService->upload($uploadedFile, [
                'title' => pathinfo($originalFilename, PATHINFO_FILENAME),
                'description' => 'Imported from WordPress '.$this->sourceHost,
                'alt_text' => pathinfo($originalFilename, PATHINFO_FILENAME),
            ]);

            @unlink($tmpPath);

            return $media->path;
        } catch (\Throwable $e) {
            Log::warning("Failed downloading media {$url}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Populate localized SeoMeta rows from Yoast SEO payloads.
     */
    protected function syncSeoMeta(Post $post, ?array $enPost, ?array $idPost): void
    {
        $primaryPost = $idPost ?: $enPost;
        $yoastId = $idPost['yoast_head_json'] ?? ($primaryPost['yoast_head_json'] ?? []);
        $yoastEn = ($enPost && $enPost !== $idPost) ? ($enPost['yoast_head_json'] ?? $yoastId) : $yoastId;

        $cleanText = function (?string $text): ?string {
            if (empty($text)) {
                return null;
            }
            $text = html_entity_decode(strip_tags($text), ENT_QUOTES, 'UTF-8');

            return preg_replace('/\s+/', ' ', trim($text));
        };

        $idTitle = $cleanText($yoastId['title'] ?? null);
        $idDesc = $cleanText($yoastId['description'] ?? null);
        $idOgTitle = $cleanText($yoastId['og_title'] ?? $idTitle);
        $idOgDesc = $cleanText($yoastId['og_description'] ?? $idDesc);

        $enTitle = $cleanText($yoastEn['title'] ?? $idTitle);
        $enDesc = $cleanText($yoastEn['description'] ?? $idDesc);
        $enOgTitle = $cleanText($yoastEn['og_title'] ?? $enTitle);
        $enOgDesc = $cleanText($yoastEn['og_description'] ?? $enDesc);

        // English SEO
        SeoMeta::updateOrCreate(
            ['seoable_type' => Post::class, 'seoable_id' => $post->id, 'locale' => 'en'],
            [
                'title' => $enTitle,
                'description' => $enDesc,
                'og_title' => $enOgTitle,
                'og_description' => $enOgDesc,
            ]
        );

        // Fallback locale ''
        SeoMeta::updateOrCreate(
            ['seoable_type' => Post::class, 'seoable_id' => $post->id, 'locale' => ''],
            [
                'title' => $enTitle,
                'description' => $enDesc,
                'og_title' => $enOgTitle,
                'og_description' => $enOgDesc,
            ]
        );

        // Indonesian SEO
        SeoMeta::updateOrCreate(
            ['seoable_type' => Post::class, 'seoable_id' => $post->id, 'locale' => 'id'],
            [
                'title' => $idTitle,
                'description' => $idDesc,
                'og_title' => $idOgTitle,
                'og_description' => $idOgDesc,
            ]
        );
    }

    /**
     * Attach categories and tags from WordPress terms and IDs.
     */
    protected function syncTaxonomies(Post $post, ?array $enPost, ?array $idPost): void
    {
        $postsToCheck = array_filter([$idPost, $enPost]);
        $categoryIds = [];
        $tagIds = [];

        foreach ($postsToCheck as $wpItem) {
            // Direct category IDs array from post
            if (! empty($wpItem['categories']) && is_array($wpItem['categories'])) {
                foreach ($wpItem['categories'] as $wpCatId) {
                    if (isset($this->wpCategoryMap[$wpCatId])) {
                        $categoryIds[] = $this->wpCategoryMap[$wpCatId];
                    }
                }
            }

            // Direct tags array from post
            if (! empty($wpItem['tags']) && is_array($wpItem['tags'])) {
                foreach ($wpItem['tags'] as $wpTagId) {
                    if (isset($this->wpTagMap[$wpTagId])) {
                        $tagIds[] = $this->wpTagMap[$wpTagId];
                    }
                }
            }

            // Embedded wp:term fallback/supplement
            $termGroups = $wpItem['_embedded']['wp:term'] ?? [];
            foreach ($termGroups as $group) {
                if (! is_array($group)) {
                    continue;
                }

                foreach ($group as $term) {
                    $taxonomy = $term['taxonomy'] ?? '';
                    $name = html_entity_decode(trim($term['name'] ?? ''), ENT_QUOTES, 'UTF-8');
                    $slug = $term['slug'] ?? Str::slug($name);
                    $wpId = $term['id'] ?? null;

                    if (empty($name)) {
                        continue;
                    }

                    if ($taxonomy === 'category') {
                        if ($wpId && isset($this->wpCategoryMap[$wpId])) {
                            $categoryIds[] = $this->wpCategoryMap[$wpId];
                        } else {
                            $category = Category::where('slug', $slug)
                                ->orWhereRaw('LOWER(name) = ?', [strtolower($name)])
                                ->first();

                            if (! $category) {
                                $category = Category::create([
                                    'name' => $name,
                                    'slug' => $slug,
                                    'description' => $term['description'] ?? '',
                                    'translations' => [
                                        'id' => ['name' => $name, 'slug' => $slug, 'description' => $term['description'] ?? ''],
                                        'en' => ['name' => $name, 'slug' => $slug, 'description' => $term['description'] ?? ''],
                                    ],
                                ]);
                            }
                            $categoryIds[] = $category->id;
                        }
                    } elseif ($taxonomy === 'post_tag') {
                        if ($wpId && isset($this->wpTagMap[$wpId])) {
                            $tagIds[] = $this->wpTagMap[$wpId];
                        } else {
                            $tag = Tag::where('slug', $slug)
                                ->orWhereRaw('LOWER(name) = ?', [strtolower($name)])
                                ->first();

                            if (! $tag) {
                                $tag = Tag::create([
                                    'name' => $name,
                                    'slug' => $slug,
                                    'translations' => [
                                        'id' => ['name' => $name, 'slug' => $slug],
                                        'en' => ['name' => $name, 'slug' => $slug],
                                    ],
                                ]);
                            }
                            $tagIds[] = $tag->id;
                        }
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
