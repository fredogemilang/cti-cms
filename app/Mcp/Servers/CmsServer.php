<?php

namespace App\Mcp\Servers;

use App\Mcp\Prompts\CreateCptEntryPrompt;
use App\Mcp\Prompts\CreatePagePrompt;
use App\Mcp\Prompts\GenerateSeoPrompt;
use App\Mcp\Prompts\ThemeDevelopmentPrompt;
use App\Mcp\Resources\ActiveThemeResource;
use App\Mcp\Resources\CmsArchitectureResource;
use App\Mcp\Resources\ConventionsResource;
use App\Mcp\Resources\DatabaseSchemaResource;
use App\Mcp\Resources\RouteMapResource;
use App\Mcp\Resources\SkillResource;
use App\Mcp\Tools\Content\CreateCptEntryTool;
use App\Mcp\Tools\Content\CreatePageTool;
use App\Mcp\Tools\Content\DeleteCptEntryTool;
use App\Mcp\Tools\Content\DeletePageTool;
use App\Mcp\Tools\Content\GetCptEntryTool;
use App\Mcp\Tools\Content\GetPageTool;
use App\Mcp\Tools\Content\ListCptEntriesTool;
use App\Mcp\Tools\Content\ListPagesTool;
use App\Mcp\Tools\Content\UpdateCptEntryTool;
use App\Mcp\Tools\Content\UpdatePageTool;
use App\Mcp\Tools\Forms\GetFormEntriesTool;
use App\Mcp\Tools\Forms\GetFormWithFieldsTool;
use App\Mcp\Tools\Forms\ListFormsTool;
use App\Mcp\Tools\Media\GetMediaInfoTool;
use App\Mcp\Tools\Media\ListMediaTool;
use App\Mcp\Tools\Media\UploadMediaTool;
use App\Mcp\Tools\Reports\ActivityLogTool;
use App\Mcp\Tools\Reports\ContentReportTool;
use App\Mcp\Tools\Reports\SeoAuditTool;
use App\Mcp\Tools\Schema\CreateCptTool;
use App\Mcp\Tools\Schema\GetCptSchemaTool;
use App\Mcp\Tools\Schema\GetThemeConfigTool;
use App\Mcp\Tools\Schema\ListCptsTool;
use App\Mcp\Tools\Schema\ListMetaFieldsTool;
use App\Mcp\Tools\Schema\ListTaxonomiesTool;
use App\Mcp\Tools\Settings\GetSettingsTool;
use App\Mcp\Tools\Settings\UpdateSettingTool;
use App\Mcp\Tools\Theme\GetActiveThemeTool;
use App\Mcp\Tools\Theme\GetViewHierarchyTool;
use App\Mcp\Tools\Theme\ListFormPlaceholdersTool;
use App\Mcp\Tools\Theme\ListTemplateBlocksTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Tool;

#[Name('CTI CMS')]
#[Version('1.0.0')]
#[Instructions(<<<'INSTRUCTIONS'
# CTI CMS — MCP Server

You are connected to the CTI CMS MCP server. This is a modular, multi-tenant CMS built with Laravel 13, Livewire 4, and Tailwind CSS 4.

## MANDATORY: Read Context First

Before performing ANY operation, you MUST read the relevant CMS context:
1. Use the `cms://architecture` resource for overall architecture
2. Use the `cms://skills/{skillName}` resource for domain-specific instructions
3. Use the `cms://schema/database` resource to understand data structure
4. Use the `cms://theme/active` resource to understand the current theme

Available skills: cms-architecture, theme-development, plugin-development, content-management, cpt-system, translation-system, media-pipeline, form-system, seo-best-practices

## Core Rules (ENFORCED)

### Translation System
- NEVER hardcode text in Blade views — always use `t('key', 'Default')`
- Model translations use `HasTranslations` trait with `translations` JSON column
- EN and ID translations MUST always be in sync

### Asset System
- NEVER use `asset('storage/')` — use `resolve_block_asset()`
- NEVER use raw `<img>` for user-uploaded images — use `<x-image>` component
- NEVER use external CDN URLs — all assets must be local
- Content images MUST go through `MediaService` for responsive variant generation
- Static theme assets (icons, logos, SVGs) go in `themes/{slug}/assets/`

### Architecture Boundary
- **Core** (`app/`): Generic CMS functionality only
- **Theme** (`themes/`): Client-specific templates and views
- **Plugin** (`plugins/`): Domain-specific business logic
- NEVER add client-specific code to core

### Content Operations
- Always create content as `draft` first, then publish separately
- Always provide translations for ALL configured locales (en, id)
- Destructive operations (delete) require `mcp.delete` ability and two-step confirmation
- SEO meta is recommended for all content

### Permission System
- Your abilities are determined by the API token used to connect
- Read operations require `mcp.read`
- Write operations require `mcp.write`
- Delete operations require `mcp.delete`
- Admin operations require `mcp.admin`
- Theme operations require `mcp.theme.read` or `mcp.theme.write`
- Publishing requires `mcp.content.publish`

### Plugin Safety
- Check `is_plugin_active('posts')` before querying plugin models
- Handle `ClassNotFoundException` gracefully when plugin is disabled

### Destructive Operations
- Delete tools use TWO-STEP confirmation: first call returns a token, second call with token executes
- System pages (is_system=true) cannot be deleted
- All deletes are soft-deletes (recoverable from admin panel)
- Every mutating operation is logged to the activity/audit table
INSTRUCTIONS)]
class CmsServer extends Server
{
    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<Tool>>
     */
    protected array $tools = [
        // Content — Read
        ListPagesTool::class,
        GetPageTool::class,
        ListCptEntriesTool::class,
        GetCptEntryTool::class,

        // Content — Write
        CreatePageTool::class,
        UpdatePageTool::class,
        CreateCptEntryTool::class,
        UpdateCptEntryTool::class,

        // Content — Delete (two-step confirmation)
        DeletePageTool::class,
        DeleteCptEntryTool::class,

        // Schema
        ListCptsTool::class,
        GetCptSchemaTool::class,
        CreateCptTool::class,
        ListMetaFieldsTool::class,
        ListTaxonomiesTool::class,
        GetThemeConfigTool::class,

        // Theme
        GetActiveThemeTool::class,
        ListTemplateBlocksTool::class,
        ListFormPlaceholdersTool::class,
        GetViewHierarchyTool::class,

        // Media
        ListMediaTool::class,
        GetMediaInfoTool::class,
        UploadMediaTool::class,

        // Forms
        ListFormsTool::class,
        GetFormWithFieldsTool::class,
        GetFormEntriesTool::class,

        // Settings
        GetSettingsTool::class,
        UpdateSettingTool::class,

        // Reports
        ContentReportTool::class,
        SeoAuditTool::class,
        ActivityLogTool::class,
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<Server\Resource>>
     */
    protected array $resources = [
        CmsArchitectureResource::class,
        DatabaseSchemaResource::class,
        ActiveThemeResource::class,
        RouteMapResource::class,
        ConventionsResource::class,
        SkillResource::class,
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<Prompt>>
     */
    protected array $prompts = [
        CreatePagePrompt::class,
        CreateCptEntryPrompt::class,
        GenerateSeoPrompt::class,
        ThemeDevelopmentPrompt::class,
    ];
}
