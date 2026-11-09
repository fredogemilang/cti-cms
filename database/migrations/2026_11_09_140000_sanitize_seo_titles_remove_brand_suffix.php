<?php

use App\Console\Commands\SanitizeSeoTitles;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Run the SanitizeSeoTitles command logic directly
        $cleaner = function (?string $title): ?string {
            if (class_exists(SanitizeSeoTitles::class)) {
                return SanitizeSeoTitles::cleanTitle($title);
            }

            if ($title === null || trim($title) === '') {
                return $title;
            }

            $patterns = [
                '/\s*\|\s*Kouventa\s+Blog\s*$/i',
                '/\s*\|\s*Kouventa\s+Enterprise\s+AI\s*$/i',
                '/\s*\|\s*Kouventa\s+by\s+Helios\s*$/i',
                '/\s*\|\s*Kouventa\s+AI\s*$/i',
                '/\s*\|\s*Kouventa\s*$/i',
                '/\s*-\s*Kouventa\s+Blog\s*$/i',
                '/\s*-\s*Kouventa\s+AI\s*$/i',
                '/\s*-\s*Kouventa\s*$/i',
                '/\s*—\s*Kouventa\s*$/i',
            ];

            $cleaned = trim(preg_replace($patterns, '', trim($title)));

            return $cleaned !== '' ? $cleaned : trim($title);
        };

        // 1. seo_meta
        if (Schema::hasTable('seo_meta')) {
            $metas = DB::table('seo_meta')
                ->where(function ($q) {
                    $q->where('title', 'like', '%Kouventa%')
                      ->orWhere('og_title', 'like', '%Kouventa%');
                })
                ->get();

            foreach ($metas as $m) {
                $newTitle = $cleaner($m->title);
                $newOg = $cleaner($m->og_title);

                if ($newTitle !== $m->title || $newOg !== $m->og_title) {
                    DB::table('seo_meta')
                        ->where('id', $m->id)
                        ->update([
                            'title' => $newTitle,
                            'og_title' => $newOg,
                            'updated_at' => now(),
                        ]);
                }
            }
        }

        // 2. pages
        if (Schema::hasTable('pages')) {
            $pages = DB::table('pages')
                ->where(function ($q) {
                    $q->where('seo', 'like', '%Kouventa%')
                      ->orWhere('translations', 'like', '%Kouventa%');
                })
                ->get();

            foreach ($pages as $p) {
                $dirty = false;
                $seoData = is_string($p->seo) ? json_decode($p->seo, true) : $p->seo;
                $transData = is_string($p->translations) ? json_decode($p->translations, true) : $p->translations;

                if (is_array($seoData) && isset($seoData['title'])) {
                    $newTitle = $cleaner($seoData['title']);
                    if ($newTitle !== $seoData['title']) {
                        $seoData['title'] = $newTitle;
                        $dirty = true;
                    }
                }

                if (is_array($transData)) {
                    foreach ($transData as $loc => $locData) {
                        if (isset($locData['seo']['title'])) {
                            $newTitle = $cleaner($locData['seo']['title']);
                            if ($newTitle !== $locData['seo']['title']) {
                                $transData[$loc]['seo']['title'] = $newTitle;
                                $dirty = true;
                            }
                        }
                    }
                }

                if ($dirty) {
                    DB::table('pages')
                        ->where('id', $p->id)
                        ->update([
                            'seo' => json_encode($seoData),
                            'translations' => json_encode($transData),
                            'updated_at' => now(),
                        ]);
                }
            }
        }

        // 3. kouventa_pages
        if (Schema::hasTable('kouventa_pages')) {
            $kPages = DB::table('kouventa_pages')
                ->where(function ($q) {
                    $q->where('sections', 'like', '%Kouventa%')
                      ->orWhere('translations', 'like', '%Kouventa%');
                })
                ->get();

            foreach ($kPages as $kp) {
                $dirty = false;
                $sections = is_string($kp->sections) ? json_decode($kp->sections, true) : $kp->sections;
                $trans = is_string($kp->translations) ? json_decode($kp->translations, true) : $kp->translations;

                if (is_array($sections) && isset($sections['seo']['meta_title'])) {
                    $newTitle = $cleaner($sections['seo']['meta_title']);
                    if ($newTitle !== $sections['seo']['meta_title']) {
                        $sections['seo']['meta_title'] = $newTitle;
                        $dirty = true;
                    }
                }

                if (is_array($trans)) {
                    foreach ($trans as $loc => $locData) {
                        if (isset($locData['sections']['seo']['meta_title'])) {
                            $newTitle = $cleaner($locData['sections']['seo']['meta_title']);
                            if ($newTitle !== $locData['sections']['seo']['meta_title']) {
                                $trans[$loc]['sections']['seo']['meta_title'] = $newTitle;
                                $dirty = true;
                            }
                        }
                    }
                }

                if ($dirty) {
                    DB::table('kouventa_pages')
                        ->where('id', $kp->id)
                        ->update([
                            'sections' => json_encode($sections),
                            'translations' => json_encode($trans),
                            'updated_at' => now(),
                        ]);
                }
            }
        }

        // 4. posts
        if (Schema::hasTable('posts')) {
            $posts = DB::table('posts')
                ->where(function ($q) {
                    $q->where('meta', 'like', '%Kouventa%')
                      ->orWhere('translations', 'like', '%Kouventa%');
                })
                ->get();

            foreach ($posts as $post) {
                $dirty = false;
                $meta = is_string($post->meta) ? json_decode($post->meta, true) : $post->meta;
                $trans = is_string($post->translations) ? json_decode($post->translations, true) : $post->translations;

                if (is_array($meta)) {
                    if (isset($meta['meta_title'])) {
                        $newTitle = $cleaner($meta['meta_title']);
                        if ($newTitle !== $meta['meta_title']) {
                            $meta['meta_title'] = $newTitle;
                            $dirty = true;
                        }
                    }
                    if (isset($meta['og_title'])) {
                        $newTitle = $cleaner($meta['og_title']);
                        if ($newTitle !== $meta['og_title']) {
                            $meta['og_title'] = $newTitle;
                            $dirty = true;
                        }
                    }
                }

                if (is_array($trans)) {
                    foreach ($trans as $loc => $locData) {
                        if (isset($locData['meta']['meta_title'])) {
                            $newTitle = $cleaner($locData['meta']['meta_title']);
                            if ($newTitle !== $locData['meta']['meta_title']) {
                                $trans[$loc]['meta']['meta_title'] = $newTitle;
                                $dirty = true;
                            }
                        }
                    }
                }

                if ($dirty) {
                    DB::table('posts')
                        ->where('id', $post->id)
                        ->update([
                            'meta' => json_encode($meta),
                            'translations' => json_encode($trans),
                            'updated_at' => now(),
                        ]);
                }
            }
        }

        // 5. cpt_entries
        if (Schema::hasTable('cpt_entries')) {
            $entries = DB::table('cpt_entries')
                ->where(function ($q) {
                    $q->where('seo', 'like', '%Kouventa%')
                      ->orWhere('translations', 'like', '%Kouventa%');
                })
                ->get();

            foreach ($entries as $e) {
                $dirty = false;
                $seoData = is_string($e->seo) ? json_decode($e->seo, true) : $e->seo;
                $transData = is_string($e->translations) ? json_decode($e->translations, true) : $e->translations;

                if (is_array($seoData) && isset($seoData['title'])) {
                    $newTitle = $cleaner($seoData['title']);
                    if ($newTitle !== $seoData['title']) {
                        $seoData['title'] = $newTitle;
                        $dirty = true;
                    }
                }

                if (is_array($transData)) {
                    foreach ($transData as $loc => $locData) {
                        if (isset($locData['seo']['title'])) {
                            $newTitle = $cleaner($locData['seo']['title']);
                            if ($newTitle !== $locData['seo']['title']) {
                                $transData[$loc]['seo']['title'] = $newTitle;
                                $dirty = true;
                            }
                        }
                    }
                }

                if ($dirty) {
                    DB::table('cpt_entries')
                        ->where('id', $e->id)
                        ->update([
                            'seo' => json_encode($seoData),
                            'translations' => json_encode($transData),
                            'updated_at' => now(),
                        ]);
                }
            }
        }
    }

    public function down(): void
    {
        // One-way sanitization; rollback is not needed
    }
};
