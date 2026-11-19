<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('search_index', function (Blueprint $table) {
            $table->id();
            $table->string('searchable_type', 255);
            $table->unsignedBigInteger('searchable_id');
            $table->string('locale', 10);
            $table->string('title', 512);
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('url', 1024);
            $table->timestamp('indexed_at')->nullable();

            $table->unique(['searchable_type', 'searchable_id', 'locale'], 'uniq_searchable_locale');
            $table->index(['locale', 'searchable_type']);

            if (DB::connection()->getDriverName() === 'mysql') {
                $table->fullText(['title', 'excerpt', 'body'], 'ft_content');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_index');
    }
};
