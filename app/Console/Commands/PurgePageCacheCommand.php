<?php

namespace App\Console\Commands;

use App\Services\CacheManager;
use Illuminate\Console\Command;

class PurgePageCacheCommand extends Command
{
    protected $signature = 'page-cache:purge {--warm : Automatically preload and warm cache after purging}';

    protected $description = 'Purge all cached anonymous pages across LiteSpeed Cache and PageCache.';

    public function handle(): int
    {
        CacheManager::purgeAll();

        $mode = CacheManager::isLiteSpeed() ? 'LiteSpeed Cache + Page Cache' : 'Page cache';

        $this->info("{$mode} purged successfully.");

        if ($this->option('warm')) {
            $this->call('page-cache:warm');
        }

        return self::SUCCESS;
    }
}
