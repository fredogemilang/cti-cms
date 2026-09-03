<?php

namespace App\Mcp\Prompts;

use App\Models\CptEntry;
use App\Models\Page;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

#[Name('generate-seo')]
#[Description('Structured prompt that extracts content from a Page or CPT entry and instructs AI on optimal SEO meta generation.')]
class GenerateSeoPrompt extends Prompt
{
    public function arguments(): array
    {
        return [
            new Argument('content_type', 'Type of content: "page" or "cpt" (required)', true),
            new Argument('content_id', 'The ID of the page or CPT entry (required)', true),
        ];
    }

    public function handle(Request $request): Response
    {
        $type = strtolower((string) $request->get('content_type'));
        $id = (int) $request->get('content_id');

        if (! $type || ! $id) {
            return Response::error('Both content_type and content_id are required.');
        }

        $title = '';
        $body = '';
        $currentSeo = [];

        if ($type === 'page') {
            $page = Page::find($id);
            if (! $page) {
                return Response::error("Page with ID {$id} not found.");
            }
            $title = $page->title;
            $currentSeo = $page->seo ?? [];
            $body = collect($page->allBlocks)->pluck('value')->implode(' ');
        } elseif ($type === 'cpt') {
            $entry = CptEntry::with('postType')->find($id);
            if (! $entry) {
                return Response::error("CPT entry with ID {$id} not found.");
            }
            $title = $entry->title;
            $currentSeo = $entry->seo ?? [];
            $body = $entry->content.' '.$entry->excerpt;
        } else {
            return Response::error('Invalid content_type. Supported values: "page" or "cpt".');
        }

        $cleanBody = preg_replace('/\s+/', ' ', strip_tags($body));
        $excerpt = mb_substr($cleanBody, 0, 500);

        $promptText = <<<TEXT
# SEO Generation Task for: "{$title}"

## Content Details:
- **Type**: {$type} (ID: {$id})
- **Title**: {$title}
- **Content Sample**: "{$excerpt}..."
- **Current SEO**:
TEXT;
        $promptText .= "\n".json_encode($currentSeo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $promptText .= <<<'TEXT'


## SEO Guidelines & Constraints:
1. **Meta Title**:
   - Optimal length: 50–60 characters.
   - Must include primary keyword early in the title.
   - Append brand name if space permits (e.g. `| CDT`).
2. **Meta Description**:
   - Optimal length: 130–155 characters (strict max 160).
   - Action-oriented call-to-action (CTA) or clear value proposition.
   - Do not use quotes or special brackets that break HTML meta tags.
3. **OpenGraph Tags**:
   - `og_title`: Same or slightly punchier than meta title.
   - `og_description`: Engaging summary for social shares.
4. **Bilingual Sync**:
   - Provide corresponding Indonesian translations for both title and description.

## Output Format:
Generate a JSON object matching this schema:
```json
{
  "meta_title": "...",
  "meta_description": "...",
  "og_title": "...",
  "og_description": "...",
  "id_translations": {
    "meta_title": "...",
    "meta_description": "..."
  }
}
```
After generating, apply it using `update-page` or `update-cpt-entry`.
TEXT;

        return Response::text($promptText);
    }
}
