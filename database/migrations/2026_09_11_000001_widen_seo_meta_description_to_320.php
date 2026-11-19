<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_meta', function (Blueprint $table) {
            $table->string('description', 320)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('seo_meta', function (Blueprint $table) {
            $table->string('description', 160)->nullable()->change();
        });
    }
};
