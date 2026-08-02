<?php

namespace Plugins\Youtube\Livewire;

use App\Models\Setting;
use Livewire\Component;
use Plugins\Youtube\Services\YoutubeApiService;

class YoutubeSettings extends Component
{
    public string $apiKey = '';

    public string $channelId = '';

    public int $perPage = 12;

    public ?array $channelInfo = null;

    public bool $testing = false;

    public ?string $testError = null;

    public bool $saving = false;

    public function mount()
    {
        $this->apiKey = (string) (setting('youtube_api_key') ?? config('youtube.api_key') ?? '');
        $this->channelId = (string) (setting('youtube_channel_id') ?? config('youtube.channel_id') ?? '');
        $this->perPage = (int) (setting('youtube_per_page') ?? config('youtube.per_page') ?? 12);
    }

    public function testConnection()
    {
        $this->testing = true;
        $this->testError = null;
        $this->channelInfo = null;

        try {
            // Instantiate service temporarily with current API key
            config(['youtube.api_key' => $this->apiKey]);
            $service = app(YoutubeApiService::class);
            $this->channelInfo = $service->testConnection($this->channelId);
        } catch (\Exception $e) {
            $this->testError = $e->getMessage();
        }

        $this->testing = false;
    }

    public function save()
    {
        $this->validate([
            'apiKey' => 'required|string',
            'channelId' => 'required|string',
            'perPage' => 'required|integer|min:1|max:50',
        ]);

        $this->saving = true;

        Setting::set('youtube_api_key', $this->apiKey);
        Setting::set('youtube_channel_id', $this->channelId);
        Setting::set('youtube_per_page', $this->perPage);

        $this->saving = false;

        $this->dispatch('notify', type: 'success', message: 'Settings saved successfully');
    }

    public function render()
    {
        return view('youtube::livewire.youtube-settings');
    }
}
