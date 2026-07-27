<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Http\Request;

class WebhookAdminController extends Controller
{
    /**
     * List all webhooks
     */
    public function index()
    {
        $webhooks = Webhook::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $webhooks,
        ]);
    }

    /**
     * Create a new Webhook
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'events' => 'required|array',
            'secret' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $webhook = Webhook::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Webhook created successfully.',
            'data' => $webhook,
        ], 201);
    }

    /**
     * Update a Webhook
     */
    public function update(Request $request, int $id)
    {
        $webhook = Webhook::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'url' => 'sometimes|required|url|max:500',
            'events' => 'sometimes|required|array',
            'secret' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $webhook->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Webhook updated successfully.',
            'data' => $webhook,
        ]);
    }

    /**
     * Delete a Webhook
     */
    public function destroy(int $id)
    {
        $webhook = Webhook::findOrFail($id);
        $webhook->delete();

        return response()->json([
            'success' => true,
            'message' => 'Webhook deleted successfully.',
        ]);
    }

    /**
     * List deliveries log for a Webhook
     */
    public function deliveries(int $id)
    {
        $webhook = Webhook::findOrFail($id);
        $deliveries = WebhookDelivery::where('webhook_id', $webhook->id)->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $deliveries,
        ]);
    }
}
