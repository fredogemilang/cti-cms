<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

#[Signature('media:import
    {path : File or directory path to import (absolute or relative to project root)}
    {--page= : Assign imported images as block values to a page (by slug or ID)}
    {--block= : Block name to assign the image to (use with --page for single file)}
    {--dry-run : Show what would be imported without actually importing}
    {--skip-existing : Skip files that already exist in Media Library (matched by original_filename)}
')]
#[Description('Import image files into Media Library through MediaService pipeline. Generates WebP, responsive variants (sm/md/lg/xl), and LQIP automatically.')]
class MediaImport extends Command
{
    protected const SUPPORTED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

    public function handle(MediaService $svc): int
    {
        $inputPath = $this->argument('path');

        // Resolve relative paths from project root
        if (! str_starts_with($inputPath, '/') && ! preg_match('#^[A-Z]:\\\\#i', $inputPath)) {
            $inputPath = base_path($inputPath);
        }

        if (! file_exists($inputPath)) {
            $this->error("Path not found: {$inputPath}");

            return self::FAILURE;
        }

        // Collect files
        $files = is_dir($inputPath)
            ? $this->collectFromDirectory($inputPath)
            : [$inputPath];

        if (empty($files)) {
            $this->info('No supported image files found.');

            return self::SUCCESS;
        }

        $isDryRun = $this->option('dry-run');
        $skipExisting = $this->option('skip-existing');

        $this->info(($isDryRun ? '[DRY RUN] ' : '').'Found '.count($files).' image(s) to import.');
        $this->newLine();

        $table = [];
        $imported = [];
        $ok = $skip = $fail = 0;

        foreach ($files as $filePath) {
            $filename = basename($filePath);
            $size = File::size($filePath);
            $humanSize = $this->humanSize($size);

            // Check if already exists
            if ($skipExisting) {
                $exists = Media::where('original_filename', $filename)->exists();
                if ($exists) {
                    $table[] = [$filename, $humanSize, '<fg=yellow>SKIPPED</> (exists)'];
                    $skip++;

                    continue;
                }
            }

            if ($isDryRun) {
                $table[] = [$filename, $humanSize, '<fg=cyan>WOULD IMPORT</>'];
                $ok++;

                continue;
            }

            try {
                $uploadedFile = new UploadedFile(
                    $filePath,
                    $filename,
                    mime_content_type($filePath),
                    null,
                    true // test mode — don't move the file
                );

                $media = $svc->upload($uploadedFile, [
                    'alt_text' => $this->filenameToAlt($filename),
                    'title' => $this->filenameToTitle($filename),
                ]);

                $table[] = [
                    $filename,
                    $humanSize,
                    "<fg=green>OK</> → media #{$media->id} ({$media->path})",
                ];
                $imported[$filename] = $media;
                $ok++;
            } catch (\Throwable $e) {
                $table[] = [$filename, $humanSize, "<fg=red>FAILED</>: {$e->getMessage()}"];
                $fail++;
            }
        }

        $this->table(['File', 'Size', 'Result'], $table);
        $this->newLine();

        if ($isDryRun) {
            $this->info("[DRY RUN] Would import {$ok} file(s), skip {$skip}.");

            return self::SUCCESS;
        }

        $this->info("Done: {$ok} imported, {$skip} skipped, {$fail} failed.");

        // Assign to page block if requested
        if ($this->option('page') && $this->option('block') && count($imported) === 1) {
            $this->assignToPageBlock(array_values($imported)[0]);
        }

        // Show usage hints
        if ($ok > 0) {
            $this->newLine();
            $this->line('<fg=cyan>💡 Usage in Blade templates:</>');
            foreach ($imported as $filename => $media) {
                $this->line("   <fg=white><x-image :src=\"'{$media->path}'\" alt=\"{$media->alt_text}\" /></>");
            }
            $this->newLine();
            $this->line('<fg=cyan>💡 Or set as page block value in seeder/command:</>');
            $this->line("   <fg=white>\$page->setBlock('hero_image', \$media->path);</>");
        }

        return self::SUCCESS;
    }

    protected function collectFromDirectory(string $dir): array
    {
        $files = [];
        $extensions = self::SUPPORTED_EXTENSIONS;

        foreach (File::allFiles($dir) as $file) {
            $ext = strtolower($file->getExtension());
            if (in_array($ext, $extensions)) {
                $files[] = $file->getPathname();
            }
        }

        sort($files);

        return $files;
    }

    protected function filenameToAlt(string $filename): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        // Remove Vite hashes like -DHYDqbF8, -w800-e1IoyY61
        $name = preg_replace('/-[A-Za-z0-9]{6,}$/', '', $name);
        $name = preg_replace('/-w\d+(-[A-Za-z0-9]+)?$/', '', $name);
        // Convert separators to spaces
        $name = str_replace(['-', '_'], ' ', $name);

        return ucwords(trim($name));
    }

    protected function filenameToTitle(string $filename): string
    {
        return $this->filenameToAlt($filename);
    }

    protected function assignToPageBlock(Media $media): void
    {
        $pageRef = $this->option('page');
        $blockName = $this->option('block');

        $page = is_numeric($pageRef)
            ? \App\Models\Page::find($pageRef)
            : \App\Models\Page::where('slug', $pageRef)->first();

        if (! $page) {
            $this->warn("Page '{$pageRef}' not found. Skipping block assignment.");

            return;
        }

        $page->setBlock($blockName, $media->path);
        $this->info("✅ Assigned {$media->path} → page '{$page->title}' block '{$blockName}'");
    }

    protected function humanSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 1).' '.$units[$i];
    }
}
