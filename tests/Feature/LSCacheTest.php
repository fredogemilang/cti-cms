<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Services\CacheManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LSCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        Setting::resetEncryptedKeyCache();
        Setting::set('page_cache_enabled', true, 'cache', 'boolean');
        Setting::set('page_cache_ttl', 3600, 'cache', 'integer');
        Setting::flushMemo();

        Route::middleware('web')->get('/lscache-test/page', function () {
            return response('<html><body>Hello LSCache</body></html>')->header('Content-Type', 'text/html; charset=UTF-8');
        });

        Route::middleware('web')->post('/lscache-test/page', function () {
            return response('<html><body>Submitted</body></html>')->header('Content-Type', 'text/html; charset=UTF-8');
        });
    }

    #[Test]
    public function lscache_headers_middleware_sets_cache_control_and_tag_on_public_get(): void
    {
        $response = $this->get('/lscache-test/page');

        $response->assertHeader('X-LiteSpeed-Cache-Control', 'public,max-age=3600');
        $response->assertHeader('X-LiteSpeed-Tag', 'public,route:lscache-test');
    }

    #[Test]
    public function lscache_headers_sets_no_cache_on_post_requests(): void
    {
        $response = $this->post('/lscache-test/page');

        $response->assertHeader('X-LiteSpeed-Cache-Control', 'no-cache');
        $response->assertHeaderMissing('X-LiteSpeed-Tag');
    }

    #[Test]
    public function lscache_headers_sets_no_cache_on_authenticated_requests(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/lscache-test/page');

        $response->assertHeader('X-LiteSpeed-Cache-Control', 'no-cache');
        $response->assertHeaderMissing('X-LiteSpeed-Tag');
    }

    #[Test]
    public function lscache_headers_sets_no_cache_when_cache_setting_is_disabled(): void
    {
        Setting::set('page_cache_enabled', false, 'cache', 'boolean');
        Setting::flushMemo();
        Cache::forget('setting:page_cache_enabled');

        $response = $this->get('/lscache-test/page');

        $response->assertHeader('X-LiteSpeed-Cache-Control', 'no-cache');
        $response->assertHeaderMissing('X-LiteSpeed-Tag');
    }

    #[Test]
    public function lscache_headers_sets_no_cache_on_excluded_paths(): void
    {
        Setting::set('page_cache_excluded_paths', '/lscache-test/*', 'cache', 'string');
        Setting::flushMemo();
        Cache::forget('setting:page_cache_excluded_paths');

        $response = $this->get('/lscache-test/page');

        $response->assertHeader('X-LiteSpeed-Cache-Control', 'no-cache');
        $response->assertHeaderMissing('X-LiteSpeed-Tag');
    }

    #[Test]
    public function cache_manager_purge_all_executes_safely_and_invalidates_page_cache(): void
    {
        $version = (int) Cache::get('page-cache:version', 1);

        CacheManager::purgeAll();

        $this->assertSame($version + 1, (int) Cache::get('page-cache:version'));
    }

    #[Test]
    public function cache_manager_purge_tag_executes_safely(): void
    {
        $version = (int) Cache::get('page-cache:version', 1);

        CacheManager::purgeTag('home');

        // On non-LiteSpeed (testing environment), purgeTag falls back to PageCache::purgeAll
        $this->assertSame($version + 1, (int) Cache::get('page-cache:version'));
    }

    #[Test]
    public function cache_settings_group_registers_info_field_without_validation_rules(): void
    {
        $registry = app(\App\Services\SettingsRegistry::class);
        $fields = $registry->fields('cache');

        $infoField = collect($fields)->firstWhere('key', '_cache_mode_info');
        $this->assertNotNull($infoField);
        $this->assertSame('info', $infoField['type']);

        // Defaults and rules should not contain the info field
        $defaults = $registry->defaults('cache');
        $rules = $registry->rules('cache');

        $this->assertArrayNotHasKey('_cache_mode_info', $defaults);
        $this->assertArrayNotHasKey('_cache_mode_info', $rules);
    }
}
