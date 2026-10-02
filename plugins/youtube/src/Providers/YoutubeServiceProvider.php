<?php

namespace Plugins\Youtube\Providers;

use App\Events\RenderAdminMenu;
use App\Providers\CmsPluginServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Plugins\Youtube\Console\Commands\SyncYoutubeVideosCommand;
use Plugins\Youtube\Livewire\YoutubeSettings;
use Plugins\Youtube\Livewire\YoutubeTable;

class YoutubeServiceProvider extends CmsPluginServiceProvider
{
    protected string $pluginSlug = 'youtube';

    protected array $livewireComponents = [
        'plugins.youtube-table' => YoutubeTable::class,
        'plugins.youtube-settings' => YoutubeSettings::class,
    ];

    public function register(): void
    {
        parent::register();

        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncYoutubeVideosCommand::class,
            ]);
        }
    }

    public function boot(): void
    {
        parent::boot();

        // Register scheduled command for auto sync
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('youtube:sync')->daily();
        });
    }

    /**
     * Register admin menu items.
     */
    protected function registerMenuItems(RenderAdminMenu $event): void
    {
        $event->addMenuItem([
            'title' => 'YouTube Videos',
            'route' => 'admin.youtube.index',
            'url' => route('admin.youtube.index'),
            'icon' => 'video_library',
            'permission' => 'youtube.view',
            'is_active' => true,
            'source' => 'plugin:youtube',
            'children' => [
                [
                    'title' => 'All Videos',
                    'route' => 'admin.youtube.index',
                    'activeRoutePattern' => 'admin.youtube.index|admin.youtube.show',
                    'url' => route('admin.youtube.index'),
                    'icon' => 'list',
                    'permission' => 'youtube.view',
                    'is_active' => true,
                    'source' => 'plugin:youtube',
                    'children' => [],
                ],
                [
                    'title' => 'Settings',
                    'route' => 'admin.youtube.settings',
                    'activeRoutePattern' => 'admin.youtube.settings',
                    'url' => route('admin.youtube.settings'),
                    'icon' => 'settings',
                    'permission' => 'youtube.view',
                    'is_active' => true,
                    'source' => 'plugin:youtube',
                    'children' => [],
                ],
            ],
        ]);
    }
}
