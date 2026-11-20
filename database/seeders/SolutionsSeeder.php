<?php

namespace Database\Seeders;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Media;
use App\Models\MetaField;
use App\Services\MediaService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Plugins\CsiMenus\Models\NavMenu;
use Plugins\CsiMenus\Models\NavMenuItem;

class SolutionsSeeder extends Seeder
{
    public function run(): void
    {
        $cpt = $this->upsertPostTypeAndFields();
        $mediaMap = $this->importSolutionMedia();

        $alliancesCpt = CustomPostType::where('slug', 'technology-alliances')->first();
        $vendorIdsBySlug = $alliancesCpt
            ? CptEntry::where('post_type_id', $alliancesCpt->id)
                ->where('status', 'published')
                ->pluck('id', 'slug')
                ->all()
            : [];

        $resolveVendors = static function (array $slugs) use ($vendorIdsBySlug): array {
            $ids = [];
            foreach ($slugs as $slug) {
                if (isset($vendorIdsBySlug[$slug])) {
                    $ids[] = (int) $vendorIdsBySlug[$slug];
                }
            }

            return $ids;
        };

        foreach ($this->solutionsPayload($mediaMap, $resolveVendors) as $order => $item) {
            $entry = CptEntry::withTrashed()
                ->where('post_type_id', $cpt->id)
                ->where('slug', $item['slug'])
                ->first();

            if (! $entry) {
                $entry = new CptEntry;
                $entry->post_type_id = $cpt->id;
                $entry->slug = $item['slug'];
                $entry->author_id = 1;
            } elseif ($entry->trashed()) {
                $entry->restore();
            }

            $entry->title = $item['title_en'];
            $entry->excerpt = $item['subtitle_en'];
            $entry->content = $item['hero_subtitle_en'];
            $entry->featured_image = $item['modules_image'];
            $entry->status = 'published';
            $entry->published_at ??= now();
            $entry->menu_order = $order + 1;

            $entry->translations = [
                'en' => [
                    'title' => $item['title_en'],
                    'slug' => $item['slug'],
                    'excerpt' => $item['subtitle_en'],
                    'content' => $item['hero_subtitle_en'],
                ],
                'id' => [
                    'title' => $item['title_id'],
                    'slug' => $item['slug_id'],
                    'excerpt' => $item['subtitle_id'],
                    'content' => $item['hero_subtitle_id'],
                ],
            ];

            $entry->meta = [
                'subtitle' => $item['subtitle_en'],
                'archive_summary' => $item['archive_summary_en'],
                'archive_bullets' => implode("\n", $item['archive_bullets_en']),
                'cta_text' => $item['cta_en'],
                'icon' => $item['icon'],
                'accent_variant' => $item['accent_variant'],
                'hero_subtitle' => $item['hero_subtitle_en'],
                'modules_image' => $item['modules_image'],
                'modules_heading' => $item['modules_heading_en'],
                'modules' => $item['modules_en'],
                'services_heading' => $item['services_heading_en'],
                'services_subtitle' => $item['services_subtitle_en'],
                'services_cards' => $item['services_cards_en'],
                '_translations' => [
                    'id' => [
                        'subtitle' => $item['subtitle_id'],
                        'archive_summary' => $item['archive_summary_id'],
                        'archive_bullets' => implode("\n", $item['archive_bullets_id']),
                        'cta_text' => $item['cta_id'],
                        'hero_subtitle' => $item['hero_subtitle_id'],
                        'modules_heading' => $item['modules_heading_id'],
                        'modules' => $item['modules_id'],
                        'services_heading' => $item['services_heading_id'],
                        'services_subtitle' => $item['services_subtitle_id'],
                        'services_cards' => $item['services_cards_id'],
                    ],
                ],
            ];

            $entry->seo = [
                'title' => $item['title_en'].' Solutions | Converion Solution Informatica',
                'description' => $item['hero_subtitle_en'],
                '_translations' => [
                    'id' => [
                        'title' => 'Solusi '.$item['title_id'].' | Converion Solution Informatica',
                        'description' => $item['hero_subtitle_id'],
                    ],
                ],
            ];

            $entry->save();
            $this->command?->info("Synced solution: {$item['slug']} (#{$entry->id})");
        }

        $this->syncSolutionNavLinks();
    }

    protected function upsertPostTypeAndFields(): CustomPostType
    {
        $metaBoxes = [
            ['id' => 'overview_card', 'title' => 'Homepage Card & Hero Overview', 'context' => 'normal'],
            ['id' => 'modules_section', 'title' => 'Solution Modules & Related Products', 'context' => 'normal'],
            ['id' => 'services_section', 'title' => 'Related Services', 'context' => 'normal'],
        ];

        $cpt = CustomPostType::updateOrCreate(
            ['slug' => 'solutions'],
            [
                'name' => 'Solutions',
                'singular_label' => 'Solution',
                'plural_label' => 'Solutions',
                'description' => 'Enterprise IT solution pillars (Infrastructure, Cloud, Data & Platform, Security, AI).',
                'icon' => 'grid_view',
                'has_archive' => true,
                'publicly_queryable' => true,
                'show_in_menu' => true,
                'supports' => ['title', 'excerpt', 'thumbnail'],
                'is_active' => true,
                'translations' => [
                    'en' => [
                        'name' => 'Solutions',
                        'slug' => 'solutions',
                        'singular_label' => 'Solution',
                        'plural_label' => 'Solutions',
                    ],
                    'id' => [
                        'name' => 'Solusi',
                        'slug' => 'solusi',
                        'singular_label' => 'Solusi',
                        'plural_label' => 'Solusi',
                    ],
                ],
                'settings' => [
                    'meta_boxes' => $metaBoxes,
                ],
            ]
        );

        $fields = [
            [
                'name' => 'subtitle',
                'label' => 'Card Subtitle',
                'type' => 'text',
                'field_group' => 'overview_card',
                'description' => 'Short summary displayed on homepage card and mega menu',
                'order' => 1,
            ],
            [
                'name' => 'archive_summary',
                'label' => 'Solutions Archive Intro',
                'type' => 'textarea',
                'field_group' => 'overview_card',
                'description' => 'Overview paragraph displayed inside the /solutions accordion item.',
                'order' => 2,
            ],
            [
                'name' => 'archive_bullets',
                'label' => 'Solutions Archive Highlights (One per line)',
                'type' => 'textarea',
                'field_group' => 'overview_card',
                'description' => 'Bullet list displayed below the archive intro (use "Label: Description" per line).',
                'order' => 3,
            ],
            [
                'name' => 'cta_text',
                'label' => 'Card CTA Text',
                'type' => 'text',
                'field_group' => 'overview_card',
                'description' => 'Example: Explore Infrastructure Solutions',
                'order' => 4,
            ],
            [
                'name' => 'icon',
                'label' => 'Category Icon',
                'type' => 'media',
                'field_group' => 'overview_card',
                'order' => 5,
            ],
            [
                'name' => 'accent_variant',
                'label' => 'Accent Color Variant',
                'type' => 'select',
                'field_group' => 'overview_card',
                'options' => [
                    'choices' => [
                        'cyan' => 'Cyan -> Blue',
                        'orange' => 'Orange',
                    ],
                ],
                'order' => 6,
            ],
            [
                'name' => 'hero_subtitle',
                'label' => 'Hero Description',
                'type' => 'textarea',
                'field_group' => 'overview_card',
                'description' => 'Full description displayed below the H1 heading on the detail page.',
                'order' => 7,
            ],
            [
                'name' => 'modules_image',
                'label' => 'Default Modules Showcase Image',
                'type' => 'media',
                'field_group' => 'overview_card',
                'order' => 8,
            ],
            [
                'name' => 'modules_heading',
                'label' => 'Modules Section Heading',
                'type' => 'text',
                'field_group' => 'modules_section',
                'description' => 'Example: Core Infrastructure Modules',
                'order' => 7,
            ],
            [
                'name' => 'modules',
                'label' => 'Solution Modules',
                'type' => 'repeater',
                'field_group' => 'modules_section',
                'order' => 8,
                'options' => [
                    'repeater_fields' => [
                        ['name' => 'title', 'label' => 'Module Title', 'type' => 'text'],
                        ['name' => 'description', 'label' => 'Module Description', 'type' => 'textarea'],
                        ['name' => 'image', 'label' => 'Module Image (Optional Override)', 'type' => 'media'],
                        [
                            'name' => 'related_products',
                            'label' => 'Related Products (Technology Alliances)',
                            'type' => 'relationship',
                            'target_post_type' => 'technology-alliances',
                            'cardinality' => 'many_to_many',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'services_heading',
                'label' => 'Related Services Heading',
                'type' => 'text',
                'field_group' => 'services_section',
                'description' => 'Example: Service Related to Infrastructure Solution',
                'order' => 9,
            ],
            [
                'name' => 'services_subtitle',
                'label' => 'Related Services Subtitle',
                'type' => 'textarea',
                'field_group' => 'services_section',
                'order' => 10,
            ],
            [
                'name' => 'services_cards',
                'label' => 'Service Cards',
                'type' => 'repeater',
                'field_group' => 'services_section',
                'order' => 11,
                'options' => [
                    'repeater_fields' => [
                        ['name' => 'icon', 'label' => 'Service Icon', 'type' => 'media'],
                        ['name' => 'title', 'label' => 'Service Title', 'type' => 'text'],
                        ['name' => 'description', 'label' => 'Summary Paragraph (Optional)', 'type' => 'textarea'],
                        ['name' => 'checklist_items', 'label' => 'Checklist Items (One per line)', 'type' => 'textarea'],
                    ],
                ],
            ],
        ];

        $activeNames = [];
        foreach ($fields as $f) {
            $activeNames[] = $f['name'];
            MetaField::updateOrCreate(
                [
                    'fieldable_type' => CustomPostType::class,
                    'fieldable_id' => $cpt->id,
                    'name' => $f['name'],
                ],
                [
                    'label' => $f['label'],
                    'type' => $f['type'],
                    'field_group' => $f['field_group'],
                    'description' => $f['description'] ?? null,
                    'options' => $f['options'] ?? null,
                    'is_required' => false,
                    'is_active' => true,
                    'order' => $f['order'],
                ]
            );
        }

        MetaField::where('fieldable_type', CustomPostType::class)
            ->where('fieldable_id', $cpt->id)
            ->whereNotIn('name', $activeNames)
            ->delete();

        return $cpt;
    }

    /**
     * Import local solution assets cleanly via MediaService (idempotent by original_filename).
     *
     * @return array<string, string> Map of filename => $media->path
     */
    protected function importSolutionMedia(): array
    {
        $dir = database_path('seeders/assets/solutions');
        $service = app(MediaService::class);
        $paths = [];

        foreach (glob($dir.'/*') ?: [] as $fullPath) {
            if (! is_file($fullPath)) {
                continue;
            }
            $basename = basename($fullPath);
            $existing = Media::where('original_filename', $basename)->latest('id')->first();

            if ($existing) {
                $paths[$basename] = $existing->path;

                continue;
            }

            $mime = str_ends_with($basename, '.svg') ? 'image/svg+xml' : (mime_content_type($fullPath) ?: 'image/png');
            $uploaded = new UploadedFile($fullPath, $basename, $mime, null, true);
            $media = $service->upload($uploaded, [
                'title' => pathinfo($basename, PATHINFO_FILENAME),
                'alt_text' => str_replace(['-', '.png', '.svg'], [' ', '', ''], $basename),
                'uploaded_by' => 1,
            ]);
            $paths[$basename] = $media->path;
        }

        return $paths;
    }

    /**
     * Update header/footer NavMenu links so Solutions & its 5 pillars point cleanly to the live CPT URLs.
     */
    protected function syncSolutionNavLinks(): void
    {
        if (! class_exists(NavMenu::class) || ! class_exists(NavMenuItem::class)) {
            return;
        }

        NavMenuItem::where('item_type', 'mega_solutions')
            ->get()
            ->each(function (NavMenuItem $item) {
                $trans = is_array($item->translations) ? $item->translations : [];
                $trans['id']['url'] = '/solusi';
                $item->update([
                    'url' => '/solutions',
                    'translations' => $trans,
                ]);
            });

        $footerPagesMenu = NavMenu::where('location', 'footer_pages')->first();
        if ($footerPagesMenu) {
            NavMenuItem::where('menu_id', $footerPagesMenu->id)
                ->where('title', 'Solutions')
                ->get()
                ->each(function (NavMenuItem $item) {
                    $trans = is_array($item->translations) ? $item->translations : [];
                    $trans['id']['url'] = '/solusi';
                    $item->update([
                        'url' => '/solutions',
                        'translations' => $trans,
                    ]);
                });
        }

        $footerSolutionsMenu = NavMenu::where('location', 'footer_solutions')->first();
        if ($footerSolutionsMenu) {
            $slugMap = [
                'Infrastructure' => ['/solutions/infrastructure', '/solusi/infrastruktur'],
                'Cloud' => ['/solutions/cloud', '/solusi/cloud'],
                'Data & Platform' => ['/solutions/data-platform', '/solusi/data-platform'],
                'Security' => ['/solutions/security', '/solusi/keamanan'],
                'Artificial Intelligence' => ['/solutions/artificial-intelligence', '/solusi/artificial-intelligence'],
            ];

            foreach ($slugMap as $title => [$enUrl, $idUrl]) {
                NavMenuItem::where('menu_id', $footerSolutionsMenu->id)
                    ->where('title', $title)
                    ->get()
                    ->each(function (NavMenuItem $item) use ($enUrl, $idUrl) {
                        $trans = is_array($item->translations) ? $item->translations : [];
                        $trans['id']['url'] = $idUrl;
                        $item->update([
                            'url' => $enUrl,
                            'translations' => $trans,
                        ]);
                    });
            }
        }
    }

    /**
     * Complete bilingual dataset for all 5 Solutions mapped strictly to Figma + technology-alliances slugs.
     *
     * @param  array<string, string>  $m
     * @return array<int, array<string, mixed>>
     */
    protected function solutionsPayload(array $m, callable $resolveVendors): array
    {
        $buildModules = static function (array $rows) use ($resolveVendors): array {
            $en = [];
            $id = [];
            foreach ($rows as $row) {
                $vendorIds = $resolveVendors($row['vendors']);
                $en[] = [
                    'title' => $row['title_en'],
                    'description' => $row['desc_en'],
                    'image' => $row['image'] ?? '',
                    'related_products' => $vendorIds,
                ];
                $id[] = [
                    'title' => $row['title_id'],
                    'description' => $row['desc_id'],
                    'image' => $row['image'] ?? '',
                    'related_products' => $vendorIds,
                ];
            }

            return [$en, $id];
        };

        $buildServices = static function (array $rows): array {
            $en = [];
            $id = [];
            foreach ($rows as $row) {
                $en[] = [
                    'icon' => $row['icon'],
                    'title' => $row['title_en'],
                    'description' => $row['desc_en'] ?? '',
                    'checklist_items' => implode("\n", $row['checks_en'] ?? []),
                ];
                $id[] = [
                    'icon' => $row['icon'],
                    'title' => $row['title_id'],
                    'description' => $row['desc_id'] ?? '',
                    'checklist_items' => implode("\n", $row['checks_id'] ?? []),
                ];
            }

            return [$en, $id];
        };

        [$infraModEn, $infraModId] = $buildModules([
            [
                'title_en' => 'Server and Storage Infrastructure',
                'title_id' => 'Infrastruktur Server dan Penyimpanan',
                'desc_en' => 'Modern compute fleets, HCI clusters, and high-density all-flash NVMe arrays tailored for mission-critical virtualization and cloud workloads.',
                'desc_id' => 'Armada komputasi modern, kluster HCI, dan array all-flash NVMe berdensitas tinggi yang dirancang untuk beban kerja virtualisasi dan cloud kritikal.',
                'vendors' => ['lenovo', 'penguin-solutions', 'asrock-rack', 'netapp', 'supermicro'],
            ],
            [
                'title_en' => 'Network Solutions',
                'title_id' => 'Solusi Jaringan',
                'desc_en' => 'Ultra-low latency spine-and-leaf fabrics, enterprise SD-WAN connectivity, zero-trust network microsegmentation, and high-throughput non-blocking switches.',
                'desc_id' => 'Fabrik spine-and-leaf berlatensi ultra-rendah, konektivitas SD-WAN enterprise, mikrosegmentasi jaringan zero-trust, dan switch non-blocking berkecepatan tinggi.',
                'vendors' => ['smartoptics', 'cambium-networks', 'cpacket-networks', 'edgecore-networks', 'extreme-networks', 'ip-infusion', 'ruijie-networks'],
            ],
            [
                'title_en' => 'Infrastructure Management & Monitoring',
                'title_id' => 'Manajemen & Pemantauan Infrastruktur',
                'desc_en' => 'Centralized full-stack IT observability, real-time automated hardware telemetry, proactive health alerts, and multi-vendor infrastructure management.',
                'desc_id' => 'Observabilitas TI full-stack terpusat, telemetri perangkat keras otomatis real-time, peringatan kesehatan proaktif, dan manajemen infrastruktur multi-vendor.',
                'vendors' => ['progress'],
            ],
            [
                'title_en' => 'Database Solutions',
                'title_id' => 'Solusi Basis Data',
                'desc_en' => 'High-performance relational and distributed enterprise database platforms, in-memory caching, auto-sharding, and zero-latency data querying.',
                'desc_id' => 'Platform basis data enterprise relasional dan terdistribusi berkinerja tinggi, caching in-memory, auto-sharding, dan kueri data berlatensi nol.',
                'vendors' => ['progress'],
            ],
            [
                'title_en' => 'Backup and Recovery',
                'title_id' => 'Pencadangan & Pemulihan',
                'desc_en' => 'Resilient disaster recovery architectures, air-gapped immutable ransomware vaults, synchronous multi-site replication, and near-zero RTO/RPO SLAs.',
                'desc_id' => 'Arsitektur pemulihan bencana tangguh, brankas ransomware immutable terisolasi, replikasi multi-situs sinkron, dan SLA RTO/RPO mendekati nol.',
                'vendors' => ['falconstor', 'nakivo', 'netapp'],
            ],
        ]);

        [$infraSvcEn, $infraSvcId] = $buildServices([
            [
                'icon' => $m['svc-technical-services.svg'] ?? '',
                'title_en' => 'Technical Services',
                'title_id' => 'Layanan Teknis',
                'checks_en' => [
                    'Server & HCI Deployment',
                    'Scale Out NAS & Object Storage Implementation',
                    'Virtualization Implementation',
                    'Hybrid Cloud Implementation',
                    'Backup & Recovery',
                    'VDI Implementation',
                ],
                'checks_id' => [
                    'Implementasi Server & HCI',
                    'Implementasi Scale Out NAS & Object Storage',
                    'Implementasi Virtualisasi',
                    'Implementasi Hybrid Cloud',
                    'Pencadangan & Pemulihan',
                    'Implementasi VDI',
                ],
            ],
            [
                'icon' => $m['svc-managed-services.svg'] ?? '',
                'title_en' => 'Managed Services',
                'title_id' => 'Layanan Terkelola',
                'checks_en' => [
                    'IT System Health Check & Audits',
                    'Dedicated Resident Engineers',
                    '24/7 Hardware & Network Telemetry',
                    'Automated Firmware & Patch Management',
                ],
                'checks_id' => [
                    'Audit & Pemeriksaan Kesehatan Sistem TI',
                    'Resident Engineer Khusus',
                    'Telemetri Perangkat Keras & Jaringan 24/7',
                    'Manajemen Firmware & Patch Otomatis',
                ],
            ],
            [
                'icon' => $m['svc-professional-services.svg'] ?? '',
                'title_en' => 'Professional Services',
                'title_id' => 'Layanan Profesional',
                'checks_en' => [
                    'IT System Assessment & Modernization',
                    'Multiplatform VM Migration',
                    'Container Platform Implementation',
                ],
                'checks_id' => [
                    'Asesmen & Modernisasi Sistem TI',
                    'Migrasi VM Multiplatform',
                    'Implementasi Platform Kontainer',
                ],
            ],
        ]);

        [$cloudModEn, $cloudModId] = $buildModules([
            [
                'title_en' => 'Infrastructure as a Service (IaaS)',
                'title_id' => 'Infrastructure as a Service (IaaS)',
                'desc_en' => 'On-demand elastic compute instances, high-throughput cloud storage, and virtual private networks with enterprise SLA availability.',
                'desc_id' => 'Instans komputasi elastis sesuai permintaan, penyimpanan cloud berkecepatan tinggi, dan jaringan privat virtual dengan jaminan SLA enterprise.',
                'vendors' => ['alibaba-cloud', 'digitalocean', 'oracle'],
            ],
            [
                'title_en' => 'Platform as a Service (PaaS)',
                'title_id' => 'Platform as a Service (PaaS)',
                'desc_en' => 'Managed Kubernetes clusters, serverless application runtimes, and automated CI/CD pipelines that accelerate modern software delivery.',
                'desc_id' => 'Kluster Kubernetes terkelola, runtime aplikasi serverless, dan pipeline CI/CD otomatis untuk mempercepat pengiriman perangkat lunak modern.',
                'vendors' => ['alibaba-cloud', 'digitalocean', 'byteplus'],
            ],
            [
                'title_en' => 'Software as a Service (SaaS)',
                'title_id' => 'Software as a Service (SaaS)',
                'desc_en' => 'Ready-to-use enterprise productivity suites, secure collaboration workspaces, and cloud-native business applications with predictable scaling.',
                'desc_id' => 'Aplikasi produktivitas enterprise siap pakai, ruang kerja kolaborasi aman, dan aplikasi bisnis cloud-native dengan skalabilitas terukur.',
                'vendors' => ['easishare', 'byteplus', 'progress'],
            ],
            [
                'title_en' => 'Database as a Service (DBaaS)',
                'title_id' => 'Database as a Service (DBaaS)',
                'desc_en' => 'Fully managed relational, NoSQL, and distributed cloud databases with automated backups, horizontal scaling, and multi-region failover.',
                'desc_id' => 'Basis data cloud relasional, NoSQL, dan terdistribusi yang dikelola penuh dengan pencadangan otomatis, penskalaan horizontal, dan failover multi-wilayah.',
                'vendors' => ['oracle', 'couchbase', 'oceanbase', 'alibaba-cloud', 'digitalocean'],
            ],
        ]);

        [$cloudSvcEn, $cloudSvcId] = $buildServices([
            [
                'icon' => $m['svc-cloud-migration.svg'] ?? '',
                'title_en' => 'Cloud Migration and Optimization',
                'title_id' => 'Migrasi & Optimasi Cloud',
                'desc_en' => 'Seamlessly transition and optimize your IT infrastructure for the cloud to enhance performance and reduce costs.',
                'desc_id' => 'Transisikan dan optimalkan infrastruktur TI Anda ke cloud secara mulus untuk meningkatkan performa dan menekan biaya operasional.',
            ],
            [
                'icon' => $m['svc-managed-services.svg'] ?? '',
                'title_en' => 'Professional or Managed Services',
                'title_id' => 'Layanan Profesional & Terkelola',
                'desc_en' => 'Comprehensive support and management services to ensure your cloud operations run smoothly and efficiently.',
                'desc_id' => 'Dukungan komprehensif dan layanan terkelola untuk memastikan operasional cloud Anda berjalan lancar dan efisien setiap saat.',
            ],
            [
                'icon' => $m['svc-consultation.svg'] ?? '',
                'title_en' => 'Consultation',
                'title_id' => 'Konsultasi Cloud',
                'desc_en' => 'Expert advice and strategic planning to help you leverage cloud technologies for maximum business impact.',
                'desc_id' => 'Panduan ahli dan perencanaan strategis untuk memanfaatkan teknologi cloud secara optimal demi dampak bisnis maksimal.',
            ],
        ]);

        [$dataModEn, $dataModId] = $buildModules([
            [
                'title_en' => 'Data Management',
                'title_id' => 'Manajemen Data',
                'desc_en' => 'Unified enterprise data governance, warehousing, and lifecycle management to keep structured and unstructured data accurate and accessible.',
                'desc_id' => 'Tata kelola data enterprise terpadu, data warehousing, dan manajemen siklus hidup data terstruktur maupun tidak terstruktur.',
                'vendors' => ['teradata', 'oracle', 'oceanbase', 'couchbase'],
            ],
            [
                'title_en' => 'Middleware & Application Integration',
                'title_id' => 'Middleware & Integrasi Aplikasi',
                'desc_en' => 'High-reliability messaging buses, API orchestration, and application middleware connecting legacy cores with modern digital services.',
                'desc_id' => 'Integrasi API, orkestrasi layanan, dan middleware berkeandalan tinggi yang menghubungkan sistem inti dengan layanan digital modern.',
                'vendors' => ['progress', 'oracle'],
            ],
            [
                'title_en' => 'Analytics & Business Intelligence',
                'title_id' => 'Analitik & Intelijen Bisnis',
                'desc_en' => 'Real-time operational analytics, predictive intelligence engines, and executive BI dashboards enabling faster data-driven decisions.',
                'desc_id' => 'Analitik operasional real-time, mesin intelijen prediktif, dan dasbor BI eksekutif untuk keputusan bisnis yang lebih cepat berbasis data.',
                'vendors' => ['teradata', 'i2-group', 'couchbase'],
            ],
            [
                'title_en' => 'Geographical Information System (GIS)',
                'title_id' => 'Sistem Informasi Geografis (GIS)',
                'desc_en' => 'Enterprise location intelligence, spatial mapping, and real-time geospatial analytics for logistics, utilities, and public sector operations.',
                'desc_id' => 'Intelijen lokasi enterprise, pemetaan spasial, dan analitik geospasial real-time untuk operasional logistik, utilitas, dan sektor publik.',
                'vendors' => ['esri'],
            ],
            [
                'title_en' => 'E-KYC & Digital Identity',
                'title_id' => 'E-KYC & Identitas Digital',
                'desc_en' => 'Automated identity verification, biometric onboarding, and strong multi-factor authentication compliant with regulatory standards.',
                'desc_id' => 'Verifikasi identitas otomatis, onboarding biometrik, dan autentikasi multi-faktor yang memenuhi standar regulasi industri.',
                'vendors' => ['i-sprint', 'byteplus'],
            ],
            [
                'title_en' => 'On-Premise Data Management',
                'title_id' => 'Manajemen Data On-Premise',
                'desc_en' => 'Low-latency on-premise storage clusters, sovereign data lakes, and high-availability database platforms under full internal control.',
                'desc_id' => 'Kluster penyimpanan on-premise berlatensi rendah dan platform basis data ketersediaan tinggi dengan kendali penuh di pusat data internal.',
                'vendors' => ['netapp', 'oracle', 'oceanbase'],
            ],
            [
                'title_en' => 'Cloud Data Management',
                'title_id' => 'Manajemen Data Cloud',
                'desc_en' => 'Unified hybrid cloud data pipelines, cross-cloud replication, and elastic analytics warehouses with optimized storage tiering.',
                'desc_id' => 'Pipeline data hybrid cloud terpadu, replikasi lintas cloud, dan gudang analitik elastis dengan optimasi tiering penyimpanan.',
                'vendors' => ['netapp', 'alibaba-cloud', 'teradata'],
            ],
            [
                'title_en' => 'Data Protection & Security',
                'title_id' => 'Perlindungan & Keamanan Data',
                'desc_en' => 'End-to-end encryption, granular access governance, and secure enterprise file collaboration protecting sensitive assets everywhere.',
                'desc_id' => 'Enkripsi menyeluruh, tata kelola akses terperinci, dan kolaborasi berkas aman untuk melindungi aset data sensitif.',
                'vendors' => ['easishare', 'imperva', 'thales'],
            ],
        ]);

        [$dataSvcEn, $dataSvcId] = $buildServices([
            [
                'icon' => $m['svc-data-implementation.svg'] ?? '',
                'title_en' => 'Implementation',
                'title_id' => 'Implementasi Data & Platform',
                'checks_en' => ['Expert installation', 'Integration', 'Migration', 'Maintenance Services'],
                'checks_id' => ['Instalasi oleh Tenaga Ahli', 'Integrasi Sistem', 'Migrasi Data', 'Layanan Pemeliharaan'],
            ],
            [
                'icon' => $m['svc-service-integration.svg'] ?? '',
                'title_en' => 'Service Integration & Management',
                'title_id' => 'Integrasi & Manajemen Layanan',
                'checks_en' => ['Seamless Coordination', 'Management Data Services'],
                'checks_id' => ['Koordinasi Lintas Layanan Mulus', 'Layanan Manajemen Data'],
            ],
            [
                'icon' => $m['svc-cloud-integration.svg'] ?? '',
                'title_en' => 'On-premise to Cloud Data Integration and Visualization',
                'title_id' => 'Integrasi & Visualisasi Data On-Premise ke Cloud',
                'checks_en' => ['Setup', 'Configuration', 'Define Data Policy', 'Optimization'],
                'checks_id' => ['Penyiapan Arsitektur', 'Konfigurasi Pipeline', 'Penetapan Kebijakan Data', 'Optimasi Performa'],
            ],
        ]);

        [$secModEn, $secModId] = $buildModules([
            [
                'title_en' => 'Infrastructure Security',
                'title_id' => 'Keamanan Infrastruktur',
                'desc_en' => 'Multi-layered perimeter firewalls, zero-trust browsing isolation, and continuous network telemetry protecting core enterprise systems.',
                'desc_id' => 'Firewall perimeter berlapis, isolasi penjelajahan zero-trust, dan telemetri jaringan berkelanjutan untuk melindungi sistem inti enterprise.',
                'vendors' => ['barracuda-networks', 'menlo-security', 'cpacket-networks'],
            ],
            [
                'title_en' => 'Data & File Security',
                'title_id' => 'Keamanan Data & Berkas',
                'desc_en' => 'Hardware-backed cryptography, enterprise file encryption, and tamper-resistant storage protecting confidential documents in transit and at rest.',
                'desc_id' => 'Kriptografi berbasis perangkat keras, enkripsi berkas enterprise, dan penyimpanan tahan manipulasi untuk dokumen rahasia.',
                'vendors' => ['thales', 'easishare', 'swissbit'],
            ],
            [
                'title_en' => 'Web Application Firewall',
                'title_id' => 'Web Application Firewall (WAF)',
                'desc_en' => 'Adaptive Layer-7 application protection blocking OWASP Top 10 exploits, malicious bots, and volumetric DDoS attacks with zero false friction.',
                'desc_id' => 'Perlindungan aplikasi Layer-7 adaptif terhadap eksploitasi OWASP Top 10, bot berbahaya, dan serangan DDoS volumetrik.',
                'vendors' => ['imperva', 'barracuda-networks'],
            ],
            [
                'title_en' => 'Unified Intelligent Platform',
                'title_id' => 'Platform Keamanan Terpadu & Cerdas',
                'desc_en' => 'Single-pane security operations correlating alerts, compliance posture, and automated incident workflows across hybrid environments.',
                'desc_id' => 'Operasional keamanan terpusat yang mengkorelasikan peringatan ancaman, kepatuhan, dan respons insiden otomatis lintas lingkungan hybrid.',
                'vendors' => ['progress', 'barracuda-networks', 'imperva'],
            ],
            [
                'title_en' => 'Network Detection and Response (NDR)',
                'title_id' => 'Network Detection and Response (NDR)',
                'desc_en' => 'Deep packet inspection and behavioral anomaly detection identifying lateral movement and zero-day threats inside east-west traffic.',
                'desc_id' => 'Inspeksi paket mendalam dan deteksi anomali perilaku untuk mengidentifikasi pergerakan lateral dan ancaman zero-day pada lalu lintas jaringan.',
                'vendors' => ['cpacket-networks', 'progress', 'extreme-networks'],
            ],
            [
                'title_en' => 'Data Security',
                'title_id' => 'Keamanan Data Menyeluruh',
                'desc_en' => 'Centralized key management, tokenization, and continuous data discovery aligned with UU PDP, GDPR, and PCI-DSS mandates.',
                'desc_id' => 'Manajemen kunci terpusat, tokenisasi, dan penemuan data sensitif yang selaras dengan kepatuhan UU PDP, GDPR, dan PCI-DSS.',
                'vendors' => ['thales', 'imperva', 'swissbit'],
            ],
            [
                'title_en' => 'Monitor and Manage Data Access',
                'title_id' => 'Pemantauan & Manajemen Akses Data',
                'desc_en' => 'Privileged Access Management (PAM), passwordless credentials, and session recording ensuring every administrative action is verified and audited.',
                'desc_id' => 'Privileged Access Management (PAM), kredensial tanpa kata sandi, dan perekaman sesi untuk audit penuh atas setiap akses istimewa.',
                'vendors' => ['axidian', 'versasec', 'i-sprint'],
            ],
            [
                'title_en' => 'Secure Administrative Control for Databases',
                'title_id' => 'Kontrol Administratif Aman untuk Basis Data',
                'desc_en' => 'Real-time database activity monitoring, privilege segregation, and SQL threat blocking protecting mission-critical records from insider or external misuse.',
                'desc_id' => 'Pemantauan aktivitas basis data real-time, pemisahan hak akses, dan pemblokiran ancaman SQL untuk melindungi data kritikal.',
                'vendors' => ['imperva', 'axidian', 'thales'],
            ],
        ]);

        [$secSvcEn, $secSvcId] = $buildServices([
            [
                'icon' => $m['svc-unified-platform.svg'] ?? '',
                'title_en' => 'Unified Intelligent Platform',
                'title_id' => 'Platform Cerdas Terpadu',
                'checks_en' => ['Implementation and Installation Network'],
                'checks_id' => ['Implementasi dan Instalasi Jaringan Terpadu'],
            ],
            [
                'icon' => $m['svc-ndr.svg'] ?? '',
                'title_en' => 'Network Detection and Response (NDR)',
                'title_id' => 'Network Detection and Response (NDR)',
                'checks_en' => ['Implementation and Installation'],
                'checks_id' => ['Implementasi dan Instalasi NDR'],
            ],
            [
                'icon' => $m['svc-data-security.svg'] ?? '',
                'title_en' => 'Data Security',
                'title_id' => 'Keamanan Data',
                'checks_en' => ['Data Assessment', 'Configure Data Encryption', 'Define Data Policy', 'Security Recommendation'],
                'checks_id' => ['Asesmen Data Sensitif', 'Konfigurasi Enkripsi Data', 'Penetapan Kebijakan Data', 'Rekomendasi Keamanan'],
            ],
            [
                'icon' => $m['svc-threat-detection.svg'] ?? '',
                'title_en' => 'Advanced Threat Detection Services',
                'title_id' => 'Layanan Deteksi Ancaman Lanjutan',
                'checks_en' => ['Security Vulnerabilities Assessment', 'Implementing Security Best Practices', 'Threat Monitoring'],
                'checks_id' => ['Asesmen Kerentanan Keamanan', 'Penerapan Praktik Keamanan Terbaik', 'Pemantauan Ancaman Berkelanjutan'],
            ],
            [
                'icon' => $m['svc-db-admin-control.svg'] ?? '',
                'title_en' => 'Strengthening Administrative Controls in Database',
                'title_id' => 'Penguatan Kontrol Administratif Basis Data',
                'checks_en' => ['Installation and Configuration of Security Tools', 'Administering Database Security Objects', 'Maintaining Database Security'],
                'checks_id' => ['Instalasi & Konfigurasi Perangkat Keamanan', 'Administrasi Objek Keamanan Basis Data', 'Pemeliharaan Keamanan Basis Data'],
            ],
        ]);

        [$aiModEn, $aiModId] = $buildModules([
            [
                'title_en' => 'AI-Ready Data Center',
                'title_id' => 'Pusat Data Siap AI',
                'desc_en' => 'High-density rack power, direct-liquid cooling, and modular compute pods engineered for large-scale AI training and enterprise inference workloads.',
                'desc_id' => 'Dukungan daya rak berkapasitas tinggi, pendinginan cair efisien, dan pod komputasi modular untuk pelatihan serta inferensi AI berskala enterprise.',
                'vendors' => ['supermicro', 'lenovo', 'penguin-solutions', 'asrock-rack'],
            ],
            [
                'title_en' => 'High-Speed Low-Latency Networking',
                'title_id' => 'Jaringan Berkecepatan Tinggi & Latensi Rendah',
                'desc_en' => '400G/800G RoCEv2 Ethernet fabrics and optical interconnects eliminating GPU communication bottlenecks across distributed compute clusters.',
                'desc_id' => 'Fabrik Ethernet 400G/800G dan interkoneksi optik untuk menghilangkan hambatan komunikasi antar kluster GPU terdistribusi.',
                'vendors' => ['smartoptics', 'cpacket-networks', 'edgecore-networks', 'ip-infusion'],
            ],
            [
                'title_en' => 'Modern Graphic Server Accelerator',
                'title_id' => 'Akselerator Server Grafis Modern',
                'desc_en' => 'Multi-GPU server nodes engineered for foundation model training, fine-tuning, computer vision, and accelerated high-concurrency LLM serving.',
                'desc_id' => 'Node server multi-GPU yang dirancang khusus untuk pelatihan model fondasi, fine-tuning, computer vision, dan inferensi LLM berkonkurensi tinggi.',
                'vendors' => ['supermicro', 'lenovo', 'asrock-rack'],
            ],
            [
                'title_en' => 'Hybrid Vector Database',
                'title_id' => 'Basis Data Vektor Hybrid',
                'desc_en' => 'Sub-millisecond embedding storage, hybrid semantic-keyword indexing, and Retrieval-Augmented Generation (RAG) backends at petabyte scale.',
                'desc_id' => 'Penyimpanan embedding sub-milidetik, pengindeksan semantik hybrid, dan backend Retrieval-Augmented Generation (RAG) berskala besar.',
                'vendors' => ['couchbase', 'teradata', 'oceanbase'],
            ],
            [
                'title_en' => 'Advanced Search Engine',
                'title_id' => 'Mesin Pencarian Lanjutan',
                'desc_en' => 'AI-assisted neural search, semantic document ranking, and enterprise knowledge discovery connecting unstructured repositories cleanly.',
                'desc_id' => 'Pencarian neural berbasis AI, pemeringkatan dokumen semantik, dan penemuan pengetahuan enterprise lintas repositori data.',
                'vendors' => ['couchbase', 'byteplus'],
            ],
            [
                'title_en' => 'In-Memory Database Acceleration',
                'title_id' => 'Akselerasi Basis Data In-Memory',
                'desc_en' => 'Memory-first caching and feature stores serving low-latency fraud scoring, personalization, and real-time inference features.',
                'desc_id' => 'Penyimpanan fitur dan caching berbasis memori untuk melayani skor penipuan real-time, personalisasi, dan inferensi berlatensi sangat rendah.',
                'vendors' => ['couchbase', 'oracle', 'oceanbase'],
            ],
            [
                'title_en' => 'Container Platform',
                'title_id' => 'Platform Kontainer AI',
                'desc_en' => 'Enterprise Kubernetes orchestration with GPU scheduling, multi-tenancy isolation, and reproducible MLOps runtime environments.',
                'desc_id' => 'Orkestrasi Kubernetes enterprise dengan penjadwalan GPU, isolasi multi-tenant, dan lingkungan runtime MLOps yang konsisten.',
                'vendors' => ['alibaba-cloud', 'digitalocean', 'netapp'],
            ],
            [
                'title_en' => 'Data Science Platform',
                'title_id' => 'Platform Sains Data',
                'desc_en' => 'Collaborative model experimentation, experiment tracking, automated feature pipelines, and governance from prototype to production.',
                'desc_id' => 'Ruang kerja eksperimen model kolaboratif, pelacakan eksperimen, pipeline fitur otomatis, dan tata kelola dari prototipe hingga produksi.',
                'vendors' => ['byteplus', 'teradata', 'alibaba-cloud'],
            ],
        ]);

        [$aiSvcEn, $aiSvcId] = $buildServices([
            [
                'icon' => $m['svc-ai-consulting.svg'] ?? '',
                'title_en' => 'AI Consulting & Readiness Assessment',
                'title_id' => 'Konsultasi AI & Asesmen Kesiapan',
                'desc_en' => 'Evaluate your IT infrastructure, data quality, and architecture readiness for structured, cost-effective enterprise AI adoption.',
                'desc_id' => 'Evaluasi kesiapan infrastruktur, data, dan arsitektur IT Anda untuk adopsi AI yang terarah dan hemat biaya.',
            ],
            [
                'icon' => $m['svc-ai-architecture.svg'] ?? '',
                'title_en' => 'Architecture Design & Implementation',
                'title_id' => 'Desain & Implementasi Arsitektur AI',
                'desc_en' => 'End-to-end AI infrastructure design and deployment—spanning GPU clusters, high-speed fabrics, and enterprise container platforms.',
                'desc_id' => 'Perancangan dan penggelaran (deployment) infrastruktur AI end-to-end—mulai dari kluster GPU, jaringan berkecepatan tinggi, hingga platform container.',
            ],
            [
                'icon' => $m['svc-ai-model-dev.svg'] ?? '',
                'title_en' => 'Model Development & Fine-Tuning',
                'title_id' => 'Pengembangan & Fine-Tuning Model',
                'desc_en' => 'Hands-on support building, fine-tuning, and optimizing machine learning and GenAI models tailored to your specific business use cases.',
                'desc_id' => 'Pendampingan pembuatan, penyesuaian (fine-tuning), dan optimasi model machine learning dan GenAI sesuai use case spesifik bisnis.',
            ],
            [
                'icon' => $m['svc-ai-mlops.svg'] ?? '',
                'title_en' => 'Data Pipeline & MLOps Enablement',
                'title_id' => 'Data Pipeline & Otomatisasi MLOps',
                'desc_en' => 'Automated data and model workflows covering ingestion, curation, training, deployment, and continuous production monitoring.',
                'desc_id' => 'Otomatisasi alur kerja data dari ingestion, pembersihan, training, hingga monitoring performa model di lingkungan produksi.',
            ],
            [
                'icon' => $m['svc-managed-services.svg'] ?? '',
                'title_en' => 'Managed Services & 24/7 Support',
                'title_id' => 'Layanan Terkelola & Dukungan 24/7',
                'desc_en' => 'Proactive telemetry, system maintenance, and continuous AI infrastructure optimization ensuring maximum production uptime.',
                'desc_id' => 'Pemantauan proaktif, pemeliharaan sistem, dan optimasi performa infrastruktur AI Anda guna menjamin ketersediaan (uptime) maksimal.',
            ],
        ]);

        return [
            [
                'slug' => 'infrastructure',
                'slug_id' => 'infrastruktur',
                'title_en' => 'Infrastructure',
                'title_id' => 'Infrastruktur',
                'subtitle_en' => 'Reliable servers, storage, and networks for your enterprise.',
                'subtitle_id' => 'Server, penyimpanan, dan jaringan andal untuk enterprise Anda.',
                'archive_summary_en' => 'Power critical workloads with high-performance computing, storage, networking, and data center solutions built for reliability and scale.',
                'archive_summary_id' => 'Dukung workload kritis dengan komputasi, storage, jaringan, dan data center berperforma tinggi untuk menjaga keandalan operasional.',
                'archive_bullets_en' => [
                    'Mission-Critical Computing & Storage: Enterprise servers and all-flash SAN storage with ultra-low latency (less than 1 ms).',
                    'Modern Networking: SD-WAN, modern network fabrics, and resilient switching with speeds up to 400GbE.',
                    'Data Center Modernization: Tier III/IV-standard redundancy and energy-efficient data center designs.',
                ],
                'archive_bullets_id' => [
                    'Mission-Critical Computing & Storage: Server enterprise dan all-flash SAN storage dengan latensi di bawah 1 ms.',
                    'Modern Networking: SD-WAN, modern network fabrics, dan switching andal hingga 400GbE.',
                    'Data Center Modernization: Redundansi berstandar Tier III/IV dan desain data center hemat energi.',
                ],
                'cta_en' => 'Explore Infrastructure Solutions',
                'cta_id' => 'Jelajahi Solusi Infrastruktur',
                'hero_subtitle_en' => 'Steadfast server, storage, network, management, and monitoring options to strengthen your business foundation.',
                'hero_subtitle_id' => 'Pilihan server, penyimpanan, jaringan, manajemen, dan pemantauan yang tangguh untuk memperkuat fondasi bisnis Anda.',
                'icon' => $m['solution-icon-infrastructure.svg'] ?? '',
                'accent_variant' => 'cyan',
                'modules_image' => $m['module-infrastructure.png'] ?? '',
                'modules_heading_en' => 'Core Infrastructure Modules',
                'modules_heading_id' => 'Modul Infrastruktur Inti',
                'modules_en' => $infraModEn,
                'modules_id' => $infraModId,
                'services_heading_en' => 'Service Related to Infrastructure Solution',
                'services_heading_id' => 'Layanan Terkait Solusi Infrastruktur',
                'services_subtitle_en' => 'End-to-end operational execution spanning technical integration, round-the-clock defense, and executive advisory.',
                'services_subtitle_id' => 'Eksekusi operasional menyeluruh mulai dari integrasi teknis, dukungan proaktif 24/7, hingga konsultasi arsitektur.',
                'services_cards_en' => $infraSvcEn,
                'services_cards_id' => $infraSvcId,
            ],
            [
                'slug' => 'cloud',
                'slug_id' => 'cloud',
                'title_en' => 'Cloud',
                'title_id' => 'Cloud',
                'subtitle_en' => 'Flexible, scalable, and efficient cloud services (IaaS, PaaS, SaaS).',
                'subtitle_id' => 'Layanan cloud fleksibel, skalabel, dan efisien (IaaS, PaaS, SaaS).',
                'archive_summary_en' => 'Unify public and private cloud environments with integrated IaaS, PaaS, SaaS, and managed multicloud solutions.',
                'archive_summary_id' => 'Kelola lingkungan public dan private cloud melalui solusi IaaS, PaaS, SaaS, dan multicloud terintegrasi.',
                'archive_bullets_en' => [
                    'Multicloud Orchestration: Coordinate cloud deployments with availability targets of up to 99.99% and flexible workload management.',
                    'Cloud Optimization: Automated scaling, FinOps cost governance, and Kubernetes container optimization.',
                    'Hybrid Cloud Integration: Seamless connectivity between on-premises private clouds and leading hyperscale public cloud platforms.',
                ],
                'archive_bullets_id' => [
                    'Multicloud Orchestration: Kelola deployment cloud dengan target ketersediaan hingga 99,99%.',
                    'Cloud Optimization: Penskalaan otomatis, pengendalian biaya FinOps, dan optimasi container Kubernetes.',
                    'Hybrid Cloud Integration: Integrasi private cloud on-premises dengan platform public cloud berskala global.',
                ],
                'cta_en' => 'Explore Cloud Solutions',
                'cta_id' => 'Jelajahi Solusi Cloud',
                'hero_subtitle_en' => 'Tailored cloud solutions for businesses seeking flexibility, scalability, and efficiency. Offering IaaS, PaaS, and SaaS.',
                'hero_subtitle_id' => 'Solusi cloud yang disesuaikan bagi bisnis yang mengutamakan fleksibilitas, skalabilitas, dan efisiensi melalui IaaS, PaaS, dan SaaS.',
                'icon' => $m['solution-icon-cloud.svg'] ?? '',
                'accent_variant' => 'cyan',
                'modules_image' => $m['module-cloud.png'] ?? '',
                'modules_heading_en' => 'Comprehensive Cloud Modules',
                'modules_heading_id' => 'Modul Cloud Komprehensif',
                'modules_en' => $cloudModEn,
                'modules_id' => $cloudModId,
                'services_heading_en' => 'Service Related to Cloud Solution',
                'services_heading_id' => 'Layanan Terkait Solusi Cloud',
                'services_subtitle_en' => 'Cloud migration, managed operations, and strategic advisory designed to maximize performance and cost governance.',
                'services_subtitle_id' => 'Layanan migrasi cloud, operasional terkelola, dan konsultasi strategis untuk memaksimalkan performa serta tata kelola biaya.',
                'services_cards_en' => $cloudSvcEn,
                'services_cards_id' => $cloudSvcId,
            ],
            [
                'slug' => 'data-platform',
                'slug_id' => 'data-platform',
                'title_en' => 'Data & Platform',
                'title_id' => 'Data & Platform',
                'subtitle_en' => 'Unlock the true value of your data and platforms.',
                'subtitle_id' => 'Optimalkan nilai sejati dari data dan platform Anda.',
                'archive_summary_en' => 'Connect enterprise systems, simplify data management, and turn complex data into real-time business insights.',
                'archive_summary_id' => 'Hubungkan sistem enterprise, optimalkan database, dan ubah data menjadi insight bisnis secara real-time.',
                'archive_bullets_en' => [
                    'Enterprise Integration: API gateways, integration platforms, and high-performance databases.',
                    'Real-Time Analytics: Modern data warehouse orchestration and real-time data processing.',
                    'Data Governance: Automated data pipelines, enterprise data governance, and privacy compliance.',
                ],
                'archive_bullets_id' => [
                    'Enterprise Integration: API gateway, platform integrasi, dan database berperforma tinggi.',
                    'Real-Time Analytics: Data warehouse modern dan pemrosesan data real-time untuk keputusan yang lebih cepat.',
                    'Data Governance: Otomatisasi alur data, tata kelola, dan kepatuhan privasi.',
                ],
                'cta_en' => 'Explore Data & Platform Solutions',
                'cta_id' => 'Jelajahi Solusi Data & Platform',
                'hero_subtitle_en' => 'Data Management Solutions helps you store, protect, and optimize your data across on-premises and cloud environments.',
                'hero_subtitle_id' => 'Solusi Manajemen Data membantu Anda menyimpan, melindungi, dan mengoptimalkan data di lingkungan on-premise maupun cloud.',
                'icon' => $m['solution-icon-data-platform.svg'] ?? '',
                'accent_variant' => 'cyan',
                'modules_image' => $m['module-data-platform.png'] ?? '',
                'modules_heading_en' => 'Comprehensive Solution Modules',
                'modules_heading_id' => 'Modul Solusi Komprehensif',
                'modules_en' => $dataModEn,
                'modules_id' => $dataModId,
                'services_heading_en' => 'Service Related to Data Management Solution',
                'services_heading_id' => 'Layanan Terkait Solusi Data & Platform',
                'services_subtitle_en' => 'Full-lifecycle data integration, analytics engineering, and managed platform governance across hybrid estates.',
                'services_subtitle_id' => 'Integrasi data menyeluruh, rekayasa analitik, dan manajemen platform data terkelola lintas infrastruktur hybrid.',
                'services_cards_en' => $dataSvcEn,
                'services_cards_id' => $dataSvcId,
            ],
            [
                'slug' => 'security',
                'slug_id' => 'keamanan',
                'title_en' => 'Security',
                'title_id' => 'Keamanan',
                'subtitle_en' => 'Protect your business against evolving digital threats.',
                'subtitle_id' => 'Lindungi bisnis Anda dari ancaman digital yang terus berkembang.',
                'archive_summary_en' => 'Secure your data, networks, and identities with integrated protection and proactive threat detection.',
                'archive_summary_id' => 'Lindungi data, jaringan, dan identitas dengan keamanan terintegrasi dan deteksi ancaman proaktif.',
                'archive_bullets_en' => [
                    'Advanced Threat Protection: XDR, Identity Governance, and Zero Trust Architecture (ZTA).',
                    'Security Operations: 24/7/365 SOC services and AI-powered threat detection and automated response.',
                    'Security & Compliance: ISO 27001 audits, vulnerability assessments, and security posture management.',
                ],
                'archive_bullets_id' => [
                    'Advanced Threat Protection: XDR, Identity Governance, dan Zero Trust Architecture (ZTA).',
                    'Security Operations: Layanan SOC 24/7/365, deteksi ancaman berbasis AI, dan respons otomatis.',
                    'Security & Compliance: Audit ISO 27001, asesmen kerentanan, dan pengelolaan postur keamanan.',
                ],
                'cta_en' => 'Explore Security Solutions',
                'cta_id' => 'Jelajahi Solusi Keamanan',
                'hero_subtitle_en' => 'Reliable security solutions for businesses. As a solution-centric service provider, we safeguard infrastructure, data, files, and web applications.',
                'hero_subtitle_id' => 'Solusi keamanan andal untuk bisnis modern. Sebagai penyedia layanan berbasis solusi, kami melindungi infrastruktur, data, berkas, dan aplikasi web.',
                'icon' => $m['solution-icon-security.svg'] ?? '',
                'accent_variant' => 'orange',
                'modules_image' => $m['module-security.png'] ?? '',
                'modules_heading_en' => 'Comprehensive Solution Modules',
                'modules_heading_id' => 'Modul Solusi Komprehensif',
                'modules_en' => $secModEn,
                'modules_id' => $secModId,
                'services_heading_en' => 'Service Related to Security Solution',
                'services_heading_id' => 'Layanan Terkait Solusi Keamanan',
                'services_subtitle_en' => 'Proactive threat detection, encryption governance, and database administrative protection built for regulatory confidence.',
                'services_subtitle_id' => 'Deteksi ancaman proaktif, tata kelola enkripsi, dan perlindungan administratif basis data untuk kepatuhan regulasi.',
                'services_cards_en' => $secSvcEn,
                'services_cards_id' => $secSvcId,
            ],
            [
                'slug' => 'artificial-intelligence',
                'slug_id' => 'artificial-intelligence',
                'title_en' => 'Artificial Intelligence',
                'title_id' => 'Artificial Intelligence',
                'subtitle_en' => 'Accelerate innovation with integrated AI infrastructure & platforms.',
                'subtitle_id' => 'Percepat inovasi dengan infrastruktur dan platform AI terintegrasi.',
                'archive_summary_en' => 'Turn AI capabilities into business outcomes with GPU-powered infrastructure, machine learning, and intelligent analytics.',
                'archive_summary_id' => 'Kembangkan kapabilitas AI melalui infrastruktur GPU, machine learning, dan analitik cerdas.',
                'archive_bullets_en' => [
                    'AI-Ready Infrastructure: AI-ready data centers, high-speed GPU acceleration, and hybrid vector databases.',
                    'AI & Automation: AI/ML model development, intelligent automation, and enterprise AI workflow integration.',
                    'AI Governance & Vision: Data governance, AI security, and computer vision for operational efficiency.',
                ],
                'archive_bullets_id' => [
                    'AI-Ready Infrastructure: Data center siap AI, akselerasi GPU, dan database vektor hybrid.',
                    'AI & Automation: Pengembangan model AI/ML, otomatisasi cerdas, dan integrasi AI enterprise.',
                    'AI Governance & Vision: Tata kelola data, keamanan AI, dan computer vision untuk efisiensi operasional.',
                ],
                'cta_en' => 'Explore AI Solutions',
                'cta_id' => 'Jelajahi Solusi AI',
                'hero_subtitle_en' => 'Build and deploy enterprise AI models faster with integrated GPU infrastructure, high-speed networking, and cloud-native container platforms.',
                'hero_subtitle_id' => 'Kembangkan dan terapkan model AI enterprise lebih cepat dengan infrastruktur GPU dan container terintegrasi.',
                'icon' => $m['solution-icon-ai.svg'] ?? '',
                'accent_variant' => 'orange',
                'modules_image' => $m['module-ai.png'] ?? '',
                'modules_heading_en' => 'Comprehensive Solution Modules',
                'modules_heading_id' => 'Modul Solusi Komprehensif',
                'modules_en' => $aiModEn,
                'modules_id' => $aiModId,
                'services_heading_en' => 'Service Related to Artificial Intelligence Solution',
                'services_heading_id' => 'Layanan Terkait Solusi Artificial Intelligence',
                'services_subtitle_en' => 'From readiness assessments and GPU cluster architecture to fine-tuning, MLOps automation, and 24/7 operational support.',
                'services_subtitle_id' => 'Dari asesmen kesiapan dan arsitektur kluster GPU hingga fine-tuning model, otomatisasi MLOps, dan dukungan operasional 24/7.',
                'services_cards_en' => $aiSvcEn,
                'services_cards_id' => $aiSvcId,
            ],
        ];
    }
}
