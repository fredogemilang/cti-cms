<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\Page;
use App\Models\Plugin;
use App\Models\Theme;
use App\Models\User;
use App\Services\ThemeLoader;
use Database\Seeders\ContactPageSeeder;
use Database\Seeders\FormsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Plugins\CsiMenus\Models\NavMenu;
use Plugins\CsiMenus\Models\NavMenuItem;
use Tests\TestCase;

class ContactPageTest extends TestCase
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

    public function test_contact_page_seeder_creates_bilingual_page_uploads_media_and_updates_menus(): void
    {
        Storage::fake('public');
        config(['media.disk' => 'public']);

        $headerMenu = NavMenu::create(['name' => 'Header', 'slug' => 'header', 'location' => 'header']);
        $headerItem = NavMenuItem::create([
            'menu_id' => $headerMenu->id,
            'title' => 'Contact Us',
            'url' => '#contact',
            'item_type' => 'button',
            'translations' => ['id' => ['title' => 'Hubungi Kami']],
            'order' => 1,
            'is_active' => true,
        ]);

        $footerMenu = NavMenu::create(['name' => 'Footer Pages', 'slug' => 'footer-pages', 'location' => 'footer_pages']);
        $footerItem = NavMenuItem::create([
            'menu_id' => $footerMenu->id,
            'title' => 'Contact Us',
            'url' => '#contact',
            'item_type' => 'link',
            'translations' => ['id' => ['title' => 'Hubungi Kami']],
            'order' => 1,
            'is_active' => true,
        ]);

        $this->seed(ContactPageSeeder::class);

        $page = Page::where('slug', 'contact-us')->first();
        $this->assertNotNull($page);
        $this->assertSame('contact', $page->template);
        $this->assertSame('hubungi-kami', $page->getTranslation('slug', 'id'));

        $heroBg = $page->block('hero_bg_image');
        $this->assertNotEmpty($heroBg);
        $this->assertStringStartsWith('media/', $heroBg);
        $this->assertTrue(Storage::disk('public')->exists($heroBg));

        $headerItem->refresh();
        $footerItem->refresh();
        $this->assertSame('/contact-us', $headerItem->getRawOriginal('url'));
        $this->assertSame('/hubungi-kami', $headerItem->translations['id']['url']);
        $this->assertSame('/contact-us', $footerItem->getRawOriginal('url'));
        $this->assertSame('/hubungi-kami', $footerItem->translations['id']['url']);
    }

    public function test_contact_us_page_renders_en_and_id_routes_with_interactive_map_and_contact_bar(): void
    {
        Storage::fake('public');
        config(['media.disk' => 'public']);

        $this->seed(FormsSeeder::class);
        $this->seed(ContactPageSeeder::class);

        $form = Form::where('slug', 'contact-inquiry')->first();
        if ($form && function_exists('set_setting')) {
            set_setting('form_placeholder_contact_form', $form->id);
        }

        auth()->logout();

        $resEn = $this->get('/contact-us');
        $resEn->assertStatus(200);
        $resEn->assertSee('Let’s Connect and Grow Together');
        $resEn->assertSee('PT Converion Solution Informatica');
        $resEn->assertSee('Jakarta Office');
        $resEn->assertSee('Surabaya Office');
        $resEn->assertSee('Centennial Tower');
        $resEn->assertSee('Graha SA Lantai 8, Suite 801');
        $resEn->assertSee('+6221 806 22278');
        $resEn->assertSee('Converion Care Hub (WA)');
        $resEn->assertSee('+62822 9992 2278');
        $resEn->assertSee(base64_encode('marketing@converion.com'));
        $resEn->assertDontSee('mailto:marketing@converion.com', false);
        $resEn->assertDontSee('/cdn-cgi/l/email-protection', false);
        $resEn->assertSee('<iframe', false);
        $resEn->assertDontSee('figma.com/api/mcp/asset', false);

        // Verify ID localized rendering via PageController directly (matches production /{locale}/{slug} dispatch)
        app()->setLocale('id');
        \App\Models\Setting::set('default_locale', 'en');
        \App\Models\Setting::set('available_locales', 'en,id');
        $idRequest = \Illuminate\Http\Request::create('/id/hubungi-kami', 'GET');
        $this->app->instance('request', $idRequest);
        $idView = app(\App\Http\Controllers\PageController::class)->show('id', 'hubungi-kami');
        $idHtml = $idView instanceof \Illuminate\Contracts\View\View ? $idView->render() : (string) $idView;

        $this->assertStringContainsString('Mari Terhubung dan Bertumbuh Bersama', $idHtml);
        $this->assertStringContainsString('Telepon', $idHtml);
        $this->assertStringContainsString(base64_encode('marketing@converion.com'), $idHtml);
        $this->assertStringNotContainsString('mailto:marketing@converion.com', $idHtml);
        $this->assertStringContainsString('<iframe', $idHtml);

        $robots = $this->get('/robots.txt');
        $robots->assertStatus(200);
        $robots->assertSee('Disallow: /cdn-cgi/', false);
    }
}
