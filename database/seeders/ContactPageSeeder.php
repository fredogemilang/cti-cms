<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\User;
use App\Services\CacheManager;
use App\Services\MediaService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Plugins\CsiMenus\Models\NavMenu;
use Plugins\CsiMenus\Models\NavMenuItem;

class ContactPageSeeder extends Seeder
{
    public function run(): void
    {
        $authorId = auth()->id() ?? User::query()->value('id');
        $heroBgPath = $this->importContactHeroAsset($authorId);

        $page = Page::withTrashed()->where('slug', 'contact-us')->first();
        if (! $page) {
            $page = new Page;
            $page->slug = 'contact-us';
            $page->author_id = $authorId;
        } elseif ($page->trashed()) {
            $page->restore();
        }

        $page->title = 'Contact Us';
        $page->template = 'contact';
        $page->status = 'published';
        $page->published_at ??= now();
        $page->translations = [
            'id' => [
                'title' => 'Hubungi Kami',
                'slug' => 'hubungi-kami',
            ],
        ];
        $page->seo = [
            'title' => 'Contact Us | Converion Solution Informatica',
            'description' => 'Connect with PT Converion Solution Informatica in Jakarta and Surabaya. Reach us via phone, WhatsApp Care Hub, email, or submit your project inquiry.',
            '_translations' => [
                'id' => [
                    'title' => 'Hubungi Kami | Converion Solution Informatica',
                    'description' => 'Terhubung dengan PT Converion Solution Informatica di Jakarta dan Surabaya melalui telepon, WhatsApp Care Hub, email, atau formulir konsultasi.',
                ],
            ],
        ];
        $page->save();

        $jakartaEmbed = 'https://maps.google.com/maps?q=Centennial+Tower+Jakarta+Jl.+Jend.+Gatot+Subroto+Kav.+24-25&t=&z=16&ie=UTF8&iwloc=&output=embed';
        $jakartaMaps = 'https://www.google.com/maps/search/?api=1&query=Centennial+Tower+Jakarta+Jl.+Jend.+Gatot+Subroto+Kav.+24-25';
        $surabayaEmbed = 'https://maps.google.com/maps?q=Graha+SA+Jl.+Raya+Gubeng+No.+19-21+Surabaya&t=&z=16&ie=UTF8&iwloc=&output=embed';
        $surabayaMaps = 'https://www.google.com/maps/search/?api=1&query=Graha+SA+Jl.+Raya+Gubeng+No.+19-21+Surabaya';

        $offices = [
            [
                'name' => 'Jakarta Office',
                'floor' => 'Centennial Tower 12th Floor',
                'address' => 'Jl. Jend. Gatot Subroto Kav. 24 - 25, Jakarta 12930, Indonesia',
                'phone' => 'P: +6221 806 22278',
                'embed_url' => $jakartaEmbed,
                'maps_url' => $jakartaMaps,
            ],
            [
                'name' => 'Surabaya Office',
                'floor' => 'Graha SA Lantai 8, Suite 801',
                'address' => 'Jl. Raya Gubeng No. 19-21, Surabaya - Indonesia',
                'phone' => 'P: +6231 501 8978',
                'embed_url' => $surabayaEmbed,
                'maps_url' => $surabayaMaps,
            ],
        ];

        $highlightsEn = [
            [
                'icon_type' => 'phone',
                'label' => 'Phone',
                'value' => '+6221 806 22278',
                'url' => 'tel:+622180622278',
            ],
            [
                'icon_type' => 'whatsapp',
                'label' => 'Converion Care Hub (WA)',
                'value' => '+62822 9992 2278',
                'url' => 'https://wa.me/6282299922278',
            ],
            [
                'icon_type' => 'email',
                'label' => 'Email',
                'value' => 'marketing@converion.com',
                'url' => 'mailto:marketing@converion.com',
            ],
        ];

        $highlightsId = [
            [
                'icon_type' => 'phone',
                'label' => 'Telepon',
                'value' => '+6221 806 22278',
                'url' => 'tel:+622180622278',
            ],
            [
                'icon_type' => 'whatsapp',
                'label' => 'Converion Care Hub (WA)',
                'value' => '+62822 9992 2278',
                'url' => 'https://wa.me/6282299922278',
            ],
            [
                'icon_type' => 'email',
                'label' => 'Email',
                'value' => 'marketing@converion.com',
                'url' => 'mailto:marketing@converion.com',
            ],
        ];

        $blocks = [
            [
                'name' => 'hero_bg_image',
                'type' => 'media',
                'label' => 'Hero Background Image',
                'value' => $heroBgPath,
                'value_id' => null,
            ],
            [
                'name' => 'hero_title',
                'type' => 'text',
                'label' => 'Hero Title',
                'value' => 'Let’s Connect and Grow Together',
                'value_id' => 'Mari Terhubung dan Bertumbuh Bersama',
            ],
            [
                'name' => 'hero_subtitle',
                'type' => 'textarea',
                'label' => 'Hero Subtitle',
                'value' => 'Want to learn more about our solutions, services, or how Converion can accelerate your business’s digital transformation? Our team is ready to support you every step of the way.',
                'value_id' => 'Ingin mengetahui lebih lanjut mengenai solusi, layanan, atau bagaimana Converion dapat mempercepat transformasi digital bisnis Anda? Tim kami siap mendukung Anda di setiap langkah.',
            ],
            [
                'name' => 'hero_cta',
                'type' => 'button',
                'label' => 'Hero CTA Button',
                'value' => json_encode([
                    'text' => 'Contact Us',
                    'url' => '#contact',
                    'target' => '_self',
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'value_id' => json_encode([
                    'text' => 'Hubungi Kami',
                    'url' => '#contact',
                    'target' => '_self',
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ],
            [
                'name' => 'offices_heading',
                'type' => 'text',
                'label' => 'Offices Section Heading',
                'value' => 'PT Converion Solution Informatica',
                'value_id' => 'PT Converion Solution Informatica',
            ],
            [
                'name' => 'offices_list',
                'type' => 'repeater',
                'label' => 'Office Locations',
                'value' => json_encode($offices, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'value_id' => json_encode($offices, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ],
            [
                'name' => 'contact_highlights',
                'type' => 'repeater',
                'label' => 'Contact Info Bar Items',
                'value' => json_encode($highlightsEn, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'value_id' => json_encode($highlightsId, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ],
        ];

        foreach ($blocks as $index => $item) {
            $translations = $item['value_id'] !== null
                ? ['id' => ['value' => $item['value_id']]]
                : null;

            PageBlock::updateOrCreate(
                [
                    'page_id' => $page->id,
                    'name' => $item['name'],
                    'parent_block_id' => null,
                ],
                [
                    'type' => $item['type'],
                    'label' => $item['label'],
                    'value' => $item['value'],
                    'translations' => $translations,
                    'order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }

        $this->syncNavigationLinks();

        CacheManager::purgeAll();
        $this->command?->info("Synced Contact Us page (#{$page->id}) & navigation links.");
    }

    protected function importContactHeroAsset(?int $uploadedBy = null): string
    {
        $source = database_path('seeders/assets/contact/contact-hero-bg.png');
        if (! is_file($source)) {
            return '';
        }

        $basename = basename($source);
        $existing = Media::where('original_filename', $basename)->latest('id')->first();
        if ($existing) {
            return (string) ($existing->webp_path ?: $existing->path);
        }

        $uploaded = new UploadedFile($source, $basename, mime_content_type($source) ?: 'image/png', null, true);
        $media = app(MediaService::class)->upload($uploaded, [
            'title' => 'Contact Us Hero Building Background',
            'alt_text' => 'Converion Solution Informatica Contact Banner',
            'uploaded_by' => $uploadedBy,
        ]);

        return (string) ($media->webp_path ?: $media->path);
    }

    protected function syncNavigationLinks(): void
    {
        if (! class_exists(NavMenu::class) || ! class_exists(NavMenuItem::class) || ! Schema::hasTable('csi_nav_menus')) {
            return;
        }

        $menuIds = NavMenu::whereIn('location', ['header', 'footer_pages'])->pluck('id');
        if ($menuIds->isEmpty()) {
            return;
        }

        NavMenuItem::whereIn('menu_id', $menuIds)
            ->where(function ($q) {
                $q->where('title', 'Contact Us')
                    ->orWhereRaw('LOWER(url) = ?', ['#contact']);
            })
            ->get()
            ->each(function (NavMenuItem $item) {
                $trans = is_array($item->translations) ? $item->translations : [];
                $trans['id']['title'] = $trans['id']['title'] ?? 'Hubungi Kami';
                $trans['id']['url'] = '/hubungi-kami';

                $item->update([
                    'url' => '/contact-us',
                    'translations' => $trans,
                ]);
            });
    }
}
