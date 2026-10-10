<?php

namespace App\Mcp\Tools\Schema;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\Activity;
use App\Models\CustomPostType;
use App\Models\MetaField;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Define and register a new Custom Post Type (CPT) with optional meta fields. Requires mcp.admin ability.')]
class CreateCptTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()
                ->description('Display name of the CPT (e.g., "Services", "Portfolio").')
                ->required(),

            'slug' => $schema->string()
                ->description('Unique slug identifier (e.g., "services", "case_studies"). Auto-generated if omitted.'),

            'singular_label' => $schema->string()
                ->description('Singular label (e.g., "Service"). Defaults to name if omitted.'),

            'plural_label' => $schema->string()
                ->description('Plural label (e.g., "Services"). Defaults to name if omitted.'),

            'icon' => $schema->string()
                ->description('Material Symbols icon name (e.g., "layers", "business", "star"). Default: "layers".')
                ->default('layers'),

            'description' => $schema->string()
                ->description('Short description of the post type.'),

            'is_hierarchical' => $schema->boolean()
                ->description('Whether entries can have parent/child relationships (like Pages). Default: false.')
                ->default(false),

            'publicly_queryable' => $schema->boolean()
                ->description('Whether entries have public frontend single/archive URLs. Default: true.')
                ->default(true),

            'show_in_menu' => $schema->boolean()
                ->description('Whether this CPT shows up in the admin navigation menu. Default: true.')
                ->default(true),

            'meta_fields' => $schema->array()
                ->description('Optional array of meta field definitions: [{ name, label, type, is_required, options, description }]'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.admin');

        $name = $request->get('name');
        $slug = $request->get('slug') ? Str::slug($request->get('slug'), '_') : Str::slug($name, '_');

        if (CustomPostType::where('slug', $slug)->exists()) {
            return Response::error("CPT with slug '{$slug}' already exists.");
        }

        $cpt = CustomPostType::create([
            'name' => $name,
            'slug' => $slug,
            'singular_label' => $request->get('singular_label') ?: $name,
            'plural_label' => $request->get('plural_label') ?: $name,
            'icon' => $request->get('icon') ?: 'layers',
            'description' => $request->get('description') ?: '',
            'is_hierarchical' => (bool) ($request->get('is_hierarchical') ?? false),
            'publicly_queryable' => (bool) ($request->get('publicly_queryable') ?? true),
            'show_in_menu' => (bool) ($request->get('show_in_menu') ?? true),
            'show_in_rest' => true,
            'has_archive' => true,
            'is_active' => true,
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'author'],
        ]);

        $metaFieldsCreated = 0;
        if ($fields = $request->get('meta_fields')) {
            $order = 1;
            foreach ($fields as $field) {
                if (empty($field['name']) || empty($field['type'])) {
                    continue;
                }

                $options = $field['options'] ?? [];
                // Respect CMS convention: repeater fields must use 'repeater_fields' key
                if ($field['type'] === 'repeater' && isset($options['sub_fields']) && ! isset($options['repeater_fields'])) {
                    $options['repeater_fields'] = $options['sub_fields'];
                    unset($options['sub_fields']);
                }

                MetaField::create([
                    'fieldable_type' => CustomPostType::class,
                    'fieldable_id' => $cpt->id,
                    'name' => Str::slug($field['name'], '_'),
                    'label' => $field['label'] ?? Str::headline($field['name']),
                    'type' => $field['type'],
                    'is_required' => (bool) ($field['is_required'] ?? false),
                    'description' => $field['description'] ?? '',
                    'options' => $options,
                    'order' => $order++,
                    'is_active' => true,
                ]);
                $metaFieldsCreated++;
            }
        }

        $token = McpAbilityGuard::resolveToken();
        Activity::create([
            'user_id' => $token?->tokenable_id,
            'action' => 'created',
            'subject_type' => CustomPostType::class,
            'subject_id' => $cpt->id,
            'description' => "CPT '{$cpt->name}' ({$cpt->slug}) defined via MCP with {$metaFieldsCreated} meta fields",
            'properties' => ['source' => 'mcp', 'token_id' => $token?->id],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return Response::structured([
            'success' => true,
            'cpt' => [
                'id' => $cpt->id,
                'name' => $cpt->name,
                'slug' => $cpt->slug,
                'singular_label' => $cpt->singular_label,
                'plural_label' => $cpt->plural_label,
                'is_hierarchical' => $cpt->is_hierarchical,
                'meta_fields_count' => $metaFieldsCreated,
            ],
        ]);
    }
}
