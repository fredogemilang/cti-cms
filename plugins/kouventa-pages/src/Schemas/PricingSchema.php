<?php

namespace Plugins\KouventaPages\Schemas;

class PricingSchema
{
    /**
     * Get list of admin section tabs for this page.
     */
    public static function getTabs(): array
    {
        return [
            ['id' => 'hero', 'label' => '1. Hero & Toggle', 'icon' => 'home'],
            ['id' => 'tiers_ai', 'label' => '2. AI Tiers (3 Plans)', 'icon' => 'smart_toy'],
            ['id' => 'tiers_ce', 'label' => '3. CE Tiers (3 Plans)', 'icon' => 'forum'],
            ['id' => 'addons', 'label' => '4. Add-ons & Extensions', 'icon' => 'extension'],
            ['id' => 'templates', 'label' => '5. WhatsApp Templates', 'icon' => 'mark_chat_read'],
            ['id' => 'calculator', 'label' => '6. WhatsApp Calculator Rates', 'icon' => 'calculate'],
            ['id' => 'cta', 'label' => '7. Bottom CTA', 'icon' => 'call_to_action'],
            ['id' => 'seo', 'label' => '8. SEO & Settings', 'icon' => 'travel_explore'],
        ];
    }

    /**
     * Get full default structured data for this page.
     */
    public static function getDefaults(string $locale = 'en'): array
    {
        $isId = ($locale === 'id');

        if ($isId) {
            return array (
  'hero' => 
  array (
    'badge' => 'HARGA',
    'title' => 
    array (
      'prefix' => 'AI yang Anda',
      'highlight' => 'kendalikan,',
      'suffix' => 'dengan harga sesuai pertumbuhan Anda',
    ),
    'subtitle' => 'Dua platform, masing-masing tiga paket. Mulai sesuai kondisi tim Anda sekarang, lalu kembangkan tanpa perlu ganti vendor',
    'toggle_ai' => 'Artificial Intelligence',
    'toggle_ce' => 'Customer Engagement',
  ),
  'tiers_ai' => 
  array (
    'starter' => 
    array (
      'name' => 'Starter',
      'desc' => 'Terapkan AI di workflow pertama Anda.',
      'price' => 'IDR 5.999.000',
      'period' => 'per bulan, ditagih tahunan',
      'highlight' => 'Use case Company Wikipedia tanpa batas',
      'bullets' => 
      array (
        0 => 'Generative AI dengan rujukan sumber',
        1 => '19+ jenis sumber data yang didukung',
        2 => 'LLM terkurasi dengan pemilihan dinamis',
        3 => 'Storage 100 GB, 100rb API call/bulan',
        4 => 'AI guardrails & penyamaran PII',
        5 => 'Pengiriman via Livechat, akses SDK',
      ),
      'cta_text' => 'Hubungi sales',
      'cta_url' => '#whatsapp',
    ),
    'grow' => 
    array (
      'name' => 'Grow',
      'badge' => 'Paling populer',
      'desc' => 'Tambahkan kemampuan agentic di seluruh tim Anda.',
      'price' => 'Harga khusus',
      'period' => 'langganan tahunan',
      'highlight' => 'Wikipedia tanpa batas + 3 use case Task Force',
      'bullets' => 
      array (
        0 => 'Semua fitur Starter',
        1 => 'Agentic AI dengan orkestrasi multi-agent',
        2 => 'Dukungan LLM khusus',
        3 => 'Konektor: Slack, SAP, SQL, custom',
        4 => 'Storage 500 GB, chat playground',
        5 => 'Pembuatan file & grafik',
      ),
      'cta_text' => 'Hubungi sales',
      'cta_url' => '#whatsapp',
    ),
    'scale' => 
    array (
      'name' => 'Scale',
      'desc' => 'AI enterprise yang terorkestrasi penuh.',
      'price' => 'Harga khusus',
      'period' => 'cakupan khusus, sekali bayar atau berlangganan',
      'highlight' => 'Use case & workflow tanpa batas',
      'bullets' => 
      array (
        0 => 'Semua fitur Grow',
        1 => 'Orkestrasi otomasi workflow penuh',
        2 => 'Hybrid, private cloud, atau on-premise',
        3 => 'Konektor kustom & fine-tuning',
        4 => 'Tim engineering khusus',
        5 => 'Audit trail & persetujuan perubahan',
      ),
      'cta_text' => 'Hubungi sales',
      'cta_url' => '#whatsapp',
    ),
  ),
  'tiers_ce' => 
  array (
    'starter' => 
    array (
      'name' => 'Starter',
      'desc' => 'Untuk tim yang baru memulai omnichannel.',
      'price' => 'IDR 1.499.000',
      'period' => 'per bulan',
      'highlight' => 'Hingga 3.000 pengguna aktif bulanan',
      'bullets' => 
      array (
        0 => 'WhatsApp, Instagram, Facebook',
        1 => 'Agent customer service tanpa batas',
        2 => 'Chatbot statis & routing agent',
        3 => 'Database 10.000 kontak',
        4 => 'Analitik, akses berbasis peran, MFA',
        5 => 'Deployment SaaS cloud',
      ),
      'cta_text' => 'Hubungi sales',
      'cta_url' => '#whatsapp',
    ),
    'pro' => 
    array (
      'name' => 'Professional',
      'badge' => 'Paling populer',
      'desc' => 'Untuk tim yang sedang berkembang dan memperluas engagement di berbagai channel.',
      'price' => 'IDR 4.499.000',
      'period' => 'per bulan',
      'highlight' => 'Hingga 15.000 pengguna aktif bulanan',
      'bullets' => 
      array (
        0 => 'Semua fitur Starter',
        1 => 'Pencarian di seluruh riwayat percakapan',
        2 => 'Database 45.000 kontak',
        3 => 'Formulir & template WhatsApp Flow',
        4 => 'Penjadwalan broadcast & CSAT',
        5 => 'Dukungan standar 9×5, uptime 99,5%',
      ),
      'cta_text' => 'Hubungi sales',
      'cta_url' => '#whatsapp',
    ),
    'enterprise' => 
    array (
      'name' => 'Enterprise',
      'desc' => 'Kontrol penuh untuk operasional skala enterprise.',
      'price' => 'Harga khusus',
      'period' => 'cakupan khusus, sekali bayar atau berlangganan',
      'highlight' => 'MAU & database kontak bisa disesuaikan',
      'bullets' => 
      array (
        0 => 'Semua fitur Professional',
        1 => 'Hybrid, private cloud, atau on-premise',
        2 => 'Dukungan Platinum 24×7',
        3 => 'SLA uptime 99,9%',
        4 => 'Tim implementasi khusus',
        5 => 'Penyamaran data & audit trail',
      ),
      'cta_text' => 'Hubungi sales',
      'cta_url' => '#whatsapp',
    ),
  ),
  'addons' => 
  array (
    'title' => 'Perluas paket Anda',
    'subtitle_ai' => 'Tambahkan kemampuan seiring adopsi AI Anda berkembang.',
    'subtitle_ce' => 'Tambahkan kemampuan sesuai kebutuhan, di paket mana pun.',
    'note_text' => 'Tersedia di semua paket.',
    'note_link_text' => 'Hubungi sales',
    'note_suffix' => 'untuk cakupan dan harga.',
    'items_ai' => 
    array (
      0 => 
      array (
        'title' => 'Platform Customer Engagement',
        'desc' => 'Tambahkan channel WhatsApp dengan inbox omnichannel.',
      ),
      1 => 
      array (
        'title' => 'Tambahan use case Task Force',
        'desc' => 'Jalankan lebih banyak workflow agentic di luar kuota paket Anda.',
      ),
      2 => 
      array (
        'title' => 'Paket token AI',
        'desc' => 'Tambahan kapasitas token untuk pemakaian model terkurasi.',
      ),
      3 => 
      array (
        'title' => 'Paket token API call',
        'desc' => 'Tambah kuota API call bulanan Anda.',
      ),
      4 => 
      array (
        'title' => 'Tambahan storage',
        'desc' => 'Tambahan kapasitas storage untuk sumber data dan embedding.',
      ),
      5 => 
      array (
        'title' => 'Tambahan masa simpan data',
        'desc' => 'Simpan riwayat percakapan dan log audit lebih dari 365 hari.',
      ),
      6 => 
      array (
        'title' => 'Dukungan Golden 9×5',
        'desc' => 'Dukungan jam kerja yang lebih panjang dengan tiket change request.',
      ),
      7 => 
      array (
        'title' => 'Dukungan Platinum 24×7',
        'desc' => 'Dukungan 24 jam dengan change request yang diprioritaskan.',
      ),
      8 => 
      array (
        'title' => 'Sesi pelatihan',
        'desc' => 'Onboarding terpandu untuk tim Anda, online atau di Jabodetabek.',
      ),
      9 => 
      array (
        'title' => 'Pengembangan khusus',
        'desc' => 'API, dashboard, channel, atau integrasi khusus.',
      ),
    ),
    'items_ce' => 
    array (
      0 => 
      array (
        'title' => 'Channel Livechat',
        'desc' => 'Tambahkan livechat web ke inbox omnichannel Anda.',
      ),
      1 => 
      array (
        'title' => 'Chatbot AI',
        'desc' => 'Paket starter Company Wikipedia dengan respons berbasis RAG.',
      ),
      2 => 
      array (
        'title' => 'Tambahan database kontak',
        'desc' => 'Kapasitas kontak tambahan untuk basis pelanggan yang berkembang.',
      ),
      3 => 
      array (
        'title' => 'Tambahan penyimpanan media',
        'desc' => 'Penyimpanan tambahan untuk gambar dan video yang dibagikan.',
      ),
      4 => 
      array (
        'title' => 'Pencarian percakapan',
        'desc' => 'Pencarian teks lengkap di seluruh riwayat percakapan.',
      ),
      5 => 
      array (
        'title' => 'Tambahan masa simpan data',
        'desc' => 'Simpan riwayat percakapan dan log audit lebih dari 365 hari.',
      ),
      6 => 
      array (
        'title' => 'Dukungan Golden 9×5',
        'desc' => 'Dukungan jam kerja yang lebih panjang dengan tiket change request.',
      ),
      7 => 
      array (
        'title' => 'Dukungan Platinum 24×7',
        'desc' => 'Dukungan 24 jam dengan change request yang diprioritaskan.',
      ),
      8 => 
      array (
        'title' => 'Sesi pelatihan',
        'desc' => 'Onboarding terpandu untuk tim Anda, online atau di Jabodetabek.',
      ),
      9 => 
      array (
        'title' => 'Pengembangan khusus',
        'desc' => 'API, dashboard, channel, atau integrasi khusus.',
      ),
    ),
  ),
  'templates' => 
  array (
    'title' => 'Template pesan WhatsApp',
    'subtitle' => 'Biaya WhatsApp dihitung per percakapan, tergantung kategori template yang Anda kirim.',
    'items' => 
    array (
      0 => 
      array (
        'key' => 'mk',
        'name' => 'Marketing',
        'items' => 
        array (
          0 => 'Ucapan salam, acara, dan promosi',
          1 => 'Pengumuman produk baru',
          2 => 'Pengingat keranjang belanja yang ditinggalkan',
        ),
      ),
      1 => 
      array (
        'key' => 'ut',
        'name' => 'Utility',
        'items' => 
        array (
          0 => 'Tindak lanjut atas tindakan atau permintaan pelanggan',
          1 => 'Konfirmasi pendaftaran (opt-in)',
          2 => 'Pembaruan dan peringatan akun, seperti pengingat pembayaran',
          3 => 'Survei kepuasan',
        ),
      ),
      2 => 
      array (
        'key' => 'au',
        'name' => 'Authentication',
        'items' => 
        array (
          0 => 'Kode OTP satu kali untuk verifikasi pengguna',
          1 => 'Login, pemulihan akun, pembayaran, dan pemeriksaan integritas',
        ),
      ),
      3 => 
      array (
        'key' => 'sv',
        'name' => 'Service',
        'items' => 
        array (
          0 => 'Percakapan pelanggan yang dipersonalisasi',
          1 => 'Bantuan pemesanan',
          2 => 'Penyelesaian masalah proaktif',
          3 => 'Rekomendasi produk interaktif',
        ),
      ),
    ),
  ),
  'calculator' => 
  array (
    'title' => 'Estimasi biaya WhatsApp Anda',
    'subtitle' => 'Masukkan perkiraan percakapan bulanan Anda per kategori untuk melihat perkiraan biaya bulanan dan tahunan',
    'rates' => 
    array (
      'mk' => 616,
      'ut' => 375,
      'au' => 375,
      'sv' => 375,
    ),
    'fees' => 
    array (
      'starter_mau' => 3000,
      'starter_fee' => 1499000,
      'pro_mau' => 15000,
      'pro_fee' => 4499000,
    ),
  ),
  'cta' => 
  array (
    'title' => 'Siap melihat bagaimana tim Anda akan memakainya?',
    'subtitle' => 'Jadwalkan sesi kerja dengan tim solusi kami. Kami bantu memetakan use case Anda ke paket dan deployment yang tepat',
    'primary_btn' => 
    array (
      'text' => 'Jadwalkan sesi kerja',
      'url' => '/book-demo',
    ),
    'secondary_btn' => 
    array (
      'text' => 'Hubungi sales',
      'url' => '#whatsapp',
    ),
  ),
);
        }

        return array (
  'hero' => 
  array (
    'badge' => 'PRICING',
    'title' => 
    array (
      'prefix' => 'Intelligence you',
      'highlight' => 'control,',
      'suffix' => 'priced for how you grow',
    ),
    'subtitle' => 'Two platforms, three tiers each. Start where your team is today, and scale without switching vendors',
    'toggle_ai' => 'Artificial Intelligence',
    'toggle_ce' => 'Customer Engagement',
  ),
  'tiers_ai' => 
  array (
    'starter' => 
    array (
      'name' => 'Starter',
      'desc' => 'Bring AI to your first workflow.',
      'price' => 'IDR 5.999.000',
      'period' => 'per month, billed annually',
      'highlight' => 'Unlimited Company Wikipedia use cases',
      'bullets' => 
      array (
        0 => 'Generative AI with source citation',
        1 => '19+ supported data source types',
        2 => 'Curated LLM with dynamic selection',
        3 => '100 GB storage, 100k API calls/month',
        4 => 'AI guardrails & PII masking',
        5 => 'Livechat delivery, SDK access',
      ),
      'cta_text' => 'Talk to sales',
      'cta_url' => '#whatsapp',
    ),
    'grow' => 
    array (
      'name' => 'Grow',
      'badge' => 'Most popular',
      'desc' => 'Add agentic capability across your teams.',
      'price' => 'Custom pricing',
      'period' => 'annual subscription',
      'highlight' => 'Unlimited Wikipedia + 3 Task Force use cases',
      'bullets' => 
      array (
        0 => 'Everything in Starter',
        1 => 'Agentic AI with multi-agent orchestration',
        2 => 'Custom LLM support',
        3 => 'Connectors: Slack, SAP, SQL, custom',
        4 => '500 GB storage, chat playground',
        5 => 'File & chart generation tools',
      ),
      'cta_text' => 'Talk to sales',
      'cta_url' => '#whatsapp',
    ),
    'scale' => 
    array (
      'name' => 'Scale',
      'desc' => 'Enterprise AI, fully orchestrated.',
      'price' => 'Custom pricing',
      'period' => 'custom scope, one-time or subscription',
      'highlight' => 'Unlimited use cases & workflows',
      'bullets' => 
      array (
        0 => 'Everything in Grow',
        1 => 'Full workflow automation orchestration',
        2 => 'Hybrid, private cloud, or on-premise',
        3 => 'Custom connectors & fine-tuning',
        4 => 'Dedicated engineering pod',
        5 => 'Audit trail & change approval gates',
      ),
      'cta_text' => 'Talk to sales',
      'cta_url' => '#whatsapp',
    ),
  ),
  'tiers_ce' => 
  array (
    'starter' => 
    array (
      'name' => 'Starter',
      'desc' => 'For teams starting their omnichannel journey.',
      'price' => 'IDR 1.499.000',
      'period' => 'per month',
      'highlight' => 'Up to 3,000 monthly active users',
      'bullets' => 
      array (
        0 => 'WhatsApp, Instagram, Facebook',
        1 => 'Unlimited customer service agents',
        2 => 'Static chatbot & agent routing',
        3 => '10,000 contact database',
        4 => 'Analytics, role-based access, MFA',
        5 => 'SaaS cloud deployment',
      ),
      'cta_text' => 'Talk to sales',
      'cta_url' => '#whatsapp',
    ),
    'pro' => 
    array (
      'name' => 'Professional',
      'badge' => 'Most popular',
      'desc' => 'For growing teams scaling engagement across channels.',
      'price' => 'IDR 4.499.000',
      'period' => 'per month',
      'highlight' => 'Up to 15,000 monthly active users',
      'bullets' => 
      array (
        0 => 'Everything in Starter',
        1 => 'Conversation search across history',
        2 => '45,000 contact database',
        3 => 'WhatsApp Flow forms & templates',
        4 => 'Broadcast scheduling & CSAT',
        5 => 'Standard 9×5 support, 99.5% uptime',
      ),
      'cta_text' => 'Talk to sales',
      'cta_url' => '#whatsapp',
    ),
    'enterprise' => 
    array (
      'name' => 'Enterprise',
      'desc' => 'Full control for enterprise-scale operations.',
      'price' => 'Custom pricing',
      'period' => 'custom scope, one-time or subscription',
      'highlight' => 'Customizable MAU & contact database',
      'bullets' => 
      array (
        0 => 'Everything in Professional',
        1 => 'Hybrid, private cloud, or on-premise',
        2 => 'Platinum 24×7 support',
        3 => '99.9% uptime SLA',
        4 => 'Dedicated implementation team',
        5 => 'Data masking & audit trail',
      ),
      'cta_text' => 'Talk to sales',
      'cta_url' => '#whatsapp',
    ),
  ),
  'addons' => 
  array (
    'title' => 'Extend your plan',
    'subtitle_ai' => 'Add capabilities as your AI adoption grows.',
    'subtitle_ce' => 'Add capabilities as you need them, on any tier.',
    'note_text' => 'Available across all plans.',
    'note_link_text' => 'Talk to sales',
    'note_suffix' => 'for scoping and pricing.',
    'items_ai' => 
    array (
      0 => 
      array (
        'title' => 'Customer Engagement platform',
        'desc' => 'Add WhatsApp delivery channel with omnichannel inbox.',
      ),
      1 => 
      array (
        'title' => 'Additional Task Force use case',
        'desc' => 'Deploy more agentic workflows beyond your plan\'s included quota.',
      ),
      2 => 
      array (
        'title' => 'AI token pack',
        'desc' => 'Additional token capacity for curated model usage.',
      ),
      3 => 
      array (
        'title' => 'API call token pack',
        'desc' => 'Expand your monthly API call quota.',
      ),
      4 => 
      array (
        'title' => 'Extended storage',
        'desc' => 'Additional storage capacity for datasources and embeddings.',
      ),
      5 => 
      array (
        'title' => 'Extended data retention',
        'desc' => 'Keep conversation history and audit logs beyond 365 days.',
      ),
      6 => 
      array (
        'title' => 'Golden 9×5 support',
        'desc' => 'Extended business-hours support with change request tickets.',
      ),
      7 => 
      array (
        'title' => 'Platinum 24×7 support',
        'desc' => 'Round-the-clock coverage with prioritized change requests.',
      ),
      8 => 
      array (
        'title' => 'Training sessions',
        'desc' => 'Guided onboarding for your team, online or in Jabodetabek.',
      ),
      9 => 
      array (
        'title' => 'Custom development',
        'desc' => 'Custom APIs, dashboards, channels, or integrations.',
      ),
    ),
    'items_ce' => 
    array (
      0 => 
      array (
        'title' => 'Livechat channel',
        'desc' => 'Add web-based livechat to your omnichannel inbox.',
      ),
      1 => 
      array (
        'title' => 'AI chatbot',
        'desc' => 'Company Wikipedia starter package with RAG-powered responses.',
      ),
      2 => 
      array (
        'title' => 'Extended contact database',
        'desc' => 'Additional contact capacity for growing customer bases.',
      ),
      3 => 
      array (
        'title' => 'Extended media storage',
        'desc' => 'Additional storage for shared images and video.',
      ),
      4 => 
      array (
        'title' => 'Conversation search',
        'desc' => 'Full-text search across historical conversations.',
      ),
      5 => 
      array (
        'title' => 'Extended data retention',
        'desc' => 'Keep conversation history and audit logs beyond 365 days.',
      ),
      6 => 
      array (
        'title' => 'Golden 9×5 support',
        'desc' => 'Extended business-hours support with change request tickets.',
      ),
      7 => 
      array (
        'title' => 'Platinum 24×7 support',
        'desc' => 'Round-the-clock coverage with prioritized change requests.',
      ),
      8 => 
      array (
        'title' => 'Training sessions',
        'desc' => 'Guided onboarding for your team, online or in Jabodetabek.',
      ),
      9 => 
      array (
        'title' => 'Custom development',
        'desc' => 'Custom APIs, dashboards, channels, or integrations.',
      ),
    ),
  ),
  'templates' => 
  array (
    'title' => 'WhatsApp message templates',
    'subtitle' => 'WhatsApp charges per conversation, based on the template category you send.',
    'items' => 
    array (
      0 => 
      array (
        'key' => 'mk',
        'name' => 'Marketing',
        'items' => 
        array (
          0 => 'Greetings, events and promotions',
          1 => 'New product announcements',
          2 => 'Cart abandonment reminders',
        ),
      ),
      1 => 
      array (
        'key' => 'ut',
        'name' => 'Utility',
        'items' => 
        array (
          0 => 'Follow-ups on customer actions or requests',
          1 => 'Opt-in confirmations',
          2 => 'Account updates and alerts, like payment reminders',
          3 => 'Feedback surveys',
        ),
      ),
      2 => 
      array (
        'key' => 'au',
        'name' => 'Authentication',
        'items' => 
        array (
          0 => 'One-time passcodes to verify users',
          1 => 'Login, account recovery, payment and integrity checks',
        ),
      ),
      3 => 
      array (
        'key' => 'sv',
        'name' => 'Service',
        'items' => 
        array (
          0 => 'Personalized customer conversations',
          1 => 'Order assistance',
          2 => 'Proactive issue resolution',
          3 => 'Interactive product recommendations',
        ),
      ),
    ),
  ),
  'calculator' => 
  array (
    'title' => 'Estimate your WhatsApp cost',
    'subtitle' => 'Enter your expected monthly conversations per category to see your estimated monthly and yearly cost',
    'rates' => 
    array (
      'mk' => 616,
      'ut' => 375,
      'au' => 375,
      'sv' => 375,
    ),
    'fees' => 
    array (
      'starter_mau' => 3000,
      'starter_fee' => 1499000,
      'pro_mau' => 15000,
      'pro_fee' => 4499000,
    ),
  ),
  'cta' => 
  array (
    'title' => 'Ready to see how your team would use it?',
    'subtitle' => 'Book a working session with our solution team. We\'ll map your use cases to the right tier and deployment',
    'primary_btn' => 
    array (
      'text' => 'Book a working session',
      'url' => '/book-demo',
    ),
    'secondary_btn' => 
    array (
      'text' => 'Contact sales',
      'url' => '#whatsapp',
    ),
  ),
);
    }
}
