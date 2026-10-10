<?php

namespace App\Console\Commands;

use App\Models\SearchIndex;
use App\Services\SearchIndexer;
use Illuminate\Console\Command;

class SearchReindexCommand extends Command
{
    protected $signature = 'search:reindex 
                            {--type= : Specific content type to index (pages, cpts, posts)}
                            {--fresh : Truncate search_index table before reindexing}';

    protected $description = 'Rebuild the denormalized search index for public content';

    public function handle(SearchIndexer $indexer): int
    {
        $type = $this->option('type');
        $fresh = $this->option('fresh');

        if ($fresh) {
            $this->warn('Truncating search_index table...');
            SearchIndex::truncate();
            $this->info('Search index cleared.');
        }

        $this->info('Indexing public content...');

        $count = $indexer->reindexAll($type, function (string $item) {
            $this->line("  <info>✓</info> Indexed: {$item}");
        });

        $this->newLine();
        $this->info("Successfully indexed {$count} content items across all available locales.");

        return Command::SUCCESS;
    }
}
