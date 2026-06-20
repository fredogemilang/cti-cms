<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->json('variants')->nullable()->after('webp_path');
            $table->decimal('focal_x', 5, 4)->default(0.5)->after('variants');
            $table->decimal('focal_y', 5, 4)->default(0.5)->after('focal_x');
            $table->text('placeholder_data_uri')->nullable()->after('focal_y');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['variants', 'focal_x', 'focal_y', 'placeholder_data_uri']);
        });
    }
};
