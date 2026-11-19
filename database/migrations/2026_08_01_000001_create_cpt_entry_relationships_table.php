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
        Schema::create('cpt_entry_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_entry_id')->constrained('cpt_entries')->onDelete('cascade');
            $table->foreignId('child_entry_id')->constrained('cpt_entries')->onDelete('cascade');
            $table->foreignId('meta_field_id')->constrained('meta_fields')->onDelete('cascade');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->unique(['parent_entry_id', 'child_entry_id', 'meta_field_id'], 'cpt_rel_unique_key');
            $table->index(['parent_entry_id', 'meta_field_id']);
            $table->index(['child_entry_id', 'meta_field_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cpt_entry_relationships');
    }
};
