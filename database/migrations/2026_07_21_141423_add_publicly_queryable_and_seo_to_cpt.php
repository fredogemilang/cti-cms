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
        Schema::table('custom_post_types', function (Blueprint $table) {
            $table->boolean('publicly_queryable')->default(true)->after('has_archive');
        });

        Schema::table('cpt_entries', function (Blueprint $table) {
            $table->json('seo')->nullable()->after('meta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_post_types', function (Blueprint $table) {
            $table->dropColumn('publicly_queryable');
        });

        Schema::table('cpt_entries', function (Blueprint $table) {
            $table->dropColumn('seo');
        });
    }
};
