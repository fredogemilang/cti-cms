<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\StringTranslationKey;
use App\Models\StringTranslation;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update PageBlocks for Product AI (page_id = 2)
        $prodAiPage = Page::where('slug', 'product/ai')->first();
        if ($prodAiPage) {
            $blocks = [
                'products_title' => [
                    'value' => 'The products that put you in control.',
                    'id' => 'Produk yang membuat Anda tetap memegang kendali.'
                ],
                'bottom_cta_title' => [
                    'value' => 'Bring all your AI into one platform.',
                    'id' => 'Satu platform untuk semua kebutuhan AI organisasi Anda.'
                ],
                'bottom_cta_subtitle' => [
                    'value' => 'Book a demo to see how Kouventa connects your company knowledge, automates routine tasks, and keeps you in control.',
                    'id' => 'Jadwalkan demo untuk melihat bagaimana Kouventa menghubungkan pengetahuan perusahaan Anda, mengotomasi tugas rutin, dan menjaga Anda tetap memegang kendali.'
                ]
            ];

            foreach ($blocks as $name => $data) {
                $block = PageBlock::where('page_id', $prodAiPage->id)->where('name', $name)->first();
                if ($block) {
                    $block->value = $data['value'];
                    $trans = $block->translations ?? [];
                    $trans['id'] = ['value' => $data['id']];
                    $block->translations = $trans;
                    $block->save();
                } else {
                    PageBlock::create([
                        'page_id' => $prodAiPage->id,
                        'name' => $name,
                        'type' => 'text',
                        'label' => ucwords(str_replace('_', ' ', $name)),
                        'value' => $data['value'],
                        'translations' => ['id' => ['value' => $data['id']]],
                        'is_active' => true,
                        'order' => 10
                    ]);
                }
            }
        }

        // 2. Update PageBlocks for Solution CE (page_id = 5)
        $solCePage = Page::where('slug', 'solutions/customer-engagement')->first();
        if ($solCePage) {
            $ceBlocks = [
                'bottom_cta_title' => [
                    'value' => 'Ready to improve every customer conversation?',
                    'id' => 'Siap meningkatkan setiap percakapan dengan pelanggan?'
                ],
                'bottom_cta_subtitle' => [
                    'value' => "Book a demo and we'll walk you through how Kouventa connects your channels, automates replies, and keeps your team in control.",
                    'id' => 'Jadwalkan demo dan kami tunjukkan bagaimana Kouventa menghubungkan channel Anda, mengotomasi balasan, dan menjaga tim Anda tetap memegang kendali.'
                ]
            ];

            foreach ($ceBlocks as $name => $data) {
                $block = PageBlock::where('page_id', $solCePage->id)->where('name', $name)->first();
                if ($block) {
                    $block->value = $data['value'];
                    $trans = $block->translations ?? [];
                    $trans['id'] = ['value' => $data['id']];
                    $block->translations = $trans;
                    $block->save();
                } else {
                    PageBlock::create([
                        'page_id' => $solCePage->id,
                        'name' => $name,
                        'type' => 'text',
                        'label' => ucwords(str_replace('_', ' ', $name)),
                        'value' => $data['value'],
                        'translations' => ['id' => ['value' => $data['id']]],
                        'is_active' => true,
                        'order' => 10
                    ]);
                }
            }
        }

        // 3. Update String Translations
        $stringTranslations = [
            'Data masking' => 'Masking data',
            'Role-based access' => 'Akses per peran',
            'RECRUITING AGENT · DONE' => 'RECRUITING AGENT · SELESAI',
            'RECRUITMENT AGENT · DONE' => 'RECRUITING AGENT · SELESAI',
            'An AI chatbot that understands the context of your questions and delivers fast, accurate answers in seconds, drawing from your company knowledge, websites, databases, and other connected data sources.' => 'Chatbot berbasis AI yang memahami konteks pertanyaan Anda dan memberi jawaban cepat dan akurat dalam hitungan detik, bersumber dari pengetahuan perusahaan, website, database, dan sumber data lain yang terhubung.',
            'Guardrails & PII' => 'Guardrails & PII',
            'Usage & budget' => 'Pemakaian & budget',
            'Approved · logged' => 'Disetujui · tercatat'
        ];

        foreach ($stringTranslations as $en => $id) {
            $key = StringTranslationKey::firstOrCreate(
                ['group' => 'kouventa', 'key' => $en],
                ['created_at' => now(), 'updated_at' => now()]
            );

            StringTranslation::updateOrCreate(
                ['translation_key_id' => $key->id, 'locale' => 'id'],
                ['value' => $id, 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
    }
};
