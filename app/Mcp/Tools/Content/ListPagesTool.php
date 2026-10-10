<?php

namespace App\Mcp\Tools\Content;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\Page;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('List all pages in the CMS. Returns titles, slugs, templates, statuses, and menu order. Supports filtering by status and template.')]
#[IsReadOnly]
#[IsIdempotent]
class ListPagesTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'status' => $schema->string()
                ->enum(['published', 'draft', 'trashed', 'scheduled'])
                ->description('Filter by page status. Defaults to all statuses.'),

            'template' => $schema->string()
                ->description('Filter by page template slug (e.g., "home", "default").'),

            'limit' => $schema->integer()
                ->description('Maximum number of pages to return. Default: 50, Max: 200.')
                ->default(50),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.read');

        $query = Page::query()->orderBy('menu_order');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($template = $request->get('template')) {
            $query->where('template', $template);
        }

        $limit = min((int) ($request->get('limit') ?? 50), 200);

        $pages = $query->take($limit)->get(['id', 'title', 'slug', 'template', 'status', 'menu_order', 'parent_id', 'created_at', 'updated_at']);

        $result = $pages->map(fn (Page $page) => [
            'id' => $page->id,
            'title' => $page->title,
            'slug' => $page->slug,
            'url' => url('/'.$page->slug),
            'template' => $page->template,
            'status' => $page->status,
            'menu_order' => $page->menu_order,
            'parent_id' => $page->parent_id,
            'updated_at' => $page->updated_at?->toIso8601String(),
        ]);

        return Response::structured([
            'total' => $result->count(),
            'pages' => $result->toArray(),
        ]);
    }
}
