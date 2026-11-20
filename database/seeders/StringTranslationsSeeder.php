<?php

namespace Database\Seeders;

use App\Models\StringTranslation;
use App\Models\StringTranslationKey;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class StringTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [
            'industry.infrastructure' => [
                'group' => 'industry',
                'key' => 'infrastructure',
                'default' => 'Infrastructure',
                'en' => 'Infrastructure',
                'id' => 'Infrastruktur',
            ],
            'industry.cloud' => [
                'group' => 'industry',
                'key' => 'cloud',
                'default' => 'Cloud',
                'en' => 'Cloud',
                'id' => 'Cloud',
            ],
            'industry.security' => [
                'group' => 'industry',
                'key' => 'security',
                'default' => 'Security',
                'en' => 'Security',
                'id' => 'Keamanan',
            ],
            'nav.industry' => [
                'group' => 'nav',
                'key' => 'industry',
                'default' => 'Industry',
                'en' => 'Industry',
                'id' => 'Industri',
            ],
            'common.home' => [
                'group' => 'common',
                'key' => 'home',
                'default' => 'Home',
                'en' => 'Home',
                'id' => 'Beranda',
            ],
            'common.technology_alliance' => [
                'group' => 'common',
                'key' => 'technology_alliance',
                'default' => 'Technology Alliance',
                'en' => 'Technology Alliance',
                'id' => 'Aliansi Teknologi',
            ],
            'solutions_archive.hero_title' => [
                'group' => 'solutions_archive',
                'key' => 'hero_title',
                'default' => 'Our Solutions',
                'en' => 'Our Solutions',
                'id' => 'Solusi Kami',
            ],
            'solutions_archive.hero_subtitle' => [
                'group' => 'solutions_archive',
                'key' => 'hero_subtitle',
                'default' => 'Technology That Brings Your Business Together',
                'en' => 'Technology That Brings Your Business Together',
                'id' => 'Satu Mitra Beragam Solusi Teknologi',
            ],
            'solutions_archive.section_headline' => [
                'group' => 'solutions_archive',
                'key' => 'section_headline',
                'default' => 'Move Your Business Forward with Connected Enterprise Solutions',
                'en' => 'Move Your Business Forward with Connected Enterprise Solutions',
                'id' => 'Bangun Fondasi IT yang Siap Mendukung Bisnis',
            ],
            'solutions_archive.section_desc' => [
                'group' => 'solutions_archive',
                'key' => 'section_desc',
                'default' => 'From infrastructure to AI, we bring your technology together to streamline operations, accelerate growth, and keep your business ahead.',
                'en' => 'From infrastructure to AI, we bring your technology together to streamline operations, accelerate growth, and keep your business ahead.',
                'id' => 'Penuhi kebutuhan infrastruktur, cloud, data, keamanan, dan AI dengan teknologi yang mengoptimalkan operasional dan mendukung pertumbuhan bisnis.',
            ],
            'alliances_archive.hero_title' => [
                'group' => 'alliances_archive',
                'key' => 'hero_title',
                'default' => 'Technology Alliances',
                'en' => 'Technology Alliances',
                'id' => 'Kemitraan Teknologi',
            ],
            'alliances_archive.hero_subtitle' => [
                'group' => 'alliances_archive',
                'key' => 'hero_subtitle',
                'default' => 'Through collaborations with leading global technology providers, we strengthen our product capabilities and integrate cutting-edge technologies into our solutions.',
                'en' => 'Through collaborations with leading global technology providers, we strengthen our product capabilities and integrate cutting-edge technologies into our solutions.',
                'id' => 'Melalui kemitraan dengan penyedia teknologi global, kami menghadirkan produk dengan kapabilitas yang lebih kuat serta solusi yang didukung teknologi terkini.',
            ],
            'alliances_archive.partners_heading' => [
                'group' => 'alliances_archive',
                'key' => 'partners_heading',
                'default' => 'Our Strategic Partners',
                'en' => 'Our Strategic Partners',
                'id' => 'Partner Strategis Kami',
            ],
            'alliances_archive.cta_label' => [
                'group' => 'alliances_archive',
                'key' => 'cta_label',
                'default' => 'Learn More',
                'en' => 'Learn More',
                'id' => 'Pelajari Lebih Lanjut',
            ],
            'alliances_archive.empty_notice' => [
                'group' => 'alliances_archive',
                'key' => 'empty_notice',
                'default' => 'No technology alliances published yet.',
                'en' => 'No technology alliances published yet.',
                'id' => 'Belum ada mitra teknologi yang dipublikasikan.',
            ],
            'contact_page.open_maps' => [
                'group' => 'contact_page',
                'key' => 'open_maps',
                'default' => 'Open in Google Maps',
                'en' => 'Open in Google Maps',
                'id' => 'Buka di Google Maps',
            ],
            'privacy_page.reading' => [
                'group' => 'privacy_page',
                'key' => 'reading',
                'default' => 'Reading:',
                'en' => 'Reading:',
                'id' => 'Membaca:',
            ],
            'privacy_page.clause' => [
                'group' => 'privacy_page',
                'key' => 'clause',
                'default' => 'Clause',
                'en' => 'Clause',
                'id' => 'Klausul',
            ],
            'privacy_page.of' => [
                'group' => 'privacy_page',
                'key' => 'of',
                'default' => 'of',
                'en' => 'of',
                'id' => 'dari',
            ],
            'privacy_page.next_clause' => [
                'group' => 'privacy_page',
                'key' => 'next_clause',
                'default' => 'Next Clause',
                'en' => 'Next Clause',
                'id' => 'Klausul Berikutnya',
            ],
            'privacy_page.prev_clause' => [
                'group' => 'privacy_page',
                'key' => 'prev_clause',
                'default' => 'Previous',
                'en' => 'Previous',
                'id' => 'Sebelumnya',
            ],
            'privacy_page.back_to_first' => [
                'group' => 'privacy_page',
                'key' => 'back_to_first',
                'default' => 'Back to Clause 1',
                'en' => 'Back to Clause 1',
                'id' => 'Kembali ke Klausul 1',
            ],
            'search_page.results_for' => [
                'group' => 'search_page',
                'key' => 'results_for',
                'default' => 'Search results for',
                'en' => 'Search results for',
                'id' => 'Hasil pencarian untuk',
            ],
            'search_page.default_title_prefix' => [
                'group' => 'search_page',
                'key' => 'default_title_prefix',
                'default' => 'Search',
                'en' => 'Search',
                'id' => 'Telusuri',
            ],
            'search_page.default_title_accent' => [
                'group' => 'search_page',
                'key' => 'default_title_accent',
                'default' => 'Solutions & Products',
                'en' => 'Solutions & Products',
                'id' => 'Solusi & Produk',
            ],
            'search_page.input_placeholder' => [
                'group' => 'search_page',
                'key' => 'input_placeholder',
                'default' => 'Search solutions, products, or articles...',
                'en' => 'Search solutions, products, or articles...',
                'id' => 'Cari solusi, produk, atau artikel...',
            ],
            'search_page.solutions_related' => [
                'group' => 'search_page',
                'key' => 'solutions_related',
                'default' => 'Solutions related to',
                'en' => 'Solutions related to',
                'id' => 'Solusi terkait',
            ],
            'search_page.view_all_solutions' => [
                'group' => 'search_page',
                'key' => 'view_all_solutions',
                'default' => 'View All Solutions',
                'en' => 'View All Solutions',
                'id' => 'Lihat Semua Solusi',
            ],
            'search_page.view_solution_detail' => [
                'group' => 'search_page',
                'key' => 'view_solution_detail',
                'default' => 'View Solution Details',
                'en' => 'View Solution Details',
                'id' => 'Lihat Detail Solusi',
            ],
            'search_page.products_related' => [
                'group' => 'search_page',
                'key' => 'products_related',
                'default' => 'Products related to',
                'en' => 'Products related to',
                'id' => 'Produk terkait',
            ],
            'search_page.view_all_products' => [
                'group' => 'search_page',
                'key' => 'view_all_products',
                'default' => 'View All Products',
                'en' => 'View All Products',
                'id' => 'Lihat Semua Produk',
            ],
            'search_page.articles_related' => [
                'group' => 'search_page',
                'key' => 'articles_related',
                'default' => 'Articles related to',
                'en' => 'Articles related to',
                'id' => 'Artikel terkait',
            ],
            'search_page.view_all_articles' => [
                'group' => 'search_page',
                'key' => 'view_all_articles',
                'default' => 'View All Articles',
                'en' => 'View All Articles',
                'id' => 'Lihat Semua Artikel',
            ],
            'search_page.read_more' => [
                'group' => 'search_page',
                'key' => 'read_more',
                'default' => 'Read More',
                'en' => 'Read More',
                'id' => 'Selengkapnya',
            ],
            'search_page.empty_title' => [
                'group' => 'search_page',
                'key' => 'empty_title',
                'default' => 'No matching results found',
                'en' => 'No matching results found',
                'id' => 'Tidak ada hasil ditemukan untuk kata kunci tersebut',
            ],
            'search_page.empty_query_title' => [
                'group' => 'search_page',
                'key' => 'empty_query_title',
                'default' => 'Enter a keyword to search',
                'en' => 'Enter a keyword to search',
                'id' => 'Masukkan kata kunci untuk mencari',
            ],
            'search_page.empty_subtitle' => [
                'group' => 'search_page',
                'key' => 'empty_subtitle',
                'default' => 'Try another keyword such as Infrastructure, Cloud, Security, AI, Server, Storage, or one of our technology partners.',
                'en' => 'Try another keyword such as Infrastructure, Cloud, Security, AI, Server, Storage, or one of our technology partners.',
                'id' => 'Coba kata kunci lain seperti Infrastructure, Cloud, Security, AI, Server, Storage, atau nama vendor mitra kami.',
            ],
            'search_page.popular_searches' => [
                'group' => 'search_page',
                'key' => 'popular_searches',
                'default' => 'Popular:',
                'en' => 'Popular:',
                'id' => 'Pencarian populer:',
            ],
        ];

        foreach ($translations as $item) {
            StringTranslationKey::where('group', $item['group'])->whereIn('key', [$item['key'], $item['group'].'.'.$item['key']])->delete();
            StringTranslationKey::where('group', 'ui')->where('key', $item['group'].'.'.$item['key'])->delete();

            $tk = StringTranslationKey::create([
                'group' => $item['group'],
                'key' => $item['key'],
                'default_value' => $item['default'],
            ]);

            StringTranslation::create([
                'translation_key_id' => $tk->id,
                'locale' => 'en',
                'value' => $item['en'],
            ]);

            StringTranslation::create([
                'translation_key_id' => $tk->id,
                'locale' => 'id',
                'value' => $item['id'],
            ]);
        }

        Cache::forget('translations:en');
        Cache::forget('translations:id');
    }
}
