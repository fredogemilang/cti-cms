<?php

namespace App\Services;

use App\Jobs\GenerateImageVariants;
use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    /**
     * Upload a file and create media record.
     */
    public function upload(UploadedFile $file, ?array $metadata = []): Media
    {
        // Generate unique filename
        $filename = $this->generateUniqueFilename($file);
        $path = config('media.path').'/'.$filename;

        // Store the file
        $disk = Storage::disk(config('media.disk'));
        $disk->put($path, file_get_contents($file->getRealPath()));

        // Get file information
        $mimeType = $file->getMimeType();
        $extension = $file->getClientOriginalExtension();

        // Optimize original image if enabled
        if ($this->isImage($mimeType)) {
            $this->optimizeOriginal($path, $mimeType);
        }

        // Get updated size and dimensions after optimization
        $fullPath = $disk->path($path);
        $size = file_exists($fullPath) ? filesize($fullPath) : $file->getSize();
        $dimensions = $this->isImage($mimeType) ? $this->getImageDimensions($fullPath) : null;

        // Create media record
        $media = Media::create([
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $mimeType,
            'file_extension' => $extension,
            'size' => $size,
            'path' => $path,
            'width' => $dimensions['width'] ?? null,
            'height' => $dimensions['height'] ?? null,
            'alt_text' => ! empty($metadata['alt_text']) ? $metadata['alt_text'] : $file->getClientOriginalName(),
            'title' => $metadata['title'] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'description' => $metadata['description'] ?? null,
            'uploaded_by' => auth()->id(),
        ]);

        // Convert to WebP if it's an image and conversion is enabled
        if ($this->shouldConvertToWebp($mimeType)) {
            $webpPath = $this->convertToWebp($path);
            if ($webpPath) {
                $media->update(['webp_path' => $webpPath]);
            }
        }

        // Queue responsive variant generation for raster images.
        if ($this->isImage($mimeType) && $mimeType !== 'image/svg+xml') {
            GenerateImageVariants::dispatch($media->id);
        }

        return $media;
    }

    /**
     * Generate a unique filename for the uploaded file.
     */
    protected function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $basename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // Sanitize filename
        $basename = Str::slug($basename);

        // Add timestamp and random string to ensure uniqueness
        return $basename.'-'.time().'-'.Str::random(8).'.'.$extension;
    }

    /**
     * Convert an image to WebP format.
     *
     * @param  string  $path  Relative path to the image
     * @return string|null Path to WebP version or null on failure
     */
    public function convertToWebp(string $path): ?string
    {
        try {
            $disk = Storage::disk(config('media.disk'));
            $fullPath = $disk->path($path);

            if (! file_exists($fullPath)) {
                return null;
            }

            // Determine image type and create image resource
            $imageInfo = getimagesize($fullPath);
            $mimeType = $imageInfo['mime'] ?? null;

            $image = match ($mimeType) {
                'image/jpeg', 'image/jpg' => imagecreatefromjpeg($fullPath),
                'image/png' => imagecreatefrompng($fullPath),
                'image/gif' => imagecreatefromgif($fullPath),
                default => null,
            };

            if (! $image) {
                return null;
            }

            // Preserve transparency for PNG
            if ($mimeType === 'image/png') {
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
            }

            // Generate WebP filename
            $webpFilename = pathinfo($path, PATHINFO_FILENAME).'.webp';
            $webpPath = config('media.path').'/'.$webpFilename;
            $webpFullPath = $disk->path($webpPath);

            // Convert to WebP
            $quality = config('media.webp.quality', 80);
            $success = imagewebp($image, $webpFullPath, $quality);

            // Free up memory
            imagedestroy($image);

            return $success ? $webpPath : null;
        } catch (\Exception $e) {
            \Log::error('WebP conversion failed: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Delete a media file and its WebP version.
     */
    public function delete(Media $media): bool
    {
        return $media->delete();
    }

    /**
     * Update media metadata.
     */
    public function updateMetadata(Media $media, array $data): Media
    {
        $media->update([
            'alt_text' => $data['alt_text'] ?? $media->alt_text,
            'title' => $data['title'] ?? $media->title,
            'description' => $data['description'] ?? $media->description,
        ]);

        return $media->fresh();
    }

    /**
     * Get image dimensions.
     *
     * @param  string  $path  Full path to the image
     */
    protected function getImageDimensions(string $path): ?array
    {
        try {
            $imageInfo = getimagesize($path);

            return [
                'width' => $imageInfo[0] ?? null,
                'height' => $imageInfo[1] ?? null,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if the MIME type is an image.
     */
    protected function isImage(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'image/');
    }

    /**
     * Check if the image should be converted to WebP.
     */
    protected function shouldConvertToWebp(string $mimeType): bool
    {
        if (! config('media.webp.enabled', true)) {
            return false;
        }

        $convertTypes = config('media.webp.convert_types', []);

        return in_array($mimeType, $convertTypes);
    }

    /**
     * Bulk delete media files.
     *
     * @return int Number of deleted files
     */
    public function bulkDelete(array $mediaIds): int
    {
        $media = Media::whereIn('id', $mediaIds)->get();
        $count = 0;

        foreach ($media as $item) {
            if ($this->delete($item)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Optimize original image file (quality compression & optional max dimension resizing).
     */
    public function optimizeOriginal(string $path, string $mimeType): bool
    {
        if (! (bool) setting('img_optimize_original', true)) {
            return false;
        }

        if (! in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png'])) {
            return false;
        }

        try {
            $disk = Storage::disk(config('media.disk'));
            $fullPath = $disk->path($path);

            if (! file_exists($fullPath)) {
                return false;
            }

            $source = match ($mimeType) {
                'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($fullPath),
                default => @imagecreatefrompng($fullPath),
            };

            if (! $source) {
                return false;
            }

            $srcW = imagesx($source);
            $srcH = imagesy($source);

            $maxDim = (int) setting('img_max_dimension', 2560);
            $needsResize = $maxDim > 0 && ($srcW > $maxDim || $srcH > $maxDim);

            if ($needsResize) {
                if ($srcW >= $srcH) {
                    $targetW = $maxDim;
                    $targetH = (int) round($srcH * ($maxDim / $srcW));
                } else {
                    $targetH = $maxDim;
                    $targetW = (int) round($srcW * ($maxDim / $srcH));
                }

                $canvas = imagecreatetruecolor($targetW, $targetH);
                if ($mimeType === 'image/png') {
                    imagealphablending($canvas, false);
                    imagesavealpha($canvas, true);
                    $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
                    imagefilledrectangle($canvas, 0, 0, $targetW, $targetH, $transparent);
                }
                imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetW, $targetH, $srcW, $srcH);
                imagedestroy($source);
                $source = $canvas;
            }

            $jpgQ = (int) setting('img_jpg_quality', 85);
            match ($mimeType) {
                'image/jpeg', 'image/jpg' => imagejpeg($source, $fullPath, $jpgQ),
                default => imagepng($source, $fullPath, 6),
            };

            imagedestroy($source);
            clearstatcache(true, $fullPath);

            return true;
        } catch (\Exception $e) {
            \Log::error('Original image optimization failed: '.$e->getMessage());

            return false;
        }
    }
}
