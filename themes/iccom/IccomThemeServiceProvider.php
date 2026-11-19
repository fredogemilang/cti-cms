<?php

namespace Themes\Iccom;

use App\Http\Controllers\PartnershipController;
use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Services\SettingsRegistry;
use App\Support\Action;
use App\Support\Filter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * iCCom theme service provider.
 *
 * Restores iCCom-specific settings, home view data, and routes
 * (domicile manager, partnership inquiry) without modifying core files.
 */
class IccomThemeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $registry = $this->app->make(SettingsRegistry::class);

        $registry->registerGroup('domicile', [
            'label' => 'Domicile',
            'icon' => 'location_on',
            'order' => 90,
            'description' => 'Manage regional provinces and cities/regencies used for user registration.',
            'component' => 'admin.settings.domicile-manager',
        ]);

        Filter::add('home.view_data', function (array $data): array {
            return $data + [
                'testimonials' => $this->latestEntries('testimonials', 6),
                'partners' => $this->latestEntries('our-partners'),
            ];
        });

        Action::add('routes.before_catch_all', function (): void {
            Route::post('/partner', [PartnershipController::class, 'store'])
                ->name('partner.store')
                ->middleware('throttle:30,1');
        });
    }

    /**
     * Fetch published entries for a theme-specific CPT slug.
     *
     * @return Collection<int, CptEntry>
     */
    protected function latestEntries(string $cptSlug, ?int $limit = null): Collection
    {
        $cpt = CustomPostType::where('slug', $cptSlug)->first();
        if (! $cpt) {
            return collect();
        }

        $query = CptEntry::with(['author', 'terms'])
            ->where('post_type_id', $cpt->id)
            ->where('status', 'published')
            ->latest();

        if ($limit !== null) {
            $query->take($limit);
        }

        return $query->get();
    }
}
