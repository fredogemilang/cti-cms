<?php

namespace App\Livewire\Admin;

use App\Services\IconLibraryService;
use Livewire\Component;

class IconPicker extends Component
{
    public string $field = '';

    public ?string $value = null;

    public string $label = 'Select Icon';

    public bool $compact = false;

    // Modal state
    public bool $showModal = false;

    public string $search = '';

    public string $selectedLibrary = 'all';

    public int $perPage = 80;

    protected $listeners = [
        'open-icon-picker' => 'handleOpenIconPicker',
    ];

    public function mount(
        string $field = '',
        ?string $value = null,
        string $label = 'Select Icon',
        bool $compact = false
    ) {
        $this->field = $field;
        $this->value = $value;
        $this->label = $label;
        $this->compact = $compact;
    }

    public function updatedSearch(): void
    {
        $this->perPage = 80;
    }

    public function updatedSelectedLibrary(): void
    {
        $this->perPage = 80;
    }

    public function handleOpenIconPicker(string $field): void
    {
        if ($this->field === $field) {
            $this->perPage = 80;
            $this->showModal = true;
        }
    }

    public function openModal(): void
    {
        $this->perPage = 80;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->perPage = 80;
    }

    public function loadMore(): void
    {
        $this->perPage += 80;
    }

    public function selectIcon(string $iconKey): void
    {
        $this->value = $iconKey;
        $this->dispatch('icon-selected', field: $this->field, value: $iconKey);

        // Dynamic Livewire parent binding fallback
        if ($this->field) {
            $this->dispatch('set-value', path: $this->field, value: $iconKey);
        }

        $this->closeModal();
    }

    public function clearIcon(): void
    {
        $this->value = null;
        $this->dispatch('icon-selected', field: $this->field, value: null);

        if ($this->field) {
            $this->dispatch('set-value', path: $this->field, value: null);
        }
    }

    public function render()
    {
        $iconService = app(IconLibraryService::class);
        $libraries = $iconService->getLibraries();
        $allIcons = $this->showModal ? $iconService->searchIcons($this->search, $this->selectedLibrary) : [];
        $totalIcons = count($allIcons);
        $icons = array_slice($allIcons, 0, $this->perPage);

        foreach ($icons as &$icon) {
            $icon['svg'] = $iconService->renderSvg($icon['key'], 'w-6 h-6');
        }
        unset($icon);

        return view('livewire.admin.icon-picker', [
            'libraries' => $libraries,
            'icons' => $icons,
            'totalIcons' => $totalIcons,
            'hasMore' => $this->perPage < $totalIcons,
        ]);
    }
}
