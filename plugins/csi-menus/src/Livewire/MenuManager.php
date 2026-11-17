<?php

namespace Plugins\CsiMenus\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;
use Plugins\CsiMenus\Models\NavMenu;
use Plugins\CsiMenus\Models\NavMenuItem;

class MenuManager extends Component
{
    public int $selectedMenuId = 0;

    public ?int $editingItemId = null;

    public ?int $parentId = null;

    public string $titleEn = '';

    public string $titleId = '';

    public string $urlEn = '#';

    public string $urlId = '';

    public string $target = '_self';

    public string $itemType = 'link';

    public bool $isActive = true;

    public function mount(): void
    {
        $first = NavMenu::query()->orderBy('id')->first();
        if ($first) {
            $this->selectedMenuId = $first->id;
        }
    }

    public function selectMenu(int $menuId): void
    {
        $this->selectedMenuId = $menuId;
        $this->resetForm();
    }

    public function editItem(int $itemId): void
    {
        $item = NavMenuItem::findOrFail($itemId);
        $this->editingItemId = $item->id;
        $this->parentId = $item->parent_id;
        $this->titleEn = (string) $item->getRawOriginal('title');
        $this->titleId = (string) ($item->translations['id']['title'] ?? '');
        $this->urlEn = (string) $item->getRawOriginal('url');
        $this->urlId = (string) ($item->translations['id']['url'] ?? '');
        $this->target = $item->target ?: '_self';
        $this->itemType = $item->item_type ?: 'link';
        $this->isActive = (bool) $item->is_active;
    }

    public function saveItem(): void
    {
        $this->validate([
            'selectedMenuId' => 'required|exists:csi_nav_menus,id',
            'titleEn' => 'required|string|max:120',
            'titleId' => 'nullable|string|max:120',
            'urlEn' => 'required|string|max:255',
            'urlId' => 'nullable|string|max:255',
            'target' => 'required|in:_self,_blank',
            'itemType' => 'required|in:link,mega_solutions,mega_alliances,button',
        ]);

        $translations = [];
        if (trim($this->titleId) !== '' || trim($this->urlId) !== '') {
            $translations['id'] = array_filter([
                'title' => trim($this->titleId) !== '' ? trim($this->titleId) : null,
                'url' => trim($this->urlId) !== '' ? trim($this->urlId) : null,
            ], fn ($v) => $v !== null);
        }

        if ($this->editingItemId) {
            $item = NavMenuItem::findOrFail($this->editingItemId);
            $item->update([
                'parent_id' => $this->parentId ?: null,
                'title' => trim($this->titleEn),
                'url' => trim($this->urlEn),
                'target' => $this->target,
                'item_type' => $this->itemType,
                'translations' => ! empty($translations) ? $translations : null,
                'is_active' => $this->isActive,
            ]);
            session()->flash('success', 'Menu item updated.');
        } else {
            $maxOrder = NavMenuItem::where('menu_id', $this->selectedMenuId)
                ->where('parent_id', $this->parentId ?: null)
                ->max('order') ?? -1;

            NavMenuItem::create([
                'menu_id' => $this->selectedMenuId,
                'parent_id' => $this->parentId ?: null,
                'title' => trim($this->titleEn),
                'url' => trim($this->urlEn),
                'target' => $this->target,
                'item_type' => $this->itemType,
                'translations' => ! empty($translations) ? $translations : null,
                'order' => $maxOrder + 1,
                'is_active' => $this->isActive,
            ]);
            session()->flash('success', 'Menu item added.');
        }

        $this->resetForm();
    }

    public function deleteItem(int $itemId): void
    {
        NavMenuItem::where('id', $itemId)->delete();
        if ($this->editingItemId === $itemId) {
            $this->resetForm();
        }
        session()->flash('success', 'Menu item deleted.');
    }

    public function toggleActive(int $itemId): void
    {
        $item = NavMenuItem::findOrFail($itemId);
        $item->update(['is_active' => ! $item->is_active]);
    }

    public function moveUp(int $itemId): void
    {
        $this->swapOrder($itemId, 'up');
    }

    public function moveDown(int $itemId): void
    {
        $this->swapOrder($itemId, 'down');
    }

    protected function swapOrder(int $itemId, string $direction): void
    {
        $item = NavMenuItem::findOrFail($itemId);
        $siblings = NavMenuItem::where('menu_id', $item->menu_id)
            ->where('parent_id', $item->parent_id)
            ->orderBy('order')
            ->orderBy('id')
            ->get()
            ->values();

        $index = $siblings->search(fn ($row) => $row->id === $item->id);
        if ($index === false) {
            return;
        }

        $targetIndex = $direction === 'up' ? $index - 1 : $index + 1;
        if (! isset($siblings[$targetIndex])) {
            return;
        }

        $other = $siblings[$targetIndex];
        $siblings[$targetIndex] = $item;
        $siblings[$index] = $other;

        foreach ($siblings as $pos => $row) {
            $row->update(['order' => $pos]);
        }
    }

    public function resetForm(): void
    {
        $this->editingItemId = null;
        $this->parentId = null;
        $this->titleEn = '';
        $this->titleId = '';
        $this->urlEn = '#';
        $this->urlId = '';
        $this->target = '_self';
        $this->itemType = 'link';
        $this->isActive = true;
    }

    public function render()
    {
        $menus = NavMenu::query()->orderBy('id')->get();
        $selectedMenu = $menus->firstWhere('id', $this->selectedMenuId);

        /** @var Collection<int, NavMenuItem> $items */
        $items = $selectedMenu
            ? NavMenuItem::where('menu_id', $selectedMenu->id)
                ->whereNull('parent_id')
                ->orderBy('order')
                ->orderBy('id')
                ->with(['children' => fn ($q) => $q->orderBy('order')->orderBy('id')])
                ->get()
            : collect();

        return view('csi-menus::livewire.menu-manager', [
            'menus' => $menus,
            'selectedMenu' => $selectedMenu,
            'items' => $items,
        ]);
    }
}
