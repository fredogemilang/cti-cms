<?php

use Illuminate\Support\Facades\Route;
use Plugins\Posts\Http\Controllers\Api\PostAdminController;
use Plugins\Posts\Http\Controllers\Api\PostPublicController;

Route::middleware('api.cors')->prefix('api/v1')->group(function () {
    // Public Posts API
    Route::get('/posts', [PostPublicController::class, 'index']);
    Route::get('/posts/categories', [PostPublicController::class, 'categories']);
    Route::get('/posts/{slug}', [PostPublicController::class, 'show']);

    // Admin Posts Management CRUD API
    Route::prefix('admin')->group(function () {
        Route::get('/posts', [PostAdminController::class, 'index']);
        Route::post('/posts', [PostAdminController::class, 'store']);
        Route::put('/posts/{id}', [PostAdminController::class, 'update']);
        Route::delete('/posts/{id}', [PostAdminController::class, 'destroy']);
    });
});
