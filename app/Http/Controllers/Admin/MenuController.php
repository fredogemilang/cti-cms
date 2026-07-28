<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Permission;
use App\Models\Setting;
use App\Services\AdminMenuBuilder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    /**
     * Display a listing of the menu items.
     */
    public function index()
    {
        $builder = app(AdminMenuBuilder::class);
        $menus = $builder->getUnifiedMenuList();

        return view('admin.menus.index', compact('menus'));
    }

    /**
     * Display the specified menu item.
     */
    public function show(MenuItem $menu)
    {
        $menu->load(['children', 'parent']);

        return view('admin.menus.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified menu item.
     */
    public function edit(MenuItem $menu)
    {
        $parentMenus = MenuItem::whereNull('parent_id')
            ->where('id', '!=', $menu->id)
            ->ordered()
            ->get();
        $permissions = Permission::all();

        return view('admin.menus.edit', compact('menu', 'parentMenus', 'permissions'));
    }

    /**
     * Update the specified menu item in storage.
     */
    public function update(Request $request, MenuItem $menu)
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:menu_items,id', Rule::notIn([$menu->id])],
            'title' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'route' => ['nullable', 'string', 'max:255'],
            'permission' => ['nullable', 'string', 'max:255'],
            'order' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $menu->update($validated);

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu item updated successfully.');
    }

    /**
     * Remove the specified menu item from storage.
     */
    public function destroy(MenuItem $menu)
    {
        // Prevent deleting menu item if it has children
        if ($menu->children()->count() > 0) {
            return redirect()
                ->route('admin.menus.index')
                ->with('error', 'Cannot delete menu item that has sub-items.');
        }

        $menu->delete();

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu item deleted successfully.');
    }

    /**
     * Reorder menu items.
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
