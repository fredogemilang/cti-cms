<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add GEO (Generative Engine Optimization) fields to seo_meta table.
 *
 * - ai_summary: Key takeaway / TL;DR for AI search engines to cite
 * - is_cornerstone: Flag for cornerstone content (critical pages)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_meta', function (Blueprint $table) {
            $table->text('ai_summary')->nullable()->after('focus_keyword')
                ->comment('Key takeaway for AI engines. Injected as schema abstract.');
            $table->boolean('is_cornerstone')->default(false)->after('ai_summary')
                ->comment('Mark as cornerstone content for freshness tracking.');
        });
    }

    public function down(): void
    {
        Schema::table('seo_meta', function (Blueprint $table) {
            $table->dropColumn(['ai_summary', 'is_cornerstone']);
        });
    }
};
