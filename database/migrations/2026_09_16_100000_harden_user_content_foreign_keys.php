<?php

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
        $driver = DB::getDriverName();

        // 1. Pages: Change author_id foreign key from CASCADE to RESTRICT
        if (Schema::hasTable('pages')) {
            Schema::table('pages', function (Blueprint $table) use ($driver) {
                if ($driver !== 'sqlite') {
                    try {
                        $table->dropForeign(['author_id']);
                    } catch (\Exception $e) {
                        // Ignore if constraint does not exist
                    }
                }
                $table->foreign('author_id')
                    ->references('id')
                    ->on('users')
                    ->restrictOnDelete();
            });
        }

        // 2. CPT Entries: Change author_id foreign key from CASCADE to RESTRICT
        if (Schema::hasTable('cpt_entries')) {
            Schema::table('cpt_entries', function (Blueprint $table) use ($driver) {
                if ($driver !== 'sqlite') {
                    try {
                        $table->dropForeign(['author_id']);
                    } catch (\Exception $e) {
                        // Ignore if constraint does not exist
                    }
                }
                $table->foreign('author_id')
                    ->references('id')
                    ->on('users')
                    ->restrictOnDelete();
            });
        }

        // 3. Media: Change uploaded_by foreign key from CASCADE to RESTRICT
        if (Schema::hasTable('media')) {
            Schema::table('media', function (Blueprint $table) use ($driver) {
                if ($driver !== 'sqlite') {
                    try {
                        $table->dropForeign(['uploaded_by']);
                    } catch (\Exception $e) {
                        // Ignore if constraint does not exist
                    }
                }
                $table->foreign('uploaded_by')
                    ->references('id')
                    ->on('users')
                    ->restrictOnDelete();
            });
        }

        // 4. Page Revisions: Make user_id nullable and set nullOnDelete
        if (Schema::hasTable('page_revisions')) {
            Schema::table('page_revisions', function (Blueprint $table) use ($driver) {
                if ($driver !== 'sqlite') {
                    try {
                        $table->dropForeign(['user_id']);
                    } catch (\Exception $e) {
                        // Ignore if constraint does not exist
                    }
                }
                $table->unsignedBigInteger('user_id')->nullable()->change();
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            });
        }

        // 5. Editorial Notes: Make user_id nullable and set nullOnDelete
        if (Schema::hasTable('editorial_notes')) {
            Schema::table('editorial_notes', function (Blueprint $table) use ($driver) {
                if ($driver !== 'sqlite') {
                    try {
                        $table->dropForeign(['user_id']);
                    } catch (\Exception $e) {
                        // Ignore if constraint does not exist
                    }
                }
                $table->unsignedBigInteger('user_id')->nullable()->change();
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if (Schema::hasTable('pages')) {
            Schema::table('pages', function (Blueprint $table) use ($driver) {
                if ($driver !== 'sqlite') {
                    try {
                        $table->dropForeign(['author_id']);
                    } catch (\Exception $e) {}
                }
                $table->foreign('author_id')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('cpt_entries')) {
            Schema::table('cpt_entries', function (Blueprint $table) use ($driver) {
                if ($driver !== 'sqlite') {
                    try {
                        $table->dropForeign(['author_id']);
                    } catch (\Exception $e) {}
                }
                $table->foreign('author_id')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('media')) {
            Schema::table('media', function (Blueprint $table) use ($driver) {
                if ($driver !== 'sqlite') {
                    try {
                        $table->dropForeign(['uploaded_by']);
                    } catch (\Exception $e) {}
                }
                $table->foreign('uploaded_by')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnDelete();
            });
        }
    }
};
