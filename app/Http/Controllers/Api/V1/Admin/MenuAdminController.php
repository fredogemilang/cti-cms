<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuAdminController extends Controller
{
    /**
     * List all menu items including inactive ones
     */
    public function index()
    {
        $items = MenuItem::ordered()
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->ordered()])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    /**
     * Create a new MenuItem
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'permission' => 'nullable|string|max:255',
            'parent_id' => 'nullable|integer|exists:menu_items,id',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $item = MenuItem::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Menu item created successfully.',
            'data' => $item,
        ], 201);
    }

    /**
     * Show MenuItem details
     */
    public function show(int $id)
    {
        $item = MenuItem::with('children')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $item,
        ]);
    }

    /**
     * Update an existing MenuItem
     */
    public function update(Request $request, int $id)
    {
        $item = MenuItem::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'permission' => 'nullable|string|max:255',
            'parent_id' => 'nullable|integer|exists:menu_items,id',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Menu item updated successfully.',
            'data' => $item,
        ]);
    }

    /**
     * Delete a MenuItem
     */
    public function destroy(int $id)
    {
        $item = MenuItem::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu item deleted successfully.',
        ]);
    }

    /**
     * Reorder menu items tree
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:menu_items,id',
            'items.*.order' => 'required|integer',
            'items.*.parent_id' => 'nullable|integer|exists:menu_items,id',
        ]);

        foreach ($validated['items'] as $menuData) {
            MenuItem::where('id', $menuData['id'])->update([
                'order' => $menuData['order'],
                'parent_id' => $menuData['parent_id'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Menu items reordered successfully.',
        ]);
    }
}
