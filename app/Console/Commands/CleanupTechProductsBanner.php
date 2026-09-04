<?php

namespace App\Console\Commands;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use Illuminate\Console\Command;

class CleanupTechProductsBanner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cdt:cleanup-tech-products-banner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up banner fields for tech-products CPT entries exclusively';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $techProductsCpt = CustomPostType::where('slug', 'tech-products')->first();

        if (! $techProductsCpt) {
            $this->error('tech-products CPT not found!');

            return Command::FAILURE;
        }

        $entries = CptEntry::where('post_type_id', $techProductsCpt->id)->get();

        if ($entries->isEmpty()) {
            $this->warn('No tech-products entries found.');

            return Command::SUCCESS;
        }

        $this->info("Found {$entries->count()} tech-products entries. Cleaning up banner meta...");

        $keysToClean = ['banner_headline', 'banner_description', 'banner_cta', 'banner_logo', 'banner_image', 'banner'];
        $cleanedCount = 0;

        foreach ($entries as $entry) {
            $meta = $entry->meta ?? [];
            $modified = false;

            foreach ($keysToClean as $key) {
                if (isset($meta[$key])) {
                    unset($meta[$key]);
                    $modified = true;
                }
            }

            if (isset($meta['_translations']) && is_array($meta['_translations'])) {
                foreach ($meta['_translations'] as $loc => $trans) {
                    if (is_array($trans)) {
                        foreach ($keysToClean as $key) {
                            if (isset($meta['_translations'][$loc][$key])) {
                                unset($meta['_translations'][$loc][$key]);
                                $modified = true;
                            }
                        }
                    }
                }
            }

            if ($modified) {
                $entry->meta = $meta;
                $entry->save();
                $cleanedCount++;
                $this->line(" - Cleaned ID {$entry->id} ({$entry->title} / {$entry->slug})");
            }
        }

        $this->info("SUCCESS: Cleaned up banner fields for {$cleanedCount} tech-products entries!");

        return Command::SUCCESS;
    }
}
