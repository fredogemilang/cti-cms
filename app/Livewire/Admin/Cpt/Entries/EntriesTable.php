<?php

namespace App\Livewire\Admin\Cpt\Entries;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\CustomTaxonomy;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class EntriesTable extends Component
{
    use WithPagination;

    public CustomPostType $postType;

    public string $search = '';

    public string $status = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    public bool $groupByParent = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'groupByParent' => ['except' => false],
    ];

    public function mount(CustomPostType $postType)
    {
        $this->postType = $postType;

        // Auto-enable hierarchy view if CPT has any parent-child relationships
        $hasHierarchy = CptEntry::where('post_type_id', $postType->id)
            ->whereNotNull('parent_id')
            ->exists();

        if ($hasHierarchy && ! request()->has('groupByParent')) {
            $this->groupByParent = true;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
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

    public array $selectedEntries = [];

    public bool $selectAll = false;

    // ... existing properties ...

    public function updatedSelectAll($value)
    {
        if ($value) {
            $query = CptEntry::where('post_type_id', $this->postType->id);

            if ($this->status === 'trash') {
                $query->onlyTrashed();
            } elseif ($this->status) {
                $query->where('status', $this->status);
            }

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('slug', 'like', '%'.$this->search.'%');
                });
            }

            $this->selectedEntries = $query->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->selectedEntries = [];
        }
    }

    public function updatedSelectedEntries()
    {
        $this->selectAll = false;
    }

    public function clearSelection()
    {
        $this->selectedEntries = [];
        $this->selectAll = false;
    }

    public function deleteSelected()
    {
        $count = count($this->selectedEntries);

        CptEntry::whereIn('id', $this->selectedEntries)
            ->where('post_type_id', $this->postType->id)
            ->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "{$count} items have been moved to trash.",
        ]);

        $this->clearSelection();
    }

    public function restoreSelected()
    {
        $count = count($this->selectedEntries);

        CptEntry::withTrashed()
            ->whereIn('id', $this->selectedEntries)
            ->where('post_type_id', $this->postType->id)
            ->restore();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "{$count} items have been restored.",
        ]);

        $this->clearSelection();
    }

    public function forceDeleteSelected()
    {
        $count = count($this->selectedEntries);

        CptEntry::withTrashed()
            ->whereIn('id', $this->selectedEntries)
            ->where('post_type_id', $this->postType->id)
            ->forceDelete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "{$count} items have been permanently deleted.",
        ]);

        $this->clearSelection();
    }

    public function publishSelected()
    {
        $count = count($this->selectedEntries);

        CptEntry::whereIn('id', $this->selectedEntries)
            ->where('post_type_id', $this->postType->id)
            ->update(['status' => 'published']);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "{$count} items have been published.",
        ]);

        $this->clearSelection();
    }

    public function draftSelected()
    {
        $count = count($this->selectedEntries);

        CptEntry::whereIn('id', $this->selectedEntries)
            ->where('post_type_id', $this->postType->id)
            ->update(['status' => 'draft']);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "{$count} items have been moved to draft.",
        ]);

        $this->clearSelection();
    }

    public function delete(int $id)
    {
        $entry = CptEntry::where('post_type_id', $this->postType->id)->findOrFail($id);
        $title = $entry->title;
        $entry->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "'{$title}' has been moved to trash.",
        ]);
    }

    public function restore(int $id)
    {
        $entry = CptEntry::withTrashed()->where('post_type_id', $this->postType->id)->findOrFail($id);
        $entry->restore();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "'{$entry->title}' has been restored.",
        ]);
    }

    public function forceDelete(int $id)
    {
        $entry = CptEntry::withTrashed()->where('post_type_id', $this->postType->id)->findOrFail($id);
        $title = $entry->title;
        $entry->forceDelete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "'{$title}' has been permanently deleted.",
        ]);
    }

    public function getStatusCountsProperty(): array
    {
        $counts = CptEntry::where('post_type_id', $this->postType->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $trashed = CptEntry::where('post_type_id', $this->postType->id)->onlyTrashed()->count();

        return [
            'all' => array_sum($counts),
            'published' => $counts['published'] ?? 0,
            'draft' => $counts['draft'] ?? 0,
            'scheduled' => $counts['scheduled'] ?? 0,
            'archived' => $counts['archived'] ?? 0,
            'trash' => $trashed,
        ];
    }

    /**
     * Check if the current CPT has any parent-child relationships.
     */
    public function getHasHierarchyProperty(): bool
    {
        return CptEntry::where('post_type_id', $this->postType->id)
            ->whereNotNull('parent_id')
            ->exists();
    }

    /**
     * Build a flat, tree-ordered list from entries for hierarchical display.
     * Each entry gets a 'depth' attribute for indentation.
     */
    protected function buildTreeOrderedList($entries): Collection
    {
        $byParent = $entries->groupBy(fn ($e) => $e->parent_id ?? 0);
        $ordered = collect();

        $this->addChildrenRecursive($byParent, 0, 0, $ordered);

        // Add any orphaned entries (parent_id set but parent not in the list)
        $addedIds = $ordered->pluck('id')->toArray();
        foreach ($entries as $entry) {
            if (! in_array($entry->id, $addedIds)) {
                $entry->depth = 0;
                $ordered->push($entry);
            }
        }

        return $ordered;
    }

    protected function addChildrenRecursive($byParent, $parentId, $depth, &$ordered): void
    {
        $children = $byParent->get($parentId, collect());

        foreach ($children->sortBy('title') as $child) {
            $child->depth = $depth;
            $ordered->push($child);
            $this->addChildrenRecursive($byParent, $child->id, $depth + 1, $ordered);
        }
    }

    public function render()
    {
        $query = CptEntry::where('post_type_id', $this->postType->id)
            ->with(['author', 'updatedBy']);

        // Handle trash status
        if ($this->status === 'trash') {
            $query->onlyTrashed();
        } elseif ($this->status) {
            $query->where('status', $this->status);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('slug', 'like', '%'.$this->search.'%');
            });
        }

        // Hierarchical mode: fetch all, build tree, then manually paginate
        if ($this->groupByParent && ! $this->search) {
            $allEntries = $query->orderBy('title', 'asc')->get();
            $treeOrdered = $this->buildTreeOrderedList($allEntries);

            // Manual pagination
            $total = $treeOrdered->count();
            $currentPage = $this->getPage();
            $slice = $treeOrdered->slice(($currentPage - 1) * $this->perPage, $this->perPage)->values();

            $entries = new LengthAwarePaginator(
                $slice, $total, $this->perPage, $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            $entries = $query->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage);
        }

        $taxonomies = CustomTaxonomy::active()
            ->forPostType($this->postType->slug)
            ->get();

        return view('livewire.admin.cpt.entries.entries-table', [
            'entries' => $entries,
            'taxonomies' => $taxonomies,
            'statusCounts' => $this->statusCounts,
            'hasHierarchy' => $this->hasHierarchy,
        ]);
    }
}
