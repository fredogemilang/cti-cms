<?php

namespace Database\Seeders;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Database\Seeder;

class CdtThemeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create or Update CPT: products (Technology Alliance)
        $productsCpt = CustomPostType::updateOrCreate(
            ['slug' => 'products'],
            [
                'name' => 'Products & Technology Alliance',
                'singular_label' => 'Product / Alliance',
                'plural_label' => 'Products & Alliances',
                'description' => 'Global technology partners and solution products',
                'is_hierarchical' => false,
                'show_in_menu' => true,
                'show_in_rest' => true,
                'has_archive' => true,
                'publicly_queryable' => true,
                'is_active' => true,
            ]
        );

        // 2. Create or Update CPT: solutions (Solutions)
        $solutionsCpt = CustomPostType::updateOrCreate(
            ['slug' => 'solutions'],
            [
                'name' => 'Enterprise Solutions',
                'singular_label' => 'Solution',
                'plural_label' => 'Solutions',
                'description' => 'Comprehensive IT solutions for enterprise digital transformation',
                'is_hierarchical' => false,
                'show_in_menu' => true,
                'show_in_rest' => true,
                'has_archive' => true,
                'publicly_queryable' => true,
                'is_active' => true,
            ]
        );

        // 3. Create or Update CPT: customer-success
        $customerSuccessCpt = CustomPostType::updateOrCreate(
            ['slug' => 'customer-success'],
            [
                'name' => 'Customer Success Stories',
                'singular_label' => 'Customer Story',
                'plural_label' => 'Customer Success Stories',
                'description' => 'Case studies and customer success testimonials',
                'is_hierarchical' => false,
                'show_in_menu' => true,
                'show_in_rest' => true,
                'has_archive' => true,
                'publicly_queryable' => true,
                'is_active' => true,
            ]
        );

        // 4. Seed Products / Alliance Partners
        $productsData = [
            ['title' => 'Akamai', 'slug' => 'akamai', 'excerpt' => 'Leading content delivery network (CDN), cybersecurity, and cloud service provider.'],
            ['title' => 'Amazon Web Services', 'slug' => 'amazon-web-services', 'excerpt' => 'On-demand cloud computing platforms and APIs with pay-as-you-go pricing.'],
            ['title' => 'Dynatrace', 'slug' => 'dynatrace', 'excerpt' => 'AI-powered application performance management and enterprise observability platform.'],
            ['title' => 'Entrust', 'slug' => 'entrust', 'excerpt' => 'Trusted identity, payments, and data protection technology solutions.'],
            ['title' => 'F5', 'slug' => 'f5', 'excerpt' => 'Multi-cloud application security and delivery solutions for high availability.'],
            ['title' => 'Hitachi Vantara', 'slug' => 'hitachi-vantara', 'excerpt' => 'Enterprise data storage, hybrid cloud infrastructure, and digital solutions.'],
            ['title' => 'MicroStrategy', 'slug' => 'microstrategy', 'excerpt' => 'Enterprise analytics, business intelligence, and mobile software platform.'],
            ['title' => 'Zscaler', 'slug' => 'zscaler', 'excerpt' => 'Zero Trust exchange cloud security platform for hybrid and remote workforces.'],
        ];

        foreach ($productsData as $index => $item) {
            CptEntry::updateOrCreate(
                [
                    'post_type_id' => $productsCpt->id,
                    'slug' => $item['slug'],
                ],
                [
                    'title' => $item['title'],
                    'content' => "<p>{$item['excerpt']}</p>",
                    'excerpt' => $item['excerpt'],
                    'status' => 'published',
                    'menu_order' => $index,
                    'author_id' => 1,
                    'published_at' => now(),
                ]
            );
        }

        // 5. Seed Solutions
        $solutionsData = [
            [
                'title' => 'Analytics & Data Management',
                'slug' => 'analytics',
                'excerpt' => 'Turn massive enterprise data into actionable business intelligence with modern BI, data warehousing, and AI analytics.',
            ],
            [
                'title' => 'Cloud & Infrastructure Modernization',
                'slug' => 'cloud',
                'excerpt' => 'Build scalable hybrid and multi-cloud architectures with automated cloud governance and cost optimization.',
            ],
            [
                'title' => 'IT Infrastructure & Storage',
                'slug' => 'infrastructure',
                'excerpt' => 'High-performance compute, resilient storage arrays, and hyperconverged infrastructure built for mission-critical workloads.',
            ],
            [
                'title' => 'Observability & AIOps',
                'slug' => 'observability',
                'excerpt' => 'Full-stack observability providing end-to-end visibility, automated root cause analysis, and digital experience monitoring.',
            ],
            [
                'title' => 'Cybersecurity & Zero Trust',
                'slug' => 'security',
                'excerpt' => 'Comprehensive multi-layer security, cloud workload protection, IAM, and Zero Trust network access control.',
            ],
        ];

        foreach ($solutionsData as $index => $item) {
            CptEntry::updateOrCreate(
                [
                    'post_type_id' => $solutionsCpt->id,
                    'slug' => $item['slug'],
                ],
                [
                    'title' => $item['title'],
                    'content' => "<p>{$item['excerpt']}</p>",
                    'excerpt' => $item['excerpt'],
                    'status' => 'published',
                    'menu_order' => $index,
                    'author_id' => 1,
                    'published_at' => now(),
                ]
            );
        }

        // 6. Seed Customer Success Stories
        $customerSuccessData = [
            [
                'title' => 'National Bank Modernizes Hybrid Cloud Security with Zero Trust Architecture',
                'slug' => 'national-bank-zero-trust',
                'excerpt' => 'How Central Data Technology helped a Tier-1 financial institute achieve zero-downtime compliance and 40% performance gain.',
            ],
            [
                'title' => 'Retail Enterprise Scales E-Commerce Logistics with AI Observability',
                'slug' => 'retail-enterprise-observability',
                'excerpt' => 'Instant incident response and seamless customer journey during peak shopping season.',
            ],
        ];

        foreach ($customerSuccessData as $index => $item) {
            CptEntry::updateOrCreate(
                [
                    'post_type_id' => $customerSuccessCpt->id,
                    'slug' => $item['slug'],
                ],
                [
                    'title' => $item['title'],
                    'content' => "<p>{$item['excerpt']}</p>",
                    'excerpt' => $item['excerpt'],
                    'status' => 'published',
                    'menu_order' => $index,
                    'author_id' => 1,
                    'published_at' => now(),
                ]
            );
        }

        // 7. Update Homepage Page Record & Blocks
        $homePage = Page::updateOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'template' => 'home',
                'status' => 'published',
                'author_id' => 1,
                'is_system' => true,
                'published_at' => now(),
            ]
        );

        // Seed page blocks
        PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'name' => 'hero_title'],
            ['value' => 'Trusted IT Consultant for Scalable and Secure Growth', 'order' => 0]
        );

        PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'name' => 'hero_subtitle'],
            ['value' => 'Empowering business transformation through world-class IT solutions and strategic technology alliances.', 'order' => 1]
        );
    }
}
