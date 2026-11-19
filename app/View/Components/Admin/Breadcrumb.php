<?php

namespace App\View\Components\Admin;

use App\Services\AdminMenuBuilder;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\Component;
use Illuminate\View\View;

class Breadcrumb extends Component
{
    public array $items = [];

    public function __construct(array $items = [])
    {
        if (! empty($items)) {
            $this->items = $items;
        } else {
            $this->items = $this->resolveBreadcrumbsFromMenu();
        }
    }

    /**
     * Dynamically resolve breadcrumb hierarchy from AdminMenuBuilder menu tree.
     */
    protected function resolveBreadcrumbsFromMenu(): array
    {
        $resolved = [];
        /** @var AdminMenuBuilder $builder */
        $builder = app(AdminMenuBuilder::class);
        $menuTree = $builder->getUnifiedMenuList();

        $currentRoute = request()->route()?->getName();
        $currentPath = trim(request()->path(), '/');

        foreach ($menuTree as $parent) {
            // Check children first for nested sub-menu items
            if (! empty($parent['children']) && is_array($parent['children'])) {
                foreach ($parent['children'] as $child) {
                    if ($this->isItemActive($child, $currentRoute, $currentPath)) {
                        $parentUrl = $this->getItemUrl($parent);
                        $childUrl = $this->getItemUrl($child);

                        $resolved[] = ['title' => $parent['title'], 'url' => $parentUrl];
                        $resolved[] = ['title' => $child['title'], 'url' => $childUrl];

                        return $resolved;
                    }
                }
            }

            // Check top-level parent menu
            if ($this->isItemActive($parent, $currentRoute, $currentPath)) {
                $parentUrl = $this->getItemUrl($parent);
                $resolved[] = ['title' => $parent['title'], 'url' => $parentUrl];

                return $resolved;
            }
        }

        // Fallback: If no sidebar menu matched, use page title or view title if available
        $title = trim((string) ViewFacade::yieldContent('page-title', ViewFacade::yieldContent('title', '')));
        if ($title !== '') {
            $resolved[] = ['title' => $title, 'url' => request()->fullUrl()];
        }

        return $resolved;
    }

    protected function isItemActive(array $item, ?string $currentRoute, string $currentPath): bool
    {
        // 1. Check URL path exact or prefix match first (most specific)
        $itemUrl = $this->getItemUrl($item);
        if ($itemUrl && $itemUrl !== '#') {
            $itemPath = trim((string) parse_url($itemUrl, PHP_URL_PATH), '/');
            if ($itemPath !== '' && ($currentPath === $itemPath || str_starts_with($currentPath, $itemPath.'/'))) {
                return true;
            }
        }

        // 2. Check route params if routeParams are specified
        if (! empty($item['routeParams']) && is_array($item['routeParams'])) {
            foreach ($item['routeParams'] as $key => $val) {
                if (request()->route($key) != $val) {
                    return false;
                }
            }
        }

        // 3. Check named route or route pattern match
        $patterns = array_filter(explode('|', $item['activeRoutePattern'] ?? ($item['route'] ?? '')));
        foreach ($patterns as $pattern) {
            if ($currentRoute && (request()->routeIs($pattern) || request()->routeIs($pattern.'.*'))) {
                return true;
            }
        }

        return false;
    }

    protected function getItemUrl(array $item): string
    {
        if (! empty($item['route'])) {
            try {
                return route($item['route'], $item['routeParams'] ?? []);
            } catch (\Throwable $e) {
                // Fallback to url if route exception occurs
            }
        }

        return $item['url'] ?? '#';
    }

    public function render(): View
    {
        return view('components.admin.breadcrumb');
    }
}
