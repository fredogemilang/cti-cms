<?php

namespace App\Mcp\Tools\Schema;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\CustomTaxonomy;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('List all taxonomies with their terms. Taxonomies are category/tag systems attached to CPTs. Returns taxonomy names, slugs, and their term hierarchies.')]
#[IsReadOnly]
#[IsIdempotent]
class ListTaxonomiesTool extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.read');

        $taxonomies = CustomTaxonomy::with(['terms' => fn ($q) => $q->orderBy('order')])
            ->active()
            ->get();

        return Response::structured([
            'total' => $taxonomies->count(),
            'taxonomies' => $taxonomies->map(fn ($tax) => [
                'id' => $tax->id,
                'name' => $tax->name,
                'slug' => $tax->slug,
                'is_hierarchical' => $tax->is_hierarchical ?? false,
                'post_types' => $tax->post_types ?? [],
                'terms' => $tax->terms->map(fn ($term) => [
                    'id' => $term->id,
                    'name' => $term->name,
                    'slug' => $term->slug,
                    'parent_id' => $term->parent_id,
                ])->toArray(),
            ])->toArray(),
        ]);
    }
}
