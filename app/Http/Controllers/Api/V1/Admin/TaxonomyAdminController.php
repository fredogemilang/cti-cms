<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomTaxonomy;
use App\Models\TaxonomyTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaxonomyAdminController extends Controller
{
    /**
     * List all Taxonomies
     */
    public function index()
    {
        $taxonomies = CustomTaxonomy::withCount('terms')->get();

        return response()->json([
            'success' => true,
            'data' => $taxonomies,
        ]);
    }

    /**
     * Create a new Taxonomy
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'singular_label' => 'required|string|max:255',
            'plural_label' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:custom_taxonomies,slug',
            'is_hierarchical' => 'nullable|boolean',
            'show_in_menu' => 'nullable|boolean',
            'publicly_queryable' => 'nullable|boolean',
        ]);

        $validated['slug'] = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        $taxonomy = CustomTaxonomy::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Taxonomy created successfully.',
            'data' => $taxonomy,
        ], 201);
    }

    /**
     * Update a Taxonomy
     */
    public function update(Request $request, int $id)
    {
        $taxonomy = CustomTaxonomy::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'singular_label' => 'sometimes|required|string|max:255',
            'plural_label' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:custom_taxonomies,slug,'.$taxonomy->id,
            'is_hierarchical' => 'nullable|boolean',
            'publicly_queryable' => 'nullable|boolean',
        ]);

        $taxonomy->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Taxonomy updated successfully.',
            'data' => $taxonomy,
        ]);
    }

    /**
     * Delete a Taxonomy
     */
    public function destroy(int $id)
    {
        $taxonomy = CustomTaxonomy::findOrFail($id);
        $taxonomy->delete();

        return response()->json([
            'success' => true,
            'message' => 'Taxonomy deleted successfully.',
        ]);
    }

    /**
     * List Terms for a Taxonomy
     */
    public function listTerms(int $id)
    {
        $taxonomy = CustomTaxonomy::findOrFail($id);
        $terms = TaxonomyTerm::where('taxonomy_id', $taxonomy->id)->get();

        return response()->json([
            'success' => true,
            'data' => $terms,
        ]);
    }

    /**
     * Create a TaxonomyTerm
     */
    public function storeTerm(Request $request, int $id)
    {
        $taxonomy = CustomTaxonomy::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|integer|exists:taxonomy_terms,id',
        ]);

        $validated['taxonomy_id'] = $taxonomy->id;
        $validated['slug'] = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        $term = TaxonomyTerm::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Taxonomy term created successfully.',
            'data' => $term,
        ], 201);
    }

    /**
     * Update a TaxonomyTerm
     */
    public function updateTerm(Request $request, int $id, int $termId)
    {
        $term = TaxonomyTerm::where('taxonomy_id', $id)->where('id', $termId)->firstOrFail();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|integer|exists:taxonomy_terms,id',
        ]);

        if (isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        $term->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Taxonomy term updated successfully.',
            'data' => $term,
        ]);
    }

    /**
     * Delete a TaxonomyTerm
     */
    public function destroyTerm(int $id, int $termId)
    {
        $term = TaxonomyTerm::where('taxonomy_id', $id)->where('id', $termId)->firstOrFail();
        $term->delete();

        return response()->json([
            'success' => true,
            'message' => 'Taxonomy term deleted successfully.',
        ]);
    }
}
