<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\SeoMeta;
use App\Models\User;
use Illuminate\Database\Seeder;

class PageReplatformSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Running PageReplatformSeeder...');
        $authorId = User::first()?->id ?? 1;

        // Helper closure to ensure a wysiwyg block exists for a page
        $createContentBlock = function (Page $page, string $value, ?array $translations = null) {
            PageBlock::updateOrCreate(
                [
                    'page_id' => $page->id,
                    'name' => 'content',
                ],
                [
                    'type' => 'wysiwyg',
                    'label' => 'Main Content',
                    'value' => $value,
                    'translations' => $translations,
                    'order' => 0,
                    'is_active' => true,
                ]
            );
        };

        // 1. /thank-you-submission/ (EN)
        $thankSubmission = Page::updateOrCreate(
            ['slug' => 'thank-you-submission'],
            [
                'title' => 'Thank You for Your Submission',
                'status' => 'published',
                'is_system' => true,
                'author_id' => $authorId,
                'template' => 'default',
            ]
        );
        $createContentBlock($thankSubmission, '<div class="py-12 text-center max-w-2xl mx-auto">
            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-zinc-900 mb-4">Submission Received!</h2>
            <p class="text-zinc-600 text-base md:text-lg mb-8">Thank you for getting in touch with Central Data Technology. Our specialized consultants have received your details and will contact you shortly.</p>
            <a href="' . url('/') . '" class="inline-flex items-center justify-center px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider bg-primary hover:bg-red-700 text-white transition-all shadow-md">Back to Home</a>
        </div>');
        SeoMeta::updateOrCreate(
            ['seoable_type' => Page::class, 'seoable_id' => $thankSubmission->id, 'locale' => ''],
            [
                'title' => 'Thank You for Your Submission - Central Data Technology',
                'description' => 'Thank you for reaching out to Central Data Technology. Your message has been received.',
                'robots' => 'noindex,follow',
            ]
        );

        // 2. /thank-you-for-subscribing/ (EN)
        $thankSubscribe = Page::updateOrCreate(
            ['slug' => 'thank-you-for-subscribing'],
            [
                'title' => 'Thank You for Subscribing',
                'status' => 'published',
                'is_system' => true,
                'author_id' => $authorId,
                'template' => 'default',
            ]
        );
        $createContentBlock($thankSubscribe, '<div class="py-12 text-center max-w-2xl mx-auto">
            <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-zinc-900 mb-4">Subscription Confirmed!</h2>
            <p class="text-zinc-600 text-base md:text-lg mb-8">Thank you for subscribing to Central Data Technology. You will now receive regular updates on enterprise cloud computing, cybersecurity trends, and digital transformation guides.</p>
            <a href="' . url('/blog-news/') . '" class="inline-flex items-center justify-center px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider bg-primary hover:bg-red-700 text-white transition-all shadow-md">Explore Tech Insights</a>
        </div>');
        SeoMeta::updateOrCreate(
            ['seoable_type' => Page::class, 'seoable_id' => $thankSubscribe->id, 'locale' => ''],
            [
                'title' => 'Thank You for Subscribing - Central Data Technology',
                'description' => 'Subscription confirmed. Thank you for following Central Data Technology insights.',
                'robots' => 'noindex,follow',
            ]
        );

        // 3. /id/terima-kasih/ (ID)
        $terimaKasih = Page::updateOrCreate(
            ['slug' => 'terima-kasih'],
            [
                'title' => 'Terima Kasih atas Pengajuan Anda',
                'status' => 'published',
                'is_system' => true,
                'author_id' => $authorId,
                'template' => 'default',
                'translations' => [
                    'id' => [
                        'title' => 'Terima Kasih atas Pengajuan Anda',
                        'slug' => 'terima-kasih',
                    ],
                ],
            ]
        );
        $createContentBlock($terimaKasih, '<div class="py-12 text-center max-w-2xl mx-auto">
            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-zinc-900 mb-4">Pengajuan Berhasil Dikirim!</h2>
            <p class="text-zinc-600 text-base md:text-lg mb-8">Terima kasih telah menghubungi Central Data Technology. Tim konsultan IT kami telah menerima formulir Anda dan akan segera menghubungi Anda dalam waktu 1x24 jam kerja.</p>
            <a href="' . url('/id/') . '" class="inline-flex items-center justify-center px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider bg-primary hover:bg-red-700 text-white transition-all shadow-md">Kembali ke Beranda</a>
        </div>');
        SeoMeta::updateOrCreate(
            ['seoable_type' => Page::class, 'seoable_id' => $terimaKasih->id, 'locale' => 'id'],
            [
                'title' => 'Terima Kasih atas Pengajuan Anda - Central Data Technology',
                'description' => 'Terima kasih telah menghubungi Central Data Technology. Pesan Anda telah kami terima.',
                'robots' => 'noindex,follow',
            ]
        );

        // 4. /whatsapp-privacy-policy/ (EN & ID)
        $waPolicy = Page::updateOrCreate(
            ['slug' => 'whatsapp-privacy-policy'],
            [
                'title' => 'WhatsApp Communication Privacy Policy',
                'status' => 'published',
                'is_system' => true,
                'author_id' => $authorId,
                'template' => 'default',
                'translations' => [
                    'id' => [
                        'title' => 'Kebijakan Privasi Komunikasi WhatsApp',
                        'slug' => 'whatsapp-privacy-policy',
                    ],
                ],
            ]
        );
        $createContentBlock($waPolicy, '<div class="max-w-4xl mx-auto space-y-6 text-zinc-700 leading-relaxed">
            <p class="text-lg text-zinc-900 font-medium">This Privacy Policy governs the collection, processing, and protection of personal data exchanged during communications between users and Central Data Technology (CDT) via WhatsApp Business messaging channels in accordance with Indonesian Personal Data Protection Law (UU No. 27/2022).</p>
            <h3 class="text-xl font-bold text-zinc-900 mt-6">1. Information We Collect</h3>
            <p>When you initiate or consent to communication with CDT via WhatsApp, we may collect your name, phone number, company affiliation, job title, email address, and any technical inquiry details you choose to disclose.</p>
            <h3 class="text-xl font-bold text-zinc-900 mt-6">2. Purpose of Processing</h3>
            <p>We use this information solely to respond to product inquiries, schedule enterprise consultations, deliver technical support updates, and share requested whitepapers or product brochures.</p>
            <h3 class="text-xl font-bold text-zinc-900 mt-6">3. Data Security & Retention</h3>
            <p>Your data is processed strictly by authorized CDT personnel and stored within secure CRM systems with role-based access control. We never sell or transfer your contact information to third parties.</p>
            <h3 class="text-xl font-bold text-zinc-900 mt-6">4. Contact & Opt-Out</h3>
            <p>You may opt out of WhatsApp communications at any time by replying "STOP" or contacting our Data Protection Officer at <a href="mailto:marketing@centraldatatech.com" class="text-primary underline">marketing@centraldatatech.com</a>.</p>
        </div>');
        SeoMeta::updateOrCreate(
            ['seoable_type' => Page::class, 'seoable_id' => $waPolicy->id, 'locale' => ''],
            [
                'title' => 'WhatsApp Communication Privacy Policy - Central Data Technology',
                'description' => 'Privacy policy regarding official WhatsApp communications with Central Data Technology.',
            ]
        );
        SeoMeta::updateOrCreate(
            ['seoable_type' => Page::class, 'seoable_id' => $waPolicy->id, 'locale' => 'id'],
            [
                'title' => 'Kebijakan Privasi Komunikasi WhatsApp - Central Data Technology',
                'description' => 'Kebijakan privasi interaksi dan komunikasi resmi WhatsApp Central Data Technology.',
            ]
        );

        // 5. /knowledgetedy/ (EN & ID) — AI-friendly semantic catalog
        $ktedy = Page::updateOrCreate(
            ['slug' => 'knowledgetedy'],
            [
                'title' => 'Knowledge Tedy — Enterprise Solution Matrix',
                'status' => 'published',
                'is_system' => true,
                'author_id' => $authorId,
                'template' => 'knowledgetedy',
                'translations' => [
                    'id' => [
                        'title' => 'Knowledge Tedy — Matriks Solusi Enterprise',
                        'slug' => 'knowledgetedy',
                    ],
                ],
            ]
        );
        SeoMeta::updateOrCreate(
            ['seoable_type' => Page::class, 'seoable_id' => $ktedy->id, 'locale' => ''],
            [
                'title' => 'Enterprise Technology Matrix & Solution Catalog - Central Data Technology',
                'description' => 'Comprehensive enterprise IT solutions catalog, alliance portfolio, and competitor comparison matrix by Central Data Technology.',
                'robots' => 'index,follow',
            ]
        );
        SeoMeta::updateOrCreate(
            ['seoable_type' => Page::class, 'seoable_id' => $ktedy->id, 'locale' => 'id'],
            [
                'title' => 'Katalog Solusi & Matriks Teknologi Enterprise - Central Data Technology',
                'description' => 'Katalog lengkap portofolio solusi IT enterprise, kapabilitas aliansi teknologi, dan matriks komparasi kompetitor oleh Central Data Technology.',
                'robots' => 'index,follow',
            ]
        );

        $this->command->info('PageReplatformSeeder completed successfully.');
    }
}
