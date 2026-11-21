<?php

namespace Tests\Unit;

use App\Services\CaptchaService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CaptchaServiceTest extends TestCase
{
    #[Test]
    public function recaptcha_v2_returns_empty_when_site_key_is_not_configured(): void
    {
        config(['services.recaptcha.site_key' => '']);

        $service = new CaptchaService;

        $this->assertSame('', $service->renderWidget('recaptcha_v2'));
    }

    #[Test]
    public function recaptcha_v2_lazy_loads_script_without_eager_script_tag(): void
    {
        config(['services.recaptcha.site_key' => 'test-site-key-123']);

        $html = (new CaptchaService)->renderWidget('recaptcha_v2');

        $this->assertStringContainsString('class="g-recaptcha"', $html);
        $this->assertStringContainsString('data-sitekey="test-site-key-123"', $html);
        $this->assertStringContainsString('min-height:78px', $html);
        $this->assertStringContainsString('IntersectionObserver', $html);
        $this->assertStringNotContainsString('<script src="https://www.google.com/recaptcha/api.js"', $html);
    }
}
