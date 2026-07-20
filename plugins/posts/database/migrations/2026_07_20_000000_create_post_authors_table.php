<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create post_authors table
        Schema::create('post_authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->nullable();
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
        });

        // 2. Migrate existing users referenced by posts (or all users)
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            // Check if already exists to prevent duplicate key errors
            $exists = DB::table('post_authors')->where('id', $user->id)->exists();
            if (! $exists) {
                DB::table('post_authors')->insert([
                    'id' => $user->id,
                    'name' => $user->name,
                    'slug' => Str::slug($user->name).'-'.$user->id,
                    'email' => $user->email,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 3. Drop old foreign key constraint and add new one
        Schema::table('posts', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                try {
                    $table->dropForeign(['author_id']);
                } catch (Exception $e) {
                    // Ignore if constraint doesn't exist
                }
            }
        });

        Schema::table('posts', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->foreign('author_id')
                    ->references('id')
                    ->on('post_authors')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                try {
                    $table->dropForeign(['author_id']);
                } catch (Exception $e) {
                    // Ignore
                }
            }
        });

        Schema::table('posts', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->foreign('author_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            }
        });

        Schema::dropIfExists('post_authors');
    }
};
