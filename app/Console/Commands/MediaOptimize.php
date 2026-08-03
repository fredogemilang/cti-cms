<?php

namespace App\Console\Commands;

use App\Jobs\GenerateImageVariants;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('media:optimize {--force : Re-convert media that already has a WebP file} {--queue : Dispatch variant generation jobs to background queue worker}')]
#[Description('Generate WebP companions for existing image media. Skips files that already have webp_path.')]
class MediaOptimize extends Command
{
    public function handle(MediaService $svc): int
    {
        $query = Media::query()->where('mime_type', 'like', 'image/%');

        if (! $this->option('force')) {
            $query->where(fn ($w) => $w->whereNull('webp_path')->orWhere('webp_path', ''));
        }

        $total = $query->count();
        if ($total === 0) {
            $this->info('Nothing to optimize.');

            return self::SUCCESS;
        }

        $isQueue = (bool) $this->option('queue');

        $this->info($isQueue ? "Dispatching {$total} image(s) to queue worker..." : "Optimizing {$total} image(s)...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $ok = $fail = 0;
        foreach ($query->cursor() as $media) {
            try {
                if ($isQueue) {
                    GenerateImageVariants::dispatch($media->id);
                    $ok++;
                } else {
                    $webpPath = $svc->convertToWebp($media->path);
                    if ($webpPath) {
                        $media->update(['webp_path' => $webpPath]);
                        $ok++;
                    } else {
                        $fail++;
                    }
                }
            } catch (\Throwable $e) {
                $fail++;
                $this->newLine();
                $this->error("[#{$media->id}] {$media->original_filename}: {$e->getMessage()}");
            }
            $bar->advance();
        }
        // Also scan storage/app/public/media for unindexed images
        $storageDir = storage_path('app/public/media');
        if (is_dir($storageDir)) {
            $unindexed = glob($storageDir.'/*.{jpg,jpeg,png,JPG,JPEG,PNG}', GLOB_BRACE) ?: [];
            foreach ($unindexed as $filePath) {
                $filename = basename($filePath);
                $webpFilename = pathinfo($filename, PATHINFO_FILENAME).'.webp';
                $webpPath = dirname($filePath).'/'.$webpFilename;

                if (! file_exists($webpPath)) {
                    $mime = @mime_content_type($filePath);
                    $img = match ($mime) {
                        'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($filePath),
                        'image/png' => @imagecreatefrompng($filePath),
                        default => null,
                    };
                    if ($img) {
                        if ($mime === 'image/png') {
                            $w = imagesx($img);
                            $h = imagesy($img);
                            $canvas = imagecreatetruecolor($w, $h);
                            imagealphablending($canvas, false);
                            imagesavealpha($canvas, true);
                            $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
                            imagefill($canvas, 0, 0, $transparent);
                            imagecopy($canvas, $img, 0, 0, 0, 0, $w, $h);
                            imagedestroy($img);
                            $img = $canvas;
                        }
                        imagewebp($img, $webpPath, 85);
                        imagedestroy($img);
                        $ok++;
                    }
                }
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Done: {$ok} converted, {$fail} failed.");

        return self::SUCCESS;
    }
}
