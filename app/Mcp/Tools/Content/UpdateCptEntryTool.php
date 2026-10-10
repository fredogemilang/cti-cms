<?php

namespace App\Mcp\Tools\Content;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\Activity;
use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Services\EditorialWorkflowService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Update an existing CPT entry. Meta values are merged (not replaced). Use get-cpt-entry first to see current values.')]
class UpdateCptEntryTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'cpt_slug' => $schema->string()
                ->description('The CPT slug to identify the post type.')
                ->required(),

            'entry_id' => $schema->integer()
                ->description('The entry ID to update.')
                ->required(),

            'title' => $schema->string()
                ->description('New title.'),

            'slug' => $schema->string()
                ->description('New URL slug.'),

            'content' => $schema->string()
                ->description('New content body.'),

            'excerpt' => $schema->string()
                ->description('New excerpt.'),

            'featured_image' => $schema->string()
                ->description('New featured image path.'),

            'status' => $schema->string()
                ->enum(['draft', 'pending_review', 'published', 'scheduled', 'archived'])
                ->description('New status. Publishing requires the mcp.content.publish ability AND the content.approve permission on the token owner; without it the status is downgraded to pending_review.'),

            'published_at' => $schema->string()
                ->description('ISO-8601 date/time. Required when status is "scheduled" — scheduled content without it never publishes.'),

            'meta' => $schema->object()
                ->description('Meta field values to update. Merged with existing meta (not replaced).'),

            'translations' => $schema->object()
                ->description('Translations to update. Merged with existing per locale.'),

            'taxonomy_terms' => $schema->array()
                ->description('Taxonomy term IDs to sync (replaces current terms).'),

            'menu_order' => $schema->integer()
                ->description('New sort order.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.write');

        $cpt = CustomPostType::where('slug', $request->get('cpt_slug'))->where('is_active', true)->first();
        if (! $cpt) {
            return Response::error("CPT '{$request->get('cpt_slug')}' not found.");
        }

        $entry = CptEntry::where('post_type_id', $cpt->id)->find($request->get('entry_id'));
        if (! $entry) {
            return Response::error("Entry with ID {$request->get('entry_id')} not found in CPT '{$cpt->slug}'.");
        }

        $token = McpAbilityGuard::resolveToken();

        // Check if locked by someone else (simple field check — CptEntry doesn't have isLockedByOther)
        if ($entry->locked_by && $entry->locked_at && $entry->locked_by !== $token?->tokenable_id) {
            $lockedMinutesAgo = $entry->locked_at->diffInMinutes(now());
            if ($lockedMinutesAgo < 2) {
                return Response::error('Entry is currently locked by another editor.');
            }
        }

        $changes = [];

        // Simple fields
        foreach (['title', 'slug', 'content', 'excerpt', 'featured_image', 'menu_order'] as $field) {
            if ($request->get($field) !== null) {
                $changes[$field] = $request->get($field);
            }
        }

        // Status. The workflow service is resolved unconditionally so every transition —
        // including an explicit `pending_review` submission or a send-back to `draft` —
        // reaches handleTransition().
        $notice = null;
        $oldStatus = $entry->status;
        $workflow = app(EditorialWorkflowService::class);
        $tokenUser = McpAbilityGuard::resolveToken()?->tokenable;

        if ($status = $request->get('status')) {
            if (in_array($status, ['published', 'scheduled'], true)) {
                McpAbilityGuard::authorize('mcp.content.publish');
                if (! $workflow->canApprove($tokenUser)) {
                    $status = 'pending_review';
                    $notice = 'Status automatically set to pending_review: token user lacks content.approve permission.';
                } elseif ($status === 'published') {
                    $changes['published_at'] = $request->get('published_at') ?: ($entry->published_at ?? now());
                } elseif ($status === 'scheduled') {
                    // content:publish-scheduled filters on whereNotNull('published_at').
                    $scheduledFor = $request->get('published_at') ?: $entry->published_at;
                    if (! $scheduledFor) {
                        return Response::error('Status "scheduled" requires "published_at" — without it the entry would never publish.');
                    }
                    $changes['published_at'] = $scheduledFor;
                }
            }
            $changes['status'] = $status;
        }

        // Meta merge
        if ($meta = $request->get('meta')) {
            $existingMeta = $entry->meta ?? [];
            $changes['meta'] = array_merge($existingMeta, $meta);
        }

        // Translations merge
        if ($translations = $request->get('translations')) {
            $existing = $entry->translations ?? [];
            foreach ($translations as $locale => $fields) {
                $existing[$locale] = array_merge($existing[$locale] ?? [], $fields);
            }
            $changes['translations'] = $existing;
        }

        if (! empty($changes)) {
            $entry->update($changes);
        }

        // Taxonomy terms sync
        if ($termIds = $request->get('taxonomy_terms')) {
            $entry->terms()->sync($termIds);
        }

        // Audit log
        Activity::create([
            'user_id' => $token?->tokenable_id,
            'action' => 'updated',
            'subject_type' => CptEntry::class,
            'subject_id' => $entry->id,
            'description' => "CPT entry \"{$entry->title}\" ({$cpt->name}) updated via MCP",
            'properties' => [
                'source' => 'mcp',
                'token_id' => $token?->id,
                'cpt' => $cpt->slug,
                'changed_fields' => array_keys($changes),
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        $entry->refresh();

        if (isset($changes['status'])) {
            $workflow->handleTransition($entry, $entry->status, $oldStatus, $tokenUser);
        }

        $res = [
            'success' => true,
            'entry' => [
                'id' => $entry->id,
                'title' => $entry->title,
                'slug' => $entry->slug,
                'cpt' => $cpt->slug,
                'status' => $entry->status,
                'url' => $entry->getUrl(),
            ],
            'changes' => array_keys($changes),
        ];

        if ($notice) {
            $res['notice'] = $notice;
        }

        return Response::structured($res);
    }
}
