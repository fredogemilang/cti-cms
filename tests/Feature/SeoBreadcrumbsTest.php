<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use App\Services\BreadcrumbService;
use App\View\Components\SeoBreadcrumbs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoBreadcrumbsTest extends TestCase
{
    use RefreshDatabase;

    public function test_breadcrumb_service_generates_correct_hierarchy(): void
    {
        Setting::set('seo_breadcrumb_home_text', 'Beranda', 'seo', 'text');

        $page = new Page([
            'title' => 'Kontak Kami',
            'slug' => 'contact',
            'status' => 'published',
        ]);

        /** @var BreadcrumbService $service */
        $service = app(BreadcrumbService::class);
        $items = $service->getItems($page);

        $this->assertCount(2, $items);
        $this->assertEquals('Beranda', $items[0]['name']);
        $this->assertEquals('Kontak Kami', $items[1]['name']);
    }

    public function test_seo_breadcrumbs_component_renders_html(): void
    {
        Setting::set('seo_breadcrumbs_enabled', true, 'seo', 'boolean');
        Setting::set('seo_breadcrumb_separator', '»', 'seo', 'text');
        Setting::set('seo_breadcrumb_home_text', 'Home', 'seo', 'text');

        $page = new Page([
            'title' => 'Services',
            'slug' => 'services',
            'status' => 'published',
        ]);

        $view = $this->component(SeoBreadcrumbs::class, ['entity' => $page]);

        $view->assertSee('Home');
        $view->assertSee('»');
        $view->assertSee('Services');
    }

    public function test_public_page_response_contains_breadcrumb_json_ld_schema(): void
    {
        Setting::set('seo_breadcrumbs_enabled', true, 'seo', 'boolean');
        Setting::set('seo_breadcrumb_home_text', 'Home', 'seo', 'text');

        $user = User::factory()->create();
        Page::create([
            'title' => 'About Us',
            'slug' => 'about',
            'status' => 'published',
            'author_id' => $user->id,
        ]);

        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('BreadcrumbList', false);
        $response->assertSee('About Us', false);
    }
}
