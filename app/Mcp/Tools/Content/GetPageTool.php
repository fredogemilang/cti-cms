<?php

namespace App\Mcp\Tools\Content;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\Page;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Get a single page by slug or ID, including its blocks, translations, and SEO metadata. This is the primary tool for reading page content.')]
#[IsReadOnly]
#[IsIdempotent]
class GetPageTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'slug' => $schema->string()
                ->description('The page slug (e.g., "about-us", "home"). Provide either slug or id.'),

            'id' => $schema->integer()
                ->description('The page ID. Provide either slug or id.'),

            'include_blocks' => $schema->boolean()
                ->description('Whether to include page blocks. Default: true.')
                ->default(true),

            'include_seo' => $schema->boolean()
                ->description('Whether to include SEO metadata. Default: true.')
                ->default(true),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.read');

        $slug = $request->get('slug');
        $id = $request->get('id');

        if (! $slug && ! $id) {
            return Response::error('You must provide either a "slug" or "id" parameter to identify the page.');
        }

        $query = Page::query();

        if ($slug) {
            $query->where('slug', $slug);
        } else {
            $query->where('id', $id);
        }

        $includeBlocks = $request->get('include_blocks', true);
        $includeSeo = $request->get('include_seo', true);

        $relations = [];
        if ($includeBlocks) {
            $relations[] = 'blocks';
        }
        if ($includeSeo) {
            $relations[] = 'seoMeta';
        }

        if (! empty($relations)) {
            $query->with($relations);
        }

        /** @var Page|null $page */
        $page = $query->first();

        if (! $page) {
            return Response::error("Page not found. Slug: '{$slug}', ID: '{$id}'. Use the list-pages tool to see available pages.");
        }

        $data = [
            'id' => $page->id,
            'title' => $page->title,
            'slug' => $page->slug,
            'url' => url('/'.$page->slug),
            'template' => $page->template,
            'status' => $page->status,
            'menu_order' => $page->menu_order,
            'parent_id' => $page->parent_id,
            'translations' => $page->translations ?? [],
            'created_at' => $page->created_at?->toIso8601String(),
            'updated_at' => $page->updated_at?->toIso8601String(),
        ];

        if ($includeBlocks && $page->relationLoaded('blocks')) {
            $data['blocks'] = $page->blocks->map(fn ($block) => [
                'id' => $block->id,
                'key' => $block->key,
                'type' => $block->type,
                'value' => $block->value,
                'sort_order' => $block->sort_order,
                'translations' => $block->translations ?? [],
            ])->toArray();
        }

        if ($includeSeo && $page->relationLoaded('seoMeta') && $page->seoMeta) {
            $seo = $page->seoMeta;
            $data['seo'] = [
                'meta_title' => $seo->meta_title,
                'meta_description' => $seo->meta_description,
                'og_title' => $seo->og_title,
                'og_description' => $seo->og_description,
                'og_image' => $seo->og_image,
                'canonical_url' => $seo->canonical_url,
                'no_index' => $seo->no_index ?? false,
                'no_follow' => $seo->no_follow ?? false,
            ];
        }

        return Response::structured($data);
    }
}
