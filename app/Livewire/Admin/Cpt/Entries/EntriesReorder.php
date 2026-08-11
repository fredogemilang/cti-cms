<?php

namespace App\Livewire\Admin\Cpt\Entries;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use Livewire\Component;

class EntriesReorder extends Component
{
    public CustomPostType $postType;

    public ?int $parentId = null;

    public function mount(CustomPostType $postType)
    {
        $this->postType = $postType;
    }

    public function updateOrder(array $orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            CptEntry::where('id', $id)->update(['menu_order' => $index]);
        }

        session()->flash('success', 'Menu order updated successfully!');
    }

    public function render()
    {
        $query = CptEntry::where('post_type_id', $this->postType->id);

        if ($this->postType->hierarchical) {
            if ($this->parentId) {
                $query->where('parent_id', $this->parentId);
            } else {
                $query->whereNull('parent_id');
            }
        }

        $entries = $query->orderBy('menu_order')
            ->orderBy('id')
            ->get();

        $parentEntries = $this->postType->hierarchical
            ? CptEntry::where('post_type_id', $this->postType->id)
                ->whereNull('parent_id')
                ->whereHas('children')
                ->orderBy('title')
                ->get()
            : collect();

        return view('livewire.admin.cpt.entries.entries-reorder', [
            'entries' => $entries,
            'parentEntries' => $parentEntries,
        ]);
    }
}
