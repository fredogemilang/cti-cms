<?php

namespace App\Console\Commands;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Media;
use App\Models\MetaField;
use App\Services\MediaService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Plugins\Posts\Models\Post;

class ImportOfisFromJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ofis:import-from-json {--dry-run : Run the import in simulation mode without saving to database} {--force : Perform the actual import and database mutation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import OFIS solution packages and blog posts from clean JSON files with MediaService processing';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if (! $dryRun && ! $force) {
            $this->error('Please specify either --dry-run or --force to run this command.');
            $this->info('Usage: php artisan ofis:import-from-json --dry-run');
            $this->info('       php artisan ofis:import-from-json --force');

            return Command::FAILURE;
        }

        $packagesPath = base_path('data/packages.json');
        $postsPath = base_path('data/posts.json');

        if (! File::exists($packagesPath) || ! File::exists($postsPath)) {
            $this->error('Data files data/packages.json or data/posts.json were not found!');

            return Command::FAILURE;
        }

        $packagesData = json_decode(File::get($packagesPath), true);
        $postsData = json_decode(File::get($postsPath), true);

        $this->info('==================================================');
        $this->info(' OFIS CMS Data Import Command');
        $this->info(' Mode: '.($dryRun ? 'DRY-RUN (Simulation)' : 'FORCE (Database Mutation)'));
        $this->info(' Packages to process: '.count($packagesData));
        $this->info(' Posts to process:    '.count($postsData));
        $this->info('==================================================');

        if ($dryRun) {
            $this->table(['Type', 'Slug', 'Title', 'Media Path'], array_merge(
                array_map(fn ($p) => ['CPT Package', $p['slug'], $p['title'], $p['hero_image'] ?? 'N/A'], $packagesData),
                array_map(fn ($p) => ['Post', $p['slug'], $p['title'], $p['featured_image'] ?? 'N/A'], $postsData)
            ));
            $this->info('[DRY-RUN SUCCESS] Simulation finished. No database changes were made.');

            return Command::SUCCESS;
        }

        // ──────────────────────────────────────────
        // 1. Register CPT Package & MetaFields
        // ──────────────────────────────────────────
        $this->info("\n[1/3] Registering CPT \"package\"...");
        $cpt = CustomPostType::firstOrCreate(
            ['slug' => 'package'],
            [
                'name' => 'Packages',
                'singular_label' => 'Package',
                'plural_label' => 'Packages',
                'description' => 'OFIS Solution Packages',
                'icon' => 'box',
                'has_archive' => true,
                'is_active' => true,
                'settings' => [
                    'meta_boxes' => [
                        [
                            'id' => 'package_details',
                            'title' => 'Package Details',
                            'context' => 'normal',
                        ],
                    ],
                ],
            ]
        );

        // Ensure MetaFields via morphMany relationship
        $cpt->metaFields()->firstOrCreate(
            ['name' => 'hero_image'],
            [
                'label' => 'Hero Image',
                'type' => 'image',
                'field_group' => 'package_details',
                'order' => 1,
            ]
        );
        $cpt->metaFields()->firstOrCreate(
            ['name' => 'subtitle'],
            [
                'label' => 'Subtitle',
                'type' => 'text',
                'field_group' => 'package_details',
                'order' => 2,
            ]
        );
        $cpt->metaFields()->firstOrCreate(
            ['name' => 'features'],
            [
                'label' => 'Features / Sub-products',
                'type' => 'json',
                'field_group' => 'package_details',
                'order' => 3,
            ]
        );

        // ──────────────────────────────────────────
        // 2. Import CPT Packages
        // ──────────────────────────────────────────
        $this->info("\n[2/3] Importing Solution Packages...");
        $mediaService = app(MediaService::class);

        foreach ($packagesData as $pkgData) {
            $mediaPath = $this->processMediaUpload($mediaService, $pkgData['hero_image'] ?? '', $pkgData['title']);

            $entry = CptEntry::updateOrCreate(
                ['post_type_id' => $cpt->id, 'slug' => $pkgData['slug']],
                [
                    'title' => $pkgData['title'],
                    'content' => $pkgData['description'] ?? '',
                    'status' => 'published',
                    'published_at' => now(),
                    'author_id' => 1,
                    'meta' => [
                        'hero_image' => $mediaPath,
                        'subtitle' => $pkgData['subtitle'] ?? '',
                        'features' => $pkgData['features'] ?? [],
                    ],
                ]
            );

            $this->line("  ✓ Package: <fg=green>{$entry->title}</> (Media: ".($mediaPath ?: 'none').')');
        }

        // ──────────────────────────────────────────
        // 3. Import Core Blog Posts
        // ──────────────────────────────────────────
        $this->info("\n[3/3] Importing Core Blog Posts...");

        foreach ($postsData as $postItem) {
            $mediaPath = $this->processMediaUpload($mediaService, $postItem['featured_image'] ?? '', $postItem['title']);

            $post = Post::updateOrCreate(
                ['slug' => $postItem['slug']],
                [
                    'title' => $postItem['title'],
                    'content' => $postItem['content'],
                    'excerpt' => $postItem['excerpt'] ?? '',
                    'status' => 'published',
                    'published_at' => $postItem['published_at'] ?? now(),
                    'featured_image' => $mediaPath ?: null,
                    'author_id' => 1,
                ]
            );

            $this->line("  ✓ Post: <fg=cyan>{$post->title}</> (Media: ".($mediaPath ?: 'none').')');
        }

        $this->info("\n==================================================");
        $this->info(' 🎉 [IMPORT COMPLETE] Packages & Posts imported successfully!');
        $this->info('==================================================');

        return Command::SUCCESS;
    }

    /**
     * Process media upload through MediaService pipeline.
     */
    protected function processMediaUpload(MediaService $mediaService, string $imagePathOrUrl, string $title): string
    {
        if (empty($imagePathOrUrl)) {
            return '';
        }

        $originalName = basename($imagePathOrUrl);

        // Avoid re-uploading if already registered in media library
        $existingMedia = Media::where('original_filename', $originalName)
            ->orWhere('filename', $originalName)
            ->first();

        if ($existingMedia) {
            return $existingMedia->path;
        }

        // If local file exists in storage/app/public
        $storageFullPath = storage_path('app/public/'.$imagePathOrUrl);
        if (File::exists($storageFullPath)) {
            $mimeType = File::mimeType($storageFullPath);

            try {
                $uploadedFile = new UploadedFile(
                    $storageFullPath,
                    $originalName,
                    $mimeType,
                    null,
                    true
                );

                $media = $mediaService->upload($uploadedFile, [
                    'title' => $title,
                    'alt_text' => $title,
                ]);

                return $media->path;
            } catch (\Throwable $e) {
                return $imagePathOrUrl;
            }
        }

        return $imagePathOrUrl;
    }
}
