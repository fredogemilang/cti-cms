<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Plugins\PartnerRewardCatalog\Http\Controllers\CatalogController;

$adminPath = config('admin.path', config('cms.path', 'ctrlpanel'));

Route::middleware(['web', 'auth', 'enforce-2fa', 'permission:partner-reward-catalog.view'])
    ->prefix("{$adminPath}/partner-reward-catalog")
    ->name('admin.partner-reward-catalog.')
    ->group(function () {
        Route::get('/', fn () => view('partner-reward-catalog::admin.index'))->name('index');
    });

Route::middleware(['web'])->group(function () {
    Route::get('/partner-reward-catalog', [CatalogController::class, 'show'])
        ->name('partner-reward-catalog.show');

    try {
        $allLocales = array_filter(array_map('trim', explode(',', (string) setting('available_locales', 'id,en'))));
        $defaultLocale = setting('default_locale', config('app.locale', 'en'));
    } catch (\Throwable $e) {
        $allLocales = ['id', 'en'];
        $defaultLocale = 'en';
    }

    $nonDefaultLocales = array_values(array_filter($allLocales, fn ($l) => $l !== $defaultLocale));
    foreach ($nonDefaultLocales as $loc) {
        Route::get("/{$loc}/partner-reward-catalog", function (Request $request) use ($loc) {
            app()->setLocale($loc);

            return app(CatalogController::class)->show($request);
        })->name("partner-reward-catalog.show.{$loc}");
    }
});
