<?php

namespace App\View\Components;

use App\Services\BreadcrumbService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;
use Illuminate\View\View;

class SeoBreadcrumbs extends Component
{
    public bool $enabled = true;

    public string $separator = '/';

    public string $prefix = '';

    public bool $boldLast = true;

    public array $items = [];

    public function __construct(?Model $entity = null, array $customItems = [])
    {
        $this->enabled = (bool) setting('seo_breadcrumbs_enabled', true);
        $this->separator = (string) setting('seo_breadcrumb_separator', '/');
        $this->prefix = (string) setting('seo_breadcrumb_prefix', '');
        $this->boldLast = (bool) setting('seo_breadcrumb_bold_last', true);

        if (! empty($customItems)) {
            $this->items = $customItems;
        } else {
            /** @var BreadcrumbService $service */
            $service = app(BreadcrumbService::class);
            $this->items = $service->getItems($entity);
        }
    }

    public function render(): View
    {
        return view('components.seo-breadcrumbs');
    }
}
