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

        // Include the original at its native width as the largest candidate.
        if ($media->width) {
            $jpegPairs[] = $diskUrl($media->path)." {$media->width}w";
            if ($media->webp_path) {
                $webpPairs[] = $diskUrl($media->webp_path)." {$media->width}w";
            }
        }

        $picked = $variants[$size] ?? null;
        $src = $picked ? $diskUrl($picked['path']) : $diskUrl($media->path);

        return [
            'src' => $src,
            'srcset' => implode(', ', $jpegPairs),
            'webp_srcset' => $webpPairs ? implode(', ', $webpPairs) : null,
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
            $webpPathCandidate = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $cleanPath);

            if (file_exists(public_path($webpPathCandidate))) {
                $webpUrl = asset($webpPathCandidate);
            } elseif (file_exists(public_path($cleanPath.'.webp'))) {
                $webpUrl = asset($cleanPath.'.webp');
            } else {
                $storageRelative = preg_replace('#^storage/#', '', $webpPathCandidate);
                if (Storage::disk(config('media.disk', 'public'))->exists($storageRelative)) {
                    $webpUrl = Storage::disk(config('media.disk', 'public'))->url($storageRelative);
                }
            }
        } elseif ($ext === 'webp') {
            $webpUrl = $originalUrl;
        }

        return [
            'src' => $originalUrl,
            'srcset' => '',
            'webp_srcset' => $webpUrl,
            'sizes' => '',
            'width' => null,
            'height' => null,
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
