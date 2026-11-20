<?php

namespace Plugins\PartnerRewardCatalog\Support;

use App\Models\Media;
use App\Models\Setting;
use App\Services\MediaService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CatalogStore
{
    public const SETTING_KEY = 'partner_reward_catalog';

    /**
     * @return array{
     *     media_id: int|null,
     *     file_path: string|null,
     *     original_filename: string|null,
     *     file_size: int|null,
     *     title_en: string,
     *     title_id: string,
     *     subtitle_en: string,
     *     subtitle_id: string,
     *     allow_download: bool,
     *     updated_at: string|null
     * }
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
            'title_en' => ! empty($raw['title_en']) ? (string) $raw['title_en'] : 'Partner Reward Catalog',
            'title_id' => ! empty($raw['title_id']) ? (string) $raw['title_id'] : 'Katalog Reward Partner',
            'subtitle_en' => isset($raw['subtitle_en']) && $raw['subtitle_en'] !== ''
                ? (string) $raw['subtitle_en']
                : 'Explore exclusive partner incentives, tier rewards, and redemption programs in an interactive flipbook catalog.',
            'subtitle_id' => isset($raw['subtitle_id']) && $raw['subtitle_id'] !== ''
                ? (string) $raw['subtitle_id']
                : 'Jelajahi insentif eksklusif mitra, reward tier, dan program penukaran dalam katalog flipbook interaktif.',
            'allow_download' => array_key_exists('allow_download', $raw) ? (bool) $raw['allow_download'] : true,
            'updated_at' => ! empty($raw['updated_at']) ? (string) $raw['updated_at'] : null,
        ];
    }

    /**
     * Resolve active Media record if present and still valid on disk.
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
     * Save catalog metadata & optionally replace the single active PDF cleanly.
     */
    public static function saveCatalog(array $metadata, ?UploadedFile $newPdf = null): array
    {
        $current = static::getConfig();

        $next = array_merge($current, [
            'title_en' => trim((string) ($metadata['title_en'] ?? $current['title_en'])) ?: 'Partner Reward Catalog',
            'title_id' => trim((string) ($metadata['title_id'] ?? $current['title_id'])) ?: 'Katalog Reward Partner',
            'subtitle_en' => trim((string) ($metadata['subtitle_en'] ?? $current['subtitle_en'])),
            'subtitle_id' => trim((string) ($metadata['subtitle_id'] ?? $current['subtitle_id'])),
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

        Setting::set(self::SETTING_KEY, $next, 'partner-reward-catalog', 'json');

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
                $oldMedia->forceDelete();
            }
        }

        if ($path && $disk->exists($path)) {
            $disk->delete($path);
        }
    }
}
