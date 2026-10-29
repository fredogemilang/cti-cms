<?php

namespace Plugins\KouventaPages\Schemas;

class SolutionCeSchema
{
    /**
     * Get list of admin section tabs for this page.
     */
    public static function getTabs(): array
    {
        return [
            ['id' => 'hero', 'label' => '1. Hero & Subnav', 'icon' => 'home'],
            ['id' => 'role_sales', 'label' => '2. Sales & Marketing', 'icon' => 'campaign'],
            ['id' => 'role_cs', 'label' => '3. Customer Service', 'icon' => 'support_agent'],
            ['id' => 'role_ops', 'label' => '4. Operations', 'icon' => 'settings'],
            ['id' => 'cta', 'label' => '5. Bottom CTA', 'icon' => 'call_to_action'],
            ['id' => 'seo', 'label' => '6. SEO & Settings', 'icon' => 'travel_explore'],
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
      'prefix' => 'Pengalaman pelanggan',
      'main' => 'omnichannel yang mulus.',
    ),
    'subtitle' => 'Dari kampanye marketing dan customer support hingga update operasional, Kouventa membantu bisnis terhubung, merespons, dan berkomunikasi dengan lebih efektif.',
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
  'roles' => 
  array (
    'sales_marketing' => 
    array (
      'id' => 'sales_marketing',
      'badge' => 'S&M',
      'name' => 'Sales & Marketing',
      'challenges' => 
      array (
        0 => 
        array (
          'text' => 'Jangkauan dan personalisasi kampanye terbatas',
        ),
        1 => 
        array (
          'text' => 'Risiko kehilangan leads karena respons yang lambat',
        ),
        2 => 
        array (
          'text' => 'Percakapan pelanggan tersebar di banyak channel',
        ),
        3 => 
        array (
          'text' => 'Pelanggan butuh cara verifikasi identitas yang cepat dan aman tanpa mengganggu pengalaman mereka',
        ),
      ),
      'solutions' => 
      array (
        0 => 
        array (
          'title' => 'WhatsApp Broadcast',
          'desc' => 'Kirim kampanye promo, peluncuran produk, dan penawaran musiman ke pelanggan dalam skala besar.',
        ),
        1 => 
        array (
          'title' => 'Chatbot',
          'desc' => 'Jawab pertanyaan produk dan layanan dengan chatbot predefined atau AI, atau eskalasi ke agent manusia untuk permintaan yang lebih kompleks.',
        ),
        2 => 
        array (
          'title' => 'Omnichannel',
          'desc' => 'Kelola percakapan pelanggan dan balas komentar dari WhatsApp, Instagram, Facebook, dan Live Chat dalam satu platform.',
        ),
        3 => 
        array (
          'title' => 'Authentication',
          'desc' => 'Amankan login dan transaksi pelanggan dengan verifikasi OTP WhatsApp yang cepat dan mulus.',
        ),
        4 => 
        array (
          'title' => 'WhatsApp Flows',
          'desc' => 'Permudah pengumpulan data pelanggan untuk pembelian, registrasi program loyalitas, pendaftaran member, dan lainnya.',
        ),
      ),
      'impacts' => 
      array (
        0 => 
        array (
          'title' => 'Jangkau lebih banyak pelanggan, lebih efektif',
          'desc' => '',
        ),
        1 => 
        array (
          'title' => 'Minimalkan leads yang hilang dan tingkatkan pengalaman pelanggan',
          'desc' => '',
        ),
        2 => 
        array (
          'title' => 'Operasional sales dan marketing lebih efisien',
          'desc' => '',
        ),
        3 => 
        array (
          'title' => 'Tingkatkan engagement pelanggan di semua channel',
          'desc' => '',
        ),
      ),
    ),
    'customer_service' => 
    array (
      'id' => 'customer_service',
      'badge' => 'CS',
      'name' => 'Customer Service',
      'challenges' => 
      array (
        0 => 
        array (
          'text' => 'Pelanggan ingin keluhannya ditangani dengan cepat, sementara tim customer service harus menangani banyak masalah dan permintaan sekaligus',
        ),
        1 => 
        array (
          'text' => 'Tim customer service menghabiskan banyak waktu untuk pertanyaan yang berulang, sehingga sulit fokus pada masalah yang butuh penanganan manusia',
        ),
        2 => 
        array (
          'text' => 'Membuat dan mencatat tiket secara manual bisa menyebabkan kesalahan, informasi yang tidak lengkap, dan miskomunikasi saat menangani masalah',
        ),
      ),
      'solutions' => 
      array (
        0 => 
        array (
          'title' => 'Omnichannel',
          'desc' => 'Pusatkan keluhan pelanggan dari WhatsApp, Instagram, Facebook, dan Live Chat dalam satu platform, sehingga lebih mudah memantau dan merespons setiap masalah.',
        ),
        1 => 
        array (
          'title' => 'Chatbot',
          'desc' => 'Tangani keluhan umum secara instan dengan chatbot predefined atau AI, dan eskalasi masalah yang kompleks ke agent manusia saat dibutuhkan.',
        ),
        2 => 
        array (
          'title' => 'Pembuatan Tiket',
          'desc' => 'Buat tiket support dari keluhan pelanggan, mengurangi pekerjaan manual serta meminimalkan kesalahan dan miskomunikasi saat menangani masalah.',
        ),
        3 => 
        array (
          'title' => 'CSAT Survey',
          'desc' => 'Kumpulkan feedback pelanggan secara otomatis setelah keluhan selesai untuk mengukur kepuasan dan menemukan peluang meningkatkan kualitas layanan.',
        ),
      ),
      'impacts' => 
      array (
        0 => 
        array (
          'title' => 'Penyelesaian Keluhan Lebih Cepat',
          'desc' => 'Respons instan dan eskalasi mulus ke agent manusia untuk masalah yang lebih kompleks.',
        ),
        1 => 
        array (
          'title' => 'Operasional Customer Service Lebih Efisien',
          'desc' => 'Otomasi pertanyaan berulang dan kurangi pembuatan tiket manual, agar tim bisa fokus pada masalah yang lebih kompleks.',
        ),
        2 => 
        array (
          'title' => 'Pengalaman Pelanggan Lebih Baik',
          'desc' => 'Dukungan yang lebih konsisten dan responsif, serta feedback pelanggan untuk terus meningkatkan kualitas layanan.',
        ),
      ),
    ),
    'operations' => 
    array (
      'id' => 'operations',
      'badge' => 'OPS',
      'name' => 'Operasional',
      'challenges' => 
      array (
        0 => 
        array (
          'text' => 'Mengirim update pesanan, pengiriman, pembayaran, dan janji temu secara manual memakan waktu dan bisa menyebabkan keterlambatan, kesalahan, atau notifikasi yang terlewat',
        ),
        1 => 
        array (
          'text' => 'Seiring bertambahnya volume transaksi, mengirim notifikasi rutin secara manual makin sulit dilakukan secara konsisten dan tepat waktu',
        ),
      ),
      'solutions' => 
      array (
        0 => 
        array (
          'title' => 'Cross Platform Integrations',
          'desc' => 'Kirim pesan WhatsApp otomatis dari sistem bisnis berdasarkan event operasional, seperti konfirmasi pesanan, update pengiriman, pengingat pembayaran, dan notifikasi janji temu.',
        ),
      ),
      'impacts' => 
      array (
        0 => 
        array (
          'title' => 'Pelanggan Selalu Ter-update di Setiap Langkah',
          'desc' => 'Notifikasi tepat waktu yang dipicu oleh event bisnis.',
        ),
        1 => 
        array (
          'title' => 'Pengalaman Pelanggan yang Lebih Konsisten',
          'desc' => 'Update yang jelas dan seragam di setiap proses.',
        ),
      ),
    ),
  ),
  'cta' => 
  array (
    'title' => 'Siap meningkatkan kualitas percakapan dengan pelanggan?',
    'subtitle' => 'Ceritakan kebutuhan tim Anda, dan kami tunjukkan bagaimana Kouventa bisa membantu.',
    'primary_btn' => 
    array (
      'text' => 'Minta Demo',
      'url' => '/book-demo',
    ),
    'secondary_btn' => 
    array (
      'text' => 'Hubungi sales',
      'url' => '#whatsapp',
    ),
  ),
  'seo' => 
  array (
    'meta_title' => 'Customer Engagement Solutions by Industry & Use Case | Kouventa',
    'meta_description' => 'Deliver automated marketing campaigns, rapid support resolution, and transactional messaging with Kouventa omnichannel solutions.',
    'keywords' => 'customer engagement solutions, WhatsApp marketing, omnichannel support, automated notifications',
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
      'prefix' => 'Seamless omnichannel',
      'main' => 'customer experience.',
    ),
    'subtitle' => 'From marketing campaigns and customer support to operational updates, Kouventa helps businesses connect, respond, and communicate more effectively.',
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
  'roles' => 
  array (
    'sales_marketing' => 
    array (
      'id' => 'sales_marketing',
      'badge' => 'S&M',
      'name' => 'Sales & Marketing',
      'challenges' => 
      array (
        0 => 
        array (
          'text' => 'Limited campaign reach and personalization',
        ),
        1 => 
        array (
          'text' => 'Risk of losing leads due to slow responses',
        ),
        2 => 
        array (
          'text' => 'Customer conversations scattered across multiple channels',
        ),
        3 => 
        array (
          'text' => 'Customers need a fast and secure way to verify their identity without adding friction to the customer experience',
        ),
      ),
      'solutions' => 
      array (
        0 => 
        array (
          'title' => 'WhatsApp Broadcast',
          'desc' => 'Send promotional campaigns, product launches, and seasonal offers to customers at scale.',
        ),
        1 => 
        array (
          'title' => 'Chatbot',
          'desc' => 'Answer product and service inquiries with predefined or AI chatbots, or escalate to a human agent for more complex requests.',
        ),
        2 => 
        array (
          'title' => 'Omnichannel',
          'desc' => 'Manage customer conversations and respond to comments from WhatsApp, Instagram, Facebook, and Live Chat in one platform.',
        ),
        3 => 
        array (
          'title' => 'Authentication',
          'desc' => 'Secure customer logins and transactions with fast, seamless WhatsApp OTP verification.',
        ),
        4 => 
        array (
          'title' => 'WhatsApp Flows',
          'desc' => 'Simplify customer data collection for purchases, loyalty program registration, membership sign-ups, and more.',
        ),
      ),
      'impacts' => 
      array (
        0 => 
        array (
          'title' => 'Reach more customers, more effectively',
          'desc' => '',
        ),
        1 => 
        array (
          'title' => 'Minimize lost leads and enhance customer experience',
          'desc' => '',
        ),
        2 => 
        array (
          'title' => 'More efficient sales and marketing operations',
          'desc' => '',
        ),
        3 => 
        array (
          'title' => 'Improve customer engagement across channels',
          'desc' => '',
        ),
      ),
    ),
    'customer_service' => 
    array (
      'id' => 'customer_service',
      'badge' => 'CS',
      'name' => 'Customer Service',
      'challenges' => 
      array (
        0 => 
        array (
          'text' => 'Customers expect their complaints to be handled quickly, while customer service teams manage multiple issues and requests at the same time',
        ),
        1 => 
        array (
          'text' => 'Customer service teams spend significant time handling repetitive questions, limiting their ability to focus on issues that require human assistance',
        ),
        2 => 
        array (
          'text' => 'Manually creating and recording support tickets can lead to human errors, incomplete information, and miscommunication during issue handling',
        ),
      ),
      'solutions' => 
      array (
        0 => 
        array (
          'title' => 'Omnichannel',
          'desc' => 'Centralize customer complaints from WhatsApp, Instagram, Facebook, and Live Chat in one platform, making it easier to track and respond to every issue.',
        ),
        1 => 
        array (
          'title' => 'Chatbot',
          'desc' => 'Handle common customer complaints instantly with predefined or AI-powered chatbots, while escalating complex issues to human agents when needed.',
        ),
        2 => 
        array (
          'title' => 'Ticket Creation',
          'desc' => 'Create support tickets from customer complaints, reducing manual work and minimizing errors or miscommunication during issue handling.',
        ),
        3 => 
        array (
          'title' => 'CSAT Survey',
          'desc' => 'Automatically collect customer feedback after complaint resolution to measure satisfaction and identify opportunities to improve service quality.',
        ),
      ),
      'impacts' => 
      array (
        0 => 
        array (
          'title' => 'Faster Complaint Resolution',
          'desc' => 'Instant responses and seamless escalation to human agents for more complex issues.',
        ),
        1 => 
        array (
          'title' => 'More Efficient Customer Service Operations',
          'desc' => 'Automate repetitive inquiries and reduce manual ticket creation, so teams can focus on more complex issues.',
        ),
        2 => 
        array (
          'title' => 'Improved Customer Experience',
          'desc' => 'More consistent, responsive support and customer feedback to continuously improve the service experience.',
        ),
      ),
    ),
    'operations' => 
    array (
      'id' => 'operations',
      'badge' => 'OPS',
      'name' => 'Operations',
      'challenges' => 
      array (
        0 => 
        array (
          'text' => 'Sending order, delivery, payment, and appointment updates manually can be time-consuming and may lead to delays, errors, or missed customer notifications',
        ),
        1 => 
        array (
          'text' => 'As transaction volumes grow, manually sending recurring notifications becomes harder to manage consistently and on time',
        ),
      ),
      'solutions' => 
      array (
        0 => 
        array (
          'title' => 'Cross Platform Integrations',
          'desc' => 'Trigger WhatsApp messages from business systems based on operational events, such as order confirmations, delivery updates, payment reminders, and appointment notifications.',
        ),
      ),
      'impacts' => 
      array (
        0 => 
        array (
          'title' => 'Keep Customers Informed at Every Step',
          'desc' => 'Timely notifications triggered by business events.',
        ),
        1 => 
        array (
          'title' => 'Create More Consistent Customer Experiences',
          'desc' => 'Clear, standardized updates across every process.',
        ),
      ),
    ),
  ),
  'cta' => 
  array (
    'title' => 'Ready to improve every customer conversation?',
    'subtitle' => 'Tell us about your team\'s needs and we\'ll show you how Kouventa fits.',
    'primary_btn' => 
    array (
      'text' => 'Request a Demo',
      'url' => '/book-demo',
    ),
    'secondary_btn' => 
    array (
      'text' => 'Talk to Sales',
      'url' => '#whatsapp',
    ),
  ),
  'seo' => 
  array (
    'meta_title' => 'Customer Engagement Solutions by Industry & Use Case | Kouventa',
    'meta_description' => 'Deliver automated marketing campaigns, rapid support resolution, and transactional messaging with Kouventa omnichannel solutions.',
    'keywords' => 'customer engagement solutions, WhatsApp marketing, omnichannel support, automated notifications',
    'canonical_url' => '',
  ),
);
    }
}
