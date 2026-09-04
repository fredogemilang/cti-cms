<?php

namespace App\Jobs;

use App\Services\SearchIndexer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IndexSearchable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    public function __construct(
        public string $searchableType,
        public int $searchableId,
        public string $action = 'index'
    ) {}

    public function handle(SearchIndexer $indexer): void
    {
        if ($this->action === 'unindex') {
            $indexer->unindex($this->searchableType, $this->searchableId);

            return;
        }

        if (! class_exists($this->searchableType)) {
            return;
        }

        $entity = $this->searchableType::find($this->searchableId);

        if ($entity) {
            $indexer->index($entity);
        } else {
            $indexer->unindex($this->searchableType, $this->searchableId);
        }
    }
}
