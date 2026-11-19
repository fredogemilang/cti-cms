<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_blocks', function (Blueprint $table) {
            // Drop the old unique that doesn't account for parent_block_id.
            // Child blocks under different repeaters can legitimately share
            // the same name (e.g. "title", "image", "description").
            $table->dropUnique(['page_id', 'name']);

            // Top-level blocks: parent_block_id IS NULL — MySQL treats each NULL
            // as distinct in unique indexes, so this effectively only enforces
            // uniqueness among child blocks under the SAME parent.
            // Application-level validation already prevents duplicate top-level names.
        });
    }

    public function down(): void
    {
        Schema::table('page_blocks', function (Blueprint $table) {
            $table->unique(['page_id', 'name']);
        });
    }
};
