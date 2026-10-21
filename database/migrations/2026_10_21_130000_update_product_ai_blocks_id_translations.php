<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $page = DB::table('pages')->where('slug', 'product/ai')->first();
        if ($page) {
            // 1. hero_title
            $heroBlock = DB::table('page_blocks')->where('page_id', $page->id)->where('name', 'hero_title')->first();
            if ($heroBlock) {
                $trans = is_string($heroBlock->translations)
                    ? (json_decode($heroBlock->translations, true) ?: [])
                    : (is_array($heroBlock->translations) ? $heroBlock->translations : []);

                $trans['id'] = [
                    'value' => json_encode([
                        'prefix' => 'Satu platform AI',
                        'main' => 'sudah cukup.'
                    ])
                ];

                DB::table('page_blocks')->where('id', $heroBlock->id)->update([
                    'translations' => json_encode($trans),
                ]);
            }

            // 2. hero_subtitle
            $subBlock = DB::table('page_blocks')->where('page_id', $page->id)->where('name', 'hero_subtitle')->first();
            if ($subBlock) {
                $trans = is_string($subBlock->translations)
                    ? (json_decode($subBlock->translations, true) ?: [])
                    : (is_array($subBlock->translations) ? $subBlock->translations : []);

                $trans['id'] = [
                    'value' => 'Bangun AI agent dengan mudah dan gunakan untuk kebutuhan organisasi Anda, dengan kontrol yang terukur.'
                ];

                DB::table('page_blocks')->where('id', $subBlock->id)->update([
                    'translations' => json_encode($trans),
                ]);
            }

            // 3. challenges_title
            $chalBlock = DB::table('page_blocks')->where('page_id', $page->id)->where('name', 'challenges_title')->first();
            if ($chalBlock) {
                $trans = is_string($chalBlock->translations)
                    ? (json_decode($chalBlock->translations, true) ?: [])
                    : (is_array($chalBlock->translations) ? $chalBlock->translations : []);

                $trans['id'] = [
                    'value' => json_encode([
                        'prefix' => 'Terlalu banyak tools AI,',
                        'main' => 'terlalu sedikit kontrol.'
                    ])
                ];

                DB::table('page_blocks')->where('id', $chalBlock->id)->update([
                    'translations' => json_encode($trans),
                ]);
            }
        }

        // 4. string_translations
        $keys = [
            'Bring your AI together,' => 'Semua AI Anda di satu tempat,',
            'from answers to action.' => 'dari menjawab sampai bertindak.',
        ];

        foreach ($keys as $k => $idVal) {
            $keyRow = DB::table('string_translation_keys')->where('key', $k)->first();
            if (! $keyRow) {
                $keyId = DB::table('string_translation_keys')->insertGetId([
                    'group' => 'kouventa',
                    'key' => $k,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $keyId = $keyRow->id;
            }

            $transRow = DB::table('string_translations')
                ->where('translation_key_id', $keyId)
                ->where('locale', 'id')
                ->first();

            if ($transRow) {
                DB::table('string_translations')->where('id', $transRow->id)->update([
                    'value' => $idVal,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('string_translations')->insert([
                    'translation_key_id' => $keyId,
                    'locale' => 'id',
                    'value' => $idVal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
