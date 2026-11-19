<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LlmsTxtTest extends TestCase
{
    use RefreshDatabase;

    public function test_llms_txt_returns_200_when_enabled(): void
    {
        Setting::set('seo_llms_enabled', true, 'seo', 'boolean');
        Setting::set('seo_ai_summary', 'Test company expertise summary', 'seo', 'textarea');

        $response = $this->get('/llms.txt');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('Test company expertise summary');
        $response->assertSee('## About');
    }

    public function test_llms_txt_returns_404_when_disabled(): void
    {
        Setting::set('seo_llms_enabled', false, 'seo', 'boolean');

        $response = $this->get('/llms.txt');

        $response->assertStatus(404);
    }
}
