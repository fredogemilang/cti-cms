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

#[Description('Get a single CPT entry by slug or ID, including its meta fields, translations, taxonomy terms, and SEO metadata. Use list-cpt-entries first to discover entry slugs.')]
#[IsReadOnly]
#[IsIdempotent]
class GetCptEntryTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'cpt_slug' => $schema->string()
                ->description('The CPT type slug (e.g., "technology-alliance"). Required for slug-based lookup.')
                ->required(),

            'entry_slug' => $schema->string()
                ->description('The entry slug. Provide either entry_slug or entry_id.'),

            'entry_id' => $schema->integer()
                ->description('The entry ID. Provide either entry_slug or entry_id.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.read');

        $cptSlug = $request->get('cpt_slug');
        $entrySlug = $request->get('entry_slug');
        $entryId = $request->get('entry_id');

        if (! $entrySlug && ! $entryId) {
            return Response::error('You must provide either "entry_slug" or "entry_id".');
        }

        $cpt = CustomPostType::where('slug', $cptSlug)->first();
        if (! $cpt) {
            return Response::error("CPT '{$cptSlug}' not found. Use the list-cpts tool to discover available CPT slugs.");
        }

        $query = CptEntry::where('post_type_id', $cpt->id)->with(['terms', 'seoMeta']);

        if ($entrySlug) {
            $query->where('slug', $entrySlug);
        } else {
            $query->where('id', $entryId);
        }

        /** @var CptEntry|null $entry */
        $entry = $query->first();

        if (! $entry) {
            return Response::error("Entry not found in CPT '{$cptSlug}'. Use list-cpt-entries tool to see available entries.");
        }

        $data = [
            'id' => $entry->id,
            'title' => $entry->title,
            'slug' => $entry->slug,
            'url' => url('/'.$entry->slug),
            'status' => $entry->status,
            'menu_order' => $entry->menu_order,
            'meta' => $entry->meta ?? [],
            'translations' => $entry->translations ?? [],
            'created_at' => $entry->created_at?->toIso8601String(),
            'updated_at' => $entry->updated_at?->toIso8601String(),
        ];

        // Taxonomy terms
        if ($entry->relationLoaded('terms')) {
            $data['taxonomy_terms'] = $entry->terms->map(fn ($term) => [
                'id' => $term->id,
                'name' => $term->name,
                'slug' => $term->slug,
                'taxonomy_id' => $term->taxonomy_id,
            ])->toArray();
        }

        // SEO meta
        if ($entry->relationLoaded('seoMeta') && $entry->seoMeta) {
            $seo = $entry->seoMeta;
            $data['seo'] = [
                'meta_title' => $seo->meta_title,
                'meta_description' => $seo->meta_description,
                'og_title' => $seo->og_title,
                'og_description' => $seo->og_description,
                'og_image' => $seo->og_image,
                'canonical_url' => $seo->canonical_url,
                'no_index' => $seo->no_index ?? false,
            ];
        }

        // Include CPT schema context for AI understanding
        $data['_cpt_context'] = [
            'cpt_name' => $cpt->name,
            'cpt_slug' => $cpt->slug,
            'has_archive' => $cpt->has_archive ?? false,
        ];

        return Response::structured($data);
    }
}
