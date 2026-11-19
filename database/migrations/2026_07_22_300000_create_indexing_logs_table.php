<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indexing_logs', function (Blueprint $table) {
            $table->id();
            $table->string('protocol', 30); // 'indexnow' | 'google'
            $table->string('url', 500);
            $table->integer('status_code')->default(0);
            $table->text('response')->nullable();
            $table->timestamp('request_time')->useCurrent();
            $table->string('entity_type')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->timestamps();

            $table->index('protocol');
            $table->index('status_code');
            $table->index('request_time');
            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indexing_logs');
    }
};
