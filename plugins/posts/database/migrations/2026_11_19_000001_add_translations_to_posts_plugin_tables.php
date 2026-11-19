<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ensure `translations` exists on posts plugin tables even when the plugin
     * is activated after earlier core migrations have already run.
     *
     * @var array<string, string>
     */
    protected array $targets = [
        'posts' => 'meta',
        'categories' => 'order',
        'tags' => 'slug',
    ];

    public function up(): void
    {
        foreach ($this->targets as $tableName => $afterColumn) {
            if (! Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'translations')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName, $afterColumn) {
                if (Schema::hasColumn($tableName, $afterColumn)) {
                    $table->json('translations')->nullable()->after($afterColumn);
                } else {
                    $table->json('translations')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->targets) as $tableName) {
            if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, 'translations')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('translations');
            });
        }
    }
};
