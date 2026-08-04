<?php

use Illuminate\Support\Facades\Route;

$adminPath = config('admin.path', config('cms.path', 'admin'));

Route::middleware(['web', 'auth', 'permission:youtube.view'])->prefix("{$adminPath}/youtube")->name('admin.youtube.')->group(function () {
    Route::get('/', function () {
        return view('youtube::admin.index');
    })->name('index');

    Route::get('/settings', function () {
        return view('youtube::admin.settings');
    })->name('settings');
});
