<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class ResponsiveImageService
{
    /**
     * Build srcset + sizes data for a media item.
     *
     * @return array{src:string,srcset:string,webp_srcset:?string,sizes:string,width:?int,height:?int,placeholder:?string,alt:?string,focal:array{x:float,y:float}}
     */
    public function build(Media|string|null $media, string $size = 'lg', string $sizesAttr = '100vw'): array
    {
        if (is_string($media)) {
            $rawPath = (string) parse_url($media, PHP_URL_PATH);
            $cleanPath = ltrim($rawPath, '/');
            $basename = basename($cleanPath);

            $mediaModel = Media::where('path', $cleanPath)
                ->orWhere('webp_path', $cleanPath)
                ->orWhere('path', 'like', '%/'.$basename)
                ->first();

            if ($mediaModel) {
                $media = $mediaModel;
            } else {
                return $this->buildFromStringPath($media, $cleanPath);
            }
        }

        if (! $media instanceof Media) {
            return $this->blank();
        }

        $variants = (array) ($media->variants ?? []);
        $diskUrl = fn ($p) => $p ? Storage::disk(config('media.disk', 'public'))->url($p) : null;

        $ext = strtolower(pathinfo($media->path, PATHINFO_EXTENSION));
        if ($media->mime_type === 'image/svg+xml' || $ext === 'svg') {
            return [
                'src' => $diskUrl($media->path),
                'srcset' => '',
                'webp_srcset' => null,
                'sizes' => '',
                'width' => $media->width,
                'height' => $media->height,
                'placeholder' => null,
                'alt' => $media->alt_text ?? $media->title,
                'focal' => ['x' => 0.5, 'y' => 0.5],
            ];
        }

        $jpegPairs = [];
        $webpPairs = [];
        foreach ($variants as $label => $v) {
            if (empty($v['path']) || empty($v['width'])) {
                continue;
            }
            $jpegPairs[] = $diskUrl($v['path'])." {$v['width']}w";
            if (! empty($v['webp'])) {
                $webpPairs[] = $diskUrl($v['webp'])." {$v['width']}w";
            }
        }

        // Include original / webp_path as candidate
        if ($media->path) {
            $wAttr = $media->width ? " {$media->width}w" : '';
            $jpegPairs[] = $diskUrl($media->path).$wAttr;
        }
        if ($media->webp_path) {
            $wAttr = $media->width ? " {$media->width}w" : '';
            $webpPairs[] = $diskUrl($media->webp_path).$wAttr;
        }

        $picked = $variants[$size] ?? null;
        $src = $picked ? $diskUrl($picked['path']) : $diskUrl($media->path);

        return [
            'src' => $src,
            'srcset' => implode(', ', array_unique($jpegPairs)),
            'webp_srcset' => $webpPairs ? implode(', ', array_unique($webpPairs)) : null,
            'sizes' => $sizesAttr,
            'width' => $picked['width'] ?? $media->width,
            'height' => $picked['height'] ?? $media->height,
            'placeholder' => $media->placeholder_data_uri,
            'alt' => $media->alt_text ?? $media->title,
            'focal' => [
                'x' => (float) ($media->focal_x ?? 0.5),
                'y' => (float) ($media->focal_y ?? 0.5),
            ],
        ];
    }

    protected function buildFromStringPath(string $originalUrl, string $cleanPath): array
    {
        $webpUrl = null;
        $ext = strtolower(pathinfo($cleanPath, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $webpCandidate = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $cleanPath);

            if (file_exists(public_path($webpCandidate))) {
                $webpUrl = asset($webpCandidate);
            } elseif (file_exists(base_path($webpCandidate))) {
                $webpUrl = asset($webpCandidate);
            } elseif (file_exists(public_path($cleanPath.'.webp'))) {
                $webpUrl = asset($cleanPath.'.webp');
            } elseif (file_exists(base_path($cleanPath.'.webp'))) {
                $webpUrl = asset($cleanPath.'.webp');
            } else {
                $storageRelative = preg_replace('#^storage/#', '', $webpCandidate);
                $disk = Storage::disk(config('media.disk', 'public'));
                if ($disk->exists($storageRelative)) {
                    $webpUrl = $disk->url($storageRelative);
                } elseif ($disk->exists($storageRelative.'.webp')) {
                    $webpUrl = $disk->url($storageRelative.'.webp');
                }
            }
        } elseif ($ext === 'webp') {
            $webpUrl = $originalUrl;
        }

        $width = null;
        $height = null;

        $checkPath = public_path($cleanPath);
        if (! file_exists($checkPath)) {
            $checkPath = base_path($cleanPath);
        }
        if (! file_exists($checkPath)) {
            $storageRelative = preg_replace('#^storage/#', '', $cleanPath);
            $disk = Storage::disk(config('media.disk', 'public'));
            if ($disk->exists($storageRelative)) {
                $checkPath = $disk->path($storageRelative);
            }
        }
        if (file_exists($checkPath) && is_file($checkPath)) {
            $imageInfo = @getimagesize($checkPath);
            if (is_array($imageInfo)) {
                $width = $imageInfo[0];
                $height = $imageInfo[1];
            }
        }

        return [
            'src' => $originalUrl,
            'srcset' => '',
            'webp_srcset' => $webpUrl,
            'sizes' => '',
            'width' => $width,
            'height' => $height,
            'placeholder' => null,
            'alt' => null,
            'focal' => ['x' => 0.5, 'y' => 0.5],
        ];
    }

    protected function blank(): array
    {
        return [
            'src' => null, 'srcset' => '', 'webp_srcset' => null, 'sizes' => '',
            'width' => null, 'height' => null, 'placeholder' => null, 'alt' => null,
            'focal' => ['x' => 0.5, 'y' => 0.5],
        ];
    }
}
