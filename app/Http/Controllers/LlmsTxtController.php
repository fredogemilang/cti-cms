<?php

namespace App\Http\Controllers;

use App\Models\CustomPostType;
use App\Models\Page;
use App\Services\Ai\AiResourceRegistry;
use Illuminate\Http\Response;

/**
 * Serves /llms.txt — a structured plain-text file that tells AI
 * crawlers and LLM-based search engines what this site is about.
 *
 * Format follows the emerging llms.txt specification:
 *
 * @see https://llmstxt.org/
 */
class LlmsTxtController extends Controller
{
    public function index(AiResourceRegistry $aiResourceRegistry): Response
    {
        if (! setting('seo_llms_enabled', true)) {
            abort(404);
        }

        $siteName = (string) setting('site_name', config('app.name', 'Website'));
        $siteUrl = url('/');
        $summary = (string) setting('seo_ai_summary', '');
        $orgName = (string) setting('seo_org_name', '') ?: $siteName;

        $lines = [];

        // Header
        $lines[] = "# {$siteName}";
        $lines[] = '';

        // Summary
        if ($summary !== '') {
            $lines[] = "> {$summary}";
            $lines[] = '';
        }

        // Organization info
        $lines[] = '## About';
        $lines[] = "- Organization: {$orgName}";
        $lines[] = "- Website: {$siteUrl}";

        // Dynamic AI Resources
        $aiResources = $aiResourceRegistry->getResources();
        if (! empty($aiResources)) {
            $lines[] = '';
            $lines[] = '## AI Resources & Data Endpoints';
            foreach ($aiResources as $res) {
                $desc = $res['description'] ? " ({$res['description']})" : '';
                $lines[] = "- {$res['label']}: {$res['url']}{$desc}";
            }
        }

        if ($desc = setting('seo_org_description')) {
            $lines[] = "- Description: {$desc}";
        }
        if ($email = setting('seo_org_email')) {
            $lines[] = "- Contact: {$email}";
        }
        $lines[] = '';

        // Key pages
        $lines[] = '## Key Pages';
        $pages = Page::where('status', 'published')
            ->orderBy('menu_order')
            ->take(20)
            ->get(['title', 'slug']);
        foreach ($pages as $page) {
            $url = url('/'.$page->slug);
            $lines[] = "- [{$page->title}]({$url})";
        }
        $lines[] = '';

        // Custom Post Types
        $cpts = CustomPostType::where('is_active', true)->get();
        foreach ($cpts as $cpt) {
            $lines[] = "## {$cpt->name}";
            $lines[] = '- Archive: '.url('/'.$cpt->slug);
            $lines[] = '';
        }

        // Optional Markdown Files section
        $lines[] = '## Optional';
        $lines[] = '- Sitemap: '.url('/sitemap.xml');
        $lines[] = '- Robots: '.url('/robots.txt');

        $content = implode("\n", $lines);

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }
}
