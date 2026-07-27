<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use Illuminate\Http\Request;

class PluginAdminController extends Controller
{
    /**
     * List all installed plugins
     */
    public function index()
    {
        $plugins = Plugin::all();

        return response()->json([
            'success' => true,
            'data' => $plugins,
        ]);
    }

    /**
     * Toggle plugin active/inactive status
     */
    public function toggle(Request $request, string $slug)
    {
        $plugin = Plugin::where('slug', $slug)->firstOrFail();
        $isActive = $request->boolean('is_active', ! $plugin->is_active);

        $plugin->update([
            'is_active' => $isActive,
            'activated_at' => $isActive ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Plugin [{$plugin->name}] ".($isActive ? 'activated' : 'deactivated').' successfully.',
            'data' => $plugin,
        ]);
    }
}
