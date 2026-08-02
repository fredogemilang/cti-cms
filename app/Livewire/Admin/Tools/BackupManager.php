<?php

namespace App\Livewire\Admin\Tools;

use Illuminate\Support\Facades\Artisan;
use Livewire\Component;

class BackupManager extends Component
{
    public bool $isGenerating = false;

    protected function getBackupDir(): string
    {
        return storage_path('app/'.config('backup.backup.name', config('app.name', 'laravel-backup')));
    }

    public function createBackup()
    {
        $this->isGenerating = true;
        Artisan::call('backup:run');
        $this->isGenerating = false;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'New system backup created successfully!',
        ]);
    }

    public function downloadBackup(string $filename)
    {
        $filePath = $this->getBackupDir().'/'.basename($filename);
        if (! file_exists($filePath)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Backup file not found.']);

            return null;
        }

        return response()->download($filePath);
    }

    public function deleteBackup(string $filename)
    {
        $filePath = $this->getBackupDir().'/'.basename($filename);
        if (file_exists($filePath)) {
            unlink($filePath);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Backup deleted.']);
        }
    }

    public function render()
    {
        $backupDir = $this->getBackupDir();
        $backups = [];

        if (file_exists($backupDir)) {
            $files = glob($backupDir.'/*.zip');
            foreach ($files as $file) {
                $backups[] = [
                    'filename' => basename($file),
                    'size' => round(filesize($file) / (1024 * 1024), 2).' MB',
                    'created_at' => date('M d, Y @ H:i:s', filemtime($file)),
                    'timestamp' => filemtime($file),
                ];
            }
            usort($backups, fn ($a, $b) => $b['timestamp'] <=> $a['timestamp']);
        }

        return view('livewire.admin.tools.backup-manager', [
            'backups' => $backups,
        ])->layout('layouts.admin', [
            'title' => 'System Backup Manager',
            'pageTitle' => 'System Backups',
            'pageSubtitle' => 'Generate, download, and manage system snapshots (database and uploads)',
        ]);
    }
}
