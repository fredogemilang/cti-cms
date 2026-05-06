<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_custom_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('event_custom_questions')->onDelete('cascade');
            $table->string('option_text', 255);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index(['question_id', 'order'], 'idx_question_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_custom_question_options');
    }
};
