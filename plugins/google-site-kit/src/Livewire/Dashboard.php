<?php

namespace Plugins\GoogleSiteKit\Livewire;

use Livewire\Component;
use Plugins\GoogleSiteKit\Services\GoogleApiService;

class Dashboard extends Component
{
    public string $activeTab = 'analytics'; // analytics or search-console

    public array $scData = [];

    public array $gaData = [];

    public array $speedData = [];

    public bool $isConnected = false;

    public bool $loadingSpeed = false;

    public function mount(GoogleApiService $api)
    {
        $this->isConnected = $api->isConnected();
        $this->scData = $api->getSearchConsoleData();
        $this->gaData = $api->getAnalyticsData();

        // Initial PageSpeed stats (mockable fallback/saved values)
        $this->speedData = [
            'mobile' => (int) setting('gsk_speed_mobile', 84),
            'desktop' => (int) setting('gsk_speed_desktop', 97),
        ];
    }

    public function switchTab(string $tab)
    {
        if (in_array($tab, ['analytics', 'search-console'])) {
            $this->activeTab = $tab;
        }
    }

    public function refreshSpeed(GoogleApiService $api)
    {
        $this->loadingSpeed = true;

        $res = $api->getPageSpeedData();
        $this->speedData = $res;

        // Persist values in settings to avoid redundant requests on page load
        $this->saveSetting('gsk_speed_mobile', $res['mobile']);
        $this->saveSetting('gsk_speed_desktop', $res['desktop']);

        $this->loadingSpeed = false;
        session()->flash('speed_success', 'PageSpeed Insights metrics updated.');
    }

    protected function saveSetting(string $key, $value): void
    {
        \DB::table('settings')->updateOrInsert(
            ['key' => $key],
            [
                'value' => json_encode($value),
                'group' => 'google-site-kit',
                'type' => 'string',
                'updated_at' => now(),
            ]
        );
    }

    public function render()
    {
        return view('google-site-kit::livewire.dashboard');
    }
}
