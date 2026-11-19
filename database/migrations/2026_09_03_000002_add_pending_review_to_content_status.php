<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pages')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->string('status', 30)->default('draft')->change();
            });
        }

        if (Schema::hasTable('cpt_entries')) {
            Schema::table('cpt_entries', function (Blueprint $table) {
                $table->string('status', 30)->default('draft')->change();
            });
        }

        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('status', 30)->default('draft')->change();
            });
        }

        if (Schema::hasTable('page_revisions')) {
            Schema::table('page_revisions', function (Blueprint $table) {
                $table->string('status', 30)->default('draft')->change();
            });
        }
    }

    public function down(): void
    {
        // Revert back to enum if necessary
        if (Schema::hasTable('pages')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->enum('status', ['draft', 'published', 'scheduled', 'private'])->default('draft')->change();
            });
        }

        if (Schema::hasTable('cpt_entries')) {
            Schema::table('cpt_entries', function (Blueprint $table) {
                $table->enum('status', ['draft', 'published', 'scheduled', 'archived'])->default('draft')->change();
            });
        }

        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->enum('status', ['draft', 'published', 'scheduled', 'archived'])->default('draft')->change();
            });
        }

        if (Schema::hasTable('page_revisions')) {
            Schema::table('page_revisions', function (Blueprint $table) {
                $table->enum('status', ['draft', 'published', 'scheduled', 'private'])->default('draft')->change();
            });
        }
    }
};
