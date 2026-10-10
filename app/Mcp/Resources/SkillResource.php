<?php

namespace App\Mcp\Resources;

use App\Mcp\Guards\McpAbilityGuard;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\MimeType;
use Laravel\Mcp\Server\Contracts\HasUriTemplate;
use Laravel\Mcp\Server\Resource;
use Laravel\Mcp\Support\UriTemplate;

#[MimeType('text/markdown')]
#[Description('AI skill documents — domain-specific instructions for theme development, plugin development, content management, CPT system, translations, media pipeline, forms, and SEO. Available skills: cms-architecture, theme-development, plugin-development, content-management, cpt-system, translation-system, media-pipeline, form-system, seo-best-practices')]
class SkillResource extends Resource implements HasUriTemplate
{
    /**
     * Available skill slugs mapped to their filenames.
     */
    protected static array $skills = [
        'cms-architecture' => 'cms-architecture.md',
        'theme-development' => 'theme-development.md',
        'plugin-development' => 'plugin-development.md',
        'content-management' => 'content-management.md',
        'cpt-system' => 'cpt-system.md',
        'translation-system' => 'translation-system.md',
        'media-pipeline' => 'media-pipeline.md',
        'form-system' => 'form-system.md',
        'seo-best-practices' => 'seo-best-practices.md',
    ];

    public function uriTemplate(): UriTemplate
    {
        return new UriTemplate('cms://skills/{skillName}');
    }

    public function handle(Request $request): Response
    {
        McpAbilityGuard::authorize('mcp.connect');

        $skillName = $request->get('skillName');

        if (! $skillName || $skillName === '{skillName}') {
            // Return index of available skills
            $index = "# CTI CMS — Available AI Skills\n\n";
            $index .= "Read a specific skill using URI: `cms://skills/{skill-name}`\n\n";
            $index .= "| Skill | Description |\n";
            $index .= "|-------|-------------|\n";
            $index .= "| `cms-architecture` | Complete CMS architecture overview |\n";
            $index .= "| `theme-development` | Theme development rules and guidelines |\n";
            $index .= "| `plugin-development` | Plugin development rules and guidelines |\n";
            $index .= "| `content-management` | Content CRUD, blocks, page lifecycle |\n";
            $index .= "| `cpt-system` | Custom Post Type system deep-dive |\n";
            $index .= "| `translation-system` | Dual translation system (model + string) |\n";
            $index .= "| `media-pipeline` | Media upload, variants, rendering rules |\n";
            $index .= "| `form-system` | Form Builder and placeholders |\n";
            $index .= "| `seo-best-practices` | SEO meta, breadcrumbs, structured data |\n";

            return Response::text($index);
        }

        $filename = static::$skills[$skillName] ?? null;

        if (! $filename) {
            $available = implode(', ', array_keys(static::$skills));

            return Response::text("Skill '{$skillName}' not found.\n\nAvailable skills: {$available}");
        }

        $path = base_path("docs/ai-skills/{$filename}");

        if (! file_exists($path)) {
            return Response::text("Skill file not found on disk: docs/ai-skills/{$filename}");
        }

        return Response::text(file_get_contents($path));
    }
}
