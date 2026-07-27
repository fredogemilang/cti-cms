<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Redirect;
use Illuminate\Http\Request;

class RedirectAdminController extends Controller
{
    /**
     * List active redirect rules (Public / Edge CDN endpoint)
     */
    public function publicList()
    {
        $redirects = Redirect::where('is_active', true)
            ->select(['source_path', 'target_url', 'status_code'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $redirects,
        ]);
    }

    /**
     * List all redirect rules (Admin endpoint)
     */
    public function index(Request $request)
    {
        $perPage = min(100, max(1, (int) $request->query('per_page', 20)));
        $redirects = Redirect::latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $redirects,
        ]);
    }

    /**
     * Create a new Redirect rule
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_path' => 'required|string|max:255',
            'target_url' => 'required|string|max:255',
            'status_code' => 'nullable|integer|in:301,302,307,308',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['status_code'] = $validated['status_code'] ?? 301;
        $redirect = Redirect::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Redirect rule created successfully.',
            'data' => $redirect,
        ], 201);
    }

    /**
     * Update a Redirect rule
     */
    public function update(Request $request, int $id)
    {
        $redirect = Redirect::findOrFail($id);

        $validated = $request->validate([
            'source_path' => 'sometimes|required|string|max:255',
            'target_url' => 'sometimes|required|string|max:255',
            'status_code' => 'nullable|integer|in:301,302,307,308',
            'is_active' => 'nullable|boolean',
        ]);

        $redirect->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Redirect rule updated successfully.',
            'data' => $redirect,
        ]);
    }

    /**
     * Delete a Redirect rule
     */
    public function destroy(int $id)
    {
        $redirect = Redirect::findOrFail($id);
        $redirect->delete();

        return response()->json([
            'success' => true,
            'message' => 'Redirect rule deleted successfully.',
        ]);
    }
}
