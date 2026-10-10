<?php

namespace App\Mcp\Tools\Schema;

use App\Mcp\Guards\McpAbilityGuard;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Get the active theme configuration from theme.json, including page templates, form placeholders, block schemas, and archive settings.')]
#[IsReadOnly]
#[IsIdempotent]
class GetThemeConfigTool extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.read');

        $theme = active_theme();
        if (! $theme) {
            return Response::error('No active theme found.');
        }

        $themeJsonPath = resource_path("../themes/{$theme->slug}/theme.json");
        if (! file_exists($themeJsonPath)) {
            $themeJsonPath = base_path("themes/{$theme->slug}/theme.json");
        }

        $config = file_exists($themeJsonPath)
            ? json_decode(file_get_contents($themeJsonPath), true) ?? []
            : [];

        return Response::structured([
            'theme' => [
                'name' => $config['name'] ?? $theme->name ?? $theme->slug,
                'slug' => $theme->slug,
                'version' => $config['version'] ?? '1.0.0',
                'description' => $config['description'] ?? '',
                'author' => $config['author'] ?? '',
            ],
            'supports' => $config['supports'] ?? [],
            'page_templates' => $config['page_templates'] ?? [],
            'form_placeholders' => $config['form_placeholders'] ?? [],
            'archive_settings' => $config['archive_settings'] ?? [],
        ]);
    }
}
