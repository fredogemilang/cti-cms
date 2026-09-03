<?php

namespace App\Mcp\Tools\Content;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\Activity;
use App\Models\CptEntry;
use App\Models\CustomPostType;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[Description('Delete (soft-delete) a CPT entry. Requires two-step confirmation: first call returns a confirmation token, second call with that token performs the delete.')]
#[IsDestructive]
class DeleteCptEntryTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'cpt_slug' => $schema->string()
                ->description('The CPT slug.')
                ->required(),

            'entry_id' => $schema->integer()
                ->description('The entry ID to delete.')
                ->required(),

            'confirmation_token' => $schema->string()
                ->description('Confirmation token from the first call. Required to perform deletion.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.delete');

        $cpt = CustomPostType::where('slug', $request->get('cpt_slug'))->first();
        if (! $cpt) {
            return Response::error("CPT '{$request->get('cpt_slug')}' not found.");
        }

        $entry = CptEntry::where('post_type_id', $cpt->id)->find($request->get('entry_id'));
        if (! $entry) {
            return Response::error("Entry with ID {$request->get('entry_id')} not found in CPT '{$cpt->slug}'.");
        }

        $confirmationToken = $request->get('confirmation_token');
        $cacheKey = "mcp_confirm_delete_cpt_{$entry->id}";

        // Step 1: Return confirmation
        if (! $confirmationToken) {
            $nonce = Str::random(40);
            Cache::put($cacheKey, $nonce, now()->addMinutes(5));

            $relatedCount = $entry->relatedEntries()->count();
            $termsCount = $entry->terms()->count();

            $warnings = [];
            if ($relatedCount > 0) {
                $warnings[] = "This entry has {$relatedCount} related entries that will lose their relationship.";
            }
            if ($termsCount > 0) {
                $warnings[] = "This entry has {$termsCount} taxonomy term assignments that will be detached.";
            }

            return Response::structured([
                'requires_confirmation' => true,
                'confirmation_token' => $nonce,
                'expires_in' => '5 minutes',
                'entry' => [
                    'id' => $entry->id,
                    'title' => $entry->title,
                    'slug' => $entry->slug,
                    'cpt' => $cpt->name,
                    'status' => $entry->status,
                ],
                'warnings' => $warnings,
                'instruction' => 'Call this tool again with the confirmation_token within 5 minutes to confirm deletion. This is a soft-delete (recoverable).',
            ]);
        }

        // Step 2: Verify random nonce from Cache
        $cachedNonce = Cache::get($cacheKey);
        if (! $cachedNonce || ! hash_equals($cachedNonce, $confirmationToken)) {
            return Response::error('Invalid or expired confirmation token. Request a new one by calling without confirmation_token.');
        }

        Cache::forget($cacheKey);

        $token = McpAbilityGuard::resolveToken();
        $title = $entry->title;

        // Detach terms before soft-delete
        $entry->terms()->detach();
        $entry->delete();

        Activity::create([
            'user_id' => $token?->tokenable_id,
            'action' => 'deleted',
            'subject_type' => CptEntry::class,
            'subject_id' => $entry->id,
            'description' => "CPT entry \"{$title}\" ({$cpt->name}) soft-deleted via MCP",
            'properties' => ['source' => 'mcp', 'token_id' => $token?->id, 'cpt' => $cpt->slug],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return Response::structured([
            'success' => true,
            'deleted' => [
                'id' => $entry->id,
                'title' => $title,
                'cpt' => $cpt->slug,
                'type' => 'soft_delete',
                'recoverable' => true,
            ],
        ]);
    }
}
