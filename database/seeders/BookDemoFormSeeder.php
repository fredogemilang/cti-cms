<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\FormField;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class BookDemoFormSeeder extends Seeder
{
    public function run(): void
    {
        $form = Form::where('slug', 'book-demo')->first() ?: Form::find(1);

        if (!$form) {
            $form = new Form;
            $form->name = 'Book a Demo';
            $form->slug = 'book-demo';
            $form->description = 'Formulir pendaftaran konsultasi enterprise pada halaman Book a Demo';
            $form->submit_button_text = 'Submit';
            $form->is_active = true;
            $form->form_type = 'standard';
            $form->save();
        }

        // Ensure theme assignment is set
        $assignments = setting('theme_kouventa_form_assignments', []);
        $assignments['book_demo_form'] = $form->id;
        Setting::set('theme_kouventa_form_assignments', $assignments, 'theme', 'array');

        $jobRoleOptions = [
            ['value' => 'Developer/DBA/System Analysis', 'label' => 'Developer/DBA/System Analysis'],
            ['value' => 'Finance', 'label' => 'Finance'],
            ['value' => 'HR', 'label' => 'HR'],
            ['value' => 'IT/MIS', 'label' => 'IT/MIS'],
            ['value' => 'Legal', 'label' => 'Legal'],
            ['value' => 'Logistic/Purchasing', 'label' => 'Logistic/Purchasing'],
            ['value' => 'Management Operational', 'label' => 'Management Operational'],
            ['value' => 'Media', 'label' => 'Media'],
            ['value' => 'Pre-Sales', 'label' => 'Pre-Sales'],
            ['value' => 'Sales/Frontliner', 'label' => 'Sales/Frontliner'],
            ['value' => 'System Engineer', 'label' => 'System Engineer'],
            ['value' => 'User', 'label' => 'User'],
            ['value' => 'Other', 'label' => 'Other'],
        ];

        $positionOptions = [
            ['value' => 'President Director', 'label' => 'President Director'],
            ['value' => 'C-Level', 'label' => 'C-Level'],
            ['value' => 'General Manager', 'label' => 'General Manager'],
            ['value' => 'Manager', 'label' => 'Manager'],
            ['value' => 'Supervisor/Team Leader', 'label' => 'Supervisor/Team Leader'],
            ['value' => 'Staff', 'label' => 'Staff'],
        ];

        // Ensure standard fields and widths
        $emailField = $form->fields()->where('field_id', 'email')->first();
        if ($emailField) {
            $emailField->update(['column_width' => '6', 'order' => 3]);
        }

        $companyField = $form->fields()->where('field_id', 'company')->first();
        if ($companyField) {
            $companyField->update(['column_width' => '6', 'order' => 4]);
        }

        // 1. Update or create Job Role field (matching WhatsApp form)
        $roleField = $form->fields()->whereIn('field_id', ['job_role', 'role'])->first();
        if ($roleField) {
            $roleField->update([
                'field_id' => 'job_role',
                'label' => 'Job Role',
                'type' => 'select',
                'placeholder' => 'Choose Job Role',
                'options' => $jobRoleOptions,
                'order' => 5,
                'column_width' => '6',
                'is_required' => true,
            ]);
        } else {
            $form->fields()->create([
                'field_id' => 'job_role',
                'label' => 'Job Role',
                'type' => 'select',
                'placeholder' => 'Choose Job Role',
                'options' => $jobRoleOptions,
                'order' => 5,
                'column_width' => '6',
                'is_required' => true,
            ]);
        }

        // 2. Update or create Position field (matching WhatsApp form)
        $positionField = $form->fields()->where('field_id', 'position')->first();
        if ($positionField) {
            $positionField->update([
                'field_id' => 'position',
                'label' => 'Position',
                'type' => 'select',
                'placeholder' => 'Choose Your Position',
                'options' => $positionOptions,
                'order' => 6,
                'column_width' => '6',
                'is_required' => true,
            ]);
        } else {
            $form->fields()->create([
                'field_id' => 'position',
                'label' => 'Position',
                'type' => 'select',
                'placeholder' => 'Choose Your Position',
                'options' => $positionOptions,
                'order' => 6,
                'column_width' => '6',
                'is_required' => true,
            ]);
        }
    }
}
