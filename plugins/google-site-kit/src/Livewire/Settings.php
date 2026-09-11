<?php

namespace Plugins\GoogleSiteKit\Livewire;

use App\Models\Setting;
use Livewire\Component;
use Plugins\GoogleSiteKit\Services\GoogleApiService;

class Settings extends Component
{
    public string $clientId = '';

    public string $clientSecret = '';

    public string $propertyId = '';

    public bool $isConnected = false;

    protected array $rules = [
        'clientId' => 'nullable|string',
        'clientSecret' => 'nullable|string',
        'propertyId' => 'nullable|string',
    ];

    public function mount(GoogleApiService $api)
    {
        $this->clientId = (string) setting('gsk_client_id', '');
        $this->clientSecret = (string) setting('gsk_client_secret', '');
        $this->propertyId = (string) setting('gsk_ga4_property_id', '');
        $this->isConnected = $api->isConnected();
    }

    public function save()
    {
        $this->validate();

        $this->saveSetting('gsk_client_id', $this->clientId);
        $this->saveSetting('gsk_client_secret', $this->clientSecret);
        $this->saveSetting('gsk_ga4_property_id', $this->propertyId);

        session()->flash('success', 'Google Site Kit credentials saved successfully.');
    }

    protected function saveSetting(string $key, ?string $value): void
    {
        Setting::set($key, $value, 'google-site-kit', 'string');
    }

    public function render()
    {
        return view('google-site-kit::livewire.settings');
    }
}
