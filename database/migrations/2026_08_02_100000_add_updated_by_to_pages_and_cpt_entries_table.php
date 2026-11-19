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
        if (! Schema::hasColumn('pages', 'updated_by')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->foreignId('updated_by')->nullable()->after('author_id')->constrained('users')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('cpt_entries', 'updated_by')) {
            Schema::table('cpt_entries', function (Blueprint $table) {
                $table->foreignId('updated_by')->nullable()->after('author_id')->constrained('users')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pages', 'updated_by')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropForeign(['updated_by']);
                $table->dropColumn('updated_by');
            });
        }

        if (Schema::hasColumn('cpt_entries', 'updated_by')) {
            Schema::table('cpt_entries', function (Blueprint $table) {
                $table->dropForeign(['updated_by']);
                $table->dropColumn('updated_by');
            });
        }
    }
};
