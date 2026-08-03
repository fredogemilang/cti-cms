<?php

namespace App\Traits;

use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRoles
{
    /**
     * Get the roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole(string|int|Role $role): self
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->orWhere('name', $role)->firstOrFail();
        } elseif (is_int($role)) {
            $role = Role::findOrFail($role);
        }

        if (! $this->roles->contains($role->id)) {
            $this->roles()->attach($role->id);
            $this->load('roles');
        }

        return $this;
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole(string|int|Role $role): self
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->orWhere('name', $role)->first();
        } elseif (is_int($role)) {
            $role = Role::find($role);
        }

        if ($role && $this->roles->contains($role->id)) {
            $this->roles()->detach($role->id);
            $this->load('roles');
        }

        return $this;
    }

    /**
     * Sync roles for the user.
     */
    public function syncRoles(array $roles): self
    {
        $roleIds = collect($roles)->map(function ($role) {
            if ($role instanceof Role) {
                return $role->id;
            }
            if (is_int($role)) {
                return $role;
            }

            return Role::where('slug', $role)->orWhere('name', $role)->first()?->id;
        })->filter()->toArray();

        $this->roles()->sync($roleIds);
        $this->load('roles');

        return $this;
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string|array $role): bool
    {
        if (is_array($role)) {
            return $this->roles->whereIn('slug', $role)->isNotEmpty() ||
                   $this->roles->whereIn('name', $role)->isNotEmpty();
        }

        return $this->roles->where('slug', $role)->isNotEmpty() ||
               $this->roles->where('name', $role)->isNotEmpty();
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        // Super admin bypass
        if ($this->isSuperAdmin()) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (! $this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->roles->where('is_super_admin', true)->isNotEmpty();
    }

    /**
     * Get all permissions for the user.
     */
    public function getAllPermissions(): Collection
    {
        return $this->roles->flatMap(function ($role) {
            return $role->permissions;
        })->unique('id');
    }
}
