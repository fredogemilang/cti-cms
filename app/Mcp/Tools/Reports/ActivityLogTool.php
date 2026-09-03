<?php

namespace App\Mcp\Tools\Reports;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\Activity;
use App\Models\CptEntry;
use App\Models\Form;
use App\Models\Media;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Get recent activity log entries. Shows who did what and when — page edits, CPT changes, settings updates, MCP operations. Supports filtering by action, subject type, and user.')]
#[IsReadOnly]
#[IsIdempotent]
class ActivityLogTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'action' => $schema->string()
                ->description('Filter by action type: created, updated, deleted, published, login.'),

            'subject_type' => $schema->string()
                ->description('Filter by subject model: Page, CptEntry, Setting, Media, Form.'),

            'user_id' => $schema->integer()
                ->description('Filter by user ID.'),

            'source' => $schema->string()
                ->enum(['mcp', 'admin', 'all'])
                ->description('Filter by source. "mcp" = only MCP operations, "admin" = only admin panel operations. Default: all.')
                ->default('all'),

            'limit' => $schema->integer()
                ->description('Maximum entries. Default: 50, Max: 200.')
                ->default(50),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.admin');

        $query = Activity::with('user')->orderBy('created_at', 'desc');

        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        if ($subjectType = $request->get('subject_type')) {
            $modelMap = [
                'Page' => Page::class,
                'CptEntry' => CptEntry::class,
                'Setting' => Setting::class,
                'Media' => Media::class,
                'Form' => Form::class,
            ];
            $fullClass = $modelMap[$subjectType] ?? $subjectType;
            $query->where('subject_type', $fullClass);
        }

        if ($userId = $request->get('user_id')) {
            $query->where('user_id', $userId);
        }

        $source = $request->get('source') ?? 'all';
        if ($source === 'mcp') {
            $query->whereJsonContains('properties->source', 'mcp');
        } elseif ($source === 'admin') {
            $query->where(function ($q) {
                $q->whereNull('properties')
                    ->orWhere(function ($q2) {
                        $q2->whereJsonDoesntContain('properties->source', 'mcp');
                    });
            });
        }

        $limit = min((int) ($request->get('limit') ?? 50), 200);
        $activities = $query->take($limit)->get();

        return Response::structured([
            'total' => $activities->count(),
            'activities' => $activities->map(fn ($a) => [
                'id' => $a->id,
                'action' => $a->action,
                'description' => $a->description,
                'subject_type' => class_basename($a->subject_type ?? ''),
                'subject_id' => $a->subject_id,
                'user' => $a->user ? [
                    'id' => $a->user->id,
                    'name' => $a->user->name,
                ] : null,
                'source' => $a->properties['source'] ?? 'admin',
                'ip_address' => $a->ip_address,
                'created_at' => $a->created_at?->toIso8601String(),
            ])->toArray(),
        ]);
    }
}
