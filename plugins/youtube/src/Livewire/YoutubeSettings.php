<?php

namespace Plugins\Youtube\Livewire;

use App\Models\Setting;
use Livewire\Component;
use Plugins\Youtube\Services\YoutubeSyncService;

class YoutubeSettings extends Component
{
    public string $channelId = '';

    public string $apiKey = '';

    public bool $autoSync = false;

    public int $perPage = 12;

    public string $notification = '';

    public string $notificationType = 'success';

    public function mount(): void
    {
        $rawChannel = Setting::get('youtube_channel_id', 'UCG0E2Kc-QvMRLJ70Q-XeemA');
        $rawApiKey = Setting::get('youtube_api_key', 'AIzaSyBg1ngOtubANX-JCB2eJxGM-gqRIENXOPQ');
        $rawAutoSync = Setting::get('youtube_auto_sync', false);
        $rawPerPage = Setting::get('youtube_per_page', 12);

        $this->channelId = is_array($rawChannel) ? ($rawChannel['v'] ?? '') : (string) $rawChannel;
        $this->apiKey = is_array($rawApiKey) ? ($rawApiKey['v'] ?? '') : (string) $rawApiKey;
        $this->autoSync = is_array($rawAutoSync) ? (bool) ($rawAutoSync['v'] ?? false) : (bool) $rawAutoSync;
        $this->perPage = is_array($rawPerPage) ? (int) ($rawPerPage['v'] ?? 12) : (int) $rawPerPage;
    }

    public function saveSettings(): void
    {
        Setting::set('youtube_channel_id', $this->channelId, 'youtube', 'text');
        Setting::set('youtube_api_key', $this->apiKey, 'youtube', 'text');
        Setting::set('youtube_auto_sync', $this->autoSync, 'youtube', 'boolean');
        Setting::set('youtube_per_page', $this->perPage, 'youtube', 'number');

        $this->notification = 'YouTube settings updated successfully!';
        $this->notificationType = 'success';
    }

    public function testSync(YoutubeSyncService $syncService): void
    {
        $this->saveSettings();
        try {
            $result = $syncService->sync();
            $this->notification = 'Connection successful! '.$result['message'];
            $this->notificationType = 'success';
        } catch (\Throwable $e) {
            $this->notification = 'Sync test failed: '.$e->getMessage();
            $this->notificationType = 'error';
        }
    }

    public function render()
    {
        return view('youtube::admin.youtube-settings');
    }
}
