<?php

namespace App\Mcp\Prompts;

use App\Models\CustomPostType;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

#[Name('create-cpt-entry')]
#[Description('Structured prompt that inspects a CPT schema and prepares AI to construct a valid CPT entry payload.')]
class CreateCptEntryPrompt extends Prompt
{
    public function arguments(): array
    {
        return [
            new Argument('cpt_slug', 'The slug of the Custom Post Type (required)', true),
        ];
    }

    public function handle(Request $request): Response
    {
        $cptSlug = $request->get('cpt_slug');
        if (! $cptSlug) {
            return Response::error('Missing required argument: cpt_slug');
        }

        $cpt = CustomPostType::where('slug', $cptSlug)->with('metaFields')->first();
        if (! $cpt) {
            return Response::error("Custom Post Type '{$cptSlug}' not found.");
        }

        $metaFields = $cpt->metaFields->map(fn ($f) => [
            'name' => $f->name,
            'label' => $f->label,
            'type' => $f->type,
            'is_required' => (bool) $f->is_required,
            'description' => $f->description,
        ])->toArray();

        $taxonomies = $cpt->taxonomies()->map(fn ($t) => [
            'id' => $t->id,
            'name' => $t->name,
            'slug' => $t->slug,
        ])->toArray();

        $isHierarchical = $cpt->is_hierarchical ? 'Yes (supports parent_id)' : 'No';
        $supports = $cpt->supports ? implode(', ', $cpt->supports) : 'title, editor';

        $output = <<<TEXT
# Instructions for Creating a CPT Entry: "{$cpt->name}" ({$cpt->slug})

## CPT Configuration:
- **Hierarchical**: {$isHierarchical}
- **Supports**: {$supports}

## Registered Meta Fields:
TEXT;
        $output .= "\n".json_encode($metaFields, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $output .= "\n\n## Attached Taxonomies:\n";
        $output .= json_encode($taxonomies, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $output .= <<<'TEXT'


## Critical Rules (STRICT):
1. **Status**: Create as `status: "draft"` first.
2. **Repeater Sub-fields**: Repeater fields must always adhere to the registered sub-field structure.
3. **Media References**: Never store external URLs in meta fields or database. Store relative paths (e.g. `uploads/filename.webp`).
4. **Translations**: Include Indonesian translation in `translations: { "id": { "title": "...", "content": "..." } }`.
5. **Call `create-cpt-entry` tool** once the payload is prepared.
TEXT;

        return Response::text($output);
    }
}
