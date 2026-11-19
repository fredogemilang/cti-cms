<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Setting;
use App\Services\ContentTypeRegistry;
use App\Services\SeoRenderer;
use App\Services\TaxonomyRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoContentTypeTaxonomyTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_type_and_taxonomy_registries_return_active_types(): void
    {
        $ctRegistry = app(ContentTypeRegistry::class);
        $taxRegistry = app(TaxonomyRegistry::class);

        $this->assertTrue($ctRegistry->has('pages'));
        $this->assertTrue($ctRegistry->has('posts'));

        $this->assertTrue($taxRegistry->has('categories'));
        $this->assertTrue($taxRegistry->has('tags'));
    }

    public function test_seo_renderer_uses_content_type_indexing_and_title_patterns(): void
    {
        Setting::set('seo_content_type_pages_index_enabled', false, 'seo', 'boolean');
        Setting::set('seo_content_type_pages_title_pattern', '{title} Custom Page Pattern {sep} {site}', 'seo', 'text');
        Setting::set('site_name', 'My Test Site', 'general', 'text');

        $page = new Page([
            'title' => 'About Us',
            'slug' => 'about-us',
            'status' => 'published',
        ]);

        /** @var SeoRenderer $renderer */
        $renderer = app(SeoRenderer::class);
        $seo = $renderer->resolve($page);

        $this->assertEquals('noindex,follow', $seo['robots']);
        $this->assertStringContainsString('About Us Custom Page Pattern - My Test Site', $seo['title']);
    }

    public function test_sitemap_excludes_disabled_content_types(): void
    {
        Setting::set('seo_sitemap_enabled', true, 'seo', 'boolean');
        Setting::set('seo_content_type_pages_index_enabled', false, 'seo', 'boolean');

        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);

        // When pages index_enabled is false, pages are excluded from sitemap
        $this->assertStringNotContainsString('<loc>', $response->getContent());
    }
}
