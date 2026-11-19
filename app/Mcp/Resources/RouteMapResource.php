<?php

namespace App\Mcp\Resources;

use App\Mcp\Guards\McpAbilityGuard;
use Illuminate\Support\Facades\Route;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\MimeType;
use Laravel\Mcp\Server\Attributes\Uri;
use Laravel\Mcp\Server\Resource;

#[Uri('cms://routes/map')]
#[MimeType('text/markdown')]
#[Description('All registered web and API routes with their methods, URIs, controller actions, and middleware. Use this to understand the URL structure and available endpoints.')]
class RouteMapResource extends Resource
{
    public function handle(Request $request): Response
    {
        McpAbilityGuard::authorize('mcp.read');

        $routes = collect(Route::getRoutes())->map(fn ($route) => [
            'method' => implode('|', $route->methods()),
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'action' => $route->getActionName(),
            'middleware' => implode(', ', $route->gatherMiddleware()),
        ]);

        $output = "# CTI CMS — Route Map\n\n";

        // Group by prefix
        $groups = $routes->groupBy(fn ($r) => explode('/', $r['uri'])[0] ?? 'root');

        foreach ($groups as $prefix => $groupRoutes) {
            $output .= "## /{$prefix}\n\n";
            $output .= "| Method | URI | Name | Action |\n";
            $output .= "|--------|-----|------|--------|\n";

            foreach ($groupRoutes->take(50) as $route) {
                $action = str_replace('App\\Http\\Controllers\\', '', $route['action']);
                $output .= "| {$route['method']} | `{$route['uri']}` | {$route['name']} | {$action} |\n";
            }

            if ($groupRoutes->count() > 50) {
                $output .= '| ... | *('.($groupRoutes->count() - 50)." more routes)* | | |\n";
            }

            $output .= "\n";
        }

        return Response::text($output);
    }
}
