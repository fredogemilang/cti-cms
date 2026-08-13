<?php

use Illuminate\Support\Facades\Route;

$adminPath = config('admin.path', config('cms.path', 'admin'));

Route::middleware(['web', 'auth', 'permission:rma-xyora.view'])->prefix("{$adminPath}/rma-xyora")->name('admin.rma-xyora.')->group(function () {
    Route::get('/', function () {
        return view('rma-xyora::admin.index');
    })->name('index');
});
