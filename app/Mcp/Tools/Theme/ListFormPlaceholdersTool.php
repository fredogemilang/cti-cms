<?php

namespace App\Mcp\Tools\Theme;

use App\Mcp\Guards\McpAbilityGuard;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('List form placeholder slots defined in the active theme. Form placeholders are named slots in theme.json where forms can be assigned by admin. Returns placeholder keys, labels, and descriptions.')]
#[IsReadOnly]
#[IsIdempotent]
class ListFormPlaceholdersTool extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.theme.read');

        $theme = active_theme();
        if (! $theme) {
            return Response::error('No active theme found.');
        }

        $themeJsonPath = base_path("themes/{$theme->slug}/theme.json");
        $config = file_exists($themeJsonPath) ? json_decode(file_get_contents($themeJsonPath), true) ?? [] : [];

        $placeholders = $config['form_placeholders'] ?? [];

        // Get current assignments
        $assignments = setting("theme_{$theme->slug}_form_assignments", []);

        return Response::structured([
            'theme' => $theme->slug,
            'placeholders' => collect($placeholders)->map(fn ($p) => [
                'key' => $p['key'],
                'label' => $p['label'],
                'description' => $p['description'] ?? '',
                'assigned_form_id' => $assignments[$p['key']] ?? null,
            ])->toArray(),
        ]);
    }
}
