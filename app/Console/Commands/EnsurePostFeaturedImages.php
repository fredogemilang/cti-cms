<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Plugins\Posts\Models\Post;

class EnsurePostFeaturedImages extends Command
{
    protected $signature = 'posts:ensure-featured-images
                            {--force : Re-extract first image from content even if featured_image is already set}
                            {--dry-run : Only show what would be updated without saving changes}';

    protected $description = 'Check all posts and set the first image from content as featured_image if missing';

    public function handle(MediaService $mediaService): int
    {
        $force = $this->option('force');
        $dryRun = $this->option('dry-run');

        $query = Post::query();
        if (! $force) {
            $query->where(function ($q) {
                $q->whereNull('featured_image')
                  ->orWhere('featured_image', '');
            });
        }

        $posts = $query->get();
        $total = $posts->count();

        $this->info("Found {$total} post(s) to inspect.");
        if ($total === 0) {
            $this->info("All posts already have a featured image. Nothing to do.");
            return 0;
        }

        $updated = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($posts as $post) {
            $this->output->write("Processing Post #{$post->id} ('{$post->title}')... ");

            $imageUrl = $this->extractFirstImage($post);
            if (! $imageUrl) {
                $this->output->writeln("<comment>No image found in content.</comment>");
                $skipped++;
                continue;
            }

            $this->output->write("found image: {$imageUrl}... ");

            if ($dryRun) {
                $this->output->writeln("<info>[DRY RUN] would set image</info>");
                $updated++;
                continue;
            }

            $mediaPath = $this->resolveMedia($imageUrl, $mediaService);
            if (! $mediaPath) {
                $this->output->writeln("<error>Failed to resolve/download image.</error>");
                $failed++;
                continue;
            }

            $post->featured_image = $mediaPath;
            $post->save();

            $this->output->writeln("<info>Updated with {$mediaPath}</info>");
            $updated++;
        }

        $this->newLine();
        $this->info("==========================================");
        $this->info("  Finished processing featured images");
        $this->info("  Total inspected: {$total}");
        $this->info("  Updated:         {$updated}");
        $this->info("  Skipped (no img):{$skipped}");
        $this->info("  Failed:          {$failed}");
        $this->info("==========================================");

        return 0;
    }

    protected function extractFirstImage(Post $post): ?string
    {
        // 1. Check EN / primary content
        $content = $post->content ?? '';
        if ($url = $this->findImageInHtml($content)) {
            return $url;
        }

        // 2. Check localized translations (e.g. ID)
        if (! empty($post->translations) && is_array($post->translations)) {
            foreach ($post->translations as $locale => $trans) {
                if (! empty($trans['content'])) {
                    if ($url = $this->findImageInHtml($trans['content'])) {
                        return $url;
                    }
                }
            }
        }

        return null;
    }

    protected function findImageInHtml(?string $html): ?string
    {
        if (empty($html)) {
            return null;
        }

        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $matches)) {
            $src = trim($matches[1]);
            if (! empty($src)) {
                return $src;
            }
        }

        return null;
    }

    protected function resolveMedia(string $url, MediaService $mediaService): ?string
    {
        try {
            // Already a local relative path?
            if (! Str::startsWith($url, ['http://', 'https://', '//'])) {
                $cleanPath = ltrim($url, '/');
                $cleanPath = preg_replace('#^(storage/|public/)#', '', $cleanPath);
                return $cleanPath;
            }

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

            $tmpPath = $tmpDir . '/' . uniqid('featured_img_') . '_' . $originalFilename;
            file_put_contents($tmpPath, $response->body());

            $mimeType = $response->header('Content-Type') ?: mime_content_type($tmpPath) ?: 'image/jpeg';
            $uploadedFile = new UploadedFile($tmpPath, $originalFilename, $mimeType, null, true);

            $media = $mediaService->upload($uploadedFile, [
                'title' => pathinfo($originalFilename, PATHINFO_FILENAME),
                'description' => 'Featured image extracted from post content',
                'alt_text' => pathinfo($originalFilename, PATHINFO_FILENAME),
            ]);

            @unlink($tmpPath);

            return $media->path;
        } catch (\Throwable $e) {
            Log::warning("Failed resolving featured image from {$url}: " . $e->getMessage());
            return null;
        }
    }
}
