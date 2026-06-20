<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('forms') && ! Schema::hasColumn('forms', 'deleted_at')) {
            Schema::table('forms', fn (Blueprint $t) => $t->softDeletes());
        }
        if (Schema::hasTable('media') && ! Schema::hasColumn('media', 'deleted_at')) {
            Schema::table('media', fn (Blueprint $t) => $t->softDeletes());
        }
        if (Schema::hasTable('form_entries') && ! Schema::hasColumn('form_entries', 'deleted_at')) {
            Schema::table('form_entries', fn (Blueprint $t) => $t->softDeletes());
        }
    }

    public function down(): void
    {
        foreach (['forms', 'media', 'form_entries'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, fn (Blueprint $t) => $t->dropSoftDeletes());
            }
        }
    }
};
