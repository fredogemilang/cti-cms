<?php

namespace App\Mcp\Tools\Forms;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\Form;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('List all forms in the CMS Form Builder. Returns form names, slugs, and field counts.')]
#[IsReadOnly]
#[IsIdempotent]
class ListFormsTool extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.read');

        $forms = Form::withCount('fields')->orderBy('name')->get();

        return Response::structured([
            'total' => $forms->count(),
            'forms' => $forms->map(fn ($f) => [
                'id' => $f->id,
                'name' => $f->name,
                'slug' => $f->slug,
                'description' => $f->description,
                'fields_count' => $f->fields_count,
                'email_notification' => $f->email_notification ?? false,
            ])->toArray(),
        ]);
    }
}
