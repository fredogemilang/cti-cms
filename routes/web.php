<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

// Get admin path from config
$adminPath = config('admin.path', 'admin');

// Public homepage
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Public SEO
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [\App\Http\Controllers\RobotsController::class, 'index'])->name('robots');

// Public Form Submission
Route::prefix('forms')->name('forms.')->group(function () {
    Route::get('/{slug}', [\App\Http\Controllers\FormSubmissionController::class, 'show'])->name('show');
    Route::post('/{slug}/submit', [\App\Http\Controllers\FormSubmissionController::class, 'submit'])->name('submit');
    Route::post('/{slug}/ajax', [\App\Http\Controllers\FormSubmissionController::class, 'submitAjax'])->name('submit.ajax');
    Route::get('/{slug}/success', [\App\Http\Controllers\FormSubmissionController::class, 'success'])->name('success');
});

// Admin base path redirect
Route::get("/{$adminPath}", [\App\Http\Controllers\Admin\AdminController::class, 'index'])->name('admin.index');

// Authentication Routes (under admin path)
Route::prefix($adminPath)->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
});

// Admin Routes
Route::prefix($adminPath)->name('admin.')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:dashboard.view');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])
        ->name('profile.index');

    // Users Management
    Route::middleware('permission:users.view')->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    });

    // Roles Management
    // Route::middleware('permission:roles.view')->group(function () {
    //     Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    //     Route::get('roles/{role}/permissions', [\App\Http\Controllers\Admin\RoleController::class, 'editPermissions'])
    //         ->name('roles.permissions.edit')
    //         ->middleware('permission:roles.assign-permissions');
    //     Route::put('roles/{role}/permissions', [\App\Http\Controllers\Admin\RoleController::class, 'updatePermissions'])
    //         ->name('roles.permissions.update')
    //         ->middleware('permission:roles.assign-permissions');
    // });

    // Role & Permission Merged View
    Route::middleware('permission:roles.view')->group(function () {
        Route::get('role-permission', [\App\Http\Controllers\Admin\RolePermissionController::class, 'index'])
            ->name('role-permission.index');
        Route::post('role-permission/role', [\App\Http\Controllers\Admin\RolePermissionController::class, 'storeRole'])
            ->name('role-permission.store-role')
            ->middleware('permission:roles.create');
        Route::put('role-permission/role/{role}', [\App\Http\Controllers\Admin\RolePermissionController::class, 'updateRole'])
            ->name('role-permission.update-role')
            ->middleware('permission:roles.edit');
        Route::delete('role-permission/role/{role}', [\App\Http\Controllers\Admin\RolePermissionController::class, 'deleteRole'])
            ->name('role-permission.delete-role')
            ->middleware('permission:roles.delete');
        Route::post('role-permission/clone/{role}', [\App\Http\Controllers\Admin\RolePermissionController::class, 'cloneRole'])
            ->name('role-permission.clone-role')
            ->middleware('permission:roles.create');
        Route::post('role-permission/toggle/{role}', [\App\Http\Controllers\Admin\RolePermissionController::class, 'togglePermission'])
            ->name('role-permission.toggle-permission')
            ->middleware('permission:roles.assign-permissions');
    });

    // Permissions Management
    // Route::middleware('permission:permissions.view')->group(function () {
    //     Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class);
    // });

    // Menus Management
    Route::middleware('permission:menus.view')->group(function () {
        Route::resource('menus', \App\Http\Controllers\Admin\MenuController::class);
        Route::post('menus/reorder', [\App\Http\Controllers\Admin\MenuController::class, 'reorder'])
            ->name('menus.reorder');
    });

    // Media Management
    Route::middleware('permission:media.view')->group(function () {
        Route::get('media', [\App\Http\Controllers\Admin\MediaController::class, 'index'])->name('media.index');
        Route::get('media/create', [\App\Http\Controllers\Admin\MediaController::class, 'create'])
            ->name('media.create')
            ->middleware('permission:media.upload');
        Route::post('media/upload', [\App\Http\Controllers\Admin\MediaController::class, 'upload'])
            ->name('media.upload')
            ->middleware('permission:media.upload');
        Route::put('media/{media}', [\App\Http\Controllers\Admin\MediaController::class, 'update'])
            ->name('media.update')
            ->middleware('permission:media.edit');
        Route::delete('media/{media}', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])
            ->name('media.destroy')
            ->middleware('permission:media.delete');
        Route::post('media/bulk-delete', [\App\Http\Controllers\Admin\MediaController::class, 'bulkDelete'])
            ->name('media.bulk-delete')
            ->middleware('permission:media.delete');
    });

    // Plugin Management
    Route::middleware('permission:plugins.view')->group(function () {
        Route::get('plugins', [\App\Http\Controllers\Admin\PluginController::class, 'index'])->name('plugins.index');
        Route::post('plugins', [\App\Http\Controllers\Admin\PluginController::class, 'store'])->name('plugins.store');
        Route::post('plugins/{plugin}/activate', [\App\Http\Controllers\Admin\PluginController::class, 'activate'])->name('plugins.activate');
        Route::post('plugins/{plugin}/deactivate', [\App\Http\Controllers\Admin\PluginController::class, 'deactivate'])->name('plugins.deactivate');
        Route::delete('plugins/{plugin}', [\App\Http\Controllers\Admin\PluginController::class, 'destroy'])->name('plugins.destroy');
    });

    // Theme Management
    Route::prefix('appearance')->name('themes.')->middleware('permission:themes.view')->group(function () {
        Route::get('/themes', [\App\Http\Controllers\Admin\ThemesController::class, 'index'])->name('index');
        Route::post('/themes/upload', [\App\Http\Controllers\Admin\ThemesController::class, 'upload'])->name('upload');
        Route::post('/themes/{theme}/activate', [\App\Http\Controllers\Admin\ThemesController::class, 'activate'])->name('activate');
        Route::delete('/themes/{theme}', [\App\Http\Controllers\Admin\ThemesController::class, 'destroy'])->name('destroy');
    });

    // Custom Post Types Management
    Route::prefix('cpt')->name('cpt.')->group(function () {
        Route::get('/', function () {
            return view('admin.cpt.index');
        })->name('index');
        Route::get('/create', function () {
            return view('admin.cpt.create');
        })->name('create');
        Route::get('/{id}/edit', function ($id) {
            return view('admin.cpt.edit', ['id' => $id]);
        })->name('edit');

        // WordPress CPT Migration
        Route::get('/migration/wordpress', function () {
            return view('admin.cpt.wordpress-migration');
        })->name('wordpress-migration');
        
        // CPT Entries (Content) Management
        Route::prefix('entries/{postTypeSlug}')->name('entries.')->group(function () {
            Route::get('/', function ($postTypeSlug) {
                $postType = \App\Models\CustomPostType::where('slug', $postTypeSlug)->firstOrFail();
                return view('admin.cpt.entries.index', ['postType' => $postType]);
            })->name('index');
            Route::get('/create', function ($postTypeSlug) {
                $postType = \App\Models\CustomPostType::where('slug', $postTypeSlug)->firstOrFail();
                return view('admin.cpt.entries.create', ['postType' => $postType]);
            })->name('create');
            Route::get('/{id}/edit', function ($postTypeSlug, $id) {
                $postType = \App\Models\CustomPostType::where('slug', $postTypeSlug)->firstOrFail();
                return view('admin.cpt.entries.edit', ['postType' => $postType, 'id' => $id]);
            })->name('edit');
        });
    });


    // Pages Management
    Route::prefix('pages')->name('pages.')->middleware('permission:pages.view')->group(function () {
        Route::get('/', function () {
            return view('admin.pages.index');
        })->name('index');
        Route::get('/create', function () {
            return view('admin.pages.create');
        })->name('create')->middleware('permission:pages.create');
        Route::get('/{id}/edit', function ($id) {
            return view('admin.pages.edit', ['id' => $id]);
        })->name('edit')->middleware('permission:pages.edit');
        Route::get('/{id}/preview', [\App\Http\Controllers\PageController::class, 'preview'])
            ->name('preview')->middleware('permission:pages.edit');
    });

    // Forms Management
    Route::prefix('forms')->name('forms.')->middleware('permission:forms.view')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\FormController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\FormController::class, 'create'])
            ->name('create')
            ->middleware('permission:forms.create');
        Route::post('/', [\App\Http\Controllers\Admin\FormController::class, 'store'])
            ->name('store')
            ->middleware('permission:forms.create');
        Route::get('/{form}', [\App\Http\Controllers\Admin\FormController::class, 'show'])->name('show');
        Route::get('/{form}/edit', [\App\Http\Controllers\Admin\FormController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:forms.edit');
        Route::put('/{form}', [\App\Http\Controllers\Admin\FormController::class, 'update'])
            ->name('update')
            ->middleware('permission:forms.edit');
        Route::delete('/{form}', [\App\Http\Controllers\Admin\FormController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:forms.delete');
        Route::get('/{form}/entries', [\App\Http\Controllers\Admin\FormController::class, 'entries'])
            ->name('entries');
        Route::get('/{form}/export', [\App\Http\Controllers\Admin\FormController::class, 'exportEntries'])
            ->name('export');
        Route::post('/{form}/toggle', [\App\Http\Controllers\Admin\FormController::class, 'toggleStatus'])
            ->name('toggle')
            ->middleware('permission:forms.edit');
        Route::delete('/entries/{entry}', [\App\Http\Controllers\Admin\FormController::class, 'deleteEntry'])
            ->name('entries.delete')
            ->middleware('permission:forms.delete');
    });

    // Audit log
    Route::middleware('permission:activity.view')->group(function () {
        Route::get('/activity', function () {
            return view('admin.activity.index');
        })->name('activity.index');
    });

    // Settings (generic, group-based)
    Route::prefix('settings')->name('settings.')->middleware('permission:settings.view')->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.settings.show', 'general');
        })->name('index');
        Route::get('/{group}', function (string $group) {
            abort_unless(app(\App\Services\SettingsRegistry::class)->hasGroup($group), 404);
            return view('admin.settings.show', ['group' => $group]);
        })->name('show');
    });

    // Custom Taxonomies Management
    Route::prefix('taxonomies')->name('taxonomies.')->group(function () {
        Route::get('/', function () {
            return view('admin.taxonomies.index');
        })->name('index');
        Route::get('/create', function () {
            return view('admin.taxonomies.create');
        })->name('create');
        Route::get('/{id}/edit', function ($id) {
            return view('admin.taxonomies.edit', ['id' => $id]);
        })->name('edit');

        // Taxonomy Terms Management
        Route::prefix('{taxonomy}/terms')->name('terms.')->group(function () {
             Route::get('/', function ($taxonomyId) {
                 $taxonomy = \App\Models\CustomTaxonomy::findOrFail($taxonomyId);
                 return view('admin.taxonomies.terms.index', ['taxonomy' => $taxonomy]);
             })->name('index');

             });
    });
});

// Frontend Page Route is now handled in PluginServiceProvider to ensure it runs after plugin routes
// Route::get('/{slug}', [\App\Http\Controllers\PageController::class, 'show'])
//     ->where('slug', '(?!' . preg_quote(config('admin.path', 'admin'), '/') . ')[a-zA-Z0-9\-]+')
//     ->name('pages.show');
