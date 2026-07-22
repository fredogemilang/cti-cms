<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use App\Services\SchemaAggregatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchemaAggregatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_schema_json_endpoint_returns_valid_json_ld_graph(): void
    {
        $user = User::factory()->create();
        Page::create([
            'title' => 'Test Schema Page',
            'slug' => 'test-schema-page',
            'status' => 'published',
            'author_id' => $user->id,
        ]);

        $response = $this->get('/schema.json');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/ld+json; charset=utf-8');

        $data = $response->json();
        $this->assertEquals('https://schema.org', $data['@context']);
        $this->assertIsArray($data['@graph']);

        $types = array_column($data['@graph'], '@type');
        $this->assertContains('Organization', $types);
        $this->assertContains('WebSite', $types);
        $this->assertContains('WebPage', $types);
    }

    public function test_schema_json_supports_jsonl_streaming_format(): void
    {
        $response = $this->get('/schema.json?format=jsonl');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/x-jsonlines; charset=utf-8');
    }

    public function test_llms_txt_includes_schema_graph_reference(): void
    {
        Setting::set('seo_llms_enabled', true, 'seo', 'boolean');

        $response = $this->get('/llms.txt');

        $response->assertStatus(200);
        $response->assertSee('Schema Graph:');
        $response->assertSee('/schema.json');
    }

    public function test_schema_cache_is_cleared_on_page_saved(): void
    {
        /** @var SchemaAggregatorService $service */
        $service = app(SchemaAggregatorService::class);
        $service->getAggregatedGraph();

        $this->assertTrue(cache()->has(SchemaAggregatorService::CACHE_KEY));

        $user = User::factory()->create();
        Page::create([
            'title' => 'New Page Clear Cache',
            'slug' => 'new-page-clear-cache',
            'status' => 'published',
            'author_id' => $user->id,
        ]);

        $this->assertFalse(cache()->has(SchemaAggregatorService::CACHE_KEY));
    }
}
