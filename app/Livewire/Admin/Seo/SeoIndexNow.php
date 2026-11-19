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

    public function exportLogsExcel()
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
        $filename = 'cti-indexing-logs-'.now()->format('Y-m-d').'.xls';

        return response()->streamDownload(function () use ($logs) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta charset="utf-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Indexing Logs</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>';
            echo '<body><table border="1">';
            echo '<tr style="background-color: #2563eb; color: #ffffff; font-weight: bold;">';
            echo '<th>ID</th><th>Protocol</th><th>URL</th><th>Status Code</th><th>Status</th><th>Response Details</th><th>Request Time</th><th>Entity Type</th><th>Entity ID</th>';
            echo '</tr>';

            foreach ($logs as $log) {
                $statusText = ($log->status_code === 200 || $log->status_code === 202) ? 'Success' : 'Error';
                $time = optional($log->request_time)->toIso8601String() ?? '';
                echo '<tr>';
                echo '<td>'.htmlspecialchars((string) $log->id).'</td>';
                echo '<td>'.htmlspecialchars(strtoupper($log->protocol)).'</td>';
                echo '<td>'.htmlspecialchars($log->url).'</td>';
                echo '<td>'.htmlspecialchars((string) $log->status_code).'</td>';
                echo '<td>'.htmlspecialchars($statusText).'</td>';
                echo '<td>'.htmlspecialchars((string) $log->response).'</td>';
                echo '<td>'.htmlspecialchars($time).'</td>';
                echo '<td>'.htmlspecialchars((string) ($log->entity_type ?? '')).'</td>';
                echo '<td>'.htmlspecialchars((string) ($log->entity_id ?? '')).'</td>';
                echo '</tr>';
            }

            echo '</table></body></html>';
        }, $filename, ['Content-Type' => 'application/vnd.ms-excel; charset=utf-8']);
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
