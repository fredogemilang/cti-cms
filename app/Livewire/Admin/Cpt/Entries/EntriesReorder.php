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
        $hasHierarchy = (bool) ($this->postType->hierarchical || CptEntry::where('post_type_id', $this->postType->id)->whereNotNull('parent_id')->exists());

        if ($hasHierarchy) {
            if ($this->parentId) {
                $entries = CptEntry::where('post_type_id', $this->postType->id)
                    ->where('parent_id', $this->parentId)
                    ->orderBy('menu_order')
                    ->orderBy('id')
                    ->get();
            } else {
                $entries = CptEntry::where('post_type_id', $this->postType->id)
                    ->whereNull('parent_id')
                    ->orderBy('menu_order')
                    ->orderBy('id')
                    ->get();
            }

            $parentEntries = CptEntry::where('post_type_id', $this->postType->id)
                ->whereNull('parent_id')
                ->orderBy('menu_order')
                ->orderBy('id')
                ->get();
        } else {
            $entries = CptEntry::where('post_type_id', $this->postType->id)
                ->orderBy('menu_order')
                ->orderBy('id')
                ->get();

            $parentEntries = collect();
        }

        return view('livewire.admin.cpt.entries.entries-reorder', [
            'hasHierarchy' => $hasHierarchy,
            'entries' => $entries,
            'parentEntries' => $parentEntries,
        ]);
    }
}
