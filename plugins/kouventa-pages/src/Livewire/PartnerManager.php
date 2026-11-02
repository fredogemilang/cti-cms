<?php

namespace Plugins\KouventaPages\Livewire;

use App\Http\Middleware\PageCache;
use App\Services\CacheManager;
use Livewire\Attributes\On;
use Livewire\Component;
use Plugins\KouventaPages\Models\KouventaPartner;

class PartnerManager extends Component
{
    public bool $showModal = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $logo = '';

    public string $website_url = '';

    public string $alt_text = '';

    public int $max_height = 44;

    public bool $is_active = true;

    #[On('media-selected')]
    public function handleMediaSelected(string $field, $mediaId = null, ?string $mediaPath = null, ?string $mediaUrl = null): void
    {
        if ($field === 'partner_logo' && $mediaPath) {
            $this->logo = $mediaPath;
        }
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->editingId = null;
        $this->name = '';
        $this->logo = '';
        $this->website_url = '';
        $this->alt_text = '';
        $this->max_height = 44;
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $partner = KouventaPartner::findOrFail($id);

        $this->editingId = $partner->id;
        $this->name = (string) $partner->name;
        $this->logo = (string) $partner->logo;
        $this->website_url = (string) ($partner->website_url ?? '');
        $this->alt_text = (string) ($partner->alt_text ?? '');
        $this->max_height = (int) ($partner->max_height ?: 44);
        $this->is_active = (bool) $partner->is_active;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function clearLogo(): void
    {
        $this->logo = '';
    }

    public function save(): void
    {
        $this->website_url = trim($this->website_url);
        if ($this->website_url !== '' && ! preg_match('#^https?://#i', $this->website_url) && ! str_starts_with($this->website_url, '/')) {
            $this->website_url = 'https://' . $this->website_url;
        }

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['required', 'string', 'max:500'],
            'website_url' => ['nullable', 'string', 'max:500'],
            'alt_text' => ['nullable', 'string', 'max:500'],
            'max_height' => ['required', 'integer', 'min:16', 'max:72'],
            'is_active' => ['boolean'],
        ], [
            'name.required' => 'Please enter the partner or company name.',
            'logo.required' => 'Please select or upload a partner logo image.',
        ]);

        $data = [
            'name' => trim($this->name),
            'logo' => trim($this->logo),
            'website_url' => $this->website_url !== '' ? $this->website_url : null,
            'alt_text' => trim($this->alt_text) !== '' ? trim($this->alt_text) : ('Logo ' . trim($this->name)),
            'max_height' => $this->max_height ?: 44,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            $partner = KouventaPartner::findOrFail($this->editingId);
            $partner->update($data);
            $message = "Partner '{$partner->name}' updated successfully.";
        } else {
            $maxOrder = (int) KouventaPartner::max('sort_order');
            $data['sort_order'] = $maxOrder + 1;
            $partner = KouventaPartner::create($data);
            $message = "Partner '{$partner->name}' added to 'Technology Partners'.";
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
        $partner = KouventaPartner::findOrFail($id);
        $partner->update([
            'is_active' => ! $partner->is_active,
        ]);

        $this->flushFrontendCache();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $partner->is_active
                ? "'{$partner->name}' is now visible on the homepage."
                : "'{$partner->name}' is now hidden from the homepage.",
        ]);
    }

    public function moveUp(int $id): void
    {
        $partners = KouventaPartner::orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get()->values();
        $index = $partners->search(fn ($p) => $p->id === $id);

        if ($index !== false && $index > 0) {
            $prev = $partners[$index - 1];
            $curr = $partners[$index];

            $currOrder = $index + 1;
            $prevOrder = $index;

            foreach ($partners as $i => $item) {
                $item->update(['sort_order' => $i + 1]);
            }

            $curr->update(['sort_order' => $prevOrder]);
            $prev->update(['sort_order' => $currOrder]);

            $this->flushFrontendCache();
        }
    }

    public function moveDown(int $id): void
    {
        $partners = KouventaPartner::orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get()->values();
        $index = $partners->search(fn ($p) => $p->id === $id);

        if ($index !== false && $index < $partners->count() - 1) {
            $next = $partners[$index + 1];
            $curr = $partners[$index];

            $currOrder = $index + 1;
            $nextOrder = $index + 2;

            foreach ($partners as $i => $item) {
                $item->update(['sort_order' => $i + 1]);
            }

            $curr->update(['sort_order' => $nextOrder]);
            $next->update(['sort_order' => $currOrder]);

            $this->flushFrontendCache();
        }
    }

    public function deletePartner(int $id): void
    {
        $partner = KouventaPartner::findOrFail($id);
        $name = $partner->name;
        $partner->delete();

        $this->flushFrontendCache();

        $this->dispatch('notify', [
            'type' => 'info',
            'message' => "Partner '{$name}' removed.",
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
        $partners = KouventaPartner::orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('kouventa-pages::livewire.partner-manager', [
            'partners' => $partners,
        ]);
    }
}

