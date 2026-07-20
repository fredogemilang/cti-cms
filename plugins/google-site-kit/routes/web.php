<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Plugins\GoogleSiteKit\Services\GoogleApiService;

$adminPath = config('admin.path', config('cms.path', 'admin'));

Route::middleware(['web', 'auth', 'permission:google-site-kit.view'])
    ->prefix("{$adminPath}/google-site-kit")
    ->name('admin.google-site-kit.')
    ->group(function () {

        // Dashboard
        Route::get('/', function () {
            return view('google-site-kit::admin.index');
        })->name('index');

        // Settings
        Route::get('/settings', function () {
            return view('google-site-kit::admin.settings');
        })->name('settings')->middleware('permission:google-site-kit.edit');

        // OAuth redirect trigger
        Route::get('/connect', function (GoogleApiService $api) {
            $url = $api->getAuthUrl();
            if (empty($url)) {
                return redirect()->route('admin.google-site-kit.settings')
                    ->with('error', 'Google Client ID is missing. Please configure credentials first.');
            }

            return redirect()->away($url);
        })->name('connect')->middleware('permission:google-site-kit.edit');

        // OAuth disconnect trigger
        Route::post('/disconnect', function (GoogleApiService $api) {
            $api->disconnect();

            return redirect()->route('admin.google-site-kit.settings')
                ->with('success', 'Disconnected from Google account.');
        })->name('disconnect')->middleware('permission:google-site-kit.edit');

        // OAuth Callback handler (public but within session/auth)
        Route::get('/oauth-callback', function (Request $request, GoogleApiService $api) {
            if ($request->has('error')) {
                return redirect()->route('admin.google-site-kit.settings')
                    ->with('error', 'Access denied or error: '.$request->input('error'));
            }

            $code = $request->input('code');
            if (! $code) {
                return redirect()->route('admin.google-site-kit.settings')
                    ->with('error', 'Missing authorization code.');
            }

            if ($api->handleCallback($code)) {
                return redirect()->route('admin.google-site-kit.index')
                    ->with('success', 'Connected to Google Account successfully!');
            }

            return redirect()->route('admin.google-site-kit.settings')
                ->with('error', 'Failed to connect. Please check your credentials and logs.');
        })->name('callback')->middleware('permission:google-site-kit.edit');

    });
