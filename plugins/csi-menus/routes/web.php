<?php

use Illuminate\Support\Facades\Route;

$adminPath = config('admin.path', config('cms.path', 'ctrlpanel'));

Route::middleware(['web', 'auth', 'enforce-2fa', 'permission:menus.view'])
    ->prefix("{$adminPath}/csi-menus")
    ->name('admin.csi-menus.')
    ->group(function () {
        Route::get('/', fn () => view('csi-menus::index'))->name('index');
    });
