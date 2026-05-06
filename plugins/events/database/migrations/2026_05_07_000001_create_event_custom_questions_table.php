<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_custom_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->enum('type', ['text', 'textarea', 'single_select', 'multi_select', 'email', 'phone', 'date']);
            $table->string('question', 255);
            $table->text('question_description')->nullable();
            $table->string('short_label', 50);
            $table->boolean('required')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->string('image', 255)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['event_id', 'order'], 'idx_event_order');
            $table->index(['event_id', 'type'], 'idx_event_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_custom_questions');
    }
};
