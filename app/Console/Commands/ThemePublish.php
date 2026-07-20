<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Publish theme assets to the public directory.
 *
 * Copies the theme's `assets/` directory to `public/themes/{slug}/assets/`
 * so CSS, JS, and images are web-accessible.
 *
 * Usage:
 *   php artisan theme:publish default         # Publish one theme
 *   php artisan theme:publish --all           # Publish all themes
 */
class ThemePublish extends Command
{
    protected $signature = 'theme:publish
        {slug? : The theme slug to publish}
        {--all : Publish assets for all themes}
        {--force : Overwrite existing published assets}';

    protected $description = 'Publish theme assets to the public directory.';

    public function handle(): int
    {
        if ($this->option('all')) {
            return $this->publishAll();
        }

        $slug = $this->argument('slug');

        if (! $slug) {
            $this->components->error('Please specify a theme slug or use --all.');

            return self::FAILURE;
        }

        return $this->publishTheme($slug);
    }

    protected function publishAll(): int
    {
        $themesPath = base_path('themes');

        if (! is_dir($themesPath)) {
            $this->components->warn('No themes directory found.');

            return self::SUCCESS;
        }

        $published = 0;
        foreach (File::directories($themesPath) as $dir) {
            $slug = basename($dir);
            if ($this->publishTheme($slug) === self::SUCCESS) {
                $published++;
            }
        }

        $this->newLine();
        $this->components->info("{$published} theme(s) published.");

        return self::SUCCESS;
    }

    protected function publishTheme(string $slug): int
    {
        $source = base_path("themes/{$slug}/assets");
        $target = public_path("themes/{$slug}/assets");

        if (! is_dir($source)) {
            $this->components->warn("No assets directory for theme '{$slug}' — skipping.");

            return self::SUCCESS;
        }

        // Check if target exists and --force is not set
        if (is_dir($target) && ! $this->option('force')) {
            $this->components->task("{$slug}", function () use ($source, $target) {
                // Sync: copy only newer files
                $this->syncDirectory($source, $target);

                return true;
            });
        } else {
            $this->components->task("{$slug}", function () use ($source, $target) {
                File::copyDirectory($source, $target);

                return true;
            });
        }

        return self::SUCCESS;
    }

    /**
     * Copy files from source to target, only if source is newer.
     */
    protected function syncDirectory(string $source, string $target): void
    {
        File::ensureDirectoryExists($target);

        foreach (File::allFiles($source) as $file) {
            $relativePath = $file->getRelativePathname();
            $targetFile = $target . DIRECTORY_SEPARATOR . $relativePath;

            $targetDir = dirname($targetFile);
            if (! is_dir($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }

            // Only copy if source is newer or target doesn't exist
            if (! file_exists($targetFile) || $file->getMTime() > filemtime($targetFile)) {
                File::copy($file->getPathname(), $targetFile);
            }
        }
    }
}
