<?php

namespace App\Livewire\Admin\Seo;

use App\Models\IndexingLog;
use App\Models\Setting;
use App\Services\GoogleIndexingService;
use App\Services\IndexNowService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SeoIndexNow extends Component
{
    use WithPagination;

    #[Url(as: 'tab', keep: true)]
    public string $activeTab = 'indexnow';

    // IndexNow Settings
    public bool $indexNowEnabled = true;

    public bool $indexNowAutoPing = true;

    public string $indexNowKey = '';

    // Google Indexing API Settings
    public bool $googleEnabled = true;

    public bool $googleAutoPing = true;

    public string $credentialMode = 'json'; // 'json' | 'fields'

    public string $jsonInput = '';

    public string $clientEmailInput = '';

    public string $privateKeyInput = '';

    // Batch Submit
    public string $manualUrlsInput = '';

    public bool $submitIndexNow = true;

    public bool $submitGoogle = true;

    // Logs Filters
    public string $dateFrom = '';

    public string $dateTo = '';

    public string $statusFilter = '';

    public string $protocolFilter = '';

    protected $queryString = [
        'activeTab' => ['except' => 'indexnow'],
    ];

    public function mount(IndexNowService $indexNowService, GoogleIndexingService $googleService): void
    {
        // Load IndexNow
        $this->indexNowEnabled = (bool) setting('seo_indexnow_enabled', true);
        $this->indexNowAutoPing = (bool) setting('seo_indexnow_auto_ping', true);
        $this->indexNowKey = $indexNowService->getKey();

        // Load Google Indexing API
        $this->googleEnabled = (bool) setting('seo_google_indexing_enabled', true);
        $this->googleAutoPing = (bool) setting('seo_google_indexing_auto_ping', true);
        $this->credentialMode = (string) setting('seo_google_indexing_credential_mode', 'json');

        $creds = $googleService->resolveCredentials();
        $this->clientEmailInput = $creds['client_email'];
        $this->privateKeyInput = $creds['private_key'];
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function regenerateIndexNowKey(IndexNowService $service): void
    {
        $this->indexNowKey = $service->generateKey();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'IndexNow API Key regenerated successfully!',
        ]);
    }

    public function saveIndexNowSettings(): void
    {
        Setting::set('seo_indexnow_enabled', $this->indexNowEnabled, 'seo', 'boolean');
        Setting::set('seo_indexnow_auto_ping', $this->indexNowAutoPing, 'seo', 'boolean');
        Setting::set('seo_indexnow_key', $this->indexNowKey, 'seo', 'text');

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'IndexNow Settings saved successfully!',
        ]);
    }

    public function saveGoogleSettings(GoogleIndexingService $service): void
    {
        Setting::set('seo_google_indexing_enabled', $this->googleEnabled, 'seo', 'boolean');
        Setting::set('seo_google_indexing_auto_ping', $this->googleAutoPing, 'seo', 'boolean');

        $savedCreds = false;

        if ($this->credentialMode === 'json') {
            if (! empty(trim($this->jsonInput))) {
                $savedCreds = $service->saveJsonCredentials($this->jsonInput);
                if ($savedCreds) {
                    $this->jsonInput = '';
                    $creds = $service->resolveCredentials();
                    $this->clientEmailInput = $creds['client_email'];
                    $this->privateKeyInput = $creds['private_key'];
                }
            } else {
                $savedCreds = true; // No new JSON pasted, keep existing
            }
        } else {
            if (! empty(trim($this->clientEmailInput)) && ! empty(trim($this->privateKeyInput))) {
                $savedCreds = $service->saveFieldCredentials($this->clientEmailInput, $this->privateKeyInput);
            } else {
                $savedCreds = true;
            }
        }

        if (! $savedCreds) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to save Google Service Account credentials. Please check format or private key string.',
            ]);

            return;
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Google Indexing API settings saved successfully!',
        ]);
    }

    public function submitManualBatch(IndexNowService $indexNow, GoogleIndexingService $google): void
    {
        $split = preg_split('/\r\n|\r|\n/', $this->manualUrlsInput);
        $urls = array_values(array_filter(array_unique(array_map('trim', is_array($split) ? $split : []))));

        if (empty($urls)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Please enter at least one valid URL to submit.',
            ]);

            return;
        }

        if (! $this->submitIndexNow && ! $this->submitGoogle) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Please select at least one target protocol (IndexNow or Google).',
            ]);

            return;
        }

        $messages = [];

        if ($this->submitIndexNow) {
            $ok = $indexNow->submitUrls($urls);
            $messages[] = $ok ? 'Submitted to IndexNow' : 'IndexNow submission failed';
        }

        if ($this->submitGoogle) {
            $ok = $google->submitUrls($urls, 'URL_UPDATED');
            $messages[] = $ok ? 'Submitted to Google Indexing API' : 'Google submission failed';
        }

        $this->manualUrlsInput = '';
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => implode(' & ', $messages).'!',
        ]);
    }

    public function exportLogsCsv()
    {
        $query = IndexingLog::query()->orderBy('request_time', 'desc');

        if ($this->dateFrom) {
            $query->whereDate('request_time', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('request_time', '<=', $this->dateTo);
        }
        if ($this->statusFilter === 'success') {
            $query->where('status_code', 200);
        } elseif ($this->statusFilter === 'error') {
            $query->where('status_code', '!=', 200);
        }
        if ($this->protocolFilter) {
            $query->where('protocol', $this->protocolFilter);
        }

        $logs = $query->get();
        $filename = 'cti-indexing-logs-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            if (! $handle) {
                return;
            }
            // UTF-8 BOM
            fwrite($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['ID', 'Protocol', 'URL', 'Status Code', 'Status', 'Response', 'Request Time', 'Entity Type', 'Entity ID']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    strtoupper($log->protocol),
                    $log->url,
                    $log->status_code,
                    $log->status_code === 200 ? 'Success' : 'Error',
                    $log->response,
                    optional($log->request_time)->toIso8601String() ?? '',
                    $log->entity_type ?? '',
                    $log->entity_id ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=utf-8']);
    }

    public function resetFilters(): void
    {
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->statusFilter = '';
        $this->protocolFilter = '';
        $this->resetPage();
    }

    public function render(GoogleIndexingService $googleService)
    {
        $isGoogleConfigured = $googleService->isConfigured();
        $googleRequestsToday = $googleService->getTodayRequestCount();

        $logsQuery = IndexingLog::query()->orderBy('request_time', 'desc');

        if ($this->dateFrom) {
            $logsQuery->whereDate('request_time', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $logsQuery->whereDate('request_time', '<=', $this->dateTo);
        }
        if ($this->statusFilter === 'success') {
            $logsQuery->where('status_code', 200);
        } elseif ($this->statusFilter === 'error') {
            $logsQuery->where('status_code', '!=', 200);
        }
        if ($this->protocolFilter) {
            $logsQuery->where('protocol', $this->protocolFilter);
        }

        $logs = $logsQuery->paginate(15);

        return view('livewire.admin.seo.seo-indexnow', [
            'isGoogleConfigured' => $isGoogleConfigured,
            'googleRequestsToday' => $googleRequestsToday,
            'logs' => $logs,
        ])->layout('layouts.admin', [
            'title' => 'Instant Indexing Suite',
        ]);
    }
}
