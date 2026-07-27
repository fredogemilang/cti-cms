<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use App\Services\ThemeManager;
use Illuminate\Http\Request;

class AppearanceAdminController extends Controller
{
    protected ThemeManager $themeManager;

    public function __construct(ThemeManager $themeManager)
    {
        $this->themeManager = $themeManager;
    }

    /**
     * List all installed themes
     */
    public function index()
    {
        $themes = Theme::all();
        $activeTheme = setting('active_theme', 'default');

        return response()->json([
            'success' => true,
            'active_theme' => $activeTheme,
            'data' => $themes,
        ]);
    }

    /**
     * Activate a theme
     */
    public function activate(Request $request, string $slug)
    {
        try {
            $this->themeManager->activate($slug);

            return response()->json([
                'success' => true,
                'message' => "Theme [{$slug}] activated successfully.",
                'active_theme' => setting('active_theme'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to activate theme: '.$e->getMessage(),
            ], 500);
        }
    }
}
