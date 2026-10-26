<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Page;
use App\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        $authorId = User::first()?->id ?? 1;

        Page::updateOrCreate(
            ['slug' => 'terms-conditions'],
            [
                'title' => 'Terms & Conditions',
                'template' => 'terms-conditions',
                'status' => 'published',
                'published_at' => now(),
                'author_id' => $authorId,
                'menu_order' => 9,
                'is_system' => true,
                'translations' => [
                    'id' => [
                        'slug' => 'terms-conditions',
                        'title' => 'Syarat & Ketentuan',
                    ],
                ],
                'seo' => [
                    'title' => 'Terms & Conditions | Kouventa by Helios',
                    'description' => 'Terms and conditions outlining rules, rights, and responsibilities for using Kouventa enterprise AI and customer engagement services by PT Helios Informatika Nusantara.',
                    'keywords' => 'Kouventa terms, terms and conditions, terms of service, PT Helios Informatika Nusantara legal',
                ],
            ]
        );

        Page::updateOrCreate(
            ['slug' => 'privacy-policy'],
            [
                'title' => 'Privacy Policy',
                'template' => 'privacy-policy',
                'status' => 'published',
                'published_at' => now(),
                'author_id' => $authorId,
                'menu_order' => 10,
                'is_system' => true,
                'translations' => [
                    'id' => [
                        'slug' => 'privacy-policy',
                        'title' => 'Kebijakan Privasi',
                    ],
                ],
                'seo' => [
                    'title' => 'Privacy Policy | Kouventa by Helios',
                    'description' => 'Privacy policy explaining how PT Helios Informatika Nusantara collects, protects, and manages personal data across Kouventa platforms and WhatsApp Business integrations.',
                    'keywords' => 'Kouventa privacy policy, data protection, UU PDP, ISO 27001, Helios privacy policy',
                ],
            ]
        );
    }

    public function down(): void
    {
        Page::whereIn('slug', ['terms-conditions', 'privacy-policy'])->delete();
    }
};
