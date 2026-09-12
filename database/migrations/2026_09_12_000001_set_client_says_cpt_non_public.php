<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('custom_post_types')
            ->where('slug', 'client-says')
            ->update([
                'publicly_queryable' => false,
                'has_archive' => false,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('custom_post_types')
            ->where('slug', 'client-says')
            ->update([
                'publicly_queryable' => true,
                'has_archive' => true,
            ]);
    }
};
