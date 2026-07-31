<?php

namespace App\Services;

use App\Models\StringTranslation;
use App\Models\StringTranslationKey;
use App\Models\StringTranslationSource;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class TranslationScannerService
{
    /**
     * Non-destructive scanner: discovers keys & sources across themes, plugins, and core.
     */
    public function scanAll(): array
    {
        $discovered = [];

        // 1. Scan Themes
        $themesPath = base_path('themes');
        if (File::exists($themesPath)) {
            $themeFiles = File::allFiles($themesPath);
            foreach ($themeFiles as $file) {
                if ($this->shouldScanFile($file)) {
                    $themeName = explode(DIRECTORY_SEPARATOR, $file->getRelativePathname())[0];
                    $items = $this->extractCallsFromFile($file);
                    foreach ($items as $item) {
                        $this->registerDiscoveredCall($item, 'theme', $themeName, $file->getRelativePathname(), $discovered);
                    }
                }
            }
        }

        // 2. Scan Plugins
        $pluginsPath = base_path('plugins');
        if (File::exists($pluginsPath)) {
            $pluginFiles = File::allFiles($pluginsPath);
            foreach ($pluginFiles as $file) {
                if ($this->shouldScanFile($file)) {
                    $pluginName = explode(DIRECTORY_SEPARATOR, $file->getRelativePathname())[0];
                    $items = $this->extractCallsFromFile($file);
                    foreach ($items as $item) {
                        $this->registerDiscoveredCall($item, 'plugin', $pluginName, $file->getRelativePathname(), $discovered);
                    }
                }
            }
        }

        // 3. Scan Core Views & App
        $viewsPath = resource_path('views');
        if (File::exists($viewsPath)) {
            $viewFiles = File::allFiles($viewsPath);
            foreach ($viewFiles as $file) {
                if ($this->shouldScanFile($file)) {
                    $items = $this->extractCallsFromFile($file);
                    foreach ($items as $item) {
                        $this->registerDiscoveredCall($item, 'core', 'cdt', $file->getRelativePathname(), $discovered);
                    }
                }
            }
        }

        return $discovered;
    }

    /**
     * Determine if file should be scanned for strings.
     */
    private function shouldScanFile(SplFileInfo $file): bool
    {
        $ext = $file->getExtension();

        return in_array($ext, ['php', 'blade.php'], true);
    }

    /**
     * Extract translation calls matching t('group.key', 'Default').
     */
    private function extractCallsFromFile(SplFileInfo $file): array
    {
        $content = $file->getContents();
        $items = [];

        // Match t('key', 'Default') or t('key') or __('key')
        $patterns = [
            '/\bt\(\s*[\'"]([^\'"]+)[\'"]\s*(?:,\s*[\'"]([^\'"]+)[\'"])?\s*\)/u',
            '/__\(\s*[\'"]([^\'"]+)[\'"]\s*(?:,\s*[\'"]([^\'"]+)[\'"])?\s*\)/u',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $m) {
                    $rawKey = trim($m[1]);
                    $defaultVal = isset($m[2]) ? trim($m[2]) : $rawKey;

                    if (! empty($rawKey) && ! str_contains($rawKey, '$')) {
                        $group = 'ui';
                        $key = $rawKey;

                        if (str_contains($rawKey, '.')) {
                            $parts = explode('.', $rawKey, 2);
                            $group = $parts[0];
                            $key = $parts[1];
                        }

                        $items[] = [
                            'group' => substr($group, 0, 50),
                            'key' => substr($key, 0, 191),
                            'default_value' => $defaultVal,
                        ];
                    }
                }
            }
        }

        return $items;
    }

    /**
     * Non-destructive registration of discovered key and source.
     */
    private function registerDiscoveredCall(
        array $item,
        string $sourceType,
        string $sourceName,
        string $relativePath,
        array &$discovered
    ): void {
        // Non-destructive: firstOrCreate key
        $translationKey = StringTranslationKey::firstOrCreate(
            [
                'group' => $item['group'],
                'key' => $item['key'],
            ],
            [
                'default_value' => $item['default_value'],
            ]
        );

        // Register default EN value if missing
        $defaultLocale = config('app.locale', 'en');
        StringTranslation::firstOrCreate(
            [
                'translation_key_id' => $translationKey->id,
                'locale' => $defaultLocale,
            ],
            [
                'value' => $item['default_value'],
            ]
        );

        // Non-destructive: register unique source location
        StringTranslationSource::firstOrCreate([
            'translation_key_id' => $translationKey->id,
            'source_type' => $sourceType,
            'source_name' => $sourceName,
            'source_file' => $relativePath,
        ]);

        $discovered[] = [
            'group' => $item['group'],
            'key' => $item['key'],
            'source_file' => $relativePath,
        ];
    }
}
