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
        Schema::create('partnership_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('website')->nullable();
            $table->string('contact_name');
            $table->string('email');
            $table->string('partnership_type'); // corporate, university, community, media, other
            $table->text('message');
            $table->string('status')->default('new'); // new, contacted, archived
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partnership_inquiries');
    }
};
