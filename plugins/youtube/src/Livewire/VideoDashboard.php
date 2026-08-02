<?php

namespace Plugins\Youtube\Livewire;

use Livewire\Component;
use Plugins\Youtube\Models\YoutubePlaylist;
use Plugins\Youtube\Models\YoutubeVideo;
use Plugins\Youtube\Services\YoutubeApiService;

class VideoDashboard extends Component
{
    public int $totalVideos = 0;

    public int $totalPlaylists = 0;

    public int $totalViews = 0;

    public ?string $lastSync = null;

    public ?array $featuredVideo = null;

    public bool $syncing = false;

    public function mount()
    {
        $this->loadStats();
    }

    protected function loadStats()
    {
        $this->totalVideos = YoutubeVideo::count();
        $this->totalPlaylists = YoutubePlaylist::count();
        $this->totalViews = (int) YoutubeVideo::sum('view_count');
        $this->lastSync = YoutubeVideo::max('synced_at');

        $featured = YoutubeVideo::featured()->first();
        if ($featured) {
            $this->featuredVideo = $featured->only(['id', 'title', 'thumbnail_medium', 'youtube_id', 'formatted_views']);
        } else {
            $this->featuredVideo = null;
        }
    }

    public function syncAll()
    {
        $this->syncing = true;

        try {
            $channelId = (string) (setting('youtube_channel_id') ?? config('youtube.channel_id') ?? '');
            if (! $channelId) {
                throw new \Exception('YouTube Channel ID is not configured.');
            }

            $service = app(YoutubeApiService::class);
            $service->syncAll($channelId);

            $this->dispatch('notify', type: 'success', message: 'Sync completed successfully');
            $this->loadStats();
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Sync failed: '.$e->getMessage());
        }

        $this->syncing = false;
    }

    public function render()
    {
        return view('youtube::livewire.video-dashboard');
    }
}
