<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Database\Seeder;

class AwsCloudCreditsPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::updateOrCreate(
            ['slug' => 'amazon-web-services-cloud-credits'],
            [
                'title' => 'Get Up to 6 Months Free AWS Cloud Credits',
                'template' => 'aws-cloud-credits',
                'status' => 'published',
            ]
        );

        $page->setTranslation('title', 'en', 'Get Up to 6 Months Free AWS Cloud Credits');
        $page->setTranslation('title', 'id', 'Amazon Web Services Cloud Credits');
        $page->save();

        $blocksData = [
            [
                'name' => 'hero_title',
                'type' => 'title',
                'label' => '',
                'order' => 0,
                'en' => '{"prefix":"Get Up to","main":"6 Months of Free AWS Cloud Credits"}',
                'id' => '{"prefix":"Dapatkan Hingga","main":"6 Bulan Kredit AWS Cloud Gratis"}',
            ],
            [
                'name' => 'hero_subtitle',
                'type' => 'textarea',
                'label' => '',
                'order' => 1,
                'en' => 'Accelerate your AI and cloud transformation with AWS credits managed by Central Data Technology, an official AWS Consulting Partner. No migration required. Open to all industries.',
                'id' => 'Akselerasi transformasi AI dan cloud Anda dengan kredit AWS yang dikelola oleh Central Data Technology, Partner Konsultan Resmi AWS. Tanpa perlu migrasi. Terbuka untuk semua industri.',
            ],
            [
                'name' => 'hero_features',
                'type' => 'repeater',
                'label' => '',
                'order' => 4,
                'en' => '[{"text":"Up to 6 months credit","icon":"lucide:check-circle-2"},{"text":"AI projects (Quick & Bedrock)","icon":"lucide:sparkles"},{"text":"All Industries","icon":"lucide:building-2"},{"text":"Min. MRR $2K","icon":"lucide:badge-dollar-sign"}]',
                'id' => '[{"text":"Kredit hingga 6 bulan","icon":"lucide:check-circle-2"},{"text":"Proyek AI (Quick & Bedrock)","icon":"lucide:sparkles"},{"text":"Semua Industri","icon":"lucide:building-2"},{"text":"MRR Min. $2K","icon":"lucide:badge-dollar-sign"}]',
            ],
            [
                'name' => 'zero_cost_heading',
                'type' => 'text',
                'label' => '',
                'order' => 5,
                'en' => 'Up to 6 Months Free on AWS',
                'id' => 'Gratis hingga 6 Bulan - Tanpa Biaya di Muka',
            ],
            [
                'name' => 'zero_cost_text',
                'type' => 'textarea',
                'label' => '',
                'order' => 6,
                'en' => 'Use AWS credits to run workloads, AI experiments, and migrations. Credit amount is based on your assessment and project scope. Available to new and existing AWS customers.',
                'id' => 'Pakai AWS credits untuk jalankan workload, eksperimen Al, atau migrasi. Jumlah kredit ditentukan lewat asesmen. Buat pelanggan AWS lama maupun yang baru mulai.',
            ],
            [
                'name' => 'zero_cost_stats',
                'type' => 'repeater',
                'label' => '',
                'order' => 7,
                'en' => '[{"stat":"6","icon":"lucide:sparkles","description":"Months for AI projects (Amazon Quick & Bedrock)"},{"stat":"3","icon":"lucide:cloud","description":"Months for all AWS services"},{"stat":"$2K","icon":"lucide:badge-dollar-sign","description":"Min. monthly recurring revenue"}]',
                'id' => '[{"stat":"6","icon":"lucide:sparkles","description":"Bulan untuk proyek AI (Amazon Quick & Bedrock)"},{"stat":"3","icon":"lucide:cloud","description":"Bulan untuk semua layanan AWS"},{"stat":"$2K","icon":"lucide:badge-dollar-sign","description":"Minimum pendapatan bulanan berulang"}]',
            ],
            [
                'name' => 'what_you_get_header',
                'type' => 'title',
                'label' => '',
                'order' => 8,
                'en' => '{"prefix":"","main":"What You Get"}',
                'id' => '{"prefix":"","main":"Manfaat yang Kamu Dapatkan"}',
            ],
            [
                'name' => 'what_you_get_desc',
                'type' => 'textarea',
                'label' => '',
                'order' => 9,
                'en' => 'Reduce financial risk while you explore, build, and scale on AWS.',
                'id' => 'Kurangi risiko finansial saat mulai membangun dan scale workload di AWS.',
            ],
            [
                'name' => 'what_you_get_cards',
                'type' => 'repeater',
                'label' => '',
                'order' => 10,
                'en' => '[{"title":"AWS Credit Scheme","asset_type":"icon","icon":"lucide:coins","description_type":"text","description":"Credit limit set through assessment - no fixed cap. Usable during or after project kickoff."},{"title":"Up To 6 Months Free for AI Projects","asset_type":"icon","icon":"lucide:bot","description_type":"text","description":"Amazon Quick and Amazon Bedrock projects qualify for up to 6 months of credits."},{"title":"Up To 3 Months Free for All AWS Services","asset_type":"icon","icon":"lucide:server","description_type":"text","description":"Any AWS workload - compute, storage, databases, networking - qualifies for the 3-month window."},{"title":"POC Included","asset_type":"icon","icon":"lucide:lightbulb","description_type":"text","description":"Proof of Concept is allowed within AI project scope. Test your idea before going to full production."}]',
                'id' => '[{"title":"Skema Kredit AWS","asset_type":"icon","icon":"lucide:coins","description_type":"text","description":"Jumlah kredit ditentukan lewat asesmen - nggak ada batas maksimum. Bisa dipakai selama atau setelah proyek mulai."},{"title":"6 bulan untuk proyek AI","asset_type":"icon","icon":"lucide:bot","description_type":"text","description":"Proyek Amazon QuickSuite dan Amazon Bedrock dapat kredit hingga 6 bulan."},{"title":"3 bulan untuk semua Layanan AWS","asset_type":"icon","icon":"lucide:server","description_type":"text","description":"Semua workload AWS - compute, storage, database, jaringan - masuk periode kredit 3 bulan."},{"title":"POC diizinkan","asset_type":"icon","icon":"lucide:lightbulb","description_type":"text","description":"Boleh jalankan Proof of Concept dalam lingkup proyek Al sebelum lanjut ke produksi penuh."}]',
            ],
            [
                'name' => 'qualifies_ai_header',
                'type' => 'title',
                'label' => '',
                'order' => 11,
                'en' => '{"prefix":"","main":"What Qualifies as an AI Project?"}',
                'id' => '{"prefix":"","main":"Apa yang Termasuk Proyek AI?"}',
            ],
            [
                'name' => 'qualifies_ai_desc',
                'type' => 'textarea',
                'label' => '',
                'order' => 12,
                'en' => 'The 6-month credit window is reserved for AI initiatives using these services.',
                'id' => 'Kredit 6 bulan hanya untuk inisiatif Al menggunakan layanan berikut.',
            ],
            [
                'name' => 'qualifies_ai_cards',
                'type' => 'repeater',
                'label' => '',
                'order' => 13,
                'en' => '[{"title":"Amazon Quick","asset_type":"icon","icon":"lucide:line-chart","description_type":"text","description":"AI-powered business intelligence - dashboards, reports, and intelligent data visualisations."},{"title":"Amazon Bedrock","asset_type":"icon","icon":"lucide:brain-circuit","description_type":"text","description":"Foundation model access for generative AI. Build and deploy large language model use cases."},{"title":"Custom Use Case","asset_type":"icon","icon":"lucide:sliders","description_type":"text","description":"Specific use cases are scoped collaboratively during the assessment session."},{"title":"POC Projects","asset_type":"icon","icon":"lucide:lightbulb","description_type":"text","description":"Run a structured POC to validate your AI idea \\u2014 all within the credit period."}]',
                'id' => '[{"title":"Amazon Quick","asset_type":"icon","icon":"lucide:line-chart","description_type":"text","description":"Business intelligence berbasis AI - dasbor, laporan, dan visualisasi data cerdas."},{"title":"Amazon Bedrock","asset_type":"icon","icon":"lucide:brain-circuit","description_type":"text","description":"Akses model fondasi untuk AI generatif. Bangun dan terapkan penggunaan model bahasa besar."},{"title":"Studi Kasus Kustom","asset_type":"icon","icon":"lucide:sliders","description_type":"text","description":"Studi kasus spesifik ditentukan secara kolaboratif selama sesi asesmen."},{"title":"Proyek POC","asset_type":"icon","icon":"lucide:lightbulb","description_type":"text","description":"Jalankan POC terstruktur untuk memvalidasi ide AI Anda \\u2014 semua dalam periode kredit."}]',
            ],
            [
                'name' => 'support_workload_header',
                'type' => 'title',
                'label' => '',
                'order' => 14,
                'en' => '{"prefix":"SUPPORTED WORKLOADS","main":"Support Workload"}',
                'id' => '{"prefix":"","main":"Workload Yang Didukung"}',
            ],
            [
                'name' => 'support_workload_desc',
                'type' => 'textarea',
                'label' => '',
                'order' => 15,
                'en' => 'Credits applicable to these AWS service categories.',
                'id' => 'Kredit berlaku untuk kategori layanan AWS berikut.',
            ],
            [
                'name' => 'eligibility_header',
                'type' => 'title',
                'label' => '',
                'order' => 16,
                'en' => '{"prefix":"ELIGIBILITY","main":"Who Can Apply?"}',
                'id' => '{"prefix":"","main":"Siapa yang Bisa Daftar?"}',
            ],
            [
                'name' => 'eligibility_cards',
                'type' => 'repeater',
                'label' => '',
                'order' => 17,
                'en' => '[{"title":"ALL INDUSTRIES","asset_type":"icon","icon":"lucide:building-2","description_type":"listing","list_icon":"lucide:check-circle-2","list_items":"Finance, retail, manufacturing\\nHealthcare, education, government\\nLogistics, e-commerce, tech startups\\nAny industry with cloud or AI needs"},{"title":"MINIMUM REQUIREMENTS","asset_type":"icon","icon":"lucide:check-square","description_type":"listing","list_icon":"lucide:check-circle-2","list_items":"Minimum MRR of $2,000 USD\\nOpen to existing AWS customers\\nOpen to new AWS customers too\\nNo migration required to join"},{"title":"EXISTING AWS CUSTOMERS","asset_type":"icon","icon":"lucide:cloud-cog","description_type":"listing","list_icon":"lucide:check-circle-2","list_items":"Already running workloads on AWS\\nCan expand into AI or new services\\nCredits apply to active workloads"},{"title":"NEW TO AWS","asset_type":"icon","icon":"lucide:rocket","description_type":"listing","list_icon":"lucide:check-circle-2","list_items":"No prior AWS account needed\\nStart directly \\u2014 zero migration required\\nCDT assists with onboarding and setup"}]',
                'id' => '[{"title":"ALL INDUSTRIES","asset_type":"icon","icon":"lucide:building-2","description_type":"listing","list_icon":"lucide:check-circle-2","list_items":"Keuangan, ritel, manufaktur\\nKesehatan, pendidikan, pemerintah\\nLogistik, e-commerce, startup teknologi\\nIndustri apa pun dengan kebutuhan cloud atau AI"},{"title":"MINIMUM REQUIREMENTS","asset_type":"icon","icon":"lucide:check-square","description_type":"listing","list_icon":"lucide:check-circle-2","list_items":"MRR minimum sebesar $2,000 USD\\nTerbuka untuk pelanggan AWS yang lama\\nTerbuka juga untuk pelanggan AWS baru\\nTidak perlu migrasi untuk bergabung"},{"title":"EXISTING AWS CUSTOMERS","asset_type":"icon","icon":"lucide:cloud-cog","description_type":"listing","list_icon":"lucide:check-circle-2","list_items":"Sudah menjalankan beban kerja di AWS\\nDapat memperluas ke AI atau layanan baru\\nKredit berlaku untuk beban kerja aktif"},{"title":"NEW TO AWS","asset_type":"icon","icon":"lucide:rocket","description_type":"listing","list_icon":"lucide:check-circle-2","list_items":"Tidak perlu akun AWS sebelumnya\\nMulai langsung \\u2014 tanpa migrasi\\nCDT membantu aktivasi dan konfigurasi"}]',
            ],
            [
                'name' => 'program_terms_header',
                'type' => 'title',
                'label' => '',
                'order' => 18,
                'en' => '{"prefix":"TRANSPARENCY","main":"Program Terms"}',
                'id' => '{"prefix":"","main":"Ketentuan Program"}',
            ],
            [
                'name' => 'program_terms_card',
                'type' => 'card',
                'label' => '',
                'order' => 19,
                'en' => '{"title":"AWS CDT CLOUD CREDIT PROGRAM - KEY TERMS","description":"","asset_type":"icon","image":"","icon":"lucide:file-text","description_type":"listing","list_icon":"lucide:info","list_items":"Credit allocation is determined through the assessment process and is not guaranteed prior to scoping.\\nThe 6-month credit period applies exclusively to AI projects using Amazon Quick and\\/or Amazon Bedrock, subject to assessment approval.\\nThe 3-month credit period applies to all other qualifying AWS services.\\nCredits must be used within the approved project or workload scope. Unused credits are non-transferable.\\nMinimum MRR of $2,000 USD is required to qualify.\\nProgram is limited to 10 AWS accounts per month, served on a first-come, first-served basis.\\nCentral Data Technology reserves the right to modify or terminate the program terms at any time.","wysiwyg_content":"","button_text":"","button_url":"#","button_target":"_self"}',
                'id' => '{"title":"PROGRAM KREDIT CLOUD AWS CDT - KETENTUAN UTAMA","description":"","asset_type":"icon","image":"","icon":"lucide:file-text","description_type":"listing","list_icon":"lucide:info","list_items":"Jumlah kredit ditentukan lewat proses asesmen dan nggak dijamin sebelum sesi scoping selesai.\\nKredit 6 bulan hanya untuk proyek Al yang pakai Amazon Quick dan\\/atau Amazon Bedrock, tergantung hasil asesmen.\\nKredit 3 bulan berlaku untuk semua layanan AWS lain yang memenuhi syarat.\\nKredit hanya bisa dipakai dalam lingkup proyek yang sudah disetujui. Sisa kredit nggak bisa dipindahtangankan.\\nMinimum MRR $2.000 USD wajib dipenuhi untuk ikut program.\\nProgram dibatasi 10 use case Al dan 10 use case migrasi per siklus, berdasarkan urutan pendaftaran.\\nSubmit form nggak otomatis menjamin pendaftaran atau persetujuan kredit.\\nKredit bisa diaktifkan selama proyek berjalan atau langsung setelah kickoff.\\nWorkload POC hanya berlaku dalam lingkup proyek Al.\\nCDT berhak mengubah atau menghentikan program dengan pemberitahuan sebelumnya. Ketentuan standar AWS juga berlaku.\\nSemua peserta wajib mematuhi AWS Acceptable Use Policy selama periode kredit.","wysiwyg_content":"","button_text":"","button_url":"#","button_target":"_self"}',
            ],
        ];

        foreach ($blocksData as $item) {
            $block = PageBlock::where('page_id', $page->id)->where('name', $item['name'])->whereNull('parent_block_id')->first() ?: new PageBlock();
            $block->page_id = $page->id;
            $block->name = $item['name'];
            $block->type = $item['type'];
            $block->label = $item['label'];
            $block->order = $item['order'];
            $block->is_active = true;
            $block->value = $item['en'];
            $block->setTranslation('value', 'en', $item['en']);
            $block->setTranslation('value', 'id', $item['id']);
            $block->save();
        }
    }
}
