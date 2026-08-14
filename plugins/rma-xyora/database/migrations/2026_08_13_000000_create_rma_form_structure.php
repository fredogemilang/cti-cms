<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Form;
use App\Models\FormField;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $rmaForm = Form::updateOrCreate(
            ['slug' => 'rma-form'],
            [
                'name' => 'RMA Request Form',
                'description' => 'Xyora RMA Form',
                'is_active' => true,
                'form_type' => 'standard',
                'submit_button_text' => 'Kirim',
                'spam_protection' => [
                    'captcha_provider' => 'recaptcha_v2',
                    'honeypot' => true,
                ]
            ]
        );

        $rmaFields = [
            ['field_id' => 'nama_lengkap', 'type' => 'text', 'label' => 'Nama Lengkap', 'is_required' => true, 'order' => 0],
            ['field_id' => 'alamat_email', 'type' => 'email', 'label' => 'Alamat Email', 'is_required' => true, 'order' => 1],
            ['field_id' => 'serial_number_produk', 'type' => 'text', 'label' => 'Serial Number Produk', 'is_required' => true, 'order' => 2],
            ['field_id' => 'nama_produk', 'type' => 'text', 'label' => 'Nama Produk', 'is_required' => true, 'order' => 3],
            ['field_id' => 'alasan_pengajuan_rma', 'type' => 'text', 'label' => 'Alasan Pengajuan RMA', 'is_required' => true, 'order' => 4],
            ['field_id' => 'jenis_pengajuan', 'type' => 'text', 'label' => 'Jenis Pengajuan', 'is_required' => true, 'order' => 5],
            ['field_id' => 'jumlah_unit', 'type' => 'text', 'label' => 'Jumlah Unit', 'is_required' => true, 'order' => 6],
            ['field_id' => 'tanggal_pembelian', 'type' => 'text', 'label' => 'Tanggal Pembelian', 'is_required' => true, 'order' => 7],
            ['field_id' => 'bukti_pembelian', 'type' => 'text', 'label' => 'Link Dokumen Bukti Pembelian & Kondisi Unit', 'is_required' => true, 'order' => 8]
        ];

        foreach ($rmaFields as $f) {
            FormField::updateOrCreate(
                ['form_id' => $rmaForm->id, 'field_id' => $f['field_id']],
                [
                    'type' => $f['type'],
                    'label' => $f['label'],
                    'is_required' => $f['is_required'],
                    'order' => $f['order'],
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $form = Form::where('slug', 'rma-form')->first();
        if ($form) {
            $form->update(['is_active' => false]);
        }
    }
};
