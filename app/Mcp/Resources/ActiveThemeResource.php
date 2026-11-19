<?php

namespace App\Mcp\Resources;

use App\Mcp\Guards\McpAbilityGuard;
use App\Services\ThemeLoader;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\MimeType;
use Laravel\Mcp\Server\Attributes\Uri;
use Laravel\Mcp\Server\Resource;

#[Uri('cms://theme/active')]
#[MimeType('application/json')]
#[Description('Active theme configuration — name, version, author, templates, template blocks, form placeholders, and slots. READ THIS before any theme operation.')]
class ActiveThemeResource extends Resource
{
    public function handle(Request $request): Response
    {
        McpAbilityGuard::authorize('mcp.theme.read');

        $theme = app(ThemeLoader::class)->getActiveTheme();
        if (! $theme) {
            return Response::text(json_encode(['error' => 'No active theme']));
        }

        $themeJsonPath = base_path("themes/{$theme->slug}/theme.json");

        if (file_exists($themeJsonPath)) {
            $config = json_decode(file_get_contents($themeJsonPath), true) ?? [];
            $config['_slug'] = $theme->slug;
            $config['_views_path'] = "themes/{$theme->slug}/views/";
            $config['_assets_path'] = "themes/{$theme->slug}/assets/";

            return Response::text(json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        return Response::text(json_encode([
            'slug' => $theme->slug,
            'error' => 'theme.json not found',
        ]));
    }
}
