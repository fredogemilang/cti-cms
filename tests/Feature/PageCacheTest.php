<?php

namespace Tests\Feature;

use App\Http\Middleware\PageCache;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PageCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Array cache store persists across tests in the same process — reset it
        // so page cache entries and the version key never leak between tests.
        Cache::flush();
        Setting::resetEncryptedKeyCache();
        Setting::set('page_cache_enabled', true, 'cache', 'boolean');
        // TTL must be set explicitly: an unset ttl reads as (int) null = 0, and
        // Cache::put($key, $value, 0) on the array store expires instantly.
        Setting::set('page_cache_ttl', 3600, 'cache', 'integer');
        Setting::flushMemo();

        // Test route (avoids Theme/Page bootstrap which is singleton-bound).
        Route::middleware('web')->get('/cache-test/page', function () {
            return response(str_repeat('x', 500))->header('Content-Type', 'text/html; charset=UTF-8');
        });
    }

    #[Test]
    public function first_request_misses_then_second_hits(): void
    {
        $this->get('/cache-test/page')->assertHeader('X-Page-Cache', 'MISS');

        $this->get('/cache-test/page')
            ->assertHeader('X-Page-Cache', 'HIT')
            ->assertHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    #[Test]
    public function purge_all_bumps_version_and_invalidates(): void
    {
        $this->get('/cache-test/page');
        $this->get('/cache-test/page')->assertHeader('X-Page-Cache', 'HIT');

        $version = (int) Cache::get('page-cache:version', 1);
        PageCache::purgeAll();

        $this->assertSame($version + 1, (int) Cache::get('page-cache:version'));
        $this->get('/cache-test/page')->assertHeader('X-Page-Cache', 'MISS');
    }

    #[Test]
    public function excluded_paths_are_not_cached(): void
    {
        Setting::set('page_cache_excluded_paths', '/cache-test/page', 'cache', 'string');
        Setting::flushMemo();
        Cache::forget('setting:page_cache_excluded_paths');

        $this->get('/cache-test/page')->assertHeaderMissing('X-Page-Cache');
    }

    #[Test]
    public function post_requests_are_not_cached(): void
    {
        $this->post('/cache-test/page')->assertHeaderMissing('X-Page-Cache');
    }

    #[Test]
    public function authenticated_requests_are_not_cached(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/cache-test/page')->assertHeaderMissing('X-Page-Cache');
    }
}
