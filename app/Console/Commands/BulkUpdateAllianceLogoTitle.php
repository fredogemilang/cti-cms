<?php

namespace App\Console\Commands;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use Illuminate\Console\Command;

class BulkUpdateAllianceLogoTitle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cdt:bulk-update-logo-title';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk update logo_title meta field for all Technology Alliance entries to Official Technology Partner (EN) and Mitra Teknologi Resmi (ID)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cpt = CustomPostType::whereIn('slug', ['technology-alliance', 'products'])->first();

        if (!$cpt) {
            $this->error('Technology Alliance CPT not found!');
            return Command::FAILURE;
        }

        $entries = CptEntry::where('post_type_id', $cpt->id)->get();

        if ($entries->isEmpty()) {
            $this->warn('No Technology Alliance entries found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$entries->count()} Technology Alliance entries. Updating logo_title...");

        $count = 0;
        foreach ($entries as $entry) {
            $meta = $entry->meta ?? [];
            $meta['logo_title'] = 'Official Technology Partner';

            if (!isset($meta['_translations'])) {
                $meta['_translations'] = [];
            }
            if (!isset($meta['_translations']['id'])) {
                $meta['_translations']['id'] = [];
            }

            $meta['_translations']['id']['logo_title'] = 'Mitra Teknologi Resmi';

            $entry->meta = $meta;
            $entry->save();

            $count++;
            $this->line(" [{$count}/{$entries->count()}] Updated ID {$entry->id} ({$entry->slug}) -> EN: Official Technology Partner | ID: Mitra Teknologi Resmi");
        }

        $this->info("SUCCESS: Successfully updated logo_title for {$count} Technology Alliance entries!");
        return Command::SUCCESS;
    }
}
