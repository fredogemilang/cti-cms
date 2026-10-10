<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use App\Support\Action;
use App\Support\Filter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * Core extension points that replaced client-specific code (Phase 0.3):
 * routes.before_catch_all action, home.view_data and deferred_section.* filters.
 */
class CoreExtensionHooksTest extends TestCase
{
    use RefreshDatabase;

    protected string $viewDir;

    protected function setUp(): void
    {
        // Must be registered before the app boots: catch-all routes are built
        // in an app->booted() callback.
        Action::add('routes.before_catch_all', function (string $adminPath, string $localePattern) {
            Route::get('/{vendor}/{product}', fn (string $vendor, string $product) => "short:{$vendor}/{$product}")
                ->where('vendor', 'acme-[a-z]+')
                ->name('test.short.product');
        });

        parent::setUp();

        $this->viewDir = storage_path('framework/testing/hook-views');
        File::ensureDirectoryExists($this->viewDir.'/partials/deferred');
        File::ensureDirectoryExists($this->viewDir.'/pages');
        File::put($this->viewDir.'/partials/deferred/demo.blade.php', 'deferred:{{ $greeting ?? "none" }}');
        File::put($this->viewDir.'/pages/home.blade.php', 'home:{{ $greeting ?? "none" }}:{{ $page->slug }}');
    }

    protected function tearDown(): void
    {
        Action::clear();
        Filter::clear();
        File::deleteDirectory($this->viewDir);

        parent::tearDown();
    }

    public function test_route_registered_via_action_wins_over_page_catch_all(): void
    {
        $this->assertTrue(Route::has('test.short.product'));

        $this->get('/acme-vendor/widget')
            ->assertOk()
            ->assertSee('short:acme-vendor/widget');
    }

    public function test_deferred_section_is_404_unless_allowed_by_filter(): void
    {
        $this->get('/_deferred/demo')->assertNotFound();
    }

    public function test_allowed_deferred_section_is_404_when_theme_view_is_missing(): void
    {
        Filter::add('deferred_section.allowed', fn (array $sections) => [...$sections, 'ghost']);

        $this->get('/_deferred/ghost')->assertNotFound();
    }

    public function test_allowed_deferred_section_renders_theme_partial_with_filtered_data(): void
    {
        $this->prependViewPathToAllThemeNamespaces();

        Filter::add('deferred_section.allowed', fn (array $sections) => [...$sections, 'demo']);
        Filter::add('deferred_section.data', function (array $data, string $section) {
            return $section === 'demo' ? $data + ['greeting' => 'hello'] : $data;
        });

        $this->get('/_deferred/demo')
            ->assertOk()
            ->assertSee('deferred:hello');
    }

    public function test_home_view_data_filter_injects_theme_data(): void
    {
        $user = User::factory()->create();
        Page::create([
            'title' => 'Home',
            'slug' => 'home',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $user->id,
        ]);

        $this->prependViewPathToAllThemeNamespaces();

        Filter::add('home.view_data', fn (array $data, Page $page) => $data + ['greeting' => 'from-theme']);

        $this->get('/')
            ->assertOk()
            ->assertSee('home:from-theme:home');
    }

    /**
     * The deferred controller renders "{activeTheme}::partials.deferred.*";
     * make the test partial resolvable whatever theme namespace is active.
     */
    protected function prependViewPathToAllThemeNamespaces(): void
    {
        $finder = View::getFinder();
        $namespaces = array_keys($finder->getHints());

        foreach (array_unique([...$namespaces, 'default']) as $namespace) {
            View::prependNamespace($namespace, $this->viewDir);
        }

        $finder->prependLocation($this->viewDir);
    }
}
