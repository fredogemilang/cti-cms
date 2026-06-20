<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key_name', 100)->unique();
            $table->string('name');
            $table->string('subject');
            $table->longText('body_html');
            $table->text('body_text')->nullable();
            $table->json('variables')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Schema::create('email_template_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('email_templates')->cascadeOnDelete();
            $table->string('subject');
            $table->longText('body_html');
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->index(['template_id', 'created_at']);
        });

        if (! Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('email_template_versions');
        Schema::dropIfExists('email_templates');
    }
};
