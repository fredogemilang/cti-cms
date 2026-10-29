<?php

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
        if (! Schema::hasTable('kouventa_pages')) {
            Schema::create('kouventa_pages', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique()->index();
                $table->string('title');
                $table->string('template')->index();
                $table->string('status', 20)->default('published')->index();
                $table->timestamp('published_at')->nullable();
                $table->json('sections')->nullable();
                $table->json('translations')->nullable();
                $table->json('settings')->nullable();
                $table->json('seo')->nullable();
                $table->unsignedBigInteger('author_id')->nullable()->index();
                $table->unsignedBigInteger('updated_by')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('kouventa_page_revisions')) {
            Schema::create('kouventa_page_revisions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('page_id')->constrained('kouventa_pages')->cascadeOnDelete();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('title')->nullable();
                $table->json('sections');
                $table->json('translations')->nullable();
                $table->string('summary')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kouventa_page_revisions');
        Schema::dropIfExists('kouventa_pages');
    }
};

