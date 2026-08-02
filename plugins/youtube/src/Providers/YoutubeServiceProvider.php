<?php

namespace Plugins\Youtube\Providers;

use App\Events\RenderAdminMenu;
use App\Providers\CmsPluginServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Plugins\Youtube\Livewire\PlaylistList;
use Plugins\Youtube\Livewire\VideoDashboard;
use Plugins\Youtube\Livewire\VideoList;
use Plugins\Youtube\Livewire\YoutubeSettings;
use Plugins\Youtube\Services\YoutubeApiService;

class YoutubeServiceProvider extends CmsPluginServiceProvider
{
    protected string $pluginSlug = 'youtube';

    protected array $livewireComponents = [
        'plugins.youtube-dashboard' => VideoDashboard::class,
        'plugins.youtube-videos' => VideoList::class,
        'plugins.youtube-playlists' => PlaylistList::class,
        'plugins.youtube-settings' => YoutubeSettings::class,
    ];

    protected function registerBindings(): void
    {
        $this->app->singleton(YoutubeApiService::class, function ($app) {
            return new YoutubeApiService;
        });
    }

    protected function registerScheduledTasks(Schedule $schedule): void
    {
        $schedule->call(function () {
            $channelId = (string) (setting('youtube_channel_id') ?? config('youtube.channel_id') ?? '');
            if (! empty($channelId)) {
                app(YoutubeApiService::class)->syncAll($channelId);
            }
        })->hourly()->name('youtube:sync-all');
    }

    protected function registerMenuItems(RenderAdminMenu $event): void
    {
        $event->addMenuItem([
            'title' => 'YouTube',
            'route' => 'admin.youtube',
            'url' => route('admin.youtube.index'),
            'icon' => 'play_circle',
            'permission' => 'youtube.view',
            'is_active' => true,
            'source' => 'plugin:youtube',
            'children' => [
                [
                    'title' => 'Dashboard',
                    'route' => 'admin.youtube.index',
                    'url' => route('admin.youtube.index'),
                    'icon' => 'dashboard',
                    'permission' => 'youtube.view',
                    'is_active' => true,
                    'source' => 'plugin:youtube',
                    'children' => [],
                ],
                [
                    'title' => 'Videos',
                    'route' => 'admin.youtube.videos',
                    'activeRoutePattern' => 'admin.youtube.videos|admin.youtube.videos.*',
                    'url' => route('admin.youtube.videos'),
                    'icon' => 'video_library',
                    'permission' => 'youtube.view',
                    'is_active' => true,
                    'source' => 'plugin:youtube',
                    'children' => [],
                ],
                [
                    'title' => 'Playlists',
                    'route' => 'admin.youtube.playlists',
                    'activeRoutePattern' => 'admin.youtube.playlists|admin.youtube.playlists.*',
                    'url' => route('admin.youtube.playlists'),
                    'icon' => 'playlist_play',
                    'permission' => 'youtube.view',
                    'is_active' => true,
                    'source' => 'plugin:youtube',
                    'children' => [],
                ],
                [
                    'title' => 'Settings',
                    'route' => 'admin.youtube.settings',
                    'url' => route('admin.youtube.settings'),
                    'icon' => 'settings',
                    'permission' => 'youtube-settings.view',
                    'is_active' => true,
                    'source' => 'plugin:youtube',
                    'children' => [],
                ],
            ],
        ]);
    }
}
