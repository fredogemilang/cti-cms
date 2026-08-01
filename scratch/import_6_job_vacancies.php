<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\TaxonomyTerm;
use App\Models\User;

$admin = User::first();
$authorId = $admin ? $admin->id : 1;

$cpt = CustomPostType::where('slug', 'jobs')->first();
if (!$cpt) {
    echo "CPT jobs not found!\n";
    exit(1);
}

$termSales = TaxonomyTerm::where('slug', 'sales-bd')->first();
$termTech  = TaxonomyTerm::where('slug', 'technical-engineering')->first();
$termOps   = TaxonomyTerm::where('slug', 'operations-hr')->first();

$jobsData = [
    [
        'slug' => 'account-executive',
        'term_id' => $termSales ? $termSales->id : null,
        'title' => [
            'en' => 'Account Executive',
            'id' => 'Account Executive',
        ],
        'location' => [
            'en' => 'Jakarta, Indonesia',
            'id' => 'Jakarta, Indonesia',
        ],
        'employment_type' => 'Full-time',
        'short_description' => [
            'en' => 'Achieve and exceed sales target and deliver service excellence, Build and develop strong relationship with existing and potential end-users, Identify and capture new customer segments to expand market coverage.',
            'id' => 'Mencapai dan melampaui target penjualan serta memberikan kepuasan layanan terbaik, membangun hubungan kuat dengan pengguna akhir, dan mengidentifikasi segmen pelanggan baru.',
        ],
        'responsibilities' => [
            'en' => [
                'Achieve and exceed sales target and deliver service excellence',
                'Build and develop strong relationship with existing and potential end-users',
                'Identify and capture new customer segments to expand market coverage',
                'Collaborate with technical team to present tailored IT solution packages to enterprise clients',
                'Manage pipeline, lead generation, and contract negotiations from start to closure',
            ],
            'id' => [
                'Mencapai dan melampaui target penjualan serta memberikan kepuasan layanan yang unggul',
                'Membangun dan mengidentifikasi hubungan yang kuat dengan pengguna akhir yang ada maupun calon pelanggan',
                'Mengidentifikasi dan menangkap segmen pelanggan baru untuk memperluas cakupan pasar',
                'Berkolaborasi dengan tim teknis untuk menyajikan paket solusi IT yang disesuaikan untuk klien korporat',
                'Mengelola saluran penjualan (pipeline), pencarian prospek, dan negosiasi kontrak dari awal hingga penutupan',
            ],
        ],
        'requirements' => [
            'en' => [
                'Minimum Bachelor Degree in any major',
                'Have at least 1 year experience as a Sales Specialist or Account Manager in IT or relevant industry',
                'Have passion in digital business development and fostering client relationship',
                'Strong communication, presentation, and negotiation skills',
                'Fluency in English both written and spoken is a plus',
            ],
            'id' => [
                'Minimal Gelar Sarjana (S1) di semua jurusan',
                'Memiliki pengalaman minimal 1 tahun sebagai Sales Specialist atau Account Manager di industri IT atau relevan',
                'Memiliki passion dalam pengembangan bisnis digital dan membina hubungan baik dengan klien',
                'Keterampilan komunikasi, presentasi, dan negosiasi yang kuat',
                'Fasih berbahasa Inggris baik lisan maupun tulisan menjadi nilai tambah',
            ],
        ],
        'is_featured' => true,
        'order' => 1,
    ],
    [
        'slug' => 'manager-solution-architect',
        'term_id' => $termTech ? $termTech->id : null,
        'title' => [
            'en' => 'Manager – Solution Architect',
            'id' => 'Manager – Solution Architect',
        ],
        'location' => [
            'en' => 'Jakarta, Indonesia',
            'id' => 'Jakarta, Indonesia',
        ],
        'employment_type' => 'Full-time',
        'short_description' => [
            'en' => 'Lead the technical solution architecture team, design end-to-end cloud and enterprise IT infrastructure solutions, and act as a senior technical advisor for enterprise transformation projects.',
            'id' => 'Memimpin tim arsitek solusi teknis, merancang solusi infrastruktur IT & cloud secara end-to-end, dan menjadi penasihat teknis senior untuk proyek transformasi korporasi.',
        ],
        'responsibilities' => [
            'en' => [
                'Lead, mentor, and oversee a team of Solution Architects and Technical Specialists',
                'Design enterprise-grade multi-cloud and hybrid IT architectures tailored to customer requirements',
                'Conduct technical presentations, proof-of-concept (PoC) demos, and technical proposals for high-value clients',
                'Collaborate closely with principal partners (AWS, F5, Hitachi, Akamai) on solution roadmap alignment',
                'Ensure technical compliance, high-availability architecture standards, and security best practices',
                'Support pre-sales engagements and provide strategic architecture recommendations to executive stakeholders',
            ],
            'id' => [
                'Memimpin, membimbing, dan mengawasi tim Solution Architect dan Spesialis Teknis',
                'Merancang arsitektur IT multi-cloud dan hybrid kelas enterprise sesuai kebutuhan pelanggan',
                'Melakukan presentasi teknis, demonstrasi Proof of Concept (PoC), dan proposal teknis untuk klien bernilai tinggi',
                'Berkolaborasi erat dengan mitra prinsipal (AWS, F5, Hitachi, Akamai) dalam keselarasan peta jalan solusi',
                'Memastikan kepatuhan teknis, standar arsitektur ketersediaan tinggi, dan praktik terbaik keamanan',
                'Mendukung keterlibatan pra-penjualan dan memberikan rekomendasi arsitektur strategis kepada pemangku kepentingan',
            ],
        ],
        'requirements' => [
            'en' => [
                'Bachelor or Master Degree in Computer Science, Information Technology, or Engineering',
                '5+ years of experience in Solution Architecture or Enterprise Systems Design in IT industry',
                'Proven track record leading technical teams and complex IT solution projects',
                'Certifications in AWS Professional, F5, or equivalent enterprise technology stack',
                'Strong analytical, leadership, problem-solving, and executive presentation capabilities',
            ],
            'id' => [
                'Gelar Sarjana atau Magister (S1/S2) dalam Ilmu Komputer, Teknologi Informasi, atau Teknik',
                'Pengalaman 5+ tahun dalam Arsitektur Solusi atau Desain Sistem Korporat di industri IT',
                'Rekam jejak terbukti dalam memimpin tim teknis dan proyek solusi IT yang kompleks',
                'Sertifikasi AWS Professional, F5, atau setara dalam stack teknologi enterprise',
                'Kemampuan analitis, kepemimpinan, pemecahan masalah, dan presentasi eksekutif yang kuat',
            ],
        ],
        'is_featured' => true,
        'order' => 2,
    ],
    [
        'slug' => 'product-specialist-aws',
        'term_id' => $termTech ? $termTech->id : null,
        'title' => [
            'en' => 'Product Specialist – AWS',
            'id' => 'Product Specialist – AWS',
        ],
        'location' => [
            'en' => 'Jakarta, Indonesia',
            'id' => 'Jakarta, Indonesia',
        ],
        'employment_type' => 'Full-time',
        'short_description' => [
            'en' => 'Drive product adoption, technical sales enablement, and go-to-market execution for Amazon Web Services (AWS) cloud portfolio within CDT.',
            'id' => 'Mendorong adopsi produk, enablement penjualan teknis, dan eksekusi go-to-market untuk portofolio cloud Amazon Web Services (AWS) di CDT.',
        ],
        'responsibilities' => [
            'en' => [
                'Act as subject matter expert (SME) for AWS cloud products and solutions portfolio',
                'Drive product enablement sessions, technical training, and workshops for internal sales and partner channels',
                'Develop product positioning, competitor analysis, and go-to-market strategies for AWS services',
                'Assist sales teams during client meetings with technical product expertise and pricing estimations',
            ],
            'id' => [
                'Bertindak sebagai pakar materi subjek (SME) untuk portofolio produk dan solusi AWS cloud',
                'Mendorong sesi enablement produk, pelatihan teknis, dan lokakarya untuk tim penjualan internal dan mitra channel',
                'Mengembangkan positioning produk, analisis pesaing, dan strategi go-to-market untuk layanan AWS',
                'Membantu tim sales selama pertemuan klien dengan keahlian produk teknis dan estimasi harga',
            ],
        ],
        'requirements' => [
            'en' => [
                'Bachelor Degree in Computer Science, Information Systems, or Business Administration',
                '2+ years experience as a Product Specialist, Presales, or Business Development for Cloud/AWS solutions',
                'AWS Associate Certification (Solutions Architect or Cloud Practitioner) is highly required',
                'Excellent communication, strategic thinking, and client engagement skills',
            ],
            'id' => [
                'Gelar Sarjana (S1) dalam Ilmu Komputer, Sistem Informasi, atau Administrasi Bisnis',
                'Pengalaman 2+ tahun sebagai Product Specialist, Presales, atau Business Development untuk solusi Cloud/AWS',
                'Sertifikasi AWS Associate (Solutions Architect atau Cloud Practitioner) sangat diutamakan',
                'Keterampilan komunikasi, pemikiran strategis, dan keterlibatan klien yang sangat baik',
            ],
        ],
        'is_featured' => false,
        'order' => 3,
    ],
    [
        'slug' => 'product-specialist-f5',
        'term_id' => $termTech ? $termTech->id : null,
        'title' => [
            'en' => 'Product Specialist – F5',
            'id' => 'Product Specialist – F5',
        ],
        'location' => [
            'en' => 'Jakarta, Indonesia',
            'id' => 'Jakarta, Indonesia',
        ],
        'employment_type' => 'Full-time',
        'short_description' => [
            'en' => 'Manage product life cycle, technical sales enablement, and solution positioning for F5 Networks security and application delivery portfolio.',
            'id' => 'Mengelola siklus hidup produk, enablement penjualan teknis, dan positioning solusi untuk portofolio F5 Networks (security & application delivery).',
        ],
        'responsibilities' => [
            'en' => [
                'Drive product strategy, sales enablement, and revenue growth for F5 Networks portfolio',
                'Conduct technical presentations and solution sizing for F5 BIG-IP, WAF, NGINX, and Multi-Cloud Security',
                'Build strong relationships with principal F5 vendor team and channel partner network',
                'Collaborate with marketing to organize product campaigns, webinars, and customer seminars',
            ],
            'id' => [
                'Mendorong strategi produk, enablement penjualan, dan pertumbuhan pendapatan untuk portofolio F5 Networks',
                'Melakukan presentasi teknis dan penentuan ukuran solusi untuk F5 BIG-IP, WAF, NGINX, dan Multi-Cloud Security',
                'Membangun hubungan erat dengan tim prinsipal vendor F5 dan jaringan mitra channel',
                'Berkolaborasi dengan pemasaran untuk menyelenggarakan kampanye produk, webinar, dan seminar pelanggan',
            ],
        ],
        'requirements' => [
            'en' => [
                'Bachelor Degree in Telecommunication Engineering, Computer Science, or IT related fields',
                '2+ years experience in F5 solutions, Network Security, or Application Delivery Controllers (ADC)',
                'F5 Certified Administrator or Technical Specialist certification is a strong advantage',
                'Proactive mindset with strong analytical and client-facing communication skills',
            ],
            'id' => [
                'Gelar Sarjana (S1) Teknik Telekomunikasi, Ilmu Komputer, atau bidang IT terkait',
                'Pengalaman 2+ tahun dalam solusi F5, Keamanan Jaringan, atau Application Delivery Controller (ADC)',
                'Sertifikasi F5 Certified Administrator atau Technical Specialist merupakan keunggulan utama',
                'Pola pikir proaktif dengan keterampilan analitis dan komunikasi berhadapan dengan klien yang kuat',
            ],
        ],
        'is_featured' => false,
        'order' => 4,
    ],
    [
        'slug' => 'senior-competency-compliance',
        'term_id' => $termOps ? $termOps->id : null,
        'title' => [
            'en' => 'Senior – Competency & Compliance',
            'id' => 'Senior – Competency & Compliance',
        ],
        'location' => [
            'en' => 'Jakarta, Indonesia',
            'id' => 'Jakarta, Indonesia',
        ],
        'employment_type' => 'Full-time',
        'short_description' => [
            'en' => 'Oversee organizational competency development, ISO standards compliance, vendor certification audits, and internal quality assurance frameworks.',
            'id' => 'Mengawasi pengembangan kompetensi organisasi, kepatuhan standar ISO, audit sertifikasi vendor, dan kerangka jaminan kualitas internal.',
        ],
        'responsibilities' => [
            'en' => [
                'Develop and execute employee competency framework and technical certification roadmaps',
                'Manage compliance audits for ISO 9001 / ISO 27001 and principal vendor partner requirements',
                'Conduct internal quality process reviews and ensure operational standards adherence across business units',
                'Coordinate with HR and Business Unit Heads to identify competency gaps and training initiatives',
                'Track, monitor, and report organizational compliance metrics and partner status level maintenance',
                'Drive continuous process improvement and governance policy enforcement',
            ],
            'id' => [
                'Mengembangkan dan mengeksekusi kerangka kerja kompetensi karyawan dan peta jalan sertifikasi teknis',
                'Mengelola audit kepatuhan untuk ISO 9001 / ISO 27001 dan persyaratan mitra prinsipal vendor',
                'Melakukan peninjauan proses kualitas internal dan memastikan kepatuhan standar operasional di seluruh unit bisnis',
                'Bermitra dengan HR dan Head of Business Unit untuk mengidentifikasi kesenjangan kompetensi dan inisiatif pelatihan',
                'Melacak, memantau, dan melaporkan metrik kepatuhan organisasi serta pemeliharaan level status mitra',
                'Mendorong peningkatan proses secara berkelanjutan dan penegakan kebijakan tata kelola',
            ],
        ],
        'requirements' => [
            'en' => [
                'Bachelor Degree in Industrial Engineering, Management, Law, or Information Technology',
                '3+ years experience in Quality Assurance, ISO Audit, Compliance, or HR Competency Management',
                'Certified ISO 27001 Lead Auditor or Quality Management System certification is preferred',
                'Strong analytical capabilities, meticulous attention to detail, and process governance skills',
                'Proficiency in corporate reporting and cross-departmental stakeholder management',
            ],
            'id' => [
                'Gelar Sarjana (S1) Teknik Industri, Manajemen, Hukum, atau Teknologi Informasi',
                'Pengalaman 3+ tahun dalam Quality Assurance, Audit ISO, Kepatuhan, atau Manajemen Kompetensi HR',
                'Sertifikasi ISO 27001 Lead Auditor atau Sistem Manajemen Mutu lebih diutamakan',
                'Kemampuan analitis yang kuat, perhatian tinggi pada detail, dan keterampilan tata kelola proses',
                'Kemahiran dalam pelaporan korporat dan pengelolaan pemangku kepentingan lintas departemen',
            ],
        ],
        'is_featured' => false,
        'order' => 5,
    ],
    [
        'slug' => 'team-leader-product',
        'term_id' => $termSales ? $termSales->id : null,
        'title' => [
            'en' => 'Team Leader – Product',
            'id' => 'Team Leader – Product',
        ],
        'location' => [
            'en' => 'Jakarta, Indonesia',
            'id' => 'Jakarta, Indonesia',
        ],
        'employment_type' => 'Full-time',
        'short_description' => [
            'en' => 'Lead the product specialist team, oversee vendor product management strategies, align sales enablement, and optimize product revenue performance.',
            'id' => 'Memimpin tim spesialis produk, mengawasi strategi manajemen produk vendor, menyelaraskan enablement penjualan, dan mengoptimalkan kinerja pendapatan produk.',
        ],
        'responsibilities' => [
            'en' => [
                'Lead, coach, and manage a team of Product Specialists across multiple technology brands',
                'Formulate product line strategy, revenue target allocation, and vendor business plans',
                'Maintain high-level relationships with technology vendors and distribution partners',
                'Monitor team performance, sales enablement execution, and market expansion campaigns',
            ],
            'id' => [
                'Memimpin, membimbing, dan mengelola tim Product Specialist di berbagai merek teknologi',
                'Merumuskan strategi lini produk, alokasi target pendapatan, dan rencana bisnis vendor',
                'Memelihara hubungan tingkat tinggi dengan vendor teknologi dan mitra distribusi',
                'Memantau kinerja tim, eksekusi enablement penjualan, dan kampanye ekspansi pasar',
            ],
        ],
        'requirements' => [
            'en' => [
                'Bachelor Degree in Business, IT, Management, or Engineering',
                '4+ years experience in Product Management or Product Marketing within IT distribution or principal vendor',
                'Minimum 1-2 years experience in supervisory or team leadership role',
                'Strong strategic planning, negotiation, financial analysis, and team leadership skills',
            ],
            'id' => [
                'Gelar Sarjana (S1) Bisnis, IT, Manajemen, atau Teknik',
                'Pengalaman 4+ tahun dalam Manajemen Produk atau Pemasaran Produk di industri distribusi IT atau vendor prinsipal',
                'Pengalaman minimal 1-2 tahun dalam peran supervisi atau kepemimpinan tim',
                'Perencanaan strategis, negosiasi, analisis keuangan, dan keterampilan kepemimpinan tim yang kuat',
            ],
        ],
        'is_featured' => false,
        'order' => 6,
    ],
];

echo "Starting import of 6 job vacancies...\n";

foreach ($jobsData as $j) {
    $metaPayload = [
        'location' => $j['location']['en'],
        'employment_type' => $j['employment_type'],
        'short_description' => $j['short_description']['en'],
        'responsibilities' => $j['responsibilities']['en'],
        'requirements' => $j['requirements']['en'],
        'is_featured' => $j['is_featured'],
        'order' => $j['order'],
        '_translations' => [
            'en' => [
                'location' => $j['location']['en'],
                'employment_type' => $j['employment_type'],
                'short_description' => $j['short_description']['en'],
                'responsibilities' => $j['responsibilities']['en'],
                'requirements' => $j['requirements']['en'],
            ],
            'id' => [
                'location' => $j['location']['id'],
                'employment_type' => $j['employment_type'],
                'short_description' => $j['short_description']['id'],
                'responsibilities' => $j['responsibilities']['id'],
                'requirements' => $j['requirements']['id'],
            ],
        ],
    ];

    $entry = CptEntry::updateOrCreate(
        [
            'post_type_id' => $cpt->id,
            'slug' => $j['slug'],
        ],
        [
            'title' => $j['title']['en'],
            'content' => $j['short_description']['en'],
            'excerpt' => $j['short_description']['en'],
            'status' => 'published',
            'author_id' => $authorId,
            'published_at' => now(),
            'meta' => $metaPayload,
        ]
    );

    // Sync Taxonomy Term
    if ($j['term_id']) {
        $entry->terms()->sync([$j['term_id']]);
    }

    // Set Translations for title, content, excerpt
    $entry->setTranslation('title', 'en', $j['title']['en']);
    $entry->setTranslation('title', 'id', $j['title']['id']);

    $entry->setTranslation('content', 'en', $j['short_description']['en']);
    $entry->setTranslation('content', 'id', $j['short_description']['id']);

    $entry->setTranslation('excerpt', 'en', $j['short_description']['en']);
    $entry->setTranslation('excerpt', 'id', $j['short_description']['id']);

    $entry->save();

    echo " -> Imported Job: [{$entry->id}] {$j['title']['en']} (Slug: {$j['slug']})\n";
}

echo "All 6 job vacancies successfully imported with full EN & ID translations!\n";
