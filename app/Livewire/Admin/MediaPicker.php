<?php

namespace App\Livewire\Admin;

use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

/**
 * @property-read LengthAwarePaginator<Media> $media
 */
class MediaPicker extends Component
{
    use WithFileUploads, WithPagination;

    // Configuration
    public string $field = '';

    public ?string $value = null;

    public string $label = 'Select Media';

    public bool $multiple = false;

    public string $accept = 'image/*';

    public bool $shouldClearAfterSelection = false;

    public bool $compact = false;

    public bool $showTrigger = true;

    // Modal state
    public bool $showModal = false;

    public string $activeTab = 'library'; // 'library' or 'upload'

    // Library state
    public string $search = '';

    public string $filterType = 'images';

    public ?int $selectedMediaId = null;

    public ?array $selectedMedia = null;

    // Multiple selection state
    public array $selectedMediaIds = [];

    // Upload state
    public $uploadFile = null;

    public array $uploadFiles = [];

    public bool $uploading = false;

    protected $listeners = [
        'open-media-picker' => 'handleOpenMediaPicker',
    ];

    public function mount(
        string $field = '',
        ?string $value = null,
        string $label = 'Select Media',
        bool $multiple = false,
        string $accept = 'image/*',
        bool $shouldClearAfterSelection = false,
        bool $compact = false,
        bool $showModal = false,
        bool $showTrigger = true
    ) {
        $this->field = $field;
        $this->value = $value;
        $this->label = $label;
        $this->multiple = $multiple || str_starts_with($field, 'gallery_') || str_starts_with($field, 'gallery_add.');
        $this->accept = $accept;
        $this->shouldClearAfterSelection = $shouldClearAfterSelection || $this->multiple;
        $this->compact = $compact;
        $this->showModal = $showModal;
        $this->showTrigger = $showModal ? false : $showTrigger;
        $this->loadMediaFromValue();
    }

    public function updatedValue(): void
    {
        $this->loadMediaFromValue();
    }

    public function loadMediaFromValue(): void
    {
        if (! $this->value) {
            $this->selectedMedia = null;
            $this->selectedMediaId = null;

            return;
        }

        $media = Media::where('path', $this->value)
            ->orWhere('webp_path', $this->value)
            ->first();

        if ($media) {
            $this->selectedMediaId = $media->id;
            $this->selectedMedia = [
                'id' => $media->id,
                'path' => $media->path,
                'webp_path' => $media->webp_path,
                'url' => $media->url,
                'webp_url' => $media->webp_url,
                'original_filename' => $media->original_filename,
            ];
        } else {
            // Fallback for custom asset path or external URL
            $url = str_starts_with($this->value, 'http') || str_starts_with($this->value, 'themes/') || str_starts_with($this->value, 'assets/')
                ? asset($this->value)
                : asset('storage/'.$this->value);

            $this->selectedMediaId = null;
            $this->selectedMedia = [
                'id' => null,
                'path' => $this->value,
                'webp_path' => null,
                'url' => $url,
                'webp_url' => null,
                'original_filename' => basename($this->value),
            ];
        }
    }

    public function handleOpenMediaPicker($field = null)
    {
        if ($field) {
            $this->field = $field;
        }
        $this->openModal();
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->activeTab = 'library';
        $this->search = '';
        $this->selectedMediaIds = [];
        if ($this->selectedMediaId && ! $this->multiple) {
            $this->selectedMediaIds = [$this->selectedMediaId];
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['uploadFile', 'uploadFiles', 'uploading', 'selectedMediaIds']);
        $this->dispatch('media-picker-closed');
    }

    public function selectMedia(int $mediaId)
    {
        if ($this->multiple) {
            if (in_array($mediaId, $this->selectedMediaIds, true)) {
                $this->selectedMediaIds = array_values(array_filter($this->selectedMediaIds, fn ($id) => $id !== $mediaId));
            } else {
                $this->selectedMediaIds[] = $mediaId;
            }
        } else {
            $this->selectedMediaIds = [$mediaId];
            $media = Media::find($mediaId);
            if ($media) {
                $this->selectedMediaId = $mediaId;
                $this->selectedMedia = [
                    'id' => $media->id,
                    'path' => $media->path,
                    'webp_path' => $media->webp_path,
                    'url' => $media->url,
                    'webp_url' => $media->webp_url,
                    'original_filename' => $media->original_filename,
                ];
            }
        }
    }

    public function toggleSelectAll()
    {
        if (! $this->multiple) {
            return;
        }

        $currentPageIds = $this->media->getCollection()->pluck('id')->toArray();
        $allSelectedOnPage = empty(array_diff($currentPageIds, $this->selectedMediaIds));

        if ($allSelectedOnPage) {
            // Deselect page items
            $this->selectedMediaIds = array_values(array_diff($this->selectedMediaIds, $currentPageIds));
        } else {
            // Select all page items
            $this->selectedMediaIds = array_values(array_unique(array_merge($this->selectedMediaIds, $currentPageIds)));
        }
    }

    public function confirmSelection()
    {
        if ($this->multiple && ! empty($this->selectedMediaIds)) {
            $items = Media::whereIn('id', $this->selectedMediaIds)->get();
            $mediaPaths = [];

            foreach ($items as $media) {
                $path = $media->webp_path ?? $media->path;
                $mediaPaths[] = $path;

                // Dispatch single event per item for backwards compatibility
                $this->dispatch('media-selected',
                    field: $this->field,
                    mediaId: $media->id,
                    mediaPath: $path,
                    mediaUrl: $media->webp_url ?? $media->url
                );
            }

            // Dispatch bulk selection event
            $this->dispatch('media-selected-multiple',
                field: $this->field,
                mediaPaths: $mediaPaths
            );

            $this->selectedMediaIds = [];
        } elseif (! $this->multiple && $this->selectedMedia) {
            // Prioritize WebP path if available
            $mediaPath = $this->selectedMedia['webp_path'] ?? $this->selectedMedia['path'];
            $mediaUrl = $this->selectedMedia['webp_url'] ?? $this->selectedMedia['url'];

            $this->dispatch('media-selected',
                field: $this->field,
                mediaId: $this->selectedMedia['id'],
                mediaPath: $mediaPath,
                mediaUrl: $mediaUrl
            );

            if (! $this->shouldClearAfterSelection) {
                $this->value = $mediaPath;
            } else {
                $this->value = null;
                $this->selectedMedia = null;
                $this->selectedMediaId = null;
            }
        }

        $this->closeModal();
    }

    public function removeMedia()
    {
        $this->selectedMediaId = null;
        $this->selectedMedia = null;
        $this->selectedMediaIds = [];
        $this->value = null;
        $this->dispatch('media-removed', field: $this->field);
    }

    public function uploadAndSelect()
    {
        $filesToUpload = [];
        if (! empty($this->uploadFiles)) {
            $filesToUpload = $this->uploadFiles;
        } elseif ($this->uploadFile) {
            $filesToUpload = [$this->uploadFile];
        }

        if (empty($filesToUpload)) {
            session()->flash('picker-error', 'Please select at least one file to upload.');

            return;
        }

        if (! auth()->user()->can('media.upload')) {
            session()->flash('picker-error', 'You do not have permission to upload media.');

            return;
        }

        $this->validate([
            'uploadFiles.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,webp,svg,pdf,doc,docx,xls,xlsx,zip,rar',
            'uploadFile' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,svg,pdf,doc,docx,xls,xlsx,zip,rar',
        ]);

        $this->uploading = true;

        try {
            $mediaService = app(MediaService::class);
            $uploadedMediaIds = [];
            $lastMedia = null;

            foreach ($filesToUpload as $file) {
                $media = $mediaService->upload($file);
                $uploadedMediaIds[] = $media->id;
                $lastMedia = $media;
            }

            if ($this->multiple) {
                $this->selectedMediaIds = array_values(array_unique(array_merge($this->selectedMediaIds, $uploadedMediaIds)));
            } elseif ($lastMedia) {
                $this->selectedMediaId = $lastMedia->id;
                $this->selectedMedia = [
                    'id' => $lastMedia->id,
                    'path' => $lastMedia->path,
                    'webp_path' => $lastMedia->webp_path,
                    'url' => $lastMedia->url,
                    'webp_url' => $lastMedia->webp_url,
                    'original_filename' => $lastMedia->original_filename,
                ];
            }

            // Switch to library tab to show selections
            $this->activeTab = 'library';
            $this->reset(['uploadFile', 'uploadFiles', 'uploading']);

            session()->flash('picker-success', count($filesToUpload).' file(s) uploaded successfully.');
        } catch (\Exception $e) {
            \Log::error('Media picker upload error: '.$e->getMessage());
            session()->flash('picker-error', 'Upload failed: '.$e->getMessage());
            $this->uploading = false;
        }
    }

    public function clearUpload()
    {
        $this->reset(['uploadFile', 'uploadFiles', 'uploading']);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getMediaProperty()
    {
        $query = Media::query();

        // Apply type filter
        if ($this->filterType === 'images') {
            $query->images();
        } elseif ($this->filterType === 'documents') {
            $query->documents();
        }

        // Apply search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('original_filename', 'like', '%'.$this->search.'%')
                    ->orWhere('title', 'like', '%'.$this->search.'%')
                    ->orWhere('alt_text', 'like', '%'.$this->search.'%');
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(18);
    }

    public function render()
    {
        return view('livewire.admin.media-picker', [
            'mediaItems' => $this->media,
            'multiple' => $this->multiple,
            'selectedMediaIds' => $this->selectedMediaIds,
            'field' => $this->field,
            'label' => $this->label,
            'compact' => $this->compact,
            'showTrigger' => $this->showTrigger,
        ]);
    }
}
