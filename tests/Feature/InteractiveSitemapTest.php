<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InteractiveSitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_main_sitemap_xsl_returns_valid_xml_stylesheet(): void
    {
        $response = $this->get('/main-sitemap.xsl');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<xsl:stylesheet', false);
        $response->assertSee('CTI CMS Interactive XML Sitemap');
        $response->assertSee('AI &amp; Open Web Hub', false);
    }

    public function test_sitemap_index_returns_xml_with_stylesheet_instruction(): void
    {
        Setting::set('seo_sitemap_enabled', true, 'seo', 'boolean');

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<?xml-stylesheet type="text/xsl" href="', false);
        $response->assertSee('/main-sitemap.xsl', false);
        $response->assertSee('<sitemapindex', false);
    }

    public function test_page_sitemap_returns_urlset(): void
    {
        Setting::set('seo_sitemap_enabled', true, 'seo', 'boolean');
        Setting::set('seo_content_type_pages_index_enabled', true, 'seo', 'boolean');

        $user = User::factory()->create();
        Page::create([
            'title' => 'Sitemap Test Page',
            'slug' => 'sitemap-test-page',
            'status' => 'published',
            'author_id' => $user->id,
        ]);

        $response = $this->get('/page-sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<urlset', false);
        $response->assertSee('/sitemap-test-page');
    }
}
