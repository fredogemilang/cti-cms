<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpt_entry_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpt_entry_id')->constrained('cpt_entries')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('status')->default('draft');
            $table->longText('meta')->nullable();
            $table->longText('translations')->nullable();
            $table->boolean('is_autosave')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpt_entry_revisions');
    }
};
