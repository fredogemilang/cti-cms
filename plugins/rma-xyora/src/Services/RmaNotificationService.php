<?php

namespace Plugins\RmaXyora\Services;

use App\Models\FormEntry;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class RmaNotificationService
{
    /**
     * Send email notification to user after RMA submission.
     */
    public function sendRmaCreatedNotification(FormEntry $entry): void
    {
        try {
            $data = $entry->data ?? [];
            $userEmail = $data['alamat_email'] ?? null;

            if (empty($userEmail) || !filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                Log::warning("Cannot send RMA created notification: invalid email address.", ['entry_id' => $entry->id]);
                return;
            }

            $rmaNumber = sprintf('RMA-%04d', $entry->id);
            $subject = "[XYORA] Pengajuan RMA Berhasil Diterima - #{$rmaNumber}";

            $html = $this->buildEmailTemplate(
                "Pengajuan RMA Diterima",
                "Halo, <strong>" . e($data['nama_lengkap'] ?? 'Pelanggan') . "</strong>.<br><br>Terima kasih telah mengajukan proses RMA (Return Merchandise Authorization) produk Xyora. Pengajuan Anda telah berhasil kami terima dan sedang berada dalam antrean verifikasi.",
                $entry,
                $data
            );

            Mail::html($html, function ($mail) use ($userEmail, $subject) {
                $mail->to($userEmail)
                     ->subject($subject);
            });

            Log::info("RMA created notification email sent successfully.", ['entry_id' => $entry->id, 'email' => $userEmail]);
        } catch (\Exception $e) {
            Log::error("Failed to send RMA created notification: " . $e->getMessage(), ['entry_id' => $entry->id]);
        }
    }

    /**
     * Send email notification to user after RMA status is updated.
     */
    public function sendRmaStatusUpdatedNotification(FormEntry $entry): void
    {
        try {
            $data = $entry->data ?? [];
            $userEmail = $data['alamat_email'] ?? null;

            if (empty($userEmail) || !filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                Log::warning("Cannot send RMA status update notification: invalid email address.", ['entry_id' => $entry->id]);
                return;
            }

            $rmaNumber = sprintf('RMA-%04d', $entry->id);
            $statusText = $this->getStatusLabel($entry->status);
            $subject = "[XYORA] Update Status Pengajuan RMA #{$rmaNumber} - {$statusText}";

            $html = $this->buildEmailTemplate(
                "Update Status Pengajuan RMA",
                "Halo, <strong>" . e($data['nama_lengkap'] ?? 'Pelanggan') . "</strong>.<br><br>Kami ingin menginformasikan bahwa status pengajuan RMA Anda dengan nomor <strong>#{$rmaNumber}</strong> telah diupdate menjadi: <strong style='color: " . $this->getStatusColor($entry->status) . ";'>" . e($statusText) . "</strong>.",
                $entry,
                $data
            );

            Mail::html($html, function ($mail) use ($userEmail, $subject) {
                $mail->to($userEmail)
                     ->subject($subject);
            });

            Log::info("RMA status update notification email sent successfully.", ['entry_id' => $entry->id, 'email' => $userEmail, 'status' => $entry->status]);
        } catch (\Exception $e) {
            Log::error("Failed to send RMA status update notification: " . $e->getMessage(), ['entry_id' => $entry->id]);
        }
    }

    /**
     * Build HTML email body using premium styling.
     */
    protected function buildEmailTemplate(string $title, string $greeting, FormEntry $entry, array $data): string
    {
        $rmaNumber = sprintf('RMA-%04d', $entry->id);
        $statusText = $this->getStatusLabel($entry->status);
        $statusColor = $this->getStatusColor($entry->status);
        $timestamp = $entry->created_at ? $entry->created_at->format('d M Y H:i') : date('d M Y H:i');

        // Determine base URL dynamically based on active request
        $baseUrl = request()->getSchemeAndHttpHost();
        if (app()->runningInConsole() || !$baseUrl || $baseUrl === 'http://localhost') {
            $baseUrl = config('app.url', 'http://localhost');
        }
        $checkingLink = rtrim($baseUrl, '/') . '/kontak#status-rma';

        // Clean link
        $buktiLink = $data['bukti_pembelian'] ?? '#';
        $buktiHtml = filter_var($buktiLink, FILTER_VALIDATE_URL)
            ? '<a href="' . e($buktiLink) . '" target="_blank" style="color: #89C55C; text-decoration: underline; font-weight: 500;">Buka Dokumen</a>'
            : e($buktiLink);

        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif; line-height: 1.6; color: #334155; margin: 0; padding: 0; background-color: #f8fafc; }
                .wrapper { max-width: 600px; margin: 30px auto; padding: 0; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; }
                .header { background: #0f172a; color: white; padding: 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 22px; font-weight: 700; letter-spacing: -0.025em; font-family: "Outfit", sans-serif; }
                .header .logo { font-size: 26px; font-weight: 800; color: #89C55C; margin-bottom: 8px; }
                .content { padding: 35px 30px; }
                .greeting { font-size: 15px; margin-bottom: 25px; line-height: 1.6; }
                .details-box { background-color: #f8fafc; border-radius: 8px; border: 1px solid #f1f5f9; padding: 20px; margin-bottom: 25px; }
                .details-title { font-size: 14px; font-weight: 700; color: #0f172a; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin: 0 0 15px; letter-spacing: 0.05em; }
                .details-table { width: 100%; border-collapse: collapse; font-size: 14px; }
                .details-table td { padding: 8px 0; vertical-align: top; }
                .label-col { width: 150px; font-weight: 600; color: #64748b; }
                .value-col { color: #1e293b; }
                .status-badge { display: inline-block; background: ' . $statusColor . '20; color: ' . $statusColor . '; padding: 3px 10px; border-radius: 6px; font-weight: 700; font-size: 12px; border: 1px solid ' . $statusColor . '40; text-transform: uppercase; }
                .action-section { text-align: center; margin-top: 30px; margin-bottom: 10px; }
                .btn { display: inline-block; background-color: #89C55C; color: white !important; font-weight: 600; font-size: 14px; text-decoration: none; padding: 12px 28px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(137, 197, 92, 0.3); transition: background-color 0.2s; }
                .footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9; }
                .footer p { margin: 5px 0; }
            </style>
        </head>
        <body>
            <div class="wrapper">
                <div class="header">
                    <div class="logo">XYORA</div>
                    <h1>' . e($title) . '</h1>
                </div>
                <div class="content">
                    <div class="greeting">
                        ' . $greeting . '
                    </div>
                    <div class="details-box">
                        <h4 class="details-title">Detail Pengajuan RMA</h4>
                        <table class="details-table">
                            <tr>
                                <td class="label-col">Nomor RMA:</td>
                                <td class="value-col" style="font-weight: 700; color: #89C55C;">#' . e($rmaNumber) . '</td>
                            </tr>
                            <tr>
                                <td class="label-col">Nama Produk:</td>
                                <td class="value-col">' . e($data['nama_produk'] ?? '-') . '</td>
                            </tr>
                            <tr>
                                <td class="label-col">Serial Number:</td>
                                <td class="value-col" style="font-family: monospace; font-size: 13px;">' . e($data['serial_number_produk'] ?? '-') . '</td>
                            </tr>
                            <tr>
                                <td class="label-col">Jenis Pengajuan:</td>
                                <td class="value-col">' . e($data['jenis_pengajuan'] ?? '-') . '</td>
                            </tr>
                            <tr>
                                <td class="label-col">Jumlah Unit:</td>
                                <td class="value-col">' . e($data['jumlah_unit'] ?? '-') . ' Unit</td>
                            </tr>
                            <tr>
                                <td class="label-col">Tanggal Beli:</td>
                                <td class="value-col">' . e($data['tanggal_pembelian'] ?? '-') . '</td>
                            </tr>
                            <tr>
                                <td class="label-col">Dokumen Bukti:</td>
                                <td class="value-col">' . $buktiHtml . '</td>
                            </tr>
                            <tr>
                                <td class="label-col">Alasan RMA:</td>
                                <td class="value-col">' . e($data['alasan_pengajuan_rma'] ?? '-') . '</td>
                            </tr>
                            <tr>
                                <td class="label-col">Tanggal Masuk:</td>
                                <td class="value-col">' . e($timestamp) . '</td>
                            </tr>
                            <tr>
                                <td class="label-col">Status Terkini:</td>
                                <td class="value-col">
                                    <span class="status-badge">' . e($statusText) . '</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="action-section">
                        <a href="' . e($checkingLink) . '" class="btn">Cek Status RMA Anda</a>
                    </div>
                </div>
                <div class="footer">
                    <p>Surel ini dikirim secara otomatis oleh sistem RMA XYORA.</p>
                    <p>&copy; ' . date('Y') . ' XYORA Indonesia. Hak Cipta Dilindungi.</p>
                </div>
            </div>
        </body>
        </html>';
    }

    /**
     * Map status key to readable Indonesian label.
     */
    protected function getStatusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'Menunggu Verifikasi',
            'processing' => 'Sedang Diproses',
            'completed' => 'Disetujui / Selesai',
            'rejected' => 'Ditolak',
            default => ucfirst($status ?? 'Pending'),
        };
    }

    /**
     * Get hex color matching status.
     */
    protected function getStatusColor(?string $status): string
    {
        return match ($status) {
            'pending' => '#d97706', // amber-600
            'processing' => '#2563eb', // blue-600
            'completed' => '#89C55C', // green theme
            'rejected' => '#dc2626', // red-600
            default => '#475569', // slate-600
        };
    }
}
