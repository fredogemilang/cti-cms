<?php

namespace App\Providers;

use App\Services\SettingsRegistry;
use App\Settings\Actions\BrevoTestEmailAction;
use Illuminate\Support\ServiceProvider;

/**
 * Registers all core CMS settings groups with the SettingsRegistry.
 *
 * Each group appears as a tab under Admin → Settings.
 * Plugins can register additional groups via their own service providers.
 *
 * Extracted from AppServiceProvider for single-responsibility.
 */
class CmsSettingsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $registry = $this->app->make(SettingsRegistry::class);

        $this->registerGeneralSettings($registry);
        $this->registerContentSettings($registry);
        $this->registerAuthSettings($registry);
        $this->registerSeoSettings($registry);
        $this->registerBrevoSettings($registry);
        $this->registerRedirectSettings($registry);
        $this->registerLanguageSettings($registry);
        $this->registerCacheSettings($registry);
        $this->registerCdnSettings($registry);
        $this->registerImageOptSettings($registry);
        $this->registerPageOptSettings($registry);
        $this->registerApiSettings($registry);
    }

    protected function registerGeneralSettings(SettingsRegistry $registry): void
    {
        $registry->registerGroup('general', [
            'label' => 'General',
            'icon' => 'tune',
            'order' => 10,
            'description' => 'Site identity, regional defaults, and maintenance mode.',
            'fields' => [
                // Identity
                ['key' => 'site_name',     'label' => 'Site Name',     'type' => 'text',     'section' => 'Site Identity', 'order' => 10,
                    'default' => config('app.name', 'Web CMS'),
                    'rules' => ['required', 'string', 'max:120'],
                    'help' => 'Displayed in the browser title and various headings.'],

                ['key' => 'site_tagline',  'label' => 'Tagline',       'type' => 'text',     'section' => 'Site Identity', 'order' => 20,
                    'default' => '',
                    'rules' => ['nullable', 'string', 'max:200']],

                ['key' => 'admin_email',   'label' => 'Admin Email',   'type' => 'email',    'section' => 'Site Identity', 'order' => 30,
                    'default' => '',
                    'rules' => ['nullable', 'email'],
                    'help' => 'Used as the From/Reply-To for outgoing system notifications.'],

                ['key' => 'site_logo',    'label' => 'Site Logo',     'type' => 'media',    'section' => 'Site Identity', 'order' => 32,
                    'default' => '',
                    'rules' => ['nullable', 'string', 'max:500'],
                    'help' => 'Main logo shown in the header/navbar. Recommended: PNG or SVG, max height 60px.'],

                ['key' => 'site_favicon', 'label' => 'Favicon',       'type' => 'media',    'section' => 'Site Identity', 'order' => 34,
                    'default' => '',
                    'rules' => ['nullable', 'string', 'max:500'],
                    'help' => 'Browser tab icon. Recommended: 32×32 or 16×16 PNG, or .ico file.'],

                // Regional
                ['key' => 'timezone',      'label' => 'Timezone',      'type' => 'select',   'section' => 'Regional', 'order' => 40,
                    'default' => 'UTC',
                    'options' => [
                        'UTC' => 'UTC',
                        'Asia/Jakarta' => 'Asia/Jakarta (WIB)',
                        'Asia/Makassar' => 'Asia/Makassar (WITA)',
                        'Asia/Jayapura' => 'Asia/Jayapura (WIT)',
                        'America/New_York' => 'America/New_York (ET)',
                        'America/Chicago' => 'America/Chicago (CT)',
                        'America/Los_Angeles' => 'America/Los_Angeles (PT)',
                        'Europe/London' => 'Europe/London (GMT)',
                        'Europe/Amsterdam' => 'Europe/Amsterdam (CET)',
                        'Asia/Tokyo' => 'Asia/Tokyo (JST)',
                        'Asia/Singapore' => 'Asia/Singapore (SGT)',
                    ],
                    'rules' => ['required', 'timezone']],

                ['key' => 'date_format',   'label' => 'Date Format',   'type' => 'select',   'section' => 'Regional', 'order' => 50,
                    'default' => 'd M Y',
                    'options' => [
                        'd M Y' => date('d M Y'),
                        'd/m/Y' => date('d/m/Y'),
                        'Y-m-d' => date('Y-m-d'),
                        'F j, Y' => date('F j, Y'),
                    ],
                    'rules' => ['required', 'string']],

                ['key' => 'items_per_page', 'label' => 'Items Per Page', 'type' => 'number', 'section' => 'Regional', 'order' => 60,
                    'default' => 10,
                    'rules' => ['required', 'integer', 'min:1', 'max:100']],

                // Maintenance
                ['key' => 'maintenance_mode',    'label' => 'Enable Maintenance Mode', 'type' => 'boolean', 'section' => 'Maintenance', 'order' => 70,
                    'default' => false,
                    'rules' => ['boolean'],
                    'help' => 'When enabled, public visitors see a maintenance page. Admins can still log in.'],

                ['key' => 'maintenance_message', 'label' => 'Maintenance Message',     'type' => 'textarea', 'section' => 'Maintenance', 'order' => 80,
                    'default' => 'We will be back shortly.',
                    'rules' => ['nullable', 'string', 'max:1000']],

                // Audit log
                ['key' => 'activity_retention_days', 'label' => 'Audit Log Retention (days)', 'type' => 'number', 'section' => 'Audit Log', 'order' => 90,
                    'default' => 90,
                    'rules' => ['required', 'integer', 'min:1', 'max:3650'],
                    'help' => 'How long to keep activity log entries. The `activity:prune` command runs daily at 03:00 and deletes rows older than this.'],
            ],
        ]);
    }

    protected function registerContentSettings(SettingsRegistry $registry): void
    {
        $registry->registerGroup('content', [
            'label' => 'Content',
            'icon' => 'article',
            'order' => 12,
            'description' => 'Trash retention and scheduled publishing.',
            'fields' => [
                ['key' => 'content_trash_retention_days', 'label' => 'Trash Retention (days)', 'type' => 'number', 'section' => 'Trash', 'order' => 10,
                    'default' => 30,
                    'rules' => ['required', 'integer', 'min:1', 'max:3650'],
                    'help' => 'How long trashed content is kept before auto-purge. Runs daily at 02:30.'],
            ],
        ]);
    }

    protected function registerAuthSettings(SettingsRegistry $registry): void
    {
        $registry->registerGroup('auth', [
            'label' => 'Authentication',
            'icon' => 'lock',
            'order' => 15,
            'description' => 'Login throttling, password reset, and optional 2FA enforcement.',
            'fields' => [
                ['key' => 'auth_login_max_attempts', 'label' => 'Max Failed Attempts (per IP+email window)', 'type' => 'number', 'section' => 'Rate Limit', 'order' => 10,
                    'default' => 5,
                    'rules' => ['required', 'integer', 'min:3', 'max:50']],

                ['key' => 'auth_login_decay_minutes', 'label' => 'Throttle Window (minutes)', 'type' => 'number', 'section' => 'Rate Limit', 'order' => 20,
                    'default' => 15,
                    'rules' => ['required', 'integer', 'min:1', 'max:1440']],

                ['key' => 'auth_login_lockout_after', 'label' => 'Hard Lockout After (failed attempts)', 'type' => 'number', 'section' => 'Lockout', 'order' => 30,
                    'default' => 10,
                    'rules' => ['required', 'integer', 'min:5', 'max:100']],

                ['key' => 'auth_login_lockout_minutes', 'label' => 'Lockout Duration (minutes)', 'type' => 'number', 'section' => 'Lockout', 'order' => 40,
                    'default' => 30,
                    'rules' => ['required', 'integer', 'min:1', 'max:1440']],

                ['key' => 'auth_password_reset_enabled', 'label' => 'Enable Password Reset', 'type' => 'boolean', 'section' => 'Password Reset', 'order' => 50,
                    'default' => true,
                    'rules' => ['boolean'],
                    'help' => 'Allow users to reset password via email link.'],

                ['key' => 'auth_password_reset_expire_minutes', 'label' => 'Reset Link Expiry (minutes)', 'type' => 'number', 'section' => 'Password Reset', 'order' => 60,
                    'default' => 60,
                    'rules' => ['required', 'integer', 'min:5', 'max:1440']],

                ['key' => 'auth_force_2fa_roles', 'label' => 'Enforce 2FA for Roles', 'type' => 'text', 'section' => 'Two-Factor', 'order' => 70,
                    'default' => '',
                    'rules' => ['nullable', 'string', 'max:300'],
                    'help' => 'Comma-separated role names. Users in these roles MUST enable 2FA. Leave blank to keep 2FA fully optional.'],
            ],
        ]);
    }

    protected function registerSeoSettings(SettingsRegistry $registry): void
    {
        $registry->registerGroup('seo', [
            'label' => 'SEO',
            'icon' => 'travel_explore',
            'order' => 20,
            'description' => 'Defaults for meta tags, indexing, social previews, and verification.',
            'fields' => [
                ['key' => 'seo_title_pattern', 'label' => 'Title Pattern', 'type' => 'text', 'section' => 'Defaults', 'order' => 10,
                    'default' => '{page} | {site}',
                    'rules' => ['required', 'string', 'max:200'],
                    'help' => 'Tokens: {page}, {site}, {tagline}. Used when a page has no SEO title override.'],

                ['key' => 'seo_default_description', 'label' => 'Default Meta Description', 'type' => 'textarea', 'section' => 'Defaults', 'order' => 20,
                    'default' => '',
                    'rules' => ['nullable', 'string', 'max:300'],
                    'help' => 'Used when a page does not provide its own description. Keep under 160 chars.'],

                ['key' => 'seo_default_og_image', 'label' => 'Default OG Image URL', 'type' => 'text', 'section' => 'Defaults', 'order' => 30,
                    'default' => '',
                    'rules' => ['nullable', 'string', 'max:500'],
                    'help' => 'Absolute or relative URL. Recommended size: 1200x630.'],

                ['key' => 'seo_allow_indexing', 'label' => 'Allow Search Engines to Index', 'type' => 'boolean', 'section' => 'Indexing', 'order' => 40,
                    'default' => true,
                    'rules' => ['boolean'],
                    'help' => 'Turn off on staging. Adds <meta name="robots" content="noindex,nofollow"> and a strict robots.txt.'],

                ['key' => 'seo_sitemap_enabled', 'label' => 'Enable /sitemap.xml', 'type' => 'boolean', 'section' => 'Indexing', 'order' => 50,
                    'default' => true,
                    'rules' => ['boolean']],

                ['key' => 'seo_robots_extra', 'label' => 'Extra robots.txt Lines', 'type' => 'code', 'section' => 'Indexing', 'order' => 60,
                    'default' => '',
                    'rules' => ['nullable', 'string', 'max:4000'],
                    'help' => 'Appended verbatim to robots.txt. One directive per line (e.g. `Disallow: /private`).'],

                ['key' => 'seo_indexnow_key', 'label' => 'IndexNow Key', 'type' => 'text', 'section' => 'Indexing', 'order' => 65,
                    'default' => '',
                    'rules' => ['nullable', 'string', 'max:128', 'regex:/^[A-Za-z0-9_-]*$/'],
                    'help' => 'Optional. When set, the CMS pings Bing/Yandex/Seznam via IndexNow on publish. Generate at https://www.indexnow.org/.'],

                ['key' => 'seo_twitter_handle', 'label' => 'Twitter Handle', 'type' => 'text', 'section' => 'Social', 'order' => 70,
                    'default' => '',
                    'rules' => ['nullable', 'string', 'max:50', 'regex:/^@?[A-Za-z0-9_]{1,15}$/'],
                    'help' => 'Without the @ — used for twitter:site card attribution.'],

                ['key' => 'seo_facebook_url', 'label' => 'Facebook Page URL', 'type' => 'text', 'section' => 'Social', 'order' => 80,
                    'default' => '',
                    'rules' => ['nullable', 'url']],

                ['key' => 'seo_default_locale', 'label' => 'Default OG Locale', 'type' => 'select', 'section' => 'Social', 'order' => 90,
                    'default' => 'en_US',
                    'options' => [
                        'en_US' => 'English (en_US)',
                        'en_GB' => 'English UK (en_GB)',
                        'id_ID' => 'Indonesian (id_ID)',
                    ],
                    'rules' => ['required', 'string']],

                ['key' => 'seo_google_verification', 'label' => 'Google Site Verification', 'type' => 'text', 'section' => 'Verification', 'order' => 100,
                    'default' => '',
                    'rules' => ['nullable', 'string', 'max:200'],
                    'help' => 'The content value from Search Console TXT/meta verification.'],

                ['key' => 'seo_bing_verification', 'label' => 'Bing Webmaster Verification', 'type' => 'text', 'section' => 'Verification', 'order' => 110,
                    'default' => '',
                    'rules' => ['nullable', 'string', 'max:200']],

                ['key' => 'seo_org_name', 'label' => 'Organization Name', 'type' => 'text', 'section' => 'Schema.org', 'order' => 120,
                    'default' => '',
                    'rules' => ['nullable', 'string', 'max:200'],
                    'help' => 'Falls back to Site Name. Used in JSON-LD Organization schema.'],

                ['key' => 'seo_org_logo', 'label' => 'Organization Logo URL', 'type' => 'text', 'section' => 'Schema.org', 'order' => 130,
                    'default' => '',
                    'rules' => ['nullable', 'string', 'max:500']],
            ],
        ]);
    }

    protected function registerBrevoSettings(SettingsRegistry $registry): void
    {
        $registry->registerGroup('brevo', [
            'label' => 'Brevo API',
            'icon' => 'mail',
            'order' => 25,
            'description' => 'Send transactional email via Brevo HTTP API. Use this when your hosting blocks outbound SMTP.',
            'fields' => [
                ['key' => 'brevo_enabled', 'label' => 'Route mail through Brevo API', 'type' => 'boolean', 'section' => 'Status', 'order' => 5,
                    'default' => false,
                    'rules' => ['boolean'],
                    'help' => 'When on, Laravel mail() and notifications go through Brevo /v3/smtp/email instead of the default driver.'],

                ['key' => 'brevo_api_key', 'label' => 'API Key', 'type' => 'password', 'section' => 'Credentials', 'order' => 10,
                    'default' => '',
                    'rules' => ['nullable', 'string', 'regex:/^xkeysib-[A-Za-z0-9]+/'],
                    'help' => 'Get your API key from app.brevo.com → SMTP & API → API Keys. Starts with "xkeysib-".'],

                ['key' => 'brevo_sender_email', 'label' => 'Sender Email', 'type' => 'email', 'section' => 'Sender', 'order' => 20,
                    'default' => '',
                    'rules' => ['nullable', 'email'],
                    'help' => 'Must be a verified sender in Brevo (Senders, Domains & Dedicated IPs).'],

                ['key' => 'brevo_sender_name', 'label' => 'Sender Name', 'type' => 'text', 'section' => 'Sender', 'order' => 30,
                    'default' => '',
                    'rules' => ['nullable', 'string', 'max:100']],
            ],
            'actions' => [
                ['label' => 'Send test email to me', 'handler' => BrevoTestEmailAction::class, 'icon' => 'send', 'color' => 'blue'],
            ],
        ]);
    }

    protected function registerRedirectSettings(SettingsRegistry $registry): void
    {
        $registry->registerGroup('redirect', [
            'label' => 'Redirect',
            'icon' => 'trending_flat',
            'order' => 30,
            'description' => 'Redirect old URLs to new ones. Supports exact paths or regex patterns with capture groups.',
            'component' => 'admin.redirects.redirect-table',
        ]);
    }

    protected function registerLanguageSettings(SettingsRegistry $registry): void
    {
        $registry->registerGroup('languages', [
            'label' => 'Languages',
            'icon' => 'language',
            'order' => 40,
            'description' => 'Locale defaults and the public language switcher. Per-page content translation is not enabled here.',
            'fields' => [
                ['key' => 'default_locale', 'label' => 'Default Locale', 'type' => 'select', 'section' => 'Locale', 'order' => 10,
                    'default' => config('app.locale', 'en'),
                    'options' => ['en' => 'English', 'id' => 'Bahasa Indonesia'],
                    'rules' => ['required', 'string', 'in:id,en']],

                ['key' => 'fallback_locale', 'label' => 'Fallback Locale', 'type' => 'select', 'section' => 'Locale', 'order' => 20,
                    'default' => 'en',
                    'options' => ['en' => 'English', 'id' => 'Bahasa Indonesia'],
                    'rules' => ['required', 'string', 'in:id,en']],

                ['key' => 'available_locales', 'label' => 'Available Locales', 'type' => 'text', 'section' => 'Locale', 'order' => 30,
                    'default' => 'en',
                    'rules' => ['required', 'string', 'regex:/^[a-z]{2}(_[A-Z]{2})?(,[a-z]{2}(_[A-Z]{2})?)*$/'],
                    'help' => 'Comma-separated list of locale codes (no spaces). E.g. `en,id,ja`.'],

                ['key' => 'locale_switcher_enabled', 'label' => 'Show Locale Switcher', 'type' => 'boolean', 'section' => 'Public', 'order' => 40,
                    'default' => false,
                    'rules' => ['boolean'],
                    'help' => 'Renders a dropdown on public pages (theme must include the <x-locale-switcher /> component).'],
            ],
        ]);
    }

    protected function registerCacheSettings(SettingsRegistry $registry): void
    {
        $registry->registerGroup('cache', [
            'label' => 'Cache',
            'icon' => 'bolt',
            'order' => 60,
            'description' => 'Full-page HTML cache for anonymous visitors. Auto-purged when pages or CPT entries are saved.',
            'fields' => [
                ['key' => 'page_cache_enabled', 'label' => 'Enable Page Cache', 'type' => 'boolean', 'section' => 'Page Cache', 'order' => 10,
                    'default' => false,
                    'rules' => ['boolean'],
                    'help' => 'Caches GET responses for anonymous visitors. Authenticated users always bypass.'],

                ['key' => 'page_cache_ttl', 'label' => 'TTL (seconds)', 'type' => 'number', 'section' => 'Page Cache', 'order' => 20,
                    'default' => 3600,
                    'rules' => ['required', 'integer', 'min:30', 'max:604800']],

                ['key' => 'page_cache_excluded_paths', 'label' => 'Excluded Paths', 'type' => 'code', 'section' => 'Page Cache', 'order' => 30,
                    'default' => "/forms/*\n/cart\n/checkout",
                    'rules' => ['nullable', 'string', 'max:4000'],
                    'help' => 'One pattern per line. Supports `*` wildcards.'],
            ],
        ]);
    }

    protected function registerCdnSettings(SettingsRegistry $registry): void
    {
        $registry->registerGroup('cdn', [
            'label' => 'CDN',
            'icon' => 'cloud',
            'order' => 65,
            'description' => 'Rewrite local asset URLs to a CDN. Settings only — wire up your CDN provider before enabling.',
            'fields' => [
                ['key' => 'cdn_enabled', 'label' => 'Enable CDN URL Rewriting', 'type' => 'boolean', 'section' => 'CDN', 'order' => 10,
                    'default' => false,
                    'rules' => ['boolean']],

                ['key' => 'cdn_base_url', 'label' => 'CDN Base URL', 'type' => 'text', 'section' => 'CDN', 'order' => 20,
                    'default' => '',
                    'rules' => ['nullable', 'url', 'max:300'],
                    'help' => 'Without trailing slash. Example: https://cdn.example.com'],

                ['key' => 'cdn_paths_to_rewrite', 'label' => 'Paths to Rewrite', 'type' => 'code', 'section' => 'CDN', 'order' => 30,
                    'default' => "/storage\n/build\n/themes",
                    'rules' => ['nullable', 'string', 'max:2000'],
                    'help' => 'One path prefix per line. Local URLs starting with these are rewritten to CDN.'],
            ],
        ]);
    }

    protected function registerImageOptSettings(SettingsRegistry $registry): void
    {
        $registry->registerGroup('imgopt', [
            'label' => 'Image Optimization',
            'icon' => 'image',
            'order' => 70,
            'description' => 'Image conversion and lazy loading. Run `php artisan media:optimize` to backfill existing images.',
            'fields' => [
                ['key' => 'img_auto_webp', 'label' => 'Auto-convert to WebP on Upload', 'type' => 'boolean', 'section' => 'Conversion', 'order' => 10,
                    'default' => true,
                    'rules' => ['boolean'],
                    'help' => 'New uploads get a WebP companion saved alongside the original.'],

                ['key' => 'img_optimize_original', 'label' => 'Compress Original File on Upload', 'type' => 'boolean', 'section' => 'Conversion', 'order' => 15,
                    'default' => true,
                    'rules' => ['boolean'],
                    'help' => 'Automatically compresses original JPG/PNG file quality and resizes oversized dimensions upon upload.'],

                ['key' => 'img_max_dimension', 'label' => 'Max Original Dimension (px)', 'type' => 'number', 'section' => 'Conversion', 'order' => 16,
                    'default' => 2560,
                    'rules' => ['required', 'integer', 'min:0', 'max:10000'],
                    'help' => 'Downscales oversized original images to this maximum width/height (0 for no limit).'],

                ['key' => 'img_jpg_quality', 'label' => 'JPEG Quality (%)', 'type' => 'number', 'section' => 'Conversion', 'order' => 20,
                    'default' => 85,
                    'rules' => ['required', 'integer', 'min:50', 'max:100']],

                ['key' => 'img_webp_quality', 'label' => 'WebP Quality (%)', 'type' => 'number', 'section' => 'Conversion', 'order' => 30,
                    'default' => 80,
                    'rules' => ['required', 'integer', 'min:50', 'max:100']],

                ['key' => 'img_lazy_load', 'label' => 'Lazy Load Images', 'type' => 'boolean', 'section' => 'Delivery', 'order' => 40,
                    'default' => true,
                    'rules' => ['boolean'],
                    'help' => 'Adds `loading="lazy"` to <img> tags in cached HTML responses.'],
            ],
        ]);
    }

    protected function registerPageOptSettings(SettingsRegistry $registry): void
    {
        $registry->registerGroup('pageopt', [
            'label' => 'Page Optimization',
            'icon' => 'speed',
            'order' => 75,
            'description' => 'Output minification, deferred resources, and resource hints applied to public HTML responses.',
            'fields' => [
                ['key' => 'pageopt_minify_html', 'label' => 'Minify HTML', 'type' => 'boolean', 'section' => 'Minification', 'order' => 10,
                    'default' => false,
                    'rules' => ['boolean'],
                    'help' => 'Strips comments and collapses whitespace in HTML responses (admin excluded).'],

                ['key' => 'pageopt_remove_query_strings', 'label' => 'Remove ?ver= Query Strings', 'type' => 'boolean', 'section' => 'Minification', 'order' => 20,
                    'default' => false,
                    'rules' => ['boolean']],

                ['key' => 'pageopt_gzip_enabled', 'label' => 'Enable Gzip Compression', 'type' => 'boolean', 'section' => 'Compression', 'order' => 30,
                    'default' => false,
                    'rules' => ['boolean'],
                    'help' => 'Compresses HTML/CSS/JS responses with gzip. Skip if your web server (NGINX/Apache) already does this.'],

                ['key' => 'pageopt_defer_external_scripts', 'label' => 'Defer External Scripts', 'type' => 'boolean', 'section' => 'Scripts', 'order' => 40,
                    'default' => false,
                    'rules' => ['boolean'],
                    'help' => 'Adds `defer` to external <script src="..."> tags. Inline scripts are never touched.'],

                ['key' => 'pageopt_defer_exclude', 'label' => 'Defer Exclude Patterns', 'type' => 'code', 'section' => 'Scripts', 'order' => 50,
                    'default' => '',
                    'rules' => ['nullable', 'string', 'max:2000'],
                    'help' => 'One pattern per line. Scripts whose src contains any pattern stay non-deferred (e.g. `analytics.js`, `gtm.js`).'],

                ['key' => 'pageopt_critical_css', 'label' => 'Critical CSS (above-the-fold)', 'type' => 'code', 'section' => 'Critical CSS', 'order' => 60,
                    'default' => '',
                    'rules' => ['nullable', 'string', 'max:50000'],
                    'help' => 'Inlined into <head> as <style>. Use a tool like https://www.sitelocity.com/critical-path-css-generator or `npx critical` to extract.'],

                ['key' => 'pageopt_deferred_stylesheets', 'label' => 'Deferred Stylesheet Patterns', 'type' => 'code', 'section' => 'Critical CSS', 'order' => 70,
                    'default' => '',
                    'rules' => ['nullable', 'string', 'max:2000'],
                    'help' => 'One pattern per line. Matching <link rel="stylesheet"> tags become non-blocking via rel="preload" swap. Only enable after critical CSS is set.'],
            ],
        ]);
    }

    protected function registerApiSettings(SettingsRegistry $registry): void
    {
        $registry->registerGroup('api', [
            'label' => 'API',
            'icon' => 'api',
            'order' => 80,
            'description' => 'CORS, default rate limit, and headless content surface.',
            'fields' => [
                ['key' => 'api_cors_origins', 'label' => 'CORS Allowed Origins', 'type' => 'text', 'section' => 'CORS', 'order' => 10,
                    'default' => '*',
                    'rules' => ['nullable', 'string', 'max:1000'],
                    'help' => '`*` to allow all, or comma-separated list of full origins (e.g. `https://app.example.com,https://www.example.com`).'],

                ['key' => 'api_default_rate_limit', 'label' => 'Default Rate Limit (req/min/token)', 'type' => 'number', 'section' => 'Rate Limit', 'order' => 20,
                    'default' => 60,
                    'rules' => ['required', 'integer', 'min:1', 'max:6000']],
            ],
        ]);
    }
}
