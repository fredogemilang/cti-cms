<?php

namespace App\Mcp\Tools\Reports;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\FormEntry;
use App\Models\Media;
use App\Models\Page;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Get content statistics report: total pages, CPT entries by type and status, media count, form submission count. Provides a high-level overview of CMS content.')]
#[IsReadOnly]
#[IsIdempotent]
class ContentReportTool extends Tool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.admin');

        // Pages
        $pages = Page::withTrashed()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // CPT entries per type
        $cpts = CustomPostType::where('is_active', true)->get();
        $cptStats = [];
        foreach ($cpts as $cpt) {
            $entries = CptEntry::where('post_type_id', $cpt->id)
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $cptStats[] = [
                'name' => $cpt->name,
                'slug' => $cpt->slug,
                'total' => array_sum($entries),
                'by_status' => $entries,
            ];
        }

        // Media
        $mediaCount = Media::count();
        $mediaSize = Media::sum('size');

        // Forms
        $formSubmissions = 0;
        if (class_exists(FormEntry::class)) {
            $formSubmissions = FormEntry::count();
        }

        return Response::structured([
            'pages' => [
                'total' => array_sum($pages),
                'by_status' => $pages,
            ],
            'cpt_entries' => $cptStats,
            'media' => [
                'total_files' => $mediaCount,
                'total_size_mb' => round($mediaSize / 1024 / 1024, 2),
            ],
            'form_submissions' => $formSubmissions,
            'generated_at' => now()->toIso8601String(),
        ]);
    }
}
