<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use App\Models\StringTranslationKey;
use App\Models\StringTranslation;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update Page #8 (Insight -> Blog)
        DB::table('pages')
            ->where('slug', 'insight')
            ->orWhere('id', 8)
            ->update([
                'slug' => 'blog',
                'title' => 'Blog',
                'template' => 'blog',
                'translations' => json_encode([
                    'id' => [
                        'slug' => 'blog',
                        'title' => 'Blog',
                    ],
                ]),
                'seo' => json_encode([
                    'title' => 'Blog & Thought Leadership on Enterprise AI | Kouventa',
                    'keywords' => 'Kouventa blog, agentic AI insights, WhatsApp Business guide, enterprise AI trends',
                    'description' => 'Explore the latest articles, best practices, and case studies on Agentic AI, WhatsApp Business API, and enterprise digital transformation.',
                ]),
                'updated_at' => now(),
            ]);

        // 2. 301 Permanent Redirects (/insight -> /blog, /id/insight -> /id/blog)
        if (Schema::hasTable('redirects')) {
            DB::table('redirects')->updateOrInsert(
                ['from_path' => '/insight'],
                [
                    'to_url' => '/blog',
                    'status_code' => 301,
                    'is_active' => 1,
                    'is_regex' => 0,
                    'notes' => 'Permanent redirect /insight to /blog',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            DB::table('redirects')->updateOrInsert(
                ['from_path' => '/id/insight'],
                [
                    'to_url' => '/id/blog',
                    'status_code' => 301,
                    'is_active' => 1,
                    'is_regex' => 0,
                    'notes' => 'Permanent redirect /id/insight to /id/blog',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            Cache::forget('redirects:active');
        }

        // 3. String translations for Insight dropdown menu
        $strings = [
            'Blog' => [
                'en' => 'Blog',
                'id' => 'Blog',
            ],
            'About us' => [
                'en' => 'About us',
                'id' => 'Tentang kami',
            ],
            'Insight' => [
                'en' => 'Insight',
                'id' => 'Insight',
            ],
            'Thought leadership & AI articles' => [
                'en' => 'Thought leadership & AI articles',
                'id' => 'Artikel, pemikiran & wawasan AI terkini',
            ],
            'Our story, team & enterprise mission' => [
                'en' => 'Our story, team & enterprise mission',
                'id' => 'Kisah, tim, dan misi perusahaan kami',
            ],
            'View all articles' => [
                'en' => 'View all articles',
                'id' => 'Lihat semua artikel',
            ],
            'Back to Blog' => [
                'en' => 'Back to Blog',
                'id' => 'Kembali ke Blog',
            ],
        ];

        foreach ($strings as $keyName => $locales) {
            $keyModel = StringTranslationKey::firstOrCreate(
                ['group' => 'kouventa', 'key' => $keyName]
            );

            foreach ($locales as $locale => $val) {
                StringTranslation::updateOrCreate(
                    ['translation_key_id' => $keyModel->id, 'locale' => $locale],
                    ['value' => $val]
                );
            }
        }

        // 4. Update posts plugin archive settings to 'blog'
        if (class_exists(\Plugins\Posts\Models\Setting::class)) {
            \Plugins\Posts\Models\Setting::set('archive_slug', 'blog');
            \Plugins\Posts\Models\Setting::set('archive_slug_id', 'blog');
            \Plugins\Posts\Models\Setting::set('archive_title', 'Blog');
        }
        if (class_exists(\App\Models\Setting::class)) {
            \App\Models\Setting::set('permalink_post_base', 'blog');
        }

        // 5. Also redirect old /blog-news to /blog
        if (Schema::hasTable('redirects')) {
            DB::table('redirects')->updateOrInsert(
                ['from_path' => '/blog-news'],
                [
                    'to_url' => '/blog',
                    'status_code' => 301,
                    'is_active' => 1,
                    'is_regex' => 0,
                    'notes' => 'Permanent redirect /blog-news to /blog',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            DB::table('redirects')->updateOrInsert(
                ['from_path' => '/id/blog-news'],
                [
                    'to_url' => '/id/blog',
                    'status_code' => 301,
                    'is_active' => 1,
                    'is_regex' => 0,
                    'notes' => 'Permanent redirect /id/blog-news to /id/blog',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            Cache::forget('redirects:active');
        }
    }

    public function down(): void
    {
        DB::table('pages')
            ->where('slug', 'blog')
            ->orWhere('id', 8)
            ->update([
                'slug' => 'insight',
                'title' => 'Insight',
                'template' => 'insight',
                'translations' => json_encode([
                    'id' => [
                        'slug' => 'insight',
                        'title' => 'Insight',
                    ],
                ]),
                'updated_at' => now(),
            ]);

        if (Schema::hasTable('redirects')) {
            DB::table('redirects')->whereIn('from_path', ['/insight', '/id/insight'])->delete();
            Cache::forget('redirects:active');
        }
    }
};
