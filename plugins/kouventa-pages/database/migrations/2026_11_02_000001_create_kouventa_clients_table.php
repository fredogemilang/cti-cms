<?php

use App\Models\Setting;
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
        if (! Schema::hasTable('kouventa_clients')) {
            Schema::create('kouventa_clients', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('logo');
                $table->string('website_url')->nullable();
                $table->string('alt_text')->nullable();
                $table->unsignedSmallInteger('max_height')->default(44);
                $table->integer('sort_order')->default(0)->index();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
        }

        // Update custom admin sidebar order if stored in settings
        try {
            if (Schema::hasTable('settings')) {
                $customOrder = Setting::get('admin_sidebar_custom_order', []);
                if (! empty($customOrder) && is_array($customOrder)) {
                    $newKeys = [
                        'plugin:kouventa-solutions',
                        'plugin:kouventa-products',
                        'plugin:kouventa-pricing',
                        'plugin:kouventa-clients',
                    ];

                    $updatedOrder = [];
                    $inserted = false;

                    foreach ($customOrder as $item) {
                        if ($item === 'plugin:kouventa-pages') {
                            foreach ($newKeys as $nk) {
                                if (! in_array($nk, $updatedOrder, true)) {
                                    $updatedOrder[] = $nk;
                                }
                            }
                            $inserted = true;
                            continue;
                        }

                        if (in_array($item, $newKeys, true)) {
                            continue;
                        }

                        $updatedOrder[] = $item;

                        if ($item === 'core:2' && ! in_array('plugin:kouventa-pages', $customOrder, true) && ! $inserted) {
                            foreach ($newKeys as $nk) {
                                $updatedOrder[] = $nk;
                            }
                            $inserted = true;
                        }
                    }

                    if (! $inserted) {
                        foreach ($newKeys as $nk) {
                            $updatedOrder[] = $nk;
                        }
                    }

                    Setting::set('admin_sidebar_custom_order', array_values($updatedOrder));
                }
            }
        } catch (\Throwable $e) {
            // Ignore setting update errors during migration
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kouventa_clients');
    }
};

