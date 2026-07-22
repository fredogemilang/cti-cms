<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use App\Services\IndexNowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IndexNowTest extends TestCase
{
    use RefreshDatabase;

    public function test_indexnow_key_endpoint_returns_plain_text_key(): void
    {
        /** @var IndexNowService $service */
        $service = app(IndexNowService::class);
        $key = $service->getKey();

        $response = $this->get('/indexnow-'.$key.'.txt');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee($key);
    }

    public function test_indexnow_key_endpoint_returns_404_for_invalid_key(): void
    {
        $response = $this->get('/indexnow-invalid-key-999.txt');

        $response->assertStatus(404);
    }

    public function test_indexnow_service_submits_urls_via_http_post(): void
    {
        Http::fake([
            'https://api.indexnow.org/indexnow' => Http::response(null, 200),
        ]);

        /** @var IndexNowService $service */
        $service = app(IndexNowService::class);
        $success = $service->submitUrls(['https://example.com/test-page']);

        $this->assertTrue($success);
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.indexnow.org/indexnow'
                && isset($request['urlList'])
                && $request['urlList'][0] === 'https://example.com/test-page';
        });
    }

    public function test_page_saved_event_triggers_indexnow_ping(): void
    {
        Http::fake([
            'https://api.indexnow.org/indexnow' => Http::response(null, 200),
        ]);

        Setting::set('seo_indexnow_enabled', true, 'seo', 'boolean');
        Setting::set('seo_indexnow_auto_ping', true, 'seo', 'boolean');

        $user = User::factory()->create();
        Page::create([
            'title' => 'IndexNow Test Page',
            'slug' => 'indexnow-test-page',
            'status' => 'published',
            'author_id' => $user->id,
        ]);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.indexnow.org/indexnow'
                && in_array(url('/indexnow-test-page'), $request['urlList'], true);
        });
    }
}
