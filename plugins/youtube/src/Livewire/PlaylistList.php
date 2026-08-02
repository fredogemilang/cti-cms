<?php

namespace Plugins\Youtube\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Plugins\Youtube\Models\YoutubePlaylist;
use Plugins\Youtube\Services\YoutubeApiService;

class PlaylistList extends Component
{
    use WithPagination;

    public $search = '';

    public $perPage = 15;

    public bool $syncing = false;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleVisibility(int $id)
    {
        $playlist = YoutubePlaylist::findOrFail($id);
        $playlist->is_visible = ! $playlist->is_visible;
        $playlist->save();

        $status = $playlist->is_visible ? 'visible' : 'hidden';
        $this->dispatch('notify', type: 'success', message: "Playlist is now {$status}");
    }

    public function syncPlaylists()
    {
        $this->syncing = true;
        try {
            $channelId = setting('youtube_channel_id', config('youtube.channel_id', ''));
            if (! $channelId) {
                throw new \Exception('YouTube Channel ID is not configured.');
            }

            $service = app(YoutubeApiService::class);
            $service->syncAllPlaylists($channelId);

            $this->dispatch('notify', type: 'success', message: 'Playlists synced successfully');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Sync failed: '.$e->getMessage());
        }
        $this->syncing = false;
    }

    public function render()
    {
        $query = YoutubePlaylist::query();

        if ($this->search) {
            $query->where('title', 'like', '%'.$this->search.'%');
        }

        $playlists = $query->orderBy('sort_order', 'asc')->paginate($this->perPage);

        return view('youtube::livewire.playlist-list', [
            'playlists' => $playlists,
        ]);
    }
}
