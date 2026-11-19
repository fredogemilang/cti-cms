<?php

namespace Plugins\RmaXyora\Support;

class RmaStatus
{
    public const PENDING = 'pending';

    public const VERIFIKASI_RMA = 'verifikasi_rma';

    public const MENUNGGU_PENGIRIMAN_UNIT = 'menunggu_pengiriman_unit';

    public const PROSES_DIAGNOSTIK = 'proses_diagnostik';

    public const PROSES_PERSIAPAN_UNIT = 'proses_persiapan_unit';

    public const PROSES_PENGUJIAN_UNIT = 'proses_pengujian_unit';

    public const PROSES_SERVICE_SELESAI = 'proses_service_selesai';

    public const PENGIRIMAN_UNIT_XYORA = 'pengiriman_unit_xyora';

    public const COMPLETED = 'completed';

    public const REJECTED = 'rejected';

    public const PROCESSING = 'processing'; // Legacy support

    /**
     * Get all active statuses with full metadata.
     */
    public static function all(): array
    {
        return [
            self::PENDING => [
                'step' => 1,
                'number' => '1.',
                'label' => 'Menunggu verifikasi',
                'short_label' => 'Menunggu Verifikasi',
                'dropdown' => '1. Menunggu verifikasi',
                'badge' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                'color' => '#d97706',
                'bg_rgba' => 'rgba(217, 119, 6, 0.15)',
                'border_rgba' => 'rgba(217, 119, 6, 0.4)',
            ],
            self::VERIFIKASI_RMA => [
                'step' => 2,
                'number' => '2.',
                'label' => 'Verifikasi RMA (Disetujui/ditolak)',
                'short_label' => 'Verifikasi RMA',
                'dropdown' => '2. Verifikasi RMA (Disetujui/ditolak)',
                'badge' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
                'color' => '#2563eb',
                'bg_rgba' => 'rgba(37, 99, 235, 0.15)',
                'border_rgba' => 'rgba(37, 99, 235, 0.4)',
            ],
            self::MENUNGGU_PENGIRIMAN_UNIT => [
                'step' => 3,
                'number' => '3.',
                'label' => 'Menunggu pengiriman unit oleh customer',
                'short_label' => 'Kirim Unit Customer',
                'dropdown' => '3. Menunggu pengiriman unit oleh customer',
                'badge' => 'bg-orange-500/10 text-orange-600 dark:text-orange-400 border-orange-500/20',
                'color' => '#ea580c',
                'bg_rgba' => 'rgba(234, 88, 12, 0.15)',
                'border_rgba' => 'rgba(234, 88, 12, 0.4)',
            ],
            self::PROSES_DIAGNOSTIK => [
                'step' => 4,
                'number' => '4.',
                'label' => 'Proses service - Diagnostik Unit',
                'short_label' => 'Diagnostik Unit',
                'dropdown' => '4. Proses service - Diagnostik Unit',
                'badge' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
                'color' => '#9333ea',
                'bg_rgba' => 'rgba(147, 51, 234, 0.15)',
                'border_rgba' => 'rgba(147, 51, 234, 0.4)',
            ],
            self::PROSES_PERSIAPAN_UNIT => [
                'step' => 5,
                'number' => '5.',
                'label' => 'Proses service - Persiapan Unit Pengganti',
                'short_label' => 'Persiapan Unit',
                'dropdown' => '5. Proses service - Persiapan Unit Pengganti',
                'badge' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
                'color' => '#4f46e5',
                'bg_rgba' => 'rgba(79, 70, 229, 0.15)',
                'border_rgba' => 'rgba(79, 70, 229, 0.4)',
            ],
            self::PROSES_PENGUJIAN_UNIT => [
                'step' => 6,
                'number' => '6.',
                'label' => 'Proses service - Pengujian unit pengganti',
                'short_label' => 'Pengujian Unit',
                'dropdown' => '6. Proses service - Pengujian unit pengganti',
                'badge' => 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border-cyan-500/20',
                'color' => '#0891b2',
                'bg_rgba' => 'rgba(8, 145, 178, 0.15)',
                'border_rgba' => 'rgba(8, 145, 178, 0.4)',
            ],
            self::PROSES_SERVICE_SELESAI => [
                'step' => 7,
                'number' => '7.',
                'label' => 'Proses service selesai',
                'short_label' => 'Service Selesai',
                'dropdown' => '7. Proses service selesai',
                'badge' => 'bg-teal-500/10 text-teal-600 dark:text-teal-400 border-teal-500/20',
                'color' => '#0d9488',
                'bg_rgba' => 'rgba(13, 148, 136, 0.15)',
                'border_rgba' => 'rgba(13, 148, 136, 0.4)',
            ],
            self::PENGIRIMAN_UNIT_XYORA => [
                'step' => 8,
                'number' => '8.',
                'label' => 'Pengiriman unit pengganti oleh Xyora',
                'short_label' => 'Pengiriman Xyora',
                'dropdown' => '8. Pengiriman unit pengganti oleh Xyora',
                'badge' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20',
                'color' => '#0284c7',
                'bg_rgba' => 'rgba(2, 132, 199, 0.15)',
                'border_rgba' => 'rgba(2, 132, 199, 0.4)',
            ],
            self::COMPLETED => [
                'step' => 9,
                'number' => '9.',
                'label' => 'RMA Selesai (unit diterima customer)',
                'short_label' => 'RMA Selesai',
                'dropdown' => '9. RMA Selesai (unit diterima customer)',
                'badge' => 'bg-green-500/10 text-green-600 dark:text-green-400 border-green-500/20',
                'color' => '#16a34a',
                'bg_rgba' => 'rgba(22, 163, 74, 0.15)',
                'border_rgba' => 'rgba(22, 163, 74, 0.4)',
            ],
            self::REJECTED => [
                'step' => null,
                'number' => '',
                'label' => 'Ditolak (Rejected)',
                'short_label' => 'Ditolak',
                'dropdown' => 'Ditolak (Rejected)',
                'badge' => 'bg-red-500/10 text-red-600 dark:text-red-400 border-red-500/20',
                'color' => '#dc2626',
                'bg_rgba' => 'rgba(220, 38, 38, 0.15)',
                'border_rgba' => 'rgba(220, 38, 38, 0.4)',
            ],
        ];
    }

    /**
     * Map a status string to its standard metadata.
     */
    public static function get(?string $status): array
    {
        $all = static::all();

        // Handle legacy alias
        if ($status === self::PROCESSING) {
            return [
                'step' => null,
                'number' => '',
                'label' => 'Sedang Diproses',
                'short_label' => 'Diproses',
                'dropdown' => 'Sedang Diproses (Processing)',
                'badge' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
                'color' => '#4f46e5',
                'bg_rgba' => 'rgba(79, 70, 229, 0.15)',
                'border_rgba' => 'rgba(79, 70, 229, 0.4)',
            ];
        }

        if ($status && isset($all[$status])) {
            return $all[$status];
        }

        // Default fallback
        $label = ucfirst(str_replace('_', ' ', (string) $status));

        return [
            'step' => null,
            'number' => '',
            'label' => $label ?: 'Menunggu verifikasi',
            'short_label' => $label ?: 'Menunggu Verifikasi',
            'dropdown' => $label ?: 'Menunggu verifikasi',
            'badge' => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20',
            'color' => '#475569',
            'bg_rgba' => 'rgba(71, 85, 105, 0.15)',
            'border_rgba' => 'rgba(71, 85, 105, 0.4)',
        ];
    }

    /**
     * Get label for display.
     */
    public static function getLabel(?string $status): string
    {
        return static::get($status)['label'];
    }

    /**
     * Get badge class.
     */
    public static function getBadgeClass(?string $status): string
    {
        return static::get($status)['badge'];
    }

    /**
     * Get hex color code.
     */
    public static function getColor(?string $status): string
    {
        return static::get($status)['color'];
    }
}
