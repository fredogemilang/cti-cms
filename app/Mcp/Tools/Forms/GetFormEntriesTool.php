<?php

namespace App\Mcp\Tools\Forms;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\Form;
use App\Models\FormEntry;
use Carbon\Carbon;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Get form submission entries/statistics for a specific form. Returns submission data, counts, and recent entries. Requires mcp.admin ability.')]
#[IsReadOnly]
#[IsIdempotent]
class GetFormEntriesTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'form_id' => $schema->integer()
                ->description('The form ID. Use list-forms to find IDs.'),

            'form_slug' => $schema->string()
                ->description('The form slug. Provide either form_id or form_slug.'),

            'limit' => $schema->integer()
                ->description('Maximum entries to return. Default: 20, Max: 100.')
                ->default(20),

            'since' => $schema->string()
                ->description('Only show entries after this ISO date (e.g., "2026-01-01").'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.admin');

        $formId = $request->get('form_id');
        $formSlug = $request->get('form_slug');

        if (! $formId && ! $formSlug) {
            return Response::error('Provide either "form_id" or "form_slug".');
        }

        $form = $formId
            ? Form::find($formId)
            : Form::where('slug', $formSlug)->first();

        if (! $form) {
            return Response::error('Form not found. Use list-forms to see available forms.');
        }

        $query = FormEntry::where('form_id', $form->id)->orderBy('created_at', 'desc');

        if ($since = $request->get('since')) {
            try {
                $parsedSince = Carbon::parse($since);
                $query->where('created_at', '>=', $parsedSince);
            } catch (\Throwable) {
                return Response::error('Invalid "since" date format. Use ISO-8601 (e.g. 2026-01-01).');
            }
        }

        $limit = max(1, min((int) ($request->get('limit') ?? 20), 100));
        $total = (clone $query)->count();
        $entries = $query->take($limit)->get();

        return Response::structured([
            'form' => [
                'id' => $form->id,
                'name' => $form->name,
                'slug' => $form->slug,
            ],
            'total_submissions' => $total,
            'showing' => $entries->count(),
            'entries' => $entries->map(function ($e) {
                $data = $e->data ?? [];
                // Redact sensitive keys if present in form submission
                $safeData = [];
                foreach ($data as $k => $v) {
                    $lowerK = strtolower($k);
                    if (str_contains($lowerK, 'password') || str_contains($lowerK, 'secret') || str_contains($lowerK, 'card') || str_contains($lowerK, 'cvv')) {
                        $safeData[$k] = '********';
                    } else {
                        $safeData[$k] = $v;
                    }
                }

                // Mask IP address (keep network, hide host)
                $maskedIp = $e->ip_address;
                if ($maskedIp && str_contains($maskedIp, '.')) {
                    $parts = explode('.', $maskedIp);
                    if (count($parts) === 4) {
                        $parts[3] = 'xxx';
                        $maskedIp = implode('.', $parts);
                    }
                }

                return [
                    'id' => $e->id,
                    'data' => $safeData,
                    'ip_address' => $maskedIp,
                    'created_at' => $e->created_at?->toIso8601String(),
                ];
            })->toArray(),
        ]);
    }
}
