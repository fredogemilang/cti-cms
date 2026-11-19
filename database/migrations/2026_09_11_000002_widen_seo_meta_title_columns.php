<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_meta', function (Blueprint $table) {
            $table->string('title', 255)->nullable()->change();
            $table->string('og_title', 255)->nullable()->change();
            $table->string('og_description', 500)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('seo_meta', function (Blueprint $table) {
            $table->string('title', 70)->nullable()->change();
            $table->string('og_title', 95)->nullable()->change();
            $table->string('og_description', 200)->nullable()->change();
        });
    }
};
