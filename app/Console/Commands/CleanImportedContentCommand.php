<?php

namespace App\Console\Commands;

use App\Models\CptEntry;
use App\Models\Page;
use App\Models\PageBlock;
use App\Services\ContentSanitizerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CleanImportedContentCommand extends Command
{
    protected $signature = 'content:clean-imported {--dry-run : Run simulation without saving changes to database}';

    protected $description = 'Sanitize imported HTML content into clean, TipTap-compatible semantic HTML';

    public function handle(ContentSanitizerService $sanitizer): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('🔍 Running SIMULATION (--dry-run). No database changes will be saved.');
        } else {
            $this->info('🚀 Running HTML Content Sanitization on Database...');
        }

        $totalCleaned = 0;

        // 1. Clean Posts (if Posts plugin exists)
        if (Schema::hasTable('posts') && class_exists(\Plugins\Posts\Models\Post::class)) {
            $totalCleaned += $this->cleanPosts($sanitizer, $dryRun);
        }

        // 2. Clean Pages
        $totalCleaned += $this->cleanPages($sanitizer, $dryRun);

        // 3. Clean PageBlocks (WYSIWYG / Textarea)
        $totalCleaned += $this->cleanPageBlocks($sanitizer, $dryRun);

        // 4. Clean CptEntries
        $totalCleaned += $this->cleanCptEntries($sanitizer, $dryRun);

        $this->newLine();
        if ($dryRun) {
            $this->info("✨ Simulation finished! {$totalCleaned} record(s) would be cleaned. Run without --dry-run to apply.");
        } else {
            $this->info("✅ Content sanitization completed! {$totalCleaned} record(s) updated successfully.");
        }

        return self::SUCCESS;
    }

    protected function cleanPosts(ContentSanitizerService $sanitizer, bool $dryRun): int
    {
        $posts = \Plugins\Posts\Models\Post::all();
        $cleanedCount = 0;

        foreach ($posts as $post) {
            $changed = false;

            // Main content
            if (! empty($post->content)) {
                $cleaned = $sanitizer->sanitize($post->content);
                if ($cleaned !== $post->content) {
                    $post->content = $cleaned;
                    $changed = true;
                }
            }

            // Translations JSON
            if (! empty($post->translations) && is_array($post->translations)) {
                $translations = $post->translations;
                foreach ($translations as $locale => $fields) {
                    if (isset($fields['content']) && is_string($fields['content'])) {
                        $cleanedTrans = $sanitizer->sanitize($fields['content']);
                        if ($cleanedTrans !== $fields['content']) {
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
                $cleanedCount++;
                if (! $dryRun) {
                    $post->save();
                }
            }
        }

        $this->line("  📄 Posts: {$cleanedCount} cleaned / " . $posts->count() . ' total');

        return $cleanedCount;
    }

    protected function cleanPages(ContentSanitizerService $sanitizer, bool $dryRun): int
    {
        $pages = Page::all();
        $cleanedCount = 0;

        foreach ($pages as $page) {
            $changed = false;

            if (! empty($page->content)) {
                $cleaned = $sanitizer->sanitize($page->content);
                if ($cleaned !== $page->content) {
                    $page->content = $cleaned;
                    $changed = true;
                }
            }

            if (! empty($page->translations) && is_array($page->translations)) {
                $translations = $page->translations;
                foreach ($translations as $locale => $fields) {
                    if (isset($fields['content']) && is_string($fields['content'])) {
                        $cleanedTrans = $sanitizer->sanitize($fields['content']);
                        if ($cleanedTrans !== $fields['content']) {
                            $translations[$locale]['content'] = $cleanedTrans;
                            $changed = true;
                        }
                    }
                }
                if ($changed) {
                    $page->translations = $translations;
                }
            }

            if ($changed) {
                $cleanedCount++;
                if (! $dryRun) {
                    $page->save();
                }
            }
        }

        $this->line("  📃 Pages: {$cleanedCount} cleaned / " . $pages->count() . ' total');

        return $cleanedCount;
    }

    protected function cleanPageBlocks(ContentSanitizerService $sanitizer, bool $dryRun): int
    {
        $blocks = PageBlock::whereIn('type', ['wysiwyg', 'textarea'])->get();
        $cleanedCount = 0;

        foreach ($blocks as $block) {
            $changed = false;

            if (! empty($block->value) && is_string($block->value)) {
                $cleaned = $sanitizer->sanitize($block->value);
                if ($cleaned !== $block->value) {
                    $block->value = $cleaned;
                    $changed = true;
                }
            }

            if (! empty($block->translations) && is_array($block->translations)) {
                $translations = $block->translations;
                foreach ($translations as $locale => $fields) {
                    if (isset($fields['value']) && is_string($fields['value'])) {
                        $cleanedTrans = $sanitizer->sanitize($fields['value']);
                        if ($cleanedTrans !== $fields['value']) {
                            $translations[$locale]['value'] = $cleanedTrans;
                            $changed = true;
                        }
                    }
                }
                if ($changed) {
                    $block->translations = $translations;
                }
            }

            if ($changed) {
                $cleanedCount++;
                if (! $dryRun) {
                    $block->save();
                }
            }
        }

        $this->line("  🧩 Page Blocks: {$cleanedCount} cleaned / " . $blocks->count() . ' total');

        return $cleanedCount;
    }

    protected function cleanCptEntries(ContentSanitizerService $sanitizer, bool $dryRun): int
    {
        if (! Schema::hasTable('cpt_entries')) {
            return 0;
        }

        $entries = CptEntry::all();
        $cleanedCount = 0;

        foreach ($entries as $entry) {
            $changed = false;

            if (! empty($entry->content) && is_string($entry->content)) {
                $cleaned = $sanitizer->sanitize($entry->content);
                if ($cleaned !== $entry->content) {
                    $entry->content = $cleaned;
                    $changed = true;
                }
            }

            if (! empty($entry->translations) && is_array($entry->translations)) {
                $translations = $entry->translations;
                foreach ($translations as $locale => $fields) {
                    if (isset($fields['content']) && is_string($fields['content'])) {
                        $cleanedTrans = $sanitizer->sanitize($fields['content']);
                        if ($cleanedTrans !== $fields['content']) {
                            $translations[$locale]['content'] = $cleanedTrans;
                            $changed = true;
                        }
                    }
                }
                if ($changed) {
                    $entry->translations = $translations;
                }
            }

            if ($changed) {
                $cleanedCount++;
                if (! $dryRun) {
                    $entry->save();
                }
            }
        }

        $this->line("  📦 CPT Entries: {$cleanedCount} cleaned / " . $entries->count() . ' total');

        return $cleanedCount;
    }
}
