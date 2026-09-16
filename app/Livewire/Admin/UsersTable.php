<?php

namespace App\Livewire\Admin;

use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @property-read array<string, int> $statusCounts
 */
class UsersTable extends Component
{
    use WithPagination;

    public $search = '';

    public $roleFilter = '';

    public $statusFilter = '';

    public $perPage = 10;

    // Sorting
    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $selectedUsers = [];

    public $selectAll = false;

    // Bulk change role
    public $bulkRoleId = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            // Default to desc for date fields
            $this->sortDirection = in_array($field, ['created_at', 'last_login_at']) ? 'desc' : 'asc';
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedUsers = $this->users->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function getUsersProperty()
    {
        return User::query()
            ->with('roles')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->roleFilter, function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('roles.id', $this->roleFilter);
                });
            })
            ->when($this->statusFilter, function ($query) {
                if ($this->statusFilter === 'active') {
                    $query->where('is_active', true);
                } elseif ($this->statusFilter === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function getStatusCountsProperty(): array
    {
        $baseQuery = User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->roleFilter, function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('roles.id', $this->roleFilter);
                });
            });

        return [
            'all' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('is_active', true)->count(),
            'inactive' => (clone $baseQuery)->where('is_active', false)->count(),
        ];
    }

    public function getRolesProperty()
    {
        return Role::orderBy('name')->get();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->roleFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    // Delete & Reassignment modal state
    public $userToDeleteId = null;

    public $userToDeleteName = '';

    public $userToDeleteContent = [];

    public $reassignToUserId = null;

    public $showDeleteModal = false;

    public function clearSelection()
    {
        $this->selectedUsers = [];
        $this->selectAll = false;
    }

    public function getAvailableReassignUsersProperty()
    {
        return User::where('is_active', true)
            ->when($this->userToDeleteId, fn ($q) => $q->where('id', '!=', $this->userToDeleteId))
            ->orderBy('name')
            ->get();
    }

    public function confirmDeleteUser($userId)
    {
        $user = User::find($userId);

        if (! $user || $user->id === auth()->id()) {
            return;
        }

        $this->userToDeleteId = $user->id;
        $this->userToDeleteName = $user->name;
        $this->userToDeleteContent = $user->authoredContentCounts();
        $this->reassignToUserId = auth()->id();
        $this->showDeleteModal = true;
    }

    public function cancelDeleteUser()
    {
        $this->userToDeleteId = null;
        $this->userToDeleteName = '';
        $this->userToDeleteContent = [];
        $this->reassignToUserId = null;
        $this->showDeleteModal = false;
    }

    public function deleteUserConfirmed()
    {
        if (! $this->userToDeleteId) {
            return;
        }

        $user = User::find($this->userToDeleteId);

        if (! $user || $user->id === auth()->id()) {
            $this->cancelDeleteUser();
            return;
        }

        $totalContent = array_sum($this->userToDeleteContent);

        if ($totalContent > 0) {
            if (! $this->reassignToUserId) {
                session()->flash('error', 'Please select a user to attribute content to.');
                return;
            }

            $targetUser = User::where('id', $this->reassignToUserId)
                ->where('id', '!=', $user->id)
                ->where('is_active', true)
                ->first();

            if (! $targetUser) {
                session()->flash('error', 'Invalid target user selected for content reassignment.');
                return;
            }

            $user->reassignContentTo($targetUser);
        }

        if ($user->avatar) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        $this->selectedUsers = array_diff($this->selectedUsers, [(string) $user->id]);
        $this->cancelDeleteUser();

        session()->flash('success', "User '{$user->name}' deleted successfully".($totalContent > 0 ? ' and all content was reassigned.' : '.'));
    }

    public function deleteSelected()
    {
        $users = User::whereIn('id', $this->selectedUsers)
            ->where('id', '!=', auth()->id())
            ->get();

        $deletedCount = 0;
        $skippedCount = 0;

        foreach ($users as $user) {
            if ($user->hasAuthoredContent()) {
                $skippedCount++;
                continue;
            }

            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }

            $user->delete();
            $deletedCount++;
        }

        $this->clearSelection();

        if ($skippedCount > 0) {
            session()->flash('error', "{$skippedCount} user(s) could not be deleted because they own published content. Please delete them individually to reassign content.");
        }

        if ($deletedCount > 0) {
            session()->flash('success', "{$deletedCount} user(s) deleted successfully.");
        }
    }

    public function changeRoleSelected($roleId)
    {
        if (empty($roleId)) {
            return;
        }

        $role = Role::find($roleId);
        if (! $role) {
            return;
        }

        $users = User::whereIn('id', $this->selectedUsers)
            ->where('id', '!=', auth()->id())
            ->get();

        foreach ($users as $user) {
            $user->roles()->sync([$roleId]);
        }

        $this->clearSelection();
        $this->bulkRoleId = '';

        session()->flash('success', count($users).' user(s) role changed to "'.$role->name.'".');
    }

    public function activateSelected()
    {
        $count = User::whereIn('id', $this->selectedUsers)
            ->where('id', '!=', auth()->id())
            ->update(['is_active' => true]);

        $this->clearSelection();

        session()->flash('success', $count.' user(s) activated successfully.');
    }

    public function deactivateSelected()
    {
        $count = User::whereIn('id', $this->selectedUsers)
            ->where('id', '!=', auth()->id())
            ->update(['is_active' => false]);

        $this->clearSelection();

        session()->flash('success', $count.' user(s) deactivated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.users-table', [
            'users' => $this->users,
            'roles' => $this->roles,
            'statusCounts' => $this->statusCounts,
        ]);
    }
}
