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
        $homePage = DB::table('pages')->where('slug', 'home')->first();
        if (! $homePage) {
            return;
        }

        // 1. paths_title -> Mau mulai dari mana?
        $pathsBlock = DB::table('page_blocks')
            ->where('page_id', $homePage->id)
            ->where('name', 'paths_title')
            ->first();

        if ($pathsBlock) {
            $trans = is_string($pathsBlock->translations)
                ? (json_decode($pathsBlock->translations, true) ?: [])
                : (is_array($pathsBlock->translations) ? $pathsBlock->translations : []);

            $trans['id'] = [
                'value' => json_encode([
                    'prefix' => '',
                    'main' => 'Mau mulai dari mana?'
                ])
            ];

            DB::table('page_blocks')->where('id', $pathsBlock->id)->update([
                'translations' => json_encode($trans),
            ]);
        }

        // 2. why_title -> Siap untuk skala enterprise sejak hari pertama.
        $whyBlock = DB::table('page_blocks')
            ->where('page_id', $homePage->id)
            ->where('name', 'why_title')
            ->first();

        if ($whyBlock) {
            $trans = is_string($whyBlock->translations)
                ? (json_decode($whyBlock->translations, true) ?: [])
                : (is_array($whyBlock->translations) ? $whyBlock->translations : []);

            $trans['id'] = [
                'value' => json_encode([
                    'prefix' => 'Siap untuk skala enterprise',
                    'main' => 'sejak hari pertama.'
                ])
            ];

            DB::table('page_blocks')->where('id', $whyBlock->id)->update([
                'translations' => json_encode($trans),
            ]);
        }

        // 3. customers_title -> Mereka sudah percaya.
        $customersBlock = DB::table('page_blocks')
            ->where('page_id', $homePage->id)
            ->where('name', 'customers_title')
            ->first();

        if ($customersBlock) {
            $trans = is_string($customersBlock->translations)
                ? (json_decode($customersBlock->translations, true) ?: [])
                : (is_array($customersBlock->translations) ? $customersBlock->translations : []);

            $trans['id'] = [
                'value' => 'Mereka sudah percaya.'
            ];

            DB::table('page_blocks')->where('id', $customersBlock->id)->update([
                'translations' => json_encode($trans),
            ]);
        }

        // 4. cta_title -> Siap lihat langsung hasilnya untuk bisnis Anda?
        $ctaBlock = DB::table('page_blocks')
            ->where('page_id', $homePage->id)
            ->where('name', 'cta_title')
            ->first();

        if ($ctaBlock) {
            $trans = is_string($ctaBlock->translations)
                ? (json_decode($ctaBlock->translations, true) ?: [])
                : (is_array($ctaBlock->translations) ? $ctaBlock->translations : []);

            $trans['id'] = [
                'value' => 'Siap lihat langsung hasilnya untuk bisnis Anda?'
            ];

            DB::table('page_blocks')->where('id', $ctaBlock->id)->update([
                'translations' => json_encode($trans),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $homePage = DB::table('pages')->where('slug', 'home')->first();
        if (! $homePage) {
            return;
        }

        // Rollback paths_title
        $pathsBlock = DB::table('page_blocks')
            ->where('page_id', $homePage->id)
            ->where('name', 'paths_title')
            ->first();

        if ($pathsBlock) {
            $trans = is_string($pathsBlock->translations)
                ? (json_decode($pathsBlock->translations, true) ?: [])
                : (is_array($pathsBlock->translations) ? $pathsBlock->translations : []);

            $trans['id'] = [
                'value' => json_encode([
                    'prefix' => '',
                    'main' => 'Di mana Anda ingin memulai?'
                ])
            ];

            DB::table('page_blocks')->where('id', $pathsBlock->id)->update([
                'translations' => json_encode($trans),
            ]);
        }

        // Rollback customers_title
        $customersBlock = DB::table('page_blocks')
            ->where('page_id', $homePage->id)
            ->where('name', 'customers_title')
            ->first();

        if ($customersBlock) {
            $trans = is_string($customersBlock->translations)
                ? (json_decode($customersBlock->translations, true) ?: [])
                : (is_array($customersBlock->translations) ? $customersBlock->translations : []);

            $trans['id'] = [
                'value' => 'Mereka mempercayai kami.'
            ];

            DB::table('page_blocks')->where('id', $customersBlock->id)->update([
                'translations' => json_encode($trans),
            ]);
        }

        // Rollback cta_title
        $ctaBlock = DB::table('page_blocks')
            ->where('page_id', $homePage->id)
            ->where('name', 'cta_title')
            ->first();

        if ($ctaBlock) {
            $trans = is_string($ctaBlock->translations)
                ? (json_decode($ctaBlock->translations, true) ?: [])
                : (is_array($ctaBlock->translations) ? $ctaBlock->translations : []);

            $trans['id'] = [
                'value' => 'Siap mengubah cara bisnis Anda beroperasi dengan AI?'
            ];

            DB::table('page_blocks')->where('id', $ctaBlock->id)->update([
                'translations' => json_encode($trans),
            ]);
        }
    }
};
