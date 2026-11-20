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
        ];

        foreach ($translations as $item) {
            StringTranslationKey::where('group', $item['group'])->where('key', $item['key'])->delete();
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
