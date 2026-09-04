<?php

namespace App\Console\Commands;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use Illuminate\Console\Command;

class CleanCustomerSuccessQuotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cdt:clean-customer-success-quotes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up redundant double quote marks inside blockquotes for Customer Success entries';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cpt = CustomPostType::where('slug', 'customer-success')->first();

        if (! $cpt) {
            $this->error('customer-success CPT not found!');

            return Command::FAILURE;
        }

        $entries = CptEntry::where('post_type_id', $cpt->id)->get();
        $this->info("Found {$entries->count()} Customer Success entries. Cleaning up quote marks...");

        $cleanBlockquoteText = function ($html) {
            if (empty($html)) {
                return $html;
            }

            return preg_replace_callback('/<blockquote>(.*?)<\/blockquote>/s', function ($matches) {
                $inner = $matches[1];

                $inner = preg_replace_callback('/<p>(.*?)<\/p>/s', function ($pMatches) {
                    $text = trim($pMatches[1]);

                    // Remove leading quote entities/chars
                    $text = preg_replace('/^(?:&ldquo;|&rdquo;|&quot;|""|“|”|")+/u', '', $text);

                    // Remove trailing quote entities/chars
                    $text = preg_replace('/(?:&ldquo;|&rdquo;|&quot;|""|“|”|")+$/u', '', $text);

                    return '<p>'.trim($text).'</p>';
                }, $inner);

                return '<blockquote>'.$inner.'</blockquote>';
            }, $html);
        };

        $updatedCount = 0;

        foreach ($entries as $e) {
            $modified = false;

            if (! empty($e->content)) {
                $cleanedMain = $cleanBlockquoteText($e->content);
                if ($cleanedMain !== $e->content) {
                    $e->content = $cleanedMain;
                    $modified = true;
                }
            }

            $meta = $e->meta ?? [];
            if (! empty($meta['_translations']) && is_array($meta['_translations'])) {
                foreach ($meta['_translations'] as $loc => $transData) {
                    if (is_array($transData) && ! empty($transData['content'])) {
                        $cleanedTrans = $cleanBlockquoteText($transData['content']);
                        if ($cleanedTrans !== $transData['content']) {
                            $meta['_translations'][$loc]['content'] = $cleanedTrans;
                            $modified = true;
                        }
                    }
                }
            }

            if ($modified) {
                $e->meta = $meta;
                $e->save();
                $updatedCount++;
                $this->line(" - Cleaned quote marks for entry ID {$e->id} ({$e->title})");
            }
        }

        $this->info("SUCCESS: Cleaned up quote marks for {$updatedCount} Customer Success entries!");

        return Command::SUCCESS;
    }
}
