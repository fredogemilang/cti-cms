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

    #[Test]
    public function api_provides_full_dashboard_suite_metrics(): void
    {
        $api = app(GoogleApiService::class);

        // 1. Search Funnel
        $funnel = $api->getSearchFunnel('28days');
        $this->assertArrayHasKey('impressions', $funnel);
        $this->assertArrayHasKey('clicks', $funnel);
        $this->assertArrayHasKey('ctr', $funnel);
        $this->assertArrayHasKey('position', $funnel);
        $this->assertArrayHasKey('visitors', $funnel);
        $this->assertArrayHasKey('chart', $funnel);
        $this->assertCount(28, $funnel['chart']);

        // 2. 7 days funnel
        $funnel7 = $api->getSearchFunnel('7days');
        $this->assertCount(7, $funnel7['chart']);

        // 3. Top Queries & Filtering
        $queries = $api->getTopQueries('28days', 10);
        $this->assertNotEmpty($queries);
        $this->assertArrayHasKey('query', $queries[0]);
        $this->assertArrayHasKey('clicks', $queries[0]);

        $filteredQueries = $api->getTopQueries('28days', 10, 'oracle');
        $this->assertNotEmpty($filteredQueries);
        $this->assertStringContainsStringIgnoringCase('oracle', $filteredQueries[0]['query']);

        // 4. Top Pages
        $pages = $api->getTopPages('28days', 5);
        $this->assertCount(5, $pages);
        $this->assertArrayHasKey('title', $pages[0]);
        $this->assertArrayHasKey('path', $pages[0]);
        $this->assertArrayHasKey('pageviews', $pages[0]);

        // 5. Traffic Channels
        $channels = $api->getTrafficChannels('28days');
        $this->assertArrayHasKey('channels', $channels);
        $this->assertNotEmpty($channels['channels']);

        // 6. Devices & Location
        $devices = $api->getDeviceAndLocationStats('28days');
        $this->assertArrayHasKey('devices', $devices);
        $this->assertArrayHasKey('countries', $devices);

        // 7. Detailed PageSpeed & Core Web Vitals
        $speed = $api->getDetailedPageSpeed();
        $this->assertArrayHasKey('mobile', $speed);
        $this->assertArrayHasKey('desktop', $speed);
        $this->assertArrayHasKey('vitals', $speed);
        $this->assertArrayHasKey('lcp', $speed['vitals']);
        $this->assertArrayHasKey('inp', $speed['vitals']);
        $this->assertArrayHasKey('cls', $speed['vitals']);
        $this->assertArrayHasKey('fcp', $speed['vitals']);
        $this->assertArrayHasKey('tbt', $speed['vitals']);
    }

    #[Test]
    public function dashboard_livewire_component_renders_and_switches_date_ranges(): void
    {
        \Livewire\Livewire::test(\Plugins\GoogleSiteKit\Livewire\Dashboard::class)
            ->assertSee('Google Site Kit Dashboard')
            ->assertSee('Search Funnel')
            ->assertSee('Top Search Queries')
            ->assertSee('Most Popular Content')
            ->assertSee('Traffic Acquisition Channels')
            ->assertSee('Core Web Vitals Assessment')
            ->call('changeDateRange', '7days')
            ->assertSet('dateRange', '7days')
            ->call('changeDateRange', '90days')
            ->assertSet('dateRange', '90days')
            ->set('searchQuery', 'cloud')
            ->assertSee('cloud');
    }
}
