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

#[Description('Get the full schema definition for a specific CPT, including all meta fields with their types, validation rules, and options. Essential for understanding what data a CPT entry expects.')]
#[IsReadOnly]
#[IsIdempotent]
class GetCptSchemaTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'slug' => $schema->string()
                ->description('The CPT slug (e.g., "technology-alliance").')
                ->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.read');

        $validated = $request->validate(['slug' => 'required|string']);

        $cpt = CustomPostType::where('slug', $validated['slug'])
            ->with(['metaFields' => fn ($q) => $q->orderBy('order')])
            ->first();

        if (! $cpt) {
            return Response::error("CPT '{$validated['slug']}' not found. Use list-cpts to discover available CPT slugs.");
        }

        $data = [
            'id' => $cpt->id,
            'name' => $cpt->name,
            'slug' => $cpt->slug,
            'description' => $cpt->description,
            'is_active' => $cpt->is_active,
            'has_archive' => $cpt->has_archive ?? false,
            'icon' => $cpt->icon,
            'meta_fields' => $cpt->metaFields->map(fn ($field) => [
                'id' => $field->id,
                'key' => $field->key,
                'label' => $field->label,
                'type' => $field->type,
                'is_required' => $field->is_required ?? false,
                'is_translatable' => $field->is_translatable ?? false,
                'options' => $field->options,
                'sort_order' => $field->order,
            ])->toArray(),
            'taxonomies' => $cpt->taxonomies()->map(fn ($tax) => [
                'id' => $tax->id,
                'name' => $tax->name,
                'slug' => $tax->slug,
                'is_hierarchical' => $tax->is_hierarchical ?? false,
                'terms' => $tax->terms->map(fn ($term) => [
                    'id' => $term->id,
                    'name' => $term->name,
                    'slug' => $term->slug,
                    'parent_id' => $term->parent_id,
                ])->toArray(),
            ])->toArray(),
            'template_resolution' => [
                'single' => "themes/{theme}/views/single-{$cpt->slug}.blade.php",
                'archive' => "themes/{theme}/views/archive-{$cpt->slug}.blade.php",
                'fallback_single' => 'themes/{theme}/views/single.blade.php',
                'fallback_archive' => 'themes/{theme}/views/archive.blade.php',
            ],
        ];

        return Response::structured($data);
    }
}
