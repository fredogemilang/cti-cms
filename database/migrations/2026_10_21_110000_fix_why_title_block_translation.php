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
        $block = DB::table('page_blocks')->where('name', 'why_title')->first();
        if ($block) {
            $trans = is_string($block->translations)
                ? (json_decode($block->translations, true) ?: [])
                : (is_array($block->translations) ? $block->translations : []);

            $trans['id'] = [
                'value' => json_encode([
                    'prefix' => 'Siap untuk skala enterprise',
                    'main' => 'sejak hari pertama.'
                ])
            ];

            DB::table('page_blocks')->where('id', $block->id)->update([
                'translations' => json_encode($trans),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
