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

    public function test_selecting_and_clearing_cover_banner_from_media_library_preserves_media_records(): void
    {
        Storage::fake('public');
        config(['media.disk' => 'public']);
        $this->activatePlugin();

        $admin = User::factory()->create();
        $this->actingAs($admin);

        $mediaService = app(\App\Services\MediaService::class);
        $firstMedia = $mediaService->upload(UploadedFile::fake()->image('cover-2026.png', 1200, 675), [
            'title' => 'Cover 2026',
            'uploaded_by' => $admin->id,
        ]);
        $secondMedia = $mediaService->upload(UploadedFile::fake()->image('cover-2027.png', 1200, 675), [
            'title' => 'Cover 2027',
            'uploaded_by' => $admin->id,
        ]);

        $cfg1 = CatalogStore::saveCatalog([
            'cover_media_id' => $firstMedia->id,
            'promo_title_en' => 'Annual Partner Incentive Trip 2027',
            'promo_title_id' => 'Annual Partner Incentive Trip 2027',
            'promo_cta_text_en' => 'Explore the Partner Reward Catalog',
            'promo_cta_text_id' => 'Lihat Katalog Hadiah Mitra',
        ]);

        $this->assertSame($firstMedia->id, $cfg1['cover_media_id']);
        $this->assertTrue(Storage::disk('public')->exists($cfg1['cover_image_path']));

        $cfg2 = CatalogStore::saveCatalog([
            'cover_media_id' => $secondMedia->id,
            'promo_title_en' => 'Partner Trip Tokyo 2028',
            'promo_title_id' => 'Perjalanan Mitra Tokyo 2028',
        ]);

        $this->assertSame($secondMedia->id, $cfg2['cover_media_id']);
        // Both Media Library records must stay intact when swapping via Media Library picker
        $this->assertDatabaseHas('media', ['id' => $firstMedia->id]);
        $this->assertDatabaseHas('media', ['id' => $secondMedia->id]);
        $this->assertTrue(Storage::disk('public')->exists($secondMedia->webp_path ?? $secondMedia->path));

        $bannerEn = CatalogStore::promoBanner('en');
        $bannerId = CatalogStore::promoBanner('id');
        $this->assertSame('/partner-reward-catalog', $bannerEn['cta_url']);
        $this->assertSame('Partner Trip Tokyo 2028', $bannerEn['title']);
        $this->assertSame('Perjalanan Mitra Tokyo 2028', $bannerId['title']);
        $this->assertSame($cfg2['cover_image_path'], $bannerEn['cover_path']);

        // Clearing cover selection must keep the Media Library item untouched
        $cleared = CatalogStore::removeCoverImage();
        $this->assertNull($cleared['cover_media_id']);
        $this->assertNull($cleared['cover_image_path']);
        $this->assertDatabaseHas('media', ['id' => $secondMedia->id]);
    }
}
