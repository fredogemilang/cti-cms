<?php

namespace App\Http\Middleware;

use App\Models\Page;
use App\Services\BreadcrumbService;
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
        $seo = $this->seoRenderer->resolve($entity);

        // Replace existing <title> tag with the resolved SEO title (pattern + separator)
        if (preg_match('/<title\b[^>]*>(.*?)<\/title>/is', $content)) {
            $content = (string) preg_replace('/<title\b[^>]*>(.*?)<\/title>/is', '<title>'.e($seo['title']).'</title>', $content);
        }

        // Build SEO tags
        $seoHtml = $this->buildSeoHtml($seo, $entity);

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
    protected function buildSeoHtml(array $seo, ?Model $entity = null): string
    {
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
        if (setting('seo_opengraph_enabled', true)) {
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
            if ($twHandle = setting('seo_twitter_handle')) {
                $lines[] = '<meta name="twitter:site" content="'.e($twHandle).'">';
            }
        }

        // Site Connections / Webmaster verification (site-wide)
        if ($gsc = setting('seo_google_verification')) {
            $lines[] = '<meta name="google-site-verification" content="'.e($gsc).'">';
        }
        if ($bing = setting('seo_bing_verification')) {
            $lines[] = '<meta name="msvalidate.01" content="'.e($bing).'">';
        }
        if ($baidu = setting('seo_baidu_verification')) {
            $lines[] = '<meta name="baidu-site-verification" content="'.e($baidu).'">';
        }
        if ($pinterest = setting('seo_pinterest_verification')) {
            $lines[] = '<meta name="p:domain_verify" content="'.e($pinterest).'">';
        }
        if ($yandex = setting('seo_yandex_verification')) {
            $lines[] = '<meta name="yandex-verification" content="'.e($yandex).'">';
        }
        if ($ahrefs = setting('seo_ahrefs_verification')) {
            $lines[] = '<meta name="ahrefs-site-verification" content="'.e($ahrefs).'">';
        }
        // Google Site Kit Tracking Snippets (GA4, GTM, Ads)
        if (setting('gsk_enabled', true)) {
            if ($ga4Id = setting('gsk_ga4_tag_id')) {
                $lines[] = '<!-- Google Analytics (gtag.js) -->';
                $lines[] = '<script async src="https://www.googletagmanager.com/gtag/js?id='.e($ga4Id).'"></script>';
                $lines[] = '<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config","'.e($ga4Id).'");</script>';
            }
            if ($gtmId = setting('gsk_gtm_id')) {
                $lines[] = '<!-- Google Tag Manager -->';
                $lines[] = '<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({"gtm.start":new Date().getTime(),event:"gtm.js"});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!="dataLayer"?"&l="+l:"";j.async=true;j.src="https://www.googletagmanager.com/gtm.js?id="+i+dl;f.parentNode.insertBefore(j,f);})(window,document,"script","dataLayer","'.e($gtmId).'");</script>';
            }
            if ($adsId = setting('gsk_ads_id')) {
                $lines[] = '<!-- Google Ads -->';
                $lines[] = '<script async src="https://www.googletagmanager.com/gtag/js?id='.e($adsId).'"></script>';
                $lines[] = '<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config","'.e($adsId).'");</script>';
            }
        }

        // JSON-LD Schema (site-wide toggle)
        if (setting('seo_schema_enabled', true)) {
            // Per-entity schema
            if (! empty($seo['schema'])) {
                $lines[] = '<script type="application/ld+json">'.json_encode($seo['schema'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).'</script>';
            }

            // WebSite schema
            $websiteSchema = $this->buildWebSiteSchema();
            if ($websiteSchema) {
                $lines[] = '<script type="application/ld+json">'.json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).'</script>';
            }

            // Organization schema
            $orgSchema = $this->buildOrganizationSchema();
            if ($orgSchema) {
                $lines[] = '<script type="application/ld+json">'.json_encode($orgSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).'</script>';
            }

            // BreadcrumbList schema (auto-injected for Google Rich Snippets)
            if (setting('seo_breadcrumbs_enabled', true)) {
                /** @var BreadcrumbService $bcService */
                $bcService = app(BreadcrumbService::class);
                $bcItems = $bcService->getItems($entity);
                $bcSchema = $this->schemaBuilder->breadcrumbList($bcItems);
                $lines[] = '<script type="application/ld+json">'.json_encode($bcSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).'</script>';
            }
        }

        $lines[] = '<!-- /CMS SEO/GEO -->';

        return implode("\n    ", $lines);
    }

    /**
     * Build the WebSite JSON-LD schema with alternateName & Sitelinks Search Box.
     */
    protected function buildWebSiteSchema(): ?array
    {
        $siteName = (string) setting('site_name', config('app.name'));
        $altName = (string) setting('seo_site_alternate_name', '');

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => url('/#website'),
            'url' => url('/'),
            'name' => $siteName,
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => url('/search?q={search_term_string}'),
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];

        if ($altName !== '') {
            $schema['alternateName'] = $altName;
        }

        return $schema;
    }

    /**
     * Build the enriched Organization JSON-LD schema with
     * Yoast-inspired fields + Publishing Principles for GEO.
     */
    protected function buildOrganizationSchema(): ?array
    {
        $siteName = (string) setting('site_name', config('app.name'));
        $altName = (string) setting('seo_site_alternate_name', '');

        $orgType = (string) setting('seo_org_type', 'Organization');
        $orgName = (string) setting('seo_org_name', '') ?: $siteName;
        $orgAltName = (string) setting('seo_org_alternate_name', '') ?: $altName;

        $logo = setting('seo_org_logo') ? url((string) setting('seo_org_logo')) : (setting('site_logo') ? url((string) setting('site_logo')) : (setting('seo_default_og_image') ? url((string) setting('seo_default_og_image')) : null));

        $schema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => $orgType,
            'name' => $orgName,
            'url' => url('/'),
            'logo' => $logo,

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
                ($tw = setting('seo_twitter_handle')) ? (str_starts_with((string) $tw, 'http') ? $tw : "https://x.com/{$tw}") : null,
                setting('seo_linkedin_url') ?: null,
                setting('seo_instagram_url') ?: null,
                setting('seo_youtube_url') ?: null,
                setting('seo_wikipedia_url') ?: null,
            ])),

            // Publishing Principles (GEO/E-E-A-T)
            'publishingPrinciples' => $this->resolvePolicyUrl((int) setting('seo_policy_publishing_principles')),
            'correctionsPolicy' => $this->resolvePolicyUrl((int) setting('seo_policy_corrections')),
            'ethicsPolicy' => $this->resolvePolicyUrl((int) setting('seo_policy_ethics')),
            'diversityPolicy' => $this->resolvePolicyUrl((int) setting('seo_policy_diversity')),
            'ownershipFundingInfo' => $this->resolvePolicyUrl((int) setting('seo_policy_ownership_funding')),
        ], fn ($v) => $v !== null && $v !== '' && $v !== []);

        if ($orgAltName !== '') {
            $schema['alternateName'] = $orgAltName;
        }

        // numberOfEmployees as QuantitativeValue (Yoast pattern)
        $employees = setting('seo_org_employees');
        if ($employees) {
            $range = explode('-', (string) $employees);
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

    protected function resolvePolicyUrl(int $pageId): ?string
    {
        if ($pageId <= 0) {
            return null;
        }

        $page = Page::find($pageId);

        return $page ? $page->getUrl() : null;
    }
}
