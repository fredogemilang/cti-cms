<?php

namespace Plugins\KouventaPages\Seeders;

use App\Models\Page;
use App\Models\Plugin;
use App\Models\SeoMeta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Plugins\KouventaPages\Models\KouventaPage;
use Plugins\KouventaPages\Schemas\ProductAiSchema;

class KouventaPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Activate plugin in database
        Plugin::updateOrCreate(
            ['slug' => 'kouventa-pages'],
            [
                'name' => 'Kouventa Pages',
                'version' => '1.0.0',
                'description' => 'Dedicated section and repeater management for Kouventa interactive product, solution, and pricing pages.',
                'author' => 'Kouventa Team',
                'provider' => 'Plugins\\KouventaPages\\Providers\\KouventaPagesServiceProvider',
                'is_active' => 1,
                'installed_at' => now(),
                'activated_at' => now(),
            ]
        );

        // 2. Seed / Migrate: Product AI (ID 2 in pages)
        $existingAiPage = Page::where('slug', 'product/ai')->first();

        $enAiSections = ProductAiSchema::getDefaults('en');
        $idAiSections = ProductAiSchema::getDefaults('id');

        $kouventaAiPage = KouventaPage::updateOrCreate(
            ['slug' => 'product/ai'],
            [
                'title' => 'Artificial Intelligence',
                'template' => 'product-ai',
                'status' => 'published',
                'published_at' => now(),
                'sections' => $enAiSections,
                'translations' => [
                    'id' => [
                        'title' => 'Artificial Intelligence',
                        'slug' => 'product/ai',
                        'sections' => $idAiSections,
                    ],
                ],
                'seo' => $existingAiPage?->seo,
            ]
        );

        // Migrate SEO Meta polymorphic records if they exist on old Page
        if ($existingAiPage) {
            $oldSeo = SeoMeta::where('seoable_type', Page::class)
                ->where('seoable_id', $existingAiPage->id)
                ->get();

            foreach ($oldSeo as $seoRow) {
                $seoAttrs = collect($seoRow->toArray())
                    ->except(['id', 'seoable_type', 'seoable_id', 'created_at', 'updated_at'])
                    ->toArray();

                SeoMeta::updateOrCreate(
                    [
                        'seoable_type' => KouventaPage::class,
                        'seoable_id' => $kouventaAiPage->id,
                        'locale' => $seoRow->locale,
                    ],
                    $seoAttrs
                );
            }

            // Create baseline revision
            $kouventaAiPage->saveRevision('Initial baseline migration from Core Page');

            // Takeout: Delete old Page ID 2
            $existingAiPage->forceDelete();
        }

        // Clean any old search index pointing to old Page ID 2
        if (\Illuminate\Support\Facades\Schema::hasTable('search_index')) {
            \App\Models\SearchIndex::where('searchable_type', Page::class)
                ->where('searchable_id', 2)
                ->delete();
        }
    }
}

