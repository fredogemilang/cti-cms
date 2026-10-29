<?php

namespace Plugins\KouventaPages\Schemas;

class ProductAiSchema
{
    /**
     * Get list of admin section tabs for this page.
     */
    public static function getTabs(): array
    {
        return [
            ['id' => 'hero', 'label' => '1. Hero & Subnav', 'icon' => 'home'],
            ['id' => 'challenges', 'label' => '2. The Challenges (3 Cards)', 'icon' => 'warning'],
            ['id' => 'journey', 'label' => '3. Enterprise AI Journey', 'icon' => 'alt_route'],
            ['id' => 'products', 'label' => '4. Our Products (4 Flagships)', 'icon' => 'apps'],
            ['id' => 'governance', 'label' => '5. Platform Control (6 Cards)', 'icon' => 'shield'],
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
                'title' => [
                    'prefix' => $isId ? 'Satu platform AI' : 'One AI platform is',
                    'main' => $isId ? 'sudah cukup' : 'all you need',
                ],
                'subtitle' => $isId
                    ? 'Bangun AI agent dengan mudah dan gunakan untuk kebutuhan organisasi Anda, dengan kontrol yang terukur'
                    : 'Build AI agents with ease and put them to work on what your organization needs, with control you can measure',
                'primary_cta' => [
                    'text' => $isId ? 'Jadwalkan demo' : 'Book a demo',
                    'url' => '/book-demo',
                ],
                'secondary_cta' => [
                    'text' => $isId ? 'Jelajahi produk' : 'Explore products',
                    'url' => '#ai-products',
                ],
            ],

            'challenges' => [
                'title' => [
                    'prefix' => $isId ? 'Terlalu banyak tools AI,' : 'Too many AI tools,',
                    'main' => $isId ? 'terlalu sedikit kontrol' : 'too little control',
                ],
                'cards' => [
                    [
                        'id' => 'scattered',
                        'title' => $isId ? 'Tools AI tersebar di banyak tim' : 'AI tools are scattered across teams',
                        'desc' => $isId
                            ? 'Setiap tim memakai tools AI yang berbeda sesuai kebutuhannya, sehingga pemakaian AI di organisasi jadi terpecah-pecah.'
                            : 'Different teams use different AI tools based on their needs, creating a fragmented AI environment across the organization.',
                    ],
                    [
                        'id' => 'control',
                        'title' => $isId ? 'Kontrol pemakaian AI terbatas' : 'Limited control over AI usage',
                        'desc' => $isId
                            ? 'Karena tools AI dikelola terpisah, Anda sulit mengontrol cara pakainya dan memastikan data bisnis tetap aman.'
                            : 'With AI tools managed separately, it becomes harder to control how AI is used and ensure business data is handled securely.',
                    ],
                    [
                        'id' => 'billing',
                        'title' => $isId ? 'Pemakaian & tagihan AI sulit dikelola' : 'AI usage & billing are hard to manage',
                        'desc' => $isId
                            ? 'Langganan AI yang terpisah-pisah membuat pemakaian sulit dipusatkan, langganan sulit diatur, dan pengeluaran tiap tim susah dipantau.'
                            : 'Multiple AI subscriptions make it difficult to centralize usage, manage subscriptions, and keep track of spending across teams.',
                    ],
                ],
            ],

            'journey' => [
                'title' => [
                    'prefix' => $isId ? 'Semua AI Anda di satu tempat,' : 'Bring your AI together,',
                    'main' => $isId ? 'dari menjawab sampai bertindak' : 'from answers to action',
                ],
                'subtitle' => $isId
                    ? 'Kouventa AI membantu bisnis mengubah AI menjadi hasil nyata, mulai dari menjawab pertanyaan dan menggali insight, hingga menjalankan proses bisnis secara otonom lintas sistem.'
                    : 'Kouventa AI helps businesses turn AI into practical business outcomes, from answering questions and uncovering insights to autonomously executing business processes across systems.',
                'stages' => [
                    [
                        'id' => 'answer',
                        'number' => '01',
                        'name' => $isId ? 'Tanya Jawab' : 'Answer',
                        'headline' => $isId
                            ? 'Beri semua orang jawaban yang cepat dan akurat dari pengetahuan perusahaan Anda sendiri.'
                            : 'Give everyone fast, accurate answers from your own knowledge.',
                        'sub' => $isId
                            ? 'Kouventa AI mengubah dokumen dan pengetahuan perusahaan menjadi asisten AI yang membantu pelanggan dan karyawan menemukan informasi yang tepat secara instan, tanpa harus mencari di banyak file atau menunggu tim lain.'
                            : "Kouventa AI turns your company's documents and knowledge into an AI assistant that helps customers and employees find the right information instantly, without searching through files or waiting for another team.",
                        'cta' => [
                            'text' => $isId ? 'Lihat produk untuk Tanya Jawab' : 'See the products for Answer',
                            'default_product' => 'wiki',
                        ],
                    ],
                    [
                        'id' => 'analyze',
                        'number' => '02',
                        'name' => $isId ? 'Analisis' : 'Analyze',
                        'headline' => $isId
                            ? 'Ubah data Anda menjadi insight untuk keputusan yang lebih baik.'
                            : 'Turn your data into insights that drive better decisions.',
                        'sub' => $isId
                            ? 'Lebih dari sekadar menjawab pertanyaan. Kouventa AI menganalisis data bisnis dan percakapan untuk menemukan pola, menyoroti risiko dan peluang, serta merekomendasikan langkah selanjutnya.'
                            : 'Go beyond answering questions. Kouventa AI analyzes business data and conversations to uncover patterns, highlight risks and opportunities, and recommend what to do next.',
                        'cta' => [
                            'text' => $isId ? 'Lihat produk untuk Analisis' : 'See the products for Analyze',
                            'default_product' => 'taskforce',
                        ],
                    ],
                    [
                        'id' => 'execute',
                        'number' => '03',
                        'name' => $isId ? 'Eksekusi' : 'Execute',
                        'headline' => $isId
                            ? 'Biarkan AI bertindak, dengan persetujuan Anda.'
                            : 'Let AI take action, with your approval.',
                        'sub' => $isId
                            ? 'Setelah rekomendasi disetujui, agent Kouventa AI menalar, merencanakan, dan mengerjakan tugasnya di sistem yang terhubung, sehingga insight berubah menjadi pekerjaan yang selesai.'
                            : 'Once a recommendation is approved, Kouventa AI agents reason, plan, and carry out the task across connected systems, so insights turn into completed work.',
                        'cta' => [
                            'text' => $isId ? 'Lihat produk untuk Eksekusi' : 'See the products for Execute',
                            'default_product' => 'taskforce',
                        ],
                    ],
                ],
            ],

            'products' => [
                'title' => $isId ? 'Produk yang membuat Anda tetap memegang kendali' : 'The products that put you in control',
                'items' => [
                    'wiki' => [
                        'name' => 'AI Company Wikipedia',
                        'tag' => $isId ? 'Asisten pengetahuan' : 'Knowledge assistant',
                        'headline' => $isId
                            ? 'Chatbot berbasis AI yang memahami konteks pertanyaan Anda dan memberi jawaban cepat dan akurat dalam hitungan detik, bersumber dari pengetahuan perusahaan, website, database, dan sumber data lain yang terhubung.'
                            : 'An AI chatbot that understands the context of your questions and delivers fast, accurate answers in seconds, drawing from your company knowledge, websites, databases, and other connected data sources.',
                        'features' => [
                            [
                                'title' => $isId ? 'Jawaban akurat, lebih cepat' : 'Reliable answers, faster',
                                'desc' => $isId ? 'Ajukan pertanyaan dan dapatkan informasi yang tepat dalam hitungan detik.' : 'Ask a question and get the right information in seconds.',
                            ],
                            [
                                'title' => $isId ? 'Sumber rujukan' : 'Source citations',
                                'desc' => $isId ? 'Setiap jawaban disertai sumber agar lebih transparan.' : 'Every answer is backed by sources for greater transparency.',
                            ],
                            [
                                'title' => $isId ? '19+ format sumber data' : '19+ data source formats',
                                'desc' => $isId ? 'Cari di dokumen, database, website, dan sistem bisnis.' : 'Search across documents, databases, websites, and business systems.',
                            ],
                            [
                                'title' => $isId ? 'Pengalihan ke manusia' : 'Human handover',
                                'desc' => $isId ? 'Alihkan percakapan ke agent manusia dengan mulus saat dibutuhkan.' : 'Seamlessly hand conversations over to human agents when needed.',
                            ],
                        ],
                    ],

                    'taskforce' => [
                        'name' => 'AI Task Force',
                        'tag' => 'Agentic AI',
                        'headline' => $isId
                            ? 'Solusi berbasis Agentic AI yang memahami tujuan bisnis Anda dan menjalankan workflow kompleks di sistem bisnis yang terhubung, menalar setiap tugas dan meminta persetujuan Anda di tahap penting.'
                            : 'An Agentic AI solution that understands your business objectives and executes complex workflows across connected business systems, reasoning through tasks and seeking your approval at critical steps.',
                        'features' => [
                            [
                                'title' => $isId ? 'Otomasi proses menyeluruh' : 'Full process automation',
                                'desc' => $isId ? 'Otomasi proses bisnis lintas sistem.' : 'Automate business processes across multiple systems.',
                            ],
                            [
                                'title' => $isId ? 'Analisis yang memahami konteks' : 'Contextual analysis',
                                'desc' => $isId ? 'Menganalisis konteks dan data untuk menentukan langkah terbaik.' : 'Analyze context and data to determine the best course of action.',
                            ],
                            [
                                'title' => $isId ? 'Kolaborasi multi agent' : 'Multi agent collaboration',
                                'desc' => $isId ? 'Agent dengan keahlian khusus bekerja sama menyelesaikan tugas kompleks.' : 'Specialized agents work together to complete complex tasks.',
                            ],
                            [
                                'title' => $isId ? 'Konektor MCP' : 'MCP connectors',
                                'desc' => $isId ? 'Jalankan aksi di ERP, CRM, OMS, HRIS, dan lainnya.' : 'Execute actions across ERP, CRM, OMS, HRIS, and more.',
                            ],
                            [
                                'title' => $isId ? 'Gerbang persetujuan manusia' : 'Human approval gates',
                                'desc' => $isId ? 'Tinjau dan setujui aksi penting sebelum dijalankan.' : 'Review and approve critical actions before they run.',
                            ],
                        ],
                    ],

                    'playground' => [
                        'name' => 'Chat Playground',
                        'tag' => $isId ? 'Bekerja dengan Wikipedia & Task Force' : 'Works with Wikipedia & Task Force',
                        'headline' => $isId
                            ? 'Chat Playground adalah workspace terkontrol untuk chat dengan AI Company Wikipedia, serta memicu, menjalankan, dan memantau agent AI Task Force, secara aman, efisien, dan sesuai batasan Anda.'
                            : 'Chat Playground is your controlled workspace to chat with AI Company Wikipedia, and to trigger, run, and monitor AI Task Force agents, securely, efficiently, and within your boundaries.',
                        'features' => [
                            [
                                'title' => $isId ? 'Picu workflow agentic AI' : 'Trigger agentic AI workflows',
                                'desc' => $isId ? 'Menganalisis, bertindak, dan memberi hasil di tools yang sudah Anda pakai, termasuk email, Slack, dashboard, dan lainnya.' : 'Analyze, act, and deliver results across your existing tools, including email, Slack, dashboards, and more.',
                            ],
                            [
                                'title' => $isId ? 'Runner & agent yang terkontrol' : 'Controlled runners & agents',
                                'desc' => $isId ? 'Gunakan hanya yang sudah diizinkan admin, seperti runner yang disetujui dan agent yang diizinkan.' : 'Use only what admins have authorized, such as approved runners and permitted agents.',
                            ],
                            [
                                'title' => $isId ? 'Akses berbasis hierarki peran' : 'Access by role & hierarchy',
                                'desc' => $isId ? 'Beri akses yang tepat ke orang yang tepat berdasarkan peran, hierarki, dan departemen.' : 'Give the right access to the right people based on role, hierarchy, and department.',
                            ],
                            [
                                'title' => $isId ? 'Kontrol biaya & manajemen budget' : 'Cost control & budget management',
                                'desc' => $isId ? 'Pantau pemakaian dan pengeluaran secara real time dengan budget per pengguna, batas pemakaian, dan peringatan saat melebihi batas.' : 'Track usage and spending in real time with budgets per user, limits, and overage alerts.',
                            ],
                        ],
                    ],

                    'workflow' => [
                        'name' => 'TaskO',
                        'tag' => $isId ? 'Bagian dari AI Task Force' : 'Part of AI Task Force',
                        'badge' => $isId ? 'SEGERA HADIR' : 'COMING SOON',
                        'headline' => $isId
                            ? 'TaskO (Task Automation) memungkinkan bisnis merancang dan mengotomasi proses secara menyeluruh dengan menghubungkan tugas, agent AI, sistem bisnis, dan data, tanpa coding yang rumit.'
                            : 'TaskO (Task Automation) lets businesses design and automate entire processes by connecting tasks, AI agents, business systems, and data, without complex coding.',
                        'features' => [
                            [
                                'title' => $isId ? 'Builder visual drag & drop' : 'Visual drag & drop builder',
                                'desc' => $isId ? 'Rancang workflow secara visual dengan blok dan konektor siap pakai.' : 'Design workflows visually using prebuilt blocks and connectors.',
                            ],
                            [
                                'title' => $isId ? 'Builder agent tanpa coding' : 'No code agent builder',
                                'desc' => $isId ? 'Konfigurasikan agent AI sesuai peran, tentukan perilakunya, pasang guardrails, dan uji di skenario nyata.' : 'Configure AI agents for specific roles, define behavior, set guardrails, and test in real scenarios.',
                            ],
                            [
                                'title' => $isId ? 'Hubungkan sistem & data Anda' : 'Connect your systems & data',
                                'desc' => $isId ? 'Integrasikan dengan database, API, aplikasi SaaS, dan sistem enterprise.' : 'Integrate with databases, APIs, SaaS apps, and enterprise systems.',
                            ],
                            [
                                'title' => $isId ? 'Guardrails & persetujuan' : 'Guardrails & approvals',
                                'desc' => $isId ? 'Tambahkan validasi, persetujuan manusia, dan kebijakan agar eksekusi aman dan sesuai aturan.' : 'Add validations, human approvals, and policies to ensure safe and compliant execution.',
                            ],
                        ],
                    ],
                ],
            ],

            'governance' => [
                'title' => $isId ? 'Tata kelola AI untuk organisasi Anda' : 'Govern AI across your organization',
                'cards' => [
                    [
                        'id' => 'guardrails',
                        'title' => 'AI Guardrails',
                        'desc' => $isId
                            ? 'Atur apa yang boleh diakses, diproses, diucapkan, dan dilakukan AI berdasarkan aturan dan batasan yang jelas, termasuk menyamarkan data pribadi yang sensitif (PII).'
                            : 'Control what AI can access, process, say, and do based on defined rules and boundaries, including masking sensitive personally identifiable information (PII).',
                    ],
                    [
                        'id' => 'rbac',
                        'title' => $isId ? 'Akses per peran' : 'Role-Based Access',
                        'desc' => $isId
                            ? 'Atur akses ke sumber daya dan kemampuan AI berdasarkan peran, hierarki, dan izin pengguna.'
                            : 'Control access to AI resources and capabilities by user role, hierarchy, and permissions.',
                    ],
                    [
                        'id' => 'analytics',
                        'title' => $isId ? 'Analitik Pemakaian' : 'Usage Analytics',
                        'desc' => $isId
                            ? 'Pantau pemakaian dan aktivitas AI untuk memahami tingkat adopsi, pola pemakaian, dan konsumsi sumber daya.'
                            : 'Track AI usage and activity to understand adoption, usage patterns, and resource consumption.',
                    ],
                    [
                        'id' => 'learning',
                        'title' => 'AI Learning',
                        'desc' => $isId
                            ? 'Gunakan feedback dan koreksi untuk terus meningkatkan jawaban dan performa AI.'
                            : 'Use feedback and corrections to continuously improve AI responses and performance.',
                    ],
                    [
                        'id' => 'audit',
                        'title' => 'Audit Trail Logs',
                        'desc' => $isId
                            ? 'Catat aktivitas, akses, aksi, dan hasil pengguna untuk ketertelusuran dan tata kelola.'
                            : 'Record user activities, access, actions, and outcomes for traceability and governance.',
                    ],
                    [
                        'id' => 'change_mgmt',
                        'title' => $isId ? 'Manajemen Perubahan Pengetahuan' : 'Knowledge Change Management',
                        'desc' => $isId
                            ? 'Wajibkan setiap perubahan pada sumber daya AI melewati peninjauan dan persetujuan sebelum diterapkan.'
                            : 'Require changes to AI resources to go through review and approval before they are applied.',
                    ],
                ],
            ],

            'why' => [
                'title' => [
                    'prefix' => $isId ? 'Kendala adopsi AI,' : 'What held your AI back,',
                    'main' => $isId ? 'kini terselesaikan' : 'now solved',
                ],
                'cards' => [
                    [
                        'id' => 'multi_model',
                        'title' => 'Multi-Model AI',
                        'desc' => $isId
                            ? 'Akses berbagai model AI dan pilih model yang tepat untuk tiap kebutuhan bisnis, semuanya dalam satu platform.'
                            : 'Access multiple AI models and choose the right model for each business need, all in one platform.',
                        'badge' => $isId ? 'Mengatasi: tools AI yang tersebar' : 'Solves: scattered AI tools',
                    ],
                    [
                        'id' => 'controlled',
                        'title' => $isId ? 'AI yang terkontrol' : 'Controlled AI',
                        'desc' => $isId
                            ? 'Atur cara AI bekerja di berbagai proses bisnis dengan izin, workflow, dan aturan eksekusi yang jelas.'
                            : 'Control how AI operates across multiple business processes with defined permissions, workflows, and execution rules.',
                        'badge' => $isId ? 'Mengatasi: kontrol yang terbatas' : 'Solves: limited control',
                    ],
                    [
                        'id' => 'secure',
                        'title' => $isId ? 'AI yang aman' : 'Secure AI',
                        'desc' => $isId
                            ? 'Jaga pemrosesan dan deployment AI tetap aman dengan perlindungan data seperti penyamaran PII, sementara data bisnis yang sensitif tetap dalam kendali Anda.'
                            : 'Keep AI processing and deployment secure with data protection measures such as PII masking, while keeping sensitive business data under your control.',
                        'badge' => $isId ? 'Mengatasi: kontrol yang terbatas' : 'Solves: limited control',
                    ],
                    [
                        'id' => 'budget',
                        'title' => $isId ? 'Manajemen Budget' : 'Budget Management',
                        'desc' => $isId
                            ? 'Pantau dan kendalikan pemakaian serta pengeluaran AI dengan budget per pengguna, batas pemakaian, dan peringatan saat melebihi batas.'
                            : 'Monitor and control AI usage and spending with budgets per user, usage limits, and overage alerts.',
                        'badge' => $isId ? 'Mengatasi: tagihan yang sulit dikelola' : 'Solves: hard-to-manage billing',
                    ],
                ],
            ],

            'cta' => [
                'title' => $isId
                    ? 'Satu platform untuk semua kebutuhan AI organisasi Anda'
                    : 'Bring all your AI into one platform',
                'primary_cta' => [
                    'text' => $isId ? 'Jadwalkan demo' : 'Book a demo',
                    'url' => '/book-demo',
                ],
                'secondary_cta' => [
                    'text' => $isId ? 'Lihat harga' : 'See pricing',
                    'url' => '/pricing?tab=ai',
                ],
            ],
        ];
    }
}
