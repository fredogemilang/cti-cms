<?php

namespace Tests\Feature;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\CustomTaxonomy;
use App\Models\Domicile;
use App\Models\Page;
use App\Models\PartnershipInquiry;
use App\Models\Plugin;
use App\Models\TaxonomyTerm;
use App\Models\Theme;
use App\Models\User;
use App\Services\PluginLoader;
use App\Services\SettingsRegistry;
use App\Services\ThemeLoader;
use App\Support\Action;
use App\Support\Filter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class IccomIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Action::clear();
        Filter::clear();

        parent::tearDown();
    }

    protected function activateIccomEnvironment(): void
    {
        $theme = Theme::create([
            'name' => 'iCCom',
            'slug' => 'iccom',
            'version' => '1.0.0',
            'is_active' => true,
        ]);

        app(ThemeLoader::class)->setActiveTheme($theme);

        foreach ([
            ['slug' => 'posts', 'name' => 'Posts', 'provider' => 'Plugins\\Posts\\Providers\\PostsServiceProvider'],
            ['slug' => 'events', 'name' => 'Events', 'provider' => 'Plugins\\Events\\Providers\\EventsServiceProvider'],
            ['slug' => 'membership', 'name' => 'Membership', 'provider' => 'Plugins\\Membership\\Providers\\MembershipServiceProvider'],
            ['slug' => 'article-submission', 'name' => 'Article Submission', 'provider' => 'Plugins\\ArticleSubmission\\Providers\\ArticleSubmissionServiceProvider'],
        ] as $plugin) {
            Plugin::create([
                'name' => $plugin['name'],
                'slug' => $plugin['slug'],
                'version' => '1.0.0',
                'provider' => $plugin['provider'],
                'is_active' => true,
            ]);
        }

        app(PluginLoader::class)->boot();

        Route::middleware('web')->group(function (): void {
            Action::run('routes.before_catch_all', config('admin.path', 'admin'), 'nothing-to-match');
        });
        Route::getRoutes()->refreshNameLookups();

        $this->artisan('migrate', ['--no-interaction' => true]);
    }

    public function test_iccom_theme_provider_registers_domicile_settings_and_home_view_data(): void
    {
        $user = User::factory()->create();
        Page::create([
            'title' => 'Home',
            'slug' => 'home',
            'template' => 'home',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $user->id,
        ]);

        $this->activateIccomEnvironment();

        $this->assertTrue(app(SettingsRegistry::class)->hasGroup('domicile'));
        $this->assertTrue(Route::has('partner.store'));

        $this->get('/')
            ->assertOk()
            ->assertSee('iCCom');
    }

    public function test_partnership_inquiry_submission_validates_and_persists(): void
    {
        $this->activateIccomEnvironment();

        $response = $this->from('/partner')->post(route('partner.store'), [
            'company_name' => 'PT Cloud Nusantara',
            'website' => 'https://cloudnusantara.id',
            'contact_name' => 'Budi Santoso',
            'email' => 'budi@cloudnusantara.id',
            'partnership_type' => 'corporate',
            'message' => 'Interested in sponsoring an upcoming iCCom conference.',
        ]);

        $response->assertRedirect('/partner');
        $response->assertSessionHas('partner_success');

        $this->assertDatabaseHas('partnership_inquiries', [
            'company_name' => 'PT Cloud Nusantara',
            'email' => 'budi@cloudnusantara.id',
            'partnership_type' => 'corporate',
            'status' => 'new',
        ]);

        $this->assertSame(1, PartnershipInquiry::count());
    }

    public function test_domicile_model_and_complete_expired_events_command_work(): void
    {
        Domicile::create([
            'code' => '31',
            'name' => 'DKI Jakarta',
            'type' => 'province',
            'parent_code' => null,
        ]);

        $this->assertDatabaseHas('domiciles', [
            'code' => '31',
            'name' => 'DKI Jakarta',
            'type' => 'province',
        ]);

        $this->artisan('events:complete-expired')->assertExitCode(0);
    }

    public function test_our_partners_on_homepage_are_grouped_by_taxonomy_term(): void
    {
        $user = User::factory()->create();
        Page::create([
            'title' => 'Home',
            'slug' => 'home',
            'template' => 'home',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $user->id,
        ]);

        $cpt = CustomPostType::create([
            'name' => 'our_partners',
            'singular_label' => 'Our Partner',
            'plural_label' => 'Our Partners',
            'slug' => 'our-partners',
            'is_active' => true,
        ]);

        $taxonomy = CustomTaxonomy::create([
            'name' => 'partners_category',
            'singular_label' => 'Category',
            'plural_label' => 'Categories',
            'slug' => 'partners-category',
            'post_types' => ['our-partners'],
            'is_active' => true,
        ]);

        $corpTerm = TaxonomyTerm::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Corporate Partner',
            'slug' => 'corporate-partner',
        ]);

        $uniTerm = TaxonomyTerm::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'University Partner',
            'slug' => 'university-partner',
        ]);

        $commTerm = TaxonomyTerm::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Community Partner',
            'slug' => 'community-partner',
        ]);

        $corpPartner = CptEntry::create([
            'post_type_id' => $cpt->id,
            'title' => 'TiDB',
            'slug' => 'tidb',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $user->id,
        ]);
        $corpPartner->terms()->attach($corpTerm->id);

        $uniPartner = CptEntry::create([
            'post_type_id' => $cpt->id,
            'title' => 'Binus University',
            'slug' => 'binus-university',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $user->id,
        ]);
        $uniPartner->terms()->attach($uniTerm->id);

        $commPartner = CptEntry::create([
            'post_type_id' => $cpt->id,
            'title' => 'Cloud Native ID',
            'slug' => 'cloud-native-id',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $user->id,
        ]);
        $commPartner->terms()->attach($commTerm->id);

        $this->activateIccomEnvironment();

        $response = $this->get('/');
        $response->assertOk();
        $html = $response->getContent();

        $this->assertMatchesRegularExpression("/partnerData\['corporate'\]\.push\(\{\s*name:\s*\"TiDB\"/", $html);
        $this->assertMatchesRegularExpression("/partnerData\['university'\]\.push\(\{\s*name:\s*\"Binus University\"/", $html);
        $this->assertMatchesRegularExpression("/partnerData\['community'\]\.push\(\{\s*name:\s*\"Cloud Native ID\"/", $html);
        $this->assertDoesNotMatchRegularExpression("/partnerData\['corporate'\]\.push\(\{\s*name:\s*\"Binus University\"/", $html);
    }
}
