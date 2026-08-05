<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('not_found_logs', function (Blueprint $table) {
            $table->id();
            $table->string('path', 500)->index();
            $table->string('full_url', 1000)->nullable();
            $table->string('referrer', 1000)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->unsignedBigInteger('hit_count')->default(1);
            $table->timestamp('first_seen_at')->useCurrent();
            $table->timestamp('last_seen_at')->useCurrent();
            $table->boolean('is_resolved')->default(false)->index();
            $table->foreignId('redirect_id')->nullable()->constrained('redirects')->nullOnDelete();
            $table->timestamps();

            $table->unique('path');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('not_found_logs');
    }
};
