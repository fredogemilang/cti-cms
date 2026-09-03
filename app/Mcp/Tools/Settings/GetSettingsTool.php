<?php

namespace App\Mcp\Tools\Settings;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\Setting;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Get CMS settings. Can retrieve all settings or filter by group (general, seo, media, social, mail). Returns setting keys and their current values.')]
#[IsReadOnly]
#[IsIdempotent]
class GetSettingsTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'group' => $schema->string()
                ->description('Filter by settings group. Leave empty for all settings. Common groups: general, seo, media, social, mail.'),

            'keys' => $schema->array()
                ->description('Specific setting keys to retrieve (e.g., ["site_name", "seo_llms_enabled"]).'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.read');

        $group = $request->get('group');
        $keys = $request->get('keys');

        if ($keys && is_array($keys)) {
            $settings = [];
            foreach ($keys as $key) {
                if (Setting::isSensitiveKey($key)) {
                    continue; // Strictly filter out sensitive / encrypted keys
                }
                $settings[$key] = setting($key);
            }

            return Response::structured([
                'group' => 'specific_keys',
                'total' => count($settings),
                'settings' => $settings,
            ]);
        }

        $query = Setting::query();

        if ($group) {
            $query->where('group', $group);
        }

        $settings = [];
        foreach ($query->get() as $s) {
            if (Setting::isSensitiveKey($s->key)) {
                continue; // Strictly filter out sensitive / encrypted keys
            }
            $settings[$s->key] = setting($s->key);
        }

        return Response::structured([
            'group' => $group ?? 'all',
            'total' => count($settings),
            'settings' => $settings,
        ]);
    }
}
