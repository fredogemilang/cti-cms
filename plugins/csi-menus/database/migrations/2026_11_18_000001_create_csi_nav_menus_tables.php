<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('csi_nav_menus')) {
            Schema::create('csi_nav_menus', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name');
                $table->string('location')->index();
                $table->string('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('csi_nav_menu_items')) {
            Schema::create('csi_nav_menu_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('menu_id')->constrained('csi_nav_menus')->cascadeOnDelete();
                $table->foreignId('parent_id')->nullable()->constrained('csi_nav_menu_items')->cascadeOnDelete();
                $table->string('title');
                $table->string('url')->default('#');
                $table->string('target', 20)->default('_self');
                $table->string('item_type', 40)->default('link');
                $table->string('css_class')->nullable();
                $table->json('translations')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['menu_id', 'parent_id', 'order']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('csi_nav_menu_items');
        Schema::dropIfExists('csi_nav_menus');
    }
};
