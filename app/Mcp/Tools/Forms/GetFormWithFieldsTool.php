<?php

namespace App\Mcp\Tools\Forms;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\Form;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Get a specific form by ID or slug, including all its field definitions with types, labels, validation rules, and options.')]
#[IsReadOnly]
#[IsIdempotent]
class GetFormWithFieldsTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->description('The form ID. Provide either id or slug.'),

            'slug' => $schema->string()
                ->description('The form slug. Provide either id or slug.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.read');

        $id = $request->get('id');
        $slug = $request->get('slug');

        if (! $id && ! $slug) {
            return Response::error('Provide either "id" or "slug" to identify the form.');
        }

        $query = Form::with(['fields' => fn ($q) => $q->orderBy('sort_order')]);

        if ($id) {
            $query->where('id', $id);
        } else {
            $query->where('slug', $slug);
        }

        $form = $query->first();
        if (! $form) {
            return Response::error('Form not found. Use list-forms to see available forms.');
        }

        return Response::structured([
            'id' => $form->id,
            'name' => $form->name,
            'slug' => $form->slug,
            'description' => $form->description,
            'success_message' => $form->success_message,
            'email_notification' => $form->email_notification ?? false,
            'notification_email' => $form->notification_email,
            'fields' => $form->fields->map(fn ($f) => [
                'id' => $f->id,
                'label' => $f->label,
                'name' => $f->name,
                'type' => $f->type,
                'is_required' => $f->is_required ?? false,
                'placeholder' => $f->placeholder,
                'validation_rules' => $f->validation_rules,
                'options' => $f->options,
                'sort_order' => $f->sort_order,
            ])->toArray(),
            'submission_endpoint' => url("/api/v1/forms/{$form->slug}/submit"),
        ]);
    }
}
