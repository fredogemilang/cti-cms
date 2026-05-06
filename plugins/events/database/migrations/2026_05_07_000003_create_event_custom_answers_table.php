<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_custom_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_registration_id')->constrained('event_registrations')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('event_custom_questions')->onDelete('cascade');
            $table->text('answer')->nullable();
            $table->timestamps();

            $table->index(['event_registration_id', 'question_id'], 'idx_registration_question');
            $table->index('question_id', 'idx_question');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_custom_answers');
    }
};
