<?php

namespace App\Mcp\Tools\Content;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\Activity;
use App\Models\Page;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[Description('Delete (soft-delete) a CMS page. Requires two-step confirmation: first call returns a confirmation token, second call with that token performs the delete. System pages cannot be deleted.')]
#[IsDestructive]
class DeletePageTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->description('The page ID to delete.')
                ->required(),

            'confirmation_token' => $schema->string()
                ->description('Confirmation token returned by the first call. Required to actually perform the delete.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.delete');

        $page = Page::find($request->get('id'));
        if (! $page) {
            return Response::error("Page with ID {$request->get('id')} not found.");
        }

        if ($page->is_system) {
            return Response::error("Cannot delete system page \"{$page->title}\". System pages are protected.");
        }

        $confirmationToken = $request->get('confirmation_token');
        $cacheKey = "mcp_confirm_delete_page_{$page->id}";

        // Step 1: Return confirmation prompt with secure random token
        if (! $confirmationToken) {
            $nonce = Str::random(40);
            Cache::put($cacheKey, $nonce, now()->addMinutes(5));

            $childCount = $page->children()->count();
            $blockCount = $page->allBlocks()->count();

            $warnings = [];
            if ($childCount > 0) {
                $warnings[] = "This page has {$childCount} child page(s) that will become orphaned.";
            }
            if ($blockCount > 0) {
                $warnings[] = "This page has {$blockCount} block(s) that will be soft-deleted.";
            }

            return Response::structured([
                'requires_confirmation' => true,
                'confirmation_token' => $nonce,
                'expires_in' => '5 minutes',
                'page' => [
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'status' => $page->status,
                ],
                'warnings' => $warnings,
                'instruction' => 'Call this tool again with the confirmation_token within 5 minutes to confirm deletion. This will soft-delete the page (recoverable from admin panel).',
            ]);
        }

        // Step 2: Verify random nonce from Cache
        $cachedNonce = Cache::get($cacheKey);
        if (! $cachedNonce || ! hash_equals($cachedNonce, $confirmationToken)) {
            return Response::error('Invalid or expired confirmation token. Request a new one by calling without confirmation_token.');
        }

        Cache::forget($cacheKey);

        $token = McpAbilityGuard::resolveToken();
        $title = $page->title;

        $page->delete(); // Soft delete

        // Audit log
        Activity::create([
            'user_id' => $token?->tokenable_id,
            'action' => 'deleted',
            'subject_type' => Page::class,
            'subject_id' => $page->id,
            'description' => "Page \"{$title}\" soft-deleted via MCP",
            'properties' => ['source' => 'mcp', 'token_id' => $token?->id],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return Response::structured([
            'success' => true,
            'deleted' => [
                'id' => $page->id,
                'title' => $title,
                'type' => 'soft_delete',
                'recoverable' => true,
            ],
        ]);
    }
}
