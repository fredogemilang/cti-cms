<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CustomTaxonomy;
use App\Models\TaxonomyTerm;

class TaxonomyPublicController extends Controller
{
    /**
     * List all public taxonomies
     */
    public function index()
    {
        $taxonomies = CustomTaxonomy::where('publicly_queryable', true)->get();

        return response()->json([
            'success' => true,
            'data' => $taxonomies,
        ]);
    }

    /**
     * List terms for a specific taxonomy slug
     */
    public function listTerms(string $slug)
    {
        $taxonomy = CustomTaxonomy::where('slug', $slug)->firstOrFail();
        $terms = TaxonomyTerm::where('taxonomy_id', $taxonomy->id)
            ->withCount('entries')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'taxonomy' => $taxonomy,
            'data' => $terms,
        ]);
    }
}
