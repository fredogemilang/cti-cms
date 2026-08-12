<?php

namespace App\Console\Commands;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\MetaField;
use Illuminate\Console\Command;

class MergeCustomerSuccessContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cdt:merge-customer-success-content';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge Content + Excerpt + Client Quote into single Content field for Customer Success CPT and clean up schema/meta';

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
        $this->info("Found {$entries->count()} Customer Success entries. Starting merge...");

        foreach ($entries as $e) {
            $meta = $e->meta ?? [];
            $translations = $meta['_translations'] ?? [];

            // Helper to merge parts into unified content: Content Utama -> Excerpt Awal -> Quote Pelanggan
            $mergeParts = function ($content, $excerpt, $quote, $quoteAuthor, $quoteRole) {
                $parts = [];

                // 1. Content Utama
                $cleanContent = trim((string) $content);
                if (! empty($cleanContent)) {
                    $parts[] = $cleanContent;
                }

                // 2. Excerpt Awal
                $cleanExcerpt = trim(strip_tags((string) $excerpt));
                if (! empty($cleanExcerpt) && ! str_contains($cleanContent, $cleanExcerpt)) {
                    $parts[] = '<p>' . $cleanExcerpt . '</p>';
                }

                // 3. Quote Pelanggan
                $cleanQuote = trim(strip_tags((string) $quote));
                if (! empty($cleanQuote) && ! str_contains($cleanContent, $cleanQuote)) {
                    $quoteHtml = '<blockquote><p>&ldquo;' . $cleanQuote . '&rdquo;</p>';
                    if (! empty($quoteAuthor)) {
                        $authorText = $quoteAuthor . (! empty($quoteRole) ? ' &mdash; ' . $quoteRole : '');
                        $quoteHtml .= '<footer><strong>' . $authorText . '</strong></footer>';
                    }
                    $quoteHtml .= '</blockquote>';
                    $parts[] = $quoteHtml;
                }

                return implode("\n\n", $parts);
            };

            // Main EN / default locale content
            $newMainContent = $mergeParts(
                $e->content,
                $e->excerpt,
                $meta['quote'] ?? $meta['testimonial_quote'] ?? null,
                $meta['quote_author'] ?? $meta['testimonial_author'] ?? $meta['client_name'] ?? null,
                $meta['quote_role'] ?? $meta['author_role'] ?? null
            );

            // Update translations if present
            if (! empty($translations) && is_array($translations)) {
                foreach ($translations as $loc => $transData) {
                    if (is_array($transData)) {
                        $locContent = $mergeParts(
                            $transData['content'] ?? null,
                            $transData['excerpt'] ?? null,
                            $transData['quote'] ?? $transData['testimonial_quote'] ?? null,
                            $transData['quote_author'] ?? $transData['testimonial_author'] ?? $transData['client_name'] ?? null,
                            $transData['quote_role'] ?? $transData['author_role'] ?? null
                        );

                        $translations[$loc]['content'] = $locContent;
                        unset(
                            $translations[$loc]['excerpt'],
                            $translations[$loc]['industry'],
                            $translations[$loc]['impact'],
                            $translations[$loc]['quote'],
                            $translations[$loc]['quote_author'],
                            $translations[$loc]['quote_role'],
                            $translations[$loc]['testimonial_quote'],
                            $translations[$loc]['testimonial_author'],
                            $translations[$loc]['client_name']
                        );
                    }
                }
            }

            // Clear deprecated meta keys
            unset(
                $meta['industry'],
                $meta['impact'],
                $meta['quote'],
                $meta['quote_author'],
                $meta['quote_role'],
                $meta['testimonial_quote'],
                $meta['testimonial_author'],
                $meta['client_name'],
                $meta['author_role']
            );

            $meta['_translations'] = $translations;

            $e->content = $newMainContent;
            $e->excerpt = null;
            $e->meta = $meta;
            $e->save();

            $this->line(" - Merged & cleaned entry ID {$e->id} ({$e->title})");
        }

        // Clean up MetaFields relationship for customer-success
        $removedCount = $cpt->metaFields()
            ->whereIn('name', ['client_name', 'industry', 'impact', 'quote', 'quote_author', 'testimonial_quote', 'testimonial_author', 'author_role'])
            ->delete();

        $this->info("Removed {$removedCount} obsolete MetaField records from database.");

        // Update CPT settings meta_boxes to leave ONLY Related Products & Alliances tab
        $settings = $cpt->settings ?? [];
        $settings['meta_boxes'] = [
            [
                'id' => 'related_products',
                'title' => 'Related Products & Alliances',
                'context' => 'normal',
            ],
        ];
        $cpt->settings = $settings;

        // Also update MetaField 97 field_group to 'related_products'
        $relField = $cpt->metaFields()->where('name', 'related_products')->first();
        if ($relField) {
            $relField->field_group = 'related_products';
            $relField->save();
        }

        $cpt->save();

        $this->info('SUCCESS: Customer Success content merged and admin tabs updated to ONLY Related Products & Alliances!');
        return Command::SUCCESS;
    }
}
