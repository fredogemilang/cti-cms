<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_meta', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
            $table->text('og_description')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('seo_meta', function (Blueprint $table) {
            $table->string('description', 320)->nullable()->change();
            $table->string('og_description', 200)->nullable()->change();
        });
    }
};
