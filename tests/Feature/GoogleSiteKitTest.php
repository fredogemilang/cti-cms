<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Services\ThemeLoader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Plugins\GoogleSiteKit\Providers\GoogleSiteKitServiceProvider;
use Plugins\GoogleSiteKit\Services\GoogleApiService;
use Tests\TestCase;

class GoogleSiteKitTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Manually register the namespace in Composer's autoloader for the test environment
        $loader = require base_path('vendor/autoload.php');
        $loader->addPsr4('Plugins\\GoogleSiteKit\\', base_path('plugins/google-site-kit/src'));

        // 2. Manually register the plugin provider since RefreshDatabase clears the active plugin DB record during app boot
        app()->register(GoogleSiteKitServiceProvider::class);

        // 3. Seed default theme
        \DB::table('themes')->updateOrInsert(
            ['slug' => 'default'],
            [
                'name' => 'Default',
                'version' => '1.0.0',
                'description' => 'A clean, modern default theme for the Web CMS.',
                'author' => 'Web CMS',
                'is_active' => true,
                'supports' => json_encode(['pages', 'posts', 'menus']),
                'installed_at' => now(),
                'activated_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 4. Force reboot ThemeLoader so the views and namespaces are registered in the test container
        app(ThemeLoader::class)->boot();

        // 5. Refresh route name lookups to ensure the newly loaded plugin routes are cached
        app('router')->getRoutes()->refreshNameLookups();

        $this->user = User::factory()->create();
    }

    #[Test]
    public function tracking_snippets_are_injected_when_configured(): void
    {
        // 1. Configure tracking IDs
        Setting::set('gsk_enabled', true, 'google-site-kit', 'boolean');
        Setting::set('gsk_ga4_tag_id', 'G-TEST123456', 'google-site-kit', 'string');
        Setting::set('gsk_gtm_id', 'GTM-TEST789', 'google-site-kit', 'string');
        Setting::set('gsk_ads_id', 'AW-TEST555', 'google-site-kit', 'string');

        // Create a homepage with valid author
        \DB::table('pages')->insert([
            'title' => 'Home',
            'slug' => 'home',
            'status' => 'published',
            'template' => 'default',
            'author_id' => $this->user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Get root URL and verify scripts are in output
        $response = $this->get('/');
        $response->assertStatus(200);

        $html = $response->getContent();
        $this->assertStringContainsString('https://www.googletagmanager.com/gtag/js?id=G-TEST123456', $html);
        $this->assertStringContainsString('GTM-TEST789', $html);
        $this->assertStringContainsString('https://www.googletagmanager.com/gtag/js?id=AW-TEST555', $html);
    }

    #[Test]
    public function tracking_snippets_are_hidden_when_disabled(): void
    {
        Setting::set('gsk_enabled', false, 'google-site-kit', 'boolean');
        Setting::set('gsk_ga4_tag_id', 'G-TEST123456', 'google-site-kit', 'string');

        \DB::table('pages')->insert([
            'title' => 'Home',
            'slug' => 'home',
            'status' => 'published',
            'template' => 'default',
            'author_id' => $this->user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        $html = $response->getContent();
        $this->assertStringNotContainsString('G-TEST123456', $html);
    }

    #[Test]
    public function auth_url_requires_client_id(): void
    {
        $api = app(GoogleApiService::class);

        // No client ID configured
        Setting::set('gsk_client_id', '', 'google-site-kit', 'string');
        $this->assertEmpty($api->getAuthUrl());

        // With client ID
        Setting::set('gsk_client_id', 'client-id-xyz', 'google-site-kit', 'string');
        $url = $api->getAuthUrl();
        $this->assertStringContainsString('client_id=client-id-xyz', $url);
        $this->assertStringContainsString('scope=', $url);
    }

    #[Test]
    public function api_returns_mock_data_when_disconnected(): void
    {
        $api = app(GoogleApiService::class);
        $this->assertFalse($api->isConnected());

        $scData = $api->getSearchConsoleData();
        $this->assertArrayHasKey('clicks', $scData);
        $this->assertArrayHasKey('chart', $scData);
        $this->assertGreaterThan(0, $scData['clicks']);

        $gaData = $api->getAnalyticsData();
        $this->assertArrayHasKey('users', $gaData);
        $this->assertArrayHasKey('chart', $gaData);
        $this->assertGreaterThan(0, $gaData['users']);
    }
}
