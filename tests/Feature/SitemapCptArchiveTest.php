<?php

namespace Tests\Feature;

use App\Models\CustomPostType;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapCptArchiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_cpt_sitemap_respects_has_archive_setting(): void
    {
        Setting::set('seo_sitemap_enabled', true, 'seo', 'boolean');

        /** @var CustomPostType $cpt */
        $cpt = CustomPostType::create([
            'name' => 'Technology Alliance',
            'singular_label' => 'Technology Alliance',
            'plural_label' => 'Technology Alliances',
            'slug' => 'technology-alliance',
            'has_archive' => true,
            'publicly_queryable' => true,
            'is_active' => true,
        ]);

        // 1. When has_archive is true, sitemap should contain archive URL
        $response = $this->get('/technology-alliance-sitemap.xml');
        $response->assertStatus(200);
        $response->assertSee(url('/technology-alliance'));

        // 2. When has_archive is set to false, sitemap should NOT contain archive URL
        $cpt->update(['has_archive' => false]);

        $response2 = $this->get('/technology-alliance-sitemap.xml');
        $response2->assertStatus(200);
        $response2->assertDontSee(url('/technology-alliance'));
    }
}
