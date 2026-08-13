<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Cached pages embed the cache-writer's `_token`; the theme JS re-stamps the
 * hidden input from the visitor's own XSRF-TOKEN cookie before submitting.
 * For that to work, the cookie must be RAW (excluded from cookie encryption)
 * and must be re-issued per visitor — including on page-cache HIT responses.
 */
class CsrfDoubleSubmitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        Setting::resetEncryptedKeyCache();

        Route::middleware('web')->get('/csrf-cookie-test/page', function () {
            return response(str_repeat('y', 300));
        });
    }

    #[Test]
    public function xsrf_token_cookie_is_raw_and_matches_session_token(): void
    {
        $response = $this->get('/csrf-cookie-test/page');

        $cookie = collect($response->headers->getCookies())
            ->first(fn ($c) => $c->getName() === 'XSRF-TOKEN');

        $this->assertNotNull($cookie, 'XSRF-TOKEN cookie should be present on web responses');
        $this->assertSame(session()->token(), $cookie->getValue());
        $this->assertStringNotContainsString('eyJpdiI', $cookie->getValue());
    }

    #[Test]
    public function xsrf_token_cookie_is_reissued_on_cache_hits(): void
    {
        Setting::set('page_cache_enabled', true, 'cache', 'boolean');
        Setting::set('page_cache_ttl', 3600, 'cache', 'integer');
        Setting::flushMemo();

        $this->get('/csrf-cookie-test/page');
        $hit = $this->get('/csrf-cookie-test/page');
        $hit->assertHeader('X-Page-Cache', 'HIT');

        // Even on HIT the visitor gets THEIR OWN raw XSRF-TOKEN cookie —
        // this is what makes the double-submit re-stamp work.
        $cookie = collect($hit->headers->getCookies())
            ->first(fn ($c) => $c->getName() === 'XSRF-TOKEN');

        $this->assertNotNull($cookie, 'XSRF-TOKEN cookie should be re-issued on cache HITs');
        $this->assertSame(session()->token(), $cookie->getValue());
    }
}
