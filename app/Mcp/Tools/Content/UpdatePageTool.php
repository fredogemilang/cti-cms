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

#[Description('Update an existing CMS page. Can update title, slug, status, SEO, translations, and blocks. Use get-page first to see current values. System pages cannot have their slug or template changed.')]
class UpdatePageTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->description('The page ID to update. Use list-pages to find IDs.')
                ->required(),

            'title' => $schema->string()
                ->description('New page title.'),

            'slug' => $schema->string()
                ->description('New URL slug. System pages cannot have slug changed.'),

            'template' => $schema->string()
                ->description('New page template. System pages cannot have template changed.'),

            'status' => $schema->string()
                ->enum(['draft', 'published', 'scheduled', 'private'])
                ->description('New status. Publishing requires mcp.content.publish ability.'),

            'menu_order' => $schema->integer()
                ->description('New sort order.'),

            'seo' => $schema->object()
                ->description('SEO meta data update. Merges with existing.'),

            'blocks' => $schema->object()
                ->description('Block values to update. Keys matching existing blocks will update them; new keys create new blocks.'),

            'translations' => $schema->object()
                ->description('Updated translations. Merges with existing. Format: { "id": { "title": "...", "slug": "..." } }'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.write');

        $page = Page::find($request->get('id'));
        if (! $page) {
            return Response::error("Page with ID {$request->get('id')} not found.");
        }

        $token = McpAbilityGuard::resolveToken();

        // Check if locked by someone else
        if ($page->isLockedByOther($token?->tokenable_id)) {
            return Response::error('Page is currently locked by another editor. Try again later.');
        }

        $changes = [];

        // Simple field updates
        foreach (['title', 'slug', 'template', 'menu_order'] as $field) {
            if ($request->get($field) !== null) {
                $changes[$field] = $request->get($field);
            }
        }

        // Status change
        if ($status = $request->get('status')) {
            if ($status === 'published') {
                McpAbilityGuard::authorize('mcp.content.publish');
                $changes['published_at'] = $page->published_at ?? now();
            }
            $changes['status'] = $status;
        }

        // SEO merge
        if ($seo = $request->get('seo')) {
            $changes['seo'] = array_merge($page->seo ?? [], $seo);
        }

        // Translations merge
        if ($translations = $request->get('translations')) {
            $existing = $page->translations ?? [];
            foreach ($translations as $locale => $fields) {
                $existing[$locale] = array_merge($existing[$locale] ?? [], $fields);
            }
            $changes['translations'] = $existing;
        }

        if (! empty($changes)) {
            $page->update($changes);
        }

        // Block updates (optimized: batch load existing blocks and compute order in-memory)
        $blocksUpdated = 0;
        $blocksCreated = 0;
        if ($blocks = $request->get('blocks')) {
            $existingBlocks = $page->allBlocks()->get()->keyBy('name');
            $currentMaxOrder = $existingBlocks->max('order') ?? 0;

            foreach ($blocks as $name => $value) {
                $serializedValue = is_array($value) ? json_encode($value) : $value;
                $existing = $existingBlocks->get($name);

                if ($existing) {
                    $existing->value = $serializedValue;
                    $existing->save();
                    $blocksUpdated++;
                } else {
                    $currentMaxOrder++;
                    $type = is_array($value) ? 'repeater' : 'text';
                    $newBlock = $page->blocks()->create([
                        'name' => $name,
                        'type' => $type,
                        'label' => str($name)->headline()->toString(),
                        'value' => $serializedValue,
                        'order' => $currentMaxOrder,
                        'is_active' => true,
                    ]);
                    $existingBlocks->put($name, $newBlock);
                    $blocksCreated++;
                }
            }
        }

        // Audit log
        Activity::create([
            'user_id' => $token?->tokenable_id,
            'action' => 'updated',
            'subject_type' => Page::class,
            'subject_id' => $page->id,
            'description' => "Page \"{$page->title}\" updated via MCP",
            'properties' => [
                'source' => 'mcp',
                'token_id' => $token?->id,
                'changed_fields' => array_keys($changes),
                'blocks_updated' => $blocksUpdated,
                'blocks_created' => $blocksCreated,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        $page->refresh();

        return Response::structured([
            'success' => true,
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'status' => $page->status,
                'template' => $page->template,
                'url' => $page->getUrl(),
            ],
            'changes' => [
                'fields_updated' => array_keys($changes),
                'blocks_updated' => $blocksUpdated,
                'blocks_created' => $blocksCreated,
            ],
        ]);
    }
}
