<?php

namespace Tests\Feature;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Page;
use App\Models\Theme;
use App\Models\User;
use App\Services\ThemeLoader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CareersExploreModalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Theme::updateOrCreate(
            ['slug' => 'cdt'],
            [
                'name' => 'CDT Theme',
                'version' => '1.0.0',
                'description' => 'Central Data Technology Theme',
                'is_active' => true,
                'supports' => ['pages', 'posts', 'menus'],
            ]
        );

        app(ThemeLoader::class)->boot();
    }

    #[Test]
    public function careers_page_renders_explore_cdt_modal_with_wireframe_specifications(): void
    {
        $user = User::factory()->create();

        $page = Page::create([
            'title' => 'Careers',
            'slug' => 'careers',
            'template' => 'careers',
            'status' => 'published',
            'author_id' => $user->id,
        ]);

        $cpt = CustomPostType::create([
            'name' => 'Technology Alliance',
            'slug' => 'technology-alliance',
            'singular_label' => 'Technology Alliance',
            'plural_label' => 'Technology Alliances',
            'description' => 'Partners',
            'is_active' => true,
        ]);

        $publishedSlugs = [
            ['title' => 'Akamai', 'slug' => 'akamai'],
            ['title' => 'Amazon Web Services', 'slug' => 'amazon-web-services'],
            ['title' => 'Dynatrace', 'slug' => 'dynatrace'],
            ['title' => 'F5', 'slug' => 'f5'],
            ['title' => 'TiDB', 'slug' => 'tidb'],
            ['title' => 'Hitachi Vantara', 'slug' => 'hitachi-vantara'],
            ['title' => 'Zscaler', 'slug' => 'zscaler'],
            ['title' => 'Nebula Cloud Console', 'slug' => 'nebula-cloud-console'],
            ['title' => 'NetGain Systems', 'slug' => 'netgain-systems'],
        ];

        foreach ($publishedSlugs as $p) {
            CptEntry::create([
                'post_type_id' => $cpt->id,
                'title' => $p['title'],
                'slug' => $p['slug'],
                'status' => 'published',
                'author_id' => $user->id,
            ]);
        }

        // Create a draft product that should NOT be displayed
        $draftProduct = CptEntry::create([
            'post_type_id' => $cpt->id,
            'title' => 'Unpublished Partner',
            'slug' => 'unpublished-partner',
            'status' => 'draft',
            'author_id' => $user->id,
        ]);

        $rendered = view('cdt::pages.careers', [
            'page' => $page,
            'title' => $page->title,
            'meta_title' => $page->title,
        ])->render();

        // 1. Modal header elements
        $this->assertStringContainsString('APPLICATION SUBMITTED', $rendered);
        $this->assertStringContainsString('Explore CDT', $rendered);

        // 2. Banner welcome message
        $this->assertStringContainsString('A MESSAGE FROM CDT', $rendered);
        $this->assertStringContainsString('Thank you for considering a career at CDT.', $rendered);

        // 3. Section Titles
        $this->assertStringContainsString('OUR PRODUCTS', $rendered);
        $this->assertStringContainsString('OUR SOLUTIONS', $rendered);
        $this->assertStringContainsString('EXPLORE OUR WEBSITE', $rendered);

        // 4. Products List
        $this->assertStringContainsString('Akamai', $rendered);
        $this->assertStringContainsString('Amazon Web Services', $rendered);
        $this->assertStringContainsString('Dynatrace', $rendered);
        $this->assertStringContainsString('F5', $rendered);
        $this->assertStringContainsString('TiDB', $rendered);
        $this->assertStringContainsString('Hitachi Vantara', $rendered);
        $this->assertStringContainsString('Zscaler', $rendered);
        $this->assertStringContainsString('Nebula Cloud Console', $rendered);
        $this->assertStringContainsString('NetGain Systems', $rendered);

        // 5. Others button leading to technology-alliance section on homepage
        $this->assertStringContainsString('#technology-alliance', $rendered);
        $this->assertStringContainsString('Others', $rendered);

        // 6. Solutions List
        $this->assertStringContainsString('Analytics', $rendered);
        $this->assertStringContainsString('Cloud', $rendered);
        $this->assertStringContainsString('Infrastructure', $rendered);
        $this->assertStringContainsString('Observability', $rendered);
        $this->assertStringContainsString('Security', $rendered);

        // 7. Explore Website Cards
        $this->assertStringContainsString('INSIGHTS', $rendered);
        $this->assertStringContainsString('ABOUT US', $rendered);
        $this->assertStringContainsString('HOMEPAGE', $rendered);

        // 8. Published Status check & updated copy
        $this->assertStringContainsString('Explore our website, products, solutions, articles, and company journey to get to know us better and gain insight into our culture and values.', $rendered);
        $this->assertStringNotContainsString('Unpublished Partner', $rendered);
    }
}
