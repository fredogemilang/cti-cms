<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Plugins\Posts\Models\Post;

class RemovePostLeadingImages extends Command
{
    protected $signature = 'posts:remove-leading-images
                            {--dry-run : Simulate removing leading images without saving to database}';

    protected $description = 'Remove the leading image from post content (EN & ID) when a featured image is present, preventing duplicate display';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $posts = Post::whereNotNull('featured_image')
            ->where('featured_image', '!=', '')
            ->get();

        $total = $posts->count();
        $this->info("Scanning {$total} posts with featured images...");

        $updated = 0;
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($posts as $post) {
            $changed = false;

            // 1. Check EN / primary content
            if (! empty($post->content)) {
                $cleanedEn = strip_leading_image($post->content);
                if ($cleanedEn !== $post->content) {
                    $post->content = $cleanedEn;
                    $changed = true;
                }
            }

            // 2. Check translations (e.g. ID)
            $translations = $post->translations;
            if (is_array($translations)) {
                foreach ($translations as $locale => $data) {
                    if (! empty($data['content'])) {
                        $cleanedTrans = strip_leading_image($data['content']);
                        if ($cleanedTrans !== $data['content']) {
                            $translations[$locale]['content'] = $cleanedTrans;
                            $changed = true;
                        }
                    }
                }
                if ($changed) {
                    $post->translations = $translations;
                }
            }

            if ($changed) {
                $updated++;
                if (! $dryRun) {
                    $post->save();
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("==========================================");
        $this->info("  Finished processing posts");
        $this->info("  Total scanned:   {$total}");
        $this->info("  Posts updated:   {$updated}" . ($dryRun ? " [DRY RUN - NOT SAVED]" : ""));
        $this->info("==========================================");

        return 0;
    }
}
