<?php

namespace Tests\Feature;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapMultiLocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_generates_urls_for_all_available_locales(): void
    {
        Setting::set('seo_sitemap_enabled', true, 'seo', 'boolean');
        Setting::set('available_locales', 'en,id', 'general', 'text');
        Setting::set('default_locale', 'en', 'general', 'text');
        Setting::set('locale_prefix_hide_default', true, 'general', 'boolean');
        Setting::set('locale_url_structure', 'prefix', 'general', 'text');

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
            'translations' => [
                'id' => ['title' => 'Akamai ID', 'slug' => 'akamai'],
            ],
        ]);

        // Register filter hook (cdt theme behavior)
        add_filter('cpt_entry.url', function ($url, $e, $locale = null) use ($cpt) {
            if ($e && $e->post_type_id === $cpt->id) {
                $locPrefix = ($locale && $locale !== 'en') ? '/'.$locale : '';

                return url($locPrefix.'/'.$e->slug);
            }

            return $url;
        });

        $response = $this->get('/technology-alliance-sitemap.xml');
        $response->assertStatus(200);

        // Should see both default EN URL (https://cdt.devs/akamai) and ID URL (https://cdt.devs/id/akamai)
        $response->assertSee(url('/akamai'));
        $response->assertSee(url('/id/akamai'));
    }
}
