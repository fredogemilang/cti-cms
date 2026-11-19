<?php

namespace Themes\Xyora\Seeders;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Media;
use App\Models\Page;
use App\Models\SeoMeta;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class XyoraSeoSeeder extends Seeder
{
    public function run(): void
    {
        $defaultOgPath = 'media/main-banner-18-2mb-1789702301-rAIBPpcn.jpg';
        $defaultOgMedia = Media::where('path', $defaultOgPath)->first()
            ?? Media::where('path', 'like', 'media/main-banner-%')->first();

        Setting::set(
            'seo_default_description',
            'XYORA menyediakan solusi perangkat jaringan Wi-Fi 6/7, Access Point, Smart Gateway, dan PoE Switch yang cepat, stabil, serta mudah dikelola secara terpusat.',
            'seo',
            'text'
        );

        if ($defaultOgMedia) {
            Setting::set('seo_default_og_image', $defaultOgMedia->path, 'seo', 'text');
        } elseif (! setting('seo_default_og_image')) {
            Setting::set('seo_default_og_image', $defaultOgPath, 'seo', 'text');
        }

        $pagesSeo = [
            'home' => [
                'title' => 'Solusi Jaringan Wi-Fi Premium & Mudah Dikelola | XYORA',
                'description' => 'XYORA menghadirkan perangkat jaringan Wi-Fi 6/7, Smart Gateway, dan PoE Switch yang cepat, stabil, dan mudah dikelola terpusat untuk bisnis maupun hunian.',
                'og_image' => 'media/main-banner-18-2mb-1789702301-rAIBPpcn.jpg',
            ],
            'tentang' => [
                'title' => 'Tentang Kami - Brand Teknologi Jaringan Indonesia | XYORA',
                'description' => 'Kenali XYORA, brand teknologi jaringan Indonesia yang menyediakan Access Point, Smart Gateway, dan PoE Switch berkualitas dengan manajemen cloud terpusat.',
                'og_image' => 'media/about-layer-1787027743-20RXvJvc.png',
            ],
            'kontak' => [
                'title' => 'Hubungi Kami & Layanan RMA Produk Jaringan | XYORA',
                'description' => 'Konsultasikan kebutuhan jaringan bisnis Anda, ajukan proses RMA resmi, atau cek status pengajuan RMA produk XYORA secara online bersama tim dukungan kami.',
                'og_image' => 'media/contact1-1786430651-7MA7GXt8.png',
            ],
            'usecase-sekolah-kampus' => [
                'title' => 'Solusi Jaringan Wi-Fi Sekolah & Kampus | XYORA',
                'description' => 'Wujudkan konektivitas Wi-Fi stabil dan aman untuk ruang kelas, perpustakaan, hingga area kampus padat pengguna dengan manajemen jaringan terpusat XYORA.',
                'og_image' => 'media/main-banner-17-2mb-1789702041-2mLnvF78.jpg',
            ],
            'usecase-hotel-resort' => [
                'title' => 'Solusi Jaringan Wi-Fi Hotel & Resort | XYORA',
                'description' => 'Berikan pengalaman Wi-Fi cepat, seamless roaming, dan stabil di seluruh kamar serta area publik hotel dan resort dengan perangkat jaringan unggulan XYORA.',
                'og_image' => 'media/main-banner-2mb-1789700992-AcFQ3lbR.jpg',
            ],
            'usecase-gedung-bertingkat' => [
                'title' => 'Solusi Jaringan Wi-Fi Gedung Bertingkat & Kantor | XYORA',
                'description' => 'Optimalkan konektivitas kantor dan gedung bertingkat dengan perangkat Wi-Fi, Smart Gateway, serta PoE Switch XYORA yang andal untuk ratusan pengguna aktif.',
                'og_image' => 'media/main-banner-18-2mb-1789702301-rAIBPpcn.jpg',
            ],
            'access-point' => [
                'title' => 'Wireless Access Point Wi-Fi 6 & Wi-Fi 7 Enterprise | XYORA',
                'description' => 'Jelajahi rangkaian Wireless Access Point XYORA mulai dari Ceiling, In-Wall Wi-Fi 6/7, hingga Outdoor AP untuk cakupan luas dan koneksi berkecepatan tinggi.',
                'og_image' => 'media/chatgpt-image-sep-1-2026-12-55-19-pm-1788360609-MNXD72QD.webp',
            ],
            'gateway' => [
                'title' => 'Smart Gateway Multi-WAN & Manajemen Terpusat | XYORA',
                'description' => 'Smart Gateway XYORA dengan fitur Multi-WAN Load Balancing, output PoE, dan cloud remote management terpusat untuk mengelola hingga 128 unit Access Point.',
                'og_image' => 'media/smart-gateway-1788361183-yxIy7HFb.webp',
            ],
            'switch' => [
                'title' => 'PoE Switch Gigabit Manajemen Jaringan Stabil | XYORA',
                'description' => 'PoE Switch Gigabit XYORA dengan total power budget 120W, desain metal fanless tahan lama, dan distribusi daya efisien untuk Access Point dan IP Camera.',
                'og_image' => 'media/chatgpt-image-sep-1-2026-11-55-11-pm-1788365867-G80CZq4L.webp',
            ],
        ];

        foreach ($pagesSeo as $slug => $data) {
            $page = Page::where('slug', $slug)->first();
            if (! $page) {
                continue;
            }
            $media = Media::where('path', $data['og_image'])->first() ?? $defaultOgMedia;
            $this->upsertSeoMeta($page, $data['title'], $data['description'], $media?->id);
        }

        $productsCpt = CustomPostType::where('slug', 'products')->first();
        if ($productsCpt) {
            $this->upsertSeoMeta(
                $productsCpt,
                'Semua Produk Jaringan Wi-Fi, Gateway & PoE Switch | XYORA',
                'Temukan katalog lengkap perangkat jaringan XYORA: Ceiling & In-Wall Access Point Wi-Fi 6/7, Outdoor AP, Smart Gateway, dan PoE Switch bergaransi resmi.',
                $defaultOgMedia?->id
            );
        }

        $productsSeo = [
            'ceiling-wireless-access-point' => [
                'title' => 'Ceiling Wireless Access Point Wi-Fi 6 Dual-Band | XYORA',
                'description' => 'Ceiling Access Point Wi-Fi 6 dual-band hingga 3000 Mbps dari XYORA, ideal untuk kantor, sekolah, hotel, dan area padat dengan manajemen cloud terpusat.',
            ],
            'wi-fi-6-in-wall-access-point' => [
                'title' => 'Wi-Fi 6 In-Wall Access Point 86-Type Elegan | XYORA',
                'description' => 'Wi-Fi 6 In-Wall Access Point dual-band hingga 1800 Mbps berdesain minimalis 86-type wall mount untuk kamar hotel, apartemen, dan ruang kerja modern.',
            ],
            'wi-fi-7-in-wall-access-point' => [
                'title' => 'Wi-Fi 7 In-Wall Access Point Tri-Band Premium | XYORA',
                'description' => 'Wi-Fi 7 In-Wall Access Point Tri-Band dengan teknologi Multi-Link Operation untuk kecepatan ultra-tinggi dan latensi rendah di hunian serta kantor modern.',
            ],
            'outdoor-access-point' => [
                'title' => 'Outdoor Wireless Access Point Wi-Fi 6 Tahan Cuaca | XYORA',
                'description' => 'Outdoor Access Point Wi-Fi 6 dual-band dengan jangkauan hingga radius 100 meter dan perlindungan tahan cuaca untuk taman, kampus, serta area terbuka.',
            ],
            'smart-gateway' => [
                'title' => 'Smart Gateway Multi-WAN Load Balancing & PoE | XYORA',
                'description' => 'Smart Gateway Multi-WAN Load Balancing dari XYORA dengan output PoE dan cloud remote management yang mampu mengelola hingga 128 unit Access Point terpusat.',
            ],
            'poe-switch' => [
                'title' => 'Smart PoE Switch 8-Port Gigabit 120W Fanless | XYORA',
                'description' => 'Smart PoE Switch Gigabit XYORA dengan total power budget 120W, 2 port uplink, dan casing metal fanless yang senyap serta tangguh untuk perangkat jaringan.',
            ],
        ];

        foreach ($productsSeo as $slug => $data) {
            $entry = CptEntry::where('slug', $slug)->first();
            if (! $entry) {
                continue;
            }
            $this->upsertSeoMeta($entry, $data['title'], $data['description'], null);
        }
    }

    protected function upsertSeoMeta(object $entity, string $title, string $description, ?int $ogImageId): void
    {
        $meta = SeoMeta::firstOrNew([
            'seoable_type' => get_class($entity),
            'seoable_id' => $entity->getKey(),
            'locale' => '',
        ]);

        if (empty($meta->title)) {
            $meta->title = $title;
        }
        if (empty($meta->description)) {
            $meta->description = $description;
        }
        if (empty($meta->og_title)) {
            $meta->og_title = $title;
        }
        if (empty($meta->og_description)) {
            $meta->og_description = $description;
        }
        if (empty($meta->og_image_id) && $ogImageId) {
            $meta->og_image_id = $ogImageId;
        }
        if (empty($meta->robots)) {
            $meta->robots = 'index,follow';
        }
        if (empty($meta->twitter_card)) {
            $meta->twitter_card = 'summary_large_image';
        }

        $meta->save();
    }
}
