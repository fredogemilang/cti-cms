<?php

namespace App\Services;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Media;
use App\Models\MetaField;
use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Scans content tables to figure out which Media rows are referenced anywhere
 * and which are orphans. Result is cached for a short TTL since the scan is
 * non-trivial.
 *
 * References checked:
 *   - pages.featured_image (path string)
 *   - pages.seo->'og_image'
 *   - page_blocks.value (media id for type='media', JSON array of ids for type='gallery')
 *   - cpt_entries.featured_image
 *   - cpt_entries.content (img src=… scan)
 *   - cpt_entries.meta (deep scan for media/gallery field references)
 *   - users.avatar (path string)
 */
class MediaUsageService
{
    protected const CACHE_KEY = 'media:usage-map';

    protected const CACHE_TTL = 300; // 5 minutes

    /**
     * Map of media_id => total references count.
     *
     * @return array<int, int>
     */
    public function usageMap(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $map = [];
            $bump = function (int|string|null $id) use (&$map) {
                if (! $id || ! is_numeric($id)) {
                    return;
                }
                $id = (int) $id;
                $map[$id] = ($map[$id] ?? 0) + 1;
            };

            // Build path → media id lookup; both `path` (original) and `webp_path` (companion)
            // can be referenced by content rows, so include both.
            $pathToId = [];
            foreach (Media::query()->get(['id', 'path', 'webp_path']) as $m) {
                if ($m->path) {
                    $pathToId[$m->path] = $m->id;
                }
                if ($m->webp_path) {
                    $pathToId[$m->webp_path] = $m->id;
                }
            }
            $resolveByPath = function ($v) use ($pathToId) {
                if (! is_string($v)) {
                    return null;
                }
                $clean = ltrim($v, '/');
                if (str_starts_with($clean, 'storage/')) {
                    $clean = substr($clean, 8);
                }

                return $pathToId[$clean] ?? $pathToId[ltrim($v, '/')] ?? $pathToId[$v] ?? null;
            };

            foreach (Page::select('featured_image', 'seo')->get() as $p) {
                $bump($resolveByPath($p->featured_image));
                if (is_array($p->seo) && ! empty($p->seo['og_image'])) {
                    $bump($resolveByPath($p->seo['og_image']));
                }
            }

            // PageBlock values
            foreach (PageBlock::select('type', 'value')->get() as $b) {
                $val = $b->value;
                if ($b->type === 'media') {
                    // value can be a media id or a path
                    if (is_numeric($val)) {
                        $bump($val);
                    } else {
                        $bump($resolveByPath($val));
                    }
                } elseif ($b->type === 'gallery') {
                    $arr = is_array($val) ? $val : (json_decode((string) $val, true) ?: []);
                    foreach ($arr as $v) {
                        is_numeric($v) ? $bump($v) : $bump($resolveByPath($v));
                    }
                }
            }

            // Map of post_type_id => [field_names that hold media references]
            $mediaFieldsByCpt = MetaField::query()
                ->where('fieldable_type', CustomPostType::class)
                ->whereIn('type', ['media', 'gallery', 'image'])
                ->get(['fieldable_id', 'name', 'type'])
                ->groupBy('fieldable_id')
                ->map(fn ($rows) => $rows->mapWithKeys(fn ($r) => [$r->name => $r->type])->all())
                ->all();

            // CptEntries
            foreach (CptEntry::select('id', 'post_type_id', 'featured_image', 'content', 'meta')->get() as $e) {
                $bump($resolveByPath($e->featured_image));

                // img src= references in HTML content
                if ($e->content) {
                    preg_match_all('/<img[^>]+src=("|\')([^"\']+)\1/i', $e->content, $m);
                    foreach ($m[2] ?? [] as $src) {
                        $bump($resolveByPath($src));
                    }
                }

                // Meta media fields — resolve by known schema or deep recursive scan
                if (is_array($e->meta)) {
                    $this->scanMetaForMediaReferences($e->meta, $bump, $resolveByPath);
                }
            }

            // Global Settings (site_logo, site_favicon, seo_default_og_image, etc.)
            foreach (DB::table('settings')->whereNotNull('value')->pluck('value') as $val) {
                if (is_string($val)) {
                    $json = json_decode($val, true);
                    if (is_array($json)) {
                        $this->scanMetaForMediaReferences($json, $bump, $resolveByPath);
                    } else {
                        is_numeric($val) ? $bump($val) : $bump($resolveByPath($val));
                    }
                }
            }

            // SeoMeta table (og_image_id)
            foreach (DB::table('seo_meta')->whereNotNull('og_image_id')->pluck('og_image_id') as $ogId) {
                $bump($ogId);
            }

            // User avatars (string path)
            foreach (DB::table('users')->whereNotNull('avatar')->pluck('avatar') as $av) {
                $bump($resolveByPath($av));
            }

            return $map;
        });
    }

    public function usageCount(int $mediaId): int
    {
        return $this->usageMap()[$mediaId] ?? 0;
    }

    /** Media row IDs that aren't referenced anywhere. */
    public function orphanIds(): array
    {
        $used = array_keys($this->usageMap());

        return Media::whereNotIn('id', $used)->pluck('id')->all();
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected function scanMetaForMediaReferences(array $meta, callable $bump, callable $resolveByPath): void
    {
        foreach ($meta as $key => $value) {
            if (is_array($value)) {
                $this->scanMetaForMediaReferences($value, $bump, $resolveByPath);
            } elseif (is_string($value) && ! empty($value)) {
                if (is_numeric($value)) {
                    $bump($value);
                } else {
                    $bump($resolveByPath($value));
                }
            } elseif (is_int($value)) {
                $bump($value);
            }
        }
    }
}
