<?php

namespace Themes\Csi\Support;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Media;
use Illuminate\Support\Collection;
use Plugins\CsiMenus\Models\NavMenu;

class CsiDataProvider
{
    /**
     * Build dynamic + fallback data for the CSI homepage.
     *
     * @return array<string, mixed>
     */
    public static function homeViewData(): array
    {
        return [
            'clientLogos' => static::clientLogos(),
            'solutionsList' => static::solutionsList(),
            'allianceCategories' => static::allianceCategories(),
            'insightCategories' => static::insightCategories(),
            'contactForm' => function_exists('get_assigned_form') ? get_assigned_form('contact_form') : null,
            'contactSideImage' => static::findMediaByTitlePrefix('contact-side-image'),
        ];
    }

    /**
     * Load menus safely from csi-menus plugin (fail-safe if deactivated).
     *
     * @return Collection<int, mixed>
     */
    public static function menuItems(string $location): Collection
    {
        if (
            function_exists('is_plugin_active')
            && is_plugin_active('csi-menus')
            && class_exists(NavMenu::class)
        ) {
            return NavMenu::itemsForLocation($location);
        }

        return collect();
    }

    /**
     * Extract a YouTube video ID from a full URL, short URL, embed URL, or bare ID.
     */
    public static function extractYoutubeId(?string $input, string $fallback = 'C3w39WEDYeg'): string
    {
        $raw = trim((string) $input);
        if ($raw === '') {
            return $fallback;
        }

        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $raw)) {
            return $raw;
        }

        $patterns = [
            '/[?&]v=([A-Za-z0-9_-]{11})/',
            '/youtu\.be\/([A-Za-z0-9_-]{11})/',
            '/youtube(?:-nocookie)?\.com\/embed\/([A-Za-z0-9_-]{11})/',
            '/youtube\.com\/shorts\/([A-Za-z0-9_-]{11})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $raw, $m)) {
                return $m[1];
            }
        }

        return $fallback;
    }

    /**
     * Query Client Logo CPT entries.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public static function clientLogos(): Collection
    {
        $cpt = CustomPostType::whereIn('slug', ['client', 'clients'])->where('is_active', true)->first();
        if (! $cpt) {
            return collect();
        }

        return CptEntry::published()
            ->where('post_type_id', $cpt->id)
            ->orderBy('menu_order')
            ->orderBy('id')
            ->get()
            ->map(function (CptEntry $entry) {
                $logoPath = $entry->getMeta('logo') ?: $entry->featured_image;

                return [
                    'name' => $entry->getTranslation('title') ?? $entry->title,
                    'logo' => $logoPath,
                    'website_url' => $entry->getMeta('website_url'),
                ];
            })
            ->filter(fn (array $item) => ! empty($item['logo']))
            ->values();
    }

    /**
     * Query Partner Success Stories CPT entries (`partner_story`).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public static function partnerStories(): Collection
    {
        $cpt = CustomPostType::whereIn('slug', ['partner_story', 'partner-story', 'partner-stories', 'partner_stories'])
            ->where('is_active', true)
            ->first();

        if (! $cpt) {
            return collect();
        }

        $isId = app()->getLocale() === 'id';

        return CptEntry::published()
            ->where('post_type_id', $cpt->id)
            ->orderBy('menu_order')
            ->orderBy('id')
            ->get()
            ->values()
            ->map(function (CptEntry $entry, int $idx) use ($isId) {
                return [
                    'title' => $entry->getTranslation('title') ?? $entry->title,
                    'partner_name' => $entry->getMeta('partner_name') ?: ($entry->getTranslation('title') ?? $entry->title),
                    'summary' => $entry->getMeta('summary') ?: ($entry->getTranslation('excerpt') ?? $entry->excerpt ?? ''),
                    'image' => $entry->getMeta('image') ?: $entry->featured_image,
                    'link_text' => $entry->getMeta('link_text') ?: ($isId ? 'Selengkapnya' : 'Read More'),
                    'link_url' => $entry->getMeta('link_url') ?: '#contact',
                    'accent_color' => $entry->getMeta('accent_color') ?: ($idx % 2 === 0 ? '#ED7F22' : '#04B1C7'),
                ];
            });
    }

    /**
     * Query Solutions CPT if available, otherwise return static fallback cards.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function solutionsList(): array
    {
        $cpt = CustomPostType::whereIn('slug', ['solutions', 'solution'])->where('is_active', true)->first();
        if ($cpt) {
            $entries = CptEntry::published()
                ->where('post_type_id', $cpt->id)
                ->whereNull('parent_id')
                ->orderBy('menu_order')
                ->orderBy('id')
                ->get();

            if ($entries->isNotEmpty()) {
                return $entries->values()->map(function (CptEntry $entry, int $idx) {
                    $isOrange = ($entry->getMeta('accent_variant') === 'orange') || $idx >= 3;

                    return [
                        'title' => $entry->getTranslation('title') ?? $entry->title,
                        'subtitle' => $entry->getMeta('subtitle') ?: ($entry->getTranslation('excerpt') ?? $entry->excerpt ?? ''),
                        'cta' => $entry->getMeta('cta_text') ?: t('solutions.explore_prefix', 'Explore').' '.($entry->getTranslation('title') ?? $entry->title),
                        'url' => function_exists('cpt_entry_url') ? cpt_entry_url($entry) : '#solutions',
                        'icon' => $entry->getMeta('icon') ?: theme_asset('images/solution-icon-infrastructure.svg'),
                        'variant' => $isOrange ? 'orange' : 'cyan',
                    ];
                })->all();
            }
        }

        $isId = app()->getLocale() === 'id';

        return [
            [
                'title' => $isId ? 'Infrastruktur' : 'Infrastructure',
                'subtitle' => $isId ? 'Fondasi Infrastruktur IT Modern' : 'Modern IT Foundation',
                'cta' => $isId ? 'Jelajahi Solusi Infrastruktur' : 'Explore Infrastructure Solutions',
                'url' => '#solutions',
                'icon' => theme_asset('images/solution-icon-infrastructure.svg'),
                'variant' => 'cyan',
                'icon_class' => 'w-[30px] h-[30px]',
            ],
            [
                'title' => 'Cloud',
                'subtitle' => $isId ? 'Infrastruktur yang Fleksibel dan Scalable' : 'Scalable Infrastructure',
                'cta' => $isId ? 'Jelajahi Solusi Cloud' : 'Explore Cloud Solutions',
                'url' => '#solutions',
                'icon' => theme_asset('images/solution-icon-cloud.svg'),
                'variant' => 'cyan',
                'icon_class' => 'w-[28px] h-[21px]',
            ],
            [
                'title' => $isId ? 'Data dan Platform' : 'Data & Platform',
                'subtitle' => $isId ? 'Database dan Analytics untuk Kebutuhan Enterprise' : 'Enterprise Database & Analytics',
                'cta' => $isId ? 'Jelajahi Solusi Data dan Platform' : 'Explore Data & Platform Solutions',
                'url' => '#solutions',
                'icon' => theme_asset('images/solution-icon-data-platform.svg'),
                'variant' => 'cyan',
                'icon_class' => 'w-[30px] h-[30px]',
            ],
            [
                'title' => 'Security',
                'subtitle' => 'Robust Enterprise Protection',
                'cta' => 'Explore Security Solutions',
                'url' => '#solutions',
                'icon' => theme_asset('images/solution-icon-security.svg'),
                'variant' => 'orange',
                'icon_class' => 'w-[30px] h-[30px]',
            ],
            [
                'title' => 'Artificial Intelligence',
                'subtitle' => $isId ? 'Fondasi untuk Implementasi AI' : 'AI Deployment Foundation',
                'cta' => $isId ? 'Jelajahi Solusi Artificial Intelligence' : 'Explore AI Solutions',
                'url' => '#solutions',
                'icon' => theme_asset('images/solution-icon-ai.svg'),
                'variant' => 'orange',
                'icon_class' => 'w-[30px] h-[30px]',
            ],
        ];
    }

    /**
     * Load Technology Alliances dynamically from published `technology-alliances` CPT entries,
     * falling back to static catalog only if the CPT has not been registered.
     *
     * @return array<string, array<int, array{name: string, logo: string, url: string}>>
     */
    public static function allianceCategories(): array
    {
        $locale = app()->getLocale();
        $cpt = CustomPostType::whereIn('slug', ['technology-alliances', 'technology_alliances'])
            ->where('is_active', true)
            ->first();

        if ($cpt) {
            $entries = CptEntry::where('post_type_id', $cpt->id)
                ->where('status', 'published')
                ->orderBy('menu_order')
                ->orderBy('id')
                ->get();

            $categoryOrder = ['infrastructure', 'cloud', 'data-platform', 'security', 'ai'];
            $grouped = [];

            foreach ($entries as $entry) {
                $cat = trim((string) ($entry->getMeta('alliance_category') ?: 'infrastructure'));
                $logoPath = $entry->getMeta('brand_logo') ?: $entry->featured_image;
                $logo = $logoPath ? resolve_block_asset((string) $logoPath) : '';

                if ($logo === '') {
                    continue;
                }

                $grouped[$cat][] = [
                    'name' => (string) $entry->getTranslation('title', $locale),
                    'logo' => $logo,
                    'url' => (string) $entry->getUrl($locale),
                ];
            }

            foreach ($grouped as $cat => &$items) {
                usort($items, fn (array $a, array $b): int => strnatcasecmp($a['name'], $b['name']));
            }
            unset($items);

            // Preserve canonical tab order for known categories, then append any custom slugs.
            $ordered = [];
            foreach ($categoryOrder as $key) {
                if (isset($grouped[$key])) {
                    $ordered[$key] = $grouped[$key];
                    unset($grouped[$key]);
                }
            }

            foreach ($grouped as $key => $items) {
                $ordered[$key] = $items;
            }

            return $ordered;
        }

        return [
            'infrastructure' => [
                ['name' => 'Penguin Solutions', 'logo' => theme_asset('images/products/infra/penguin-solutions.png'), 'url' => '#alliances'],
                ['name' => 'Extreme Networks', 'logo' => theme_asset('images/products/infra/extreme-networks.png'), 'url' => '#alliances'],
                ['name' => 'Lenovo', 'logo' => theme_asset('images/products/infra/lenovo.png'), 'url' => '#alliances'],
                ['name' => 'IP Infusion', 'logo' => theme_asset('images/products/infra/ip-infusion.png'), 'url' => '#alliances'],
                ['name' => 'Smartoptics', 'logo' => theme_asset('images/products/infra/smartoptics.png'), 'url' => '#alliances'],
                ['name' => 'NetApp', 'logo' => theme_asset('images/products/infra/netapp.png'), 'url' => '#alliances'],
                ['name' => 'Progress', 'logo' => theme_asset('images/products/infra/progress.png'), 'url' => '#alliances'],
                ['name' => 'Supermicro', 'logo' => theme_asset('images/products/infra/supermicro.png'), 'url' => '#alliances'],
                ['name' => 'Cambium Networks', 'logo' => theme_asset('images/products/infra/cambium.png'), 'url' => '#alliances'],
                ['name' => 'NAKIVO', 'logo' => theme_asset('images/products/infra/nakivo.png'), 'url' => '#alliances'],
                ['name' => 'cPacket Networks', 'logo' => theme_asset('images/products/infra/cpacket.png'), 'url' => '#alliances'],
                ['name' => 'ASRock Rack', 'logo' => theme_asset('images/products/infra/asrock-rack.png'), 'url' => '#alliances'],
                ['name' => 'FalconStor', 'logo' => theme_asset('images/products/infra/falconstor.png'), 'url' => '#alliances'],
                ['name' => 'Ruijie Networks', 'logo' => theme_asset('images/products/infra/ruijie.png'), 'url' => '#alliances'],
                ['name' => 'Edgecore Networks', 'logo' => theme_asset('images/products/infra/edgecore.png'), 'url' => '#alliances'],
                ['name' => 'BlueCat', 'logo' => theme_asset('images/products/infra/bluecat.png'), 'url' => '#alliances'],
            ],
            'cloud' => [
                ['name' => 'Alibaba Cloud', 'logo' => theme_asset('images/products/cloud/alibaba-cloud.png'), 'url' => '#alliances'],
                ['name' => 'BytePlus', 'logo' => theme_asset('images/products/cloud/byteplus.png'), 'url' => '#alliances'],
                ['name' => 'DigitalOcean', 'logo' => theme_asset('images/products/cloud/digitalocean.png'), 'url' => '#alliances'],
            ],
            'data-platform' => [
                ['name' => 'Couchbase', 'logo' => theme_asset('images/products/data-platform/couchbase.png'), 'url' => '#alliances'],
                ['name' => 'EasiShare', 'logo' => theme_asset('images/products/data-platform/easishare.png'), 'url' => '#alliances'],
                ['name' => 'Esri', 'logo' => theme_asset('images/products/data-platform/esri.png'), 'url' => '#alliances'],
                ['name' => 'i2 Group', 'logo' => theme_asset('images/products/data-platform/i2.png'), 'url' => '#alliances'],
                ['name' => 'OceanBase', 'logo' => theme_asset('images/products/data-platform/oceanbase.png'), 'url' => '#alliances'],
                ['name' => 'Oracle', 'logo' => theme_asset('images/products/data-platform/oracle.png'), 'url' => '#alliances'],
                ['name' => 'Teradata', 'logo' => theme_asset('images/products/data-platform/teradata.png'), 'url' => '#alliances'],
            ],
            'security' => [
                ['name' => 'Axidian', 'logo' => theme_asset('images/products/security/axidian.png'), 'url' => '#alliances'],
                ['name' => 'Barracuda Networks', 'logo' => theme_asset('images/products/security/barracuda.png'), 'url' => '#alliances'],
                ['name' => 'i-Sprint', 'logo' => theme_asset('images/products/security/i-sprint.png'), 'url' => '#alliances'],
                ['name' => 'Menlo Security', 'logo' => theme_asset('images/products/security/menlo.png'), 'url' => '#alliances'],
                ['name' => 'Swissbit', 'logo' => theme_asset('images/products/security/swissbit.png'), 'url' => '#alliances'],
                ['name' => 'Thales', 'logo' => theme_asset('images/products/security/thales.png'), 'url' => '#alliances'],
                ['name' => 'Versasec', 'logo' => theme_asset('images/products/security/versasec.png'), 'url' => '#alliances'],
            ],
        ];
    }

    /**
     * Resolve human-readable tab labels for active Technology Alliance categories.
     *
     * @param  array<string, array<int, mixed>>  $categories
     * @return array<string, string>
     */
    public static function allianceCategoryTabs(array $categories): array
    {
        $labels = [
            'infrastructure' => 'Infrastructure',
            'cloud' => 'Cloud',
            'data-platform' => 'Data Platform',
            'security' => 'Security',
            'ai' => 'Artificial Intelligence',
        ];

        $tabs = [];
        foreach (array_keys($categories) as $key) {
            $tabs[$key] = $labels[$key] ?? ucwords(str_replace(['-', '_'], ' ', (string) $key));
        }

        return $tabs;
    }

    /**
     * Real Insights fallback case studies (uses MediaService-imported images with responsive srcset).
     *
     * @return array<string, array<int, array<string, string>>>
     */
    public static function insightCategories(): array
    {
        $isId = app()->getLocale() === 'id';

        $campusImg = static::findMediaUrl('case-halu-oleo-campus', 'md');
        $bengkuluImg = static::findMediaUrl('case-univ-bengkulu', 'md');
        $wikaImg = static::findMediaUrl('case-wika', 'md');
        $iskakImg = static::findMediaUrl('case-rsud-iskak', 'md');
        $eximbankImg = static::findMediaUrl('case-eximbank', 'md');

        $uhoLogo = static::findMediaUrl('logo-halu-oleo', 'sm');
        $client2 = static::findMediaUrl('client-2', 'sm');
        $client3 = static::findMediaUrl('client-3', 'sm');
        $client4 = static::findMediaUrl('client-4', 'sm');
        $client5 = static::findMediaUrl('client-5', 'sm');

        return [
            'education' => [
                [
                    'title' => $isId
                        ? 'Meningkatkan Produktivitas Akademik bagi Lebih dari 40.000 Sivitas Akademika'
                        : 'Accelerating Academic Productivity for 40,000+ Campus Community Members',
                    'quote' => $isId
                        ? '"Solusi enterprise networking dari Converion Informatica membantu mengatasi bottleneck dan area dengan jangkauan Wi-Fi terbatas di seluruh lingkungan kampus. Dengan konektivitas yang lancar melalui lebih dari 788 access point, sivitas akademika dapat mengakses sistem informasi akademik, platform penelitian, dan berbagai kebutuhan administrasi sehari-hari dengan lebih mudah dan tanpa gangguan."'
                        : '"Our enterprise networking solutions from Converion Informatica eliminate bottlenecks and Wi-Fi dead zones across the entire campus environment. Seamless connectivity powered by 788+ access points ensures smooth and uninterrupted access to academic information systems, research platforms, and daily administrative operations."',
                    'author' => 'Saefuddin',
                    'role' => $isId
                        ? "Kepala Unit Penunjang Akademik (UPA)\nTeknologi Informasi dan Komunikasi"
                        : "Head of the Academic Support Unit (UPA)\nInformation and Communication Technology",
                    'logoImg' => $uhoLogo,
                    'image' => $campusImg,
                ],
                [
                    'title' => $isId
                        ? 'Mendukung Lebih dari 8.000 Mahasiswa dan Meningkatkan Performa E-Learning dengan All-Flash Storage'
                        : 'Supporting 8,000+ Students & Accelerating E-Learning Performance with All-Flash Storage',
                    'quote' => $isId
                        ? '"Untuk mendukung kelancaran aktivitas akademik lebih dari 8.000 mahasiswa, transisi ke all-flash storage bersama tim Converion telah meningkatkan kecepatan baca dan tulis data secara signifikan. Kini, peningkatan beban kerja e-learning dan kebutuhan aplikasi kampus dapat ditangani dengan lebih cepat tanpa terkendala bottleneck pada storage."'
                        : '"To support the seamless academic activities of more than 8,000 students, our transition to all-flash storage with the Converion team has significantly increased data read and write speeds. Growing e-learning workloads and demands across campus applications can now be handled instantly without storage-related bottlenecks."',
                    'author' => 'Funny Farady',
                    'role' => 'Head of SIM Center, LPTIK, Universitas Bengkulu',
                    'logoImg' => $client5,
                    'image' => $bengkuluImg,
                ],
            ],
            'construction' => [
                [
                    'title' => $isId
                        ? 'Modernisasi Sistem Enterprise Tanpa Downtime untuk Menjamin Kelancaran Operasional'
                        : 'Zero Downtime & Uninterrupted Operations for Mission-Critical Enterprise Systems',
                    'quote' => $isId
                        ? '"Sebagai perusahaan infrastruktur berskala besar, kami harus memastikan sistem tetap beroperasi 24/7. Bersama Converion Informatica, proses modernisasi dan upgrade storage controller berhasil diselesaikan di luar jam operasional tanpa downtime. Hal ini turut menjaga keamanan dan stabilitas seluruh data operasional serta backup ERP yang bersifat kritikal."'
                        : '"As a large-scale infrastructure company, our systems must remain operational 24/7. With Converion Informatica, our storage controller modernization and upgrade process was successfully completed outside business hours with zero downtime, ensuring the security and stability of all operational data and critical ERP backups."',
                    'author' => 'Boggi Haryo',
                    'role' => 'Network Infrastructure & OM Manager, WIKA',
                    'logoImg' => $client2,
                    'image' => $wikaImg,
                ],
            ],
            'healthcare' => [
                [
                    'title' => $isId
                        ? 'Menjaga Kesiapan Operasional IT 24/7 dan Memastikan Migrasi Data Berjalan Lancar'
                        : '24/7 IT Operational Readiness & Seamless Data Migration',
                    'quote' => $isId
                        ? '"Sebagai rumah sakit rujukan yang beroperasi 24/7, keandalan sistem IT sangat penting untuk mendukung pelayanan dan keselamatan pasien. Solusi enterprise storage dari Converion Informatica memastikan ribuan rekam medis dan riwayat klinis dapat diakses secara cepat dan akurat oleh dokter serta tenaga medis tanpa gangguan."'
                        : '"As a referral hospital operating 24/7, IT system reliability is critical to patient care and safety. Converion Informatica\'s enterprise storage solutions ensure that thousands of medical records and clinical histories can be accessed instantly by doctors and medical professionals, accurately and without disruption."',
                    'author' => 'Heru Eko Susanto',
                    'role' => 'Sub-Coordinator of Analysis & Monitoring, RSUD dr. Iskak Tulungagung',
                    'logoImg' => $client3,
                    'image' => $iskakImg,
                ],
            ],
            'banking' => [
                [
                    'title' => $isId
                        ? 'Sinkronisasi Data Antar Cabang di Seluruh Indonesia dengan Lebih Lancar'
                        : 'Seamless Data Synchronization Across Branches Nationwide',
                    'quote' => $isId
                        ? '"Dengan EasiShare sebagai solusi file sharing dan management, kini kami memiliki sistem on-premises yang terintegrasi, dilengkapi perlindungan antivirus dan deteksi dini ancaman untuk memastikan kontrol serta keamanan data. Diimplementasikan oleh PT Converion Solution Informatica, solusi ini juga memungkinkan sinkronisasi dan berbagi file antar cabang di seluruh Indonesia dengan lebih lancar. Kehadirannya sangat mendukung kelancaran operasional bisnis kami."'
                        : '"With EasiShare as our file-sharing and management solution, we now have an integrated on-premises system equipped with antivirus protection and early threat detection to ensure data control and security. Implemented by PT Converion Solution Informatica, the solution also enables seamless file synchronization and sharing across our branches throughout Indonesia. This solution has greatly supported our business operations."',
                    'author' => 'David Sugiana',
                    'role' => 'IT Infrastructure Officer, Exim Bank Indonesia',
                    'logoImg' => $client4,
                    'image' => $eximbankImg,
                ],
            ],
        ];
    }

    public static function findMediaByTitlePrefix(string $titlePrefix): ?Media
    {
        return Media::where('title', $titlePrefix)->latest('id')->first();
    }

    protected static function findMediaUrl(string $title, string $variant = 'md'): string
    {
        $media = static::findMediaByTitlePrefix($title);
        if (! $media) {
            return '';
        }

        return resolve_block_asset($media->path, $variant);
    }
}
