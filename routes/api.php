<?php

use App\Http\Controllers\Api\TranslationApiController;
use App\Http\Controllers\Api\V1\Admin\ActivityLogAdminController;
use App\Http\Controllers\Api\V1\Admin\AppearanceAdminController;
use App\Http\Controllers\Api\V1\Admin\CptAdminController;
use App\Http\Controllers\Api\V1\Admin\CptEntryAdminController;
use App\Http\Controllers\Api\V1\Admin\EmailTemplateAdminController;
use App\Http\Controllers\Api\V1\Admin\FormAdminController;
use App\Http\Controllers\Api\V1\Admin\IndexingLogAdminController;
use App\Http\Controllers\Api\V1\Admin\MediaAdminController;
use App\Http\Controllers\Api\V1\Admin\MenuAdminController;
use App\Http\Controllers\Api\V1\Admin\PageAdminController;
use App\Http\Controllers\Api\V1\Admin\PageRevisionAdminController;
use App\Http\Controllers\Api\V1\Admin\PluginAdminController;
use App\Http\Controllers\Api\V1\Admin\RedirectAdminController;
use App\Http\Controllers\Api\V1\Admin\SeoAdminController;
use App\Http\Controllers\Api\V1\Admin\SettingsAdminController;
use App\Http\Controllers\Api\V1\Admin\TaxonomyAdminController;
use App\Http\Controllers\Api\V1\Admin\UserAdminController;
use App\Http\Controllers\Api\V1\Admin\WebhookAdminController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CptController;
use App\Http\Controllers\Api\V1\FormController;
use App\Http\Controllers\Api\V1\MediaController;
use App\Http\Controllers\Api\V1\MenuPublicController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\SettingsPublicController;
use App\Http\Controllers\Api\V1\TaxonomyPublicController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.cors')->prefix('v1')->group(function () {
    // String Translations Public API Dictionary
    Route::get('/translations/{locale}', [TranslationApiController::class, 'index']);

    // Auth
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('api.auth');
    Route::get('/me', [AuthController::class, 'me'])->middleware('api.auth');

    // Public content (read-only)
    Route::get('/pages', [PageController::class, 'index']);
    Route::get('/pages/{slug}', [PageController::class, 'show']);
    Route::get('/cpt/{type}', [CptController::class, 'index']);
    Route::get('/cpt/{type}/{slug}', [CptController::class, 'show']);
    Route::get('/menus', [MenuPublicController::class, 'index']);
    Route::get('/taxonomies', [TaxonomyPublicController::class, 'index']);
    Route::get('/taxonomies/{slug}/terms', [TaxonomyPublicController::class, 'listTerms']);
    Route::get('/settings/public', [SettingsPublicController::class, 'index']);
    Route::get('/redirects', [RedirectAdminController::class, 'publicList']);

    // Media — public read, authenticated write only
    Route::get('/media', [MediaController::class, 'index']);
    Route::get('/media/{id}', [MediaController::class, 'show']);

    // Form submission (public, with throttle)
    Route::post('/forms/{slug}/submit', [FormController::class, 'submit'])
        ->middleware('throttle:30,1');

    // Admin Headless Management CRUD API
    Route::prefix('admin')->middleware('api.auth')->group(function () {
        // CPT Schema Management
        Route::get('/cpt', [CptAdminController::class, 'index']);
        Route::post('/cpt', [CptAdminController::class, 'store']);
        Route::get('/cpt/{id}', [CptAdminController::class, 'show']);
        Route::put('/cpt/{id}', [CptAdminController::class, 'update']);
        Route::delete('/cpt/{id}', [CptAdminController::class, 'destroy']);
        Route::get('/cpt/{id}/fields', [CptAdminController::class, 'listFields']);
        Route::post('/cpt/{id}/fields', [CptAdminController::class, 'storeField']);
        Route::put('/cpt/{id}/fields/{fieldId}', [CptAdminController::class, 'updateField']);
        Route::delete('/cpt/{id}/fields/{fieldId}', [CptAdminController::class, 'destroyField']);

        // CPT Entries & Relationships CRUD
        Route::get('/cpt/{type}/entries', [CptEntryAdminController::class, 'index']);
        Route::post('/cpt/{type}/entries', [CptEntryAdminController::class, 'store']);
        Route::get('/cpt/{type}/entries/{id}', [CptEntryAdminController::class, 'show']);
        Route::put('/cpt/{type}/entries/{id}', [CptEntryAdminController::class, 'update']);
        Route::delete('/cpt/{type}/entries/{id}', [CptEntryAdminController::class, 'destroy']);

        // Pages & Page Blocks CRUD
        Route::get('/pages', [PageAdminController::class, 'index']);
        Route::post('/pages', [PageAdminController::class, 'store']);
        Route::get('/pages/{id}', [PageAdminController::class, 'show']);
        Route::put('/pages/{id}', [PageAdminController::class, 'update']);
        Route::delete('/pages/{id}', [PageAdminController::class, 'destroy']);
        Route::get('/pages/{id}/blocks', [PageAdminController::class, 'listBlocks']);
        Route::post('/pages/{id}/blocks', [PageAdminController::class, 'storeBlock']);
        Route::put('/pages/{id}/blocks/{blockId}', [PageAdminController::class, 'updateBlock']);
        Route::delete('/pages/{id}/blocks/{blockId}', [PageAdminController::class, 'destroyBlock']);
        Route::get('/pages/{id}/seo', [SeoAdminController::class, 'getPageSeo']);
        Route::put('/pages/{id}/seo', [SeoAdminController::class, 'updatePageSeo']);

        // CPT Entries SEO
        Route::get('/cpt/{type}/entries/{id}/seo', [SeoAdminController::class, 'getCptEntrySeo']);
        Route::put('/cpt/{type}/entries/{id}/seo', [SeoAdminController::class, 'updateCptEntrySeo']);

        // Media Management API
        Route::post('/media/upload', [MediaAdminController::class, 'upload']);
        Route::put('/media/{id}', [MediaAdminController::class, 'update']);
        Route::delete('/media/{id}', [MediaAdminController::class, 'destroy']);
        Route::post('/media/bulk-delete', [MediaAdminController::class, 'bulkDelete']);

        // Menus Management API
        Route::get('/menus', [MenuAdminController::class, 'index']);
        Route::post('/menus', [MenuAdminController::class, 'store']);
        Route::get('/menus/{id}', [MenuAdminController::class, 'show']);
        Route::put('/menus/{id}', [MenuAdminController::class, 'update']);
        Route::delete('/menus/{id}', [MenuAdminController::class, 'destroy']);
        Route::post('/menus/reorder', [MenuAdminController::class, 'reorder']);

        // Form Builder & Entries List API
        Route::get('/forms', [FormAdminController::class, 'index']);
        Route::post('/forms', [FormAdminController::class, 'store']);
        Route::get('/forms/{id}', [FormAdminController::class, 'show']);
        Route::put('/forms/{id}', [FormAdminController::class, 'update']);
        Route::delete('/forms/{id}', [FormAdminController::class, 'destroy']);
        Route::get('/forms/{id}/entries', [FormAdminController::class, 'listEntries']);
        Route::delete('/forms/{id}/entries/{entryId}', [FormAdminController::class, 'destroyEntry']);

        // Taxonomies & Terms API
        Route::get('/taxonomies', [TaxonomyAdminController::class, 'index']);
        Route::post('/taxonomies', [TaxonomyAdminController::class, 'store']);
        Route::put('/taxonomies/{id}', [TaxonomyAdminController::class, 'update']);
        Route::delete('/taxonomies/{id}', [TaxonomyAdminController::class, 'destroy']);
        Route::get('/taxonomies/{id}/terms', [TaxonomyAdminController::class, 'listTerms']);
        Route::post('/taxonomies/{id}/terms', [TaxonomyAdminController::class, 'storeTerm']);
        Route::put('/taxonomies/{id}/terms/{termId}', [TaxonomyAdminController::class, 'updateTerm']);
        Route::delete('/taxonomies/{id}/terms/{termId}', [TaxonomyAdminController::class, 'destroyTerm']);

        // Settings API
        Route::get('/settings', [SettingsAdminController::class, 'index']);
        Route::get('/settings/{group}', [SettingsAdminController::class, 'show']);
        Route::put('/settings/{group}', [SettingsAdminController::class, 'update']);

        // Redirect Rules API
        Route::get('/redirects', [RedirectAdminController::class, 'index']);
        Route::post('/redirects', [RedirectAdminController::class, 'store']);
        Route::put('/redirects/{id}', [RedirectAdminController::class, 'update']);
        Route::delete('/redirects/{id}', [RedirectAdminController::class, 'destroy']);

        // Webhooks Engine API
        Route::get('/webhooks', [WebhookAdminController::class, 'index']);
        Route::post('/webhooks', [WebhookAdminController::class, 'store']);
        Route::put('/webhooks/{id}', [WebhookAdminController::class, 'update']);
        Route::delete('/webhooks/{id}', [WebhookAdminController::class, 'destroy']);
        Route::get('/webhooks/{id}/deliveries', [WebhookAdminController::class, 'deliveries']);

        // User & Role Management API
        Route::get('/users', [UserAdminController::class, 'index']);
        Route::post('/users', [UserAdminController::class, 'store']);
        Route::get('/users/{id}', [UserAdminController::class, 'show']);
        Route::put('/users/{id}', [UserAdminController::class, 'update']);
        Route::delete('/users/{id}', [UserAdminController::class, 'destroy']);
        Route::get('/roles', [UserAdminController::class, 'listRoles']);

        // Activity Logs API
        Route::get('/activity-logs', [ActivityLogAdminController::class, 'index']);

        // Plugin Management API
        Route::get('/plugins', [PluginAdminController::class, 'index']);
        Route::post('/plugins/{slug}/toggle', [PluginAdminController::class, 'toggle']);

        // Page Revisions API
        Route::get('/pages/{id}/revisions', [PageRevisionAdminController::class, 'index']);
        Route::post('/pages/{id}/revisions/{revisionId}/restore', [PageRevisionAdminController::class, 'restore']);

        // Email Templates API
        Route::get('/email-templates', [EmailTemplateAdminController::class, 'index']);
        Route::get('/email-templates/{id}', [EmailTemplateAdminController::class, 'show']);
        Route::put('/email-templates/{id}', [EmailTemplateAdminController::class, 'update']);

        // IndexNow & Sitemap Indexing Logs API
        Route::get('/seo/indexing-logs', [IndexingLogAdminController::class, 'index']);

        // Appearance & Themes API
        Route::get('/themes', [AppearanceAdminController::class, 'index']);
        Route::post('/themes/upload', [AppearanceAdminController::class, 'upload']);
        Route::post('/themes/{slug}/activate', [AppearanceAdminController::class, 'activate']);
    });

    // OpenAPI spec stub
    Route::get('/openapi.json', function () {
        return response()->json([
            'openapi' => '3.1.0',
            'info' => [
                'title' => setting('site_name', config('app.name')).' API',
                'version' => '1.0.0',
                'description' => 'Public content + authenticated mutation surface. Partial specification. Full API docs at /docs/api-reference.md.',
            ],
            'servers' => [['url' => url('/api/v1')]],
            'paths' => [
                '/pages' => ['get' => ['summary' => 'List published pages']],
                '/pages/{slug}' => ['get' => ['summary' => 'Get a single page']],
                '/cpt/{type}' => ['get' => ['summary' => 'List CPT entries']],
                '/cpt/{type}/{slug}' => ['get' => ['summary' => 'Get a CPT entry']],
                '/media' => ['get' => ['summary' => 'List media']],
                '/forms/{slug}/submit' => ['post' => ['summary' => 'Submit a form']],
                '/auth/login' => ['post' => ['summary' => 'Exchange credentials for a token']],
                '/auth/logout' => ['post' => ['summary' => 'Revoke current token']],
                '/me' => ['get' => ['summary' => 'Get current authenticated user']],
            ],
        ]);
    });
});
