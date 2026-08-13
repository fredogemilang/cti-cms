<?php

namespace Tests\Feature;

use App\Jobs\WarmCacheJob;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use App\Settings\Actions\WarmPageCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CacheWarmingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::resetEncryptedKeyCache();
        Setting::set('page_cache_enabled', true, 'cache', 'boolean');
        Setting::set('page_cache_warm_on_save', true, 'cache', 'boolean');
        Setting::set('page_cache_warm_concurrency', 5, 'cache', 'integer');
        Setting::flushMemo();
    }

    #[Test]
    public function warm_cache_job_sends_http_get_requests_and_reports_metrics(): void
    {
        Http::fake([
            url('/test-page-1') => Http::response('<html>OK 1</html>', 200),
            url('/test-page-2') => Http::response('<html>OK 2</html>', 200),
        ]);

        $job = new WarmCacheJob(['/test-page-1', '/test-page-2']);
        $result = $job->handle();

        $this->assertSame(2, $result['total']);
        $this->assertSame(2, $result['warmed']);
        $this->assertSame(0, $result['failed']);

        Http::assertSent(function ($request) {
            return str_contains($request->header('User-Agent')[0] ?? '', 'CdtCacheCrawler');
        });
    }

    #[Test]
    public function warm_cache_job_skips_excluded_and_admin_paths(): void
    {
        Setting::set('page_cache_excluded_paths', "/forms/*\n/cart", 'cache', 'string');
        Setting::flushMemo();
        \Illuminate\Support\Facades\Cache::flush();

        Http::fake([
            url('/allowed-page') => Http::response('<html>OK</html>', 200),
        ]);

        $job = new WarmCacheJob([
            '/allowed-page',
            '/forms/contact',
            '/cart',
            '/admin/dashboard',
        ]);
        $result = $job->handle();

        $this->assertSame(1, $result['total']);
        $this->assertSame(1, $result['warmed']);

        Http::assertSent(fn ($req) => $req->url() === url('/allowed-page'));
        Http::assertNotSent(fn ($req) => $req->url() === url('/forms/contact'));
        Http::assertNotSent(fn ($req) => $req->url() === url('/cart'));
        Http::assertNotSent(fn ($req) => $req->url() === url('/admin/dashboard'));
    }

    #[Test]
    public function warm_cache_job_skips_when_page_cache_is_disabled(): void
    {
        Setting::set('page_cache_enabled', false, 'cache', 'boolean');
        Setting::flushMemo();

        Http::fake();

        $job = new WarmCacheJob(['/some-page']);
        $result = $job->handle();

        $this->assertSame(0, $result['total']);
        $this->assertSame(0, $result['warmed']);
        Http::assertNothingSent();
    }

    #[Test]
    public function warm_cache_command_runs_successfully(): void
    {
        Http::fake([
            url('/custom-path') => Http::response('<html>OK</html>', 200),
        ]);

        $this->artisan('page-cache:warm', ['--url' => '/custom-path'])
            ->expectsOutputToContain('Cache successfully warmed')
            ->assertSuccessful();
    }

    #[Test]
    public function purge_command_with_warm_option_triggers_warming(): void
    {
        Http::fake([
            url('/') => Http::response('<html>OK</html>', 200),
        ]);

        $this->artisan('page-cache:purge', ['--warm' => true])
            ->assertSuccessful();
    }

    #[Test]
    public function warm_page_cache_settings_action_returns_success_notification(): void
    {
        Http::fake([
            url('/') => Http::response('<html>OK</html>', 200),
        ]);

        $action = new WarmPageCache();
        $response = $action->handle(['page_cache_warm_concurrency' => 3]);

        $this->assertSame('success', $response['type']);
        $this->assertStringContainsString('Cache warming complete', $response['message']);
    }

    #[Test]
    public function saving_published_page_dispatches_targeted_warm_cache_job(): void
    {
        Queue::fake([WarmCacheJob::class]);

        $user = User::factory()->create();
        $page = Page::create([
            'title' => 'About Our Company',
            'slug' => 'about-us',
            'content' => 'Content here',
            'status' => 'published',
            'author_id' => $user->id,
        ]);

        Queue::assertPushed(WarmCacheJob::class, function ($job) {
            return is_array($job->urls) && in_array('/', $job->urls);
        });
    }
}
