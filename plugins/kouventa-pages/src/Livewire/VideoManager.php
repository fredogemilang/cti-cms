<?php

namespace Plugins\KouventaPages\Livewire;

use App\Http\Middleware\PageCache;
use App\Services\CacheManager;
use Livewire\Attributes\On;
use Livewire\Component;
use Plugins\KouventaPages\Models\KouventaVideo;

class VideoManager extends Component
{
    public bool $showModal = false;

    public ?int $editingId = null;

    public string $title = '';

    public string $youtube_url = '';

    public string $youtube_id = '';

    public string $category = 'Artificial Intelligence';

    public string $custom_category = '';

    public string $thumbnail = '';

    public bool $is_active = true;

    #[On('media-selected')]
    public function handleMediaSelected(string $field, $mediaId = null, ?string $mediaPath = null, ?string $mediaUrl = null): void
    {
        if ($field === 'video_thumbnail' && $mediaPath) {
            $this->thumbnail = $mediaPath;
        }
    }

    public function updatedYoutubeUrl($value): void
    {
        $id = KouventaVideo::extractYoutubeId($value);
        if ($id) {
            $this->youtube_id = $id;
            if (! str_starts_with(trim($value), 'http')) {
                $this->youtube_url = "https://www.youtube.com/watch?v={$id}";
            }
        }
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->editingId = null;
        $this->title = '';
        $this->youtube_url = '';
        $this->youtube_id = '';
        $this->category = 'Artificial Intelligence';
        $this->custom_category = '';
        $this->thumbnail = '';
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $video = KouventaVideo::findOrFail($id);

        $this->editingId = $video->id;
        $this->title = (string) $video->title;
        $this->youtube_url = (string) $video->youtube_url;
        $this->youtube_id = (string) $video->youtube_id;

        $standardCategories = ['Artificial Intelligence', 'Customer Engagement'];
        if (in_array($video->category, $standardCategories, true)) {
            $this->category = $video->category;
            $this->custom_category = '';
        } else {
            $this->category = 'custom';
            $this->custom_category = (string) $video->category;
        }

        $this->thumbnail = (string) ($video->thumbnail ?? '');
        $this->is_active = (bool) $video->is_active;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function clearThumbnail(): void
    {
        $this->thumbnail = '';
    }

    public function save(): void
    {
        $this->youtube_url = trim($this->youtube_url);
        $extractedId = KouventaVideo::extractYoutubeId($this->youtube_url);

        if ($extractedId) {
            $this->youtube_id = $extractedId;
            if (! str_starts_with($this->youtube_url, 'http')) {
                $this->youtube_url = "https://www.youtube.com/watch?v={$extractedId}";
            }
        }

        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'youtube_url' => ['required', 'string', 'max:500'],
            'youtube_id' => ['required', 'string', 'size:11'],
            'category' => ['required', 'string'],
            'custom_category' => ['nullable', 'string', 'max:100'],
            'thumbnail' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ], [
            'title.required' => 'Please enter the video title.',
            'youtube_url.required' => 'Please enter a valid YouTube URL or Video ID.',
            'youtube_id.required' => 'A valid 11-character YouTube Video ID could not be detected from the URL.',
            'youtube_id.size' => 'The YouTube Video ID must be exactly 11 characters.',
        ]);

        $finalCategory = $this->category === 'custom'
            ? (trim($this->custom_category) !== '' ? trim($this->custom_category) : 'Artificial Intelligence')
            : $this->category;

        $data = [
            'title' => trim($this->title),
            'youtube_url' => $this->youtube_url,
            'youtube_id' => $this->youtube_id,
            'category' => $finalCategory,
            'thumbnail' => trim($this->thumbnail) !== '' ? trim($this->thumbnail) : null,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            $video = KouventaVideo::findOrFail($this->editingId);
            $video->update($data);
            $message = "Video '{$video->title}' updated successfully.";
        } else {
            $maxOrder = (int) KouventaVideo::max('sort_order');
            $data['sort_order'] = $maxOrder + 1;
            $video = KouventaVideo::create($data);
            $message = "Video '{$video->title}' added successfully.";
        }

        $this->showModal = false;
        $this->flushFrontendCache();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    public function toggleActive(int $id): void
    {
        $video = KouventaVideo::findOrFail($id);
        $video->update([
            'is_active' => ! $video->is_active,
        ]);

        $this->flushFrontendCache();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $video->is_active
                ? "'{$video->title}' is now visible on homepage."
                : "'{$video->title}' is now hidden from homepage.",
        ]);
    }

    public function moveUp(int $id): void
    {
        $videos = KouventaVideo::orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get()->values();
        $index = $videos->search(fn ($v) => $v->id === $id);

        if ($index !== false && $index > 0) {
            $prev = $videos[$index - 1];
            $curr = $videos[$index];

            $currOrder = $index + 1;
            $prevOrder = $index;

            foreach ($videos as $i => $item) {
                $item->update(['sort_order' => $i + 1]);
            }

            $curr->update(['sort_order' => $prevOrder]);
            $prev->update(['sort_order' => $currOrder]);

            $this->flushFrontendCache();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "'{$curr->title}' moved up.",
            ]);
        }
    }

    public function moveDown(int $id): void
    {
        $videos = KouventaVideo::orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get()->values();
        $index = $videos->search(fn ($v) => $v->id === $id);

        if ($index !== false && $index < ($videos->count() - 1)) {
            $next = $videos[$index + 1];
            $curr = $videos[$index];

            $currOrder = $index + 1;
            $nextOrder = $index + 2;

            foreach ($videos as $i => $item) {
                $item->update(['sort_order' => $i + 1]);
            }

            $curr->update(['sort_order' => $nextOrder]);
            $next->update(['sort_order' => $currOrder]);

            $this->flushFrontendCache();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "'{$curr->title}' moved down.",
            ]);
        }
    }

    public function delete(int $id): void
    {
        $video = KouventaVideo::findOrFail($id);
        $title = $video->title;
        $video->delete();

        // Re-index remaining order
        $remaining = KouventaVideo::orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
        foreach ($remaining as $i => $item) {
            $item->update(['sort_order' => $i + 1]);
        }

        $this->flushFrontendCache();

        $this->dispatch('notify', [
            'type' => 'info',
            'message' => "Video '{$title}' removed.",
        ]);
    }

    protected function flushFrontendCache(): void
    {
        try {
            app(CacheManager::class)->invalidateAll();
            PageCache::flush();
        } catch (\Throwable $e) {
            // Ignore cache errors
        }
    }

    public function render()
    {
        $videos = KouventaVideo::orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('kouventa-pages::livewire.video-manager', [
            'videos' => $videos,
        ]);
    }
}
