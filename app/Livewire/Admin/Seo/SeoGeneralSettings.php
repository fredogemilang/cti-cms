<?php

namespace App\Livewire\Admin\Seo;

use App\Models\Page;
use App\Models\Setting;
use App\Services\ContentTypeRegistry;
use App\Services\IndexNowService;
use App\Services\TaxonomyRegistry;
use Livewire\Attributes\Url;
use Livewire\Component;

class SeoGeneralSettings extends Component
{
    #[Url(as: 'tab', keep: true)]
    public string $activeSection = 'site-basics';

    public string $searchQuery = '';

    // Features Toggles
    public bool $allowIndexing = true;

    public bool $sitemapEnabled = true;

    public bool $llmsEnabled = true;

    public bool $openGraphEnabled = true;

    public bool $schemaEnabled = true;

    public bool $readabilityAnalysis = true;

    public bool $inclusiveLanguage = false;

    public bool $cornerstoneContent = true;

    // Site Basics & Info
    public string $siteName = '';

    public string $siteAlternateName = '';

    public string $siteTagline = '';

    public string $titleSeparator = '-';

    public string $titlePattern = '{page} {sep} {site}';

    public string $defaultDescription = '';

    public string $defaultOgImage = '';

    // Preferences
    public bool $restrictAdvancedSettings = true;

    // E-E-A-T Site Policies (Page Selectors)
    public int $publishingPrinciplesPageId = 0;

    public int $ownershipFundingPageId = 0;

    public int $correctionsPolicyPageId = 0;

    public int $ethicsPolicyPageId = 0;

    public int $diversityPolicyPageId = 0;

    // Organization Schema & Site Representation
    public string $orgType = 'Organization';

    public string $orgName = '';

    public string $orgAlternateName = '';

    public string $orgLogo = '';

    public string $orgDescription = '';

    public string $orgEmail = '';

    public string $orgPhone = '';

    public string $orgLegalName = '';

    // Social Profiles (Other profiles for Knowledge Graph)
    public string $facebookUrl = '';

    public string $twitterHandle = '';

    public string $linkedinUrl = '';

    public string $instagramUrl = '';

    public string $youtubeUrl = '';

    public string $wikipediaUrl = '';

    // Content Types Dynamic Settings (Pages, Posts, CPTs)
    public array $contentTypeSettings = [];

    // Taxonomies Dynamic Settings (Categories, Tags, Custom Taxonomies)
    public array $taxonomySettings = [];

    // Breadcrumbs Settings (Yoast 2026 Layout)
    public bool $breadcrumbsEnabled = true;

    public string $breadcrumbSeparator = '/';

    public string $breadcrumbHomeText = 'Home';

    public string $breadcrumbPrefix = '';

    public bool $breadcrumbBoldLast = true;

    public string $breadcrumbPostTaxonomy = 'categories';

    // Site Connections (Webmaster Verification Tools)
    public string $defaultLocale = 'en_US';

    public string $googleVerification = '';

    public string $bingVerification = '';

    public string $baiduVerification = '';

    public string $pinterestVerification = '';

    public string $yandexVerification = '';

    public string $ahrefsVerification = '';

    // Google Site Kit Integration Settings
    public bool $gskEnabled = true;

    public string $gskGa4TagId = '';

    public string $gskGtmId = '';

    public string $gskAdsId = '';

    public string $gskPagespeedApiKey = '';

    // GEO & AI
    public string $aiSummary = '';

    public string $editorialPolicyUrl = '';

    public string $transparencyPolicyUrl = '';

    // Advanced & Tools
    public string $robotsExtra = '';

    public string $indexNowKey = '';

    public bool $indexNowEnabled = true;

    public bool $indexNowAutoPing = true;

    public string $manualUrlsInput = '';

    // Media Picker Modal State
    public bool $showMediaPicker = false;

    public ?string $mediaTargetType = null;

    protected $listeners = ['media-picker-selected' => 'onMediaSelected'];

    public function mount(IndexNowService $indexNowService): void
    {
        $this->allowIndexing = (bool) setting('seo_allow_indexing', true);
        $this->sitemapEnabled = (bool) setting('seo_sitemap_enabled', true);
        $this->llmsEnabled = (bool) setting('seo_llms_enabled', true);
        $this->openGraphEnabled = (bool) setting('seo_opengraph_enabled', true);
        $this->schemaEnabled = (bool) setting('seo_schema_enabled', true);
        $this->readabilityAnalysis = (bool) setting('seo_readability_analysis', true);
        $this->inclusiveLanguage = (bool) setting('seo_inclusive_language', false);
        $this->cornerstoneContent = (bool) setting('seo_cornerstone_content', true);

        $this->siteName = (string) setting('site_name', config('app.name', 'CTI CMS'));
        $this->siteAlternateName = (string) setting('seo_site_alternate_name', '');
        $this->siteTagline = (string) setting('site_tagline', '');
        $this->titleSeparator = (string) setting('seo_title_separator', '-');
        $this->titlePattern = (string) setting('seo_title_pattern', '{page} {sep} {site}');
        $this->defaultDescription = (string) setting('seo_default_description', '');
        $this->defaultOgImage = (string) setting('seo_default_og_image', '');

        $this->restrictAdvancedSettings = (bool) setting('seo_restrict_advanced_settings', true);

        $this->publishingPrinciplesPageId = (int) setting('seo_policy_publishing_principles', 0);
        $this->ownershipFundingPageId = (int) setting('seo_policy_ownership_funding', 0);
        $this->correctionsPolicyPageId = (int) setting('seo_policy_corrections', 0);
        $this->ethicsPolicyPageId = (int) setting('seo_policy_ethics', 0);
        $this->diversityPolicyPageId = (int) setting('seo_policy_diversity', 0);

        $this->orgType = (string) setting('seo_org_type', 'Organization');
        $this->orgName = (string) setting('seo_org_name', '');
        $this->orgAlternateName = (string) setting('seo_org_alternate_name', '');
        $this->orgLogo = (string) setting('seo_org_logo', '');
        $this->orgDescription = (string) setting('seo_org_description', '');
        $this->orgEmail = (string) setting('seo_org_email', '');
        $this->orgPhone = (string) setting('seo_org_phone', '');
        $this->orgLegalName = (string) setting('seo_org_legal_name', '');

        $this->facebookUrl = (string) setting('seo_facebook_url', '');
        $this->twitterHandle = (string) setting('seo_twitter_handle', '');
        $this->linkedinUrl = (string) setting('seo_linkedin_url', '');
        $this->instagramUrl = (string) setting('seo_instagram_url', '');
        $this->youtubeUrl = (string) setting('seo_youtube_url', '');
        $this->wikipediaUrl = (string) setting('seo_wikipedia_url', '');

        // Load all registered Content Types (Pages, Posts, CPTs)
        /** @var ContentTypeRegistry $ctRegistry */
        $ctRegistry = app(ContentTypeRegistry::class);
        foreach ($ctRegistry->all() as $slug => $typeConfig) {
            $legacyTitlePattern = $slug === 'posts'
                ? setting('seo_post_title_pattern')
                : ($slug === 'pages' ? setting('seo_page_title_pattern') : null);

            $this->contentTypeSettings[$slug] = [
                'index_enabled' => (bool) setting("seo_content_type_{$slug}_index_enabled", true),
                'title_pattern' => (string) setting(
                    "seo_content_type_{$slug}_title_pattern",
                    $legacyTitlePattern ?? $typeConfig['default_title_pattern']
                ),
                'description_pattern' => (string) setting("seo_content_type_{$slug}_description_pattern", ''),
                'schema_default' => (string) setting(
                    "seo_content_type_{$slug}_schema_default",
                    $typeConfig['default_schema_type']
                ),
                'social_image' => (string) setting("seo_content_type_{$slug}_social_image", ''),
            ];
        }

        // Load all registered Taxonomies (Categories, Tags, Custom Taxonomies)
        /** @var TaxonomyRegistry $taxRegistry */
        $taxRegistry = app(TaxonomyRegistry::class);
        foreach ($taxRegistry->all() as $slug => $taxConfig) {
            $this->taxonomySettings[$slug] = [
                'index_enabled' => (bool) setting("seo_taxonomy_{$slug}_index_enabled", true),
                'title_pattern' => (string) setting(
                    "seo_taxonomy_{$slug}_title_pattern",
                    $taxConfig['default_title_pattern']
                ),
                'description_pattern' => (string) setting("seo_taxonomy_{$slug}_description_pattern", ''),
                'schema_default' => (string) setting(
                    "seo_taxonomy_{$slug}_schema_default",
                    $taxConfig['default_schema_type']
                ),
                'social_image' => (string) setting("seo_taxonomy_{$slug}_social_image", ''),
            ];
        }

        // Load Breadcrumb Settings
        $this->breadcrumbsEnabled = (bool) setting('seo_breadcrumbs_enabled', true);
        $this->breadcrumbSeparator = (string) setting('seo_breadcrumb_separator', '/');
        $this->breadcrumbHomeText = (string) setting('seo_breadcrumb_home_text', 'Home');
        $this->breadcrumbPrefix = (string) setting('seo_breadcrumb_prefix', '');
        $this->breadcrumbBoldLast = (bool) setting('seo_breadcrumb_bold_last', true);
        $this->breadcrumbPostTaxonomy = (string) setting('seo_breadcrumb_post_taxonomy', 'categories');

        $this->defaultLocale = (string) setting('seo_default_locale', 'en_US');
        $this->googleVerification = (string) setting('seo_google_verification', '');
        $this->bingVerification = (string) setting('seo_bing_verification', '');
        $this->baiduVerification = (string) setting('seo_baidu_verification', '');
        $this->pinterestVerification = (string) setting('seo_pinterest_verification', '');
        $this->yandexVerification = (string) setting('seo_yandex_verification', '');
        $this->ahrefsVerification = (string) setting('seo_ahrefs_verification', '');

        $this->gskEnabled = (bool) setting('gsk_enabled', true);
        $this->gskGa4TagId = (string) setting('gsk_ga4_tag_id', '');
        $this->gskGtmId = (string) setting('gsk_gtm_id', '');
        $this->gskAdsId = (string) setting('gsk_ads_id', '');
        $this->gskPagespeedApiKey = (string) setting('gsk_pagespeed_api_key', '');

        $this->aiSummary = (string) setting('seo_ai_summary', '');
        $this->editorialPolicyUrl = (string) setting('seo_editorial_policy_url', '');
        $this->transparencyPolicyUrl = (string) setting('seo_transparency_policy_url', '');

        $this->robotsExtra = (string) setting('seo_robots_extra', '');
        $this->indexNowKey = $indexNowService->getKey();
        $this->indexNowEnabled = (bool) setting('seo_indexnow_enabled', true);
        $this->indexNowAutoPing = (bool) setting('seo_indexnow_auto_ping', true);
    }

    public function setSection(string $section): void
    {
        $this->activeSection = $section;
    }

    public function selectSeparator(string $sep): void
    {
        $this->titleSeparator = $sep;
    }

    public function selectBreadcrumbSeparator(string $sep): void
    {
        $this->breadcrumbSeparator = $sep;
    }

    public function regenerateIndexNowKey(IndexNowService $indexNowService): void
    {
        $this->indexNowKey = $indexNowService->generateKey();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'IndexNow API Key regenerated successfully!',
        ]);
    }

    public function submitManualUrls(IndexNowService $indexNowService): void
    {
        $urls = preg_split('/\r\n|\r|\n/', $this->manualUrlsInput);
        if (! $urls) {
            return;
        }

        $success = $indexNowService->submitUrls($urls);

        if ($success) {
            $this->manualUrlsInput = '';
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'URLs submitted to IndexNow API successfully!',
            ]);
        } else {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to submit URLs to IndexNow API. Please check URL formats or try again later.',
            ]);
        }
    }

    public function insertSnippetVariable(string $slug, string $field, string $variable): void
    {
        if (isset($this->contentTypeSettings[$slug][$field])) {
            $this->contentTypeSettings[$slug][$field] .= ' '.$variable;
            $this->contentTypeSettings[$slug][$field] = trim($this->contentTypeSettings[$slug][$field]);
        }
    }

    public function insertTaxonomySnippetVariable(string $slug, string $field, string $variable): void
    {
        if (isset($this->taxonomySettings[$slug][$field])) {
            $this->taxonomySettings[$slug][$field] .= ' '.$variable;
            $this->taxonomySettings[$slug][$field] = trim($this->taxonomySettings[$slug][$field]);
        }
    }

    public function openMediaPicker(?string $targetType = null): void
    {
        $this->mediaTargetType = $targetType;
        $this->dispatch('open-media-picker', ['targetField' => $targetType ?? 'defaultOgImage']);
    }

    public function onMediaSelected(array $payload): void
    {
        if (isset($payload['url'])) {
            if ($this->mediaTargetType && str_starts_with($this->mediaTargetType, 'content_type_')) {
                $slug = str_replace('content_type_', '', $this->mediaTargetType);
                if (isset($this->contentTypeSettings[$slug])) {
                    $this->contentTypeSettings[$slug]['social_image'] = $payload['url'];
                }
            } elseif ($this->mediaTargetType && str_starts_with($this->mediaTargetType, 'taxonomy_')) {
                $slug = str_replace('taxonomy_', '', $this->mediaTargetType);
                if (isset($this->taxonomySettings[$slug])) {
                    $this->taxonomySettings[$slug]['social_image'] = $payload['url'];
                }
            } else {
                $this->defaultOgImage = $payload['url'];
            }
        }
        $this->mediaTargetType = null;
    }

    public function removeOgImage(): void
    {
        $this->defaultOgImage = '';
    }

    public function removeContentTypeSocialImage(string $slug): void
    {
        if (isset($this->contentTypeSettings[$slug])) {
            $this->contentTypeSettings[$slug]['social_image'] = '';
        }
    }

    public function removeTaxonomySocialImage(string $slug): void
    {
        if (isset($this->taxonomySettings[$slug])) {
            $this->taxonomySettings[$slug]['social_image'] = '';
        }
    }

    public function save(): void
    {
        $this->validate([
            'siteName' => 'required|string|max:200',
            'siteAlternateName' => 'nullable|string|max:200',
            'siteTagline' => 'nullable|string|max:250',
            'titleSeparator' => 'required|string|max:10',
            'titlePattern' => 'required|string|max:200',
            'defaultDescription' => 'nullable|string|max:300',
            'defaultOgImage' => 'nullable|string|max:500',
            'breadcrumbSeparator' => 'required|string|max:10',
            'breadcrumbHomeText' => 'required|string|max:100',
            'breadcrumbPrefix' => 'nullable|string|max:100',
            'twitterHandle' => 'nullable|string|max:50',
            'facebookUrl' => 'nullable|url',
            'linkedinUrl' => 'nullable|url',
            'instagramUrl' => 'nullable|url',
            'youtubeUrl' => 'nullable|url',
            'wikipediaUrl' => 'nullable|url',
            'googleVerification' => 'nullable|string|max:200',
            'bingVerification' => 'nullable|string|max:200',
            'baiduVerification' => 'nullable|string|max:200',
            'pinterestVerification' => 'nullable|string|max:200',
            'yandexVerification' => 'nullable|string|max:200',
            'ahrefsVerification' => 'nullable|string|max:200',
            'gskGa4TagId' => 'nullable|string|max:50',
            'gskGtmId' => 'nullable|string|max:50',
            'gskAdsId' => 'nullable|string|max:50',
            'gskPagespeedApiKey' => 'nullable|string|max:200',
            'orgName' => 'nullable|string|max:200',
            'orgAlternateName' => 'nullable|string|max:200',
            'orgLogo' => 'nullable|string|max:500',
            'orgEmail' => 'nullable|email|max:200',
            'orgPhone' => 'nullable|string|max:30',
        ]);

        Setting::set('site_name', $this->siteName, 'general', 'text');
        Setting::set('seo_site_alternate_name', $this->siteAlternateName, 'seo', 'text');
        Setting::set('site_tagline', $this->siteTagline, 'general', 'text');
        Setting::set('seo_title_separator', $this->titleSeparator, 'seo', 'text');
        Setting::set('seo_title_pattern', $this->titlePattern, 'seo', 'text');
        Setting::set('seo_default_description', $this->defaultDescription, 'seo', 'textarea');
        Setting::set('seo_default_og_image', $this->defaultOgImage, 'seo', 'text');

        Setting::set('seo_allow_indexing', $this->allowIndexing, 'seo', 'boolean');
        Setting::set('seo_sitemap_enabled', $this->sitemapEnabled, 'seo', 'boolean');
        Setting::set('seo_llms_enabled', $this->llmsEnabled, 'seo', 'boolean');
        Setting::set('seo_opengraph_enabled', $this->openGraphEnabled, 'seo', 'boolean');
        Setting::set('seo_schema_enabled', $this->schemaEnabled, 'seo', 'boolean');
        Setting::set('seo_readability_analysis', $this->readabilityAnalysis, 'seo', 'boolean');
        Setting::set('seo_inclusive_language', $this->inclusiveLanguage, 'seo', 'boolean');
        Setting::set('seo_cornerstone_content', $this->cornerstoneContent, 'seo', 'boolean');

        Setting::set('seo_restrict_advanced_settings', $this->restrictAdvancedSettings, 'seo', 'boolean');

        Setting::set('seo_policy_publishing_principles', $this->publishingPrinciplesPageId, 'seo', 'integer');
        Setting::set('seo_policy_ownership_funding', $this->ownershipFundingPageId, 'seo', 'integer');
        Setting::set('seo_policy_corrections', $this->correctionsPolicyPageId, 'seo', 'integer');
        Setting::set('seo_policy_ethics', $this->ethicsPolicyPageId, 'seo', 'integer');
        Setting::set('seo_policy_diversity', $this->diversityPolicyPageId, 'seo', 'integer');

        Setting::set('seo_org_type', $this->orgType, 'seo', 'select');
        Setting::set('seo_org_name', $this->orgName, 'seo', 'text');
        Setting::set('seo_org_alternate_name', $this->orgAlternateName, 'seo', 'text');
        Setting::set('seo_org_logo', $this->orgLogo, 'seo', 'text');
        Setting::set('seo_org_description', $this->orgDescription, 'seo', 'textarea');
        Setting::set('seo_org_email', $this->orgEmail, 'seo', 'email');
        Setting::set('seo_org_phone', $this->orgPhone, 'seo', 'text');
        Setting::set('seo_org_legal_name', $this->orgLegalName, 'seo', 'text');

        Setting::set('seo_facebook_url', $this->facebookUrl, 'seo', 'text');
        Setting::set('seo_twitter_handle', $this->twitterHandle, 'seo', 'text');
        Setting::set('seo_linkedin_url', $this->linkedinUrl, 'seo', 'text');
        Setting::set('seo_instagram_url', $this->instagramUrl, 'seo', 'text');
        Setting::set('seo_youtube_url', $this->youtubeUrl, 'seo', 'text');
        Setting::set('seo_wikipedia_url', $this->wikipediaUrl, 'seo', 'text');

        // Save per-Content-Type Settings (Pages, Posts, CPTs)
        foreach ($this->contentTypeSettings as $slug => $data) {
            Setting::set("seo_content_type_{$slug}_index_enabled", (bool) ($data['index_enabled'] ?? true), 'seo', 'boolean');
            Setting::set("seo_content_type_{$slug}_title_pattern", (string) ($data['title_pattern'] ?? '{title} {sep} {site}'), 'seo', 'text');
            Setting::set("seo_content_type_{$slug}_description_pattern", (string) ($data['description_pattern'] ?? ''), 'seo', 'textarea');
            Setting::set("seo_content_type_{$slug}_schema_default", (string) ($data['schema_default'] ?? 'WebPage'), 'seo', 'text');
            Setting::set("seo_content_type_{$slug}_social_image", (string) ($data['social_image'] ?? ''), 'seo', 'text');

            if ($slug === 'posts') {
                Setting::set('seo_post_title_pattern', (string) ($data['title_pattern'] ?? '{title} {sep} {site}'), 'seo', 'text');
            } elseif ($slug === 'pages') {
                Setting::set('seo_page_title_pattern', (string) ($data['title_pattern'] ?? '{title} {sep} {site}'), 'seo', 'text');
            }
        }

        // Save per-Taxonomy Settings (Categories, Tags, Custom Taxonomies)
        foreach ($this->taxonomySettings as $slug => $data) {
            Setting::set("seo_taxonomy_{$slug}_index_enabled", (bool) ($data['index_enabled'] ?? true), 'seo', 'boolean');
            Setting::set("seo_taxonomy_{$slug}_title_pattern", (string) ($data['title_pattern'] ?? '{term} Archives {sep} {site}'), 'seo', 'text');
            Setting::set("seo_taxonomy_{$slug}_description_pattern", (string) ($data['description_pattern'] ?? ''), 'seo', 'textarea');
            Setting::set("seo_taxonomy_{$slug}_schema_default", (string) ($data['schema_default'] ?? 'CollectionPage'), 'seo', 'text');
            Setting::set("seo_taxonomy_{$slug}_social_image", (string) ($data['social_image'] ?? ''), 'seo', 'text');
        }

        // Save Breadcrumb Settings
        Setting::set('seo_breadcrumbs_enabled', $this->breadcrumbsEnabled, 'seo', 'boolean');
        Setting::set('seo_breadcrumb_separator', $this->breadcrumbSeparator, 'seo', 'text');
        Setting::set('seo_breadcrumb_home_text', $this->breadcrumbHomeText, 'seo', 'text');
        Setting::set('seo_breadcrumb_prefix', $this->breadcrumbPrefix, 'seo', 'text');
        Setting::set('seo_breadcrumb_bold_last', $this->breadcrumbBoldLast, 'seo', 'boolean');
        Setting::set('seo_breadcrumb_post_taxonomy', $this->breadcrumbPostTaxonomy, 'seo', 'select');

        Setting::set('seo_default_locale', $this->defaultLocale, 'seo', 'select');
        Setting::set('seo_google_verification', $this->googleVerification, 'seo', 'text');
        Setting::set('seo_bing_verification', $this->bingVerification, 'seo', 'text');
        Setting::set('seo_baidu_verification', $this->baiduVerification, 'seo', 'text');
        Setting::set('seo_pinterest_verification', $this->pinterestVerification, 'seo', 'text');
        Setting::set('seo_yandex_verification', $this->yandexVerification, 'seo', 'text');
        Setting::set('seo_ahrefs_verification', $this->ahrefsVerification, 'seo', 'text');

        Setting::set('gsk_enabled', $this->gskEnabled, 'google-site-kit', 'boolean');
        Setting::set('gsk_ga4_tag_id', $this->gskGa4TagId, 'google-site-kit', 'text');
        Setting::set('gsk_gtm_id', $this->gskGtmId, 'google-site-kit', 'text');
        Setting::set('gsk_ads_id', $this->gskAdsId, 'google-site-kit', 'text');
        Setting::set('gsk_pagespeed_api_key', $this->gskPagespeedApiKey, 'google-site-kit', 'text');

        Setting::set('seo_ai_summary', $this->aiSummary, 'seo', 'textarea');
        Setting::set('seo_editorial_policy_url', $this->editorialPolicyUrl, 'seo', 'url');
        Setting::set('seo_transparency_policy_url', $this->transparencyPolicyUrl, 'seo', 'url');

        Setting::set('seo_robots_extra', $this->robotsExtra, 'seo', 'code');
        Setting::set('seo_indexnow_key', $this->indexNowKey, 'seo', 'text');
        Setting::set('seo_indexnow_enabled', $this->indexNowEnabled, 'seo', 'boolean');
        Setting::set('seo_indexnow_auto_ping', $this->indexNowAutoPing, 'seo', 'boolean');

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'SEO Settings saved successfully!',
        ]);
    }

    public function render()
    {
        $publishedPages = Page::query()->orderBy('title')->get(['id', 'title', 'slug']);
        /** @var ContentTypeRegistry $ctRegistry */
        $ctRegistry = app(ContentTypeRegistry::class);
        $contentTypes = $ctRegistry->all();

        /** @var TaxonomyRegistry $taxRegistry */
        $taxRegistry = app(TaxonomyRegistry::class);
        $taxonomies = $taxRegistry->all();

        return view('livewire.admin.seo.seo-general-settings', [
            'publishedPages' => $publishedPages,
            'contentTypes' => $contentTypes,
            'taxonomies' => $taxonomies,
            'separators' => ['-', '—', '–', ':', '·', '•', '*', '|', '~', '«', '»', '<', '>'],
            'breadcrumbSeparators' => ['/', '»', '>', '·', '•', '-', '|', '→'],
            'schemaTypes' => [
                'WebPage' => 'Web Page (default)',
                'Article' => 'Article (General)',
                'BlogPosting' => 'Blog Post',
                'NewsArticle' => 'News Article',
                'TechArticle' => 'Tech Article',
                'AboutPage' => 'About Page',
                'ContactPage' => 'Contact Page',
                'FAQPage' => 'FAQ Page',
                'ItemPage' => 'Item / Product Page',
                'CollectionPage' => 'Collection Page (Taxonomy Archive)',
                'ItemList' => 'Item List',
            ],
        ])->layout('layouts.admin', [
            'title' => 'SEO Settings',
        ]);
    }
}
