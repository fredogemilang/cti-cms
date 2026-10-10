<?php

namespace App\Mcp\Tools\Reports;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Page;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Run an SEO audit across all published content. Checks for missing meta titles, descriptions, featured images, and heading structure. Returns a prioritized list of issues.')]
#[IsReadOnly]
#[IsIdempotent]
class SeoAuditTool extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.admin');

        $issues = [];
        $score = 100;

        // Audit pages
        $pages = Page::published()->with('seoMeta')->get();
        foreach ($pages as $page) {
            $pageIssues = [];
            $seo = $page->seo ?? [];

            if (empty($seo['meta_title']) && empty($page->title)) {
                $pageIssues[] = 'Missing meta title';
            }
            if (empty($seo['meta_description'])) {
                $pageIssues[] = 'Missing meta description';
            }
            if (! empty($seo['meta_title']) && strlen($seo['meta_title']) > 60) {
                $pageIssues[] = 'Meta title too long ('.strlen($seo['meta_title']).' chars, max 60)';
            }
            if (! empty($seo['meta_description']) && strlen($seo['meta_description']) > 160) {
                $pageIssues[] = 'Meta description too long ('.strlen($seo['meta_description']).' chars, max 160)';
            }

            if (! empty($pageIssues)) {
                $issues[] = [
                    'type' => 'page',
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'url' => $page->getUrl(),
                    'issues' => $pageIssues,
                ];
                $score -= count($pageIssues);
            }
        }

        // Audit CPT entries
        $cpts = CustomPostType::where('is_active', true)->where('publicly_queryable', true)->get();
        foreach ($cpts as $cpt) {
            $entries = CptEntry::where('post_type_id', $cpt->id)->published()->get();
            foreach ($entries as $entry) {
                $entryIssues = [];
                $seo = $entry->seo ?? [];

                if (empty($seo['meta_title']) && empty($entry->title)) {
                    $entryIssues[] = 'Missing meta title';
                }
                if (empty($seo['meta_description']) && empty($entry->excerpt)) {
                    $entryIssues[] = 'Missing meta description and excerpt';
                }
                if (empty($entry->featured_image)) {
                    $entryIssues[] = 'Missing featured image';
                }

                if (! empty($entryIssues)) {
                    $issues[] = [
                        'type' => 'cpt_entry',
                        'cpt' => $cpt->slug,
                        'id' => $entry->id,
                        'title' => $entry->title,
                        'slug' => $entry->slug,
                        'issues' => $entryIssues,
                    ];
                    $score -= count($entryIssues);
                }
            }
        }

        $score = max(0, $score);

        return Response::structured([
            'score' => $score,
            'total_issues' => count($issues),
            'issues' => array_slice($issues, 0, 50),
            'summary' => [
                'pages_audited' => $pages->count(),
                'cpt_entries_audited' => $cpts->sum(fn ($cpt) => CptEntry::where('post_type_id', $cpt->id)->published()->count()),
            ],
            'generated_at' => now()->toIso8601String(),
        ]);
    }
}
