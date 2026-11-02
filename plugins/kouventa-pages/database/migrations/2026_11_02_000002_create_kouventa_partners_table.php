<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('kouventa_partners')) {
            Schema::create('kouventa_partners', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('logo');
                $table->string('website_url')->nullable();
                $table->string('alt_text')->nullable();
                $table->unsignedSmallInteger('max_height')->default(44);
                $table->integer('sort_order')->default(0)->index();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
        }

        // Seed initial technology partners if empty
        try {
            if (Schema::hasTable('kouventa_partners') && \Illuminate\Support\Facades\DB::table('kouventa_partners')->count() === 0) {
                $defaultPartners = [
                    [
                        'name' => 'Meta',
                        'logo' => 'themes/kouventa/assets/images/logos/techonology-partners/meta.webp',
                        'website_url' => null,
                        'alt_text' => 'Logo Meta - Official WhatsApp Business Solution Provider Mitra Kouventa',
                        'max_height' => 44,
                        'sort_order' => 1,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name' => 'Amazon Web Services',
                        'logo' => 'themes/kouventa/assets/images/logos/techonology-partners/aws.webp',
                        'website_url' => null,
                        'alt_text' => 'Logo AWS Amazon Web Services - Cloud Infrastructure Partner Kouventa AI',
                        'max_height' => 44,
                        'sort_order' => 2,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name' => 'Microsoft Foundry',
                        'logo' => 'themes/kouventa/assets/images/logos/techonology-partners/microsoft-foundry.webp',
                        'website_url' => null,
                        'alt_text' => 'Logo Microsoft Foundry - AI Technology Partner Kouventa',
                        'max_height' => 44,
                        'sort_order' => 3,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name' => 'Lintasarta Cloudeka',
                        'logo' => 'themes/kouventa/assets/images/logos/techonology-partners/deka-llm.webp',
                        'website_url' => null,
                        'alt_text' => 'Logo Lintasarta Cloudeka Deka LLM - Mitra Komputasi Sovereign AI Cloud Kouventa',
                        'max_height' => 44,
                        'sort_order' => 4,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name' => 'Motadata',
                        'logo' => 'themes/kouventa/assets/images/logos/techonology-partners/motadata.webp',
                        'website_url' => null,
                        'alt_text' => 'Logo Motadata - Mitra Solusi Monitoring & Ticketing Enterprise Kouventa',
                        'max_height' => 44,
                        'sort_order' => 5,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name' => 'TokenKu.ai',
                        'logo' => 'themes/kouventa/assets/images/logos/techonology-partners/tokeknku.webp',
                        'website_url' => null,
                        'alt_text' => 'Logo TokenKu - Platform Manajemen Token & LLM AI Mitra Kouventa',
                        'max_height' => 44,
                        'sort_order' => 6,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ];

                \Illuminate\Support\Facades\DB::table('kouventa_partners')->insert($defaultPartners);
            }
        } catch (\Throwable $e) {
            // Ignore seeding errors
        }

        // Update custom admin sidebar order if stored in settings
        try {
            if (Schema::hasTable('settings')) {
                $customOrder = Setting::get('admin_sidebar_custom_order', []);
                if (! empty($customOrder) && is_array($customOrder)) {
                    if (! in_array('plugin:kouventa-partners', $customOrder, true)) {
                        $updatedOrder = [];
                        foreach ($customOrder as $item) {
                            $updatedOrder[] = $item;
                            if ($item === 'plugin:kouventa-clients') {
                                $updatedOrder[] = 'plugin:kouventa-partners';
                            }
                        }
                        if (! in_array('plugin:kouventa-partners', $updatedOrder, true)) {
                            $updatedOrder[] = 'plugin:kouventa-partners';
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
        Schema::dropIfExists('kouventa_partners');
    }
};

