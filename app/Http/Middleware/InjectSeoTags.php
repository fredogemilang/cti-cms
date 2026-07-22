<?php

namespace App\Http\Middleware;

use App\Services\SchemaBuilder;
use App\Services\SeoRenderer;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Auto-inject SEO meta tags, Open Graph, Twitter Cards, and JSON-LD
 * into every public HTML response's <head>, regardless of which theme
 * is active. This ensures GEO/SEO works across ALL themes without
 * requiring theme developers to manually include SEO components.
 *
 * The middleware detects the entity ($page, $entry) from the response's
 * view data and uses SeoRenderer to build the full SEO payload.
 */
class InjectSeoTags
{
    public function __construct(
        protected SeoRenderer $seoRenderer,
        protected SchemaBuilder $schemaBuilder,
    ) {}

    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        // Only process successful HTML responses (not redirects, JSON, admin, etc.)
        if (! $response instanceof Response) {
            return $response;
        }
        if ($response->getStatusCode() !== 200) {
            return $response;
        }
        $contentType = $response->headers->get('Content-Type', '');
        if (! str_contains($contentType, 'text/html') && $contentType !== '') {
            return $response;
        }

        // Skip admin routes
        $adminPath = config('admin.path', 'admin');
        if (str_starts_with(ltrim($request->path(), '/'), $adminPath)) {
            return $response;
        }

        $content = $response->getContent();
        if (! $content || ! str_contains($content, '</head>')) {
            return $response;
        }

        // Extract the entity from the view response (if available)
        $entity = $this->resolveEntity($response);

        // Build SEO tags
        $seoHtml = $this->buildSeoHtml($entity);

        // Inject just before </head>, after any existing @stack('meta') content
        $content = str_replace('</head>', $seoHtml."\n</head>", $content);

        $response->setContent($content);

        return $response;
    }

    /**
     * Try to extract the primary entity (Page, CptEntry, etc.)
     * from the response's view data, so we can resolve per-page SEO.
     */
    protected function resolveEntity(Response $response): ?Model
    {
        $original = $response->getOriginalContent();

        if (! $original || ! method_exists($original, 'getData')) {
            return null;
        }

        $data = $original->getData();

        // Priority: $page > $entry > $post > $event
        foreach (['page', 'entry', 'post', 'event'] as $key) {
            if (isset($data[$key]) && $data[$key] instanceof Model) {
                return $data[$key];
            }
        }

        return null;
    }

    /**
     * Build the full SEO/GEO HTML string to inject.
     */
    protected function buildSeoHtml(?Model $entity): string
    {
        $seo = $this->seoRenderer->resolve($entity);
        $lines = [];

        $lines[] = '<!-- CMS SEO/GEO: auto-injected -->';

        // Meta description
        if ($seo['description']) {
            $lines[] = '<meta name="description" content="'.e($seo['description']).'">';
        }

        // Robots
        if ($seo['robots'] !== 'index,follow') {
            $lines[] = '<meta name="robots" content="'.e($seo['robots']).'">';
        }

        // Canonical
        if ($seo['canonical']) {
            $lines[] = '<link rel="canonical" href="'.e($seo['canonical']).'">';
        }

        // Open Graph
        $lines[] = '<meta property="og:type" content="'.e($seo['og']['type']).'">';
        $lines[] = '<meta property="og:title" content="'.e($seo['og']['title']).'">';
        if ($seo['og']['description']) {
            $lines[] = '<meta property="og:description" content="'.e($seo['og']['description']).'">';
        }
        if ($seo['og']['image']) {
            $lines[] = '<meta property="og:image" content="'.e($seo['og']['image']).'">';
        }
        if ($seo['og']['url']) {
            $lines[] = '<meta property="og:url" content="'.e($seo['og']['url']).'">';
        }
        $lines[] = '<meta property="og:site_name" content="'.e($seo['og']['site_name']).'">';

        // Twitter Card
        $lines[] = '<meta name="twitter:card" content="'.e($seo['twitter']['card']).'">';
        $lines[] = '<meta name="twitter:title" content="'.e($seo['twitter']['title']).'">';
        if ($seo['twitter']['description']) {
            $lines[] = '<meta name="twitter:description" content="'.e($seo['twitter']['description']).'">';
        }
        if ($seo['twitter']['image']) {
            $lines[] = '<meta name="twitter:image" content="'.e($seo['twitter']['image']).'">';
        }

        // Google / Bing verification (site-wide)
        if ($gsc = setting('seo_google_verification')) {
            $lines[] = '<meta name="google-site-verification" content="'.e($gsc).'">';
        }
        if ($bing = setting('seo_bing_verification')) {
            $lines[] = '<meta name="msvalidate.01" content="'.e($bing).'">';
        }

        // JSON-LD Schema (per-entity)
        if (! empty($seo['schema'])) {
            $lines[] = '<script type="application/ld+json">'.json_encode($seo['schema'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).'</script>';
        }

        // JSON-LD Organization schema (site-wide, always present)
        $orgSchema = $this->buildOrganizationSchema();
        if ($orgSchema) {
            $lines[] = '<script type="application/ld+json">'.json_encode($orgSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).'</script>';
        }

        $lines[] = '<!-- /CMS SEO/GEO -->';

        return implode("\n    ", $lines);
    }

    /**
     * Build the enriched Organization JSON-LD schema with
     * Yoast-inspired fields + Publishing Principles for GEO.
     */
    protected function buildOrganizationSchema(): ?array
    {
        $siteName = setting('site_name', config('app.name'));
        $orgName = setting('seo_org_name', '') ?: $siteName;

        $schema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $orgName,
            'url' => url('/'),
            'logo' => setting('seo_org_logo') ? url(setting('seo_org_logo')) : null,

            // Enriched fields (Yoast-inspired)
            'description' => setting('seo_org_description') ?: null,
            'email' => setting('seo_org_email') ?: null,
            'telephone' => setting('seo_org_phone') ?: null,
            'legalName' => setting('seo_org_legal_name') ?: null,
            'foundingDate' => setting('seo_org_founding_date') ?: null,
            'vatID' => setting('seo_org_vat_id') ?: null,
            'taxID' => setting('seo_org_tax_id') ?: null,
            'duns' => setting('seo_org_duns') ?: null,
            'naics' => setting('seo_org_naics') ?: null,

            // Social profiles
            'sameAs' => array_values(array_filter([
                setting('seo_facebook_url') ?: null,
                ($tw = setting('seo_twitter_handle')) ? "https://twitter.com/{$tw}" : null,
            ])),

            // Publishing Principles (GEO/E-E-A-T — Yoast Premium pattern)
            'publishingPrinciples' => setting('seo_publishing_principles_url') ?: null,
            'correctionsPolicy' => setting('seo_corrections_policy_url') ?: null,
            'ethicsPolicy' => setting('seo_ethics_policy_url') ?: null,
            'diversityPolicy' => setting('seo_diversity_policy_url') ?: null,
            'ownershipFundingInfo' => setting('seo_ownership_funding_url') ?: null,
            'actionableFeedbackPolicy' => setting('seo_actionable_feedback_url') ?: null,
        ], fn ($v) => $v !== null && $v !== '' && $v !== []);

        // numberOfEmployees as QuantitativeValue (Yoast pattern)
        $employees = setting('seo_org_employees');
        if ($employees) {
            $range = explode('-', $employees);
            $schema['numberOfEmployees'] = ['@type' => 'QuantitativeValue'];
            if (count($range) === 2) {
                $schema['numberOfEmployees']['minValue'] = trim($range[0]);
                $schema['numberOfEmployees']['maxValue'] = trim($range[1]);
            } else {
                $schema['numberOfEmployees']['value'] = $employees;
            }
        }

        return $schema;
    }
}
