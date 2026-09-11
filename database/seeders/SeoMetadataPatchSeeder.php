<?php

namespace Database\Seeders;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Page;
use App\Models\SeoMeta;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SeoMetadataPatchSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting SeoMetadataPatchSeeder...');

        // 1. Homepage & Global Title / Tagline
        $this->command->info('1. Updating Homepage & Global Tagline...');
        Setting::set('seo_default_title', 'Trusted IT Consultant for Scalable and Secure Growth');
        Setting::set('site_tagline', 'Trusted IT Consultant for Scalable and Secure Growth');

        $homePage = Page::where('slug', 'home')->first();
        if ($homePage) {
            SeoMeta::updateOrCreate(
                ['seoable_type' => Page::class, 'seoable_id' => $homePage->id, 'locale' => ''],
                [
                    'title' => 'Trusted IT Consultant for Scalable and Secure Growth - Central Data Technology',
                    'description' => 'Drive business growth faster with Central Data Technology’s expert IT consultants and end-to-end IT solutions.',
                ]
            );

            SeoMeta::updateOrCreate(
                ['seoable_type' => Page::class, 'seoable_id' => $homePage->id, 'locale' => 'id'],
                [
                    'title' => 'Konsultan IT Tepercaya untuk Pertumbuhan yang Skalabel dan Aman - Central Data Technology',
                    'description' => 'Tingkatkan pertumbuhan bisnis lebih cepat bersama konsultan IT andal dan solusi IT menyeluruh dari Central Data Technology.',
                ]
            );
        }

        // 2. Fix Slugs & Dynatrace Digital Experience (AKAR-4)
        $this->command->info('2. Fixing CPT Slugs & AKAR-4 Dynatrace Digital Experience...');
        $ias = CptEntry::where('slug', 'Identity-Access-Security')->first();
        if ($ias) {
            $ias->slug = 'identity-access-security';
            $trans = $ias->translations ?? [];
            $trans['id']['slug'] = 'identity-access-security';
            $ias->translations = $trans;
            $ias->save();
        }

        // Entry 618 is top-level /solution/infrastructure. Entry 623 is child /solution/observability/infrastructure-1.
        // The unique key (post_type_id, slug) prevents duplicate slugs in the same post_type, so infrastructure-1 is handled via 301 redirect.
        $infra1 = CptEntry::where('slug', 'infrastructure-1')->first();
        if ($infra1) {
            $trans = $infra1->translations ?? [];
            $trans['id']['slug'] = 'infrastructure-1';
            $infra1->translations = $trans;
            $infra1->save();
        }

        // Dynatrace Digital Experience (Entry 288) vs Zscaler Digital Experience (Entry 332)
        $dynaEntry = CptEntry::find(288);
        if ($dynaEntry) {
            $dynaEntry->slug = 'digital-experience';
            $trans = $dynaEntry->translations ?? [];
            $trans['id'] = array_merge($trans['id'] ?? [], [
                'slug' => 'pengalaman-digital',
                'title' => 'Pengalaman Digital',
                'content' => 'Hadirkan manajemen digital experience yang sempurna dengan real-user monitoring dan replay session.',
                'excerpt' => 'Hadirkan manajemen digital experience yang sempurna dengan real-user monitoring dan replay session.',
            ]);
            $dynaEntry->translations = $trans;
            $dynaEntry->save();
        }

        // 3. Restore 18 Indonesian Titles in cpt_entries (AKAR-3)
        $this->command->info('3. Restoring 18 Indonesian Titles in CPT Entries...');
        $idTitleRestorations = [
            'analisis-bisnis' => 'Analisis Bisnis',
            'keamanan-aplikasi' => 'Keamanan Aplikasi',
            'observabilitas-infrastruktur' => 'Observabilitas Infrastruktur',
            'pemantauan-kinerja-aplikasi' => 'Pemantauan Kinerja Aplikasi',
            'otomatisasi' => 'Otomatisasi',
            'keamanan-data' => 'Keamanan Data',
            'perencanaan-pemulihan-bencana' => 'Perencanaan Pemulihan Bencana',
            'layanan-pengaturan-data' => 'Layanan Pengaturan Data',
            'manajemen-perangkat-internet-of-things-iot' => 'Manajemen Perangkat Internet of Things (IoT)',
            'akses-internet-aman-zia' => 'Akses Internet Aman (ZIA)',
            'akses-pribadi-aman-zpa' => 'Akses Pribadi Aman (ZPA)',
            'pengalaman-digital-zdx' => 'Pengalaman Digital (ZDX)',
            'telekomunikasi-penyedia-layanan' => 'Telekomunikasi (Penyedia Layanan)',
            'advanced-web-application-firewall-waf' => 'Advanced Web Application Firewall (WAF)',
            'local-traffic-manager-ltm' => 'Local Traffic Manager (LTM)',
            'aplikasi-delivery-and-security-platform' => 'Application Delivery dan Security Platform untuk Solusi Keamanan AI',
            'okta-universal-directory' => 'Okta Universal Directory',
            'okta-lifecycle-management' => 'Okta Lifecycle Management',
        ];

        foreach ($idTitleRestorations as $slugKey => $restoredTitle) {
            $entry = CptEntry::where('slug', $slugKey)
                ->orWhereRaw('JSON_UNQUOTE(JSON_EXTRACT(translations, "$.id.slug")) = ?', [$slugKey])
                ->first();

            if ($entry) {
                $trans = $entry->translations ?? [];
                $trans['id']['title'] = $restoredTitle;
                $trans['id']['slug'] = $slugKey;
                $entry->translations = $trans;
                $entry->save();
            }
        }

        // 4. Archive Metadata for CPTs (AKAR-2)
        $this->command->info('4. Populating Archive Metadata for CPTs...');
        $cptArchiveMeta = [
            'customer-success' => [
                'en' => [
                    'title' => 'Customer Success Stories - Central Data Technology',
                    'description' => 'Explore how Central Data Technology helps leading enterprises accelerate digital transformation and optimize IT infrastructure.',
                ],
                'id' => [
                    'title' => 'Kisah Keberhasilan Pelanggan - Central Data Technology',
                    'description' => 'Pelajari bagaimana Central Data Technology membantu berbagai perusahaan terkemuka mempercepat transformasi digital dan infrastruktur IT.',
                ],
            ],
            'solution' => [
                'en' => [
                    'title' => 'IT Solutions - Central Data Technology',
                    'description' => 'Comprehensive enterprise IT solutions including cloud, security, data analytics, and modern infrastructure from Central Data Technology.',
                ],
                'id' => [
                    'title' => 'Solusi IT - Central Data Technology',
                    'description' => 'Solusi IT enterprise komprehensif mencakup cloud, keamanan, analitik data, dan infrastruktur modern dari Central Data Technology.',
                ],
            ],
            'industry' => [
                'en' => [
                    'title' => 'Industry Solutions - Central Data Technology',
                    'description' => 'Tailored enterprise technology solutions for finance, telecommunications, healthcare, manufacturing, and e-commerce.',
                ],
                'id' => [
                    'title' => 'Solusi Industri - Central Data Technology',
                    'description' => 'Solusi teknologi enterprise terkurasi untuk sektor perbankan, telekomunikasi, kesehatan, manufaktur, dan e-commerce.',
                ],
            ],
            'client-says' => [
                'en' => [
                    'title' => 'What Our Clients Say - Central Data Technology',
                    'description' => 'Client testimonials and feedback on Central Data Technology IT consulting and deployment services.',
                ],
                'id' => [
                    'title' => 'Testimoni Klien - Central Data Technology',
                    'description' => 'Testimoni dan ulasan klien atas layanan konsultasi dan implementasi IT dari Central Data Technology.',
                ],
            ],
        ];

        foreach ($cptArchiveMeta as $cptSlug => $metaLocales) {
            $cpt = CustomPostType::where('slug', $cptSlug)->first();
            if ($cpt) {
                SeoMeta::updateOrCreate(
                    ['seoable_type' => CustomPostType::class, 'seoable_id' => $cpt->id, 'locale' => ''],
                    [
                        'title' => $metaLocales['en']['title'],
                        'description' => $metaLocales['en']['description'],
                    ]
                );

                SeoMeta::updateOrCreate(
                    ['seoable_type' => CustomPostType::class, 'seoable_id' => $cpt->id, 'locale' => 'id'],
                    [
                        'title' => $metaLocales['id']['title'],
                        'description' => $metaLocales['id']['description'],
                    ]
                );
            }
        }

        // 5. Special Pages (Video, Blog-News, Contact)
        $this->command->info('5. Updating Special Pages (Video, Blog-News, Contact)...');
        $videoPage = Page::where('slug', 'video')->first();
        if ($videoPage) {
            SeoMeta::updateOrCreate(
                ['seoable_type' => Page::class, 'seoable_id' => $videoPage->id, 'locale' => ''],
                [
                    'title' => 'Video Library - Central Data Technology',
                    'description' => 'Watch webinars, product demos, and expert tech discussions from Central Data Technology.',
                ]
            );
            SeoMeta::updateOrCreate(
                ['seoable_type' => Page::class, 'seoable_id' => $videoPage->id, 'locale' => 'id'],
                [
                    'title' => 'Pustaka Video - Central Data Technology',
                    'description' => 'Tonton webinar, demo produk, dan pembahasan teknologi dari para ahli Central Data Technology.',
                ]
            );
        }

        $contactPage = Page::where('slug', 'contact-us')->first();
        if ($contactPage) {
            SeoMeta::updateOrCreate(
                ['seoable_type' => Page::class, 'seoable_id' => $contactPage->id, 'locale' => ''],
                [
                    'title' => 'Contact Us - Central Data Technology',
                    'description' => 'Connect with Central Data Technology experts for comprehensive IT consulting, enterprise cloud, security, and digital infrastructure solutions.',
                ]
            );
            SeoMeta::updateOrCreate(
                ['seoable_type' => Page::class, 'seoable_id' => $contactPage->id, 'locale' => 'id'],
                [
                    'title' => 'Hubungi Kami - Central Data Technology',
                    'description' => 'Hubungi tim ahli Central Data Technology untuk konsultasi IT terpercaya, solusi cloud enterprise, keamanan, dan infrastruktur digital.',
                ]
            );
        }

        $blogPage = Page::where('slug', 'blog-news')->first();
        if (! $blogPage) {
            $blogPage = Page::create([
                'title' => 'Blog & News',
                'slug' => 'blog-news',
                'status' => 'published',
                'is_system' => true,
                'author_id' => \App\Models\User::first()?->id ?? 1,
                'translations' => [
                    'id' => [
                        'title' => 'Blog & Berita',
                        'slug' => 'blog-news',
                    ],
                ],
            ]);
        }
        if ($blogPage) {
            SeoMeta::updateOrCreate(
                ['seoable_type' => Page::class, 'seoable_id' => $blogPage->id, 'locale' => ''],
                [
                    'title' => 'Blog & News - Central Data Technology',
                    'description' => 'Stay updated with the latest IT insights, cloud computing news, cybersecurity trends, and technology best practices from Central Data Technology.',
                ]
            );
            SeoMeta::updateOrCreate(
                ['seoable_type' => Page::class, 'seoable_id' => $blogPage->id, 'locale' => 'id'],
                [
                    'title' => 'Blog & Berita - Central Data Technology',
                    'description' => 'Dapatkan wawasan IT terbaru, berita komputasi cloud, tren keamanan siber, dan praktik terbaik teknologi dari Central Data Technology.',
                ]
            );
        }

        // 6. Import 46 Commercial Meta Descriptions from JSON
        $this->command->info('6. Importing 46 Commercial Meta Descriptions from Production Snapshot...');
        $jsonFile = base_path('scripts/prod-with-description.json');
        if (file_exists($jsonFile)) {
            $descriptions = json_decode(file_get_contents($jsonFile), true);
            $importedCount = 0;

            foreach ($descriptions as $path => $item) {
                $urlPath = trim($path, '/');
                $isId = str_starts_with($urlPath, 'id/') || $urlPath === 'id';
                $locale = $isId ? 'id' : '';

                $cleanSlug = $urlPath;
                if ($isId) {
                    $cleanSlug = substr($cleanSlug, 3);
                }

                if ($cleanSlug === '' || $cleanSlug === false) {
                    continue; // Homepage handled above
                }

                // Explicit aliases for known URL divergences
                $slugAliases = [
                    'amazon-web-services-2' => 'amazon-web-services',
                    'netgain-application-performance-management-apm' => 'netgain-systems-netgain-apm',
                    'netgain-network-traffic-analytics-nta' => 'netgain-systems-netgain-nta',
                    'netgain-network-configuration-monitoring-ncm' => 'netgain-systems-netgain-ncm',
                    'netgain-security-analytics-siem' => 'netgain-systems-netgain-siem',
                ];

                if (isset($slugAliases[$cleanSlug])) {
                    $cleanSlug = $slugAliases[$cleanSlug];
                }

                // If path is a single slug (e.g. 'akamai', 'f5')
                $segments = explode('/', $cleanSlug);
                $entity = null;

                if (count($segments) === 1) {
                    // Could be alliance CPT entry or Page
                    $entity = CptEntry::where('slug', $segments[0])
                        ->orWhereRaw('JSON_UNQUOTE(JSON_EXTRACT(translations, "$.id.slug")) = ?', [$segments[0]])
                        ->first();

                    if (! $entity) {
                        $entity = Page::where('slug', $segments[0])
                            ->orWhereRaw('JSON_UNQUOTE(JSON_EXTRACT(translations, "$.id.slug")) = ?', [$segments[0]])
                            ->first();
                    }
                } elseif (count($segments) >= 2) {
                    // Could be tech-product or sub-solution
                    $productSlug = end($segments);
                    $entity = CptEntry::where('slug', $productSlug)
                        ->orWhereRaw('JSON_UNQUOTE(JSON_EXTRACT(translations, "$.id.slug")) = ?', [$productSlug])
                        ->first();
                }

                if ($entity) {
                    SeoMeta::updateOrCreate(
                        [
                            'seoable_type' => get_class($entity),
                            'seoable_id' => $entity->id,
                            'locale' => $locale,
                        ],
                        [
                            'title' => $item['title'],
                            'description' => $item['description'],
                        ]
                    );
                    $importedCount++;
                }
            }

            $this->command->info("Successfully matched and saved {$importedCount} meta descriptions to seo_meta!");
        }

        $this->command->info('SeoMetadataPatchSeeder completed successfully.');
    }
}
