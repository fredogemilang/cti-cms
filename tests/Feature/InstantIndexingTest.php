<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Services\GoogleIndexingService;
use App\Services\IndexNowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class InstantIndexingTest extends TestCase
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

    public function test_google_indexing_service_saves_and_decrypts_credentials(): void
    {
        /** @var GoogleIndexingService $service */
        $service = app(GoogleIndexingService::class);

        $fakeJson = json_encode([
            'client_email' => 'test-bot@project.iam.gserviceaccount.com',
            'private_key' => '-----BEGIN PRIVATE KEY-----\nTESTKEY\n-----END PRIVATE KEY-----',
        ]);

        $saved = $service->saveJsonCredentials((string) $fakeJson);
        $this->assertTrue($saved);

        $creds = $service->resolveCredentials();
        $this->assertEquals('test-bot@project.iam.gserviceaccount.com', $creds['client_email']);
        $this->assertStringContainsString('TESTKEY', $creds['private_key']);
    }

    public function test_manual_batch_submission_logs_activity(): void
    {
        Http::fake([
            'https://api.indexnow.org/indexnow' => Http::response(null, 200),
            'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'fake_access_token', 'expires_in' => 3600], 200),
            'https://indexing.googleapis.com/v3/urlNotifications:publish' => Http::response(null, 200),
        ]);

        /** @var IndexNowService $indexNow */
        $indexNow = app(IndexNowService::class);
        $ok = $indexNow->submitUrls(['https://example.com/page-1']);

        $this->assertTrue($ok);
        $this->assertDatabaseHas('indexing_logs', [
            'protocol' => 'indexnow',
            'url' => 'https://example.com/page-1',
            'status_code' => 200,
        ]);
    }

    public function test_page_saved_event_triggers_instant_indexing(): void
    {
        Http::fake([
            'https://api.indexnow.org/indexnow' => Http::response(null, 200),
            'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'fake_token'], 200),
            'https://indexing.googleapis.com/v3/urlNotifications:publish' => Http::response(null, 200),
        ]);

        Setting::set('seo_indexnow_enabled', true, 'seo', 'boolean');
        Setting::set('seo_indexnow_auto_ping', true, 'seo', 'boolean');

        $user = User::factory()->create();
        Page::create([
            'title' => 'Instant Indexing Page',
            'slug' => 'instant-indexing-page',
            'status' => 'published',
            'author_id' => $user->id,
        ]);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.indexnow.org/indexnow'
                && in_array(url('/instant-indexing-page'), $request['urlList'], true);
        });
    }

    public function test_authorized_user_can_access_instant_indexing_suite_page(): void
    {
        $user = User::factory()->create();
        $role = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'is_super_admin' => true,
        ]);
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user)->get('/ctrlpanel/seo/indexnow');

        $response->assertStatus(200);
        $response->assertSeeLivewire('admin.seo.seo-index-now');
    }
}
