<?php

namespace App\Mcp\Tools\Content;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\CptEntry;
use App\Models\CustomPostType;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('List entries for a specific Custom Post Type (CPT). Requires the CPT slug (e.g., "technology-alliance", "industry"). Use the list-cpts tool first to discover available CPT slugs.')]
#[IsReadOnly]
#[IsIdempotent]
class ListCptEntriesTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'cpt_slug' => $schema->string()
                ->description('The CPT slug (e.g., "technology-alliance"). Use list-cpts tool to discover available slugs.')
                ->required(),

            'status' => $schema->string()
                ->enum(['published', 'draft', 'trashed', 'scheduled'])
                ->description('Filter by entry status. Defaults to all statuses.'),

            'limit' => $schema->integer()
                ->description('Maximum number of entries to return. Default: 50, Max: 200.')
                ->default(50),

            'search' => $schema->string()
                ->description('Search entries by title (partial match).'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.read');

        $validated = $request->validate([
            'cpt_slug' => 'required|string',
        ]);

        $cpt = CustomPostType::where('slug', $validated['cpt_slug'])->first();
        if (! $cpt) {
            return Response::error("CPT '{$validated['cpt_slug']}' not found. Use the list-cpts tool to discover available CPT slugs.");
        }

        $query = CptEntry::where('post_type_id', $cpt->id)->orderBy('menu_order')->orderBy('created_at', 'desc');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->get('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $limit = min((int) ($request->get('limit') ?? 50), 200);

        $entries = $query->take($limit)->get(['id', 'title', 'slug', 'status', 'menu_order', 'created_at', 'updated_at']);

        $result = $entries->map(fn (CptEntry $entry) => [
            'id' => $entry->id,
            'title' => $entry->title,
            'slug' => $entry->slug,
            'url' => url('/'.$entry->slug),
            'status' => $entry->status,
            'menu_order' => $entry->menu_order,
            'updated_at' => $entry->updated_at?->toIso8601String(),
        ]);

        return Response::structured([
            'cpt' => ['id' => $cpt->id, 'name' => $cpt->name, 'slug' => $cpt->slug],
            'total' => $result->count(),
            'entries' => $result->toArray(),
        ]);
    }
}
