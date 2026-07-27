<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateAdminController extends Controller
{
    /**
     * List all email templates
     */
    public function index()
    {
        $templates = EmailTemplate::withCount('versions')->get();

        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }

    /**
     * Show single email template details with version history
     */
    public function show(int $id)
    {
        $template = EmailTemplate::with('versions')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $template,
        ]);
    }

    /**
     * Update an email template
     */
    public function update(Request $request, int $id)
    {
        $template = EmailTemplate::findOrFail($id);

        $validated = $request->validate([
            'subject' => 'sometimes|required|string|max:255',
            'body_html' => 'sometimes|required|string',
            'body_text' => 'nullable|string',
            'description' => 'nullable|string',
            'change_summary' => 'nullable|string',
        ]);

        $changeSummary = $validated['change_summary'] ?? 'Updated via API';
        unset($validated['change_summary']);

        // Save current version before updating
        $template->versions()->create([
            'subject' => $template->subject,
            'body_html' => $template->body_html,
            'body_text' => $template->body_text,
            'change_summary' => $changeSummary,
            'user_id' => auth()->id() ?? 1,
        ]);

        $template->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Email template updated successfully.',
            'data' => $template->fresh('versions'),
        ]);
    }
}
