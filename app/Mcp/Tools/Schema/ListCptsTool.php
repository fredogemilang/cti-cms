<?php

namespace App\Mcp\Tools\Schema;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\CustomPostType;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('List all Custom Post Types (CPTs) defined in the CMS. Returns name, slug, status, field count, and archive settings. Use this to discover what content types are available before querying entries.')]
#[IsReadOnly]
#[IsIdempotent]
class ListCptsTool extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.read');

        $cpts = CustomPostType::withCount('metaFields')
            ->orderBy('name')
            ->get();

        $result = $cpts->map(fn ($cpt) => [
            'id' => $cpt->id,
            'name' => $cpt->name,
            'slug' => $cpt->slug,
            'description' => $cpt->description,
            'is_active' => $cpt->is_active,
            'has_archive' => $cpt->has_archive ?? false,
            'icon' => $cpt->icon,
            'meta_fields_count' => $cpt->meta_fields_count,
            'archive_url' => ($cpt->has_archive ?? false) ? url('/'.$cpt->slug) : null,
        ]);

        return Response::structured([
            'total' => $result->count(),
            'cpts' => $result->toArray(),
        ]);
    }
}
