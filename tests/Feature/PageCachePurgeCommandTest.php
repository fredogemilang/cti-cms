<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Settings\Actions\PurgePageCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PageCachePurgeCommandTest extends TestCase
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

        Route::middleware('web')->get('/purge-cmd-test/page', function () {
            return response(str_repeat('z', 300));
        });
    }

    #[Test]
    public function artisan_command_purges_page_cache(): void
    {
        $this->get('/purge-cmd-test/page');
        $this->get('/purge-cmd-test/page')->assertHeader('X-Page-Cache', 'HIT');

        $this->artisan('page-cache:purge')->assertSuccessful();

        $this->get('/purge-cmd-test/page')->assertHeader('X-Page-Cache', 'MISS');
    }

    #[Test]
    public function settings_action_returns_success_and_bumps_version(): void
    {
        $version = (int) Cache::get('page-cache:version', 1);

        $result = (new PurgePageCache)->handle([]);

        $this->assertSame('success', $result['type']);
        $this->assertSame($version + 1, (int) Cache::get('page-cache:version'));
    }
}
