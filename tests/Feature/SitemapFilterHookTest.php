<?php

namespace Tests\Feature;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapFilterHookTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_respects_cpt_entry_url_filter_hook(): void
    {
        Setting::set('seo_sitemap_enabled', true, 'seo', 'boolean');

        $user = User::factory()->create();
        $cpt = CustomPostType::create([
            'name' => 'Technology Alliance',
            'singular_label' => 'Technology Alliance',
            'plural_label' => 'Technology Alliances',
            'slug' => 'technology-alliance',
            'has_archive' => false,
            'publicly_queryable' => true,
            'is_active' => true,
        ]);

        $entry = CptEntry::create([
            'post_type_id' => $cpt->id,
            'title' => 'Akamai',
            'slug' => 'akamai',
            'status' => 'published',
            'author_id' => $user->id,
        ]);

        // Register filter hook to override CPT entry URL (e.g., adding /id/ or custom prefix)
        add_filter('cpt_entry.url', function (string $url, CptEntry $e) {
            if ($e->slug === 'akamai') {
                return url('/id/akamai');
            }

            return $url;
        });

        $response = $this->get('/technology-alliance-sitemap.xml');
        $response->assertStatus(200);

        // Verify the sitemap outputs the custom filtered URL
        $response->assertSee(url('/id/akamai'));
        $response->assertDontSee(url('/technology-alliance/akamai'));
    }
}
