<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('kouventa_videos')) {
            Schema::create('kouventa_videos', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('youtube_id', 32)->index();
                $table->string('youtube_url', 500);
                $table->string('category', 100)->default('Artificial Intelligence')->index();
                $table->string('thumbnail', 500)->nullable();
                $table->integer('sort_order')->default(0)->index();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
        }

        // Seed initial default videos if table is empty
        try {
            if (Schema::hasTable('kouventa_videos') && DB::table('kouventa_videos')->count() === 0) {
                $defaultVideos = [
                    [
                        'youtube_id' => 'oOY1BnbQPUc',
                        'youtube_url' => 'https://www.youtube.com/watch?v=oOY1BnbQPUc',
                        'title' => 'Tim Finance Masih Perlu Workflow Manual di Era Agentic AI?',
                        'category' => 'Artificial Intelligence',
                        'thumbnail' => null,
                        'sort_order' => 1,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'youtube_id' => 'N76UFyc7v9s',
                        'youtube_url' => 'https://www.youtube.com/watch?v=N76UFyc7v9s',
                        'title' => 'Kouventa Agentic AI | A New Generation of AI for Business',
                        'category' => 'Artificial Intelligence',
                        'thumbnail' => null,
                        'sort_order' => 2,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'youtube_id' => '6_6n4erRFXQ',
                        'youtube_url' => 'https://www.youtube.com/watch?v=6_6n4erRFXQ',
                        'title' => 'Cara Mendapatkan Centang Biru di WhatsApp untuk Akun Bisnis!',
                        'category' => 'Customer Engagement',
                        'thumbnail' => null,
                        'sort_order' => 3,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'youtube_id' => 'w1OtL8yThnc',
                        'youtube_url' => 'https://www.youtube.com/watch?v=w1OtL8yThnc',
                        'title' => 'Stop Kirim Pesan Manual! Ini Cara Upgrade CRM Otomatis Tanpa Ribet',
                        'category' => 'Customer Engagement',
                        'thumbnail' => null,
                        'sort_order' => 4,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'youtube_id' => '9lcV7QXol_g',
                        'youtube_url' => 'https://www.youtube.com/watch?v=9lcV7QXol_g',
                        'title' => '5 MASALAH CUSTOMER SERVICE WHATSAPP | Live Chat Agent',
                        'category' => 'Customer Engagement',
                        'thumbnail' => null,
                        'sort_order' => 5,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'youtube_id' => 'O0E_XfPl5CE',
                        'youtube_url' => 'https://www.youtube.com/watch?v=O0E_XfPl5CE',
                        'title' => 'Evolusi Cara Bisnis Melayani Pelanggan: Kenapa Omnichannel Jadi Penting?',
                        'category' => 'Customer Engagement',
                        'thumbnail' => null,
                        'sort_order' => 6,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'youtube_id' => '1xvLiyz3zJk',
                        'youtube_url' => 'https://www.youtube.com/watch?v=1xvLiyz3zJk',
                        'title' => 'RAHASIA CS NGGAK KEWALAHAN SAAT ACARA KANTOR!',
                        'category' => 'Customer Engagement',
                        'thumbnail' => null,
                        'sort_order' => 7,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'youtube_id' => 'DbQ0ZBY_XSs',
                        'youtube_url' => 'https://www.youtube.com/watch?v=DbQ0ZBY_XSs',
                        'title' => 'Tim HR/IT Pusing Ticketing? Ini Cara Tracking Tanpa Ribet!',
                        'category' => 'Artificial Intelligence',
                        'thumbnail' => null,
                        'sort_order' => 8,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'youtube_id' => '21WZzU5uou4',
                        'youtube_url' => 'https://www.youtube.com/watch?v=21WZzU5uou4',
                        'title' => 'STOP Pakai WhatsApp Biasa untuk Bisnis! Ini Alasannya',
                        'category' => 'Customer Engagement',
                        'thumbnail' => null,
                        'sort_order' => 9,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'youtube_id' => 'UbcQOWUxJHI',
                        'youtube_url' => 'https://www.youtube.com/watch?v=UbcQOWUxJHI',
                        'title' => 'JANGAN SEMBARANGAN INPUT DATA KE AI! Data Kantormu Bisa Bocor',
                        'category' => 'Artificial Intelligence',
                        'thumbnail' => null,
                        'sort_order' => 10,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ];

                DB::table('kouventa_videos')->insert($defaultVideos);
            }
        } catch (\Throwable $e) {
            // Ignore seeding errors during migration
        }

        // Update custom admin sidebar order if stored in settings
        try {
            if (Schema::hasTable('settings')) {
                $customOrder = Setting::get('admin_sidebar_custom_order', []);
                if (! empty($customOrder) && is_array($customOrder)) {
                    $key = 'plugin:kouventa-videos';
                    if (! in_array($key, $customOrder, true)) {
                        $updatedOrder = [];
                        $inserted = false;

                        foreach ($customOrder as $item) {
                            $updatedOrder[] = $item;
                            if ($item === 'plugin:kouventa-partners') {
                                $updatedOrder[] = $key;
                                $inserted = true;
                            }
                        }

                        if (! $inserted) {
                            $updatedOrder[] = $key;
                        }

                        Setting::set('admin_sidebar_custom_order', array_values($updatedOrder));
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore setting update errors during migration
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kouventa_videos');
    }
};
