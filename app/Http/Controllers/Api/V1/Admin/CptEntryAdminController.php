<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\CptEntry;
use App\Models\CptEntryRelationship;
use App\Models\CustomPostType;
use App\Models\MetaField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CptEntryAdminController extends Controller
{
    /**
     * List entries for a given CPT slug
     */
    public function index(Request $request, string $type)
    {
        $postType = CustomPostType::where('slug', $type)->firstOrFail();
        $perPage = min(100, max(1, (int) $request->query('per_page', 20)));

        $q = CptEntry::where('post_type_id', $postType->id);

        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }

        if ($search = $request->query('q')) {
            $q->where('title', 'like', "%{$search}%");
        }

        $entries = $q->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $entries,
        ]);
    }

    /**
     * Create a new CPT entry with meta values & relationships
     */
    public function store(Request $request, string $type)
    {
        $postType = CustomPostType::where('slug', $type)->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string',
            'featured_image' => 'nullable|string',
            'parent_id' => 'nullable|integer|exists:cpt_entries,id',
            'status' => 'nullable|string|in:draft,published,scheduled,archived',
            'published_at' => 'nullable|date',
            'meta' => 'nullable|array',
            'translations' => 'nullable|array',
            'relationships' => 'nullable|array', // e.g. ["field_name" => [1, 2, 3]]
        ]);

        $validated['post_type_id'] = $postType->id;
        $validated['slug'] = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['status'] = $validated['status'] ?? 'published';
        $validated['published_at'] = $validated['published_at'] ?? now();
        $validated['author_id'] = auth()->id() ?? 1;

        $relationships = $validated['relationships'] ?? [];
        unset($validated['relationships']);

        $entry = CptEntry::create($validated);

        // Process CPT Relationships
        if (! empty($relationships)) {
            $this->syncRelationships($entry, $relationships);
        }

        return response()->json([
            'success' => true,
            'message' => 'CPT Entry created successfully.',
            'data' => $entry->fresh(['parent', 'children']),
        ], 201);
    }

    /**
     * Show single CPT entry details
     */
    public function show(string $type, int $id)
    {
        $postType = CustomPostType::where('slug', $type)->firstOrFail();
        $entry = CptEntry::where('post_type_id', $postType->id)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $entry->load(['postType', 'parent', 'children']),
        ]);
    }

    /**
     * Update an existing CPT entry
     */
    public function update(Request $request, string $type, int $id)
    {
        $postType = CustomPostType::where('slug', $type)->firstOrFail();
        $entry = CptEntry::where('post_type_id', $postType->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string',
            'featured_image' => 'nullable|string',
            'parent_id' => 'nullable|integer|exists:cpt_entries,id',
            'status' => 'nullable|string|in:draft,published,scheduled,archived',
            'published_at' => 'nullable|date',
            'meta' => 'nullable|array',
            'translations' => 'nullable|array',
            'relationships' => 'nullable|array',
        ]);

        if (isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        $relationships = $validated['relationships'] ?? null;
        unset($validated['relationships']);

        $entry->update($validated);

        if ($relationships !== null) {
            $this->syncRelationships($entry, $relationships);
        }

        return response()->json([
            'success' => true,
            'message' => 'CPT Entry updated successfully.',
            'data' => $entry->fresh(['parent', 'children']),
        ]);
    }

    /**
     * Delete a CPT entry
     */
    public function destroy(string $type, int $id)
    {
        $postType = CustomPostType::where('slug', $type)->firstOrFail();
        $entry = CptEntry::where('post_type_id', $postType->id)->findOrFail($id);
        $entry->delete();

        return response()->json([
            'success' => true,
            'message' => 'CPT Entry deleted successfully.',
        ]);
    }

    /**
     * Sync relationship entries for a CPT entry
     */
    protected function syncRelationships(CptEntry $entry, array $relationships): void
    {
        foreach ($relationships as $fieldNameOrId => $childIds) {
            $metaFieldId = is_numeric($fieldNameOrId)
                ? (int) $fieldNameOrId
                : MetaField::where('name', $fieldNameOrId)
                    ->where('fieldable_type', CustomPostType::class)
                    ->where('fieldable_id', $entry->post_type_id)
                    ->value('id');

            if (! $metaFieldId) {
                continue;
            }

            // Remove existing relationships for this field
            CptEntryRelationship::where('parent_entry_id', $entry->id)
                ->where('meta_field_id', $metaFieldId)
                ->delete();

            // Insert new relationships
            if (is_array($childIds)) {
                foreach ($childIds as $order => $childId) {
                    CptEntryRelationship::create([
                        'parent_entry_id' => $entry->id,
                        'child_entry_id' => $childId,
                        'meta_field_id' => $metaFieldId,
                        'order' => $order,
                    ]);
                }
            }
        }
    }
}
