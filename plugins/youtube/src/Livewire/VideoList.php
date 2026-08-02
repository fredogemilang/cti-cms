<?php

namespace Plugins\Youtube\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Plugins\Youtube\Models\YoutubePlaylist;
use Plugins\Youtube\Models\YoutubeVideo;
use Plugins\Youtube\Services\YoutubeApiService;

class VideoList extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $playlistFilter = '';

    public $perPage = 15;

    public $sortField = 'published_at';

    public $sortDirection = 'desc';

    public bool $syncing = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'playlistFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPlaylistFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function syncVideos()
    {
        $this->syncing = true;
        try {
            $channelId = (string) (setting('youtube_channel_id') ?? config('youtube.channel_id') ?? '');
            if (! $channelId) {
                throw new \Exception('YouTube Channel ID is not configured.');
            }

            $service = app(YoutubeApiService::class);
            $service->syncAllVideos($channelId);

            $this->dispatch('notify', type: 'success', message: 'Videos synced successfully');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Sync failed: '.$e->getMessage());
        }
        $this->syncing = false;
    }

    public function setFeatured(int $id)
    {
        YoutubeVideo::where('is_featured', true)->update(['is_featured' => false]);
        YoutubeVideo::findOrFail($id)->update(['is_featured' => true]);
        $this->dispatch('notify', type: 'success', message: 'Video featured successfully');
    }

    public function unsetFeatured(int $id)
    {
        YoutubeVideo::findOrFail($id)->update(['is_featured' => false]);
        $this->dispatch('notify', type: 'success', message: 'Video feature removed');
    }

    public function toggleVisibility(int $id)
    {
        $video = YoutubeVideo::findOrFail($id);
        $video->is_visible = ! $video->is_visible;
        $video->save();

        $status = $video->is_visible ? 'visible' : 'hidden';
        $this->dispatch('notify', type: 'success', message: "Video is now {$status}");
    }

    public function render()
    {
        $query = YoutubeVideo::query();

        if ($this->search) {
            $query->where('title', 'like', '%'.$this->search.'%')
                ->orWhere('channel_title', 'like', '%'.$this->search.'%');
        }

        if ($this->statusFilter === 'visible') {
            $query->where('is_visible', true);
        } elseif ($this->statusFilter === 'hidden') {
            $query->where('is_visible', false);
        } elseif ($this->statusFilter === 'featured') {
            $query->where('is_featured', true);
        }

        if ($this->playlistFilter) {
            $query->whereHas('playlists', function ($q) {
                $q->where('youtube_playlists.id', $this->playlistFilter);
            });
        }

        $query->orderBy($this->sortField, $this->sortDirection);
        $videos = $query->paginate($this->perPage);
        $playlists = YoutubePlaylist::orderBy('title', 'asc')->get();

        return view('youtube::livewire.video-list', [
            'videos' => $videos,
            'playlists' => $playlists,
        ]);
    }
}
