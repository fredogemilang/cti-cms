<?php

namespace Tests\Feature;

use App\Contracts\SchemaProviderInterface;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use App\Services\Schema\SchemaRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExtensibleSchemaAggregatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_schema_manifest_endpoint_returns_data_catalog_index(): void
    {
        $response = $this->get('/schema-manifest.json');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json; charset=utf-8');
        $this->assertNotEmpty($response->headers->get('ETag'));

        $data = $response->json();
        $this->assertEquals('DataCatalog', $data['@type']);
        $this->assertEquals('CTI CMS 2.0 AI Engine', $data['generator']);
        $this->assertArrayHasKey('organization', $data['collections']);
        $this->assertArrayHasKey('pages', $data['collections']);
    }

    public function test_custom_schema_provider_registration(): void
    {
        /** @var SchemaRegistry $registry */
        $registry = app(SchemaRegistry::class);

        $mockProvider = new class implements SchemaProviderInterface
        {
            public function getIdentifier(): string
            {
                return 'products';
            }

            public function getLabel(): string
            {
                return 'Store Products';
            }

            public function getNodes(?string $since = null, int $page = 1, int $perPage = 50): array
            {
                return [
                    [
                        '@type' => 'Product',
                        'name' => 'Test Product',
                    ],
                ];
            }

            public function getTotalCount(?string $since = null): int
            {
                return 1;
            }

            public function getMetadata(): array
            {
                return ['plugin' => 'ecommerce'];
            }
        };

        $registry->registerProvider($mockProvider);

        $response = $this->get('/schema/products');
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Test Product']);
    }

    public function test_etag_returns_304_not_modified_when_if_none_match_matches(): void
    {
        $firstResponse = $this->get('/schema.json');
        $etag = $firstResponse->headers->get('ETag');

        $this->assertNotEmpty($etag);

        $secondResponse = $this->withHeaders([
            'If-None-Match' => $etag,
        ])->get('/schema.json');

        $secondResponse->assertStatus(304);
    }

    public function test_incremental_sync_filters_by_since_date(): void
    {
        $user = User::factory()->create();

        Page::create([
            'title' => 'Recent Page',
            'slug' => 'recent-page',
            'status' => 'published',
            'author_id' => $user->id,
            'updated_at' => now(),
        ]);

        $sinceDate = urlencode(now()->subDays(1)->toIso8601String());
        $response = $this->get('/schema/pages?since='.$sinceDate);

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Recent Page']);
    }

    public function test_partitioned_collection_pagination(): void
    {
        $user = User::factory()->create();
        for ($i = 1; $i <= 5; $i++) {
            Page::create([
                'title' => "Page {$i}",
                'slug' => "page-{$i}",
                'status' => 'published',
                'author_id' => $user->id,
            ]);
        }

        $response = $this->get('/schema/pages?page=1&per_page=2');
        $response->assertStatus(200);

        $data = $response->json();
        $this->assertEquals(1, $data['page']);
        $this->assertEquals(2, $data['perPage']);
        $this->assertEquals(5, $data['totalItems']);
        $this->assertCount(2, $data['@graph']);
    }

    public function test_llms_txt_renders_dynamic_ai_resources(): void
    {
        Setting::set('seo_llms_enabled', true, 'seo', 'boolean');

        $response = $this->get('/llms.txt');

        $response->assertStatus(200);
        $response->assertSee('## AI Resources & Data Endpoints', false);
        $response->assertSee('/schema-manifest.json');
        $response->assertSee('/schema.json');
    }
}
