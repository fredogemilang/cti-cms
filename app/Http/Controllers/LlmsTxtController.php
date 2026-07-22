<?php

namespace App\Http\Controllers;

use App\Models\CustomPostType;
use App\Models\Page;
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
    public function index(): Response
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
            $url = $page->slug === 'home' ? $siteUrl : url("/{$page->slug}");
            $lines[] = "- [{$page->title}]({$url})";
        }
        $lines[] = '';

        // Content types (CPTs)
        $cpts = CustomPostType::where('is_active', true)->get(['plural_label', 'slug', 'has_archive']);
        if ($cpts->isNotEmpty()) {
            $lines[] = '## Content Types';
            foreach ($cpts as $cpt) {
                $archiveUrl = $cpt->has_archive ? url("/{$cpt->slug}/") : null;
                $line = "- {$cpt->plural_label}";
                if ($archiveUrl) {
                    $line .= ": [{$archiveUrl}]({$archiveUrl})";
                }
                $lines[] = $line;
            }
            $lines[] = '';
        }

        // Publishing principles (E-E-A-T signals)
        $principles = array_filter([
            'Publishing Principles' => $this->resolvePolicyUrl((int) setting('seo_policy_publishing_principles', 0)),
            'Ownership & Funding' => $this->resolvePolicyUrl((int) setting('seo_policy_ownership_funding', 0)),
            'Corrections Policy' => $this->resolvePolicyUrl((int) setting('seo_policy_corrections', 0)),
            'Ethics Policy' => $this->resolvePolicyUrl((int) setting('seo_policy_ethics', 0)),
            'Diversity Policy' => $this->resolvePolicyUrl((int) setting('seo_policy_diversity', 0)),
        ]);
        if (! empty($principles)) {
            $lines[] = '## Editorial Policies';
            foreach ($principles as $label => $url) {
                $lines[] = "- [{$label}]({$url})";
            }
            $lines[] = '';
        }

        // Sitemap & Technical
        if (setting('seo_sitemap_enabled', true)) {
            $lines[] = '## Technical';
            $lines[] = '- Sitemap: '.url('/sitemap.xml');
            $lines[] = '- Robots: '.url('/robots.txt');
            $lines[] = '';
        }

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    protected function resolvePolicyUrl(int $pageId): ?string
    {
        if ($pageId <= 0) {
            return null;
        }

        $page = Page::find($pageId);

        return $page ? $page->getUrl() : null;
    }
}
