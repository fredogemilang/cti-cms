<?php

namespace App\Settings\Actions;

use App\Services\CacheManager;
use App\Settings\Contracts\SettingsAction;

class PurgePageCache implements SettingsAction
{
    public function handle(array $values): array
    {
        CacheManager::purgeAll();

        $mode = CacheManager::isLiteSpeed() ? 'LiteSpeed Cache + Page Cache' : 'Page cache';

        return ['type' => 'success', 'message' => "{$mode} purged. Next anonymous visits will be re-rendered."];
    }
}
