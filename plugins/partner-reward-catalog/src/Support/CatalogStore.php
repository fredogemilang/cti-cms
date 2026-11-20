<?php

namespace Plugins\PartnerRewardCatalog\Support;

use App\Models\Media;
use App\Models\Setting;
use App\Services\CacheManager;
use App\Services\MediaService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CatalogStore
{
    public const SETTING_KEY = 'partner_reward_catalog';

    /**
     * @return array<string, mixed>
     */
    public static function getConfig(): array
    {
        $raw = Setting::get(self::SETTING_KEY, []);
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : [];
        }
        if (! is_array($raw)) {
            $raw = [];
        }

        return [
            'media_id' => isset($raw['media_id']) && $raw['media_id'] ? (int) $raw['media_id'] : null,
            'file_path' => ! empty($raw['file_path']) ? (string) $raw['file_path'] : null,
            'original_filename' => ! empty($raw['original_filename']) ? (string) $raw['original_filename'] : null,
            'file_size' => isset($raw['file_size']) && $raw['file_size'] ? (int) $raw['file_size'] : null,
            'cover_media_id' => isset($raw['cover_media_id']) && $raw['cover_media_id'] ? (int) $raw['cover_media_id'] : null,
            'cover_image_path' => ! empty($raw['cover_image_path']) ? (string) $raw['cover_image_path'] : null,
            'cover_original_filename' => ! empty($raw['cover_original_filename']) ? (string) $raw['cover_original_filename'] : null,
            'title_en' => ! empty($raw['title_en']) ? (string) $raw['title_en'] : 'Partner Reward Catalog',
            'title_id' => ! empty($raw['title_id']) ? (string) $raw['title_id'] : 'Katalog Reward Partner',
            'subtitle_en' => isset($raw['subtitle_en']) && $raw['subtitle_en'] !== ''
                ? (string) $raw['subtitle_en']
                : 'Explore exclusive partner incentives, tier rewards, and redemption programs in an interactive flipbook catalog.',
            'subtitle_id' => isset($raw['subtitle_id']) && $raw['subtitle_id'] !== ''
                ? (string) $raw['subtitle_id']
                : 'Jelajahi insentif eksklusif mitra, reward tier, dan program penukaran dalam katalog flipbook interaktif.',
            'promo_title_en' => ! empty($raw['promo_title_en']) ? (string) $raw['promo_title_en'] : 'Annual Partner Incentive Trip 2027',
            'promo_title_id' => ! empty($raw['promo_title_id']) ? (string) $raw['promo_title_id'] : 'Annual Partner Incentive Trip 2027',
            'promo_description_en' => isset($raw['promo_description_en']) && $raw['promo_description_en'] !== ''
                ? (string) $raw['promo_description_en']
                : 'Turn your sales achievements into an extraordinary journey to Germany! Reach IDR 6 Billion in sales and get ready to explore the iconic Neuschwanstein Castle, shop at Metzingen Outlets, and visit the Mercedes-Benz Museum.',
            'promo_description_id' => isset($raw['promo_description_id']) && $raw['promo_description_id'] !== ''
                ? (string) $raw['promo_description_id']
                : 'Wujudkan pencapaian penjualan Anda menjadi perjalanan istimewa ke Jerman! Raih target penjualan Rp 6 Miliar dan bersiaplah menjelajahi Kastel Neuschwanstein yang ikonik, berbelanja di Outletcity Metzingen, serta mengunjungi Museum Mercedes-Benz.',
            'promo_period_en' => isset($raw['promo_period_en']) && $raw['promo_period_en'] !== ''
                ? (string) $raw['promo_period_en']
                : 'Promo Period: 1 January – 31 December 2026.',
            'promo_period_id' => isset($raw['promo_period_id']) && $raw['promo_period_id'] !== ''
                ? (string) $raw['promo_period_id']
                : 'Periode Promo: 1 Januari – 31 Desember 2026.',
            'promo_cta_text_en' => ! empty($raw['promo_cta_text_en']) ? (string) $raw['promo_cta_text_en'] : 'Explore the Partner Reward Catalog',
            'promo_cta_text_id' => ! empty($raw['promo_cta_text_id']) ? (string) $raw['promo_cta_text_id'] : 'Lihat Katalog Hadiah Mitra',
            'allow_download' => array_key_exists('allow_download', $raw) ? (bool) $raw['allow_download'] : true,
            'updated_at' => ! empty($raw['updated_at']) ? (string) $raw['updated_at'] : null,
        ];
    }

    /**
     * Resolve localized Promo Banner data for the Partner landing page.
     *
     * @return array{cover_path: string|null, title: string, description: string, period: string, cta_text: string, cta_url: string}
     */
    public static function promoBanner(?string $locale = null): array
    {
        $locale ??= app()->getLocale();
        $isId = $locale === 'id';
        $config = static::getConfig();

        return [
            'cover_path' => static::coverImagePath($config),
            'title' => $isId ? $config['promo_title_id'] : $config['promo_title_en'],
            'description' => $isId ? $config['promo_description_id'] : $config['promo_description_en'],
            'period' => $isId ? $config['promo_period_id'] : $config['promo_period_en'],
            'cta_text' => $isId ? $config['promo_cta_text_id'] : $config['promo_cta_text_en'],
            'cta_url' => '/partner-reward-catalog',
        ];
    }

    /**
     * Resolve active Media record for the catalog PDF if present and still valid on disk.
     */
    public static function activeMedia(?array $config = null): ?Media
    {
        $config ??= static::getConfig();

        if (! empty($config['media_id'])) {
            $media = Media::find($config['media_id']);
            if ($media && Storage::disk(config('media.disk', 'public'))->exists($media->path)) {
                return $media;
            }
        }

        if (! empty($config['file_path']) && Storage::disk(config('media.disk', 'public'))->exists($config['file_path'])) {
            return Media::where('path', $config['file_path'])->first();
        }

        return null;
    }

    /**
     * Resolve relative storage path for the active catalog Cover Image (e.g. for <x-image :src="..." />).
     */
    public static function coverImagePath(?array $config = null): ?string
    {
        $config ??= static::getConfig();
        $disk = Storage::disk(config('media.disk', 'public'));

        if (! empty($config['cover_media_id'])) {
            $media = Media::find($config['cover_media_id']);
            if ($media) {
                $preferredPath = $media->webp_path && $disk->exists($media->webp_path)
                    ? $media->webp_path
                    : $media->path;
                if ($preferredPath && $disk->exists($preferredPath)) {
                    return $preferredPath;
                }
            }
        }

        if (! empty($config['cover_image_path']) && $disk->exists($config['cover_image_path'])) {
            return $config['cover_image_path'];
        }

        return null;
    }

    /**
     * Resolve display URL for the active catalog Cover Image.
     */
    public static function coverImageUrl(?array $config = null): ?string
    {
        $path = static::coverImagePath($config);
        if (! $path) {
            return null;
        }

        return function_exists('resolve_block_asset') ? resolve_block_asset($path) : asset('storage/'.ltrim($path, '/'));
    }

    /**
     * Resolve same-origin URL to the active catalog PDF.
     */
    public static function pdfUrl(?array $config = null): ?string
    {
        $config ??= static::getConfig();
        $media = static::activeMedia($config);
        $path = $media?->path ?? $config['file_path'] ?? null;

        if (! $path || ! Storage::disk(config('media.disk', 'public'))->exists($path)) {
            return null;
        }

        return asset('storage/'.ltrim($path, '/'));
    }

    /**
     * Save catalog metadata & optionally replace the single active PDF / Cover Image cleanly.
     */
    public static function saveCatalog(array $metadata, ?UploadedFile $newPdf = null, ?UploadedFile $newCoverImage = null): array
    {
        $current = static::getConfig();

        $next = array_merge($current, [
            'title_en' => trim((string) ($metadata['title_en'] ?? $current['title_en'])) ?: 'Partner Reward Catalog',
            'title_id' => trim((string) ($metadata['title_id'] ?? $current['title_id'])) ?: 'Katalog Reward Partner',
            'subtitle_en' => trim((string) ($metadata['subtitle_en'] ?? $current['subtitle_en'])),
            'subtitle_id' => trim((string) ($metadata['subtitle_id'] ?? $current['subtitle_id'])),
            'promo_title_en' => trim((string) ($metadata['promo_title_en'] ?? $current['promo_title_en'])) ?: 'Annual Partner Incentive Trip 2027',
            'promo_title_id' => trim((string) ($metadata['promo_title_id'] ?? $current['promo_title_id'])) ?: 'Annual Partner Incentive Trip 2027',
            'promo_description_en' => trim((string) ($metadata['promo_description_en'] ?? $current['promo_description_en'])),
            'promo_description_id' => trim((string) ($metadata['promo_description_id'] ?? $current['promo_description_id'])),
            'promo_period_en' => trim((string) ($metadata['promo_period_en'] ?? $current['promo_period_en'])),
            'promo_period_id' => trim((string) ($metadata['promo_period_id'] ?? $current['promo_period_id'])),
            'promo_cta_text_en' => trim((string) ($metadata['promo_cta_text_en'] ?? $current['promo_cta_text_en'])) ?: 'Explore the Partner Reward Catalog',
            'promo_cta_text_id' => trim((string) ($metadata['promo_cta_text_id'] ?? $current['promo_cta_text_id'])) ?: 'Lihat Katalog Hadiah Mitra',
            'allow_download' => (bool) ($metadata['allow_download'] ?? $current['allow_download']),
        ]);

        if ($newPdf !== null) {
            $oldMediaId = $current['media_id'] ?? null;
            $oldPath = $current['file_path'] ?? null;

            $media = app(MediaService::class)->upload($newPdf, [
                'title' => $next['title_en'].' (PDF)',
                'alt_text' => $next['title_en'],
                'uploaded_by' => auth()->id() ?? 1,
            ]);

            static::deletePhysicalAndRecord($oldMediaId, $oldPath, $media->id);

            $next['media_id'] = $media->id;
            $next['file_path'] = $media->path;
            $next['original_filename'] = $media->original_filename;
            $next['file_size'] = $media->size;
            $next['updated_at'] = now()->toIso8601String();
        }

        if ($newCoverImage !== null) {
            $coverMedia = app(MediaService::class)->upload($newCoverImage, [
                'title' => $next['promo_title_en'].' (Cover Banner)',
                'alt_text' => $next['promo_title_en'],
                'uploaded_by' => auth()->id() ?? 1,
            ]);

            $next['cover_media_id'] = $coverMedia->id;
            $next['cover_image_path'] = $coverMedia->webp_path ?: $coverMedia->path;
            $next['cover_original_filename'] = $coverMedia->original_filename;
            $next['updated_at'] = now()->toIso8601String();
        } elseif (array_key_exists('cover_media_id', $metadata)) {
            $coverMediaId = $metadata['cover_media_id'] ? (int) $metadata['cover_media_id'] : null;

            if ($coverMediaId) {
                $coverMedia = Media::find($coverMediaId);
                if ($coverMedia) {
                    $next['cover_media_id'] = $coverMedia->id;
                    $next['cover_image_path'] = $coverMedia->webp_path ?: $coverMedia->path;
                    $next['cover_original_filename'] = $coverMedia->original_filename;
                }
            } else {
                $next['cover_media_id'] = null;
                $next['cover_image_path'] = ! empty($metadata['cover_image_path']) ? (string) $metadata['cover_image_path'] : null;
                $next['cover_original_filename'] = $next['cover_image_path'] ? basename($next['cover_image_path']) : null;
            }

            $next['updated_at'] = now()->toIso8601String();
        } elseif (! empty($metadata['cover_image_path'])) {
            $path = (string) $metadata['cover_image_path'];
            $coverMedia = Media::where('path', $path)->orWhere('webp_path', $path)->first();

            $next['cover_media_id'] = $coverMedia?->id;
            $next['cover_image_path'] = $coverMedia ? ($coverMedia->webp_path ?: $coverMedia->path) : $path;
            $next['cover_original_filename'] = $coverMedia?->original_filename ?? basename($path);
            $next['updated_at'] = now()->toIso8601String();
        }

        Setting::set(self::SETTING_KEY, $next, 'partner-reward-catalog', 'json');
        CacheManager::purgeAll();

        return $next;
    }

    /**
     * Delete the active catalog PDF and clear file references from settings.
     */
    public static function removePdf(): array
    {
        $current = static::getConfig();
        static::deletePhysicalAndRecord($current['media_id'] ?? null, $current['file_path'] ?? null);

        $next = array_merge($current, [
            'media_id' => null,
            'file_path' => null,
            'original_filename' => null,
            'file_size' => null,
            'updated_at' => now()->toIso8601String(),
        ]);

        Setting::set(self::SETTING_KEY, $next, 'partner-reward-catalog', 'json');
        CacheManager::purgeAll();

        return $next;
    }

    /**
     * Clear the active Partner promo cover selection from settings (preserving Media Library item).
     */
    public static function removeCoverImage(): array
    {
        $current = static::getConfig();

        $next = array_merge($current, [
            'cover_media_id' => null,
            'cover_image_path' => null,
            'cover_original_filename' => null,
            'updated_at' => now()->toIso8601String(),
        ]);

        Setting::set(self::SETTING_KEY, $next, 'partner-reward-catalog', 'json');
        CacheManager::purgeAll();

        return $next;
    }

    protected static function deletePhysicalAndRecord(?int $mediaId, ?string $path, ?int $exceptMediaId = null): void
    {
        $disk = Storage::disk(config('media.disk', 'public'));

        if ($mediaId && $mediaId !== $exceptMediaId) {
            $oldMedia = Media::find($mediaId);
            if ($oldMedia) {
                if ($oldMedia->path && $disk->exists($oldMedia->path)) {
                    $disk->delete($oldMedia->path);
                }
                if ($oldMedia->webp_path && $disk->exists($oldMedia->webp_path)) {
                    $disk->delete($oldMedia->webp_path);
                }
                $oldMedia->forceDelete();
            }
        }

        if ($path && $disk->exists($path)) {
            $disk->delete($path);
        }
    }
}
