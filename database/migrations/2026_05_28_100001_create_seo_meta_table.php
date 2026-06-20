<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_meta', function (Blueprint $table) {
            $table->id();
            $table->morphs('seoable');
            $table->string('title', 70)->nullable();
            $table->string('description', 160)->nullable();
            $table->string('canonical_url', 500)->nullable();
            $table->string('robots', 50)->default('index,follow');
            $table->string('og_title', 95)->nullable();
            $table->string('og_description', 200)->nullable();
            $table->foreignId('og_image_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('twitter_card', 20)->default('summary_large_image');
            $table->string('schema_type', 50)->nullable();
            $table->json('schema_data')->nullable();
            $table->string('focus_keyword', 100)->nullable();
            $table->unsignedTinyInteger('seo_score')->nullable();
            $table->unsignedTinyInteger('readability_score')->nullable();
            $table->timestamps();

            $table->unique(['seoable_type', 'seoable_id'], 'uq_seoable');
        });

        Schema::create('sitemap_pings', function (Blueprint $table) {
            $table->id();
            $table->enum('target', ['google', 'bing', 'indexnow']);
            $table->string('url', 500);
            $table->enum('status', ['queued', 'sent', 'failed'])->default('queued');
            $table->smallInteger('response_code')->nullable();
            $table->text('response_body')->nullable();
            $table->timestamp('pinged_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'pinged_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sitemap_pings');
        Schema::dropIfExists('seo_meta');
    }
};
