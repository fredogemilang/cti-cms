<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

class SettingsPublicController extends Controller
{
    /**
     * Get public site identity and configuration settings
     */
    public function index()
    {
        $settings = [
            'site_name' => setting('site_name', config('app.name', 'Central Data Technology')),
            'site_tagline' => setting('site_tagline', ''),
            'site_logo' => setting('site_logo') ? asset('storage/'.setting('site_logo')) : null,
            'site_favicon' => setting('site_favicon') ? asset('storage/'.setting('site_favicon')) : null,
            'default_locale' => setting('default_locale', config('app.locale', 'en')),
            'available_locales' => array_filter(array_map('trim', explode(',', (string) setting('available_locales', 'en,id')))),
            'locale_url_structure' => setting('locale_url_structure', 'prefix'),
            'locale_prefix_hide_default' => (bool) setting('locale_prefix_hide_default', true),
            'active_theme' => setting('active_theme', 'default'),
            'maintenance_mode' => (bool) setting('maintenance_mode', false),
        ];

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }
}
