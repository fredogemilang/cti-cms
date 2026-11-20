<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Plugins\PartnerRewardCatalog\Support\CatalogStore;
use Tests\TestCase;

class PartnerRewardCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function activatePlugin(): void
    {
        $loader = require base_path('vendor/autoload.php');
        $loader->addPsr4('Plugins\\PartnerRewardCatalog\\', base_path('plugins/partner-reward-catalog/src'));

        $plugin = Plugin::firstOrCreate(
            ['slug' => 'partner-reward-catalog'],
            [
                'name' => 'Partner Reward Catalog',
                'version' => '1.0.0',
                'provider' => \Plugins\PartnerRewardCatalog\Providers\PartnerRewardCatalogServiceProvider::class,
                'is_active' => true,
            ]
        );
        if (! $plugin->is_active) {
            $plugin->update(['is_active' => true]);
        }
        app()->register(\Plugins\PartnerRewardCatalog\Providers\PartnerRewardCatalogServiceProvider::class);
    }

    public function test_public_catalog_renders_empty_state_when_no_pdf_uploaded(): void
    {
        $this->activatePlugin();

        $controller = app(\Plugins\PartnerRewardCatalog\Http\Controllers\CatalogController::class);
        $view = $controller->show(request());
        $html = $view->render();

        $this->assertStringContainsString('Partner Reward Catalog Is Coming Soon', $html);
        $this->assertStringContainsString('id="partner-reward-catalog-root"', $html);
    }

    public function test_uploading_new_pdf_replaces_previous_pdf_file_and_media_record(): void
    {
        Storage::fake('public');
        config(['media.disk' => 'public']);
        $this->activatePlugin();

        $admin = User::factory()->create();
        $this->actingAs($admin);

        $pdfBytes = "%PDF-1.4\n1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n2 0 obj << /Type /Pages /Kids [] /Count 0 >> endobj\ntrailer << /Root 1 0 R >>\n%%EOF";

        $firstPdf = UploadedFile::fake()->createWithContent('catalog-v1.pdf', $pdfBytes);
        $firstConfig = CatalogStore::saveCatalog([
            'title_en' => 'Partner Reward Catalog',
            'title_id' => 'Katalog Reward Partner',
            'allow_download' => true,
        ], $firstPdf);

        $firstMediaId = $firstConfig['media_id'];
        $firstPath = $firstConfig['file_path'];

        $this->assertNotNull($firstMediaId);
        $this->assertTrue(Storage::disk('public')->exists($firstPath));
        $this->assertDatabaseHas('media', ['id' => $firstMediaId]);

        $secondPdf = UploadedFile::fake()->createWithContent('catalog-v2.pdf', $pdfBytes);
        $secondConfig = CatalogStore::saveCatalog([
            'title_en' => 'Partner Reward Catalog 2026',
            'title_id' => 'Katalog Reward Partner 2026',
            'allow_download' => true,
        ], $secondPdf);

        $secondMediaId = $secondConfig['media_id'];
        $secondPath = $secondConfig['file_path'];

        $this->assertNotSame($firstMediaId, $secondMediaId);
        $this->assertFalse(Storage::disk('public')->exists($firstPath));
        $this->assertDatabaseMissing('media', ['id' => $firstMediaId]);
        $this->assertTrue(Storage::disk('public')->exists($secondPath));
        $this->assertDatabaseHas('media', ['id' => $secondMediaId, 'original_filename' => 'catalog-v2.pdf']);
    }

    public function test_uploading_new_cover_banner_replaces_previous_cover_and_populates_promo_banner(): void
    {
        Storage::fake('public');
        config(['media.disk' => 'public']);
        $this->activatePlugin();

        $admin = User::factory()->create();
        $this->actingAs($admin);

        $firstCover = UploadedFile::fake()->image('cover-2026.png', 1200, 675);
        $cfg1 = CatalogStore::saveCatalog([
            'promo_title_en' => 'Annual Partner Incentive Trip 2027',
            'promo_title_id' => 'Annual Partner Incentive Trip 2027',
            'promo_cta_text_en' => 'Explore the Partner Reward Catalog',
            'promo_cta_text_id' => 'Lihat Katalog Hadiah Mitra',
        ], null, $firstCover);

        $this->assertNotNull($cfg1['cover_media_id']);
        $this->assertTrue(Storage::disk('public')->exists($cfg1['cover_image_path']));

        $secondCover = UploadedFile::fake()->image('cover-2027.png', 1200, 675);
        $cfg2 = CatalogStore::saveCatalog([
            'promo_title_en' => 'Partner Trip Tokyo 2028',
            'promo_title_id' => 'Perjalanan Mitra Tokyo 2028',
        ], null, $secondCover);

        $this->assertNotSame($cfg1['cover_media_id'], $cfg2['cover_media_id']);
        $this->assertFalse(Storage::disk('public')->exists($cfg1['cover_image_path']));
        $this->assertDatabaseMissing('media', ['id' => $cfg1['cover_media_id']]);
        $this->assertTrue(Storage::disk('public')->exists($cfg2['cover_image_path']));

        $bannerEn = CatalogStore::promoBanner('en');
        $bannerId = CatalogStore::promoBanner('id');
        $this->assertSame('/partner-reward-catalog', $bannerEn['cta_url']);
        $this->assertSame('Partner Trip Tokyo 2028', $bannerEn['title']);
        $this->assertSame('Perjalanan Mitra Tokyo 2028', $bannerId['title']);
        $this->assertSame($cfg2['cover_image_path'], $bannerEn['cover_path']);
    }
}
