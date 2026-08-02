<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('youtube_videos', function (Blueprint $table) {
            $table->id();
            $table->string('youtube_id', 50)->unique();
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->string('thumbnail_default', 500)->nullable();
            $table->string('thumbnail_medium', 500)->nullable();
            $table->string('thumbnail_high', 500)->nullable();
            $table->string('channel_title', 255)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('duration', 30)->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->unsignedBigInteger('view_count')->default(0);
            $table->unsignedBigInteger('like_count')->default(0);
            $table->json('tags')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['is_visible', 'published_at']);
            $table->index('is_featured');
        });

        Schema::create('youtube_playlists', function (Blueprint $table) {
            $table->id();
            $table->string('youtube_id', 50)->unique();
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->string('thumbnail_url', 500)->nullable();
            $table->unsignedInteger('video_count')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index('is_visible');
        });

        Schema::create('youtube_playlist_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playlist_id')->constrained('youtube_playlists')->cascadeOnDelete();
            $table->foreignId('video_id')->constrained('youtube_videos')->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['playlist_id', 'video_id']);
            $table->index(['playlist_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('youtube_playlist_videos');
        Schema::dropIfExists('youtube_playlists');
        Schema::dropIfExists('youtube_videos');
    }
};
