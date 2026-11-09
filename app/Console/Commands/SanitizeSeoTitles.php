<?php

namespace App\Console\Commands;

use App\Models\SeoMeta;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SanitizeSeoTitles extends Command
{
    protected $signature = 'seo:sanitize-titles {--dry-run : Preview changes without updating the database}';
    protected $description = 'Sanitize SEO titles across seo_meta, pages, kouventa_pages, posts, and cpt_entries by removing brand suffixes like | Kouventa, | Kouventa Blog, etc.';

    public static function cleanTitle(?string $title): ?string
    {
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
    }

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $this->info($isDryRun ? '--- DRY RUN: Previewing SEO Title Sanitization ---' : '--- Running SEO Title Sanitization ---');

        $totalUpdated = 0;

        // 1. Sanitize seo_meta table
        if (Schema::hasTable('seo_meta')) {
            $metas = DB::table('seo_meta')
                ->where(function ($q) {
                    $q->where('title', 'like', '%Kouventa%')
                      ->orWhere('og_title', 'like', '%Kouventa%');
                })
                ->get();

            $metaUpdated = 0;
            foreach ($metas as $m) {
                $newTitle = self::cleanTitle($m->title);
                $newOg = self::cleanTitle($m->og_title);

                if ($newTitle !== $m->title || $newOg !== $m->og_title) {
                    $this->line("seo_meta #{$m->id} [{$m->seoable_type} #{$m->seoable_id}]:");
                    if ($newTitle !== $m->title) {
                        $this->line("  title: '{$m->title}' -> '{$newTitle}'");
                    }
                    if ($newOg !== $m->og_title) {
                        $this->line("  og_title: '{$m->og_title}' -> '{$newOg}'");
                    }

                    if (! $isDryRun) {
                        DB::table('seo_meta')
                            ->where('id', $m->id)
                            ->update([
                                'title' => $newTitle,
                                'og_title' => $newOg,
                                'updated_at' => now(),
                            ]);
                    }
                    $metaUpdated++;
                }
            }
            $this->info("seo_meta: {$metaUpdated} rows " . ($isDryRun ? 'would be updated' : 'updated'));
            $totalUpdated += $metaUpdated;
        }

        // 2. Sanitize pages table (seo JSON & translations JSON)
        if (Schema::hasTable('pages')) {
            $pages = DB::table('pages')
                ->where(function ($q) {
                    $q->where('seo', 'like', '%Kouventa%')
                      ->orWhere('translations', 'like', '%Kouventa%');
                })
                ->get();

            $pageUpdated = 0;
            foreach ($pages as $p) {
                $dirty = false;
                $seoData = is_string($p->seo) ? json_decode($p->seo, true) : $p->seo;
                $transData = is_string($p->translations) ? json_decode($p->translations, true) : $p->translations;

                if (is_array($seoData) && isset($seoData['title'])) {
                    $newTitle = self::cleanTitle($seoData['title']);
                    if ($newTitle !== $seoData['title']) {
                        $this->line("page #{$p->id} ({$p->slug}) seo.title: '{$seoData['title']}' -> '{$newTitle}'");
                        $seoData['title'] = $newTitle;
                        $dirty = true;
                    }
                }

                if (is_array($transData)) {
                    foreach ($transData as $loc => $locData) {
                        if (isset($locData['seo']['title'])) {
                            $newTitle = self::cleanTitle($locData['seo']['title']);
                            if ($newTitle !== $locData['seo']['title']) {
                                $this->line("page #{$p->id} ({$p->slug}) trans.{$loc}.seo.title: '{$locData['seo']['title']}' -> '{$newTitle}'");
                                $transData[$loc]['seo']['title'] = $newTitle;
                                $dirty = true;
                            }
                        }
                    }
                }

                if ($dirty) {
                    if (! $isDryRun) {
                        DB::table('pages')
                            ->where('id', $p->id)
                            ->update([
                                'seo' => json_encode($seoData),
                                'translations' => json_encode($transData),
                                'updated_at' => now(),
                            ]);
                    }
                    $pageUpdated++;
                }
            }
            $this->info("pages: {$pageUpdated} rows " . ($isDryRun ? 'would be updated' : 'updated'));
            $totalUpdated += $pageUpdated;
        }

        // 3. Sanitize kouventa_pages table (sections JSON & translations JSON)
        if (Schema::hasTable('kouventa_pages')) {
            $kPages = DB::table('kouventa_pages')
                ->where(function ($q) {
                    $q->where('sections', 'like', '%Kouventa%')
                      ->orWhere('translations', 'like', '%Kouventa%');
                })
                ->get();

            $kPageUpdated = 0;
            foreach ($kPages as $kp) {
                $dirty = false;
                $sections = is_string($kp->sections) ? json_decode($kp->sections, true) : $kp->sections;
                $trans = is_string($kp->translations) ? json_decode($kp->translations, true) : $kp->translations;

                if (is_array($sections) && isset($sections['seo']['meta_title'])) {
                    $newTitle = self::cleanTitle($sections['seo']['meta_title']);
                    if ($newTitle !== $sections['seo']['meta_title']) {
                        $this->line("kouventa_page #{$kp->id} ({$kp->slug}) sections.seo.meta_title: '{$sections['seo']['meta_title']}' -> '{$newTitle}'");
                        $sections['seo']['meta_title'] = $newTitle;
                        $dirty = true;
                    }
                }

                if (is_array($trans)) {
                    foreach ($trans as $loc => $locData) {
                        if (isset($locData['sections']['seo']['meta_title'])) {
                            $newTitle = self::cleanTitle($locData['sections']['seo']['meta_title']);
                            if ($newTitle !== $locData['sections']['seo']['meta_title']) {
                                $this->line("kouventa_page #{$kp->id} ({$kp->slug}) trans.{$loc}.sections.seo.meta_title: '{$locData['sections']['seo']['meta_title']}' -> '{$newTitle}'");
                                $trans[$loc]['sections']['seo']['meta_title'] = $newTitle;
                                $dirty = true;
                            }
                        }
                    }
                }

                if ($dirty) {
                    if (! $isDryRun) {
                        DB::table('kouventa_pages')
                            ->where('id', $kp->id)
                            ->update([
                                'sections' => json_encode($sections),
                                'translations' => json_encode($trans),
                                'updated_at' => now(),
                            ]);
                    }
                    $kPageUpdated++;
                }
            }
            $this->info("kouventa_pages: {$kPageUpdated} rows " . ($isDryRun ? 'would be updated' : 'updated'));
            $totalUpdated += $kPageUpdated;
        }

        // 4. Sanitize posts table (meta JSON & translations JSON)
        if (Schema::hasTable('posts')) {
            $posts = DB::table('posts')
                ->where(function ($q) {
                    $q->where('meta', 'like', '%Kouventa%')
                      ->orWhere('translations', 'like', '%Kouventa%');
                })
                ->get();

            $postUpdated = 0;
            foreach ($posts as $post) {
                $dirty = false;
                $meta = is_string($post->meta) ? json_decode($post->meta, true) : $post->meta;
                $trans = is_string($post->translations) ? json_decode($post->translations, true) : $post->translations;

                if (is_array($meta)) {
                    if (isset($meta['meta_title'])) {
                        $newTitle = self::cleanTitle($meta['meta_title']);
                        if ($newTitle !== $meta['meta_title']) {
                            $this->line("post #{$post->id} ({$post->slug}) meta.meta_title: '{$meta['meta_title']}' -> '{$newTitle}'");
                            $meta['meta_title'] = $newTitle;
                            $dirty = true;
                        }
                    }
                    if (isset($meta['og_title'])) {
                        $newTitle = self::cleanTitle($meta['og_title']);
                        if ($newTitle !== $meta['og_title']) {
                            $this->line("post #{$post->id} ({$post->slug}) meta.og_title: '{$meta['og_title']}' -> '{$newTitle}'");
                            $meta['og_title'] = $newTitle;
                            $dirty = true;
                        }
                    }
                }

                if (is_array($trans)) {
                    foreach ($trans as $loc => $locData) {
                        if (isset($locData['meta']['meta_title'])) {
                            $newTitle = self::cleanTitle($locData['meta']['meta_title']);
                            if ($newTitle !== $locData['meta']['meta_title']) {
                                $this->line("post #{$post->id} ({$post->slug}) trans.{$loc}.meta.meta_title: '{$locData['meta']['meta_title']}' -> '{$newTitle}'");
                                $trans[$loc]['meta']['meta_title'] = $newTitle;
                                $dirty = true;
                            }
                        }
                    }
                }

                if ($dirty) {
                    if (! $isDryRun) {
                        DB::table('posts')
                            ->where('id', $post->id)
                            ->update([
                                'meta' => json_encode($meta),
                                'translations' => json_encode($trans),
                                'updated_at' => now(),
                            ]);
                    }
                    $postUpdated++;
                }
            }
            $this->info("posts: {$postUpdated} rows " . ($isDryRun ? 'would be updated' : 'updated'));
            $totalUpdated += $postUpdated;
        }

        // 5. Sanitize cpt_entries table
        if (Schema::hasTable('cpt_entries')) {
            $entries = DB::table('cpt_entries')
                ->where(function ($q) {
                    $q->where('seo', 'like', '%Kouventa%')
                      ->orWhere('translations', 'like', '%Kouventa%');
                })
                ->get();

            $entryUpdated = 0;
            foreach ($entries as $e) {
                $dirty = false;
                $seoData = is_string($e->seo) ? json_decode($e->seo, true) : $e->seo;
                $transData = is_string($e->translations) ? json_decode($e->translations, true) : $e->translations;

                if (is_array($seoData) && isset($seoData['title'])) {
                    $newTitle = self::cleanTitle($seoData['title']);
                    if ($newTitle !== $seoData['title']) {
                        $this->line("cpt_entry #{$e->id} ({$e->slug}) seo.title: '{$seoData['title']}' -> '{$newTitle}'");
                        $seoData['title'] = $newTitle;
                        $dirty = true;
                    }
                }

                if (is_array($transData)) {
                    foreach ($transData as $loc => $locData) {
                        if (isset($locData['seo']['title'])) {
                            $newTitle = self::cleanTitle($locData['seo']['title']);
                            if ($newTitle !== $locData['seo']['title']) {
                                $this->line("cpt_entry #{$e->id} ({$e->slug}) trans.{$loc}.seo.title: '{$locData['seo']['title']}' -> '{$newTitle}'");
                                $transData[$loc]['seo']['title'] = $newTitle;
                                $dirty = true;
                            }
                        }
                    }
                }

                if ($dirty) {
                    if (! $isDryRun) {
                        DB::table('cpt_entries')
                            ->where('id', $e->id)
                            ->update([
                                'seo' => json_encode($seoData),
                                'translations' => json_encode($transData),
                                'updated_at' => now(),
                            ]);
                    }
                    $entryUpdated++;
                }
            }
            $this->info("cpt_entries: {$entryUpdated} rows " . ($isDryRun ? 'would be updated' : 'updated'));
            $totalUpdated += $entryUpdated;
        }

        $this->info("Total affected records: {$totalUpdated}");

        return Command::SUCCESS;
    }
}
