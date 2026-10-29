<?php

use Illuminate\Support\Facades\Route;
use Plugins\KouventaPages\Http\Controllers\KouventaPageController;
use Plugins\KouventaPages\Models\KouventaPage;

$adminPath = config('admin.path', config('cms.path', 'admin'));

// Admin Management Routes
Route::middleware(['web', 'auth'])->prefix("{$adminPath}/kouventa-pages")->name('admin.kouventa-pages.')->group(function () {
    Route::get('/', function () {
        return view('kouventa-pages::index');
    })->name('index');

    Route::get('/{id}/edit', function ($id) {
        $page = KouventaPage::findOrFail($id);

        return view('kouventa-pages::edit', ['id' => $id, 'page' => $page]);
    })->name('edit');
});

