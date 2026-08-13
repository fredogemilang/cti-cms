<?php

namespace Plugins\RmaXyora\Providers;

use App\Events\RenderAdminMenu;
use App\Providers\CmsPluginServiceProvider;
use App\Services\SettingsRegistry;
use Plugins\RmaXyora\Observers\FormEntryObserver;
use App\Models\FormEntry;

class RmaXyoraServiceProvider extends CmsPluginServiceProvider
{
    protected string $pluginSlug = 'rma-xyora';

    /**
     * Bootstrap the plugin services.
     */
    public function boot(): void
    {
        parent::boot();

        // Register the FormEntry observer to manage status and send email notifications
        if (class_exists(FormEntry::class)) {
            FormEntry::observe(FormEntryObserver::class);
        }
    }

    /**
     * Register admin menu items.
     */
    protected function registerMenuItems(RenderAdminMenu $event): void
    {
        $event->addMenuItem([
            'title'      => 'RMA Xyora',
            'route'      => 'admin.rma-xyora',
            'url'        => route('admin.rma-xyora.index'),
            'icon'       => 'build',
            'permission' => 'rma-xyora.view',
            'is_active'  => true,
            'source'     => 'plugin:rma-xyora',
            'children'   => [],
        ]);
    }

    /**
     * Register settings fields (appears under Admin → Settings).
     */
    protected function registerSettings(SettingsRegistry $registry): void
    {
        // No custom settings needed for now
    }
}
