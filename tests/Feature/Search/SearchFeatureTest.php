<?php

namespace Tests\Feature\Search;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\SearchIndex;
use App\Models\User;
use App\Services\SearchIndexer;
use App\Services\SearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_page_is_indexed_on_creation_when_published(): void
    {
        $page = Page::create([
            'title' => 'Multi-Cloud Architecture',
            'slug' => 'multicloud-architecture',
            'status' => 'published',
            'author_id' => $this->user->id,
            'translations' => [
                'id' => [
                    'title' => 'Arsitektur Multi-Cloud',
                ],
            ],
        ]);

        PageBlock::create([
            'page_id' => $page->id,
            'name' => 'body',
            'type' => 'text',
            'value' => 'Enterprise resilience and disaster recovery.',
            'translations' => [
                'id' => [
                    'value' => 'Ketahanan enterprise dan pemulihan bencana.',
                ],
            ],
            'is_active' => true,
        ]);

        // Manually run indexer or job synchronously for testing
        app(SearchIndexer::class)->index($page);

        $this->assertDatabaseHas('search_index', [
            'searchable_type' => Page::class,
            'searchable_id' => $page->id,
            'locale' => 'en',
            'title' => 'Multi-Cloud Architecture',
        ]);

        $this->assertDatabaseHas('search_index', [
            'searchable_type' => Page::class,
            'searchable_id' => $page->id,
            'locale' => 'id',
            'title' => 'Arsitektur Multi-Cloud',
        ]);
    }

    public function test_draft_or_private_page_is_unindexed(): void
    {
        $page = Page::create([
            'title' => 'Internal Draft Strategy',
            'slug' => 'internal-draft',
            'status' => 'published',
            'author_id' => $this->user->id,
        ]);

        app(SearchIndexer::class)->index($page);
        $this->assertEquals(2, SearchIndex::where('searchable_id', $page->id)->count());

        // Change to draft
        $page->status = 'draft';
        $page->save();
        app(SearchIndexer::class)->index($page);

        $this->assertEquals(0, SearchIndex::where('searchable_id', $page->id)->count());
    }

    public function test_search_service_finds_content_and_supports_short_terms(): void
    {
        $page = Page::create([
            'title' => 'Cyber Threat Defense',
            'slug' => 'cyber-defense',
            'status' => 'published',
            'author_id' => $this->user->id,
        ]);

        PageBlock::create([
            'page_id' => $page->id,
            'name' => 'content',
            'type' => 'text',
            'value' => 'Comprehensive detection and AI observability across edge endpoints.',
            'is_active' => true,
        ]);

        app(SearchIndexer::class)->index($page);

        $service = app(SearchService::class);

        // Standard query
        $results = $service->search('observability', 'en');
        $this->assertGreaterThanOrEqual(1, $results->total());
        $this->assertEquals('Cyber Threat Defense', $results->items()[0]->title);

        // Short query (< 3 chars fallback)
        $shortResults = $service->search('AI', 'en');
        $this->assertGreaterThanOrEqual(1, $shortResults->total());

        // Empty query
        $emptyResults = $service->search('', 'en');
        $this->assertEquals(0, $emptyResults->total());
    }

    public function test_search_route_renders_results_and_enforces_noindex(): void
    {
        $page = Page::create([
            'title' => 'Artificial Intelligence Platform',
            'slug' => 'ai-platform',
            'status' => 'published',
            'author_id' => $this->user->id,
        ]);

        PageBlock::create([
            'page_id' => $page->id,
            'name' => 'hero',
            'type' => 'text',
            'value' => 'Automate workflow pipelines with state-of-the-art AI.',
            'is_active' => true,
        ]);

        app(SearchIndexer::class)->index($page);

        $response = $this->get('/search?q=pipeline');

        $response->assertStatus(200);
        $response->assertHeader('X-Robots-Tag', 'noindex, follow');
        $response->assertSee('Artificial Intelligence Platform', false);
    }

    public function test_search_reindex_command_executes(): void
    {
        $page = Page::create([
            'title' => 'Disaster Recovery Plan',
            'slug' => 'disaster-recovery',
            'status' => 'published',
            'author_id' => $this->user->id,
        ]);

        $this->artisan('search:reindex', ['--fresh' => true])
            ->assertExitCode(0);

        $this->assertDatabaseHas('search_index', [
            'searchable_id' => $page->id,
            'title' => 'Disaster Recovery Plan',
        ]);
    }
}
