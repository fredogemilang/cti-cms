<?php

namespace Themes\Csi;

use App\Models\CustomPostType;
use App\Models\Page;
use App\Support\Action;
use App\Support\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Themes\Csi\Support\CsiDataProvider;
use Themes\Csi\Support\CsiSearchService;

class CsiThemeServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Filter::add('home.view_data', function (array $data, Page $page): array {
            return array_merge($data, CsiDataProvider::homeViewData());
        });

        Filter::add('cpt_archive.per_page', function (int $perPage, CustomPostType $postType): int {
            return $postType->slug === 'technology-alliances' ? 100 : $perPage;
        });

        Action::add('routes.before_catch_all', function () {
            Route::get('/_csi/search-suggest', function (Request $request) {
                $locale = (string) $request->query('locale', app()->getLocale());
                $available = function_exists('available_locales') ? available_locales() : ['en', 'id'];
                if (! in_array($locale, $available, true)) {
                    $locale = config('app.locale', 'en');
                }
                app()->setLocale($locale);

                $q = (string) $request->query('q', '');

                return response()->json(CsiSearchService::suggest($q, $locale));
            })->name('csi.search.suggest');
        });
    }
}
