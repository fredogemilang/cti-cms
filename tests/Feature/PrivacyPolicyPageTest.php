<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\Page;
use App\Models\Plugin;
use App\Models\Setting;
use App\Models\Theme;
use App\Models\User;
use App\Services\ThemeLoader;
use Database\Seeders\FormsSeeder;
use Database\Seeders\PrivacyPolicySeeder;
use Database\Seeders\StringTranslationsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Plugins\CsiMenus\Models\NavMenu;
use Plugins\CsiMenus\Models\NavMenuItem;
use Tests\TestCase;

class PrivacyPolicyPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);

        $loader = require base_path('vendor/autoload.php');
        $loader->addPsr4('Plugins\\CsiMenus\\', base_path('plugins/csi-menus/src'));

        if (! Schema::hasTable('csi_nav_menus')) {
            $migration = require base_path('plugins/csi-menus/database/migrations/2026_11_18_000001_create_csi_nav_menus_tables.php');
            $migration->up();
        }

        Plugin::firstOrCreate(
            ['slug' => 'csi-menus'],
            [
                'name' => 'CSI Menus',
                'version' => '1.0.0',
                'provider' => \Plugins\CsiMenus\Providers\CsiMenusServiceProvider::class,
                'is_active' => true,
            ]
        );
        if (class_exists(\Plugins\CsiMenus\Providers\CsiMenusServiceProvider::class)) {
            app()->register(\Plugins\CsiMenus\Providers\CsiMenusServiceProvider::class);
        }

        $theme = Theme::firstOrCreate(
            ['slug' => 'csi'],
            ['name' => 'CSI Theme', 'version' => '1.0.0', 'is_active' => true]
        );
        app(ThemeLoader::class)->setActiveTheme($theme);
    }

    public function test_privacy_policy_seeder_populates_11_bilingual_clauses_and_updates_footer_menu(): void
    {
        $footerOther = NavMenu::create(['name' => 'Footer Other', 'slug' => 'footer-other', 'location' => 'footer_other']);
        $privacyNav = NavMenuItem::create([
            'menu_id' => $footerOther->id,
            'title' => 'Privacy Policy',
            'url' => '#contact',
            'item_type' => 'link',
            'translations' => ['id' => ['title' => 'Kebijakan Privasi']],
            'order' => 2,
            'is_active' => true,
        ]);

        $this->seed(PrivacyPolicySeeder::class);

        $page = Page::where('slug', 'privacy-policy')->first();
        $this->assertNotNull($page);
        $this->assertSame('privacy', $page->template);
        $this->assertSame('kebijakan-privasi', $page->getTranslation('slug', 'id'));

        $clausesEn = $page->repeaterBlock('clauses_list', []);
        $this->assertCount(11, $clausesEn);
        $this->assertSame('Personal Data We Collect', $clausesEn[0]['title']);
        $this->assertSame('Contact & Grievance Officer', $clausesEn[10]['title']);

        $privacyNav->refresh();
        $this->assertSame('/privacy-policy', $privacyNav->getRawOriginal('url'));
        $this->assertSame('/kebijakan-privasi', $privacyNav->translations['id']['url']);
    }

    public function test_privacy_policy_renders_en_and_id_with_all_clauses_and_cloudflare_safe_email(): void
    {
        Storage::fake('public');
        config(['media.disk' => 'public']);

        $this->seed(StringTranslationsSeeder::class);
        $this->seed(FormsSeeder::class);
        $this->seed(PrivacyPolicySeeder::class);

        $form = Form::where('slug', 'contact-inquiry')->first();
        if ($form && function_exists('set_setting')) {
            set_setting('form_placeholder_contact_form', $form->id);
        }

        auth()->logout();

        $resEn = $this->get('/privacy-policy');
        $resEn->assertStatus(200);
        $resEn->assertSee('Privacy Policy');
        $resEn->assertSee('Introduction &amp; Scope', false);
        $resEn->assertSee('Policy Contents');
        $resEn->assertSee('Personal Data We Collect');
        $resEn->assertSee('Identifiable Data');
        $resEn->assertSee('Technical Information');
        $resEn->assertSee('Usage Metrics');
        $resEn->assertSee('Clause');
        $resEn->assertSee('Next Clause');
        // Cloudflare Email Obfuscation protection: no plaintext email / mailto link in raw HTML
        $resEn->assertDontSee('legal@computradetech.com', false);
        $resEn->assertDontSee('mailto:legal@computradetech.com', false);
        $resEn->assertDontSee('/cdn-cgi/l/email-protection', false);
        $resEn->assertSee(base64_encode('legal@computradetech.com'));
        $resEn->assertDontSee('figma.com/api/mcp/asset', false);

        app()->setLocale('id');
        Setting::set('default_locale', 'en');
        Setting::set('available_locales', 'en,id');
        $idRequest = Request::create('/id/kebijakan-privasi', 'GET');
        $this->app->instance('request', $idRequest);
        $idView = app(\App\Http\Controllers\PageController::class)->show('id', 'kebijakan-privasi');
        $idHtml = $idView instanceof \Illuminate\Contracts\View\View ? $idView->render() : (string) $idView;

        $this->assertStringContainsString('Kebijakan Privasi', $idHtml);
        $this->assertStringContainsString('Pendahuluan &amp; Ruang Lingkup', $idHtml);
        $this->assertStringContainsString('Data Pribadi yang Kami Kumpulkan', $idHtml);
        $this->assertStringContainsString('Klausul Berikutnya', $idHtml);
        $this->assertStringNotContainsString('legal@computradetech.com', $idHtml);
        $this->assertStringContainsString(base64_encode('legal@computradetech.com'), $idHtml);
    }
}
