<?php

namespace App\Mcp\Tools\Schema;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\CustomPostType;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('List all meta field definitions for a specific CPT. Returns field keys, types, labels, and validation rules. Use this before creating/updating CPT entries to know what fields are expected.')]
#[IsReadOnly]
#[IsIdempotent]
class ListMetaFieldsTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'cpt_slug' => $schema->string()
                ->description('The CPT slug to list meta fields for.')
                ->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.read');

        $validated = $request->validate(['cpt_slug' => 'required|string']);

        $cpt = CustomPostType::where('slug', $validated['cpt_slug'])->first();
        if (! $cpt) {
            return Response::error("CPT '{$validated['cpt_slug']}' not found.");
        }

        $fields = $cpt->metaFields()->get();

        return Response::structured([
            'cpt' => ['name' => $cpt->name, 'slug' => $cpt->slug],
            'total' => $fields->count(),
            'fields' => $fields->map(fn ($f) => [
                'id' => $f->id,
                'key' => $f->key,
                'label' => $f->label,
                'type' => $f->type,
                'is_required' => $f->is_required ?? false,
                'is_translatable' => $f->is_translatable ?? false,
                'options' => $f->options,
                'sort_order' => $f->order,
            ])->toArray(),
        ]);
    }
}
