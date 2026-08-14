<?php

namespace App\Console\Commands;

use App\Jobs\WarmCacheJob;
use Illuminate\Console\Command;

class WarmPageCacheCommand extends Command
{
    protected $signature = 'page-cache:warm {--url= : Specific URL to warm (targeted mode)} {--concurrency=5 : Number of concurrent HTTP requests}';

    protected $description = 'Preload and warm full-page cache for anonymous visitors.';

    public function handle(): int
    {
        $specificUrl = $this->option('url');
        $concurrency = (int) $this->option('concurrency');

        $this->info('Starting page cache warming process...');

        $urls = $specificUrl ? [$specificUrl] : null;
        $job = new WarmCacheJob($urls, $concurrency);

        $result = $job->handle();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Target URLs', $result['total']],
                ['Successfully Warmed', $result['warmed']],
                ['Failed / Skipped', $result['failed']],
            ]
        );

        if ($result['warmed'] > 0) {
            $this->info("Cache successfully warmed for {$result['warmed']} pages.");
        } else {
            $this->warn('No pages were warmed (verify that Page Cache is enabled in settings).');
        }

        return self::SUCCESS;
    }
}
