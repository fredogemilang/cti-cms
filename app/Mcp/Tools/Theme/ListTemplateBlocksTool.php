<?php

namespace App\Mcp\Tools\Theme;

use App\Mcp\Guards\McpAbilityGuard;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('List block definitions for a specific page template. Returns the block keys, types, labels, and defaults that the template supports. Use this before creating pages to know what blocks to populate.')]
#[IsReadOnly]
#[IsIdempotent]
class ListTemplateBlocksTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'template' => $schema->string()
                ->description('The page template slug (e.g., "home", "default"). Use get-theme-config to discover available templates.')
                ->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.theme.read');

        $validated = $request->validate(['template' => 'required|string']);
        $templateSlug = $validated['template'];

        $theme = active_theme();
        if (! $theme) {
            return Response::error('No active theme found.');
        }

        $themeJsonPath = base_path("themes/{$theme->slug}/theme.json");
        $config = file_exists($themeJsonPath)
            ? json_decode(file_get_contents($themeJsonPath), true) ?? []
            : [];

        $templates = $config['page_templates'] ?? [];

        if (! isset($templates[$templateSlug])) {
            $available = implode(', ', array_keys($templates));

            return Response::error("Template '{$templateSlug}' not found. Available templates: {$available}");
        }

        $template = $templates[$templateSlug];

        return Response::structured([
            'template' => $templateSlug,
            'label' => $template['label'] ?? $templateSlug,
            'description' => $template['description'] ?? '',
            'blocks' => $template['blocks'] ?? [],
        ]);
    }
}
