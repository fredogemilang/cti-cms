<?php

namespace Database\Seeders;

use App\Models\CustomPostType;
use App\Models\CptEntry;
use App\Models\MetaField;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure CPT permissions exist and assigned to admin/super-admin
        $cptPermissions = [
            ['name' => 'cpt.view', 'module' => 'cpt', 'action' => 'view', 'description' => 'View custom post types & entries', 'source' => 'core', 'icon' => 'folder_open', 'sort_order' => 45],
            ['name' => 'cpt.create', 'module' => 'cpt', 'action' => 'create', 'description' => 'Create custom post types & entries', 'source' => 'core', 'icon' => 'folder_open', 'sort_order' => 46],
            ['name' => 'cpt.edit', 'module' => 'cpt', 'action' => 'edit', 'description' => 'Edit custom post types & entries', 'source' => 'core', 'icon' => 'folder_open', 'sort_order' => 47],
            ['name' => 'cpt.delete', 'module' => 'cpt', 'action' => 'delete', 'description' => 'Delete custom post types & entries', 'source' => 'core', 'icon' => 'folder_open', 'sort_order' => 48],
        ];

        foreach ($cptPermissions as $permData) {
            $perm = Permission::updateOrCreate(
                ['name' => $permData['name']],
                $permData
            );

            // Assign to super_admin and admin roles if present
            $roles = Role::whereIn('slug', ['super_admin', 'admin'])->get();
            foreach ($roles as $role) {
                if (! $role->permissions()->where('permissions.id', $perm->id)->exists()) {
                    $role->permissions()->attach($perm->id);
                }
            }
        }

        // 2. Register CPT 'testimonials'
        // Rule: strictly register meta_boxes in settings
        $cpt = CustomPostType::updateOrCreate(
            ['slug' => 'testimonials'],
            [
                'name' => 'testimonial',
                'singular_label' => 'Testimonial',
                'plural_label' => 'Testimonials',
                'icon' => 'format_quote',
                'description' => 'Client success stories, statistics, and testimonials.',
                'is_hierarchical' => false,
                'show_in_menu' => true,
                'show_in_rest' => true,
                'has_archive' => false,
                'publicly_queryable' => false,
                'supports' => ['title', 'thumbnail'],
                'settings' => [
                    'meta_boxes' => [
                        [
                            'id' => 'testimonial_details',
                            'title' => 'Testimonial Details',
                            'context' => 'normal',
                        ],
                    ],
                ],
                'is_active' => true,
            ]
        );

        // 3. Register MetaFields strictly mapped to 'testimonial_details' field_group
        $metaFields = [
            [
                'name' => 'client_name',
                'label' => 'Client / Person Name',
                'type' => 'text',
                'description' => 'Name of the person giving the testimonial (e.g. Caca Patricia)',
                'is_required' => true,
                'order' => 1,
            ],
            [
                'name' => 'role',
                'label' => 'Role / Designation',
                'type' => 'text',
                'description' => 'Role and company of the client (e.g. Partnership Specialist, PT Kaluna Indonesia Group)',
                'is_required' => true,
                'order' => 2,
            ],
            [
                'name' => 'quote',
                'label' => 'Testimonial Quote',
                'type' => 'textarea',
                'description' => 'Full quote given by the client',
                'is_required' => true,
                'order' => 3,
            ],
            [
                'name' => 'stat',
                'label' => 'Primary Metric / Stat (e.g. +55%)',
                'type' => 'text',
                'description' => 'Large highlighted statistic percentage or number',
                'is_required' => false,
                'order' => 4,
            ],
            [
                'name' => 'stat_label',
                'label' => 'Primary Metric Label',
                'type' => 'text',
                'description' => 'Short explanation of the primary metric',
                'is_required' => false,
                'order' => 5,
            ],
            [
                'name' => 'stat2',
                'label' => 'Secondary Metric / Stat (e.g. 35%)',
                'type' => 'text',
                'description' => 'Secondary statistic number',
                'is_required' => false,
                'order' => 6,
            ],
            [
                'name' => 'stat2_label',
                'label' => 'Secondary Metric Label',
                'type' => 'text',
                'description' => 'Short explanation of the secondary metric',
                'is_required' => false,
                'order' => 7,
            ],
        ];

        foreach ($metaFields as $fieldData) {
            MetaField::updateOrCreate(
                [
                    'fieldable_type' => CustomPostType::class,
                    'fieldable_id' => $cpt->id,
                    'name' => $fieldData['name'],
                ],
                [
                    'label' => $fieldData['label'],
                    'type' => $fieldData['type'],
                    'description' => $fieldData['description'] ?? null,
                    'field_group' => 'testimonial_details', // Strict Rule: must match meta_box id
                    'is_required' => $fieldData['is_required'] ?? false,
                    'order' => $fieldData['order'] ?? 0,
                    'is_active' => true,
                ]
            );
        }

        // 4. Seed Existing 4 Testimonials
        $adminUserId = User::first()?->id ?? 1;

        $testimonialsData = [
            [
                'company' => "Luna's Doughnuts",
                'slug' => 'lunas-doughnuts',
                'client_name' => 'Caca Patricia',
                'role_en' => 'Partnership Specialist, PT Kaluna Indonesia Group',
                'role_id' => 'Partnership Specialist, PT Kaluna Indonesia Group',
                'quote_en' => "Kouventa provides an efficient and customizable communication solution that perfectly fits our business needs. Since using it, we've achieved faster customer responses and significant cost savings.",
                'quote_id' => "Kouventa memberikan solusi komunikasi yang efisien dan bisa disesuaikan, pas dengan kebutuhan bisnis kami. Sejak memakainya, respons ke pelanggan jadi lebih cepat dan biaya jauh lebih hemat.",
                'stat' => '+55%',
                'stat_label_en' => 'time saved in daily communication and coordination',
                'stat_label_id' => 'waktu hemat dalam komunikasi dan koordinasi harian',
                'stat2' => '35%',
                'stat2_label_en' => 'lower operational costs',
                'stat2_label_id' => 'biaya operasional lebih rendah',
                'menu_order' => 0,
            ],
            [
                'company' => 'PakeDriver',
                'slug' => 'pakedriver',
                'client_name' => 'Yudhistira',
                'role_en' => 'CEO of PakeDriver',
                'role_id' => 'CEO PakeDriver',
                'quote_en' => "Kouventa makes it effortless for us to engage directly with customers, from service updates to promotional offers. Its intuitive AI features have strengthened our professional image and boosted engagement.",
                'quote_id' => "Kouventa memudahkan kami terhubung langsung dengan pelanggan, mulai dari update layanan hingga penawaran promo. Fitur AI yang intuitif memperkuat citra profesional kami dan meningkatkan interaksi.",
                'stat' => '+45%',
                'stat_label_en' => 'improvement in customer engagement',
                'stat_label_id' => 'peningkatan interaksi dengan pelanggan',
                'stat2' => '30%',
                'stat2_label_en' => 'faster customer response time',
                'stat2_label_id' => 'respons ke pelanggan lebih cepat',
                'menu_order' => 1,
            ],
            [
                'company' => 'Liberta Hotel',
                'slug' => 'liberta-hotel',
                'client_name' => 'Novellinda Putri Kairo',
                'role_en' => 'Corporate Sales Admin, Liberta',
                'role_id' => 'Corporate Sales Admin, Liberta',
                'quote_en' => "Kouventa's WhatsApp automation streamlined our promotions and reservations. Their intuitive platform and dedicated support made them a true partner in our hospitality journey.",
                'quote_id' => "Otomasi WhatsApp dari Kouventa membuat promosi dan reservasi kami lebih efisien. Platform yang intuitif dan dukungan yang berdedikasi menjadikan mereka mitra sejati dalam bisnis perhotelan kami.",
                'stat' => '70%',
                'stat_label_en' => 'time saved sending promotional broadcasts and confirmations',
                'stat_label_id' => 'waktu hemat mengirim broadcast promo dan konfirmasi',
                'stat2' => '+45%',
                'stat2_label_en' => 'increase in guest response rates',
                'stat2_label_id' => 'peningkatan respons dari tamu',
                'menu_order' => 2,
            ],
            [
                'company' => 'DPay',
                'slug' => 'dpay',
                'client_name' => 'Sebastian Hadinata',
                'role_en' => 'General Manager of DPay',
                'role_id' => 'General Manager DPay',
                'quote_en' => "Kouventa's WhatsApp automation solution revolutionized our logistics tracking and freed our team to focus on service innovation. Their flexible API and responsive support made integration seamless.",
                'quote_id' => "Solusi otomasi WhatsApp dari Kouventa mengubah cara kami melacak logistik dan membuat tim bisa fokus pada inovasi layanan. API yang fleksibel dan dukungan yang responsif membuat integrasinya mulus.",
                'stat' => '60%',
                'stat_label_en' => 'fewer customer service tickets',
                'stat_label_id' => 'tiket customer service berkurang',
                'stat2' => '+40%',
                'stat2_label_en' => 'efficiency gain in operational lead time',
                'stat2_label_id' => 'efisiensi lead time operasional meningkat',
                'menu_order' => 3,
            ],
        ];

        foreach ($testimonialsData as $data) {
            $meta = [
                'client_name' => $data['client_name'],
                'role' => $data['role_en'],
                'quote' => $data['quote_en'],
                'stat' => $data['stat'],
                'stat_label' => $data['stat_label_en'],
                'stat2' => $data['stat2'],
                'stat2_label' => $data['stat2_label_en'],
                '_translations' => [
                    'id' => [
                        'client_name' => $data['client_name'],
                        'role' => $data['role_id'],
                        'quote' => $data['quote_id'],
                        'stat' => $data['stat'],
                        'stat_label' => $data['stat_label_id'],
                        'stat2' => $data['stat2'],
                        'stat2_label' => $data['stat2_label_id'],
                    ],
                ],
            ];

            CptEntry::updateOrCreate(
                [
                    'post_type_id' => $cpt->id,
                    'slug' => $data['slug'],
                ],
                [
                    'title' => $data['company'],
                    'status' => 'published',
                    'published_at' => now(),
                    'author_id' => $adminUserId,
                    'menu_order' => $data['menu_order'],
                    'meta' => $meta,
                ]
            );
        }
    }
}
