<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Services\SvgSanitizerService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('media:sanitize-svg {--dry-run : Inspect and report dirty SVG files without modifying them} {--force : Re-sanitize all SVGs regardless of threat detection}')]
#[Description('Sanitize existing SVG files in the media library and storage against XSS vectors.')]
class MediaSanitizeSvg extends Command
{
    public function handle(SvgSanitizerService $sanitizer): int
    {
        $isDryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        $this->info($isDryRun
            ? '🔍 [DRY RUN] Scanning SVG files for potential XSS threats...'
            : '🛡️  Sanitizing SVG files in media storage...');

        $query = Media::query()
            ->where(fn ($q) => $q->where('mime_type', 'image/svg+xml')
                ->orWhere('file_extension', 'svg')
                ->orWhere('path', 'like', '%.svg'));

        $total = $query->count();
        $disk = Storage::disk(config('media.disk'));

        $cleanCount = 0;
        $sanitizedCount = 0;
        $reserializedCount = 0;
        $failedCount = 0;
        $missingCount = 0;
        $threatDetails = [];

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($query->cursor() as $media) {
            $path = $media->path;

            if (! $disk->exists($path)) {
                $missingCount++;
                $bar->advance();

                continue;
            }

            $raw = $disk->get($path);
            if ($raw === null || trim($raw) === '') {
                $failedCount++;
                $bar->advance();

                continue;
            }

            $clean = $sanitizer->clean($raw);

            if ($clean === null) {
                $failedCount++;
                $this->newLine();
                $this->error("[#{$media->id}] {$media->filename}: Unparseable or rejected SVG.");
                $bar->advance();

                continue;
            }

            // A threat is an XSS vector present in the source, or an issue the sanitizer
            // itself reported. A byte difference alone is NOT a threat: the sanitizer
            // always re-serializes (adds an XML declaration, re-indents, expands
            // self-closing tags), so every SVG differs after even a clean pass.
            $indicators = $this->detectThreats($raw, $sanitizer->getXmlIssues());
            $hasThreat = ! empty($indicators);

            if (! $hasThreat && trim($raw) !== trim($clean)) {
                $reserializedCount++;
            }

            // Leave threat-free files untouched — rewriting them buys no security,
            // only churn. --force exists for when a full rewrite is wanted anyway.
            if (! $hasThreat && ! $force) {
                $cleanCount++;
                $bar->advance();

                continue;
            }

            $sanitizedCount++;

            $threatDetails[] = [
                'id' => $media->id,
                'filename' => $media->original_filename ?: $media->filename,
                'threat' => $hasThreat
                    ? implode(', ', $indicators)
                    : 'No threats found — re-serialized via --force',
                'before' => strlen($raw),
                'after' => strlen($clean),
            ];

            if (! $isDryRun) {
                $disk->put($path, $clean);
                $media->update(['size' => strlen($clean)]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Display threat details table if any found
        if (! empty($threatDetails)) {
            $this->warn($isDryRun
                ? '⚠️  SVG files flagged for sanitization:'
                : '✅ Sanitized the following SVG files:');

            $tableRows = array_map(fn ($item) => [
                $item['id'],
                $item['filename'],
                $item['threat'],
                "{$item['before']} B → {$item['after']} B",
            ], $threatDetails);

            $this->table(['ID', 'Filename', 'Detected Threats / Changes', 'Size Change'], $tableRows);
        }

        // Summary table
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total SVGs Scanned', $total],
                ['Already Clean (no threats)', $cleanCount],
                [$isDryRun ? 'Threats Found' : 'Sanitized', $sanitizedCount],
                ['Cosmetic Diff Only (left untouched)', $reserializedCount],
                ['Failed / Corrupted', $failedCount],
                ['Missing from Disk', $missingCount],
            ]
        );

        if ($reserializedCount > 0) {
            $this->line("  <fg=gray>{$reserializedCount} file(s) would differ only by re-serialization (XML declaration,");
            $this->line('  indentation, self-closing tags). That is not a threat, so they were left as-is.</>');
        }

        if ($isDryRun && $sanitizedCount > 0) {
            $this->info("Run without '--dry-run' to sanitize these {$sanitizedCount} file(s).");
        }

        return self::SUCCESS;
    }

    /**
     * Identify concrete XSS vectors in raw SVG source.
     *
     * @param  array<int, array{message?: string, line?: int}>  $issues  Issues reported by the sanitizer.
     * @return array<int, string> Human-readable threat indicators; empty when the source is clean.
     */
    protected function detectThreats(string $raw, array $issues): array
    {
        $indicators = [];

        if (stripos($raw, '<script') !== false) {
            $indicators[] = '<script> tag';
        }
        if (preg_match('/\bon[a-z]+\s*=/i', $raw)) {
            $indicators[] = 'inline event handler (on*)';
        }
        if (stripos($raw, 'javascript:') !== false) {
            $indicators[] = 'javascript: URI';
        }
        if (stripos($raw, '<foreignObject') !== false) {
            $indicators[] = '<foreignObject>';
        }
        foreach ($issues as $issue) {
            if (! empty($issue['message'])) {
                $indicators[] = $issue['message'];
            }
        }

        return array_values(array_unique($indicators));
    }
}
