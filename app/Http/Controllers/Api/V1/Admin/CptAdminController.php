<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomPostType;
use App\Models\MetaField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CptAdminController extends Controller
{
    /**
     * List all Custom Post Types
     */
    public function index()
    {
        $cpts = CustomPostType::with('metaFields')->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $cpts,
        ]);
    }

    /**
     * Create a new Custom Post Type
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'singular_label' => 'required|string|max:255',
            'plural_label' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:custom_post_types,slug',
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_hierarchical' => 'nullable|boolean',
            'show_in_menu' => 'nullable|boolean',
            'show_in_rest' => 'nullable|boolean',
            'has_archive' => 'nullable|boolean',
            'supports' => 'nullable|array',
            'settings' => 'nullable|array',
        ]);

        $validated['slug'] = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        $cpt = CustomPostType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Custom Post Type created successfully.',
            'data' => $cpt,
        ], 201);
    }

    /**
     * Show a Custom Post Type details
     */
    public function show(int $id)
    {
        $cpt = CustomPostType::with('metaFields')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $cpt,
        ]);
    }

    /**
     * Update an existing Custom Post Type
     */
    public function update(Request $request, int $id)
    {
        $cpt = CustomPostType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'singular_label' => 'sometimes|required|string|max:255',
            'plural_label' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:custom_post_types,slug,'.$cpt->id,
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_hierarchical' => 'nullable|boolean',
            'supports' => 'nullable|array',
            'settings' => 'nullable|array',
        ]);

        $cpt->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Custom Post Type updated successfully.',
            'data' => $cpt,
        ]);
    }

    /**
     * Delete a Custom Post Type
     */
    public function destroy(int $id)
    {
        $cpt = CustomPostType::findOrFail($id);
        $cpt->delete();

        return response()->json([
            'success' => true,
            'message' => 'Custom Post Type deleted successfully.',
        ]);
    }

    /**
     * List MetaFields for a CPT
     */
    public function listFields(int $id)
    {
        $cpt = CustomPostType::findOrFail($id);
        $fields = MetaField::where('fieldable_type', CustomPostType::class)
            ->where('fieldable_id', $cpt->id)
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $fields,
        ]);
    }

    /**
     * Create MetaField for a CPT
     */
    public function storeField(Request $request, int $id)
    {
        $cpt = CustomPostType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_required' => 'nullable|boolean',
            'options' => 'nullable|array',
            'order' => 'nullable|integer',
            'field_group' => 'nullable|string|max:255',
        ]);

        $validated['fieldable_type'] = CustomPostType::class;
        $validated['fieldable_id'] = $cpt->id;

        $field = MetaField::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Meta field created successfully.',
            'data' => $field,
        ], 201);
    }

    /**
     * Update MetaField
     */
    public function updateField(Request $request, int $id, int $fieldId)
    {
        $field = MetaField::where('fieldable_type', CustomPostType::class)
            ->where('fieldable_id', $id)
            ->where('id', $fieldId)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'label' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:50',
            'description' => 'nullable|string',
            'is_required' => 'nullable|boolean',
            'options' => 'nullable|array',
            'order' => 'nullable|integer',
            'field_group' => 'nullable|string|max:255',
            'advanced_settings' => 'nullable|array',
        ]);

        $field->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Meta field updated successfully.',
            'data' => $field,
        ]);
    }

    /**
     * Delete MetaField
     */
    public function destroyField(int $id, int $fieldId)
    {
        $field = MetaField::where('fieldable_type', CustomPostType::class)
            ->where('fieldable_id', $id)
            ->where('id', $fieldId)
            ->firstOrFail();

        $field->delete();

        return response()->json([
            'success' => true,
            'message' => 'Meta field deleted successfully.',
        ]);
    }
}
