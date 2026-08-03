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
        if (Schema::hasTable('custom_post_types') && ! Schema::hasColumn('custom_post_types', 'translations')) {
            Schema::table('custom_post_types', function (Blueprint $table) {
                $table->json('translations')->nullable()->after('settings');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('custom_post_types') && Schema::hasColumn('custom_post_types', 'translations')) {
            Schema::table('custom_post_types', function (Blueprint $table) {
                $table->dropColumn('translations');
            });
        }
    }
};
