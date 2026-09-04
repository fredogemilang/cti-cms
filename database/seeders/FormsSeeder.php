<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\FormField;
use Illuminate\Database\Seeder;

class FormsSeeder extends Seeder
{
    public function run(): void
    {
        // Form: Digital Solution Guide Download (ID: 1)
        $form = Form::where('id', 1)->orWhere('slug', 'digital-solution-guide')->first() ?: new Form;
        $form->id = 1;
        $form->slug = 'digital-solution-guide';
        $form->name = 'Digital Solution Guide Download';
        $form->description = 'Updated via Form Studio Workspace';
        $form->submit_button_text = 'Download Guide';
        $form->is_active = true;
        $form->form_type = 'standard';
        $form->save();

        $form->setTranslation('name', 'en', 'Digital Solution Guide Download');
        $form->save();

        $fieldsData = [
            [
                'field_id' => 'name',
                'label' => 'Name',
                'type' => 'text',
                'options' => null,
                'validation' => null,
                'order' => 0,
                'is_required' => true,
                'placeholder' => null,
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'full',
                'translations' => '{"id":{"label":"Nama Lengkap"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'job_title',
                'label' => 'Job Title',
                'type' => 'text',
                'options' => null,
                'validation' => null,
                'order' => 1,
                'is_required' => true,
                'placeholder' => null,
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'full',
                'translations' => '{"id":{"label":"Jabatan","placeholder":"Masukkan jabatan Anda"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'corporate_email',
                'label' => 'Corporate Email',
                'type' => 'email',
                'options' => null,
                'validation' => null,
                'order' => 2,
                'is_required' => true,
                'placeholder' => null,
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'full',
                'translations' => '{"id":{"label":"Email Perusahaan","placeholder":"Masukkan email perusahaan Anda"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'company_name',
                'label' => 'Company Name',
                'type' => 'text',
                'options' => null,
                'validation' => null,
                'order' => 3,
                'is_required' => true,
                'placeholder' => null,
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'full',
                'translations' => '{"id":{"label":"Nama Perusahaan","placeholder":"Masukkan nama perusahaan Anda"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'phone_number',
                'label' => 'Phone Number',
                'type' => 'tel',
                'options' => null,
                'validation' => null,
                'order' => 4,
                'is_required' => true,
                'placeholder' => null,
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'full',
                'translations' => '{"id":{"label":"Nomor Telepon","placeholder":"Masukkan nomor telepon Anda"}}',
                'is_hidden' => false,
            ],
        ];

        foreach ($fieldsData as $fData) {
            FormField::updateOrCreate(
                ['form_id' => $form->id, 'field_id' => $fData['field_id']],
                [
                    'label' => $fData['label'],
                    'type' => $fData['type'],
                    'options' => $fData['options'] ? json_decode($fData['options'], true) : null,
                    'validation' => $fData['validation'] ? json_decode($fData['validation'], true) : null,
                    'order' => $fData['order'],
                    'is_required' => $fData['is_required'],
                    'placeholder' => $fData['placeholder'],
                    'help_text' => $fData['help_text'],
                    'default_value' => $fData['default_value'],
                    'conditional_logic' => $fData['conditional_logic'] ? json_decode($fData['conditional_logic'], true) : null,
                    'column_width' => $fData['column_width'],
                    'translations' => $fData['translations'] ? json_decode($fData['translations'], true) : null,
                    'is_hidden' => $fData['is_hidden'],
                ]
            );
        }

        // Form: Get In Touch (ID: 2)
        $form = Form::where('id', 2)->orWhere('slug', 'get-in-touch')->first() ?: new Form;
        $form->id = 2;
        $form->slug = 'get-in-touch';
        $form->name = 'Get In Touch';
        $form->description = 'Have some Question? Reach out to Central Data Technology';
        $form->submit_button_text = 'Submit';
        $form->is_active = true;
        $form->form_type = 'standard';
        $form->save();

        $form->setTranslation('name', 'en', 'Get In Touch');
        $form->save();

        $fieldsData = [
            [
                'field_id' => 'name',
                'label' => 'Name',
                'type' => 'text',
                'options' => null,
                'validation' => null,
                'order' => 0,
                'is_required' => true,
                'placeholder' => 'John Doe',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'half',
                'translations' => '{"id":{"label":"Nama Lengkap"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'company_name',
                'label' => 'Company Name',
                'type' => 'text',
                'options' => null,
                'validation' => null,
                'order' => 1,
                'is_required' => true,
                'placeholder' => 'PT Central Data Technology',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'half',
                'translations' => '{"id":{"label":"Nama Perusahaan","placeholder":"Masukkan nama perusahaan Anda"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'job_title',
                'label' => 'Job Title',
                'type' => 'text',
                'options' => null,
                'validation' => null,
                'order' => 2,
                'is_required' => true,
                'placeholder' => 'Software Engineer',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'half',
                'translations' => '{"id":{"label":"Jabatan","placeholder":"Masukkan jabatan Anda"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'phone_number',
                'label' => 'Phone Number',
                'type' => 'tel',
                'options' => null,
                'validation' => null,
                'order' => 3,
                'is_required' => true,
                'placeholder' => '08123456789',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'half',
                'translations' => '{"id":{"label":"Nomor Telepon","placeholder":"Masukkan nomor telepon Anda"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'corporate_email',
                'label' => 'Corporate Email',
                'type' => 'email',
                'options' => null,
                'validation' => null,
                'order' => 4,
                'is_required' => true,
                'placeholder' => 'mail@corporate.com',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'half',
                'translations' => '{"id":{"label":"Email Perusahaan","placeholder":"Masukkan email perusahaan Anda"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'solution_needed',
                'label' => 'Solution Needed',
                'type' => 'select',
                'options' => null,
                'validation' => null,
                'order' => 5,
                'is_required' => true,
                'placeholder' => 'Please Select One',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'half',
                'translations' => '{"id":{"label":"Solusi yang Dibutuhkan","placeholder":"Silakan Pilih"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'message',
                'label' => 'Message',
                'type' => 'textarea',
                'options' => null,
                'validation' => null,
                'order' => 6,
                'is_required' => false,
                'placeholder' => null,
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'full',
                'translations' => '{"id":{"label":"Pesan \\/ Pertanyaan"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'field_hiv6k8',
                'label' => 'Privacy Consent',
                'type' => 'gdpr',
                'options' => null,
                'validation' => null,
                'order' => 7,
                'is_required' => true,
                'placeholder' => null,
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'full',
                'translations' => '{"id":{"label":"Persetujuan Privasi","consent_text":"<p>Dengan mencentang kotak ini, saya setuju bahwa informasi pribadi saya akan diberikan kepada Central Data Technology (CDT)<\\/p><br\\/><p>Dengan mengisi data pribadi Anda pada kolom di atas, PT Central Data Technology dan afiliasinya akan mengumpulkan dan memroses data tersebut. Untuk mengetahui lebih lanjut tentang kebijakan privasi kami, silahkan kunjungi: <a href=\\"#\\" class=\\"text-primary hover:underline font-semibold\\">Kebijakan Privasi<\\/a> PT Central Data Technology.<\\/p>"}}',
                'is_hidden' => false,
            ],
        ];

        foreach ($fieldsData as $fData) {
            FormField::updateOrCreate(
                ['form_id' => $form->id, 'field_id' => $fData['field_id']],
                [
                    'label' => $fData['label'],
                    'type' => $fData['type'],
                    'options' => $fData['options'] ? json_decode($fData['options'], true) : null,
                    'validation' => $fData['validation'] ? json_decode($fData['validation'], true) : null,
                    'order' => $fData['order'],
                    'is_required' => $fData['is_required'],
                    'placeholder' => $fData['placeholder'],
                    'help_text' => $fData['help_text'],
                    'default_value' => $fData['default_value'],
                    'conditional_logic' => $fData['conditional_logic'] ? json_decode($fData['conditional_logic'], true) : null,
                    'column_width' => $fData['column_width'],
                    'translations' => $fData['translations'] ? json_decode($fData['translations'], true) : null,
                    'is_hidden' => $fData['is_hidden'],
                ]
            );
        }

        // Form: Technology Alliance - Product Inquiry (ID: 4)
        $form = Form::where('id', 4)->orWhere('slug', 'alliance-product-inquiry')->first() ?: new Form;
        $form->id = 4;
        $form->slug = 'alliance-product-inquiry';
        $form->name = 'Technology Alliance - Product Inquiry';
        $form->description = null;
        $form->submit_button_text = 'Send Message';
        $form->is_active = true;
        $form->form_type = 'standard';
        $form->save();

        $form->setTranslation('name', 'en', 'Technology Alliance - Product Inquiry');
        $form->save();

        $fieldsData = [
            [
                'field_id' => 'full_name',
                'label' => 'Full Name',
                'type' => 'text',
                'options' => null,
                'validation' => null,
                'order' => 0,
                'is_required' => true,
                'placeholder' => 'Enter your full name',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'full',
                'translations' => '{"id":{"label":"Nama Lengkap","placeholder":"Masukkan nama lengkap Anda"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'company_name',
                'label' => 'Company Name',
                'type' => 'text',
                'options' => null,
                'validation' => null,
                'order' => 1,
                'is_required' => true,
                'placeholder' => 'Enter your company name',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'half',
                'translations' => '{"id":{"label":"Nama Perusahaan","placeholder":"Masukkan nama perusahaan Anda"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'job_title',
                'label' => 'Job Title',
                'type' => 'text',
                'options' => null,
                'validation' => null,
                'order' => 2,
                'is_required' => true,
                'placeholder' => 'Enter your job title',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'half',
                'translations' => '{"id":{"label":"Jabatan","placeholder":"Masukkan jabatan Anda"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'phone_number',
                'label' => 'Phone Number',
                'type' => 'tel',
                'options' => null,
                'validation' => null,
                'order' => 3,
                'is_required' => true,
                'placeholder' => 'Enter your phone number',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'half',
                'translations' => '{"id":{"label":"Nomor Telepon","placeholder":"Masukkan nomor telepon Anda"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'corporate_email',
                'label' => 'Corporate Email',
                'type' => 'email',
                'options' => null,
                'validation' => null,
                'order' => 4,
                'is_required' => true,
                'placeholder' => 'Enter your corporate email',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'half',
                'translations' => '{"id":{"label":"Email Perusahaan","placeholder":"Masukkan email perusahaan Anda"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'solution_needed',
                'label' => 'Solution Needed',
                'type' => 'vendor_solutions',
                'options' => null,
                'validation' => null,
                'order' => 5,
                'is_required' => true,
                'placeholder' => 'Please select a solution',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'full',
                'translations' => '{"id":{"label":"Solusi yang Dibutuhkan","placeholder":"Silakan Pilih"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'privacy_consent',
                'label' => 'Privacy Consent',
                'type' => 'gdpr',
                'options' => null,
                'validation' => null,
                'order' => 6,
                'is_required' => true,
                'placeholder' => null,
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'full',
                'translations' => '{"id":{"label":"Persetujuan Privasi","consent_text":"<p>Dengan mencentang kotak ini, saya setuju bahwa informasi pribadi saya akan diberikan kepada Central Data Technology (CDT)<\\/p><br\\/><p>Dengan mengisi data pribadi Anda pada kolom di atas, PT Central Data Technology dan afiliasinya akan mengumpulkan dan memroses data tersebut. Untuk mengetahui lebih lanjut tentang kebijakan privasi kami, silahkan kunjungi: <a href=\\"#\\" class=\\"text-primary hover:underline font-semibold\\">Kebijakan Privasi<\\/a> PT Central Data Technology.<\\/p>"}}',
                'is_hidden' => false,
            ],
        ];

        foreach ($fieldsData as $fData) {
            FormField::updateOrCreate(
                ['form_id' => $form->id, 'field_id' => $fData['field_id']],
                [
                    'label' => $fData['label'],
                    'type' => $fData['type'],
                    'options' => $fData['options'] ? json_decode($fData['options'], true) : null,
                    'validation' => $fData['validation'] ? json_decode($fData['validation'], true) : null,
                    'order' => $fData['order'],
                    'is_required' => $fData['is_required'],
                    'placeholder' => $fData['placeholder'],
                    'help_text' => $fData['help_text'],
                    'default_value' => $fData['default_value'],
                    'conditional_logic' => $fData['conditional_logic'] ? json_decode($fData['conditional_logic'], true) : null,
                    'column_width' => $fData['column_width'],
                    'translations' => $fData['translations'] ? json_decode($fData['translations'], true) : null,
                    'is_hidden' => $fData['is_hidden'],
                ]
            );
        }

        // Form: Job Application Form (ID: 5)
        $form = Form::where('id', 5)->orWhere('slug', 'job-application-form')->first() ?: new Form;
        $form->id = 5;
        $form->slug = 'job-application-form';
        $form->name = 'Job Application Form';
        $form->description = 'Candidate vacancy application form';
        $form->submit_button_text = 'Submit';
        $form->is_active = true;
        $form->form_type = 'standard';
        $form->save();

        $form->setTranslation('name', 'en', 'Job Application Form');
        $form->save();

        $fieldsData = [
            [
                'field_id' => 'full_name',
                'label' => 'Full Name',
                'type' => 'text',
                'options' => null,
                'validation' => null,
                'order' => 0,
                'is_required' => true,
                'placeholder' => 'e.g. John Doe',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'half',
                'translations' => '{"id":{"label":"Nama Lengkap"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'email_address',
                'label' => 'Email Address',
                'type' => 'email',
                'options' => null,
                'validation' => null,
                'order' => 1,
                'is_required' => true,
                'placeholder' => 'johndoe@email.com',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'half',
                'translations' => '{"id":{"label":"Alamat Email"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'phone_number',
                'label' => 'Phone Number',
                'type' => 'tel',
                'options' => null,
                'validation' => null,
                'order' => 2,
                'is_required' => true,
                'placeholder' => '+62 812-3456-7890',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'half',
                'translations' => '{"id":{"label":"Nomor Telepon"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'preferred_job_position',
                'label' => 'Preferred Job Position',
                'type' => 'text',
                'options' => null,
                'validation' => null,
                'order' => 3,
                'is_required' => true,
                'placeholder' => 'e.g. Solution Architect',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'half',
                'translations' => '{"id":{"label":"Posisi Pekerjaan yang Dilamar"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'linkedin_url',
                'label' => 'LinkedIn Profile URL (Optional - Will Be Prioritized)',
                'type' => 'url',
                'options' => null,
                'validation' => null,
                'order' => 4,
                'is_required' => false,
                'placeholder' => 'https://linkedin.com/in/username',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'full',
                'translations' => '{"id":{"label":"URL Profil LinkedIn (Opsional - Diutamakan)"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'privacy_consent',
                'label' => 'By ticking this box, I agree that my personal information will be given to Central Data Technology (CDT)',
                'type' => 'gdpr',
                'options' => null,
                'validation' => null,
                'order' => 5,
                'is_required' => true,
                'placeholder' => null,
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'full',
                'translations' => '{"id":{"label":"Dengan mencentang kotak ini, saya menyetujui bahwa informasi pribadi saya akan diberikan kepada Central Data Technology (CDT)","consent_text":"<p>Dengan mengisi data pribadi Anda pada kolom di atas, <strong>PT Central Data Technology<\\/strong> dan afiliasinya akan mengumpulkan dan memroses data tersebut. Untuk mengetahui lebih lanjut tentang kebijakan privasi kami, silahkan kunjungi: <strong><a style=\\"color:#E2231A\\" href=\\"https:\\/\\/www.centraldatatech.com\\/id\\/privacy-policy\\/\\">Kebijakan Privasi<\\/a> PT Central Data Technology. <\\/strong><\\/p>"}}',
                'is_hidden' => false,
            ],
        ];

        foreach ($fieldsData as $fData) {
            FormField::updateOrCreate(
                ['form_id' => $form->id, 'field_id' => $fData['field_id']],
                [
                    'label' => $fData['label'],
                    'type' => $fData['type'],
                    'options' => $fData['options'] ? json_decode($fData['options'], true) : null,
                    'validation' => $fData['validation'] ? json_decode($fData['validation'], true) : null,
                    'order' => $fData['order'],
                    'is_required' => $fData['is_required'],
                    'placeholder' => $fData['placeholder'],
                    'help_text' => $fData['help_text'],
                    'default_value' => $fData['default_value'],
                    'conditional_logic' => $fData['conditional_logic'] ? json_decode($fData['conditional_logic'], true) : null,
                    'column_width' => $fData['column_width'],
                    'translations' => $fData['translations'] ? json_decode($fData['translations'], true) : null,
                    'is_hidden' => $fData['is_hidden'],
                ]
            );
        }

        // Form: Newsletter Subscription (ID: 6)
        $form = Form::where('id', 6)->orWhere('slug', 'newsletter-subscription')->first() ?: new Form;
        $form->id = 6;
        $form->slug = 'newsletter-subscription';
        $form->name = 'Newsletter Subscription';
        $form->description = 'Newsletter subscription form for footer popup modal.';
        $form->submit_button_text = 'Subscribe Now';
        $form->is_active = true;
        $form->form_type = 'standard';
        $form->save();

        $form->setTranslation('name', 'en', 'Newsletter Subscription');
        $form->save();

        $fieldsData = [
            [
                'field_id' => 'full_name',
                'label' => 'Full Name',
                'type' => 'text',
                'options' => null,
                'validation' => null,
                'order' => 0,
                'is_required' => true,
                'placeholder' => 'John Doe',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'full',
                'translations' => '{"id":{"label":"Nama Lengkap"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'email',
                'label' => 'Email Address',
                'type' => 'email',
                'options' => null,
                'validation' => null,
                'order' => 1,
                'is_required' => true,
                'placeholder' => 'john@example.com',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'full',
                'translations' => '{"id":{"label":"Alamat Email"}}',
                'is_hidden' => false,
            ],
        ];

        foreach ($fieldsData as $fData) {
            FormField::updateOrCreate(
                ['form_id' => $form->id, 'field_id' => $fData['field_id']],
                [
                    'label' => $fData['label'],
                    'type' => $fData['type'],
                    'options' => $fData['options'] ? json_decode($fData['options'], true) : null,
                    'validation' => $fData['validation'] ? json_decode($fData['validation'], true) : null,
                    'order' => $fData['order'],
                    'is_required' => $fData['is_required'],
                    'placeholder' => $fData['placeholder'],
                    'help_text' => $fData['help_text'],
                    'default_value' => $fData['default_value'],
                    'conditional_logic' => $fData['conditional_logic'] ? json_decode($fData['conditional_logic'], true) : null,
                    'column_width' => $fData['column_width'],
                    'translations' => $fData['translations'] ? json_decode($fData['translations'], true) : null,
                    'is_hidden' => $fData['is_hidden'],
                ]
            );
        }

        // Form: Start Your Free Credit Assessment (ID: 7)
        $form = Form::where('id', 7)->orWhere('slug', 'aws_credits_form')->first() ?: new Form;
        $form->id = 7;
        $form->slug = 'aws_credits_form';
        $form->name = 'Start Your Free Credit Assessment';
        $form->description = 'Our AWS specialist will contact you within 1 business day.';
        $form->submit_button_text = 'Send';
        $form->is_active = true;
        $form->form_type = 'standard';
        $form->save();

        $form->setTranslation('name', 'en', 'Start Your Free Credit Assessment');
        $form->setTranslation('name', 'id', 'Mulai Asesmen Kredit Gratis');
        $form->save();

        $fieldsData = [
            [
                'field_id' => 'first_name',
                'label' => 'First Name',
                'type' => 'text',
                'options' => null,
                'validation' => null,
                'order' => 0,
                'is_required' => true,
                'placeholder' => 'First Name',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'half',
                'translations' => '{"id":{"label":"Nama Depan","placeholder":"Masukkan nama depan"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'last_name',
                'label' => 'Last Name',
                'type' => 'text',
                'options' => null,
                'validation' => null,
                'order' => 1,
                'is_required' => true,
                'placeholder' => 'Last Name',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'half',
                'translations' => '{"id":{"label":"Nama Belakang","placeholder":"Masukkan nama belakang"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'company_name',
                'label' => 'Company Name',
                'type' => 'text',
                'options' => null,
                'validation' => null,
                'order' => 2,
                'is_required' => true,
                'placeholder' => 'Company Name',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'half',
                'translations' => '{"id":{"label":"Nama Perusahaan","placeholder":"Nama perusahaan Anda"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'job_title',
                'label' => 'Job Title',
                'type' => 'text',
                'options' => null,
                'validation' => null,
                'order' => 3,
                'is_required' => true,
                'placeholder' => 'Job Title',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'half',
                'translations' => '{"id":{"label":"Jabatan","placeholder":"Jabatan Anda"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'corporate_email',
                'label' => 'Corporate Email',
                'type' => 'email',
                'options' => null,
                'validation' => null,
                'order' => 4,
                'is_required' => true,
                'placeholder' => 'Corporate Email',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'full',
                'translations' => '{"id":{"label":"Email Bisnis","placeholder":"nama@perusahaan.com"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'phone_number',
                'label' => 'Phone Number',
                'type' => 'tel',
                'options' => null,
                'validation' => null,
                'order' => 5,
                'is_required' => true,
                'placeholder' => 'Phone Number',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'half',
                'translations' => '{"id":{"label":"Nomor Telepon \\/ Whatsapp","placeholder":"0812xxxxxxx"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'industry',
                'label' => 'Industry',
                'type' => 'text',
                'options' => null,
                'validation' => null,
                'order' => 6,
                'is_required' => true,
                'placeholder' => 'Industry',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'half',
                'translations' => '{"id":{"label":"Industri","placeholder":"Industri Perusahaan Anda"}}',
                'is_hidden' => false,
            ],
            [
                'field_id' => 'aws_status',
                'label' => 'AWS Status',
                'type' => 'text',
                'options' => null,
                'validation' => null,
                'order' => 7,
                'is_required' => false,
                'placeholder' => 'AWS Status (Existing / New Customer)',
                'help_text' => null,
                'default_value' => null,
                'conditional_logic' => null,
                'column_width' => 'full',
                'translations' => '{"id":{"label":"Status Penggunaan AWS","placeholder":"Pelanggan Lama \\/ Pelanggan Baru"}}',
                'is_hidden' => false,
            ],
        ];

        foreach ($fieldsData as $fData) {
            FormField::updateOrCreate(
                ['form_id' => $form->id, 'field_id' => $fData['field_id']],
                [
                    'label' => $fData['label'],
                    'type' => $fData['type'],
                    'options' => $fData['options'] ? json_decode($fData['options'], true) : null,
                    'validation' => $fData['validation'] ? json_decode($fData['validation'], true) : null,
                    'order' => $fData['order'],
                    'is_required' => $fData['is_required'],
                    'placeholder' => $fData['placeholder'],
                    'help_text' => $fData['help_text'],
                    'default_value' => $fData['default_value'],
                    'conditional_logic' => $fData['conditional_logic'] ? json_decode($fData['conditional_logic'], true) : null,
                    'column_width' => $fData['column_width'],
                    'translations' => $fData['translations'] ? json_decode($fData['translations'], true) : null,
                    'is_hidden' => $fData['is_hidden'],
                ]
            );
        }

    }
}
