<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('posts') && ! Schema::hasColumn('posts', 'translations')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->json('translations')->nullable()->after('meta');
            });
        }

        if (Schema::hasTable('categories') && ! Schema::hasColumn('categories', 'translations')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->json('translations')->nullable()->after('order');
            });
        }

        if (Schema::hasTable('tags') && ! Schema::hasColumn('tags', 'translations')) {
            Schema::table('tags', function (Blueprint $table) {
                $table->json('translations')->nullable()->after('slug');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('posts') && Schema::hasColumn('posts', 'translations')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('translations');
            });
        }

        if (Schema::hasTable('categories') && Schema::hasColumn('categories', 'translations')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('translations');
            });
        }

        if (Schema::hasTable('tags') && Schema::hasColumn('tags', 'translations')) {
            Schema::table('tags', function (Blueprint $table) {
                $table->dropColumn('translations');
            });
        }
    }
};
