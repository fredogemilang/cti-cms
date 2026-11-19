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

        // Single-locale Indonesian site configuration (prevents duplicate /id/ & /en/ sitemaps/hreflangs)
        Setting::set('default_locale', 'id', 'languages', 'text');
        Setting::set('fallback_locale', 'id', 'languages', 'text');
        Setting::set('available_locales', 'id', 'languages', 'text');
        Setting::set('locale_switcher_enabled', false, 'languages', 'boolean');

        // Dedicated category pages (/access-point/, /gateway/, /switch/) are indexed instead of generic taxonomy archives
        Setting::set('seo_taxonomy_product_category_index_enabled', false, 'seo', 'boolean');

        // Google Site Kit GTM (Smart Delayed Load handles GTM non-blockingly in InjectSeoTags)
        Setting::set('gsk_enabled', true, 'google-site-kit', 'boolean');
        Setting::set('gsk_gtm_id', 'GTM-KLDBVMMQ', 'google-site-kit', 'text');

        // Critical CSS + deferred theme.css on Homepage only (eliminates render-blocking CSS)
        Setting::set('pageopt_critical_css_homepage_only', true, 'pageopt', 'boolean');
        Setting::set('pageopt_deferred_stylesheets', 'themes/xyora/assets/css/theme.css', 'pageopt', 'code');
        Setting::set('pageopt_critical_css', <<<'CSS'
:root{--primary-navy:#0f2c59;--accent-green:#89c55c;--accent-green-dark:#74a74e;--text-dark:#1e293b;--text-light:#64748b;--text-gray:#64748b;--bg-gradient:linear-gradient(135deg,#f3f8f2 0%,#faf6f0 100%);--white:#fff;--gray-light:#f8fafc;--gray-border:#e2e8f0;--font-heading:"Outfit",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;--font-body:"Inter",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;--transition-fast:.2s ease;--transition-normal:.3s ease}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:var(--font-body);color:var(--text-dark);background-color:var(--white);line-height:1.5;overflow-x:clip;-webkit-font-smoothing:antialiased}
a{text-decoration:none;color:inherit}
ul{list-style:none}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
.site-header{position:sticky;top:0;left:0;width:100%;background-color:transparent;z-index:1000;box-shadow:0 1px 4px rgba(15,44,89,.05)}
.site-header::before{content:"";position:absolute;top:0;left:0;width:100%;height:100%;background:linear-gradient(90deg,#eef7eb 0%,#fff 35%);z-index:-1;opacity:1}
body.has-admin-bar .site-header{top:32px}
.header-container{max-width:1280px;margin:0 auto;padding:1.25rem 2rem;display:flex;align-items:center;justify-content:space-between}
.logo-link{display:flex;align-items:center;outline:none}
.logo-img{height:38px;width:auto;display:block}
.main-nav{display:flex;align-items:center}
.nav-list{display:flex;align-items:center;gap:1.75rem}
.nav-item{position:relative}
.nav-link{font-family:var(--font-body);font-size:.95rem;font-weight:500;color:var(--text-dark);padding:.5rem 0;display:flex;align-items:center;gap:.25rem}
.nav-item.active .nav-link{color:var(--primary-navy);font-weight:600;position:relative}
.nav-item.active .nav-link::after{content:"";position:absolute;bottom:-4px;left:0;width:100%;height:3px;background-color:var(--accent-green);border-radius:2px}
.nav-link svg.chevron{width:12px;height:12px}
.dropdown-menu,.mega-menu{opacity:0;visibility:hidden;position:absolute}
.nav-link.hubungi{display:inline-flex;align-items:center;gap:.5rem}
.nav-link.hubungi .circle-chevron{display:flex;align-items:center;justify-content:center;width:18px;height:18px;border:1px solid var(--text-dark);border-radius:50%}
.header-search{position:relative;display:flex;align-items:center;max-width:200px}
.header-search form{position:relative;display:flex;align-items:center;width:100%}
.mobile-only-search{display:none!important}
.search-input{width:100%;padding:.5rem 2.5rem .5rem 1rem;font-family:var(--font-body);font-size:.875rem;color:var(--text-dark);background-color:var(--white);border:1px solid var(--gray-border);border-radius:20px;outline:none}
.search-btn{position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;display:flex;align-items:center;justify-content:center;padding:.25rem}
.mobile-menu-toggle{display:none;background:none;border:none;cursor:pointer;padding:.5rem;z-index:1001}
.mobile-menu-toggle svg{width:24px;height:24px;color:var(--primary-navy)}
.hero-slider-section{position:relative;background:var(--bg-gradient);overflow:hidden;padding:0}
.slider-container{width:100%;position:relative}
.slides-wrapper{display:flex;overflow-x:auto;scroll-snap-type:x mandatory;scrollbar-width:none;-ms-overflow-style:none}
.slides-wrapper::-webkit-scrollbar{display:none}
.slide{flex:0 0 100%;width:100%;scroll-snap-align:start;display:flex;align-items:center;min-height:680px;padding:2rem 5% 2rem 8%;position:relative;overflow:hidden}
.slide::after{content:"";position:absolute;bottom:0;left:0;width:100%;height:180px;background:linear-gradient(180deg,rgba(255,255,255,0) 0%,#fff 100%);z-index:3;pointer-events:none}
.slide-content{width:45%;max-width:600px;display:flex;flex-direction:column;justify-content:center;align-items:flex-start;z-index:10;position:relative}
.slide-title{font-family:var(--font-heading);font-size:clamp(2.25rem,4vw,3.5rem);font-weight:800;color:var(--primary-navy);line-height:1.15;margin-bottom:1rem}
.slide-subtitle{font-family:var(--font-body);font-size:clamp(1.1rem,2vw,1.5rem);color:var(--text-dark);font-weight:500;margin-bottom:2rem}
.slide-btn{display:inline-block;background-color:var(--accent-green);color:var(--text-dark);font-family:var(--font-body);font-weight:700;font-size:1.05rem;padding:.85rem 2.25rem;border-radius:8px;border:none;cursor:pointer;box-shadow:0 4px 6px -1px rgba(141,198,63,.2),0 2px 4px -1px rgba(141,198,63,.1)}
.slide-visual{position:absolute;top:0;right:0;width:100%;height:100%;z-index:1}
.slide-image{width:100%;height:100%;object-fit:cover;border-radius:0;box-shadow:none}
.slider-pagination{position:absolute;bottom:1.5rem;left:50%;transform:translateX(-50%);display:flex;gap:.75rem;z-index:20}
.page-dot{width:10px;height:10px;border-radius:50%;background-color:#cbd5e1;border:none;cursor:pointer;padding:0}
.page-dot.active{background-color:#64748b;width:24px;border-radius:5px}
@media(max-width:1024px){
.site-header{padding:.5rem 0}
.header-container{padding:.75rem 1.5rem}
.mobile-menu-toggle{display:block}
.main-nav{position:fixed;top:0;right:-100%;width:280px;height:100vh;background-color:var(--white);box-shadow:-4px 0 15px rgba(15,44,89,.1);z-index:1000;padding:5.5rem 2rem 2rem;display:flex!important;flex-direction:column!important;align-items:flex-start!important;justify-content:flex-start!important;gap:2rem!important;overflow-y:auto}
.main-nav .dropdown-menu,.main-nav .mega-menu{display:none}
.desktop-only-search{display:none!important}
.mobile-only-search{display:flex!important;max-width:100%;width:100%}
.slide{min-height:480px;padding:3rem 1.5rem 4.5rem;align-items:center}
.slide::before{content:"";position:absolute;top:0;left:0;width:100%;height:100%;z-index:2;pointer-events:none;background:linear-gradient(180deg,rgba(243,248,242,.2) 0%,rgba(250,246,240,.85) 60%,rgba(250,246,240,.98) 100%)}
.slide-content{width:100%;max-width:100%;text-align:center;align-items:center}
.slide-visual{top:0;left:0;width:100%;height:100%}
.slide-image{object-position:right center}
.slide-title{margin-bottom:.5rem}
.slide-subtitle{margin-bottom:1.25rem}
}
@media(max-width:480px){
.header-container{padding:.5rem 1rem}
.logo-img{height:32px}
.slide{padding:2rem 1rem 4.5rem}
.slide-title{font-size:2rem}
}
CSS, 'pageopt', 'code');

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
