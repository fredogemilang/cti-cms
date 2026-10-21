<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Database\Seeder;

class KouventaPageBlocksSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedHomePage();
        $this->seedAboutPage();
        $this->seedBookDemoPage();
        $this->seedInsightPage();
        $this->seedPricingPage();
        $this->seedProductAiPage();
        $this->seedProductCePage();
        $this->seedSolutionAiPage();
        $this->seedSolutionCePage();

        $this->command->info('Kouventa PageBlocks seeded successfully for all 9 pages.');
    }

    protected function saveBlock(int $pageId, string $name, string $type, string $label, $enValue, $idValue, int $order): void
    {
        $enEncoded = is_array($enValue) ? json_encode($enValue) : $enValue;
        $idEncoded = is_array($idValue) ? json_encode($idValue) : $idValue;

        PageBlock::updateOrCreate(
            ['page_id' => $pageId, 'name' => $name],
            [
                'type' => $type,
                'label' => $label,
                'value' => $enEncoded,
                'translations' => [
                    'id' => [
                        'value' => $idEncoded,
                    ],
                ],
                'order' => $order,
                'is_active' => true,
            ]
        );
    }

    protected function seedHomePage(): void
    {
        $page = Page::where('slug', 'home')->first();
        if (! $page) return;

        $blocks = [
            [
                'name' => 'hero_title',
                'type' => 'title',
                'label' => 'Hero Main Title',
                'en' => ['prefix' => 'AI adoption,', 'main' => 'done right.'],
                'id' => ['prefix' => 'Adopsi AI', 'main' => 'yang tepat.'],
            ],
            [
                'name' => 'hero_subtitle',
                'type' => 'textarea',
                'label' => 'Hero Subtitle',
                'en' => 'For your customers. For your teams. One enterprise AI platform, your organization’s rules.',
                'id' => 'Untuk pelanggan Anda. Untuk tim Anda. Satu platform AI enterprise, dengan aturan organisasi Anda.',
            ],
            [
                'name' => 'hero_cta',
                'type' => 'button',
                'label' => 'Hero CTA Button',
                'en' => ['text' => 'See how it fits your business', 'url' => '#pick-team', 'target' => '_self'],
                'id' => ['text' => 'Lihat kecocokannya untuk bisnis Anda', 'url' => '#pick-team', 'target' => '_self'],
            ],
            [
                'name' => 'paths_title',
                'type' => 'title',
                'label' => 'Two Paths Title',
                'en' => ['prefix' => '', 'main' => 'Where do you want to start?'],
                'id' => ['prefix' => '', 'main' => 'Di mana Anda ingin memulai?'],
            ],
            [
                'name' => 'path1_badge',
                'type' => 'text',
                'label' => 'Path 1 Badge',
                'en' => 'AI that works',
                'id' => 'AI yang bekerja',
            ],
            [
                'name' => 'path1_title',
                'type' => 'title',
                'label' => 'Path 1 Title',
                'en' => ['prefix' => 'Inside your', 'main' => 'organization.'],
                'id' => ['prefix' => 'Di dalam', 'main' => 'organisasi Anda.'],
            ],
            [
                'name' => 'path1_desc',
                'type' => 'textarea',
                'label' => 'Path 1 Description',
                'en' => 'Instant answers from your company knowledge, and agents that take routine tasks off your teams.',
                'id' => 'Jawaban instan dari knowledge perusahaan Anda, dan agen yang mengambil alih tugas rutin tim Anda.',
            ],
            [
                'name' => 'path1_button',
                'type' => 'button',
                'label' => 'Path 1 Link',
                'en' => ['text' => 'Explore more', 'url' => '/product/ai', 'target' => '_self'],
                'id' => ['text' => 'Pelajari lebih lanjut', 'url' => '/product/ai', 'target' => '_self'],
            ],
            [
                'name' => 'path2_badge',
                'type' => 'text',
                'label' => 'Path 2 Badge',
                'en' => 'AI that engages',
                'id' => 'AI yang berinteraksi',
            ],
            [
                'name' => 'path2_title',
                'type' => 'title',
                'label' => 'Path 2 Title',
                'en' => ['prefix' => 'With your', 'main' => 'customers.'],
                'id' => ['prefix' => 'Dengan', 'main' => 'pelanggan Anda.'],
            ],
            [
                'name' => 'path2_desc',
                'type' => 'textarea',
                'label' => 'Path 2 Description',
                'en' => 'Chatbots reply first on WhatsApp, Instagram, Facebook and Live Chat, then hand off to your agents when needed.',
                'id' => 'Chatbot membalas lebih dulu di WhatsApp, Instagram, Facebook dan Live Chat, lalu meneruskan ke agen Anda jika diperlukan.',
            ],
            [
                'name' => 'path2_button',
                'type' => 'button',
                'label' => 'Path 2 Link',
                'en' => ['text' => 'Explore more', 'url' => '/product/customer-engagement', 'target' => '_self'],
                'id' => ['text' => 'Pelajari lebih lanjut', 'url' => '/product/customer-engagement', 'target' => '_self'],
            ],
            [
                'name' => 'industries_title',
                'type' => 'text',
                'label' => 'Industries Section Title',
                'en' => 'Proven across industries.',
                'id' => 'Terbukti di berbagai industri.',
            ],
            [
                'name' => 'teams_title',
                'type' => 'text',
                'label' => 'Teams Section Title',
                'en' => 'Pick your team to see how it works for your organization.',
                'id' => 'Pilih tim Anda untuk melihat cara kerjanya di organisasi Anda.',
            ],
            [
                'name' => 'why_title',
                'type' => 'title',
                'label' => 'Why Section Title',
                'en' => ['prefix' => 'Enterprise ready', 'main' => 'from day one.'],
                'id' => ['prefix' => 'Enterprise ready', 'main' => 'sejak hari pertama.'],
            ],
            [
                'name' => 'partners_title',
                'type' => 'text',
                'label' => 'Partners Section Title',
                'en' => 'Technology Partners',
                'id' => 'Partner Teknologi',
            ],
            [
                'name' => 'customers_title',
                'type' => 'text',
                'label' => 'Customers Section Title',
                'en' => 'They trusted us.',
                'id' => 'Mereka mempercayai kami.',
            ],
            [
                'name' => 'results_title',
                'type' => 'text',
                'label' => 'Testimonials Section Title',
                'en' => 'Real results, in their words.',
                'id' => 'Hasil nyata, dari cerita mereka.',
            ],
            [
                'name' => 'cta_title',
                'type' => 'text',
                'label' => 'Bottom CTA Title',
                'en' => 'Ready to see it work for your business?',
                'id' => 'Siap mengubah cara bisnis Anda beroperasi dengan AI?',
            ],
            [
                'name' => 'cta_subtitle',
                'type' => 'textarea',
                'label' => 'Bottom CTA Subtitle',
                'en' => "Book a demo and we'll walk you through it with your own use case.",
                'id' => 'Jadwalkan demo dan kami tunjukkan langsung dengan use case Anda sendiri.',
            ],
            [
                'name' => 'cta_demo_button',
                'type' => 'button',
                'label' => 'CTA Book Demo Button',
                'en' => ['text' => 'Book a demo', 'url' => '/book-demo', 'target' => '_self'],
                'id' => ['text' => 'Jadwalkan demo', 'url' => '/book-demo', 'target' => '_self'],
            ],
            [
                'name' => 'cta_pricing_button',
                'type' => 'button',
                'label' => 'CTA See Pricing Button',
                'en' => ['text' => 'See pricing', 'url' => '/pricing', 'target' => '_self'],
                'id' => ['text' => 'Lihat harga', 'url' => '/pricing', 'target' => '_self'],
            ],
            [
                'name' => 'videos_title',
                'type' => 'text',
                'label' => 'Videos Section Title',
                'en' => 'See Kouventa in action.',
                'id' => 'Lihat cara kerja Kouventa.',
            ],
        ];

        foreach ($blocks as $idx => $b) {
            $this->saveBlock($page->id, $b['name'], $b['type'], $b['label'], $b['en'], $b['id'], $idx);
        }
    }

    protected function seedAboutPage(): void
    {
        $page = Page::where('slug', 'about')->first();
        if (! $page) return;

        $blocks = [
            [
                'name' => 'hero_badge',
                'type' => 'text',
                'label' => 'Hero Badge',
                'en' => 'About us',
                'id' => 'Tentang kami',
            ],
            [
                'name' => 'hero_title',
                'type' => 'title',
                'label' => 'Hero Main Title',
                'en' => ['prefix' => 'From business communication', 'main' => 'to enterprise AI.'],
                'id' => ['prefix' => 'Dari komunikasi bisnis', 'main' => 'menuju enterprise AI.'],
            ],
            [
                'name' => 'values_badge',
                'type' => 'text',
                'label' => 'Values Section Badge',
                'en' => 'Our value',
                'id' => 'Nilai kami',
            ],
            [
                'name' => 'history_title',
                'type' => 'text',
                'label' => 'History Section Title',
                'en' => 'History',
                'id' => 'Perjalanan Kami',
            ],
        ];

        foreach ($blocks as $idx => $b) {
            $this->saveBlock($page->id, $b['name'], $b['type'], $b['label'], $b['en'], $b['id'], $idx);
        }
    }

    protected function seedBookDemoPage(): void
    {
        $page = Page::where('slug', 'book-demo')->first();
        if (! $page) return;

        $blocks = [
            [
                'name' => 'hero_title',
                'type' => 'title',
                'label' => 'Hero Main Title',
                'en' => ['prefix' => 'Get in touch', 'main' => 'with us.'],
                'id' => ['prefix' => 'Hubungi', 'main' => 'kami.'],
            ],
            [
                'name' => 'hero_badge',
                'type' => 'text',
                'label' => 'Hero Badge / Tagline',
                'en' => 'Enjoy a 30-day free trial',
                'id' => 'Nikmati free trial 30 hari',
            ],
            [
                'name' => 'location_title',
                'type' => 'text',
                'label' => 'Location Label',
                'en' => 'Location',
                'id' => 'Lokasi',
            ],
            [
                'name' => 'location_text',
                'type' => 'textarea',
                'label' => 'Office Address',
                'en' => 'Centennial Tower, 12th Floor, Jl. Jend. Gatot Subroto Kav. 24-25, Jakarta',
                'id' => 'Centennial Tower, Lantai 12, Jl. Jend. Gatot Subroto Kav. 24-25, Jakarta',
            ],
            [
                'name' => 'phone_title',
                'type' => 'text',
                'label' => 'Phone Label',
                'en' => 'Phone',
                'id' => 'Telepon',
            ],
            [
                'name' => 'phone_number',
                'type' => 'text',
                'label' => 'Phone Number',
                'en' => '+62 811 8496 889',
                'id' => '+62 811 8496 889',
            ],
            [
                'name' => 'email_title',
                'type' => 'text',
                'label' => 'Email Label',
                'en' => 'Email',
                'id' => 'Email',
            ],
            [
                'name' => 'email_address',
                'type' => 'text',
                'label' => 'Email Address',
                'en' => 'kouventa@helios.id',
                'id' => 'kouventa@helios.id',
            ],
        ];

        foreach ($blocks as $idx => $b) {
            $this->saveBlock($page->id, $b['name'], $b['type'], $b['label'], $b['en'], $b['id'], $idx);
        }
    }

    protected function seedInsightPage(): void
    {
        $page = Page::where('slug', 'insight')->first();
        if (! $page) return;

        $blocks = [
            [
                'name' => 'hero_badge',
                'type' => 'text',
                'label' => 'Hero Badge',
                'en' => 'Blog',
                'id' => 'Blog',
            ],
            [
                'name' => 'hero_title',
                'type' => 'title',
                'label' => 'Hero Main Title',
                'en' => ['prefix' => 'Explore the newest articles', 'main' => 'in our blog.'],
                'id' => ['prefix' => 'Jelajahi artikel terbaru', 'main' => 'di blog kami.'],
            ],
        ];

        foreach ($blocks as $idx => $b) {
            $this->saveBlock($page->id, $b['name'], $b['type'], $b['label'], $b['en'], $b['id'], $idx);
        }
    }

    protected function seedPricingPage(): void
    {
        $page = Page::where('slug', 'pricing')->first();
        if (! $page) return;

        $blocks = [
            [
                'name' => 'hero_badge',
                'type' => 'text',
                'label' => 'Hero Badge',
                'en' => 'PRICING',
                'id' => 'HARGA',
            ],
            [
                'name' => 'hero_title',
                'type' => 'title',
                'label' => 'Hero Main Title',
                'en' => ['prefix' => 'Intelligence you', 'main' => 'control, priced for how you grow.'],
                'id' => ['prefix' => 'Kecerdasan dalam', 'main' => 'kendali Anda, dengan harga yang mengikuti pertumbuhan bisnis.'],
            ],
            [
                'name' => 'hero_subtitle',
                'type' => 'textarea',
                'label' => 'Hero Subtitle',
                'en' => 'Two platforms, three tiers each. Start where your team is today, and scale without switching vendors.',
                'id' => 'Dua platform, masing-masing tiga paket. Mulai dari kebutuhan tim Anda saat ini, dan tingkatkan tanpa perlu ganti vendor.',
            ],
            [
                'name' => 'calc_title',
                'type' => 'text',
                'label' => 'Calculator Section Title',
                'en' => 'Estimate your monthly WhatsApp costs.',
                'id' => 'Estimasi biaya bulanan WhatsApp Anda.',
            ],
            [
                'name' => 'calc_subtitle',
                'type' => 'textarea',
                'label' => 'Calculator Section Subtitle',
                'en' => 'Kouventa passes Meta conversation fees at cost with zero markup.',
                'id' => 'Kouventa meneruskan biaya percakapan Meta sesuai tarif resmi tanpa markup.',
            ],
            [
                'name' => 'faq_title',
                'type' => 'text',
                'label' => 'FAQ Section Title',
                'en' => 'Frequently asked questions',
                'id' => 'Pertanyaan yang sering diajukan',
            ],
            [
                'name' => 'cta_title',
                'type' => 'text',
                'label' => 'Custom Setup CTA Title',
                'en' => 'Need a custom deployment or on-premise setup?',
                'id' => 'Butuh deployment khusus atau instalasi on-premise?',
            ],
            [
                'name' => 'cta_subtitle',
                'type' => 'textarea',
                'label' => 'Custom Setup CTA Subtitle',
                'en' => 'Talk to our solution architects about compliance, private cloud, and volume pricing.',
                'id' => 'Konsultasikan dengan tim solution architect kami mengenai kepatuhan regulasi, private cloud, dan harga volume.',
            ],
        ];

        foreach ($blocks as $idx => $b) {
            $this->saveBlock($page->id, $b['name'], $b['type'], $b['label'], $b['en'], $b['id'], $idx);
        }
    }

    protected function seedProductAiPage(): void
    {
        $page = Page::where('slug', 'product/ai')->first();
        if (! $page) return;

        $blocks = [
            [
                'name' => 'hero_title',
                'type' => 'title',
                'label' => 'Hero Main Title',
                'en' => ['prefix' => 'One AI platform is', 'main' => 'all you need.'],
                'id' => ['prefix' => 'Satu platform AI adalah', 'main' => 'semua yang Anda butuhkan.'],
            ],
            [
                'name' => 'hero_subtitle',
                'type' => 'textarea',
                'label' => 'Hero Subtitle',
                'en' => 'Build AI agents with ease and put them to work on what your organization needs, with control you can measure.',
                'id' => 'Bangun AI agent dengan mudah dan pekerjakan untuk kebutuhan organisasi Anda, dengan kendali terukur.',
            ],
            [
                'name' => 'hero_cta',
                'type' => 'button',
                'label' => 'Hero CTA Button',
                'en' => ['text' => 'Book a demo', 'url' => '/book-demo', 'target' => '_self'],
                'id' => ['text' => 'Jadwalkan demo', 'url' => '/book-demo', 'target' => '_self'],
            ],
            [
                'name' => 'hero_secondary_cta',
                'type' => 'button',
                'label' => 'Hero Secondary Button',
                'en' => ['text' => 'Explore products', 'url' => '#ai-products', 'target' => '_self'],
                'id' => ['text' => 'Jelajahi produk', 'url' => '#ai-products', 'target' => '_self'],
            ],
            [
                'name' => 'challenges_title',
                'type' => 'title',
                'label' => 'Challenges Section Title',
                'en' => ['prefix' => 'Too many AI tools,', 'main' => 'too little control.'],
                'id' => ['prefix' => 'Terlalu banyak AI tool,', 'main' => 'terlalu minim kendali.'],
            ],
            [
                'name' => 'products_title',
                'type' => 'text',
                'label' => 'Products Section Title',
                'en' => 'Four specialized products, one unified platform.',
                'id' => 'Empat produk terspesialisasi, satu platform terpadu.',
            ],
            [
                'name' => 'security_title',
                'type' => 'title',
                'label' => 'Security Section Title',
                'en' => ['prefix' => 'Enterprise AI needs', 'main' => 'enterprise guardrails.'],
                'id' => ['prefix' => 'Enterprise AI butuh', 'main' => 'standar keamanan enterprise.'],
            ],
            [
                'name' => 'security_subtitle',
                'type' => 'textarea',
                'label' => 'Security Section Subtitle',
                'en' => 'Built for data privacy, compliance, and control from the ground up.',
                'id' => 'Dirancang untuk privasi data, kepatuhan, dan kendali sejak awal.',
            ],
            [
                'name' => 'deploy_title',
                'type' => 'title',
                'label' => 'Deploy Section Title',
                'en' => ['prefix' => 'Deploy where your', 'main' => 'data lives.'],
                'id' => ['prefix' => 'Deploy di mana', 'main' => 'data Anda berada.'],
            ],
            [
                'name' => 'bottom_cta_title',
                'type' => 'text',
                'label' => 'Bottom CTA Title',
                'en' => 'Ready to take control of AI in your organization?',
                'id' => 'Siap mengambil kendali AI di organisasi Anda?',
            ],
            [
                'name' => 'bottom_cta_subtitle',
                'type' => 'textarea',
                'label' => 'Bottom CTA Subtitle',
                'en' => 'Book a demo to see how Kouventa unifies AI adoption with enterprise control.',
                'id' => 'Jadwalkan demo untuk melihat bagaimana Kouventa menyatukan adopsi AI dengan kontrol enterprise.',
            ],
        ];

        foreach ($blocks as $idx => $b) {
            $this->saveBlock($page->id, $b['name'], $b['type'], $b['label'], $b['en'], $b['id'], $idx);
        }
    }

    protected function seedProductCePage(): void
    {
        $page = Page::where('slug', 'product/customer-engagement')->first();
        if (! $page) return;

        $blocks = [
            [
                'name' => 'hero_title',
                'type' => 'title',
                'label' => 'Hero Main Title',
                'en' => ['prefix' => 'The smarter way to', 'main' => 'engage customers.'],
                'id' => ['prefix' => 'Cara lebih cerdas untuk', 'main' => 'berinteraksi dengan pelanggan.'],
            ],
            [
                'name' => 'hero_subtitle',
                'type' => 'textarea',
                'label' => 'Hero Subtitle',
                'en' => 'Reach more customers, respond faster, and keep every conversation seamless — on WhatsApp and beyond.',
                'id' => 'Jangkau lebih banyak pelanggan, tanggapi lebih cepat, dan jaga setiap percakapan tetap mulus — di WhatsApp dan seterusnya.',
            ],
            [
                'name' => 'hero_cta',
                'type' => 'button',
                'label' => 'Hero CTA Button',
                'en' => ['text' => 'Book a demo', 'url' => '/book-demo', 'target' => '_self'],
                'id' => ['text' => 'Jadwalkan demo', 'url' => '/book-demo', 'target' => '_self'],
            ],
            [
                'name' => 'channels_title',
                'type' => 'title',
                'label' => 'Channels Section Title',
                'en' => ['prefix' => 'Every missed message is a', 'main' => 'missed sale.'],
                'id' => ['prefix' => 'Setiap pesan yang terlewat adalah', 'main' => 'penjualan yang hilang.'],
            ],
            [
                'name' => 'features_title',
                'type' => 'title',
                'label' => 'Features Section Title',
                'en' => ['prefix' => 'One platform.', 'main' => 'Zero missed messages.'],
                'id' => ['prefix' => 'Satu platform.', 'main' => 'Nol pesan yang terlewat.'],
            ],
            [
                'name' => 'why_title',
                'type' => 'title',
                'label' => 'Why CE Section Title',
                'en' => ['prefix' => 'Why Kouventa for customer', 'main' => 'engagement'],
                'id' => ['prefix' => 'Mengapa Kouventa untuk customer', 'main' => 'engagement'],
            ],
            [
                'name' => 'bottom_cta_title',
                'type' => 'text',
                'label' => 'Bottom CTA Title',
                'en' => 'Start engaging customers on WhatsApp.',
                'id' => 'Mulai berinteraksi dengan pelanggan di WhatsApp.',
            ],
            [
                'name' => 'bottom_cta_subtitle',
                'type' => 'textarea',
                'label' => 'Bottom CTA Subtitle',
                'en' => 'Connect with our team to set up your WhatsApp Business Platform account, tailored templates, and verified badge.',
                'id' => 'Hubungi tim kami untuk menyiapkan akun WhatsApp Business Platform, template khusus, dan lencana terverifikasi Anda.',
            ],
        ];

        foreach ($blocks as $idx => $b) {
            $this->saveBlock($page->id, $b['name'], $b['type'], $b['label'], $b['en'], $b['id'], $idx);
        }
    }

    protected function seedSolutionAiPage(): void
    {
        $page = Page::where('slug', 'solutions/ai')->first();
        if (! $page) return;

        $blocks = [
            [
                'name' => 'hero_title',
                'type' => 'title',
                'label' => 'Hero Main Title',
                'en' => ['prefix' => 'From knowledge to action', 'main' => 'with AI.'],
                'id' => ['prefix' => 'Dari pengetahuan ke tindakan', 'main' => 'dengan AI.'],
            ],
            [
                'name' => 'hero_subtitle',
                'type' => 'textarea',
                'label' => 'Hero Subtitle',
                'en' => 'From knowledge retrieval to process automation, Kouventa AI helps businesses turn information into actionable insights and automate business processes with AI-powered autonomous agents.',
                'id' => 'Mulai dari pencarian informasi hingga otomatisasi proses, Kouventa AI membantu bisnis mengubah data menjadi wawasan yang dapat ditindaklanjuti dan mengotomatiskan proses bisnis dengan AI autonomous agent.',
            ],
            [
                'name' => 'hero_cta',
                'type' => 'button',
                'label' => 'Hero CTA Button',
                'en' => ['text' => 'Request a Demo', 'url' => '/book-demo', 'target' => '_self'],
                'id' => ['text' => 'Minta Demo', 'url' => '/book-demo', 'target' => '_self'],
            ],
            [
                'name' => 'bottom_cta_title',
                'type' => 'text',
                'label' => 'Bottom CTA Title',
                'en' => 'Start simple. Scale with confidence.',
                'id' => 'Mulai dengan sederhana. Berkembang dengan percaya diri.',
            ],
            [
                'name' => 'bottom_cta_subtitle',
                'type' => 'textarea',
                'label' => 'Bottom CTA Subtitle',
                'en' => "Begin with everyday AI for your employees, governed by your organization. Grow into AI agents when you're ready.",
                'id' => 'Mulai dengan AI harian untuk karyawan Anda, yang dikelola sesuai aturan organisasi. Kembangkan ke AI agent saat Anda siap.',
            ],
        ];

        foreach ($blocks as $idx => $b) {
            $this->saveBlock($page->id, $b['name'], $b['type'], $b['label'], $b['en'], $b['id'], $idx);
        }
    }

    protected function seedSolutionCePage(): void
    {
        $page = Page::where('slug', 'solutions/customer-engagement')->first();
        if (! $page) return;

        $blocks = [
            [
                'name' => 'hero_title',
                'type' => 'title',
                'label' => 'Hero Main Title',
                'en' => ['prefix' => 'Seamless omnichannel', 'main' => 'customer experience.'],
                'id' => ['prefix' => 'Pengalaman pelanggan', 'main' => 'omnichannel yang mulus.'],
            ],
            [
                'name' => 'hero_subtitle',
                'type' => 'textarea',
                'label' => 'Hero Subtitle',
                'en' => 'From marketing campaigns and customer support to operational updates, Kouventa helps businesses connect, respond, and communicate more effectively.',
                'id' => 'Mulai dari kampanye pemasaran dan dukungan pelanggan hingga pembaruan operasional, Kouventa membantu bisnis terhubung, merespons, dan berkomunikasi lebih efektif.',
            ],
            [
                'name' => 'hero_cta',
                'type' => 'button',
                'label' => 'Hero CTA Button',
                'en' => ['text' => 'Request a Demo', 'url' => '/book-demo', 'target' => '_self'],
                'id' => ['text' => 'Minta Demo', 'url' => '/book-demo', 'target' => '_self'],
            ],
            [
                'name' => 'bottom_cta_title',
                'type' => 'text',
                'label' => 'Bottom CTA Title',
                'en' => 'Ready to improve every customer conversation?',
                'id' => 'Siap meningkatkan setiap percakapan pelanggan?',
            ],
            [
                'name' => 'bottom_cta_subtitle',
                'type' => 'textarea',
                'label' => 'Bottom CTA Subtitle',
                'en' => "Tell us about your team's needs and we'll show you how Kouventa fits.",
                'id' => 'Ceritakan kebutuhan tim Anda dan kami akan tunjukkan bagaimana Kouventa dapat membantu.',
            ],
        ];

        foreach ($blocks as $idx => $b) {
            $this->saveBlock($page->id, $b['name'], $b['type'], $b['label'], $b['en'], $b['id'], $idx);
        }
    }
}
