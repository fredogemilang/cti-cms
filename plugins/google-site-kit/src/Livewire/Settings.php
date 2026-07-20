<?php

namespace Plugins\GoogleSiteKit\Livewire;

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
        $this->clientId = setting('gsk_client_id', '');
        $this->clientSecret = setting('gsk_client_secret', '');
        $this->propertyId = setting('gsk_ga4_property_id', '');
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
        return view('google-site-kit::livewire.settings');
    }
}
