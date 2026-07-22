<?php

use App\Models\CptEntry;
use App\Models\Page;
use App\Models\SeoMeta;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Add locale column to seo_meta for multi-locale SEO support,
 * and migrate existing JSON seo data from pages/cpt_entries tables.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Add locale column and update unique constraint
        Schema::table('seo_meta', function (Blueprint $table) {
            $table->string('locale', 10)->default('')->after('seoable_id')
                ->comment('Empty string = default locale. Otherwise ISO locale code.');
        });

        // Drop old unique and create new composite unique
        Schema::table('seo_meta', function (Blueprint $table) {
            $table->dropUnique('uq_seoable');
            $table->unique(['seoable_type', 'seoable_id', 'locale'], 'uq_seoable_locale');
        });

        // 2. Migrate existing Page JSON seo data → seo_meta rows
        $this->migrateJsonSeo(Page::class, 'pages');

        // 3. Migrate existing CptEntry JSON seo data → seo_meta rows
        $this->migrateJsonSeo(CptEntry::class, 'cpt_entries');
    }

    protected function migrateJsonSeo(string $modelClass, string $tableName): void
    {
        $rows = DB::table($tableName)
            ->whereNotNull('seo')
            ->where('seo', '!=', '[]')
            ->where('seo', '!=', '{}')
            ->where('seo', '!=', 'null')
            ->get(['id', 'seo', 'translations']);

        foreach ($rows as $row) {
            $seo = is_string($row->seo) ? json_decode($row->seo, true) : $row->seo;
            if (empty($seo) || ! is_array($seo)) {
                continue;
            }

            // Default locale row
            SeoMeta::updateOrCreate(
                [
                    'seoable_type' => $modelClass,
                    'seoable_id' => $row->id,
                    'locale' => '',
                ],
                array_filter([
                    'title' => $seo['meta_title'] ?? null,
                    'description' => $seo['meta_description'] ?? null,
                    'og_title' => $seo['og_title'] ?? null,
                    'og_description' => $seo['og_description'] ?? null,
                ], fn ($v) => $v !== null && $v !== '')
            );

            // Translated locale rows
            $translations = is_string($row->translations) ? json_decode($row->translations, true) : $row->translations;
            if (! is_array($translations)) {
                continue;
            }
            foreach ($translations as $locale => $fields) {
                $localeSeo = $fields['seo'] ?? [];
                if (empty($localeSeo) || ! is_array($localeSeo)) {
                    continue;
                }

                $localeData = array_filter([
                    'title' => $localeSeo['meta_title'] ?? null,
                    'description' => $localeSeo['meta_description'] ?? null,
                    'og_title' => $localeSeo['og_title'] ?? null,
                    'og_description' => $localeSeo['og_description'] ?? null,
                ], fn ($v) => $v !== null && $v !== '');

                if (! empty($localeData)) {
                    SeoMeta::updateOrCreate(
                        [
                            'seoable_type' => $modelClass,
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
        // Remove locale-specific rows (keep only default)
        DB::table('seo_meta')->where('locale', '!=', '')->delete();

        Schema::table('seo_meta', function (Blueprint $table) {
            $table->dropUnique('uq_seoable_locale');
            $table->dropColumn('locale');
            $table->unique(['seoable_type', 'seoable_id'], 'uq_seoable');
        });
    }
};
