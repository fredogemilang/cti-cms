<?php

namespace Database\Seeders;

use App\Models\StringTranslation;
use App\Models\StringTranslationKey;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class KouventaOctoberTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $dict14 = [
            'AI Company Wikipedia: instant answers from your company knowledge' => 'AI Company Wikipedia: jawaban instan dari pengetahuan perusahaan Anda',
            'AI Task Force: automate invoice reconciliation with human approval' => 'AI Task Force: otomasi rekonsiliasi invoice dengan persetujuan manusia',
            'And many more' => 'Dan masih banyak lagi',
            'Certified & Registered' => 'Tersertifikasi & Terdaftar',
            'Enterprise ready' => 'Siap untuk skala enterprise',
            'Omnichannel inbox: from Instagram DM to WhatsApp campaign' => 'Omnichannel inbox: dari DM Instagram ke campaign WhatsApp',
            'Recruitment' => 'Rekrutmen',
            'Run Kouventa in the cloud, on-premise on your own servers, or hybrid, so it fits your IT policies, infrastructure and data residency needs.' => 'Jalankan Kouventa di cloud, on-premise di server Anda sendiri, atau hybrid, sesuai kebijakan IT, infrastruktur, dan kebutuhan lokasi data Anda.',
            'See Kouventa in action.' => 'Lihat cara kerja Kouventa.',
            'Technology Partners' => 'Technology Partners',
            'Watch more on YouTube' => 'Tonton lainnya di YouTube',
            'via Webhook' => 'via Webhook',
            'What held your AI back,' => 'Kendala adopsi AI,',
            'now solved.' => 'kini teratasi.',
            'Integrate with Any Channel' => 'Integrasikan dengan Channel Apa Pun',
            'Plug your AI into any app or channel your teams and customers already use.' => 'Hubungkan AI Anda ke aplikasi atau channel mana pun yang sudah digunakan tim dan pelanggan Anda.',
            'Custom rules' => 'Custom rules',
            'Data masking' => 'Masking data',
            'Decide who can see and do what with role-based access, review every activity with a complete audit trail, and set custom rules so AI works within your policies.' => 'Atur akses sesuai peran, pantau aktivitas lewat audit trail, dan buat aturan agar AI bekerja sesuai kebijakan Anda.',
            'Explore more →' => 'Pelajari lebih lanjut →',
            'Ready to see it work for your business?' => 'Siap lihat langsung hasilnya untuk bisnis Anda?',
            'Role-based access' => 'Akses per peran',
            'Secure by design' => 'Aman dari fondasinya',
            'You\'re in control' => 'Kendali penuh',
        ];

        foreach ($dict14 as $enKey => $idVal) {
            $stk = StringTranslationKey::firstOrCreate(
                ['group' => 'kouventa', 'key' => $enKey],
                ['default_value' => $enKey]
            );

            $enTrans = StringTranslation::firstOrNew([
                'translation_key_id' => $stk->id,
                'locale' => 'en',
            ]);
            if ($enTrans->value !== $enKey) {
                $enTrans->value = $enKey;
                $enTrans->save();
            }

            $idTrans = StringTranslation::firstOrNew([
                'translation_key_id' => $stk->id,
                'locale' => 'id',
            ]);
            if ($idTrans->value !== $idVal) {
                $idTrans->value = $idVal;
                $idTrans->save();
            }
        }

        // Clear translation caches
        Cache::forget('translations.en');
        Cache::forget('translations.id');
        Cache::flush();

        $this->command->info('Kouventa 14 October translations synced successfully.');
    }
}
