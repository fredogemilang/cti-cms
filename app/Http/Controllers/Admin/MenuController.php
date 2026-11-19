<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\AdminMenuBuilder;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display a listing of the menu items for drag-and-drop customizer.
     */
    public function index()
    {
        $builder = app(AdminMenuBuilder::class);
        $menus = $builder->getUnifiedMenuList();

        return view('admin.menus.index', compact('menus'));
    }

    /**
     * Reorder menu items or reset sequence.
     */
    public function reorder(Request $request)
    {
        if ($request->boolean('reset')) {
            Setting::set('admin_sidebar_custom_order', []);

            return response()->json([
                'status' => 'success',
                'message' => 'Menu layout reset to system default.',
            ]);
        }

        $validated = $request->validate([
            'order' => ['required', 'array'],
        ]);

        Setting::set('admin_sidebar_custom_order', array_values($validated['order']));

        return response()->json([
            'status' => 'success',
            'message' => 'Menu layout order updated successfully.',
        ]);
    }
}
