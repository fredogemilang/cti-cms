<?php

namespace Plugins\KouventaPages\Schemas;

class ProductCeSchema
{
    /**
     * Get list of admin section tabs for this page.
     */
    public static function getTabs(): array
    {
        return [
            ['id' => 'hero', 'label' => '1. Hero & Subnav', 'icon' => 'home'],
            ['id' => 'challenges', 'label' => '2. The Challenges (3 Cards)', 'icon' => 'warning'],
            ['id' => 'journey', 'label' => '3. Customer Journey (4 Stages)', 'icon' => 'alt_route'],
            ['id' => 'products', 'label' => '4. CE Products (6 Products)', 'icon' => 'apps'],
            ['id' => 'platform_control', 'label' => '5. Platform Control (5 Cards)', 'icon' => 'shield'],
            ['id' => 'why', 'label' => '6. Why Kouventa (4 Cards)', 'icon' => 'verified'],
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

        return [
            'hero' => [
                'subnav' => [
                    'label' => $isId ? 'Produk' : 'Products',
                    'ai_label' => 'Artificial Intelligence',
                    'ce_label' => 'Customer Engagement',
                ],
                'eyebrow' => 'Official Meta Business Solution Provider',
                'title' => [
                    'prefix' => $isId ? 'Cara lebih cerdas untuk' : 'The smarter way to',
                    'main' => $isId ? 'berinteraksi dengan pelanggan' : 'engage customers',
                ],
                'subtitle' => $isId
                    ? 'Jangkau lebih banyak pelanggan, balas lebih cepat, dan buat percakapan lancar di WhatsApp dan channel lainnya'
                    : 'Reach more customers, respond faster, and keep conversations seamless on WhatsApp and beyond',
                'primary_cta' => [
                    'text' => $isId ? 'Jadwalkan demo' : 'Book a demo',
                    'url' => '/book-demo',
                ],
                'mockup' => [
                    'header_title' => 'Kouventa',
                    'header_subtitle' => 'Interactions › Chat',
                    'all_channels' => $isId ? 'Semua channel' : 'All channels',
                    'profile_label' => $isId ? 'Satu profil, dua channel' : 'One profile, two channels',
                    'summarize_btn' => $isId ? 'Ringkas' : 'Summarize',
                    'chat_m1' => $isId ? 'Halo! Weekend Sale masih berlaku?' : 'Hi! Is the Weekend Sale still on?',
                    'chat_m2' => $isId ? 'Masih, sampai Minggu! Mau akses awal lewat WhatsApp?' : 'Yes, until Sunday! Want early access on WhatsApp?',
                    'chat_divider' => $isId ? 'Esok hari · campaign WhatsApp' : 'Next day · WhatsApp campaign',
                    'chat_m3_badge' => 'WEEKEND SALE',
                    'chat_m3_text' => $isId ? 'Halo Rina, akses awal Anda sudah dibuka: diskon 30% sampai Minggu.' : 'Hi Rina, your early access is open: 30% off until Sunday.',
                    'chat_m4' => $isId ? 'Ukuran M masih ada?' : 'Is size M still available?',
                    'chat_m5' => $isId ? 'Ukuran M masih tersedia. Sudah saya simpankan untuk Anda.' : "Yes, size M is in stock. I've reserved it for you.",
                    'input_placeholder' => $isId ? 'Balas di WhatsApp…' : 'Reply on WhatsApp…',
                    'send_btn' => $isId ? 'Kirim' : 'Send',
                ],
            ],

            'challenges' => [
                'title' => [
                    'prefix' => $isId ? 'Setiap pesan yang terlewat adalah' : 'Every missed message is a',
                    'main' => $isId ? 'peluang yang hilang' : 'missed sale',
                ],
                'cards' => [
                    [
                        'id' => 'wait',
                        'title' => $isId ? 'Pelanggan menunggu balasan terlalu lama' : 'Customers wait too long for a reply',
                        'desc' => $isId
                            ? 'Pertanyaan menumpuk lebih cepat dari kemampuan tim menjawab, padahal pelanggan berharap dibalas dalam hitungan menit, bahkan di luar jam kerja.'
                            : 'Questions pile up faster than your team can answer, and customers expect a response in minutes, even after working hours.',
                    ],
                    [
                        'id' => 'reach',
                        'title' => $isId ? 'Kampanye tidak sampai ke orang yang tepat' : "Campaigns don't reach the right people",
                        'desc' => $isId
                            ? 'Promo yang dikirim manual hanya menjangkau sedikit pelanggan, dan tidak jelas siapa yang menerima, membaca, atau menindaklanjutinya.'
                            : "Promotions sent manually reach too few customers, and there's no clear view of who received, read, or acted on them.",
                    ],
                    [
                        'id' => 'scattered',
                        'title' => $isId ? 'Percakapan tersebar di banyak channel' : 'Conversations are scattered across channels',
                        'desc' => $isId
                            ? 'WhatsApp, Instagram, Facebook, dan web chat ada di inbox masing-masing, jadi konteks hilang dan follow-up terlewat.'
                            : 'WhatsApp, Instagram, Facebook, and web chat each live in a separate inbox, so context gets lost and follow-ups slip through.',
                    ],
                ],
            ],

            'journey' => [
                'title' => [
                    'prefix' => $isId ? 'Tak ada lagi' : 'Never miss a',
                    'main' => $isId ? 'pesan yang terlewat.' : 'message.',
                ],
                'subtitle' => $isId
                    ? 'Dari sapaan pertama hingga purna jual. Pilih tahapnya untuk melihat produk yang membantu.'
                    : 'From first hello to after-sales. Pick a stage to see which products help.',
                'customer_badge' => $isId ? 'Pelanggan Anda' : 'Your customer',
                'see_products_btn' => $isId ? 'Lihat produk untuk' : 'See the products for',
                'stages' => [
                    [
                        'number' => '01',
                        'name' => 'Prospecting',
                        'headline' => $isId ? 'Ubah prospek menjadi pelanggan.' : 'Turn prospects into customers.',
                        'sub' => $isId
                            ? 'Jangkau dan libatkan calon pelanggan dengan kampanye yang tertarget di berbagai channel.'
                            : 'Reach and engage potential customers with targeted campaigns across channels.',
                        'defaultTool' => 'broadcast',
                        'tools' => [
                            'broadcast' => $isId ? 'Jangkau lebih banyak prospek dalam skala besar' : 'Reach more prospects at scale',
                            'chatbot' => $isId ? 'Respons prospek secara instan' : 'Engage prospects instantly',
                            'omni' => $isId ? 'Terhubung di semua channel' : 'Connect across channels',
                        ],
                    ],
                    [
                        'number' => '02',
                        'name' => 'Onboarding',
                        'headline' => $isId ? 'Onboarding jadi lebih mudah.' : 'Make onboarding effortless.',
                        'sub' => $isId
                            ? 'Pandu pelanggan baru melalui registrasi, verifikasi, dan pengisian data, semuanya lewat chat.'
                            : 'Guide new customers through registration, verification and data submission, all in chat.',
                        'defaultTool' => 'flows',
                        'tools' => [
                            'flows' => $isId ? 'Registrasi dan pengisian data yang simpel' : 'Simple registration and data entry',
                            'auth' => $isId ? 'Verifikasi identitas dengan aman' : 'Verify identity securely',
                            'integration' => $isId ? 'Sebagai Utility Messages: update onboarding tepat waktu' : 'As Utility Messages: Timely onboarding updates',
                            'chatbot' => $isId ? 'Pandu dan jawab pertanyaan' : 'Guide and answer questions',
                            'omni' => $isId ? 'Satu percakapan, semua channel' : 'One conversation, every channel',
                        ],
                    ],
                    [
                        'number' => '03',
                        'name' => 'Upselling',
                        'headline' => $isId ? 'Tingkatkan nilai dari setiap pelanggan.' : 'Grow value from every customer.',
                        'sub' => $isId
                            ? 'Kirim penawaran yang relevan dan rekomendasi personal sesuai minat pelanggan.'
                            : 'Send relevant offers and personalized recommendations based on what customers like.',
                        'defaultTool' => 'broadcast',
                        'tools' => [
                            'broadcast' => $isId ? 'Jangkau pelanggan dengan penawaran yang relevan' : 'Reach customers with relevant offers',
                            'chatbot' => $isId ? 'Rekomendasikan produk, tingkatkan engagement' : 'Recommend products, drive engagement',
                        ],
                    ],
                    [
                        'number' => '04',
                        'name' => 'Support',
                        'headline' => $isId ? 'Dukungan lebih cepat, setiap saat.' : 'Faster support, every time.',
                        'sub' => $isId
                            ? 'Bantu pelanggan menemukan jawaban, menyelesaikan masalah umum, dan menghubungi tim Anda dengan mudah.'
                            : 'Help customers find answers, fix common issues, and reach your team easily.',
                        'defaultTool' => 'chatbot',
                        'tools' => [
                            'chatbot' => $isId ? 'Jawab pertanyaan secara instan' : 'Resolve questions instantly',
                            'omni' => $isId ? 'Layani pelanggan di channel apa pun' : 'Support customers on any channel',
                        ],
                    ],
                ],
            ],

            'products' => [
                'title' => [
                    'prefix' => $isId ? 'Satu platform' : 'One platform',
                    'main' => $isId ? 'Tanpa pesan yang terlewat' : 'Zero missed messages',
                ],
                'pill_highlighted' => $isId ? 'Disorot: peran tiap produk di tahap' : 'Highlighted: how each product helps in',
                'btn_show_all' => $isId ? 'Tampilkan semua produk' : 'Show all products',
                'hint_pick_stage' => $isId ? 'Pilih tahap journey di atas untuk melihat produk yang membantu' : 'Pick a journey stage above to see which products help',
                'items' => [
                    'auth' => [
                        'id' => 'auth',
                        'name' => 'Authentication',
                        'tag' => $isId ? 'Verifikasi OTP yang aman' : 'Secure OTP verification',
                        'headline' => $isId ? 'Amankan setiap login.' : 'Secure every login.',
                        'desc' => $isId
                            ? 'Kirim kode OTP lewat WhatsApp untuk memverifikasi pengguna dengan cepat dan aman.'
                            : 'Send one-time passwords on WhatsApp to verify users quickly and safely.',
                        'bullets' => [
                            $isId ? 'Verifikasi pengguna yang cepat dan aman' : 'Fast, secure user verification',
                            $isId ? 'Login OTP untuk semua akun' : 'OTP login for every account',
                            $isId ? 'Perlindungan dari akses tanpa izin' : 'Protection from unauthorized access',
                        ],
                        'alt' => $isId ? 'Ilustrasi animasi: one-time password masuk ke WhatsApp dan pengguna terverifikasi' : 'Animated illustration: a one-time password arrives on WhatsApp and the user is verified',
                    ],
                    'broadcast' => [
                        'id' => 'broadcast',
                        'name' => 'Broadcasting',
                        'tag' => $isId ? 'Kampanye skala besar' : 'Campaigns at scale',
                        'headline' => $isId ? 'Jangkau lebih banyak pelanggan, dalam skala besar.' : 'Reach more customers, at scale.',
                        'desc' => $isId
                            ? 'Kirim promo, kampanye, dan permintaan feedback lewat WhatsApp.'
                            : 'Send promotions, campaigns and feedback requests on WhatsApp.',
                        'bullets' => [
                            $isId ? 'Jadwalkan pengiriman pesan dan promo' : 'Schedule message and promo blasts',
                            $isId ? 'Laporan kampanye dan analitik bawaan' : 'Built-in campaign reports and analytics',
                            $isId ? 'Jangkauan dan awareness kampanye lebih baik' : 'Better campaign reach and awareness',
                        ],
                        'alt' => $isId ? 'Ilustrasi animasi: satu promo dikirim ke banyak pelanggan dan laporan kampanye terisi' : 'Animated illustration: one promotion is delivered to many customers and the campaign report fills in',
                    ],
                    'chatbot' => [
                        'id' => 'chatbot',
                        'name' => 'Chatbot',
                        'tag' => $isId ? 'Chatbot AI & predefined' : 'AI & predefined chatbots',
                        'headline' => $isId ? 'Customer support berbasis AI.' : 'AI-powered customer support.',
                        'desc' => $isId
                            ? 'Chatbot statis dan AI menjawab dengan cepat, lalu mengalihkan ke manusia bila perlu.'
                            : 'Static and AI chatbots answer fast, and hand over to a human when needed.',
                        'bullets' => [
                            $isId ? 'Selesaikan pertanyaan lebih cepat' : 'Resolve inquiries faster',
                            $isId ? 'Jawab pelanggan 24/7' : 'Answer customers 24/7',
                            $isId ? 'Ubah percakapan menjadi tiket support' : 'Turn conversations into support tickets',
                        ],
                        'alt' => $isId ? 'Ilustrasi animasi: bot statis menyapa pelanggan, bot AI mengambil alih pertanyaan teks bebas, lalu chat dialihkan ke agen manusia dengan tiket' : 'Animated illustration: a static bot greets the customer, an AI bot takes over the free-text question, then the chat is handed over to a human agent with a ticket',
                    ],
                    'integration' => [
                        'id' => 'integration',
                        'name' => 'Cross Platform Integration',
                        'tag' => $isId ? 'Update otomatis dari sistem' : 'Automated system updates',
                        'headline' => $isId ? 'Pelanggan selalu ter-update, secara otomatis.' : 'Keep customers updated, automatically.',
                        'desc' => $isId
                            ? 'Kirim pesan WhatsApp otomatis dari sistem bisnis Anda: pengingat, pesanan, pengiriman, janji temu.'
                            : 'Trigger WhatsApp messages from your business systems: reminders, orders, deliveries, appointments.',
                        'bullets' => [
                            $isId ? 'Pesan yang dipicu oleh event bisnis' : 'Messages triggered by business events',
                            $isId ? 'Template personal yang siap pakai' : 'Personalized, ready-to-use templates',
                            $isId ? 'Update pelanggan secara real-time' : 'Real-time customer updates',
                        ],
                        'alt' => $isId ? 'Ilustrasi animasi: pesanan di sistem bisnis memicu update WhatsApp otomatis' : 'Animated illustration: an order event in a business system becomes an automatic WhatsApp update',
                    ],
                    'flows' => [
                        'id' => 'flows',
                        'name' => 'WhatsApp Flows',
                        'tag' => $isId ? 'Formulir langsung di chat' : 'Forms inside the chat',
                        'headline' => $isId ? 'Kumpulkan data pelanggan langsung di chat.' : 'Collect customer data, right in the chat.',
                        'desc' => $isId
                            ? 'Pelanggan mengisi formulir interaktif tanpa keluar dari WhatsApp.'
                            : 'Customers fill in interactive forms without leaving WhatsApp.',
                        'bullets' => [
                            $isId ? 'Tanpa perlu aplikasi atau website' : 'No app or website needed',
                            $isId ? 'Formulir sesuai kebutuhan bisnis Anda' : 'Forms tailored to your business',
                            $isId ? 'Proses pengumpulan data yang lebih simpel' : 'A simpler data collection journey',
                        ],
                        'alt' => $isId ? 'Ilustrasi animasi: pelanggan mengisi form registrasi di dalam WhatsApp' : 'Animated illustration: a customer fills in a registration form inside WhatsApp',
                    ],
                    'omni' => [
                        'id' => 'omni',
                        'name' => 'Omnichannel',
                        'tag' => $isId ? 'Satu inbox untuk semua channel' : 'One inbox for all channels',
                        'headline' => $isId ? 'Semua percakapan, di satu tempat.' : 'All conversations, one place.',
                        'desc' => $isId
                            ? 'Kelola chat, DM, dan komentar dari WhatsApp, Instagram, Facebook, dan Live Chat dalam satu platform.'
                            : 'Manage chats, DMs and comments from WhatsApp, Instagram, Facebook and Live Chat in a single platform.',
                        'bullets' => [
                            $isId ? 'Satu inbox untuk semua channel' : 'One inbox for every channel',
                            $isId ? 'Pantau performa agent dan SLA' : 'Track agent performance and SLA',
                            $isId ? 'Ukur kepuasan dengan CSAT' : 'Measure satisfaction with CSAT',
                        ],
                        'alt' => $isId ? 'Ilustrasi animasi: pesan dari empat channel masuk ke satu inbox terpadu' : 'Animated illustration: messages from four channels flow into one unified inbox',
                    ],
                ],
            ],

            'platform_control' => [
                'title' => $isId ? 'Semua percakapan, tetap dalam kendali Anda.' : 'Stay in control of every conversation.',
                'cards' => [
                    [
                        'id' => 'supervisor',
                        'title' => $isId ? 'Mode supervisor' : 'Supervisor mode',
                        'desc' => $isId
                            ? 'Lead melihat chat timnya. Agent hanya melihat chat miliknya.'
                            : "Leads see their team's chats. Agents see only their own.",
                    ],
                    [
                        'id' => 'multi_account',
                        'title' => $isId ? 'Multi-akun' : 'Multi-account',
                        'desc' => $isId
                            ? 'Semua akun dan nomor bisnis, dalam satu platform.'
                            : 'All business accounts and numbers, one platform.',
                    ],
                    [
                        'id' => 'role_access',
                        'title' => $isId ? 'Akses berbasis peran' : 'Role-based access',
                        'desc' => $isId
                            ? 'Atur siapa yang bisa memakai modul, fitur, dan akun tertentu.'
                            : 'Decide who can use which modules, features and accounts.',
                    ],
                    [
                        'id' => 'history',
                        'title' => $isId ? 'Riwayat percakapan' : 'Conversation history',
                        'desc' => $isId
                            ? 'Semua chat tersimpan untuk visibilitas dan follow-up.'
                            : 'Every chat stored for visibility and follow-up.',
                    ],
                    [
                        'id' => 'ticketing',
                        'title' => 'Ticketing',
                        'desc' => $isId
                            ? 'Chat dengan agent? Tiket otomatis dibuat.'
                            : 'Chat with an agent? A ticket is created automatically.',
                    ],
                ],
            ],

            'why' => [
                'title' => [
                    'prefix' => $isId ? 'Kenapa Kouventa untuk customer' : 'Why Kouventa for customer',
                    'main' => 'engagement',
                ],
                'cards' => [
                    [
                        'id' => 'meta_bsp',
                        'title' => 'Official Meta Business Solution Provider',
                        'desc' => $isId
                            ? 'Akses resmi ke WhatsApp Business Platform.'
                            : 'Trusted access to the WhatsApp Business Platform.',
                    ],
                    [
                        'id' => 'custom_business',
                        'title' => $isId ? 'Dibangun sesuai bisnis Anda' : 'Built around your business',
                        'desc' => $isId
                            ? 'Disesuaikan dengan industri, alur, dan sistem Anda.'
                            : 'Customized to your industry, flows and systems.',
                    ],
                    [
                        'id' => 'secure_trusted',
                        'title' => $isId ? 'Aman & terpercaya' : 'Secure & trusted',
                        'desc' => $isId
                            ? 'Keamanan kelas enterprise untuk data bisnis dan pelanggan.'
                            : 'Enterprise-grade security for business and customer data.',
                    ],
                    [
                        'id' => 'post_paid',
                        'title' => $isId ? 'Pesan pascabayar' : 'Post-paid messaging',
                        'desc' => $isId
                            ? 'Kirim dulu. Bayar belakangan, sesuai pemakaian.'
                            : 'Send first. Pay later, for what you actually use.',
                    ],
                ],
            ],

            'cta' => [
                'title' => $isId ? 'Mulai terhubung dengan pelanggan lewat WhatsApp' : 'Start engaging customers on WhatsApp',
                'primary_btn' => [
                    'text' => $isId ? 'Jadwalkan demo' : 'Book a demo',
                    'url' => '/book-demo',
                ],
                'secondary_btn' => [
                    'text' => $isId ? 'Lihat harga' : 'See pricing',
                    'url' => '/pricing?tab=ce',
                ],
            ],
        ];
    }
}

