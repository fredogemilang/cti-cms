<?php

namespace Database\Seeders;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Page;
use App\Models\Form;
use App\Models\FormField;
use App\Models\User;
use App\Models\Theme;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class XyoraThemeSeeder extends Seeder
{
    public function run(): void
    {
        // Find or create a default user to satisfy foreign key constraints
        $admin = User::first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Administrator',
                'email' => 'admin@xyora-indonesia.com',
                'password' => bcrypt('admin123'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]);
        }

        // Assign administrator role to the admin user
        if (class_exists(\App\Models\Role::class)) {
            $adminRole = \App\Models\Role::where('slug', 'administrator')->first();
            if ($adminRole && !$admin->roles()->where('role_id', $adminRole->id)->exists()) {
                $admin->roles()->attach($adminRole->id);
            }
        }

        $authorId = $admin->id;

        // 1. Create or Update CPT: products
        $productsCpt = CustomPostType::updateOrCreate(
            ['slug' => 'products'],
            [
                'name' => 'Products',
                'singular_label' => 'Product',
                'plural_label' => 'Products',
                'description' => 'XYORA Network Products',
                'is_hierarchical' => true,
                'show_in_menu' => true,
                'show_in_rest' => true,
                'has_archive' => true,
                'publicly_queryable' => true,
                'is_active' => true,
                'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'author'],
                'settings' => [
                    'meta_boxes' => [
                        [
                            'id' => 'product_details',
                            'title' => 'Product Details',
                            'context' => 'normal',
                        ],
                    ],
                ],
                'translations' => [
                    'id' => [
                        'singular_label' => 'Produk',
                        'plural_label' => 'Produk',
                        'description' => 'Perangkat Jaringan XYORA'
                    ],
                    'en' => [
                        'singular_label' => 'Product',
                        'plural_label' => 'Products',
                        'description' => 'XYORA Network Products'
                    ]
                ]
            ]
        );

        // Register CPT MetaFields for products
        $productsCpt->metaFields()->updateOrCreate(
            ['name' => 'model_code'],
            [
                'label' => 'Model Code',
                'type' => 'text',
                'field_group' => 'product_details',
                'order' => 1,
            ]
        );

        $productsCpt->metaFields()->updateOrCreate(
            ['name' => 'badge'],
            [
                'label' => 'Badge (e.g. New)',
                'type' => 'text',
                'field_group' => 'product_details',
                'order' => 2,
            ]
        );

        $productsCpt->metaFields()->updateOrCreate(
            ['name' => 'features'],
            [
                'label' => 'Features List',
                'type' => 'repeater',
                'field_group' => 'product_details',
                'order' => 3,
                'options' => [
                    'repeater_fields' => [
                        [
                            'name' => 'feature',
                            'label' => 'Feature Description',
                            'type' => 'text'
                        ]
                    ]
                ]
            ]
        );

        $productsCpt->metaFields()->updateOrCreate(
            ['name' => 'specs'],
            [
                'label' => 'Specifications Table',
                'type' => 'repeater',
                'field_group' => 'product_details',
                'order' => 4,
                'options' => [
                    'repeater_fields' => [
                        [
                            'name' => 'key',
                            'label' => 'Spec Name',
                            'type' => 'text'
                        ],
                        [
                            'name' => 'value',
                            'label' => 'Value',
                            'type' => 'text'
                        ]
                    ]
                ]
            ]
        );

        $productsCpt->metaFields()->updateOrCreate(
            ['name' => 'datasheet_link'],
            [
                'label' => 'Datasheet Download Link',
                'type' => 'text',
                'field_group' => 'product_details',
                'order' => 5,
            ]
        );

        $productsCpt->metaFields()->updateOrCreate(
            ['name' => 'applications'],
            [
                'label' => 'Product Applications',
                'type' => 'repeater',
                'field_group' => 'product_details',
                'order' => 6,
                'options' => [
                    'repeater_fields' => [
                        [
                            'name' => 'title',
                            'label' => 'Application Title',
                            'type' => 'text'
                        ],
                        [
                            'name' => 'image',
                            'label' => 'Image',
                            'type' => 'media'
                        ],
                        [
                            'name' => 'url',
                            'label' => 'Target URL',
                            'type' => 'text'
                        ]
                    ]
                ]
            ]
        );

        $productsCpt->metaFields()->updateOrCreate(
            ['name' => 'gallery'],
            [
                'label' => 'Product Gallery',
                'type' => 'gallery',
                'field_group' => 'product_details',
                'order' => 7,
            ]
        );

        // Clean up old category CPT entries permanently
        CptEntry::where('post_type_id', $productsCpt->id)
            ->whereIn('slug', ['access-point', 'gateway', 'switch', 'access-point-1', 'gateway-1', 'switch-1'])
            ->forceDelete();

        // 2. Define Category Data (used for Custom Taxonomy Terms and Pages)
        $categoriesData = [
            [
                'title' => 'Wireless Access Point',
                'slug' => 'access-point',
                'excerpt' => 'Perluas jangkauan jaringan anda dengan Access Point Wi-Fi 6 & 7.',
                'content' => 'Dirancang untuk stabilitas tinggi di lingkungan padat dari SMB hingga Enterprise.',
                'featured_image' => 'themes/xyora/assets/images/main-product1.png',
                'menu_order' => 0
            ],
            [
                'title' => 'Smart Gateway',
                'slug' => 'gateway',
                'excerpt' => 'Keandalan perutean data dengan dukungan manajemen access point terpusat.',
                'content' => 'Kelola seluruh infrastruktur jaringan Anda secara mudah, aman, dan efisien.',
                'featured_image' => 'themes/xyora/assets/images/main-product2.png',
                'menu_order' => 1
            ],
            [
                'title' => 'Switch',
                'slug' => 'switch',
                'excerpt' => 'Keandalan transmisi data Gigabit dengan dukungan PoE pintar.',
                'content' => 'Integrasi mudah, performa andal, dan pasokan daya efisien untuk seluruh perangkat.',
                'featured_image' => 'themes/xyora/assets/images/main-product3.png',
                'menu_order' => 2
            ]
        ];

        // Seed Custom Taxonomy 'product_category' and attach to 'products' CPT
        $productCategoryTax = \App\Models\CustomTaxonomy::updateOrCreate(
            ['slug' => 'product_category'],
            [
                'name' => 'Product Categories',
                'singular_label' => 'Product Category',
                'plural_label' => 'Product Categories',
                'is_hierarchical' => true,
                'show_in_menu' => true,
                'show_in_rest' => true,
                'is_active' => true,
                'post_types' => ['products'],
            ]
        );

        $terms = [];
        foreach ($categoriesData as $cat) {
            $terms[$cat['slug']] = \App\Models\TaxonomyTerm::updateOrCreate(
                [
                    'taxonomy_id' => $productCategoryTax->id,
                    'slug' => $cat['slug'],
                ],
                [
                    'name' => $cat['title'],
                    'description' => $cat['excerpt'],
                    'order' => $cat['menu_order'],
                    'meta' => [
                        'featured_image' => $cat['featured_image'],
                        'content' => $cat['content'],
                    ]
                ]
            );
        }

        // 3. Seed Child Product Entries (with Category parent_id)
        $subProductsData = [
            // Under Wireless Access Point
            [
                'parent_slug' => 'access-point',
                'title' => 'Ceiling Wireless Access Point XA-CAP625-P',
                'slug' => 'ceiling-wireless-access-point',
                'excerpt' => 'WiFi 6 Ceiling Access Point untuk high-density deployment.',
                'content' => 'Ceiling Access Point dengan standard WiFi 6 dan dual band up to 3000Mbps.',
                'menu_order' => 0,
                'meta' => [
                    'model_code' => 'XA-CAP625-P',
                    'badge' => 'New',
                    'features' => [
                        ['feature' => 'WiFi 6 Dual-Band up to 3000 Mbps'],
                        ['feature' => 'Supports Up to 128 Devices'],
                        ['feature' => 'Powered by Qualcomm Chipset'],
                        ['feature' => 'Supports VLAN'],
                        ['feature' => '2×2 + 2×2 MIMO Technology'],
                        ['feature' => 'Adjustable Transmit Power'],
                        ['feature' => 'Flexible Ceiling or Wall Mount Design'],
                        ['feature' => 'Seamless Roaming'],
                        ['feature' => '2.5GbE Uplink and Gigabit LAN Port'],
                        ['feature' => 'High-power Wall Penetration'],
                        ['feature' => 'PoE and 12V DC Support'],
                        ['feature' => 'Cloud-Based Management'],
                        ['feature' => 'Fat and Thin AP Modes']
                    ],
                    'specs' => [
                        ['key' => 'Part Number', 'value' => 'XA-CAP625-P'],
                        ['key' => 'Standard', 'value' => 'WiFi6'],
                        ['key' => 'Class', 'value' => 'Commercial'],
                        ['key' => 'CPU', 'value' => 'IPQ5018+QCN6102+QCA8081'],
                        ['key' => 'Flash + RAM', 'value' => '8MB+128MB+512MB'],
                        ['key' => 'Radio', 'value' => '2.4G+5G'],
                        ['key' => 'Wireless Rate', 'value' => '3000Mbps'],
                        ['key' => 'Interface', 'value' => "1×2500Mbps WAN\n1×1000Mbps LAN"]
                    ],
                    'datasheet_link' => '#',
                    'applications' => [
                        ['title' => 'Hotel & Resort', 'image' => 'images/ap7.png', 'url' => '/usecase-hotel-resort'],
                        ['title' => 'Gedung Kantor', 'image' => 'images/ap8.png', 'url' => '/usecase-gedung-bertingkat'],
                        ['title' => 'Sekolah & Kampus', 'image' => 'images/ap9.png', 'url' => '/usecase-sekolah-kampus']
                    ],
                    'gallery' => [
                        'images/wifi1.png',
                        'images/wifi2.png',
                        'images/wifi3.png'
                    ]
                ]
            ],
            [
                'parent_slug' => 'access-point',
                'title' => 'In Wall Wifi 6 XA-IAP622-P',
                'slug' => 'wi-fi-6-in-wall-access-point',
                'excerpt' => 'In-Wall Access Point dengan performa stabil untuk kamar hotel atau kos.',
                'content' => 'Access Point standard WiFi 6 dual band up to 1800Mbps dengan standard 86-type wall mount.',
                'menu_order' => 1,
                'meta' => [
                    'model_code' => 'XA-IAP622-P',
                    'badge' => 'New',
                    'features' => [
                        ['feature' => 'WiFi 6 Dual-Band up to 1800 Mbps'],
                        ['feature' => 'Supports Up to 64 Devices'],
                        ['feature' => 'Standard 86-Type Wall Mount'],
                        ['feature' => 'Supports VLAN'],
                        ['feature' => '2×2 + 2×2 MIMO Technology'],
                        ['feature' => 'Seamless Roaming'],
                        ['feature' => '1×Gigabit Uplink and 2×Gigabit LAN Ports'],
                        ['feature' => 'PoE Support'],
                        ['feature' => 'Cloud-Based Management'],
                        ['feature' => 'Fat and Thin AP Modes']
                    ],
                    'specs' => [
                        ['key' => 'Part Number', 'value' => 'XA-IAP622-P'],
                        ['key' => 'Standard', 'value' => 'WiFi6'],
                        ['key' => 'Class', 'value' => 'Commercial'],
                        ['key' => 'CPU', 'value' => 'IPQ5010+QCN6102'],
                        ['key' => 'Flash + RAM', 'value' => '8MB+128MB+256MB'],
                        ['key' => 'Radio', 'value' => '2.4G+5G'],
                        ['key' => 'Wireless Rate', 'value' => '1800Mbps'],
                        ['key' => 'Interface', 'value' => '1×1000Mbps WAN, 2×1000Mbps LAN']
                    ],
                    'datasheet_link' => '#',
                    'applications' => [
                        ['title' => 'Workspace Eksklusif', 'image' => 'images/solusi8.png', 'url' => '/usecase-gedung-bertingkat'],
                        ['title' => 'Kamar Tamu', 'image' => 'images/solusi4.png', 'url' => '/usecase-hotel-resort']
                    ],
                    'gallery' => [
                        'images/wifi1.png',
                        'images/wifi2.png',
                        'images/wifi3.png'
                    ]
                ]
            ],
            [
                'parent_slug' => 'access-point',
                'title' => 'In Wall Wifi 7 XA-IAP641-P',
                'slug' => 'wi-fi-7-in-wall-access-point',
                'excerpt' => 'In-Wall Access Point dengan standard WiFi 7 tri-band premium.',
                'content' => 'WiFi 7 Tri-Band premium dengan multi-link operation untuk hunian eksklusif.',
                'menu_order' => 2,
                'meta' => [
                    'model_code' => 'XA-IAP641-P',
                    'badge' => 'New',
                    'features' => [
                        ['feature' => 'WiFi 7 Tri-Band up to 6400 Mbps'],
                        ['feature' => 'Supports Up to 256 Devices'],
                        ['feature' => 'Standard 86-Type Wall Mount'],
                        ['feature' => 'Supports VLAN'],
                        ['feature' => 'MLO (Multi-Link Operation)'],
                        ['feature' => 'Seamless Roaming'],
                        ['feature' => '1×2.5G Uplink and 2×Gigabit LAN Ports'],
                        ['feature' => 'PoE Support'],
                        ['feature' => 'Cloud-Based Management'],
                        ['feature' => 'Fat and Thin AP Modes']
                    ],
                    'specs' => [
                        ['key' => 'Part Number', 'value' => 'XA-IAP641-P'],
                        ['key' => 'Standard', 'value' => 'WiFi7'],
                        ['key' => 'Class', 'value' => 'Commercial'],
                        ['key' => 'CPU', 'value' => 'IPQ9574+QCN9274'],
                        ['key' => 'Flash + RAM', 'value' => '16MB+256MB+1GB'],
                        ['key' => 'Radio', 'value' => '2.4G+5G+6G'],
                        ['key' => 'Wireless Rate', 'value' => '6400Mbps'],
                        ['key' => 'Interface', 'value' => '1×2500Mbps WAN, 2×1000Mbps LAN']
                    ],
                    'datasheet_link' => '#',
                    'applications' => [
                        ['title' => 'Workspace Eksklusif', 'image' => 'images/solusi8.png', 'url' => '/usecase-gedung-bertingkat'],
                        ['title' => 'Kamar Tamu', 'image' => 'images/solusi4.png', 'url' => '/usecase-hotel-resort']
                    ],
                    'gallery' => [
                        'images/wifi1.png',
                        'images/wifi2.png',
                        'images/wifi4.png'
                    ]
                ]
            ],
            [
                'parent_slug' => 'access-point',
                'title' => 'Outdoor Access Point XA-OAP621-P',
                'slug' => 'outdoor-access-point',
                'excerpt' => 'Outdoor Access Point tahan cuaca ekstrem dengan sertifikasi IP65.',
                'content' => 'Access Point outdoor dual band WiFi 6 dengan jangkauan luas radius 100m.',
                'menu_order' => 3,
                'meta' => [
                    'model_code' => 'XA-OAP621-P',
                    'badge' => 'New',
                    'features' => [
                        ['feature' => 'WiFi 6 Dual-Band up to 1800 Mbps'],
                        ['feature' => 'Supports Up to 100+ Devices'],
                        ['feature' => 'IP65 Weatherproof Enclosure'],
                        ['feature' => 'PoE Support'],
                        ['feature' => 'Up to 100m Coverage Radius'],
                        ['feature' => 'Seamless Roaming'],
                        ['feature' => 'Cloud-Based Management'],
                        ['feature' => 'High-power Wall Penetration']
                    ],
                    'specs' => [
                        ['key' => 'Part Number', 'value' => 'XA-OAP621-P'],
                        ['key' => 'Standard', 'value' => 'WiFi6'],
                        ['key' => 'Class', 'value' => 'Commercial'],
                        ['key' => 'IP Rating', 'value' => 'IP65'],
                        ['key' => 'Coverage', 'value' => '100m Radius'],
                        ['key' => 'Radio', 'value' => '2.4G+5G'],
                        ['key' => 'Wireless Rate', 'value' => '1800Mbps']
                    ],
                    'datasheet_link' => '#',
                    'applications' => [
                        ['title' => 'Area Parkir & Rooftop', 'image' => 'images/solusi9.png', 'url' => '/usecase-gedung-bertingkat'],
                        ['title' => 'Kolam Renang & Outdoor', 'image' => 'images/solusi6.png', 'url' => '/usecase-hotel-resort']
                    ],
                    'gallery' => [
                        'images/wifi1.png',
                        'images/wifi2.png',
                        'images/wifi3.png'
                    ]
                ]
            ],
            // Under Smart Gateway
            [
                'parent_slug' => 'gateway',
                'title' => 'Smart Gateway XA-GW411S',
                'slug' => 'smart-gateway',
                'excerpt' => 'Gateway andal pengelola AP terpusat hingga 128 unit.',
                'content' => 'Multi-WAN Gateway Load Balancing dengan output PoE dan cloud remote management.',
                'menu_order' => 0,
                'meta' => [
                    'model_code' => 'XA-GW411S',
                    'badge' => 'New',
                    'features' => [
                        ['feature' => 'Manages Up to 128 Access Points'],
                        ['feature' => 'Dukungan Multi-WAN Load Balancing'],
                        ['feature' => 'Intelligent Bandwidth Control'],
                        ['feature' => 'Built-in Security Firewall'],
                        ['feature' => 'Cloud Remote Management'],
                        ['feature' => 'PoE Output Ports']
                    ],
                    'specs' => [
                        ['key' => 'Part Number', 'value' => 'XA-GW411S'],
                        ['key' => 'Class', 'value' => 'Commercial'],
                        ['key' => 'AP Management Capacity', 'value' => '128 APs'],
                        ['key' => 'Interface', 'value' => '4×Gigabit LAN (PoE), 1×Gigabit WAN']
                    ],
                    'datasheet_link' => '#',
                    'applications' => [
                        ['title' => 'Gedung Kantor', 'image' => 'images/ap8.png', 'url' => '/usecase-gedung-bertingkat'],
                        ['title' => 'Sekolah & Kampus', 'image' => 'images/ap9.png', 'url' => '/usecase-sekolah-kampus']
                    ],
                    'gallery' => [
                        'images/smart-gateway.png',
                        'images/ap7.png'
                    ]
                ]
            ],
            // Under Switch
            [
                'parent_slug' => 'switch',
                'title' => 'Switch PoE XA-SWG108-P',
                'slug' => 'poe-switch',
                'excerpt' => 'Smart PoE Switch dengan 8 Port PoE Gigabit dan 2 Port Uplink.',
                'content' => 'Smart PoE Switch gigabit dengan total budget 120W dan casing metal kuat fanless.',
                'menu_order' => 0,
                'meta' => [
                    'model_code' => 'XA-SWG108-P',
                    'badge' => 'New',
                    'features' => [
                        ['feature' => '8×Gigabit PoE Ports'],
                        ['feature' => '2×Gigabit Uplink Ports'],
                        ['feature' => 'IEEE 802.3af/at PoE Standards'],
                        ['feature' => 'Total PoE Budget: 120W'],
                        ['feature' => 'Smart Power Management'],
                        ['feature' => 'Metal Case, Fanless Design']
                    ],
                    'specs' => [
                        ['key' => 'Part Number', 'value' => 'XA-SWG108-P'],
                        ['key' => 'Class', 'value' => 'Commercial'],
                        ['key' => 'PoE Standard', 'value' => '802.3af/at'],
                        ['key' => 'PoE Budget', 'value' => '120W'],
                        ['key' => 'Interface', 'value' => '8×10/100/1000Mbps PoE, 2×10/100/1000Mbps Uplink']
                    ],
                    'datasheet_link' => '#',
                    'applications' => [
                        ['title' => 'Gedung Kantor', 'image' => 'images/ap8.png', 'url' => '/usecase-gedung-bertingkat'],
                        ['title' => 'Sekolah & Kampus', 'image' => 'images/ap9.png', 'url' => '/usecase-sekolah-kampus']
                    ],
                    'gallery' => [
                        'images/switch.png',
                        'images/ap8.png'
                    ]
                ]
            ]
        ];

        foreach ($subProductsData as $prod) {
            $parent = $categories[$prod['parent_slug']] ?? null;
            $entry = CptEntry::updateOrCreate(
                [
                    'post_type_id' => $productsCpt->id,
                    'slug' => $prod['slug'],
                ],
                [
                    'title' => $prod['title'],
                    'content' => $prod['content'],
                    'excerpt' => $prod['excerpt'],
                    'status' => 'published',
                    'menu_order' => $prod['menu_order'],
                    'parent_id' => $parent ? $parent->id : null,
                    'meta' => $prod['meta'],
                    'author_id' => $authorId,
                    'published_at' => now(),
                ]
            );

            // Link entry to its corresponding taxonomy term
            $term = $terms[$prod['parent_slug']] ?? null;
            if ($term) {
                \DB::table('cpt_entry_term')->updateOrInsert([
                    'entry_id' => $entry->id,
                    'term_id' => $term->id,
                ]);
            }
        }

        // 4. Seed Forms & Form Fields
        // A. Contact Form
        $contactForm = Form::updateOrCreate(
            ['slug' => 'contact-form'],
            [
                'name' => 'Contact Form',
                'description' => 'Xyora Main Contact Form',
                'is_active' => true,
                'form_type' => 'standard',
                'submit_button_text' => 'Kirim',
                'spam_protection' => [
                    'captcha_provider' => 'recaptcha_v2',
                    'honeypot' => true,
                ]
            ]
        );

        $contactFields = [
            ['field_id' => 'nama_lengkap', 'type' => 'text', 'label' => 'Nama Lengkap', 'is_required' => true, 'order' => 0],
            ['field_id' => 'nomor_telepon', 'type' => 'text', 'label' => 'Nomor Telepon', 'is_required' => true, 'order' => 1],
            ['field_id' => 'pribadi_perusahaan', 'type' => 'text', 'label' => 'Pribadi/Perusahaan', 'is_required' => true, 'order' => 2],
            ['field_id' => 'jabatan', 'type' => 'text', 'label' => 'Jabatan', 'is_required' => true, 'order' => 3],
            ['field_id' => 'alamat_email', 'type' => 'email', 'label' => 'Alamat Email', 'is_required' => true, 'order' => 4],
            ['field_id' => 'produk_yang_diminati', 'type' => 'text', 'label' => 'Produk yang Diminati', 'is_required' => true, 'order' => 5],
            ['field_id' => 'deskripsi_kebutuhan', 'type' => 'textarea', 'label' => 'Deskripsi Kebutuhan', 'is_required' => true, 'order' => 6]
        ];

        foreach ($contactFields as $f) {
            FormField::updateOrCreate(
                ['form_id' => $contactForm->id, 'field_id' => $f['field_id']],
                [
                    'type' => $f['type'],
                    'label' => $f['label'],
                    'is_required' => $f['is_required'],
                    'order' => $f['order'],
                ]
            );
        }

        // B. RMA Form
        $rmaForm = Form::updateOrCreate(
            ['slug' => 'rma-form'],
            [
                'name' => 'RMA Request Form',
                'description' => 'Xyora RMA Form',
                'is_active' => true,
                'form_type' => 'standard',
                'submit_button_text' => 'Kirim',
                'spam_protection' => [
                    'captcha_provider' => 'recaptcha_v2',
                    'honeypot' => true,
                ]
            ]
        );

        $rmaFields = [
            ['field_id' => 'nama_lengkap', 'type' => 'text', 'label' => 'Nama Lengkap', 'is_required' => true, 'order' => 0],
            ['field_id' => 'alamat_email', 'type' => 'email', 'label' => 'Alamat Email', 'is_required' => true, 'order' => 1],
            ['field_id' => 'serial_number_produk', 'type' => 'text', 'label' => 'Serial Number Produk', 'is_required' => true, 'order' => 2],
            ['field_id' => 'nama_produk', 'type' => 'text', 'label' => 'Nama Produk', 'is_required' => true, 'order' => 3],
            ['field_id' => 'alasan_pengajuan_rma', 'type' => 'text', 'label' => 'Alasan Pengajuan RMA', 'is_required' => true, 'order' => 4],
            ['field_id' => 'jenis_pengajuan', 'type' => 'text', 'label' => 'Jenis Pengajuan', 'is_required' => true, 'order' => 5],
            ['field_id' => 'jumlah_unit', 'type' => 'text', 'label' => 'Jumlah Unit', 'is_required' => true, 'order' => 6],
            ['field_id' => 'tanggal_pembelian', 'type' => 'text', 'label' => 'Tanggal Pembelian', 'is_required' => true, 'order' => 7],
            ['field_id' => 'bukti_pembelian', 'type' => 'text', 'label' => 'Bukti Pembelian', 'is_required' => true, 'order' => 8]
        ];

        foreach ($rmaFields as $f) {
            FormField::updateOrCreate(
                ['form_id' => $rmaForm->id, 'field_id' => $f['field_id']],
                [
                    'type' => $f['type'],
                    'label' => $f['label'],
                    'is_required' => $f['is_required'],
                    'order' => $f['order'],
                ]
            );
        }

        // 5. Seed Pages (matching templates)
        $pagesData = [
            ['title' => 'Home', 'slug' => 'home', 'template' => 'home'],
            ['title' => 'Tentang Kami', 'slug' => 'tentang', 'template' => 'about'],
            ['title' => 'Hubungi Kami', 'slug' => 'kontak', 'template' => 'contact'],
            ['title' => 'Sekolah & Kampus', 'slug' => 'usecase-sekolah-kampus', 'template' => 'usecase-sekolah-kampus'],
            ['title' => 'Hotel & Resort', 'slug' => 'usecase-hotel-resort', 'template' => 'usecase-hotel-resort'],
            ['title' => 'Gedung Bertingkat', 'slug' => 'usecase-gedung-bertingkat', 'template' => 'usecase-gedung-bertingkat'],
            ['title' => 'Wireless Access Point', 'slug' => 'access-point', 'template' => 'category', 'featured_image' => 'themes/xyora/assets/images/main-product1.png'],
            ['title' => 'Smart Gateway', 'slug' => 'gateway', 'template' => 'category', 'featured_image' => 'themes/xyora/assets/images/main-product2.png'],
            ['title' => 'Switch', 'slug' => 'switch', 'template' => 'category', 'featured_image' => 'themes/xyora/assets/images/main-product3.png']
        ];

        foreach ($pagesData as $p) {
            $page = Page::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'title' => $p['title'],
                    'template' => $p['template'],
                    'featured_image' => $p['featured_image'] ?? null,
                    'status' => 'published',
                    'author_id' => $authorId,
                    'published_at' => now(),
                ]
            );
            app(\App\Services\PageTemplateService::class)->seedBlocks($page);
        }

        // 6. Seed 9 Dummy Articles if posts table exists
        if (Schema::hasTable('posts')) {
            $articlesData = [
                [
                    'title' => 'Mengenal Wi-Fi 7: Masa Depan Konektivitas Nirkabel yang Super Cepat',
                    'slug' => 'mengenal-wi-fi-7-konektivitas-super-cepat',
                    'excerpt' => 'Wi-Fi 7 membawa kecepatan dan latensi super rendah untuk jaringan nirkabel modern.',
                    'content' => '<p>Teknologi Wi-Fi terus berkembang untuk mengimbangi pertumbuhan perangkat dan kebutuhan data yang semakin besar. Wi-Fi 7 hadir sebagai standard baru dengan kecepatan transfer data hingga beberapa kali lipat lebih cepat dibanding Wi-Fi 6, latency yang sangat rendah, dan stabilitas luar biasa menggunakan teknologi MLO (Multi-Link Operation).</p>'
                ],
                [
                    'title' => 'Pentingnya PoE Switch dalam Pemasangan Access Point Jaringan',
                    'slug' => 'pentingnya-poe-switch-pemasangan-access-point',
                    'excerpt' => 'Ketahui mengapa PoE (Power over Ethernet) Switch menjadi pilihan terbaik untuk efisiensi instalasi AP.',
                    'content' => '<p>Dalam instalasi infrastruktur jaringan modern, penggunaan PoE (Power over Ethernet) Switch telah menjadi standar industri. Dengan PoE, kabel Ethernet dapat mengalirkan data sekaligus daya listrik ke Access Point, sehingga mengeliminasi kebutuhan stopkontak listrik terpisah di atas plafon dan mengurangi biaya penarikan kabel.</p>'
                ],
                [
                    'title' => 'Cara Memilih Smart Gateway Terbaik untuk Kantor Skala Menengah',
                    'slug' => 'cara-memilih-smart-gateway-terbaik-kantor',
                    'excerpt' => 'Temukan panduan lengkap memilih Smart Gateway yang tepat sesuai kebutuhan lalu lintas data kantor Anda.',
                    'content' => '<p>Smart Gateway bertindak sebagai gerbang pengatur lalu lintas data serta otak dari manajemen Access Point. Bagi kantor dengan skala menengah (SMB), fitur krusial yang wajib ada adalah dukungan Multi-WAN Load Balancing untuk redundansi internet, manajemen bandwidth cerdas (QoS), dan terintegrasi dengan sistem keamanan firewall.</p>'
                ],
                [
                    'title' => 'Optimasi Jaringan Wi-Fi 6 di Area Padat Pengguna',
                    'slug' => 'optimasi-jaringan-wi-fi-6-area-padat-pengguna',
                    'excerpt' => 'Langkah praktis memaksimalkan kestabilan sinyal Wi-Fi 6 di lingkungan kantor, mall, atau kafe.',
                    'content' => '<p>Wi-Fi 6 membawa keunggulan di area padat pengguna berkat teknologi OFDMA dan MU-MIMO. Untuk memaksimalkan performa ini, pastikan channel spacing terkonfigurasi dengan baik untuk meminimalkan interferensi frekuensi, dan lakukan pembagian VLAN terpisah antara lalu lintas data staf internal dengan jaringan guest/tamu.</p>'
                ],
                [
                    'title' => 'Mengapa Bisnis Anda Memerlukan Jaringan VLAN Terpisah?',
                    'slug' => 'mengapa-bisnis-memerlukan-vlan-terpisah',
                    'excerpt' => 'Tingkatkan keamanan data internal dengan membagi segmen jaringan menggunakan Virtual Local Area Network (VLAN).',
                    'content' => '<p>Virtual Local Area Network (VLAN) memungkinkan Anda membagi satu jaringan fisik menjadi beberapa jaringan logis yang saling terisolasi. Ini sangat penting untuk menjaga privasi data sensitif seperti data finansial dan server operasional agar tidak dapat diakses oleh perangkat luar di jaringan publik/tamu.</p>'
                ],
                [
                    'title' => 'Tips Penempatan Ceiling Access Point untuk Cakupan Sinyal Maksimal',
                    'slug' => 'tips-penempatan-ceiling-access-point-sinyal-maksimal',
                    'excerpt' => 'Hindari blind spot sinyal dengan mengikuti panduan tinggi dan tata letak penempatan access point di plafon.',
                    'content' => '<p>Lokasi fisik penempatan Access Point sangat mempengaruhi cakupan dan kekuatan sinyal Wi-Fi. Tempatkan AP di titik tengah ruangan plafon tanpa penghalang logam besar di sekitarnya. Hindari memasang AP terlalu dekat dengan sudut dinding untuk meminimalkan pantulan sinyal yang mengurangi efisiensi transmisi data.</p>'
                ],
                [
                    'title' => 'Panduan Singkat Mengatur Load Balancing di Multi-WAN Gateway',
                    'slug' => 'panduan-mengatur-load-balancing-multi-wan-gateway',
                    'excerpt' => 'Menggabungkan beberapa ISP secara cerdas untuk mencegah downtime dan menjaga kelancaran bisnis Anda.',
                    'content' => '<p>Load balancing memungkinkan penggabungan bandwidth dari dua atau lebih penyedia layanan internet (ISP). Dengan mengonfigurasinya di Smart Gateway XYORA, lalu lintas pengguna akan didistribusikan secara dinamis sehingga beban kerja terbagi rata dan menyediakan failover otomatis jika salah satu koneksi ISP terputus.</p>'
                ],
                [
                    'title' => 'Keunggulan Outdoor Access Point IP65 untuk Area Publik Terbuka',
                    'slug' => 'keunggulan-outdoor-access-point-ip65-area-publik',
                    'excerpt' => 'Perangkat outdoor tahan cuaca ekstrem adalah kunci kestabilan jaringan di area parkir, rooftop, dan taman.',
                    'content' => '<p>Menyediakan Wi-Fi di area luar ruangan memerlukan perlindungan hardware yang mumpuni. Sertifikasi IP65 menjamin ketahanan perangkat terhadap debu, kelembapan udara tinggi, serta cipratan air hujan. Jangkauan pancaran sinyal yang lebih kuat juga disesuaikan untuk mengatasi redaman ruang terbuka.</p>'
                ],
                [
                    'title' => 'Mengurangi Latensi Jaringan dengan Pengelolaan Cloud-Based Management',
                    'slug' => 'mengurangi-latensi-jaringan-cloud-based-management',
                    'excerpt' => 'Pantau dan konfigurasi seluruh jaringan Anda dari mana saja secara real-time dengan solusi cloud management.',
                    'content' => '<p>Dengan sistem manajemen berbasis cloud, administrator jaringan dapat memantau kesehatan koneksi, mengonfigurasi perangkat, dan mendiagnosis masalah latensi tinggi secara instan dari jarak jauh. XYORA Cloud Management mempermudah pembaruan firmware berkala secara terpusat tanpa perlu kunjungan lapangan.</p>'
                ]
            ];

            foreach ($articlesData as $index => $art) {
                DB::table('posts')->updateOrInsert(
                    ['slug' => $art['slug']],
                    [
                        'title' => $art['title'],
                        'excerpt' => $art['excerpt'],
                        'content' => $art['content'],
                        'author_id' => $authorId,
                        'status' => 'published',
                        'visibility' => 'public',
                        'published_at' => now()->subDays($index),
                        'created_at' => now()->subDays($index),
                        'updated_at' => now()->subDays($index),
                    ]
                );
            }

            // Seed posts settings
            if (Schema::hasTable('posts_settings')) {
                DB::table('posts_settings')->updateOrInsert(
                    ['key' => 'posts_per_page'],
                    ['value' => '6']
                );
                DB::table('posts_settings')->updateOrInsert(
                    ['key' => 'archive_slug'],
                    ['value' => 'blog']
                );
            }
            if (Schema::hasTable('settings')) {
                \App\Models\Setting::set('permalink_post_base', 'blog', 'general', 'text');
            }
        }

        // 7. Create Theme database record and activate it
        $theme = Theme::updateOrCreate(
            ['slug' => 'xyora'],
            [
                'name' => 'Xyora Network Theme',
                'version' => '1.0.0',
                'description' => 'Official Xyora theme for networking solutions.',
                'author' => 'Xyora Indonesia',
                'supports' => ['pages', 'posts', 'menus', 'cpt', 'forms'],
                'installed_at' => now(),
            ]
        );

        $theme->update(['is_active' => false]);
        app(\App\Services\ThemeManager::class)->activate($theme);
    }
}
