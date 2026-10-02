<?php

namespace App\Console\Commands;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use Illuminate\Console\Command;

class BulkUpdateTechProductsBenefitsTitle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cdt:bulk-update-benefits-title';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk update benefits_title meta field for all tech-products to match their entry title in EN and ID';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $techProductsCpt = CustomPostType::whereIn('slug', ['tech-products', 'tech_products'])->first();

        if (!$techProductsCpt) {
            $this->error('CustomPostType tech-products not found!');
            return Command::FAILURE;
        }

        $entries = CptEntry::where('post_type_id', $techProductsCpt->id)->get();

        if ($entries->isEmpty()) {
            $this->warn('No tech-products entries found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$entries->count()} tech-products entries. Updating benefits_title...");

        $count = 0;
        foreach ($entries as $entry) {
            $titleEn = $entry->getTranslation('title', 'en') ?: $entry->title;
            $titleId = $entry->getTranslation('title', 'id') ?: $titleEn;

            $meta = $entry->meta ?? [];
            $meta['benefits_title'] = $titleEn;

            if (!isset($meta['_translations'])) {
                $meta['_translations'] = [];
            }
            if (!isset($meta['_translations']['id'])) {
                $meta['_translations']['id'] = [];
            }

            $meta['_translations']['id']['benefits_title'] = $titleId;

            $entry->meta = $meta;
            $entry->save();

            $count++;
            $this->line(" [{$count}/{$entries->count()}] Updated ID {$entry->id} ({$entry->slug}) -> EN: {$titleEn} | ID: {$titleId}");
        }

        $this->info("SUCCESS: Successfully bulk updated benefits_title for {$count} tech-products entries!");
        return Command::SUCCESS;
    }
}
