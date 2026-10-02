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
use Plugins\Posts\Models\Post;

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

            $pathToId = [];
            foreach (Media::query()->get(['id', 'path', 'webp_path', 'original_filename', 'filename']) as $m) {
                if ($m->path) {
                    $pathToId[$m->path] = $m->id;
                    $pathToId[basename($m->path)] = $m->id;
                }
                if ($m->webp_path) {
                    $pathToId[$m->webp_path] = $m->id;
                    $pathToId[basename($m->webp_path)] = $m->id;
                }
                if ($m->filename) {
                    $pathToId[$m->filename] = $m->id;
                }
                if ($m->original_filename) {
                    $pathToId[$m->original_filename] = $m->id;
                }
            }
            $resolveByPath = function ($v) use (&$pathToId) {
                if (! is_string($v) || empty($v)) {
                    return null;
                }
                $path = parse_url($v, PHP_URL_PATH) ?? $v;
                $clean = ltrim($path, '/');
                if (str_starts_with($clean, 'storage/')) {
                    $clean = substr($clean, 8);
                }

                $filename = basename($clean);

                return $pathToId[$clean] ?? $pathToId[ltrim($path, '/')] ?? $pathToId[$filename] ?? $pathToId[$v] ?? null;
            };

            foreach (Page::select('featured_image', 'seo')->get() as $p) {
                $bump($resolveByPath($p->featured_image));
                if (is_array($p->seo) && ! empty($p->seo['og_image'])) {
                    $bump($resolveByPath($p->seo['og_image']));
                }
            }

            // PageBlock values — scan all block types (media, gallery, card, repeater, wysiwyg, etc.)
            foreach (PageBlock::select('type', 'value')->get() as $b) {
                $val = $b->value;
                if (is_array($val)) {
                    $this->scanMetaForMediaReferences($val, $bump, $resolveByPath);
                } elseif (is_numeric($val)) {
                    $bump($val);
                } elseif (is_string($val) && ! empty($val)) {
                    $decoded = json_decode($val, true);
                    if (is_array($decoded)) {
                        $this->scanMetaForMediaReferences($decoded, $bump, $resolveByPath);
                    } else {
                        $bump($resolveByPath($val));
                        if (str_contains($val, '<img')) {
                            preg_match_all('/<img[^>]+src=("|\')([^"\']+)\1/i', $val, $m);
                            foreach ($m[2] ?? [] as $src) {
                                $bump($resolveByPath($src));
                            }
                        }
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

            // Post plugin posts (featured_image & content HTML img src, including translations)
            if (class_exists(Post::class)) {
                foreach (Post::all() as $post) {
                    $bump($resolveByPath($post->featured_image));

                    // Scan content for <img src="..."> in default locale & all translations
                    $contentTranslations = method_exists($post, 'getTranslations')
                        ? $post->getTranslations('content')
                        : [$post->content];

                    if (! is_array($contentTranslations)) {
                        $contentTranslations = [$post->content];
                    }

                    foreach ($contentTranslations as $htmlContent) {
                        if (is_string($htmlContent) && ! empty($htmlContent)) {
                            preg_match_all('/<img[^>]+src=("|\')([^"\']+)\1/i', $htmlContent, $m);
                            if (! empty($m[2])) {
                                foreach ($m[2] as $src) {
                                    $bump($resolveByPath($src));
                                }
                            }
                        }
                    }
                }
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

    /**
     * Get detailed list of locations (Pages, CPT Entries, Posts, Settings) referencing this media item.
     *
     * @return array<int, array{type: string, title: string, edit_url: ?string, public_url: ?string, context: string, icon: string, color: string}>
     */
    public function getUsagesForMedia(Media $media): array
    {
        $locations = [];
        $mediaId = (int) $media->id;
        $paths = array_values(array_filter([
            $media->path,
            $media->webp_path,
            $media->filename ? 'media/'.$media->filename : null,
            $media->original_filename ? 'media/'.$media->original_filename : null,
        ]));

        $matchesValue = function ($val) use ($mediaId, $paths) {
            if (empty($val)) {
                return false;
            }
            if (is_numeric($val) && (int) $val === $mediaId) {
                return true;
            }
            if (is_string($val)) {
                foreach ($paths as $p) {
                    if ($p && (str_contains($val, $p) || str_contains($val, basename($p)))) {
                        return true;
                    }
                }
            }

            return false;
        };

        // 1. Pages & PageBlocks
        $pages = Page::all();
        foreach ($pages as $page) {
            $foundContext = null;
            if ($matchesValue($page->featured_image)) {
                $foundContext = 'Featured Image';
            } elseif (is_array($page->seo) && ! empty($page->seo['og_image']) && $matchesValue($page->seo['og_image'])) {
                $foundContext = 'SEO OG Image';
            } else {
                $blocks = PageBlock::where('page_id', $page->id)->get();
                foreach ($blocks as $block) {
                    $val = $block->value;
                    if (is_array($val) && $this->metaContainsMedia($val, $matchesValue)) {
                        $foundContext = 'Block: '.ucfirst($block->name ?? $block->type);
                        break;
                    } elseif (is_string($val) && ! empty($val)) {
                        $decoded = json_decode($val, true);
                        if (is_array($decoded) && $this->metaContainsMedia($decoded, $matchesValue)) {
                            $foundContext = 'Block: '.ucfirst($block->name ?? $block->type);
                            break;
                        } elseif ($matchesValue($val)) {
                            $foundContext = 'Block: '.ucfirst($block->name ?? $block->type);
                            break;
                        }
                    } elseif ($matchesValue($val)) {
                        $foundContext = 'Block: '.ucfirst($block->name ?? $block->type);
                        break;
                    }
                }
            }

            if ($foundContext) {
                $locations[] = [
                    'type' => 'Page',
                    'title' => $page->title ?: 'Page #'.$page->id,
                    'edit_url' => route('admin.pages.edit', $page->id),
                    'public_url' => url($page->slug ?: '/'),
                    'context' => $foundContext,
                    'icon' => 'description',
                    'color' => 'blue',
                ];
            }
        }

        // 2. CPT Entries
        $cptEntries = CptEntry::with('postType')->get();
        foreach ($cptEntries as $entry) {
            $foundContext = null;
            if ($matchesValue($entry->featured_image)) {
                $foundContext = 'Featured Image';
            } elseif (! empty($entry->content) && $matchesValue($entry->content)) {
                $foundContext = 'Content Body';
            } elseif (is_array($entry->meta) && $this->metaContainsMedia($entry->meta, $matchesValue)) {
                $foundContext = 'Custom Field';
            }

            if ($foundContext) {
                $postTypeName = $entry->postType->singular_label ?? 'CPT';
                $cptSlug = $entry->postType->slug ?? 'cpt';
                $editUrl = route('admin.cpt.entries.edit', [$cptSlug, $entry->id]);
                $publicUrl = method_exists($entry, 'getUrl') ? $entry->getUrl() : null;

                $locations[] = [
                    'type' => $postTypeName,
                    'title' => $entry->title ?: 'Entry #'.$entry->id,
                    'edit_url' => $editUrl,
                    'public_url' => $publicUrl,
                    'context' => $foundContext,
                    'icon' => 'widgets',
                    'color' => 'purple',
                ];
            }
        }

        // 3. Blog Posts
        if (class_exists(Post::class)) {
            $posts = Post::all();
            foreach ($posts as $post) {
                $foundContext = null;
                if ($matchesValue($post->featured_image)) {
                    $foundContext = 'Featured Image';
                } elseif (! empty($post->content) && $matchesValue($post->content)) {
                    $foundContext = 'Content Body';
                }

                if ($foundContext) {
                    $locations[] = [
                        'type' => 'Blog Post',
                        'title' => $post->title ?: 'Post #'.$post->id,
                        'edit_url' => route('admin.posts.edit', $post->id),
                        'public_url' => method_exists($post, 'getUrl') ? $post->getUrl() : null,
                        'context' => $foundContext,
                        'icon' => 'article',
                        'color' => 'amber',
                    ];
                }
            }
        }

        // 4. Global Settings
        $settings = DB::table('settings')->whereNotNull('value')->get();
        foreach ($settings as $setting) {
            if ($matchesValue($setting->value)) {
                $settingName = str_replace('_', ' ', ucfirst($setting->key));
                $locations[] = [
                    'type' => 'Setting',
                    'title' => $settingName,
                    'edit_url' => url('/ctrlpanel/settings/general'),
                    'public_url' => null,
                    'context' => 'Global Setting',
                    'icon' => 'settings',
                    'color' => 'green',
                ];
            }
        }

        return $locations;
    }

    protected function metaContainsMedia(array $meta, callable $matchesValue): bool
    {
        foreach ($meta as $val) {
            if (is_array($val)) {
                if ($this->metaContainsMedia($val, $matchesValue)) {
                    return true;
                }
            } elseif ($matchesValue($val)) {
                return true;
            }
        }

        return false;
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
