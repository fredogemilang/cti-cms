<?php

namespace App\Mcp\Tools\Settings;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\Activity;
use App\Models\Setting;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Update a CMS setting value. Requires mcp.admin ability. Cannot modify sensitive settings (passwords, API keys). Use get-settings to see available keys.')]
class UpdateSettingTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'key' => $schema->string()
                ->description('The setting key to update (e.g., "site_name", "seo_llms_enabled").')
                ->required(),

            'value' => $schema->string()
                ->description('The new value for the setting. Arrays/objects should be JSON-encoded.')
                ->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.admin');

        $validated = $request->validate([
            'key' => 'required|string',
            'value' => 'present',
        ]);

        $key = $validated['key'];

        // Block all sensitive and encrypted keys dynamically
        if (Setting::isSensitiveKey($key)) {
            return Response::error("Setting '{$key}' contains sensitive credentials and cannot be modified via MCP.");
        }

        // Prevent creating arbitrary unregistered setting keys
        $existingSetting = Setting::where('key', $key)->first();
        if (! $existingSetting) {
            return Response::error("Setting '{$key}' does not exist. Only existing registered settings can be updated.");
        }

        $token = McpAbilityGuard::resolveToken();
        $oldValue = setting($key);

        // Use Setting::set() to ensure encryption at rest, cache invalidation, and proper data casting
        $setting = Setting::set($key, $validated['value'], $existingSetting->group, $existingSetting->type);

        // Audit log
        Activity::create([
            'user_id' => $token?->tokenable_id,
            'action' => 'updated',
            'subject_type' => Setting::class,
            'subject_id' => $setting->id,
            'description' => "Setting '{$key}' updated via MCP",
            'properties' => [
                'source' => 'mcp',
                'token_id' => $token?->id,
                'key' => $key,
                'old_value' => $oldValue,
                'new_value' => $validated['value'],
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return Response::structured([
            'success' => true,
            'setting' => [
                'key' => $key,
                'old_value' => $oldValue,
                'new_value' => $validated['value'],
            ],
        ]);
    }
}
