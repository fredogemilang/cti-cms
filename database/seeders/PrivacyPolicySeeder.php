<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\User;
use App\Services\CacheManager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Plugins\CsiMenus\Models\NavMenu;
use Plugins\CsiMenus\Models\NavMenuItem;

class PrivacyPolicySeeder extends Seeder
{
    public function run(): void
    {
        $authorId = auth()->id() ?? User::query()->value('id');

        $page = Page::withTrashed()->where('slug', 'privacy-policy')->first();
        if (! $page) {
            $page = new Page;
            $page->slug = 'privacy-policy';
            $page->author_id = $authorId;
        } elseif ($page->trashed()) {
            $page->restore();
        }

        $page->title = 'Privacy Policy';
        $page->template = 'privacy';
        $page->status = 'published';
        $page->published_at ??= now();
        $page->translations = [
            'id' => [
                'title' => 'Kebijakan Privasi',
                'slug' => 'kebijakan-privasi',
            ],
        ];
        $page->seo = [
            'title' => 'Privacy Policy | Converion Solution Informatica',
            'description' => 'Learn how PT Converion Solution Informatica collects, protects, stores, and processes personal data across our enterprise website and services.',
            '_translations' => [
                'id' => [
                    'title' => 'Kebijakan Privasi | Converion Solution Informatica',
                    'description' => 'Pelajari bagaimana PT Converion Solution Informatica mengumpulkan, melindungi, menyimpan, dan memproses data pribadi di seluruh layanan digital kami.',
                ],
            ],
        ];
        $page->save();

        [$clausesEn, $clausesId] = $this->bilingualClauses();

        $blocks = [
            [
                'name' => 'hero_title',
                'type' => 'text',
                'label' => 'Hero Title',
                'value' => 'Privacy Policy',
                'value_id' => 'Kebijakan Privasi',
            ],
            [
                'name' => 'hero_subtitle',
                'type' => 'textarea',
                'label' => 'Hero Subtitle',
                'value' => "At Converion Solution Informatica (CSI), ensuring the privacy, confidentiality, and security of your personal data is fundamental to our enterprise mission.\nLearn how we collect, safeguard, and process your information across all digital touchpoints.",
                'value_id' => "Di Converion Solution Informatica (CSI), menjaga privasi, kerahasiaan, dan keamanan data pribadi Anda merupakan fondasi utama misi perusahaan kami.\nPelajari bagaimana kami mengumpulkan, melindungi, dan memproses informasi Anda di seluruh kanal digital.",
            ],
            [
                'name' => 'intro_heading',
                'type' => 'text',
                'label' => 'Introduction Card Heading',
                'value' => 'Introduction & Scope',
                'value_id' => 'Pendahuluan & Ruang Lingkup',
            ],
            [
                'name' => 'intro_body',
                'type' => 'textarea',
                'label' => 'Introduction Card Body',
                'value' => "At Converion Solution Informatica (CSI), ensuring the privacy and security of your personal data is of utmost importance to us. As you navigate through our website, www.converion.com, collectively referred to as this \"Website\", we strive to create a safe and trustworthy environment for all enterprise partners, stakeholders, and visitors.\n\nThis Privacy Policy establishes the legally binding terms governing your use of our Website between you (\"you\" or \"your\") and PT Converion Solution Informatica (Centennial Tower 12th Floor, Jl. Jend. Gatot Subroto Kav. 24-25, Jakarta). By accessing or utilizing our digital infrastructure, you acknowledge that you have reviewed, understood, and consent to be bound by the terms outlined herein.",
                'value_id' => "Di Converion Solution Informatica (CSI), privasi dan keamanan data pribadi Anda penting bagi kami. Saat Anda menjelajahi situs web kami, www.converion.com, kami berupaya menciptakan ruang yang aman dan terpercaya untuk semua pengguna, mitra, dan pemangku kepentingan.\n\nKebijakan Privasi ini menetapkan ketentuan yang mengatur penggunaan situs web kami antara Anda (\"Anda\" atau \"milik Anda\") dan PT Converion Solution Informatica (Centennial Tower Lantai 12, Jl. Jend. Gatot Subroto Kav. 24-25, Jakarta). Dengan mengakses situs web kami, Anda mengakui bahwa Anda telah membaca, memahami, dan setuju untuk terikat dengan Kebijakan Privasi ini.",
            ],
            [
                'name' => 'toc_heading',
                'type' => 'text',
                'label' => 'Policy Contents Sidebar Heading',
                'value' => 'Policy Contents',
                'value_id' => 'Daftar Isi Kebijakan',
            ],
            [
                'name' => 'clauses_list',
                'type' => 'repeater',
                'label' => 'Privacy Policy Clauses',
                'value' => json_encode($clausesEn, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'value_id' => json_encode($clausesId, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ],
        ];

        foreach ($blocks as $index => $item) {
            PageBlock::updateOrCreate(
                [
                    'page_id' => $page->id,
                    'name' => $item['name'],
                    'parent_block_id' => null,
                ],
                [
                    'type' => $item['type'],
                    'label' => $item['label'],
                    'value' => $item['value'],
                    'translations' => $item['value_id'] !== null
                        ? ['id' => ['value' => $item['value_id']]]
                        : null,
                    'order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }

        $this->syncFooterMenuLink();

        CacheManager::purgeAll();
        $this->command?->info("Synced Privacy Policy page (#{$page->id}) & footer link.");
    }

    /**
     * Full 11-clause Privacy Policy content (EN & ID) aligned with https://www.converion.com/privacy-policy/.
     *
     * @return array{0: array<int, array<string, mixed>>, 1: array<int, array<string, mixed>>}
     */
    protected function bilingualClauses(): array
    {
        $clausesEn = [
            [
                'title' => 'Personal Data We Collect',
                'intro' => 'When utilizing or engaging with our Website or enterprise channels, we may gather or receive various types of data, collectively referred to as "Personal Data", including but not limited to:',
                'cards' => json_encode([
                    [
                        'icon' => 'card-identifiable.svg',
                        'title' => 'Identifiable Data',
                        'description' => 'Your name, business email address, WhatsApp/contact details, company name, or other information provided via Website forms or communication channels (email, phone, mail).',
                    ],
                    [
                        'icon' => 'card-technical.svg',
                        'title' => 'Technical Information',
                        'description' => 'Browser type and version, operating system, device type, IP address, and referring URLs automatically sent by your browser or device.',
                    ],
                    [
                        'icon' => 'card-usage.svg',
                        'title' => 'Usage Metrics',
                        'description' => 'Pages visited on our Website, click activity, searches conducted, asset interactions, and email engagement metrics (opens, clicks, and deliveries).',
                    ],
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'items' => '',
                'outro' => 'We are committed in handling such personal data in accordance with applicable laws and regulations.',
            ],
            [
                'title' => 'Methods of Collection',
                'intro' => 'Depending on the type of Personal Data, we collect or receive it through various channels, including but not limited to the following conditions:',
                'cards' => '',
                'items' => implode("\n", [
                    'a. Voluntary Submission: When you voluntarily share your Personal Data with us—for instance, when you subscribe to our newsletter or fill out our online form to request contact.',
                    'b. Cookies & Analytics Technologies: By using cookies and similar technologies that help us analyze how our Website is utilized, tailor pertinent content, and deliver relevant advertisements on our own or third-party sites.',
                    'c. Third-Party Business Sources: Information acquired through business support tools and services we utilize (such as analytics platforms) as well as public sources like social media sites, which we may merge with existing records to maintain accuracy.',
                ]),
                'outro' => '',
            ],
            [
                'title' => 'Purposes of Processing',
                'intro' => 'We utilize Information for the following purposes:',
                'cards' => '',
                'items' => implode("\n", [
                    'a. Processing your inquiries and responding to your requests, such as when you reach out to learn more about our products or services.',
                    'b. Sending you information related to our services and products that we believe may be of interest to you, such as invitations to upcoming events, follow-up via WhatsApp and/or call, newsletters, or product updates, based on explicit consent or legitimate marketing interest (you may opt out at any time).',
                    'c. Understanding how you interact with our Website and tailoring it to align with your interests, past actions, and preferences to enhance usability and diagnose technical issues.',
                    'd. Preventing fraud or harm to us or any third party, and ensuring the security of our network and services, which is in our legitimate interest.',
                    'e. Complying with our legal obligations and exercising and enforcing our legal rights as necessary for Converion Solution Informatica.',
                    'f. Utilizing certain third-party marketing and advertising networks to assist in marketing our products on our Website and third-party websites.',
                ]),
                'outro' => '',
            ],
            [
                'title' => 'Disclosure & Sharing',
                'intro' => 'To facilitate our business operations and the functioning of our Website, we may disclose your Personal Data to various third parties, including:',
                'cards' => '',
                'items' => implode("\n", [
                    'a. Our global branches and subsidiary companies.',
                    'b. Third-party service providers aiding in the operation of our Website (such as hosting companies, recruitment platforms and agencies, payment processors, business management, and email distribution providers), authorized strictly to perform services on our behalf.',
                    'c. Regulatory and law enforcement bodies when compelled by court orders, search warrants, regulatory orders, subpoenas, or other lawful requests for national security or law enforcement.',
                    'd. Legal authorities, consultants, advisors, or service providers required to investigate, respond to, or prevent fraud, or to safeguard network security and public safety.',
                    'e. Merging or acquiring entities and transaction advisors in the event of a corporate merger and/or acquisition involving Converion Solution Informatica.',
                    'f. Principals, resellers, partners, sponsors, or service providers acting on our behalf in connection with offering Converion Solution Informatica products or services.',
                    'g. Third-party marketing and advertising networks assisting in product promotion on our Website and third-party platforms (such as Google for remarketing ads).',
                ]),
                'outro' => 'Converion Solution Informatica may also disclose general aggregate and anonymized statistical information pertaining to Website usage.',
            ],
            [
                'title' => 'Cross-Border Data Transfers',
                'intro' => 'Cross-border operational infrastructure may involve international data flows subject to strict legal compliance:',
                'cards' => '',
                'items' => implode("\n", [
                    'a. We may need to transfer Personal Data to countries where we and/or our service providers operate, which may have different data protection laws than your country of origin. By using our Website, you consent to such transfers under contractual privacy safeguards.',
                    'b. Regarding the collection, use, and retention of Personal Data transferred from Indonesia, Converion Solution Informatica remains fully compliant with all applicable Indonesian data protection laws and regulations.',
                ]),
                'outro' => '',
            ],
            [
                'title' => 'Security & Data Protection',
                'intro' => 'We aim to uphold top-tier security standards throughout our business operations. We have adopted suitable technical and organizational safeguards aligned with industry best practices to prevent unauthorized access or unlawful handling of Personal Data and to mitigate accidental loss, destruction, or damage.',
                'cards' => '',
                'items' => implode("\n", [
                    'Asset & Access Governance: Strict asset management, role-based access control, physical data center security, and personnel security.',
                    'Infrastructure & Network Defense: Product security, cloud and network infrastructure hardening, and third-party vendor security assessments.',
                    'Proactive Security Operations: Continuous vulnerability management, real-time security monitoring, and documented incident response protocols.',
                ]),
                'outro' => '',
            ],
            [
                'title' => 'Storage & Retention',
                'intro' => 'We may store your Personal Data on both our own servers and those managed by third-party data hosting providers located globally, as explained in Section 5 (Cross-Border Data Transfers).',
                'cards' => '',
                'items' => '',
                'outro' => "We retain your Personal Data only for as long as necessary to fulfill the intended purpose of collection, pursue legitimate business interests, resolve legal claims, and meet statutory obligations.\n\nWhere Personal Data is used for direct marketing, we retain it until you choose to opt out of receiving marketing materials; limited suppression records may be kept solely to honor your opt-out preference.",
            ],
            [
                'title' => 'Policy Modifications',
                'intro' => 'Converion Solution Informatica reserves the right to amend this Privacy Policy at any time. In the event of significant updates, we will post a prominent notice on this page and/or adjacent to links directing to this page.',
                'cards' => '',
                'items' => '',
                'outro' => "Updates become effective immediately for newly collected Personal Data from the date of revision, and within thirty (30) days for Personal Data collected prior to the update.\n\nIf you do not agree with the revised terms, please contact our Legal Department using the details in Clause 11. We encourage you to review this page periodically.",
            ],
            [
                'title' => 'Your Legal Rights & Choices',
                'intro' => 'We offer transparent control over how your Personal Data is used for marketing communications and analytics cookies. You can unsubscribe from newsletters at any time using email opt-out links or by reaching out via our Contact page.',
                'cards' => '',
                'items' => implode("\n", [
                    'i. Right to Withdraw Consent: Withdraw previously provided consent at any time.',
                    'ii. Right of Access: Request access to specific personal data about you that we process.',
                    'iii. Right to Rectification: Correct or update inaccurate or incomplete Personal Data.',
                    'iv. Right to Erasure: Request deletion of certain Personal Data where legally applicable.',
                    'v. Right to Restriction: Temporarily suspend our processing of your Personal Data.',
                    'vi. Right to Data Portability: Receive Personal Data in a common machine-readable format.',
                    'vii. Right to Object: Object to direct marketing or processing based on legitimate interests.',
                    'viii. Right to Complain: File a formal complaint with the relevant data protection authority.',
                ]),
                'outro' => 'We will address your requests promptly subject to applicable statutory limitations. To exercise these rights, please contact Converion Solution Informatica at legal@computradetech.com.',
            ],
            [
                'title' => 'Social Media & Third-Party Services',
                'intro' => 'Our Website may include community features, share buttons, or links to external platforms such as Facebook, X, YouTube, LinkedIn, and Instagram.',
                'cards' => '',
                'items' => '',
                'outro' => 'When utilizing these features, third-party providers may collect technical information (such as your IP address or visited pages) and place cookies so the features function properly. All data processed by external platforms is governed by their respective privacy policies, which we encourage you to review carefully.',
            ],
            [
                'title' => 'Contact & Grievance Officer',
                'intro' => 'If you have any questions, requests, or concerns regarding this Website Privacy Policy, the Personal Data we collect, Converion Solution Informatica’s practices, or your interactions with our Website, please contact us through our official Legal & Data Privacy channels:',
                'cards' => '',
                'items' => implode("\n", [
                    'Email Legal & Privacy Desk: legal@computradetech.com',
                    'Postal Address: PT Converion Solution Informatica, Centennial Tower 12th Floor, Jl. Jendral Gatot Subroto Kav. 24-25, Jakarta 12930, Indonesia',
                    'Telephone: +6221-80622278',
                ]),
                'outro' => '',
            ],
        ];

        $clausesId = [
            [
                'title' => 'Data Pribadi yang Kami Kumpulkan',
                'intro' => 'Saat Anda menggunakan atau mengakses situs web kami, kami dapat mengumpulkan atau menerima beberapa jenis data yang secara kolektif disebut sebagai "Data Pribadi", termasuk namun tidak terbatas pada:',
                'cards' => json_encode([
                    [
                        'icon' => 'card-identifiable.svg',
                        'title' => 'Data Identitas',
                        'description' => 'Nama, alamat email bisnis, detail kontak WhatsApp/telepon, nama perusahaan, atau informasi lain yang diberikan melalui formulir Website maupun saluran komunikasi resmi.',
                    ],
                    [
                        'icon' => 'card-technical.svg',
                        'title' => 'Informasi Teknis',
                        'description' => 'Jenis dan versi browser, sistem operasi, tipe perangkat, alamat IP, serta URL rujukan yang dikirim secara otomatis oleh perangkat Anda.',
                    ],
                    [
                        'icon' => 'card-usage.svg',
                        'title' => 'Informasi Penggunaan',
                        'description' => 'Halaman yang dikunjungi, aktivitas klik, pencarian pada situs, serta interaksi dengan email kami seperti status buka dan klik tautan.',
                    ],
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'items' => '',
                'outro' => 'Kami berkomitmen untuk menangani data pribadi tersebut sesuai dengan hukum dan peraturan yang berlaku.',
            ],
            [
                'title' => 'Metode Pengumpulan Data',
                'intro' => 'Kami mengumpulkan atau menerima Data Pribadi melalui berbagai cara, termasuk namun tidak terbatas pada kondisi berikut:',
                'cards' => '',
                'items' => implode("\n", [
                    'a. Penyerahan Secara Sukarela: Saat Anda membagikan data pribadi kepada kami, misalnya sewaktu berlangganan newsletter atau mengisi formulir online untuk meminta kontak.',
                    'b. Cookie & Teknologi Serupa: Penggunaan cookie membantu kami menganalisis pemanfaatan situs web, menyesuaikan konten yang relevan, serta menayangkan iklan yang tepat sasaran.',
                    'c. Sumber Pihak Ketiga: Informasi yang diperoleh melalui instrumen pendukung bisnis (seperti layanan analisis) maupun sumber publik seperti media sosial, yang dapat kami gabungkan untuk menjaga keakuratan data.',
                ]),
                'outro' => '',
            ],
            [
                'title' => 'Tujuan Pemrosesan Data',
                'intro' => 'Penggunaan Data Pribadi Anda kami lakukan untuk tujuan-tujuan berikut:',
                'cards' => '',
                'items' => implode("\n", [
                    'a. Memproses pertanyaan dan menanggapi permintaan Anda, seperti saat Anda menghubungi kami untuk mengetahui lebih lanjut tentang produk atau layanan kami.',
                    'b. Mengirimkan informasi terkait layanan dan produk yang relevan—seperti undangan acara, tindak lanjut WhatsApp blast dan/atau telepon, newsletter, serta pembaruan produk—berdasarkan persetujuan Anda atau kepentingan sah pemasaran (Anda dapat berhenti berlangganan kapan saja).',
                    'c. Memahami cara Anda berinteraksi dengan situs web kami guna menyempurnakan konten, mendiagnosis kendala teknis, dan meningkatkan pengalaman navigasi.',
                    'd. Mencegah penipuan atau tindakan yang merugikan kami maupun pihak ketiga, serta menjaga keamanan jaringan dan layanan kami sesuai kepentingan yang sah.',
                    'e. Mematuhi kewajiban hukum serta menjalankan dan menegakkan hak-hak hukum Converion Solution Informatica.',
                    'f. Memanfaatkan jaringan pemasaran dan periklanan pihak ketiga untuk mendukung promosi produk pada Website kami maupun situs web mitra.',
                ]),
                'outro' => '',
            ],
            [
                'title' => 'Pengungkapan & Pembagian Data',
                'intro' => 'Untuk mendukung operasional bisnis dan menjalankan fungsi situs web kami, kami dapat membagikan Data Pribadi kepada berbagai pihak ketiga, termasuk:',
                'cards' => '',
                'items' => implode("\n", [
                    'a. Anak perusahaan dan cabang global kami.',
                    'b. Penyedia layanan pihak ketiga yang membantu pengoperasian situs web (seperti penyedia hosting, platform rekrutmen, pemroses pembayaran, manajemen bisnis, dan distribusi email), dengan otoritas terbatas hanya untuk menjalankan layanan bagi kami.',
                    'c. Otoritas berwenang apabila diwajibkan oleh hukum, perintah pengadilan, surat perintah penggeledahan, panggilan pengadilan, atau permintaan sah terkait penegakan hukum maupun keamanan nasional.',
                    'd. Otoritas hukum, konsultan, penasihat, atau penyedia keamanan untuk menyelidiki, menanggapi, atau mencegah penipuan serta menjaga keamanan jaringan dan keselamatan publik.',
                    'e. Entitas pengakuisisi atau penggabung beserta penasihat transaksi apabila terjadi aksi korporasi merger dan/atau akuisisi yang melibatkan Converion Solution Informatica.',
                    'f. Prinsipal, reseller, mitra, sponsor, atau penyedia layanan yang bertindak atas nama kami dalam penawaran produk dan solusi Converion Solution Informatica.',
                    'g. Jaringan pemasaran dan periklanan pihak ketiga (seperti Google) untuk kampanye pemasaran di situs web kami maupun situs pihak ketiga.',
                ]),
                'outro' => 'Converion Solution Informatica juga dapat mengungkapkan informasi agregat umum dan anonim (seperti data statistik penggunaan situs web).',
            ],
            [
                'title' => 'Transfer Data Lintas Negara',
                'intro' => 'Pengelolaan infrastruktur global dapat melibatkan pemindahan data antar negara dengan perlindungan hukum yang ketat:',
                'cards' => '',
                'items' => implode("\n", [
                    'a. Kami mungkin perlu mentransfer Data Pribadi ke negara tempat kami dan/atau penyedia layanan kami beroperasi yang memiliki regulasi perlindungan data berbeda. Dengan menggunakan situs web ini, Anda menyetujui transfer tersebut di bawah perjanjian perlindungan privasi yang setara.',
                    'b. Untuk pengumpulan, penggunaan, dan penyimpanan Data Pribadi yang ditransfer dari Indonesia, Converion Solution Informatica senantiasa mematuhi seluruh peraturan perundang-undangan perlindungan data yang berlaku di Indonesia.',
                ]),
                'outro' => '',
            ],
            [
                'title' => 'Keamanan & Perlindungan Data',
                'intro' => 'Kami berkomitmen menegakkan standar keamanan terbaik di seluruh operasi bisnis dengan menerapkan langkah-langkah teknis dan organisasi yang sesuai praktik industri untuk mencegah akses tidak sah, penanganan ilegal, kehilangan, maupun kerusakan data.',
                'cards' => '',
                'items' => implode("\n", [
                    'Tata Kelola Aset & Akses: Kebijakan manajemen aset, kontrol akses ketat, keamanan fisik fasilitas, serta pengamanan personel.',
                    'Ketahanan Infrastruktur & Pihak Ketiga: Keamanan produk, infrastruktur cloud dan jaringan, serta standar keamanan vendor pihak ketiga.',
                    'Pemantauan & Respons Proaktif: Manajemen kerentanan berkala, pemantauan keamanan berkelanjutan, dan protokol penanganan insiden.',
                ]),
                'outro' => '',
            ],
            [
                'title' => 'Penyimpanan & Retensi Data',
                'intro' => 'Kami dapat menyimpan Data Pribadi Anda baik di server internal kami maupun pada server penyedia hosting pihak ketiga di berbagai lokasi global sebagaimana dijelaskan pada Klausul 5 (Transfer Data Lintas Negara).',
                'cards' => '',
                'items' => '',
                'outro' => "Kami hanya menyimpan Data Pribadi Anda selama diperlukan untuk memenuhi tujuan pengumpulan awal, menjalankan kepentingan bisnis yang sah, menyelesaikan klaim hukum, dan mematuhi kewajiban perundang-undangan.\n\nJika digunakan untuk pemasaran langsung, kami menyimpan Data Pribadi Anda sampai Anda memilih berhenti menerima materi pemasaran; data terbatas dapat tetap disimpan hanya sebagai catatan preferensi berhenti berlangganan Anda.",
            ],
            [
                'title' => 'Perubahan Kebijakan Privasi',
                'intro' => 'Converion Solution Informatica berhak memperbarui Kebijakan Privasi ini sewaktu-waktu. Jika terjadi perubahan material, kami akan menyampaikan pemberitahuan di halaman ini dan/atau di samping tautan menuju halaman ini.',
                'cards' => '',
                'items' => '',
                'outro' => "Pembaruan berlaku segera untuk Data Pribadi baru sejak tanggal perubahan, dan berlaku dalam waktu tiga puluh (30) hari untuk data yang dikumpulkan sebelum pembaruan.\n\nJika Anda tidak menyetujui ketentuan yang diperbarui, silakan hubungi Tim Legal kami sesuai rincian pada Klausul 11. Kami menganjurkan Anda meninjau halaman ini secara berkala.",
            ],
            [
                'title' => 'Hak Hukum & Pilihan Anda',
                'intro' => 'Kami menyediakan kendali penuh bagi Anda atas penggunaan Data Pribadi untuk komunikasi pemasaran serta preferensi cookie. Anda dapat berhenti berlangganan newsletter kapan saja melalui tautan berhenti berlangganan di email atau melalui halaman Hubungi Kami.',
                'cards' => '',
                'items' => implode("\n", [
                    'i. Hak Menarik Persetujuan: Menarik kembali persetujuan pemrosesan data yang telah diberikan sebelumnya.',
                    'ii. Hak Akses Data: Memperoleh informasi mengenai data pribadi spesifik Anda yang kami proses.',
                    'iii. Hak Perbaikan Data: Memperbaiki atau memperbarui data pribadi yang tidak akurat.',
                    'iv. Hak Penghapusan: Meminta penghapusan data pribadi tertentu sesuai ketentuan hukum yang berlaku.',
                    'v. Hak Penangguhan Sementara: Meminta penangguhan sementara pemrosesan data pribadi oleh kami.',
                    'vi. Hak Portabilitas Data: Menerima data dalam format umum yang dapat dibaca mesin.',
                    'vii. Hak Keberatan: Menolak pemrosesan data untuk pemasaran langsung maupun atas dasar kepentingan sah.',
                    'viii. Hak Mengajukan Pengaduan: Mengajukan keluhan resmi kepada otoritas perlindungan data berwenang.',
                ]),
                'outro' => 'Kami akan menanggapi permohonan Anda sesegera mungkin sesuai batasan hukum yang berlaku. Untuk menggunakan hak-hak tersebut, silakan hubungi Converion Solution Informatica di legal@computradetech.com.',
            ],
            [
                'title' => 'Media Sosial & Layanan Pihak Ketiga',
                'intro' => 'Situs web kami dapat memuat fitur komentar blog, tombol berbagi, atau tautan menuju platform pihak ketiga seperti Facebook, X, YouTube, LinkedIn, dan Instagram.',
                'cards' => '',
                'items' => '',
                'outro' => 'Saat Anda berinteraksi dengan fitur tersebut, penyedia pihak ketiga dapat mengumpulkan data teknis seperti alamat IP atau halaman yang dikunjungi serta memasang cookie agar fitur berfungsi optimal. Seluruh data pada layanan pihak ketiga tunduk pada kebijakan privasi masing-masing penyedia.',
            ],
            [
                'title' => 'Kontak & Pejabat Pengaduan Privasi',
                'intro' => 'Jika Anda memiliki pertanyaan, masukan, atau permintaan terkait Kebijakan Privasi ini, Data Pribadi yang kami kelola, atau interaksi Anda dengan Website kami, silakan hubungi kami melalui kanal resmi berikut:',
                'cards' => '',
                'items' => implode("\n", [
                    'Email Tim Legal & Privasi: legal@computradetech.com',
                    'Alamat Surat: PT Converion Solution Informatica, Centennial Tower Lantai 12, Jl. Jendral Gatot Subroto Kav. 24-25, Jakarta 12930, Indonesia',
                    'Telepon: +6221-80622278',
                ]),
                'outro' => '',
            ],
        ];

        return [$clausesEn, $clausesId];
    }

    protected function syncFooterMenuLink(): void
    {
        if (! class_exists(NavMenu::class) || ! class_exists(NavMenuItem::class) || ! Schema::hasTable('csi_nav_menus')) {
            return;
        }

        $otherMenus = NavMenu::where('location', 'footer_other')->pluck('id');
        if ($otherMenus->isEmpty()) {
            return;
        }

        NavMenuItem::whereIn('menu_id', $otherMenus)
            ->where(function ($q) {
                $q->where('title', 'like', '%Privacy%')
                    ->orWhereRaw('LOWER(url) LIKE ?', ['%privacy%']);
            })
            ->get()
            ->each(function (NavMenuItem $item) {
                $trans = is_array($item->translations) ? $item->translations : [];
                $trans['id']['title'] = $trans['id']['title'] ?? 'Kebijakan Privasi';
                $trans['id']['url'] = '/kebijakan-privasi';

                $item->update([
                    'url' => '/privacy-policy',
                    'translations' => $trans,
                ]);
            });
    }
}
