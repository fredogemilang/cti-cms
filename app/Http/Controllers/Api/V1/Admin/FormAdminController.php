<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormAdminController extends Controller
{
    /**
     * List all Forms
     */
    public function index()
    {
        $forms = Form::with('fields')->get();

        return response()->json([
            'success' => true,
            'data' => $forms,
        ]);
    }

    /**
     * Create a new Form
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:forms,slug',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'confirmations' => 'nullable|array',
            'notifications' => 'nullable|array',
        ]);

        $validated['slug'] = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        $form = Form::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Form created successfully.',
            'data' => $form,
        ], 201);
    }

    /**
     * Show single Form details with fields & entries count
     */
    public function show(int $id)
    {
        $form = Form::with('fields')->withCount('entries')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $form,
        ]);
    }

    /**
     * Update an existing Form
     */
    public function update(Request $request, int $id)
    {
        $form = Form::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:forms,slug,'.$form->id,
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'confirmations' => 'nullable|array',
            'notifications' => 'nullable|array',
        ]);

        if (isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        $form->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Form updated successfully.',
            'data' => $form,
        ]);
    }

    /**
     * Delete a Form
     */
    public function destroy(int $id)
    {
        $form = Form::findOrFail($id);
        $form->delete();

        return response()->json([
            'success' => true,
            'message' => 'Form deleted successfully.',
        ]);
    }

    /**
     * List submitted entries for a Form
     */
    public function listEntries(Request $request, int $id)
    {
        $form = Form::findOrFail($id);
        $perPage = min(100, max(1, (int) $request->query('per_page', 20)));

        $entries = FormEntry::where('form_id', $form->id)->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $entries,
        ]);
    }

    /**
     * Delete a FormEntry submission
     */
    public function destroyEntry(int $id, int $entryId)
    {
        $entry = FormEntry::where('form_id', $id)->where('id', $entryId)->firstOrFail();
        $entry->delete();

        return response()->json([
            'success' => true,
            'message' => 'Form submission deleted successfully.',
        ]);
    }
}
