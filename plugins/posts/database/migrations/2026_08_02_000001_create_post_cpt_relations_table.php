<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('post_cpt_relations')) {
            Schema::create('post_cpt_relations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
                $table->foreignId('cpt_entry_id')->constrained('cpt_entries')->onDelete('cascade');
                $table->string('cpt_slug')->nullable()->index();
                $table->timestamps();

                $table->unique(['post_id', 'cpt_entry_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('post_cpt_relations');
    }
};
