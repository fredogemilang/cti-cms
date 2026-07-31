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
        // 1. Canonical Translation Keys
        Schema::create('string_translation_keys', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50)->default('ui')->index();
            $table->string('key', 191);
            $table->text('default_value')->nullable();
            $table->timestamps();

            $table->unique(['group', 'key']);
        });

        // 2. Localized Translations per Locale
        Schema::create('string_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('translation_key_id')->constrained('string_translation_keys')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['translation_key_id', 'locale']);
        });

        // 3. Source File Tracking per Key
        Schema::create('string_translation_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('translation_key_id')->constrained('string_translation_keys')->cascadeOnDelete();
            $table->string('source_type', 20)->default('core')->index(); // core, theme, plugin
            $table->string('source_name', 50)->default('default'); // cdt, site-kit, etc.
            $table->string('source_file')->nullable();
            $table->timestamps();

            $table->unique(['translation_key_id', 'source_type', 'source_name', 'source_file'], 'translation_source_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('string_translation_sources');
        Schema::dropIfExists('string_translations');
        Schema::dropIfExists('string_translation_keys');
    }
};
