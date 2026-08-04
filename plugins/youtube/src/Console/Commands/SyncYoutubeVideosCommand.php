<?php

namespace Plugins\Youtube\Console\Commands;

use Illuminate\Console\Command;
use Plugins\Youtube\Services\YoutubeSyncService;

class SyncYoutubeVideosCommand extends Command
{
    protected $signature = 'youtube:sync';

    protected $description = 'Synchronize public YouTube videos from the configured YouTube Channel.';

    public function handle(YoutubeSyncService $syncService): int
    {
        $this->info('Starting YouTube Channel synchronization...');

        try {
            $result = $syncService->sync();
            $this->info($result['message']);
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Failed to sync YouTube videos: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
