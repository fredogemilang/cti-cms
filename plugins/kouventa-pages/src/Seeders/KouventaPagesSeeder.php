<?php

namespace Plugins\KouventaPages\Seeders;

use App\Models\Media;
use App\Models\Page;
use App\Models\Plugin;
use App\Models\SeoMeta;
use App\Services\MediaService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Plugins\KouventaPages\Models\KouventaClient;
use Plugins\KouventaPages\Models\KouventaPage;
use Plugins\KouventaPages\Schemas\PricingSchema;
use Plugins\KouventaPages\Schemas\ProductAiSchema;
use Plugins\KouventaPages\Schemas\ProductCeSchema;
use Plugins\KouventaPages\Schemas\SolutionAiSchema;
use Plugins\KouventaPages\Schemas\SolutionCeSchema;

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
                'version' => '1.1.0',
                'description' => 'Dedicated section and repeater management for Kouventa interactive product, solution, pricing pages, and trusted clients.',
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

        $kouventaAiPage = KouventaPage::firstOrCreate(
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

            $kouventaAiPage->saveRevision('Initial baseline migration from Core Page');

            DB::table('page_blocks')->where('page_id', $existingAiPage->id)->delete();
            DB::table('page_revisions')->where('page_id', $existingAiPage->id)->delete();
            DB::table('pages')->where('id', $existingAiPage->id)->delete();
        }

        if (Schema::hasTable('search_index')) {
            \App\Models\SearchIndex::where('searchable_type', Page::class)
                ->where('searchable_id', 2)
                ->delete();
        }

        // 3. Seed / Migrate: Product Customer Engagement (ID 3 in pages)
        $existingCePage = Page::where('slug', 'product/customer-engagement')->first();

        $enCeSections = ProductCeSchema::getDefaults('en');
        $idCeSections = ProductCeSchema::getDefaults('id');

        $kouventaCePage = KouventaPage::firstOrCreate(
            ['slug' => 'product/customer-engagement'],
            [
                'title' => 'Customer Engagement',
                'template' => 'product-ce',
                'status' => 'published',
                'published_at' => now(),
                'sections' => $enCeSections,
                'translations' => [
                    'id' => [
                        'title' => 'Customer Engagement',
                        'slug' => 'product/customer-engagement',
                        'sections' => $idCeSections,
                    ],
                ],
                'seo' => $existingCePage?->seo,
            ]
        );

        if ($existingCePage) {
            $oldCeSeo = SeoMeta::where('seoable_type', Page::class)
                ->where('seoable_id', $existingCePage->id)
                ->get();

            foreach ($oldCeSeo as $seoRow) {
                $seoAttrs = collect($seoRow->toArray())
                    ->except(['id', 'seoable_type', 'seoable_id', 'created_at', 'updated_at'])
                    ->toArray();

                SeoMeta::updateOrCreate(
                    [
                        'seoable_type' => KouventaPage::class,
                        'seoable_id' => $kouventaCePage->id,
                        'locale' => $seoRow->locale,
                    ],
                    $seoAttrs
                );
            }

            $kouventaCePage->saveRevision('Initial baseline migration from Core Page');

            DB::table('page_blocks')->where('page_id', $existingCePage->id)->delete();
            DB::table('page_revisions')->where('page_id', $existingCePage->id)->delete();
            DB::table('pages')->where('id', $existingCePage->id)->delete();
        }

        if (Schema::hasTable('search_index')) {
            \App\Models\SearchIndex::where('searchable_type', Page::class)
                ->where('searchable_id', 3)
                ->delete();
        }

        // 4. Seed / Migrate: Solutions AI (ID 4 in pages)
        $existingSolAiPage = Page::where('slug', 'solutions/ai')->first();

        $enSolAiSections = SolutionAiSchema::getDefaults('en');
        $idSolAiSections = SolutionAiSchema::getDefaults('id');

        $kouventaSolAiPage = KouventaPage::firstOrCreate(
            ['slug' => 'solutions/ai'],
            [
                'title' => 'AI Solutions',
                'template' => 'solution-ai',
                'status' => 'published',
                'published_at' => now(),
                'sections' => $enSolAiSections,
                'translations' => [
                    'id' => [
                        'title' => 'AI Solutions',
                        'slug' => 'solutions/ai',
                        'sections' => $idSolAiSections,
                    ],
                ],
                'seo' => $existingSolAiPage?->seo,
            ]
        );

        if ($existingSolAiPage) {
            $oldSeo = SeoMeta::where('seoable_type', Page::class)
                ->where('seoable_id', $existingSolAiPage->id)
                ->get();

            foreach ($oldSeo as $seoRow) {
                $seoAttrs = collect($seoRow->toArray())
                    ->except(['id', 'seoable_type', 'seoable_id', 'created_at', 'updated_at'])
                    ->toArray();

                SeoMeta::updateOrCreate(
                    [
                        'seoable_type' => KouventaPage::class,
                        'seoable_id' => $kouventaSolAiPage->id,
                        'locale' => $seoRow->locale,
                    ],
                    $seoAttrs
                );
            }

            $kouventaSolAiPage->saveRevision('Initial baseline migration from Core Page');

            DB::table('page_blocks')->where('page_id', $existingSolAiPage->id)->delete();
            DB::table('page_revisions')->where('page_id', $existingSolAiPage->id)->delete();
            DB::table('pages')->where('id', $existingSolAiPage->id)->delete();
        }

        if (Schema::hasTable('search_index')) {
            \App\Models\SearchIndex::where('searchable_type', Page::class)
                ->where('searchable_id', 4)
                ->delete();
        }

        // 5. Seed / Migrate: Solutions Customer Engagement (ID 5 in pages)
        $existingSolCePage = Page::where('slug', 'solutions/customer-engagement')->first();

        $enSolCeSections = SolutionCeSchema::getDefaults('en');
        $idSolCeSections = SolutionCeSchema::getDefaults('id');

        $kouventaSolCePage = KouventaPage::firstOrCreate(
            ['slug' => 'solutions/customer-engagement'],
            [
                'title' => 'Customer Engagement Solutions',
                'template' => 'solution-ce',
                'status' => 'published',
                'published_at' => now(),
                'sections' => $enSolCeSections,
                'translations' => [
                    'id' => [
                        'title' => 'Customer Engagement Solutions',
                        'slug' => 'solutions/customer-engagement',
                        'sections' => $idSolCeSections,
                    ],
                ],
                'seo' => $existingSolCePage?->seo,
            ]
        );

        if ($existingSolCePage) {
            $oldSeo = SeoMeta::where('seoable_type', Page::class)
                ->where('seoable_id', $existingSolCePage->id)
                ->get();

            foreach ($oldSeo as $seoRow) {
                $seoAttrs = collect($seoRow->toArray())
                    ->except(['id', 'seoable_type', 'seoable_id', 'created_at', 'updated_at'])
                    ->toArray();

                SeoMeta::updateOrCreate(
                    [
                        'seoable_type' => KouventaPage::class,
                        'seoable_id' => $kouventaSolCePage->id,
                        'locale' => $seoRow->locale,
                    ],
                    $seoAttrs
                );
            }

            $kouventaSolCePage->saveRevision('Initial baseline migration from Core Page');

            DB::table('page_blocks')->where('page_id', $existingSolCePage->id)->delete();
            DB::table('page_revisions')->where('page_id', $existingSolCePage->id)->delete();
            DB::table('pages')->where('id', $existingSolCePage->id)->delete();
        }

        if (Schema::hasTable('search_index')) {
            \App\Models\SearchIndex::where('searchable_type', Page::class)
                ->where('searchable_id', 5)
                ->delete();
        }

        // 6. Seed / Migrate: Pricing (ID 6 in pages)
        $existingPricingPage = Page::where('slug', 'pricing')->first();

        $enPricingSections = PricingSchema::getDefaults('en');
        $idPricingSections = PricingSchema::getDefaults('id');

        $kouventaPricingPage = KouventaPage::firstOrCreate(
            ['slug' => 'pricing'],
            [
                'title' => 'Pricing',
                'template' => 'pricing',
                'status' => 'published',
                'published_at' => now(),
                'sections' => $enPricingSections,
                'translations' => [
                    'id' => [
                        'title' => 'Pricing',
                        'slug' => 'pricing',
                        'sections' => $idPricingSections,
                    ],
                ],
                'seo' => $existingPricingPage?->seo,
            ]
        );

        if ($existingPricingPage) {
            $oldSeo = SeoMeta::where('seoable_type', Page::class)
                ->where('seoable_id', $existingPricingPage->id)
                ->get();

            foreach ($oldSeo as $seoRow) {
                $seoAttrs = collect($seoRow->toArray())
                    ->except(['id', 'seoable_type', 'seoable_id', 'created_at', 'updated_at'])
                    ->toArray();

                SeoMeta::updateOrCreate(
                    [
                        'seoable_type' => KouventaPage::class,
                        'seoable_id' => $kouventaPricingPage->id,
                        'locale' => $seoRow->locale,
                    ],
                    $seoAttrs
                );
            }

            $kouventaPricingPage->saveRevision('Initial baseline migration from Core Page');

            DB::table('page_blocks')->where('page_id', $existingPricingPage->id)->delete();
            DB::table('page_revisions')->where('page_id', $existingPricingPage->id)->delete();
            DB::table('pages')->where('id', $existingPricingPage->id)->delete();
        }

        if (Schema::hasTable('search_index')) {
            \App\Models\SearchIndex::where('searchable_type', Page::class)
                ->where('searchable_id', 6)
                ->delete();
        }

        // 7. Synchronize SearchIndex for all 5 Kouventa interactive pages (EN & ID)
        foreach ([$kouventaAiPage, $kouventaCePage, $kouventaSolAiPage, $kouventaSolCePage, $kouventaPricingPage] as $kPage) {
            $kPage->syncSearchIndex();
        }

        // 8. Seed Default Trusted Clients ("They trusted us")
        $this->seedClients();

        // 9. Update Admin Sidebar Custom Order if stored in Settings
        $this->updateSidebarMenuOrder();
    }

    /**
     * Ensure any stored custom sidebar order replaces plugin:kouventa-pages with the 4 new top-level menus.
     */
    protected function updateSidebarMenuOrder(): void
    {
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }

            $customOrder = \App\Models\Setting::get('admin_sidebar_custom_order', []);
            if (empty($customOrder) || ! is_array($customOrder)) {
                return;
            }

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

            \App\Models\Setting::set('admin_sidebar_custom_order', array_values($updatedOrder));
        } catch (\Throwable $e) {
            // Ignore setting update errors
        }
    }

    /**
     * Seed default clients for the Homepage "They trusted us" section.
     */
    protected function seedClients(): void
    {
        if (! Schema::hasTable('kouventa_clients')) {
            return;
        }

        $defaultClients = [
            [
                'name' => 'Navios',
                'asset' => 'images/logos/customer-navios.png',
                'alt_text' => 'Logo Navios - Klien Implementasi Solusi Customer Engagement Kouventa',
                'max_height' => 32,
                'sort_order' => 1,
            ],
            [
                'name' => 'Toqco',
                'asset' => 'images/logos/customer-toqco.png',
                'alt_text' => 'Logo Toqco - Klien Solusi Otomasi WhatsApp & Omnichannel Kouventa',
                'max_height' => 42,
                'sort_order' => 2,
            ],
            [
                'name' => 'Mitra Mandiri Informatika',
                'asset' => 'images/logos/customer-mitra-mandiri.png',
                'alt_text' => 'Logo Mitra Mandiri Informatika - Klien Enterprise AI & Solusi Komunikasi Kouventa',
                'max_height' => 44,
                'sort_order' => 3,
            ],
            [
                'name' => 'Liberta Hotels',
                'asset' => 'images/logos/customer-liberta-hotels.png',
                'alt_text' => 'Logo Liberta Hotels - Klien Solusi Omnichannel Hospitality & Reservasi Kouventa',
                'max_height' => 44,
                'sort_order' => 4,
            ],
            [
                'name' => 'DPay',
                'asset' => 'images/logos/logo-dpay.png',
                'alt_text' => 'Logo DPay - Klien Notifikasi & Otomasi Transaksi Finansial WhatsApp Kouventa',
                'max_height' => 42,
                'sort_order' => 5,
            ],
            [
                'name' => 'PakeDriver',
                'asset' => 'images/logos/customer-pakedriver.png',
                'alt_text' => 'Logo PakeDriver - Klien Solusi Notifikasi & Tracking Logistik Kouventa',
                'max_height' => 38,
                'sort_order' => 6,
            ],
            [
                'name' => "Luna's Doughnuts",
                'asset' => 'images/logos/customer-lunas-doughnuts.png',
                'alt_text' => "Logo Luna's Doughnuts - Klien CRM & Broadcast WhatsApp Marketing Kouventa",
                'max_height' => 46,
                'sort_order' => 7,
            ],
            [
                'name' => 'Astra Otoparts',
                'asset' => 'images/logos/customer-astra-otoparts.png',
                'alt_text' => 'Logo Astra Otoparts - Klien Solusi Layanan Pelanggan & Otomasi Komunikasi Kouventa',
                'max_height' => 44,
                'sort_order' => 8,
            ],
        ];

        $mediaService = app(MediaService::class);

        foreach ($defaultClients as $client) {
            $existing = KouventaClient::where('name', $client['name'])->first();
            if ($existing) {
                continue;
            }

            $logoPath = $client['asset'];
            $filename = basename($client['asset']);
            $fullAssetPath = base_path('themes/kouventa/assets/' . $client['asset']);

            try {
                $media = Media::where('original_filename', $filename)->first();

                if (! $media && file_exists($fullAssetPath)) {
                    $uploadedFile = new UploadedFile(
                        $fullAssetPath,
                        $filename,
                        mime_content_type($fullAssetPath) ?: 'image/png',
                        null,
                        true
                    );

                    $media = $mediaService->upload($uploadedFile, [
                        'title' => $client['name'],
                        'alt_text' => $client['alt_text'],
                    ]);
                }

                if ($media) {
                    $logoPath = $media->webp_path ?? $media->path;
                }
            } catch (\Throwable $e) {
                // Fallback to theme asset relative path
                $logoPath = $client['asset'];
            }

            KouventaClient::create([
                'name' => $client['name'],
                'logo' => $logoPath,
                'website_url' => null,
                'alt_text' => $client['alt_text'],
                'max_height' => $client['max_height'],
                'sort_order' => $client['sort_order'],
                'is_active' => true,
            ]);
        }
    }
}
