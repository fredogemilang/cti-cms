<?php

namespace Themes\Kouventa\Handlers;

use App\Http\Controllers\PageController;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as BladeView;

class SolutionRouteHandler
{
    /**
     * AI Solution Department configurations.
     * Maps tabs 0-6 to canonical slugs, aliases, and multilingual SEO metadata.
     */
    public static function getAiDepts(): array
    {
        return [
            0 => [
                'tab' => 0,
                'slugs' => ['en' => 'customer-service', 'id' => 'customer-service'],
                'aliases' => ['cs', 'dukungan-pelanggan', 'layanan-pelanggan'],
                'title' => [
                    'en' => 'Customer Service AI Agent Solutions',
                    'id' => 'Solusi AI Agent Customer Service',
                ],
                'description' => [
                    'en' => 'Automate customer support, instant SLA responses, CRM sync, and omnichannel resolution with autonomous Kouventa AI agents.',
                    'id' => 'Otomasi layanan pelanggan, respons SLA instan, sinkronisasi CRM, dan resolusi omnichannel dengan agent AI Kouventa.',
                ],
            ],
            1 => [
                'tab' => 1,
                'slugs' => ['en' => 'recruitment', 'id' => 'rekrutmen'],
                'aliases' => ['recruitment', 'rekrutmen', 'hr', 'recruiting'],
                'title' => [
                    'en' => 'Recruitment AI Agent Solutions',
                    'id' => 'Solusi AI Agent Rekrutmen',
                ],
                'description' => [
                    'en' => 'Streamline resume screening, candidate ranking, interview scheduling, and hiring workflows with AI-powered agents.',
                    'id' => 'Sederhanakan screening CV, pemeringkatan kandidat, penjadwalan interview, dan alur rekrutmen dengan agent AI.',
                ],
            ],
            2 => [
                'tab' => 2,
                'slugs' => ['en' => 'sales-marketing', 'id' => 'sales-marketing'],
                'aliases' => ['sales', 'marketing', 'penjualan-pemasaran', 'penjualan'],
                'title' => [
                    'en' => 'Sales & Marketing AI Agent Solutions',
                    'id' => 'Solusi AI Agent Sales & Marketing',
                ],
                'description' => [
                    'en' => 'Accelerate lead qualification, personalized outreach, CRM enrichment, and marketing conversions with autonomous AI agents.',
                    'id' => 'Percepat kualifikasi prospek, penjangkauan personal, pengayaan CRM, dan konversi pemasaran dengan agent AI otonom.',
                ],
            ],
            3 => [
                'tab' => 3,
                'slugs' => ['en' => 'business-operations', 'id' => 'operasional-bisnis'],
                'aliases' => ['operations', 'business-operations', 'operasional', 'operasional-bisnis'],
                'title' => [
                    'en' => 'Business Operations AI Agent Solutions',
                    'id' => 'Solusi AI Agent Operasional Bisnis',
                ],
                'description' => [
                    'en' => 'Automate document workflows, cross-department handoffs, data reconciliation, and operational reporting.',
                    'id' => 'Otomasi alur dokumen, koordinasi antar departemen, rekonsiliasi data, dan laporan operasional bisnis.',
                ],
            ],
            4 => [
                'tab' => 4,
                'slugs' => ['en' => 'finance', 'id' => 'finance'],
                'aliases' => ['keuangan', 'payment-prioritization', 'prioritas-pembayaran'],
                'title' => [
                    'en' => 'Finance AI Agent Solutions',
                    'id' => 'Solusi AI Agent Finance',
                ],
                'description' => [
                    'en' => 'Optimize payment prioritization, cash flow management, invoice verification, and financial audit trails.',
                    'id' => 'Optimalkan prioritas pembayaran, manajemen arus kas, verifikasi invoice, dan jejak audit keuangan.',
                ],
            ],
            5 => [
                'tab' => 5,
                'slugs' => ['en' => 'bank-reconciliation', 'id' => 'rekonsiliasi-bank'],
                'aliases' => ['bank-reconciliation', 'rekonsiliasi-bank', 'bank-rec'],
                'title' => [
                    'en' => 'Bank Reconciliation AI Agent Solutions',
                    'id' => 'Solusi AI Agent Rekonsiliasi Bank',
                ],
                'description' => [
                    'en' => 'Automate multi-bank statement parsing, transaction matching, anomaly detection, and ledger sync.',
                    'id' => 'Otomasi parsing rekening koran multi-bank, pencocokan transaksi, deteksi anomali, dan sinkronisasi buku besar.',
                ],
            ],
            6 => [
                'tab' => 6,
                'slugs' => ['en' => 'legal-compliance', 'id' => 'legal-compliance'],
                'aliases' => ['legal', 'compliance', 'hukum-kepatuhan'],
                'title' => [
                    'en' => 'Legal & Compliance AI Agent Solutions',
                    'id' => 'Solusi AI Agent Legal & Compliance',
                ],
                'description' => [
                    'en' => 'Accelerate contract review, clause risk analysis, regulatory compliance checks, and legal audits.',
                    'id' => 'Percepat peninjauan kontrak, analisis risiko klausul, pemeriksaan kepatuhan regulasi, dan audit legal.',
                ],
            ],
        ];
    }

    /**
     * Customer Engagement Use Cases configurations.
     * Maps cases 0-2 to canonical slugs, aliases, and multilingual SEO metadata.
     */
    public static function getCeCases(): array
    {
        return [
            0 => [
                'case' => 0,
                'slugs' => ['en' => 'sales-marketing', 'id' => 'sales-marketing'],
                'aliases' => ['sales', 'marketing', 'penjualan-pemasaran', 'penjualan'],
                'title' => [
                    'en' => 'Sales & Marketing Customer Engagement Solutions',
                    'id' => 'Solusi Customer Engagement Sales & Marketing',
                ],
                'description' => [
                    'en' => 'Omnichannel customer engagement for lead conversion, WhatsApp campaigns, and automated follow-ups.',
                    'id' => 'Interaksi pelanggan omnichannel untuk konversi prospek, kampanye WhatsApp, dan follow-up otomatis.',
                ],
            ],
            1 => [
                'case' => 1,
                'slugs' => ['en' => 'customer-service', 'id' => 'customer-service'],
                'aliases' => ['cs', 'dukungan-pelanggan', 'layanan-pelanggan'],
                'title' => [
                    'en' => 'Customer Service Engagement Solutions',
                    'id' => 'Solusi Customer Engagement Customer Service',
                ],
                'description' => [
                    'en' => '24/7 unified inbox, intelligent ticketing, agent workspace, and automated CSAT surveys.',
                    'id' => 'Kotak masuk terpadu 24/7, sistem tiket cerdas, ruang kerja agen, dan survei CSAT otomatis.',
                ],
            ],
            2 => [
                'case' => 2,
                'slugs' => ['en' => 'operations', 'id' => 'operasional'],
                'aliases' => ['operations', 'operasional', 'ops'],
                'title' => [
                    'en' => 'Operations Customer Engagement Solutions',
                    'id' => 'Solusi Customer Engagement Operasional',
                ],
                'description' => [
                    'en' => 'Automate order tracking, identity verification, account notifications, and operational messaging.',
                    'id' => 'Otomasi pelacakan pesanan, verifikasi identitas, notifikasi akun, dan pengiriman pesan operasional.',
                ],
            ],
        ];
    }

    /**
     * Handle base solution routes (/solutions/ai, /solutions/customer-engagement)
     * and redirect legacy query parameters (?dept=X, ?case=X) to clean SEO URLs.
     */
    public function showSub(Request $request, ?string $locale = null, string $sub = ''): mixed
    {
        $currentLocale = $locale ?? app()->getLocale();
        if ($locale && in_array($locale, available_locales(), true)) {
            app()->setLocale($locale);
        }

        // Backward compatibility: 301 redirect legacy query parameters
        if ($sub === 'ai') {
            $raw = $request->query('dept') ?? $request->query('tab');
            if ($raw !== null && is_numeric($raw)) {
                $tabIndex = (int) $raw;
                $dept = self::getAiDepts()[$tabIndex] ?? null;
                if ($dept) {
                    $targetSlug = $dept['slugs'][$currentLocale] ?? $dept['slugs']['en'];
                    $targetUrl = localized_url("/solutions/ai/{$targetSlug}", $currentLocale);

                    return redirect($targetUrl, 301);
                }
            }
        } elseif ($sub === 'customer-engagement') {
            $raw = $request->query('case') ?? $request->query('tab');
            if ($raw !== null && is_numeric($raw)) {
                $caseIndex = (int) $raw;
                $case = self::getCeCases()[$caseIndex] ?? null;
                if ($case) {
                    $targetSlug = $case['slugs'][$currentLocale] ?? $case['slugs']['en'];
                    $targetUrl = localized_url("/solutions/customer-engagement/{$targetSlug}", $currentLocale);

                    return redirect($targetUrl, 301);
                }
            }
        }

        // Standard page view without query parameters
        return $locale
            ? app(PageController::class)->show($locale, "solutions/{$sub}")
            : app(PageController::class)->show("solutions/{$sub}");
    }

    /**
     * Handle AI department clean URLs (/solutions/ai/{dept}).
     */
    public function showDept(Request $request, ?string $locale = null, string $dept = ''): BladeView|RedirectResponse
    {
        $currentLocale = $locale ?? app()->getLocale();
        if ($locale && in_array($locale, available_locales(), true)) {
            app()->setLocale($locale);
        }

        $deptLower = strtolower(trim($dept));
        $matchedConfig = null;

        foreach (self::getAiDepts() as $config) {
            if (
                in_array($deptLower, array_values($config['slugs']), true) ||
                in_array($deptLower, $config['aliases'], true)
            ) {
                $matchedConfig = $config;
                break;
            }
        }

        abort_if(! $matchedConfig, 404);

        $canonicalSlug = $matchedConfig['slugs'][$currentLocale] ?? $matchedConfig['slugs']['en'];

        // Enforce canonical slug redirect (e.g. alias used or mismatched locale slug)
        if ($deptLower !== strtolower($canonicalSlug)) {
            $targetUrl = localized_url("/solutions/ai/{$canonicalSlug}", $currentLocale);

            return redirect($targetUrl, 301);
        }

        $basePage = null;
        if (class_exists(\Plugins\KouventaPages\Models\KouventaPage::class)) {
            $basePage = \Plugins\KouventaPages\Models\KouventaPage::where('slug', 'solutions/ai')->first()
                ?: \Plugins\KouventaPages\Models\KouventaPage::findByLocalizedSlug('solutions/ai');
        }
        if (! $basePage) {
            $basePage = Page::published()->where('slug', 'solutions/ai')->first()
                ?: Page::findByLocalizedSlug('solutions/ai');
        }
        abort_if(! $basePage, 404);

        if (method_exists($basePage, 'blocks')) {
            $basePage->load(['blocks' => function ($q) {
                $q->whereNull('parent_block_id')
                    ->with('childBlocks')
                    ->orderBy('order');
            }]);
        }

        // Build virtual Page for SEO injection and Breadcrumbs
        $virtualPage = clone $basePage;
        $virtualPage->id = null;
        $virtualPage->exists = false;
        $virtualPage->title = $matchedConfig['title'][$currentLocale] ?? $matchedConfig['title']['en'];
        $virtualPage->excerpt = $matchedConfig['description'][$currentLocale] ?? $matchedConfig['description']['en'];
        if (isset($virtualPage->seo) && is_array($virtualPage->seo)) {
            $virtualPage->seo = array_merge($virtualPage->seo, [
                'title' => $matchedConfig['title'][$currentLocale] ?? $matchedConfig['title']['en'],
                'description' => $matchedConfig['description'][$currentLocale] ?? $matchedConfig['description']['en'],
            ]);
        }
        $virtualPage->slug = "solutions/ai/{$canonicalSlug}";
        $virtualPage->translations = [
            'title' => [
                'en' => $matchedConfig['title']['en'],
                'id' => $matchedConfig['title']['id'],
            ],
            'slug' => [
                'en' => "solutions/ai/{$matchedConfig['slugs']['en']}",
                'id' => "solutions/ai/{$matchedConfig['slugs']['id']}",
            ],
            'excerpt' => [
                'en' => $matchedConfig['description']['en'],
                'id' => $matchedConfig['description']['id'],
            ],
        ];

        View::share('page', $virtualPage);
        $request->attributes->set('page', $virtualPage);

        return view('kouventa::pages.solution-ai', [
            'page' => $virtualPage,
            'basePage' => $basePage,
            'blocks' => $basePage->blocks ?? collect(),
            'initialTab' => $matchedConfig['tab'],
            'activeSlug' => $canonicalSlug,
        ]);
    }

    /**
     * Handle Customer Engagement use case clean URLs (/solutions/customer-engagement/{case}).
     */
    public function showCase(Request $request, ?string $locale = null, string $case = ''): BladeView|RedirectResponse
    {
        $currentLocale = $locale ?? app()->getLocale();
        if ($locale && in_array($locale, available_locales(), true)) {
            app()->setLocale($locale);
        }

        $caseLower = strtolower(trim($case));
        $matchedConfig = null;

        foreach (self::getCeCases() as $config) {
            if (
                in_array($caseLower, array_values($config['slugs']), true) ||
                in_array($caseLower, $config['aliases'], true)
            ) {
                $matchedConfig = $config;
                break;
            }
        }

        abort_if(! $matchedConfig, 404);

        $canonicalSlug = $matchedConfig['slugs'][$currentLocale] ?? $matchedConfig['slugs']['en'];

        // Enforce canonical slug redirect
        if ($caseLower !== strtolower($canonicalSlug)) {
            $targetUrl = localized_url("/solutions/customer-engagement/{$canonicalSlug}", $currentLocale);

            return redirect($targetUrl, 301);
        }

        $basePage = null;
        if (class_exists(\Plugins\KouventaPages\Models\KouventaPage::class)) {
            $basePage = \Plugins\KouventaPages\Models\KouventaPage::where('slug', 'solutions/customer-engagement')->first()
                ?: \Plugins\KouventaPages\Models\KouventaPage::findByLocalizedSlug('solutions/customer-engagement');
        }
        if (! $basePage) {
            $basePage = Page::published()->where('slug', 'solutions/customer-engagement')->first()
                ?: Page::findByLocalizedSlug('solutions/customer-engagement');
        }
        abort_if(! $basePage, 404);

        if (method_exists($basePage, 'blocks')) {
            $basePage->load(['blocks' => function ($q) {
                $q->whereNull('parent_block_id')
                    ->with('childBlocks')
                    ->orderBy('order');
            }]);
        }

        // Build virtual Page for SEO injection and Breadcrumbs
        $virtualPage = clone $basePage;
        $virtualPage->id = null;
        $virtualPage->exists = false;
        $virtualPage->title = $matchedConfig['title'][$currentLocale] ?? $matchedConfig['title']['en'];
        $virtualPage->excerpt = $matchedConfig['description'][$currentLocale] ?? $matchedConfig['description']['en'];
        if (isset($virtualPage->seo) && is_array($virtualPage->seo)) {
            $virtualPage->seo = array_merge($virtualPage->seo, [
                'title' => $matchedConfig['title'][$currentLocale] ?? $matchedConfig['title']['en'],
                'description' => $matchedConfig['description'][$currentLocale] ?? $matchedConfig['description']['en'],
            ]);
        }
        $virtualPage->slug = "solutions/customer-engagement/{$canonicalSlug}";
        $virtualPage->translations = [
            'title' => [
                'en' => $matchedConfig['title']['en'],
                'id' => $matchedConfig['title']['id'],
            ],
            'slug' => [
                'en' => "solutions/customer-engagement/{$matchedConfig['slugs']['en']}",
                'id' => "solutions/customer-engagement/{$matchedConfig['slugs']['id']}",
            ],
            'excerpt' => [
                'en' => $matchedConfig['description']['en'],
                'id' => $matchedConfig['description']['id'],
            ],
        ];

        View::share('page', $virtualPage);
        $request->attributes->set('page', $virtualPage);

        return view('kouventa::pages.solution-ce', [
            'page' => $virtualPage,
            'basePage' => $basePage,
            'blocks' => $basePage->blocks ?? collect(),
            'initialCase' => $matchedConfig['case'],
            'activeSlug' => $canonicalSlug,
        ]);
    }

    /**
     * Get all solution tab URLs for sitemap generation across all active locales.
     *
     * @return array<int, array{loc: string, lastmod: string|null, changefreq: string, priority: float, type: string}>
     */
    public static function getAllSolutionSitemapUrls(): array
    {
        $urls = [];
        $locales = available_locales();
        $defaultLocale = setting('default_locale', config('app.locale', 'en'));

        // AI Solutions
        $aiPage = null;
        if (class_exists(\Plugins\KouventaPages\Models\KouventaPage::class)) {
            $aiPage = \Plugins\KouventaPages\Models\KouventaPage::where('slug', 'solutions/ai')->first();
        }
        if (! $aiPage) {
            $aiPage = Page::published()->where('slug', 'solutions/ai')->first();
        }
        $aiLastMod = $aiPage?->updated_at ? $aiPage->updated_at->toAtomString() : now()->toAtomString();

        foreach (self::getAiDepts() as $dept) {
            foreach ($locales as $loc) {
                $slug = $dept['slugs'][$loc] ?? $dept['slugs']['en'];
                $path = ($loc !== $defaultLocale && setting('locale_url_structure', 'prefix') === 'prefix')
                    ? "/{$loc}/solutions/ai/{$slug}"
                    : "/solutions/ai/{$slug}";

                $urls[] = [
                    'loc' => trailing_slash_url(url($path)),
                    'lastmod' => $aiLastMod,
                    'changefreq' => 'weekly',
                    'priority' => 0.8,
                    'type' => 'Page',
                    'images' => [],
                ];
            }
        }

        // Customer Engagement
        $cePage = null;
        if (class_exists(\Plugins\KouventaPages\Models\KouventaPage::class)) {
            $cePage = \Plugins\KouventaPages\Models\KouventaPage::where('slug', 'solutions/customer-engagement')->first();
        }
        if (! $cePage) {
            $cePage = Page::published()->where('slug', 'solutions/customer-engagement')->first();
        }
        $ceLastMod = $cePage?->updated_at ? $cePage->updated_at->toAtomString() : now()->toAtomString();

        foreach (self::getCeCases() as $case) {
            foreach ($locales as $loc) {
                $slug = $case['slugs'][$loc] ?? $case['slugs']['en'];
                $path = ($loc !== $defaultLocale && setting('locale_url_structure', 'prefix') === 'prefix')
                    ? "/{$loc}/solutions/customer-engagement/{$slug}"
                    : "/solutions/customer-engagement/{$slug}";

                $urls[] = [
                    'loc' => trailing_slash_url(url($path)),
                    'lastmod' => $ceLastMod,
                    'changefreq' => 'weekly',
                    'priority' => 0.8,
                    'type' => 'Page',
                    'images' => [],
                ];
            }
        }

        return $urls;
    }

    /**
     * Generate localized clean URL for an AI solution department or CE usecase.
     */
    public static function url(string $type, string|int $key, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        if ($type === 'ai') {
            foreach (self::getAiDepts() as $idx => $dept) {
                if ($key === $idx || $key === (string) $idx || $key === $dept['slugs']['en'] || $key === $dept['slugs']['id'] || in_array($key, $dept['aliases'], true)) {
                    $slug = $dept['slugs'][$locale] ?? $dept['slugs']['en'];
                    return localized_url("/solutions/ai/{$slug}", $locale);
                }
            }
            return localized_url('/solutions/ai', $locale);
        }

        if ($type === 'customer-engagement' || $type === 'ce') {
            foreach (self::getCeCases() as $idx => $case) {
                if ($key === $idx || $key === (string) $idx || $key === $case['slugs']['en'] || $key === $case['slugs']['id'] || in_array($key, $case['aliases'], true)) {
                    $slug = $case['slugs'][$locale] ?? $case['slugs']['en'];
                    return localized_url("/solutions/customer-engagement/{$slug}", $locale);
                }
            }
            return localized_url('/solutions/customer-engagement', $locale);
        }

        return localized_url("/solutions/{$type}", $locale);
    }
}
