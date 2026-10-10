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

#[Description('Get information about the active theme including slug, views directory, asset path, and list of all view files (Blade templates).')]
#[IsReadOnly]
#[IsIdempotent]
class GetActiveThemeTool extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.theme.read');

        $theme = active_theme();
        if (! $theme) {
            return Response::error('No active theme found.');
        }

        $viewsPath = base_path("themes/{$theme->slug}/views");
        $assetsPath = base_path("themes/{$theme->slug}/assets");

        $views = [];
        if (is_dir($viewsPath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($viewsPath, \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $file) {
                if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
                    $relative = str_replace($viewsPath.DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $relative = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
                    $views[] = $relative;
                }
            }
            sort($views);
        }

        return Response::structured([
            'slug' => $theme->slug,
            'views_path' => "themes/{$theme->slug}/views/",
            'assets_path' => "themes/{$theme->slug}/assets/",
            'views' => $views,
            'view_count' => count($views),
        ]);
    }
}
