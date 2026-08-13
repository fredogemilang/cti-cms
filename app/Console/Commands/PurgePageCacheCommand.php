<?php

namespace App\Console\Commands;

use App\Http\Middleware\PageCache;
use Illuminate\Console\Command;

class PurgePageCacheCommand extends Command
{
    protected $signature = 'page-cache:purge';

    protected $description = 'Bump the page cache version, invalidating all cached anonymous pages.';

    public function handle(): int
    {
        PageCache::purgeAll();

        $this->info('Page cache purged.');

        return self::SUCCESS;
    }
}
