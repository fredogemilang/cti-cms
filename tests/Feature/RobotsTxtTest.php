<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RobotsTxtTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_txt_returns_200_and_default_directives(): void
    {
        Setting::set('seo_allow_indexing', true, 'seo', 'boolean');
        Setting::set('seo_sitemap_enabled', true, 'seo', 'boolean');

        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('User-agent: *');
        $response->assertSee('Sitemap: '.url('/sitemap.xml'));
    }

    public function test_robots_txt_disallows_all_when_indexing_disabled(): void
    {
        Setting::set('seo_allow_indexing', false, 'seo', 'boolean');

        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $response->assertSee('Disallow: /');
    }

    public function test_robots_txt_includes_custom_extra_directives(): void
    {
        Setting::set('seo_allow_indexing', true, 'seo', 'boolean');
        Setting::set('seo_robots_extra', "User-agent: BadBot\nDisallow: /private/", 'seo', 'string');

        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $response->assertSee('User-agent: BadBot');
        $response->assertSee('Disallow: /private/');
    }
}
