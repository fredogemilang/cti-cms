<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Admin\ActivityLog\ActivityTable;
use App\Livewire\Admin\Ai\McpSettings;
use App\Livewire\Admin\ApiTokens\Index;
use App\Livewire\Admin\Cpt\CptForm;
use App\Livewire\Admin\Cpt\CptTable;
use App\Livewire\Admin\FormsTable;
use App\Livewire\Admin\Header\GlobalSearch;
use App\Livewire\Admin\Header\NotificationsDropdown;
use App\Livewire\Admin\IconLibrariesSettings;
use App\Livewire\Admin\IconPicker;
use App\Livewire\Admin\MediaDetails;
use App\Livewire\Admin\MediaLibrary;
use App\Livewire\Admin\MediaPicker;
use App\Livewire\Admin\MediaUploader;
use App\Livewire\Admin\Pages\PageForm;
use App\Livewire\Admin\Pages\PagesTable;
use App\Livewire\Admin\Profile\ProfileForm;
use App\Livewire\Admin\Profile\TwoFactorSettings;
use App\Livewire\Admin\Queue\JobsTable;
use App\Livewire\Admin\Redirects\RedirectTable;
use App\Livewire\Admin\Seo\SeoBulkEditor;
use App\Livewire\Admin\Seo\SeoGeneralSettings;
use App\Livewire\Admin\Seo\SeoOverview;
use App\Livewire\Admin\Settings\PermalinkSettings;
use App\Livewire\Admin\Settings\SettingsPage;
use App\Livewire\Admin\StringTranslationManager;
use App\Livewire\Admin\Taxonomies\TaxonomyForm;
use App\Livewire\Admin\Taxonomies\TaxonomyTable;
use App\Livewire\Admin\Themes\ThemeManager;
use App\Livewire\Admin\TiptapMediaPicker;
use App\Livewire\Admin\Tools\BackupManager;
use App\Livewire\Admin\Trash\TrashIndex;
use App\Livewire\Admin\UsersTable;
use App\Livewire\Admin\WordPressCptMigration;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Component;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Mount-and-render smoke test for every admin Livewire component.
 *
 * Purpose: catch server-side breakage from Livewire upgrades. A minor bump
 * (e.g. v4.3 → v4.4) can change component lifecycle, property hydration, or
 * removed APIs — all of which surface here as a mount/render exception.
 *
 * Scope limit: this exercises the PHP side only. It cannot detect regressions
 * in Livewire's client-side runtime (DOM morphing, wire:model binding), which
 * still require a manual pass through the admin UI after a Livewire upgrade.
 */
class AdminComponentSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'is_super_admin' => true,
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);
        $this->actingAs($user);

        config(['admin.path' => 'admin']);
    }

    /**
     * Admin components whose mount() takes no required arguments.
     *
     * Components excluded because mount() requires a model/id argument — they
     * need per-case fixtures and belong in their own focused tests:
     *   Components\EditorialNotesBox, Cpt\Entries\{EntriesReorder,EntriesTable,EntryForm},
     *   EmailTemplates\Edit, Seo\{SeoIndexNow,SeoMetaBox},
     *   Taxonomies\Terms\{TermForm,TermTable}, Users\EditUser
     *
     * @return array<string, array{class-string}>
     */
    public static function adminComponents(): array
    {
        $components = [
            ActivityTable::class,
            McpSettings::class,
            Index::class,
            CptForm::class,
            CptTable::class,
            \App\Livewire\Admin\EmailTemplates\Index::class,
            FormsTable::class,
            GlobalSearch::class,
            NotificationsDropdown::class,
            IconLibrariesSettings::class,
            IconPicker::class,
            MediaDetails::class,
            MediaLibrary::class,
            MediaPicker::class,
            MediaUploader::class,
            PageForm::class,
            PagesTable::class,
            ProfileForm::class,
            TwoFactorSettings::class,
            JobsTable::class,
            RedirectTable::class,
            SeoBulkEditor::class,
            SeoGeneralSettings::class,
            SeoOverview::class,
            PermalinkSettings::class,
            SettingsPage::class,
            StringTranslationManager::class,
            TaxonomyForm::class,
            TaxonomyTable::class,
            ThemeManager::class,
            TiptapMediaPicker::class,
            BackupManager::class,
            TrashIndex::class,
            UsersTable::class,
            \App\Livewire\Admin\Webhooks\Index::class,
            WordPressCptMigration::class,
        ];

        // Key on the sub-namespace path, not class_basename() — several components
        // share a basename (ApiTokens\Index, EmailTemplates\Index, Webhooks\Index)
        // and would silently collapse into a single data set.
        return collect($components)
            ->mapWithKeys(fn (string $c) => [str_replace('App\\Livewire\\Admin\\', '', $c) => [$c]])
            ->all();
    }

    #[DataProvider('adminComponents')]
    public function test_admin_component_mounts_and_renders(string $component): void
    {
        Livewire::test($component)->assertStatus(200);
    }

    /**
     * Guard against the provider silently drifting out of sync with the codebase.
     * If a new no-arg admin component is added, this fails until it is covered.
     */
    public function test_provider_covers_every_no_arg_admin_component(): void
    {
        $discovered = [];

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(app_path('Livewire'))
        );

        foreach ($files as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $relative = str_replace('\\', '/', $file->getPathname());
            $relative = substr($relative, strpos($relative, 'app/Livewire/') + strlen('app/Livewire/'));
            $class = 'App\\Livewire\\'.str_replace('/', '\\', preg_replace('/\.php$/', '', $relative));

            if (! class_exists($class)) {
                continue;
            }

            $reflection = new \ReflectionClass($class);
            if ($reflection->isAbstract() || ! $reflection->isSubclassOf(Component::class)) {
                continue;
            }

            $required = $reflection->hasMethod('mount')
                ? $reflection->getMethod('mount')->getNumberOfRequiredParameters()
                : 0;

            if ($required === 0) {
                $discovered[] = $class;
            }
        }

        $covered = collect(static::adminComponents())->map(fn (array $row) => $row[0])->all();

        sort($discovered);
        sort($covered);

        $this->assertSame(
            $discovered,
            $covered,
            'Admin Livewire components are not all covered by the smoke test. '
            .'Add any new no-arg component to adminComponents().'
        );
    }
}
