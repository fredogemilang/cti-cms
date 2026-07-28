<?php

namespace App\Console\Commands;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\MetaField;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportProductsFromJson extends Command
{
    protected $signature = 'products:import-from-json
                            {--path= : Path to data directory (default: ../static-files-only/cdt-gemini/data)}
                            {--vendor= : Import specific vendor only}
                            {--dry-run : Preview without saving}
                            {--force : Force update existing entries}';

    protected $description = 'Import product data from JSON files into CPT entries';

    private array $icons = [
        'globe' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'lightning' => 'M13 10V3L4 14h7v7l9-11h-7z',
        'shield' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'cloud' => 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z',
        'server' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
        'database' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01',
        'chart' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'lock' => 'M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z',
        'search' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7',
        'cog' => 'M8.25 3v1.5M12 3v1.5m3.75-1.5v1.5M3 8.25h1.5M3 12h1.5m-1.5 3.75h1.5m15-7.5h1.5M18 12h1.5m-1.5 3.75h1.5M8.25 19.5V21M12 19.5V21m3.75-1.5V21m-12-12.75h12.75c.621 0 1.125.504 1.125 1.125v12.75c0 .621-.504 1.125-1.125 1.125H4.875a1.125 1.125 0 01-1.125-1.125V4.875c0-.621.504-1.125 1.125-1.125zM7.5 7.5h9v9h-9v-9z',
        'users' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        'cpu' => 'M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3V7.5a3 3 0 013-3h13.5a3 3 0 013 3v3.75a3 3 0 01-3 3zm-13.5 0v3.75a3 3 0 003 3h13.5a3 3 0 003-3v-3.75M6.75 6.75h.008v.008H6.75V6.75zm0 3h.008v.008H6.75V9.75zm3-3h.008v.008H9.75V6.75zm0 3h.008v.008H9.75V9.75z',
        'camera' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
    ];

    public function handle(): int
    {
        $basePath = $this->option('path')
            ?: base_path('../static-files-only/cdt-gemini/data');

        if (! File::exists($basePath)) {
            $this->error("Data directory not found: {$basePath}");

            return self::FAILURE;
        }

        $productsPath = "{$basePath}/products.json";
        if (! File::exists($productsPath)) {
            $this->error("products.json not found at: {$productsPath}");

            return self::FAILURE;
        }

        $products = json_decode(File::get($productsPath), true);
        $vendorFilter = $this->option('vendor');

        // Get CPTs
        $productsCpt = CustomPostType::where('slug', 'technology-alliance')->orWhere('slug', 'products')->first();
        if (! $productsCpt) {
            $this->error('Technology Alliance CPT not found. Run CdtThemeSeeder first.');

            return self::FAILURE;
        }

        $techProductsCpt = CustomPostType::where('slug', 'tech-products')->first() ?: $productsCpt;

        $this->info("Technology Alliance CPT ID: {$productsCpt->id}");
        $this->info("Tech Products CPT ID: {$techProductsCpt->id}");
        $this->info("Data source: {$productsPath}");
        if ($this->option('dry-run')) {
            $this->warn('DRY RUN — no changes will be saved.');
        }

        $totalProducts = 0;
        $totalSubs = 0;

        foreach ($products as $product) {
            $slug = $product['slug'];
            if ($vendorFilter && $slug !== $vendorFilter) {
                continue;
            }

            // ── Import main product ────────────────────────────────
            $totalProducts++;
            $this->importProduct($product, $productsCpt, $basePath);

            // ── Import sub-products ─────────────────────────────────
            $subPath = "{$basePath}/sub-products/{$slug}/index.json";
            if (File::exists($subPath)) {
                $subProducts = json_decode(File::get($subPath), true);
                foreach ($subProducts as $sub) {
                    $totalSubs++;
                    $this->importSubProduct($sub, $product, $techProductsCpt, $productsCpt, $basePath);
                }
            }
        }

        $this->newLine();
        $this->info("Done: {$totalProducts} products, {$totalSubs} sub-products.");

        if ($this->option('dry-run')) {
            $this->warn('Dry run — no changes saved.');
        }

        return self::SUCCESS;
    }

    // ── Main Product ────────────────────────────────────────────────

    private function importProduct(array $data, CustomPostType $cpt, string $basePath): void
    {
        $slug = $data['slug'];
        $title = $data['displayName'] ?? $data['hero']['title'];
        $desc = $data['hero']['description'] ?? '';
        $logo = $data['hero']['logo'] ?? '';

        $this->line("  <fg=cyan>{$title}</> ({$slug})");

        // Build HTML content
        $contentHtml = $this->renderProductContent($data);

        // Translations
        $translations = $this->buildTranslations($data, $slug, $basePath);

        $existingMeta = [];
        $existingEntry = CptEntry::where('post_type_id', $cpt->id)->where('slug', $slug)->first();
        if ($existingEntry && is_array($existingEntry->meta)) {
            $existingMeta = $existingEntry->meta;
        }

        $meta = array_merge($existingMeta, [
            'hero' => $data['hero'] ?? [],
            'features' => $data['features'] ?? ($existingMeta['features'] ?? []),
            'solutions' => $data['solutions'] ?? [],
            'solutions_featured' => $data['solutions']['featured'] ?? ($existingMeta['solutions_featured'] ?? []),
            'solutions_other' => $data['solutions']['other'] ?? ($existingMeta['solutions_other'] ?? []),
            'solutions_description' => $data['solutions']['description'] ?? ($existingMeta['solutions_description'] ?? ''),
            'banner' => $data['banner'] ?? [],
            'banner_badge' => $data['banner']['badge'] ?? ($existingMeta['banner_badge'] ?? ''),
            'banner_headline' => $data['banner']['headline'] ?? ($existingMeta['banner_headline'] ?? ''),
            'banner_description' => $data['banner']['description'] ?? ($existingMeta['banner_description'] ?? ''),
            'banner_cta' => $data['banner']['cta'] ?? ($existingMeta['banner_cta'] ?? ''),
            'banner_logo' => $data['banner']['logo'] ?? ($existingMeta['banner_logo'] ?? $logo),
            'videos' => $data['videos'] ?? ($existingMeta['videos'] ?? []),
            'related_articles' => $data['relatedArticles'] ?? ($existingMeta['related_articles'] ?? []),
            'badges' => $data['hero']['badges'] ?? ($existingMeta['badges'] ?? []),
            'badge_images' => $data['hero']['badgeImages'] ?? ($existingMeta['badge_images'] ?? []),
            'logo' => $logo,
        ]);

        $attributes = [
            'post_type_id' => $cpt->id,
            'title' => $title,
            'content' => $contentHtml,
            'excerpt' => strip_tags($desc),
            'status' => 'published',
            'author_id' => 1,
            'published_at' => now(),
            'meta' => $meta,
            'translations' => $translations,
        ];

        if ($this->option('dry-run')) {
            $this->line("    <fg=gray>[DRY RUN] Would upsert CPT entry: {$title}</>");

            return;
        }

        $entry = CptEntry::updateOrCreate(
            ['post_type_id' => $cpt->id, 'slug' => $slug],
            $attributes
        );

        $this->line("    <info>Saved:</info> ID={$entry->id} slug={$entry->slug}");
    }

    // ── Sub-Product ─────────────────────────────────────────────────

    private function importSubProduct(array $data, array $parent, CustomPostType $subCpt, CustomPostType $parentCpt, string $basePath): void
    {
        $slug = $data['slug'];
        $title = $data['displayName'] ?? $data['hero']['title'];
        $parentSlug = $parent['slug'];

        $this->line("    <fg=yellow>Sub:</> {$title}");

        $desc = $data['hero']['description'] ?? '';
        $aboutParas = $data['about']['paragraphs'] ?? [];
        $benefits = $data['benefits']['cards'] ?? [];

        $aboutHtml = '';
        foreach ($aboutParas as $p) {
            $aboutHtml .= '<p class="mb-4">'.e($p)."</p>\n";
        }

        $benefitsHtml = '';
        foreach ($benefits as $b) {
            $icon = $this->svgIcon($b['icon'] ?? 'shield');
            $benefitsHtml .= '<div class="flex gap-4 p-4 bg-zinc-50 rounded-xl">';
            $benefitsHtml .= "<div class=\"w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary shrink-0\"><svg class=\"w-6 h-6\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"1.5\" d=\"{$icon}\"/></svg></div>";
            $benefitsHtml .= '<div><h4 class="font-bold text-zinc-900">'.e($b['title']).'</h4><p class="text-sm text-zinc-600">'.e($b['description']).'</p></div>';
            $benefitsHtml .= "</div>\n";
        }

        $contentHtml = '<div class="space-y-8">';
        if ($aboutParas) {
            $contentHtml .= "<section><h2 class=\"text-2xl font-bold mb-4\">About {$title}</h2>{$aboutHtml}</section>";
        }
        if ($benefits) {
            $contentHtml .= "<section><h2 class=\"text-2xl font-bold mb-4\">Benefits</h2><div class=\"grid grid-cols-1 md:grid-cols-2 gap-4\">{$benefitsHtml}</div></section>";
        }
        $contentHtml .= '</div>';

        // Sub-product slugs use unique names; ensure they're prefixed to avoid collisions
        $subSlug = $parentSlug.'-'.$slug;

        $attributes = [
            'post_type_id' => $subCpt->id,
            'title' => $title,
            'content' => $contentHtml,
            'excerpt' => strip_tags($desc),
            'status' => 'published',
            'author_id' => 1,
            'published_at' => now(),
            'meta' => [
                'parent_vendor' => $parentSlug,
                'hero' => $data['hero'] ?? [],
                'about' => $data['about'] ?? [],
                'benefits' => $data['benefits'] ?? [],
                'banner' => $data['banner'] ?? [],
                'customer_stories' => $data['customerStories'] ?? [],
                'solutions' => $data['solutions'] ?? [],
            ],
        ];

        if ($this->option('dry-run')) {
            $this->line("      <fg=gray>[DRY RUN] Would upsert sub-product: {$title}</>");

            return;
        }

        $entry = CptEntry::updateOrCreate(
            ['post_type_id' => $subCpt->id, 'slug' => $subSlug],
            $attributes
        );

        // Link sub-product to parent via relationship (meta field 'product_id')
        $parentEntry = CptEntry::where('post_type_id', $parentCpt->id)
            ->where('slug', $parentSlug)
            ->first();

        if ($parentEntry) {
            // Ensure product_id meta field exists on subCpt
            $metaField = MetaField::firstOrCreate(
                [
                    'name' => 'product_id',
                    'fieldable_type' => CustomPostType::class,
                    'fieldable_id' => $subCpt->id,
                ],
                [
                    'label' => 'Parent Product',
                    'type' => 'relationship',
                    'is_active' => true,
                ]
            );

            $entry->relatedEntries('product_id')->syncWithoutDetaching([
                $parentEntry->id => ['order' => 0, 'meta_field_id' => $metaField->id],
            ]);
        }

        $this->line("      <info>Saved:</info> ID={$entry->id} slug={$entry->slug}");
    }

    // ── Helpers ─────────────────────────────────────────────────────

    private function renderProductContent(array $data): string
    {
        $features = $data['features'] ?? [];
        $solutions = $data['solutions'] ?? [];
        $desc = $data['hero']['description'] ?? '';
        $title = $data['displayName'] ?? $data['hero']['title'];

        $html = '<div class="space-y-12">';

        // Description
        if ($desc) {
            $html .= '<div class="prose max-w-none"><p class="text-lg text-zinc-700">'.e($desc).'</p></div>';
        }

        // Why section
        if ($features) {
            $html .= "<section><h2 class=\"text-2xl font-bold mb-6\">Why {$title}?</h2><div class=\"grid grid-cols-1 md:grid-cols-3 gap-6\">";
            foreach ($features as $f) {
                $icon = $this->svgIcon($f['icon'] ?? 'shield');
                $html .= '<div class="bg-zinc-50 p-6 rounded-2xl border border-zinc-200">';
                $html .= "<div class=\"w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-4\"><svg class=\"w-6 h-6\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"1.5\" d=\"{$icon}\"/></svg></div>";
                $html .= '<h3 class="font-bold text-zinc-900 mb-2">'.e($f['title']).'</h3>';
                $html .= '<p class="text-sm text-zinc-600">'.e($f['description']).'</p>';
                $html .= '</div>';
            }
            $html .= '</div></section>';
        }

        // Solutions
        $featured = $solutions['featured'] ?? [];
        $other = $solutions['other'] ?? [];
        if ($featured || $other) {
            $html .= "<section><h2 class=\"text-2xl font-bold mb-6\">{$title} Solutions</h2>";
            if ($featured) {
                $html .= '<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">';
                foreach ($featured as $f) {
                    $icon = $this->svgIcon($f['icon'] ?? 'shield');
                    $link = $f['link'] ?? '#';
                    $html .= "<a href=\"{$link}\" class=\"bg-white p-6 rounded-2xl border border-zinc-200 shadow-sm hover:shadow-md transition-shadow\">";
                    $html .= "<div class=\"w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary mb-3\"><svg class=\"w-5 h-5\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"1.5\" d=\"{$icon}\"/></svg></div>";
                    $html .= '<h4 class="font-bold text-zinc-900">'.e($f['title']).'</h4>';
                    $html .= '<p class="text-sm text-zinc-600 mt-1">'.e($f['description'] ?? '').'</p>';
                    $html .= '</a>';
                }
                $html .= '</div>';
            }
            if ($other) {
                $html .= '<div class="space-y-4">';
                foreach ($other as $o) {
                    $html .= '<div class="bg-zinc-50 p-6 rounded-2xl border border-zinc-200">';
                    $html .= '<h4 class="font-bold text-zinc-900">'.e($o['title']).'</h4>';
                    $html .= '<p class="text-sm text-zinc-600 mt-1">'.e($o['description'] ?? '').'</p>';
                    $html .= '</div>';
                }
                $html .= '</div>';
            }
            $html .= '</section>';
        }

        $html .= '</div>';

        return $html;
    }

    private function buildTranslations(array $data, string $slug, string $basePath): array
    {
        // Load ID product data
        $idPath = "{$basePath}/id/products.json";
        if (! File::exists($idPath)) {
            return [];
        }

        $idProducts = json_decode(File::get($idPath), true);
        $idProduct = collect($idProducts)->firstWhere('slug', $slug);
        if (! $idProduct) {
            return [];
        }

        $idTitle = $idProduct['displayName'] ?? $idProduct['hero']['title'];
        $idDesc = $idProduct['hero']['description'] ?? '';

        return [
            'id' => [
                'title' => $idTitle,
                'excerpt' => strip_tags($idDesc),
                'content' => $this->renderProductContent($idProduct),
            ],
        ];
    }

    private function svgIcon(string $name): string
    {
        return $this->icons[$name] ?? $this->icons['shield'];
    }
}
