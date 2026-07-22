<?php

use App\Models\SeoMeta;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migrate existing Posts JSON meta/translations SEO data into the centralized seo_meta table.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('posts')) {
            return;
        }

        $postClass = 'Plugins\\Posts\\Models\\Post';

        $rows = DB::table('posts')
            ->whereNotNull('meta')
            ->where('meta', '!=', '[]')
            ->where('meta', '!=', '{}')
            ->where('meta', '!=', 'null')
            ->get(['id', 'meta', 'translations']);

        foreach ($rows as $row) {
            $meta = is_string($row->meta) ? json_decode($row->meta, true) : $row->meta;
            if (empty($meta) || ! is_array($meta)) {
                continue;
            }

            // Default locale row
            $defaultData = array_filter([
                'title' => $meta['meta_title'] ?? null,
                'description' => $meta['meta_description'] ?? null,
                'og_title' => $meta['og_title'] ?? null,
                'og_description' => $meta['og_description'] ?? null,
            ], fn ($v) => $v !== null && $v !== '');

            if (! empty($defaultData)) {
                SeoMeta::updateOrCreate(
                    [
                        'seoable_type' => $postClass,
                        'seoable_id' => $row->id,
                        'locale' => '',
                    ],
                    $defaultData
                );
            }

            // Translated locale rows
            $translations = is_string($row->translations) ? json_decode($row->translations, true) : $row->translations;
            if (! is_array($translations)) {
                continue;
            }
            foreach ($translations as $locale => $fields) {
                $localeMeta = $fields['meta'] ?? [];
                if (empty($localeMeta) || ! is_array($localeMeta)) {
                    continue;
                }

                $localeData = array_filter([
                    'title' => $localeMeta['meta_title'] ?? null,
                    'description' => $localeMeta['meta_description'] ?? null,
                    'og_title' => $localeMeta['og_title'] ?? null,
                    'og_description' => $localeMeta['og_description'] ?? null,
                ], fn ($v) => $v !== null && $v !== '');

                if (! empty($localeData)) {
                    SeoMeta::updateOrCreate(
                        [
                            'seoable_type' => $postClass,
                            'seoable_id' => $row->id,
                            'locale' => $locale,
                        ],
                        $localeData
                    );
                }
            }
        }
    }

    public function down(): void
    {
        $postClass = 'Plugins\\Posts\\Models\\Post';
        DB::table('seo_meta')->where('seoable_type', $postClass)->delete();
    }
};
