<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class WhatsAppFormSeeder extends Seeder
{
    public function run(): void
    {
        $form = Form::where('slug', 'whatsapp-chat')->first() ?: new Form;
        $form->name = 'WhatsApp Chat';
        $form->slug = 'whatsapp-chat';
        $form->description = 'Start a Conversation popup form loaded from backend';
        $form->submit_button_text = 'Send';
        $form->is_active = true;
        $form->form_type = 'standard';
        $form->confirmations = [
            'type' => 'message',
            'message' => 'Thank you! Redirecting you to WhatsApp...',
        ];
        $form->save();

        // Delete existing fields to re-seed cleanly
        $form->fields()->delete();

        $fields = [
            [
                'field_id' => 'name',
                'label' => 'Name',
                'type' => 'text',
                'placeholder' => 'John Doe',
                'order' => 0,
                'is_required' => true,
                'column_width' => 'full',
            ],
            [
                'field_id' => 'working_email',
                'label' => 'Working Email',
                'type' => 'email',
                'placeholder' => 'john.doe@corporate.com',
                'order' => 1,
                'is_required' => true,
                'column_width' => 'half',
            ],
            [
                'field_id' => 'company',
                'label' => 'Company',
                'type' => 'text',
                'placeholder' => 'PT. Corporate',
                'order' => 2,
                'is_required' => true,
                'column_width' => 'half',
            ],
            [
                'field_id' => 'job_role',
                'label' => 'Job Role',
                'type' => 'select',
                'placeholder' => 'Choose Job Role',
                'options' => [
                    ['value' => 'Director / C-Level', 'label' => 'Director / C-Level'],
                    ['value' => 'VP / Head of Department', 'label' => 'VP / Head of Department'],
                    ['value' => 'Manager / Team Lead', 'label' => 'Manager / Team Lead'],
                    ['value' => 'Specialist / Senior Staff', 'label' => 'Specialist / Senior Staff'],
                    ['value' => 'Staff / Officer', 'label' => 'Staff / Officer'],
                    ['value' => 'Other', 'label' => 'Other'],
                ],
                'order' => 3,
                'is_required' => true,
                'column_width' => 'half',
            ],
            [
                'field_id' => 'position',
                'label' => 'Position',
                'type' => 'select',
                'placeholder' => 'Choose Your Position',
                'options' => [
                    ['value' => 'IT / Engineering', 'label' => 'IT / Engineering'],
                    ['value' => 'Customer Service / Support', 'label' => 'Customer Service / Support'],
                    ['value' => 'Sales & Marketing', 'label' => 'Sales & Marketing'],
                    ['value' => 'Operations', 'label' => 'Operations'],
                    ['value' => 'Finance & Accounting', 'label' => 'Finance & Accounting'],
                    ['value' => 'Human Resources', 'label' => 'Human Resources'],
                    ['value' => 'Other', 'label' => 'Other'],
                ],
                'order' => 4,
                'is_required' => true,
                'column_width' => 'half',
            ],
            [
                'field_id' => 'consent',
                'label' => 'Privacy Consent',
                'type' => 'gdpr',
                'order' => 5,
                'is_required' => true,
                'column_width' => 'full',
                'advanced_settings' => [
                    'consent_text' => 'By submitting your personal data in the required fields above, PT Helios Informatika Nusantara and its affiliates collect and proceed with such information. To learn more about our privacy practices, please refer to PT Helios Informatika Nusantara\'s Privacy Policy.',
                ],
            ],
        ];

        foreach ($fields as $fieldData) {
            $form->fields()->create($fieldData);
        }

        // Set theme form assignment
        $currentAssignments = setting('theme_kouventa_form_assignments', []);
        $currentAssignments['whatsapp_chat_form'] = $form->id;
        Setting::set('theme_kouventa_form_assignments', $currentAssignments, 'theme', 'array');
    }
}
