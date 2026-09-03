<?php

namespace App\Mcp\Tools\Content;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\Activity;
use App\Models\CptEntry;
use App\Models\CustomPostType;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Create a new CPT entry. Creates as draft by default. Use get-cpt-schema first to understand what meta fields are available for the CPT. All meta field values go in the "meta" parameter.')]
class CreateCptEntryTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'cpt_slug' => $schema->string()
                ->description('The Custom Post Type slug (e.g., "technology-alliance"). Use list-cpts to discover available CPTs.')
                ->required(),

            'title' => $schema->string()
                ->description('Entry title (English / default locale).')
                ->required(),

            'slug' => $schema->string()
                ->description('URL slug. Auto-generated from title if omitted.'),

            'content' => $schema->string()
                ->description('Main content body (HTML allowed).'),

            'excerpt' => $schema->string()
                ->description('Short excerpt / summary.'),

            'featured_image' => $schema->string()
                ->description('Featured image path (relative, from media library). Use list-media to find available images.'),

            'status' => $schema->string()
                ->enum(['draft', 'published', 'scheduled', 'archived'])
                ->description('Entry status. Default: "draft". Publishing requires mcp.content.publish.')
                ->default('draft'),

            'meta' => $schema->object()
                ->description('Meta field values as key-value pairs. Keys must match CPT meta field names. Use get-cpt-schema to discover available fields.'),

            'translations' => $schema->object()
                ->description('Translations for other locales. Format: { "id": { "title": "...", "slug": "...", "content": "...", "excerpt": "..." } }'),

            'taxonomy_terms' => $schema->array()
                ->description('Array of taxonomy term IDs to attach.'),

            'menu_order' => $schema->integer()
                ->description('Sort order. Default: 0.')
                ->default(0),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.write');

        $cpt = CustomPostType::where('slug', $request->get('cpt_slug'))->where('is_active', true)->first();
        if (! $cpt) {
            return Response::error("CPT '{$request->get('cpt_slug')}' not found. Use list-cpts to discover available CPT slugs.");
        }

        $status = $request->get('status') ?? 'draft';
        if ($status === 'published') {
            McpAbilityGuard::authorize('mcp.content.publish');
        }

        $token = McpAbilityGuard::resolveToken();

        // Build meta with translations support
        $meta = $request->get('meta') ?? [];

        $entry = CptEntry::create([
            'post_type_id' => $cpt->id,
            'title' => $request->get('title'),
            'slug' => $request->get('slug') ?: null,
            'content' => $request->get('content') ?? '',
            'excerpt' => $request->get('excerpt') ?? '',
            'featured_image' => $request->get('featured_image'),
            'status' => $status,
            'meta' => $meta,
            'menu_order' => $request->get('menu_order') ?? 0,
            'translations' => $request->get('translations') ?? [],
            'author_id' => $token?->tokenable_id,
            'published_at' => $status === 'published' ? now() : null,
        ]);

        // Attach taxonomy terms
        if ($termIds = $request->get('taxonomy_terms')) {
            $entry->terms()->sync($termIds);
        }

        // Audit log
        Activity::create([
            'user_id' => $token?->tokenable_id,
            'action' => 'created',
            'subject_type' => CptEntry::class,
            'subject_id' => $entry->id,
            'description' => "CPT entry \"{$entry->title}\" ({$cpt->name}) created via MCP",
            'properties' => ['source' => 'mcp', 'token_id' => $token?->id, 'cpt' => $cpt->slug],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return Response::structured([
            'success' => true,
            'entry' => [
                'id' => $entry->id,
                'title' => $entry->title,
                'slug' => $entry->slug,
                'cpt' => $cpt->slug,
                'status' => $entry->status,
                'url' => $entry->getUrl(),
                'meta_keys' => array_keys($meta),
            ],
        ]);
    }
}
