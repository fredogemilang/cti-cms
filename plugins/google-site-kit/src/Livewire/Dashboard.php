<?php

namespace Plugins\GoogleSiteKit\Livewire;

use Livewire\Component;
use Plugins\GoogleSiteKit\Services\GoogleApiService;

class Dashboard extends Component
{
    public string $dateRange = '28days'; // 7days, 14days, 28days, 90days

    public string $searchQuery = '';

    public array $funnelData = [];

    public array $topQueries = [];

    public array $topPages = [];

    public array $channelsData = [];

    public array $deviceData = [];

    public array $speedData = [];

    public bool $isConnected = false;

    public bool $loadingSpeed = false;

    public function mount(): void
    {
        $api = app(GoogleApiService::class);
        $this->isConnected = $api->isConnected();
        $this->loadData();
        $this->speedData = $api->getDetailedPageSpeed(false);
    }

    public function changeDateRange(string $range): void
    {
        if (in_array($range, ['7days', '14days', '28days', '90days'])) {
            $this->dateRange = $range;
            $this->loadData();
        }
    }

    public function updatedDateRange(): void
    {
        $this->loadData();
    }

    public function updatedSearchQuery(): void
    {
        $api = app(GoogleApiService::class);
        $this->topQueries = $api->getTopQueries($this->dateRange, 10, $this->searchQuery);
    }

    public function refreshSpeed(): void
    {
        $this->loadingSpeed = true;
        $api = app(GoogleApiService::class);
        $this->speedData = $api->getDetailedPageSpeed(true);
        $this->loadingSpeed = false;
        session()->flash('speed_success', 'PageSpeed Insights & Core Web Vitals metrics re-analyzed successfully.');
    }

    protected function loadData(): void
    {
        $api = app(GoogleApiService::class);
        $this->funnelData = $api->getSearchFunnel($this->dateRange);
        $this->topQueries = $api->getTopQueries($this->dateRange, 10, $this->searchQuery);
        $this->topPages = $api->getTopPages($this->dateRange, 10);
        $this->channelsData = $api->getTrafficChannels($this->dateRange);
        $this->deviceData = $api->getDeviceAndLocationStats($this->dateRange);
    }

    public function render()
    {
        return view('google-site-kit::livewire.dashboard');
    }
}

