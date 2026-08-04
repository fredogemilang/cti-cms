<?php

namespace Plugins\Youtube\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Plugins\Youtube\Models\YoutubeVideo;
use Plugins\Youtube\Services\YoutubeSyncService;

class YoutubeTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public bool $isSyncing = false;

    public string $syncNotification = '';

    public string $syncNotificationType = 'success';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function syncNow(YoutubeSyncService $syncService): void
    {
        $this->isSyncing = true;
        try {
            $result = $syncService->sync();
            $this->syncNotification = $result['message'];
            $this->syncNotificationType = 'success';
        } catch (\Throwable $e) {
            $this->syncNotification = 'Failed to sync: '.$e->getMessage();
            $this->syncNotificationType = 'error';
        } finally {
            $this->isSyncing = false;
            $this->resetPage();
        }
    }

    public function toggleVisibility(int $id): void
    {
        $video = YoutubeVideo::find($id);
        if ($video) {
            $video->is_visible = ! $video->is_visible;
            $video->save();
        }
    }

    public function deleteVideo(int $id): void
    {
        $video = YoutubeVideo::find($id);
        if ($video) {
            $video->delete();
            $this->syncNotification = 'Video deleted successfully.';
            $this->syncNotificationType = 'success';
        }
    }

    public function render()
    {
        $query = YoutubeVideo::query();

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('youtube_id', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter === 'visible') {
            $query->where('is_visible', true);
        } elseif ($this->statusFilter === 'hidden') {
            $query->where('is_visible', false);
        }

        $videos = $query->orderBy('published_at', 'desc')->paginate(15);
        $totalCount = YoutubeVideo::count();
        $lastSynced = YoutubeVideo::max('synced_at');

        return view('youtube::admin.youtube-table', [
            'videos' => $videos,
            'totalCount' => $totalCount,
            'lastSynced' => $lastSynced ? date('M d, Y H:i', strtotime($lastSynced)) : 'Never',
        ]);
    }
}
