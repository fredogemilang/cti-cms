<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $fixes = [
            'no_posts_title' => [
                'group' => 'blog',
                'en' => 'No Articles Found',
                'id' => 'Artikel Tidak Ditemukan',
            ],
            'no_posts_desc' => [
                'group' => 'blog',
                'en' => 'Sorry, no articles matched your filter or search criteria. Please try another keyword or reset filters.',
                'id' => 'Maaf, tidak ada artikel yang sesuai dengan filter atau pencarian Anda. Silakan coba kata kunci lain atau reset filter.',
            ],
            'reset_filters' => [
                'group' => 'blog',
                'en' => 'Reset Filters',
                'id' => 'Reset Filter',
            ],
            'you_might_also_like_desc' => [
                'group' => 'blog',
                'en' => 'Top recommended articles from our industry experts.',
                'id' => 'Rekomendasi artikel terbaik dari pakar industri kami.',
            ],
        ];

        foreach ($fixes as $key => $data) {
            $keyRow = DB::table('string_translation_keys')
                ->where('group', $data['group'])
                ->where('key', $key)
                ->first();

            if ($keyRow) {
                DB::table('string_translation_keys')
                    ->where('id', $keyRow->id)
                    ->update(['default_value' => $data['en']]);

                DB::table('string_translations')->updateOrInsert(
                    ['translation_key_id' => $keyRow->id, 'locale' => 'en'],
                    ['value' => $data['en'], 'updated_at' => now()]
                );

                DB::table('string_translations')->updateOrInsert(
                    ['translation_key_id' => $keyRow->id, 'locale' => 'id'],
                    ['value' => $data['id'], 'updated_at' => now()]
                );
            }
        }
    }

    public function down(): void
    {
        // No-op
    }
};
