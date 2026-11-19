<?php

namespace App\Mcp\Prompts;

use App\Models\Page;
use App\Services\ThemeLoader;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

#[Name('create-page')]
#[Description('Structured prompt that guides an AI to construct and validate a new CMS page before calling create-page tool.')]
class CreatePagePrompt extends Prompt
{
    public function arguments(): array
    {
        return [
            new Argument('template', 'Page template slug (e.g., "home", "default", "contact")', false),
            new Argument('locale', 'Target locale (en or id, default: en)', false),
        ];
    }

    public function handle(Request $request): Response
    {
        $template = $request->get('template') ?? 'default';
        $locale = $request->get('locale') ?? 'en';

        $theme = app(ThemeLoader::class)->getActiveTheme();
        $themeTemplates = $theme ? $theme->getPageTemplates() : Page::getTemplates();
        $blockDefinitions = [];

        if ($theme && isset($theme->getConfig()['page_templates'][$template]['blocks'])) {
            $blockDefinitions = $theme->getConfig()['page_templates'][$template]['blocks'];
        }

        $instructions = <<<TEXT
# Instructions for Creating a New CMS Page

You are creating a CMS page with template: "{$template}" and primary locale: "{$locale}".

## Core CMS Rules (STRICT):
1. **Draft First**: Always create the page with `status: "draft"`. Do not set `published` immediately unless explicitly instructed.
2. **Translations**: The CMS is bilingual (EN + ID). Always prepare translations for both locales:
   - Primary content in default locale
   - Secondary locale in `translations: { "id": { "title": "...", "slug": "..." } }`
3. **Images & Assets**:
   - Never use external CDN links (User Rule #10).
   - Never use `asset('storage/...')` in Blade templates; always use `resolve_block_asset()`.
   - All uploaded images must already exist in the Media Library or be uploaded via `upload-media`.
4. **Block Structure**:
   - Template blocks detected for "{$template}":
TEXT;

        if (! empty($blockDefinitions)) {
            $instructions .= "\n".json_encode($blockDefinitions, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } else {
            $instructions .= "\n(No predefined block schema found for this template; you may supply custom key-value blocks).";
        }

        $instructions .= <<<TEXT


## Next Steps:
1. Review the required blocks above.
2. Formulate the JSON payload for the `create-page` tool.
3. Call `create-page` with:
   - `title`: string
   - `slug`: string (optional, auto-generated if blank)
   - `template`: "{$template}"
   - `status`: "draft"
   - `blocks`: object with block values
   - `translations`: object with locale translations
   - `seo`: { "meta_title": "...", "meta_description": "..." }
TEXT;

        return Response::text($instructions);
    }
}
