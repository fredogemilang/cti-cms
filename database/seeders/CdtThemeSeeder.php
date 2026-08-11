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
                'translations' => [
                    'en' => ['singular_label' => 'Product / Alliance', 'plural_label' => 'Products & Alliances', 'slug' => 'products', 'description' => 'Global technology partners and solution products'],
                    'id' => ['singular_label' => 'Product / Alliance', 'plural_label' => 'Products & Alliances', 'slug' => 'products', 'description' => 'Global technology partners and solution products'],
                ],
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
                'translations' => [
                    'en' => ['singular_label' => 'Solution', 'plural_label' => 'Solutions', 'slug' => 'solutions', 'description' => 'Comprehensive IT solutions for enterprise digital transformation'],
                    'id' => ['singular_label' => 'Solution', 'plural_label' => 'Solutions', 'slug' => 'solutions', 'description' => 'Comprehensive IT solutions for enterprise digital transformation'],
                ],
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
                'translations' => [
                    'en' => ['singular_label' => 'Customer Story', 'plural_label' => 'Customer Success Stories', 'slug' => 'customer-success', 'description' => 'Case studies and customer success testimonials'],
                    'id' => ['singular_label' => 'Customer Story', 'plural_label' => 'Customer Success Stories', 'slug' => 'customer-success', 'description' => 'Case studies and customer success testimonials'],
                ],
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

        PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'name' => 'hero_title'],
            [
                'type' => 'title',
                'label' => 'Hero Title',
                'value' => json_encode(['prefix' => 'Speed Up Your', 'main' => 'Transformation Journey']),
                'order' => 0
            ]
        );

        PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'name' => 'hero_subtitle'],
            ['type' => 'textarea', 'label' => 'Hero Subtitle', 'value' => 'Accelerate IT transformation journey with our end-to-end expertise, from strategy to execution across cloud, security, and observability.', 'order' => 1]
        );

        PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'name' => 'hero_cta'],
            [
                'type' => 'button',
                'label' => 'Learn More Button',
                'value' => json_encode(['text' => 'Learn More', 'url' => '#areas-of-expertise', 'target' => '_self']),
                'order' => 2
            ]
        );

        PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'name' => 'expertise_title'],
            [
                'type' => 'title',
                'label' => 'Area of Expertise Title',
                'value' => json_encode(['prefix' => 'Area Of', 'main' => 'Expertise']),
                'order' => 3
            ]
        );

        $expertiseBlock = PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'name' => 'expertise_list'],
            [
                'type' => 'repeater',
                'label' => 'Area of Expertise Cards',
                'value' => json_encode([
                    [
                        'image' => 'themes/cdt/assets/security-DrNRARC-.webp',
                        'title' => 'Security',
                        'description' => "In the modern environment, it's essential for businesses to work together to ensure applications are secure. CDT's security solutions allow you to take a preventative approach against cyber threats by helping you keep tabs on potential weak spots, reduce impact in the event of an attack, and build a more powerful defense to keep your most critical assets secure. Additionally, you can tailor our security solutions to fit your specific requirements."
                    ],
                    [
                        'image' => 'themes/cdt/assets/clouds.png-Doka7eSJ.webp',
                        'title' => 'Cloud',
                        'description' => "Cloud technology opens the door to new innovations, promoting emerging markets like cloud-native development. CDT is a cloud expert with certified teams, so we see this as an opportunity to help businesses reap the benefits of the cloud by providing a variety of cloud-based solutions. Our competence has also earned the reputation of AWS Advanced Consulting Partner of the year 2022, AWS Security Expert, AWS Migration consultant, AWS Infrastructure provider, AWS Analytics, and AWS DevOps specialty."
                    ],
                    [
                        'image' => 'themes/cdt/assets/analytics.png-Bdc2CvaB.webp',
                        'title' => 'Observability',
                        'description' => "Observability in IT refers to the practice of monitoring and analyzing system and application performance in real-time. It provides insight into the behavior and health of software systems, helping organizations detect and resolve issues quickly and effectively. CDT can help using observability in business and can ensure yours IT systems are performing optimally, identify and resolve problems before they impact customers, and improve overall reliability and customer satisfaction."
                    ]
                ]),
                'options' => [
                    'children' => [
                        ['name' => 'image', 'type' => 'media', 'label' => 'Card Image'],
                        ['name' => 'title', 'type' => 'text', 'label' => 'Card Title'],
                        ['name' => 'description', 'type' => 'textarea', 'label' => 'Card Description']
                    ]
                ],
                'order' => 4
            ]
        );

        PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'parent_block_id' => $expertiseBlock->id, 'name' => 'image'],
            ['type' => 'media', 'label' => 'Card Image', 'value' => '', 'order' => 0]
        );
        PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'parent_block_id' => $expertiseBlock->id, 'name' => 'title'],
            ['type' => 'text', 'label' => 'Card Title', 'value' => '', 'order' => 1]
        );
        PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'parent_block_id' => $expertiseBlock->id, 'name' => 'description'],
            ['type' => 'textarea', 'label' => 'Card Description', 'value' => '', 'order' => 2]
        );

        PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'name' => 'alliance_title'],
            [
                'type' => 'title',
                'label' => 'Technology Alliance Title',
                'value' => json_encode(['prefix' => 'Technology', 'main' => 'Alliance']),
                'order' => 5
            ]
        );

        PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'name' => 'aws_title'],
            [
                'type' => 'title',
                'label' => 'AWS Section Title',
                'value' => json_encode(['prefix' => 'AWS', 'main' => 'Private Offers']),
                'order' => 6
            ]
        );

        PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'name' => 'aws_offers_gallery'],
            [
                'type' => 'gallery',
                'label' => 'AWS Private Offers Gallery',
                'value' => json_encode([
                    'themes/cdt/assets/confluent-logo-1024x562-BFo8llUh.png',
                    'themes/cdt/assets/datadog-logo-1024x1024-BBaPl4Qq.png',
                    'themes/cdt/assets/PT-Urun-Bangun-Negeri-BLb9ARg2.png',
                    'themes/cdt/assets/GitLab-logo-BBxYVl-u.svg',
                    'themes/cdt/assets/Mongo-DB-Logo-0iY8tsMG.svg',
                    'themes/cdt/assets/tapway-logo-hd--DjdHTKHP.png'
                ]),
                'order' => 7
            ]
        );

        PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'name' => 'why_cdt_title'],
            [
                'type' => 'title',
                'label' => 'Why CDT Title',
                'value' => json_encode(['prefix' => 'Why', 'main' => 'CDT?']),
                'order' => 8
            ]
        );

        $whyCdtBlock = PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'name' => 'why_cdt_list'],
            [
                'type' => 'repeater',
                'label' => 'Why CDT Feature Boxes',
                'value' => json_encode([
                    [
                        'image' => 'themes/cdt/assets/photo-1573164713988-8665fc963095-w800-e1IoyY61.jpg',
                        'title' => 'NUMBER ONE IT SERVICE DELIVERY',
                        'description' => "Guarantee the best quality of IT service delivery with every stage delivery involves many IT experts' role and ensure that service-level agreement (SLA) is applied."
                    ],
                    [
                        'image' => 'themes/cdt/assets/photo-1522071820081-009f0129c71c-w800-D1mgrB8h.jpg',
                        'title' => 'EXCELLENT CUSTOMER SERVICES',
                        'description' => "24/7 customer response center, and many other convenient services were given fulfill customer requirement in today's digital era."
                    ],
                    [
                        'image' => 'themes/cdt/assets/photo-1552664730-d307ca884978-w800-DNfMnljE.jpg',
                        'title' => 'YEARS OF EXPERIENCE EXPERTS',
                        'description' => "With years of experience and numerous of project portfolios, professional IT experts will measure and manage risk to ensure accuracy in implementing solutions into customer's IT environment."
                    ]
                ]),
                'options' => [
                    'children' => [
                        ['name' => 'image', 'type' => 'media', 'label' => 'Box Background Image'],
                        ['name' => 'title', 'type' => 'text', 'label' => 'Box Title'],
                        ['name' => 'description', 'type' => 'textarea', 'label' => 'Box Description']
                    ]
                ],
                'order' => 9
            ]
        );

        PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'parent_block_id' => $whyCdtBlock->id, 'name' => 'image'],
            ['type' => 'media', 'label' => 'Box Background Image', 'value' => '', 'order' => 0]
        );
        PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'parent_block_id' => $whyCdtBlock->id, 'name' => 'title'],
            ['type' => 'text', 'label' => 'Box Title', 'value' => '', 'order' => 1]
        );
        PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'parent_block_id' => $whyCdtBlock->id, 'name' => 'description'],
            ['type' => 'textarea', 'label' => 'Box Description', 'value' => '', 'order' => 2]
        );

        PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'name' => 'testimonial_title'],
            [
                'type' => 'title',
                'label' => 'Testimonials Title',
                'value' => json_encode(['prefix' => 'What Our', 'main' => 'Client Says']),
                'order' => 10
            ]
        );

        PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'name' => 'blog_callout'],
            [
                'type' => 'card',
                'label' => 'Blog Callout Card',
                'value' => json_encode([
                    'title' => 'Blog, News & Video',
                    'description' => 'Explore our latest insights and news.',
                    'image' => 'themes/cdt/assets/photo-1551288049-bebda4e38f71-w1000-CbVNUoo0.jpg'
                ]),
                'order' => 11
            ]
        );

        PageBlock::updateOrCreate(
            ['page_id' => $homePage->id, 'name' => 'life_callout'],
            [
                'type' => 'card',
                'label' => 'Life at CDT Callout Card',
                'value' => json_encode([
                    'title' => 'Life at Central Data Technology',
                    'description' => 'Discover life and opportunities at CDT.',
                    'image' => 'themes/cdt/assets/photo-1522071820081-009f0129c71c-w1000-CEqXLUmA.jpg'
                ]),
                'order' => 12
            ]
        );
    }
}
