<?php

use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\BrevoMailServiceProvider;
use App\Providers\CmsEventServiceProvider;
use App\Providers\CmsSettingsServiceProvider;
use App\Providers\PluginServiceProvider;
use App\Providers\ThemeServiceProvider;

return [
    AppServiceProvider::class,
    CmsEventServiceProvider::class,
    CmsSettingsServiceProvider::class,
    AuthServiceProvider::class,
    BrevoMailServiceProvider::class,
    PluginServiceProvider::class,
    ThemeServiceProvider::class,
];
