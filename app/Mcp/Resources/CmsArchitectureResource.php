<?php

namespace App\Mcp\Resources;

use App\Mcp\Guards\McpAbilityGuard;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\MimeType;
use Laravel\Mcp\Server\Attributes\Uri;
use Laravel\Mcp\Server\Resource;

#[Uri('cms://architecture')]
#[MimeType('text/markdown')]
#[Description('Complete CMS architecture overview — models, relationships, directory structure, translation system, asset rules, and routing. READ THIS FIRST before any CMS operation.')]
class CmsArchitectureResource extends Resource
{
    public function handle(Request $request): Response
    {
        McpAbilityGuard::authorize('mcp.connect');

        $path = base_path('docs/ai-skills/cms-architecture.md');

        if (file_exists($path)) {
            return Response::text(file_get_contents($path));
        }

        return Response::text('# CMS Architecture document not found. Contact admin.');
    }
}
