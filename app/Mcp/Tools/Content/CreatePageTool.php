<?php

namespace App\Mcp\Tools\Content;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\Activity;
use App\Models\Page;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Create a new CMS page. Creates as draft by default. Provide blocks as key-value pairs matching the page template definition. Use list-template-blocks first to discover available block keys.')]
class CreatePageTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'title' => $schema->string()
                ->description('Page title (English / default locale).')
                ->required(),

            'slug' => $schema->string()
                ->description('URL slug. Auto-generated from title if omitted.'),

            'template' => $schema->string()
                ->description('Page template slug (e.g., "home", "default"). Use get-theme-config to see available templates.')
                ->default('default'),

            'status' => $schema->string()
                ->enum(['draft', 'published', 'scheduled', 'private'])
                ->description('Page status. Defaults to "draft". Publishing requires mcp.content.publish ability.')
                ->default('draft'),

            'parent_id' => $schema->integer()
                ->description('Parent page ID for hierarchical pages.'),

            'menu_order' => $schema->integer()
                ->description('Sort order within siblings. Default: 0.')
                ->default(0),

            'seo' => $schema->object()
                ->description('SEO meta data: { meta_title, meta_description, og_image, canonical_url }'),

            'blocks' => $schema->object()
                ->description('Block values as key-value pairs. Keys must match template block definitions (use list-template-blocks to discover). Values can be strings, arrays, or objects depending on block type.'),

            'translations' => $schema->object()
                ->description('Translations for other locales. Format: { "id": { "title": "...", "slug": "..." } }'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.write');

        $status = $request->get('status') ?? 'draft';

        if ($status === 'published') {
            McpAbilityGuard::authorize('mcp.content.publish');
        }

        $token = McpAbilityGuard::resolveToken();

        $page = Page::create([
            'title' => $request->get('title'),
            'slug' => $request->get('slug') ?: null,
            'template' => $request->get('template') ?? 'default',
            'status' => $status,
            'parent_id' => $request->get('parent_id'),
            'menu_order' => $request->get('menu_order') ?? 0,
            'seo' => $request->get('seo') ?? [],
            'translations' => $request->get('translations') ?? [],
            'author_id' => $token?->tokenable_id,
            'published_at' => $status === 'published' ? now() : null,
        ]);

        // Create blocks
        $blocks = $request->get('blocks') ?? [];
        $order = 1;
        foreach ($blocks as $name => $value) {
            $type = $this->inferBlockType($value);
            $page->blocks()->create([
                'name' => $name,
                'type' => $type,
                'label' => str($name)->headline()->toString(),
                'value' => is_array($value) ? json_encode($value) : $value,
                'order' => $order++,
                'is_active' => true,
            ]);
        }

        // Audit log
        Activity::create([
            'user_id' => $token?->tokenable_id,
            'action' => 'created',
            'subject_type' => Page::class,
            'subject_id' => $page->id,
            'description' => "Page \"{$page->title}\" created via MCP",
            'properties' => ['source' => 'mcp', 'token_id' => $token?->id],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return Response::structured([
            'success' => true,
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'status' => $page->status,
                'template' => $page->template,
                'url' => $page->getUrl(),
                'blocks_created' => count($blocks),
            ],
        ]);
    }

    private function inferBlockType(mixed $value): string
    {
        if (is_array($value)) {
            if (isset($value['text'], $value['url'])) {
                return 'button';
            }
            if (isset($value['prefix']) || isset($value['main'])) {
                return 'title';
            }

            return 'repeater';
        }
        if (is_bool($value)) {
            return 'switcher';
        }
        if (is_numeric($value)) {
            return 'number';
        }
        if (is_string($value) && (str_contains($value, '<') || strlen($value) > 200)) {
            return 'wysiwyg';
        }

        return 'text';
    }
}
