<?php

namespace Plugins\CsiMenus\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class NavMenu extends Model
{
    protected $table = 'csi_nav_menus';

    protected $fillable = [
        'slug',
        'name',
        'location',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * All items belonging to this menu.
     */
    public function items(): HasMany
    {
        return $this->hasMany(NavMenuItem::class, 'menu_id')->orderBy('order');
    }

    /**
     * Top-level active items with nested active children.
     */
    public function rootItems(): HasMany
    {
        return $this->hasMany(NavMenuItem::class, 'menu_id')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->with(['children' => function ($q) {
                $q->where('is_active', true)->orderBy('order');
            }]);
    }

    /**
     * Fetch active menu items for a given theme location (header, footer_pages, etc.).
     *
     * @return Collection<int, NavMenuItem>
     */
    public static function itemsForLocation(string $location): Collection
    {
        $menu = static::query()
            ->where('location', $location)
            ->where('is_active', true)
            ->with(['rootItems'])
            ->first();

        return $menu ? $menu->rootItems : collect();
    }
}
