<?php

namespace Plugins\KouventaPages\Schemas;

class SolutionAiSchema
{
    /**
     * Get list of admin section tabs for this page.
     */
    public static function getTabs(): array
    {
        return [
            ['id' => 'hero', 'label' => '1. Hero & Subnav', 'icon' => 'home'],
            ['id' => 'dept_cs', 'label' => '2. Customer Service', 'icon' => 'support_agent'],
            ['id' => 'dept_recruitment', 'label' => '3. Recruitment', 'icon' => 'badge'],
            ['id' => 'dept_sales', 'label' => '4. Sales & Marketing', 'icon' => 'campaign'],
            ['id' => 'dept_ops', 'label' => '5. Business Operations', 'icon' => 'account_tree'],
            ['id' => 'dept_finance', 'label' => '6. Finance (2 Cases)', 'icon' => 'payments'],
            ['id' => 'dept_legal', 'label' => '7. Legal & Compliance', 'icon' => 'gavel'],
            ['id' => 'journey', 'label' => '8. Implementation Journey', 'icon' => 'alt_route'],
            ['id' => 'cta', 'label' => '9. Bottom CTA', 'icon' => 'call_to_action'],
            ['id' => 'seo', 'label' => '10. SEO & Settings', 'icon' => 'travel_explore'],
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
    'subnav' => 
    array (
      'label' => 'Solusi',
      'ai_label' => 'Artificial Intelligence',
      'ce_label' => 'Customer Engagement',
    ),
    'title' => 
    array (
      'prefix' => 'Dari pengetahuan ke aksi',
      'main' => 'dengan AI.',
    ),
    'subtitle' => 'Dari pencarian pengetahuan hingga otomasi proses, Kouventa AI membantu bisnis mengubah informasi menjadi insight yang bisa ditindaklanjuti dan mengotomasi proses bisnis dengan agent AI yang otonom.',
    'primary_cta' => 
    array (
      'text' => 'Minta Demo',
      'url' => '/book-demo',
    ),
    'secondary_btn' => 
    array (
      'text' => 'Lihat Cara Kerjanya',
    ),
  ),
  'departments' => 
  array (
    'cs' => 
    array (
      'id' => 'cs',
      'name' => 'Customer Service',
      'challenges' => 
      array (
        0 => 
        array (
          'text' => 'Agent customer service menghabiskan terlalu banyak waktu menjawab pertanyaan yang sama setiap hari',
        ),
        1 => 
        array (
          'text' => 'Pelanggan ingin respons cepat, sementara agent harus menangani banyak percakapan sekaligus',
        ),
        2 => 
        array (
          'text' => 'Agent harus berpindah-pindah antara CRM, sistem tiket, dan aplikasi bisnis lain untuk mencari informasi, sehingga penyelesaian masalah jadi lambat',
        ),
      ),
      'solutions' => 
      array (
        0 => 
        array (
          'title' => 'Customer Support Agent',
          'desc' => 'Memahami pertanyaan pelanggan sesuai konteks, memberi informasi akurat dari pengetahuan bisnis Anda, dan menyelesaikan pertanyaan rutin secara otomatis.',
        ),
        1 => 
        array (
          'title' => 'Ticket Management Agent',
          'desc' => 'Membuat tiket support secara otomatis dari percakapan pelanggan, mencatat detail yang diperlukan, dan meneruskannya ke tim yang tepat.',
        ),
      ),
      'impacts' => 
      array (
        0 => 
        array (
          'text' => 'Selesaikan pertanyaan dan masalah pelanggan lebih cepat, sehingga pengalaman pelanggan meningkat',
        ),
        1 => 
        array (
          'text' => 'Kurangi pekerjaan berulang agar agent bisa fokus pada kasus yang lebih kompleks',
        ),
        2 => 
        array (
          'text' => 'Otomasi pertanyaan rutin dan pengelolaan tiket manual',
        ),
      ),
    ),
    'recruitment' => 
    array (
      'id' => 'recruitment',
      'name' => 'Rekrutmen',
      'challenges' => 
      array (
        0 => 
        array (
          'text' => 'Tim HR menghabiskan berjam-jam meninjau CV, mencocokkan kandidat dengan kebutuhan posisi, mengurutkan profil, dan mengevaluasi hasil tes serta wawancara',
        ),
        1 => 
        array (
          'text' => 'Recruiter harus berpindah-pindah platform untuk mencari informasi kandidat dan memantau proses rekrutmen',
        ),
        2 => 
        array (
          'text' => 'Recruiter menghabiskan waktu untuk tugas berulang seperti input data, pembuatan dokumen, penjadwalan wawancara, dan notifikasi kandidat',
        ),
      ),
      'solutions' => 
      array (
        0 => 
        array (
          'title' => 'Candidate Screening and Sourcing Agent',
          'desc' => 'Mencari kandidat dari berbagai job portal, lalu menyaring dan mencocokkannya berdasarkan kebutuhan posisi dan kualifikasi.',
        ),
        1 => 
        array (
          'title' => 'Candidate Communication Agent',
          'desc' => 'Mengirim undangan wawancara dan psikotes, memberi update rekrutmen, dan menjawab pertanyaan kandidat selama proses rekrutmen.',
        ),
        2 => 
        array (
          'title' => 'Candidate Scheduling Agent',
          'desc' => 'Mengatur jadwal wawancara dan psikotes berdasarkan ketersediaan HR, pewawancara, dan kandidat.',
        ),
        3 => 
        array (
          'title' => 'Recruitment Insight Agent',
          'desc' => 'Menganalisis profil kandidat berdasarkan hasil wawancara dan psikotes untuk menemukan insight penting dan memberi rekomendasi.',
        ),
      ),
      'impacts' => 
      array (
        0 => 
        array (
          'text' => 'Lebih sedikit waktu menyaring kandidat, lebih banyak waktu merekrut orang yang tepat',
        ),
        1 => 
        array (
          'text' => 'Kurangi beban kerja HR dengan mengotomasi tugas manual yang berulang',
        ),
        2 => 
        array (
          'text' => 'Ambil keputusan rekrutmen dengan lebih yakin berdasarkan insight dari data kandidat dan hasil evaluasi',
        ),
      ),
    ),
    'sales_marketing' => 
    array (
      'id' => 'sales_marketing',
      'name' => 'Sales & Marketing',
      'challenges' => 
      array (
        0 => 
        array (
          'text' => 'Leads hilang karena respons yang terlambat, padahal pelanggan berharap dibalas cepat bahkan di luar jam kerja',
        ),
        1 => 
        array (
          'text' => 'Tim sales harus menjawab pertanyaan berulang sambil mencari informasi di banyak dokumen dan sistem',
        ),
        2 => 
        array (
          'text' => 'Tim sales harus follow-up manual ke pelanggan yang tidak merespons, dan follow-up yang terlewat bisa berarti peluang penjualan yang hilang',
        ),
      ),
      'solutions' => 
      array (
        0 => 
        array (
          'title' => 'Sales Assistant Agent',
          'desc' => 'Memahami kebutuhan pelanggan, merekomendasikan produk yang relevan, menjawab pertanyaan soal produk dan ketersediaan, serta follow-up ke pelanggan yang belum merespons.',
        ),
        1 => 
        array (
          'title' => 'Order Agent',
          'desc' => 'Membuat pesanan, memproses pembayaran, memverifikasi transaksi, dan mengonfirmasi pembelian.',
        ),
        2 => 
        array (
          'title' => 'Shipment Tracking Agent',
          'desc' => 'Melacak status pesanan dan pengiriman, memberi update pengiriman, dan menjawab pertanyaan setelah pembelian.',
        ),
      ),
      'impacts' => 
      array (
        0 => 
        array (
          'text' => 'Tingkatkan Pengalaman Pelanggan',
          'desc' => 'Respons instan serta informasi produk, pesanan, dan pengiriman yang akurat.',
        ),
        1 => 
        array (
          'text' => 'Tingkatkan Produktivitas Sales',
          'desc' => 'Lebih sedikit pertanyaan berulang dan follow-up manual, sehingga tim sales bisa fokus pada peluang yang lebih bernilai.',
        ),
        2 => 
        array (
          'text' => 'Tingkatkan Konversi & Retensi Pelanggan',
          'desc' => 'Follow-up yang proaktif dan dukungan mulus di sepanjang proses pembelian.',
        ),
      ),
    ),
    'operations' => 
    array (
      'id' => 'operations',
      'name' => 'Operasional Bisnis',
      'challenges' => 
      array (
        0 => 
        array (
          'text' => 'Informasi internal tersebar di banyak dokumen, sistem, dan sumber, sehingga karyawan sulit menemukan kebijakan, SOP, dan informasi bisnis yang tepat',
        ),
        1 => 
        array (
          'text' => 'Karyawan membuang waktu berharga untuk mencari jawaban, padahal informasi penting perlu diakses segera',
        ),
        2 => 
        array (
          'text' => 'Tim support menghabiskan terlalu banyak waktu menjawab pertanyaan internal yang sama soal kebijakan, prosedur, dan informasi bisnis',
        ),
      ),
      'solutions' => 
      array (
        0 => 
        array (
          'title' => 'Knowledge Assistant Agent',
          'desc' => 'Mencari dan menjelaskan informasi dari dokumen internal, kebijakan, SOP, dan pengetahuan bisnis perusahaan.',
        ),
        1 => 
        array (
          'title' => 'Action Agent',
          'desc' => 'Mengubah permintaan karyawan menjadi aksi dengan terhubung ke sistem internal, mengajukan permintaan, dan menjalankan tugas rutin dengan persetujuan yang diperlukan.',
        ),
      ),
      'impacts' => 
      array (
        0 => 
        array (
          'text' => 'Beri karyawan akses instan ke informasi yang mereka butuhkan, kapan saja',
        ),
        1 => 
        array (
          'text' => 'Kurangi pertanyaan internal yang berulang dan pencarian manual, sehingga pekerjaan berulang tim support berkurang',
        ),
        2 => 
        array (
          'text' => 'Bantu karyawan menghemat waktu mencari informasi dan lebih banyak waktu menyelesaikan pekerjaan',
        ),
      ),
    ),
    'payment_prioritization' => 
    array (
      'id' => 'payment_prioritization',
      'name' => 'Prioritas pembayaran',
      'challenges' => 
      array (
        0 => 
        array (
          'text' => 'Tim finance menghabiskan terlalu banyak waktu meninjau invoice, jatuh tempo, arus kas, dan data vendor sebelum memutuskan apa yang dibayar lebih dulu',
        ),
        1 => 
        array (
          'text' => 'Tim finance harus menganalisis banyak data secara manual untuk menentukan pembayaran mana yang harus diprioritaskan',
        ),
        2 => 
        array (
          'text' => 'Analisis dan validasi pembayaran secara manual meningkatkan risiko kesalahan dan keputusan pembayaran yang tidak konsisten',
        ),
      ),
      'solutions' => 
      array (
        0 => 
        array (
          'title' => 'Payment Prioritization Agent',
          'desc' => '',
          'bullets' => 
          array (
            0 => 'Menganalisis invoice, jatuh tempo, arus kas, dan data vendor',
            1 => 'Memprioritaskan pembayaran berdasarkan kriteria bisnis',
            2 => 'Merekomendasikan batch pembayaran untuk disetujui',
            3 => 'Menjalankan pembayaran yang disetujui melalui ERP',
          ),
        ),
      ),
      'impacts' => 
      array (
        0 => 
        array (
          'text' => 'Bantu tim finance menentukan prioritas pembayaran lebih cepat dan mengambil keputusan tepat waktu berbasis data',
        ),
        1 => 
        array (
          'text' => 'Capai efisiensi operasional lebih tinggi dengan mengurangi analisis manual dan menyederhanakan proses prioritas pembayaran',
        ),
        2 => 
        array (
          'text' => 'Tingkatkan akurasi validasi dengan menganalisis data invoice secara konsisten dan mengurangi kesalahan manual',
        ),
      ),
    ),
    'bank_reconciliation' => 
    array (
      'id' => 'bank_reconciliation',
      'name' => 'Rekonsiliasi Bank',
      'challenges' => 
      array (
        0 => 
        array (
          'text' => 'Tim finance masih mengandalkan rekonsiliasi manual untuk mencocokkan transaksi bank dengan invoice dan catatan keuangan',
        ),
        1 => 
        array (
          'text' => 'Tim finance harus menemukan selisih, menelusuri detail transaksi, dan mencari akar masalah secara manual untuk menyelesaikan masalah rekonsiliasi',
        ),
        2 => 
        array (
          'text' => 'Seiring bertambahnya volume transaksi, rekonsiliasi manual makin sulit dikelola secara efisien dan sulit ditingkatkan skalanya',
        ),
      ),
      'solutions' => 
      array (
        0 => 
        array (
          'title' => 'Transaction Matching Agent',
          'desc' => 'Mencocokkan transaksi bank dengan invoice secara otomatis, termasuk transaksi parsial dan gabungan.',
        ),
        1 => 
        array (
          'title' => 'Reconciliation Analysis Agent',
          'desc' => 'Menangani selisih kurs dan pertimbangan pajak, menemukan transaksi yang tidak cocok dan selisihnya, serta menjelaskan akar masalahnya.',
        ),
        2 => 
        array (
          'title' => 'Clearing Agent',
          'desc' => 'Menyiapkan usulan clearing dan menjalankan clearing di ERP.',
        ),
        3 => 
        array (
          'title' => 'Audit & Notification Agent',
          'desc' => 'Mencatat aktivitas dan mengirim notifikasi yang relevan ke tim terkait.',
        ),
      ),
      'impacts' => 
      array (
        0 => 
        array (
          'text' => 'Kurangi aktivitas finance yang manual dengan mengotomasi pencocokan dan analisis transaksi',
        ),
        1 => 
        array (
          'text' => 'Percepat rekonsiliasi dengan membantu tim finance menemukan dan menyelesaikan selisih lebih cepat, lengkap dengan penjelasan akar masalahnya',
        ),
        2 => 
        array (
          'text' => 'Tangani volume transaksi yang lebih besar tanpa menambah beban kerja manual',
        ),
      ),
    ),
    'legal_compliance' => 
    array (
      'id' => 'legal_compliance',
      'name' => 'Legal & Compliance',
      'challenges' => 
      array (
        0 => 
        array (
          'text' => 'Review kontrak memakan waktu terlalu lama karena tim legal harus mencari klausul dan ketentuan di dokumen yang panjang secara manual',
        ),
        1 => 
        array (
          'text' => 'Klausul, kebijakan, dan regulasi penting tersebar di banyak dokumen, sehingga hal penting mudah terlewat',
        ),
        2 => 
        array (
          'text' => 'Makin banyaknya kontrak dan regulasi meningkatkan risiko kewajiban compliance yang terlewat dan potensi masalah',
        ),
      ),
      'solutions' => 
      array (
        0 => 
        array (
          'title' => 'Contract Review Agent',
          'desc' => 'Meninjau kontrak dan menemukan klausul penting, ketentuan, serta potensi masalah.',
        ),
        1 => 
        array (
          'title' => 'Legal Research Agent',
          'desc' => 'Mencari kebijakan, regulasi, dan informasi hukum yang relevan sesuai konteks kontrak.',
        ),
        2 => 
        array (
          'title' => 'Contract Insights Agent',
          'desc' => 'Menjawab pertanyaan serta merangkum ketentuan penting dan informasi utama dari kontrak.',
        ),
      ),
      'impacts' => 
      array (
        0 => 
        array (
          'text' => 'Tingkatkan kualitas compliance dengan menemukan klausul, ketentuan, dan kebijakan yang berlaku, sekaligus mengurangi risiko kewajiban yang terlewat',
        ),
        1 => 
        array (
          'text' => 'Review kontrak lebih cepat dengan menemukan ketentuan, klausul, dan kebijakan yang relevan dengan sigap, sehingga tim legal menghemat waktu',
        ),
        2 => 
        array (
          'text' => 'Kurangi pekerjaan manual dengan mengotomasi tanya-jawab dan ringkasan kontrak, sehingga tim legal bisa fokus pada pekerjaan hukum yang lebih bernilai',
        ),
      ),
    ),
  ),
  'journey' => 
  array (
    'title' => 'Tahapan implementasi',
    'steps' => 
    array (
      0 => 
      array (
        'number' => 1,
        'title' => 'Analisis',
        'desc' => 'Tim kami akan memetakan proses bisnis, kendala, dan kebutuhan yang ada untuk menemukan di mana Agentic AI bisa memberi nilai.',
      ),
      1 => 
      array (
        'number' => 2,
        'title' => 'Integrasi',
        'desc' => 'Hubungkan sumber data, aplikasi, dan sistem yang dibutuhkan agent untuk menjalankan tugasnya.',
      ),
      2 => 
      array (
        'number' => 3,
        'title' => 'Konfigurasi',
        'desc' => 'Tentukan workflow, aturan bisnis, aksi, dan guardrails berdasarkan proses bisnis Anda.',
      ),
      3 => 
      array (
        'number' => 4,
        'title' => 'Deploy & Skalakan',
        'desc' => 'Luncurkan agent ke production, pantau performanya, dan perluas agent AI ke lebih banyak proses.',
      ),
    ),
  ),
  'cta' => 
  array (
    'title' => 'Mulai dari yang sederhana. Kembangkan dengan yakin.',
    'subtitle' => 'Mulai dengan AI untuk pekerjaan sehari-hari karyawan, dengan tata kelola dari organisasi Anda. Kembangkan ke agent AI saat Anda siap.',
    'primary_btn' => 
    array (
      'text' => 'Temukan use case pertama Anda',
      'url' => '#solution-tabs',
    ),
    'secondary_btn' => 
    array (
      'text' => 'Hubungi sales',
      'url' => '#whatsapp',
    ),
  ),
  'seo' => 
  array (
    'meta_title' => 'AI Solutions for Enterprise Teams & Departments | Kouventa',
    'meta_description' => 'Empower finance, HR, legal, sales, IT, and operations with autonomous AI agents that turn knowledge into action and streamline business processes.',
    'keywords' => 'AI solutions, enterprise AI agents, automated workflow, customer service AI, finance AI',
    'canonical_url' => '',
  ),
);
        }

        return array (
  'hero' => 
  array (
    'subnav' => 
    array (
      'label' => 'Solutions',
      'ai_label' => 'Artificial Intelligence',
      'ce_label' => 'Customer Engagement',
    ),
    'title' => 
    array (
      'prefix' => 'From knowledge to action',
      'main' => 'with AI.',
    ),
    'subtitle' => 'From knowledge retrieval to process automation, Kouventa AI helps businesses turn information into actionable insights and automate business processes with AI-powered autonomous agents.',
    'primary_cta' => 
    array (
      'text' => 'Request a Demo',
      'url' => '/book-demo',
    ),
    'secondary_btn' => 
    array (
      'text' => 'See How It Works',
    ),
  ),
  'departments' => 
  array (
    'cs' => 
    array (
      'id' => 'cs',
      'name' => 'Customer Service',
      'challenges' => 
      array (
        0 => 
        array (
          'text' => 'Customer service agents spend too much time answering the same repetitive questions every day',
        ),
        1 => 
        array (
          'text' => 'Customers expect fast responses, while agents need to handle multiple conversations simultaneously',
        ),
        2 => 
        array (
          'text' => 'Agents have to switch between CRM, ticketing systems, and other business applications to find the right information, slowing down issue resolution',
        ),
      ),
      'solutions' => 
      array (
        0 => 
        array (
          'title' => 'Customer Support Agent',
          'desc' => 'Understand customer questions in context, provide accurate information from your business knowledge, and resolve routine inquiries automatically.',
        ),
        1 => 
        array (
          'title' => 'Ticket Management Agent',
          'desc' => 'Automatically create support tickets from customer conversations, capture the necessary details, and assign them to the right team.',
        ),
      ),
      'impacts' => 
      array (
        0 => 
        array (
          'text' => 'Resolve customer inquiries and issues faster, improving customer experience',
        ),
        1 => 
        array (
          'text' => 'Reduce repetitive work so agents can focus on more complex cases',
        ),
        2 => 
        array (
          'text' => 'Automate routine inquiries and manual ticket management',
        ),
      ),
    ),
    'recruitment' => 
    array (
      'id' => 'recruitment',
      'name' => 'Recruitment',
      'challenges' => 
      array (
        0 => 
        array (
          'text' => 'HR teams spend hours reviewing resumes, matching candidates to job requirements, ranking profiles, and evaluating test and interview results',
        ),
        1 => 
        array (
          'text' => 'Recruiters have to switch between multiple platforms to find candidate information and track the hiring process',
        ),
        2 => 
        array (
          'text' => 'Recruiters spend time on repetitive tasks such as data entry, document generation, interview scheduling, and candidate notifications',
        ),
      ),
      'solutions' => 
      array (
        0 => 
        array (
          'title' => 'Candidate Screening and Sourcing Agent',
          'desc' => 'Source candidates from multiple job portals, then screen and match candidates based on job requirements and qualifications.',
        ),
        1 => 
        array (
          'title' => 'Candidate Communication Agent',
          'desc' => 'Send interview and psychotest invitations, provide recruitment updates, and answer candidate questions throughout the hiring process.',
        ),
        2 => 
        array (
          'title' => 'Candidate Scheduling Agent',
          'desc' => 'Coordinate and schedule interviews and psychotests based on the availability of HR, interviewers, and candidates.',
        ),
        3 => 
        array (
          'title' => 'Recruitment Insight Agent',
          'desc' => 'Analyze candidate profiles based on interview and psychotest results to identify key insights and provide recommendations.',
        ),
      ),
      'impacts' => 
      array (
        0 => 
        array (
          'text' => 'Spend less time screening candidates, and more time hiring the right ones',
        ),
        1 => 
        array (
          'text' => 'Reduce HR workload by automating repetitive manual tasks',
        ),
        2 => 
        array (
          'text' => 'Make hiring decisions with greater confidence using insights from candidate data and evaluation results',
        ),
      ),
    ),
    'sales_marketing' => 
    array (
      'id' => 'sales_marketing',
      'name' => 'Sales & Marketing',
      'challenges' => 
      array (
        0 => 
        array (
          'text' => 'Missed leads from delayed responses, because customers expect quick responses even outside working hours',
        ),
        1 => 
        array (
          'text' => 'Sales teams have to answer repetitive questions while searching through multiple documents and systems to find the right information',
        ),
        2 => 
        array (
          'text' => 'Sales teams have to manually follow up with unresponsive customers, and missed follow-ups can mean missed sales opportunities',
        ),
      ),
      'solutions' => 
      array (
        0 => 
        array (
          'title' => 'Sales Assistant Agent',
          'desc' => 'Understand customer needs, recommend relevant products, answer product and availability questions, and follow up with customers who don\'t respond.',
        ),
        1 => 
        array (
          'title' => 'Order Agent',
          'desc' => 'Create orders, process payments, verify transactions, and confirm purchases.',
        ),
        2 => 
        array (
          'title' => 'Shipment Tracking Agent',
          'desc' => 'Track order and shipment status, provide delivery updates, and answer post-purchase questions.',
        ),
      ),
      'impacts' => 
      array (
        0 => 
        array (
          'text' => 'Enhance Customer Experiences',
          'desc' => 'Instant responses and accurate product, order, and shipment information.',
        ),
        1 => 
        array (
          'text' => 'Increase Sales Productivity',
          'desc' => 'Fewer repetitive inquiries and manual follow-ups, allowing sales teams to focus on higher-value opportunities.',
        ),
        2 => 
        array (
          'text' => 'Improve Customer Conversion & Retention',
          'desc' => 'Proactive follow-ups and seamless support throughout the purchase journey.',
        ),
      ),
    ),
    'operations' => 
    array (
      'id' => 'operations',
      'name' => 'Business Operations',
      'challenges' => 
      array (
        0 => 
        array (
          'text' => 'Internal information is scattered across documents, systems, and sources, making it difficult for employees to find the right policies, SOPs, and business information',
        ),
        1 => 
        array (
          'text' => 'Employees spend valuable time searching for answers when important information needs to be accessed immediately',
        ),
        2 => 
        array (
          'text' => 'Support teams spend too much time answering the same internal questions about policies, procedures, and business information',
        ),
      ),
      'solutions' => 
      array (
        0 => 
        array (
          'title' => 'Knowledge Assistant Agent',
          'desc' => 'Find and explain information from the company\'s internal documents, policies, SOPs, and business knowledge.',
        ),
        1 => 
        array (
          'title' => 'Action Agent',
          'desc' => 'Turn employee requests into actions by connecting with internal systems, submitting requests, and executing routine tasks with the required approvals.',
        ),
      ),
      'impacts' => 
      array (
        0 => 
        array (
          'text' => 'Give employees instant access to the information they need, anytime',
        ),
        1 => 
        array (
          'text' => 'Reduce recurring internal questions and manual searches, reducing repetitive work for support teams',
        ),
        2 => 
        array (
          'text' => 'Help employees spend less time searching for information and more time getting work done',
        ),
      ),
    ),
    'payment_prioritization' => 
    array (
      'id' => 'payment_prioritization',
      'name' => 'Payment Prioritization',
      'challenges' => 
      array (
        0 => 
        array (
          'text' => 'Finance teams spend too much time reviewing invoices, due dates, cash flow, and vendor data before deciding what to pay first',
        ),
        1 => 
        array (
          'text' => 'Finance teams need to analyze multiple data points manually to determine which payments should be prioritized',
        ),
        2 => 
        array (
          'text' => 'Manual payment analysis and validation increase the risk of errors and inconsistent payment decisions',
        ),
      ),
      'solutions' => 
      array (
        0 => 
        array (
          'title' => 'Payment Prioritization Agent',
          'desc' => '',
          'bullets' => 
          array (
            0 => 'Analyze invoices, due dates, cash flow, and vendor data',
            1 => 'Prioritize payments based on business criteria',
            2 => 'Recommend payment batches for approval',
            3 => 'Execute approved payments through ERP',
          ),
        ),
      ),
      'impacts' => 
      array (
        0 => 
        array (
          'text' => 'Enable finance teams to identify payment priorities faster and make timely, data-driven decisions',
        ),
        1 => 
        array (
          'text' => 'Achieve higher operational efficiency by reducing manual analysis and streamlining the payment prioritization process',
        ),
        2 => 
        array (
          'text' => 'Improve validation accuracy by analyzing invoice data consistently and reducing manual errors',
        ),
      ),
    ),
    'bank_reconciliation' => 
    array (
      'id' => 'bank_reconciliation',
      'name' => 'Bank Reconciliation',
      'challenges' => 
      array (
        0 => 
        array (
          'text' => 'Finance teams still rely on manual reconciliation to match bank transactions with invoices and financial records',
        ),
        1 => 
        array (
          'text' => 'Finance teams need to manually identify mismatches, trace transaction details, and determine the root cause to resolve reconciliation issues',
        ),
        2 => 
        array (
          'text' => 'As transaction volumes grow, manual reconciliation becomes harder to manage efficiently and scale',
        ),
      ),
      'solutions' => 
      array (
        0 => 
        array (
          'title' => 'Transaction Matching Agent',
          'desc' => 'Automatically matches bank transactions with invoices, including partial and bulk transactions.',
        ),
        1 => 
        array (
          'title' => 'Reconciliation Analysis Agent',
          'desc' => 'Handles foreign exchange and tax considerations, identifies unmatched transactions and discrepancies, and explains the root causes.',
        ),
        2 => 
        array (
          'title' => 'Clearing Agent',
          'desc' => 'Prepares clearing proposals and executes ERP clearing.',
        ),
        3 => 
        array (
          'title' => 'Audit & Notification Agent',
          'desc' => 'Logs activities and sends relevant notifications to related teams.',
        ),
      ),
      'impacts' => 
      array (
        0 => 
        array (
          'text' => 'Reduce manual finance activities by automating transaction matching and analysis',
        ),
        1 => 
        array (
          'text' => 'Accelerate reconciliation by helping finance teams identify and resolve discrepancies faster with clear root-cause explanations',
        ),
        2 => 
        array (
          'text' => 'Handle higher transaction volumes without increasing manual workload',
        ),
      ),
    ),
    'legal_compliance' => 
    array (
      'id' => 'legal_compliance',
      'name' => 'Legal & Compliance',
      'challenges' => 
      array (
        0 => 
        array (
          'text' => 'Contract reviews take too long, with legal teams manually searching lengthy documents for relevant clauses and terms',
        ),
        1 => 
        array (
          'text' => 'Critical clauses, policies, and regulations are scattered across multiple documents, making it easy to miss what matters',
        ),
        2 => 
        array (
          'text' => 'Growing volumes of contracts and regulations increase the risk of missed compliance requirements and potential issues',
        ),
      ),
      'solutions' => 
      array (
        0 => 
        array (
          'title' => 'Contract Review Agent',
          'desc' => 'Review contracts and identify key clauses, terms, and potential issues.',
        ),
        1 => 
        array (
          'title' => 'Legal Research Agent',
          'desc' => 'Find relevant policies, regulations, and legal information based on the context of the contract.',
        ),
        2 => 
        array (
          'title' => 'Contract Insights Agent',
          'desc' => 'Answer questions and summarize key terms and important information from contracts.',
        ),
      ),
      'impacts' => 
      array (
        0 => 
        array (
          'text' => 'Improve compliance quality by identifying relevant clauses, key terms, and applicable policies while reducing the risk of missed requirements',
        ),
        1 => 
        array (
          'text' => 'Review contracts faster by quickly finding key terms, clauses, and relevant policies, so legal teams spend less time reviewing',
        ),
        2 => 
        array (
          'text' => 'Reduce manual work by automating contract Q&A and summarization, allowing legal teams to focus on higher-value legal activities',
        ),
      ),
    ),
  ),
  'journey' => 
  array (
    'title' => 'Implementation journey',
    'steps' => 
    array (
      0 => 
      array (
        'number' => 1,
        'title' => 'Discover',
        'desc' => 'Our team will map existing business processes, pain points, and requirements to identify where Agentic AI can create value.',
      ),
      1 => 
      array (
        'number' => 2,
        'title' => 'Connect',
        'desc' => 'Connect relevant data sources, applications, and systems required for the agent to perform its tasks.',
      ),
      2 => 
      array (
        'number' => 3,
        'title' => 'Configure',
        'desc' => 'Define workflows, business rules, actions, and guardrails based on your business processes.',
      ),
      3 => 
      array (
        'number' => 4,
        'title' => 'Deploy & Scale',
        'desc' => 'Launch your agent into production, monitor its performance, and expand AI agents across more processes.',
      ),
    ),
  ),
  'cta' => 
  array (
    'title' => 'Start simple. Scale with confidence.',
    'subtitle' => 'Begin with everyday AI for your employees, governed by your organization. Grow into AI agents when you\'re ready.',
    'primary_btn' => 
    array (
      'text' => 'Find your first use case',
      'url' => '#solution-tabs',
    ),
    'secondary_btn' => 
    array (
      'text' => 'Talk to Sales',
      'url' => '#whatsapp',
    ),
  ),
  'seo' => 
  array (
    'meta_title' => 'AI Solutions for Enterprise Teams & Departments | Kouventa',
    'meta_description' => 'Empower finance, HR, legal, sales, IT, and operations with autonomous AI agents that turn knowledge into action and streamline business processes.',
    'keywords' => 'AI solutions, enterprise AI agents, automated workflow, customer service AI, finance AI',
    'canonical_url' => '',
  ),
);
    }
}
