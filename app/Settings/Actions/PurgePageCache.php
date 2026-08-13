<?php

namespace App\Settings\Actions;

use App\Http\Middleware\PageCache;
use App\Settings\Contracts\SettingsAction;

class PurgePageCache implements SettingsAction
{
    public function handle(array $values): array
    {
        PageCache::purgeAll();

        return ['type' => 'success', 'message' => 'Page cache purged. Next anonymous visits will be re-rendered.'];
    }
}
