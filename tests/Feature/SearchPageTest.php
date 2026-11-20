<?php

namespace Tests\Feature;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Form;
use App\Models\Plugin;
use App\Models\Theme;
use App\Models\User;
use App\Services\ThemeLoader;
use Database\Seeders\FormsSeeder;
use Database\Seeders\StringTranslationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Themes\Csi\CsiThemeServiceProvider;
use Themes\Csi\Support\CsiSearchService;

class SearchPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);

        $theme = Theme::firstOrCreate(
            ['slug' => 'csi'],
            ['name' => 'CSI Theme', 'version' => '1.0.0', 'is_active' => true]
        );
        app(ThemeLoader::class)->setActiveTheme($theme);
        app()->register(CsiThemeServiceProvider::class);
    }

    protected function seedSampleCatalog(): void
    {
        $alliancesCpt = CustomPostType::create([
            'name' => 'Technology Alliances',
            'slug' => 'technology-alliances',
            'singular_label' => 'Technology Alliance',
            'plural_label' => 'Technology Alliances',
            'has_archive' => true,
            'publicly_queryable' => true,
            'is_active' => true,
        ]);

        $lenovo = CptEntry::create([
            'post_type_id' => $alliancesCpt->id,
            'slug' => 'lenovo',
            'title' => 'Lenovo',
            'status' => 'published',
            'published_at' => now(),
            'meta' => [
                'alliance_category' => 'infrastructure',
                'hero_headline' => 'Enterprise Server & Compute Power',
                'hero_description' => 'ThinkSystem enterprise rack servers built for AI and data centers.',
            ],
        ]);

        $netapp = CptEntry::create([
            'post_type_id' => $alliancesCpt->id,
            'slug' => 'netapp',
            'title' => 'NetApp',
            'status' => 'published',
            'published_at' => now(),
            'meta' => [
                'alliance_category' => 'infrastructure',
                'hero_headline' => 'Intelligent Data Infrastructure',
                'hero_description' => 'All-flash SAN and hybrid cloud storage solutions.',
            ],
        ]);

        $solutionsCpt = CustomPostType::create([
            'name' => 'Solutions',
            'slug' => 'solutions',
            'singular_label' => 'Solution',
            'plural_label' => 'Solutions',
            'has_archive' => true,
            'publicly_queryable' => true,
            'is_active' => true,
            'translations' => [
                'en' => ['name' => 'Solutions', 'slug' => 'solutions'],
                'id' => ['name' => 'Solusi', 'slug' => 'solusi'],
            ],
        ]);

        CptEntry::create([
            'post_type_id' => $solutionsCpt->id,
            'slug' => 'infrastructure',
            'title' => 'Infrastructure',
            'status' => 'published',
            'published_at' => now(),
            'translations' => [
                'en' => ['title' => 'Infrastructure', 'slug' => 'infrastructure'],
                'id' => ['title' => 'Infrastruktur', 'slug' => 'infrastruktur'],
            ],
            'meta' => [
                'subtitle' => 'Reliable servers, storage, and networks for your enterprise.',
                'modules' => [
                    [
                        'title' => 'Server and Storage Infrastructure',
                        'description' => 'High-density rack servers and all-flash SAN storage.',
                        'related_products' => [$lenovo->id, $netapp->id],
                    ],
                ],
                '_translations' => [
                    'id' => [
                        'subtitle' => 'Server, penyimpanan, dan jaringan andal untuk enterprise Anda.',
                        'modules' => [
                            [
                                'title' => 'Infrastruktur Server dan Penyimpanan',
                                'description' => 'Server enterprise berperforma tinggi dan penyimpanan SAN.',
                                'related_products' => [$lenovo->id, $netapp->id],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_categorized_search_renders_solutions_and_products_and_hides_empty_articles(): void
    {
        Storage::fake('public');
        config(['media.disk' => 'public']);

        $this->seed(StringTranslationsSeeder::class);
        $this->seed(FormsSeeder::class);
        $this->seedSampleCatalog();

        $form = Form::where('slug', 'contact-inquiry')->first();
        if ($form && function_exists('set_setting')) {
            set_setting('form_placeholder_contact_form', $form->id);
        }

        auth()->logout();

        $res = $this->get('/search?q=server');
        $res->assertStatus(200);
        $res->assertSee('Search results for');
        $res->assertSee('id="search-solutions-section"', false);
        $res->assertSee('Server and Storage Infrastructure');
        $res->assertSee('id="search-products-section"', false);
        $res->assertSee('Lenovo');
        // No articles matched (and posts plugin disabled) -> articles section must be hidden
        $res->assertDontSee('id="search-articles-section"', false);
        // Header refine input must render
        $res->assertSee('name="q"', false);
        // Empty state must NOT render when solutions & products exist
        $res->assertDontSee('id="search-empty-state"', false);
    }

    public function test_partial_prefix_search_and_empty_state_hides_all_sections(): void
    {
        $this->seed(StringTranslationsSeeder::class);
        $this->seedSampleCatalog();

        $prefixResult = CsiSearchService::searchCategorized('infra', 'en');
        $this->assertGreaterThan(0, $prefixResult['solutions']->count());
        $this->assertTrue($prefixResult['solutions']->contains('title', 'Infrastructure'));

        $resEmpty = $this->get('/search?q=zzzznonexistentkeyword');
        $resEmpty->assertStatus(200);
        $resEmpty->assertDontSee('id="search-solutions-section"', false);
        $resEmpty->assertDontSee('id="search-products-section"', false);
        $resEmpty->assertDontSee('id="search-articles-section"', false);
        $resEmpty->assertSee('id="search-empty-state"', false);
        $resEmpty->assertSee('No matching results found');
    }

    public function test_live_search_suggest_endpoint_returns_grouped_json(): void
    {
        $this->seedSampleCatalog();

        $data = CsiSearchService::suggest('server', 'en');
        $this->assertSame('server', $data['query']);
        $this->assertGreaterThan(0, $data['total']);
        $this->assertNotEmpty($data['solutions']);
        $this->assertNotEmpty($data['products']);
        $this->assertEmpty($data['articles']);
    }
}
