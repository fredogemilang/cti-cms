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

#[Description('Get the Blade view template resolution hierarchy for a given slug. Shows which template file is used and the fallback chain. Useful for understanding how content is rendered.')]
#[IsReadOnly]
#[IsIdempotent]
class GetViewHierarchyTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => $schema->string()
                ->enum(['page', 'cpt-single', 'cpt-archive'])
                ->description('The type of view to resolve.')
                ->required(),

            'slug' => $schema->string()
                ->description('The page template slug (for page type) or CPT slug (for cpt-single/cpt-archive).')
                ->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.theme.read');

        $validated = $request->validate([
            'type' => 'required|in:page,cpt-single,cpt-archive',
            'slug' => 'required|string',
        ]);

        $theme = active_theme();
        if (! $theme) {
            return Response::error('No active theme found.');
        }

        $themeSlug = $theme->slug;
        $slug = $validated['slug'];
        $hierarchy = [];

        switch ($validated['type']) {
            case 'page':
                $hierarchy = [
                    "themes/{$themeSlug}/views/pages/{$slug}.blade.php",
                    "themes/{$themeSlug}/views/pages/default.blade.php",
                    "themes/{$themeSlug}/views/page.blade.php",
                ];
                break;

            case 'cpt-single':
                $hierarchy = [
                    "themes/{$themeSlug}/views/single-{$slug}.blade.php",
                    "themes/{$themeSlug}/views/single.blade.php",
                ];
                break;

            case 'cpt-archive':
                $hierarchy = [
                    "themes/{$themeSlug}/views/archive-{$slug}.blade.php",
                    "themes/{$themeSlug}/views/archive.blade.php",
                ];
                break;
        }

        // Check which files actually exist
        $resolved = collect($hierarchy)->map(fn ($path) => [
            'path' => $path,
            'exists' => file_exists(base_path($path)),
        ])->toArray();

        $active = collect($resolved)->first(fn ($v) => $v['exists']);

        return Response::structured([
            'type' => $validated['type'],
            'slug' => $slug,
            'active_template' => $active['path'] ?? 'none found',
            'hierarchy' => $resolved,
        ]);
    }
}
