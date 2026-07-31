<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class IconLibraryService
{
    /**
     * Cache key for icon libraries.
     */
    protected const CACHE_KEY = 'icon_libraries_manifest';

    /**
     * Get all registered icon libraries.
     */
    public function getLibraries(): array
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            $libraries = [
                'lucide' => [
                    'name' => 'Lucide Icons',
                    'prefix' => 'lucide',
                    'is_system' => true,
                    'is_active' => true,
                    'icons' => $this->loadLucideIcons(),
                ],
            ];

            // Load custom libraries from storage/app/icons/
            $customPath = storage_path('app/icons');
            if (File::exists($customPath)) {
                $files = File::files($customPath);
                foreach ($files as $file) {
                    if ($file->getExtension() === 'json') {
                        $content = json_decode(File::get($file), true);
                        if (is_array($content) && isset($content['prefix'], $content['icons'])) {
                            $prefix = $content['prefix'];
                            $libraries[$prefix] = [
                                'name' => $content['name'] ?? Str::title($prefix),
                                'prefix' => $prefix,
                                'is_system' => false,
                                'is_active' => $content['is_active'] ?? true,
                                'icons' => $content['icons'],
                            ];
                        }
                    }
                }
            }

            return $libraries;
        });
    }

    /**
     * Search icons across all active libraries.
     */
    public function searchIcons(?string $query = null, ?string $libraryPrefix = null): array
    {
        $libraries = $this->getLibraries();
        $results = [];

        foreach ($libraries as $prefix => $lib) {
            if (! ($lib['is_active'] ?? true)) {
                continue;
            }

            if ($libraryPrefix && $libraryPrefix !== 'all' && $prefix !== $libraryPrefix) {
                continue;
            }

            foreach ($lib['icons'] as $name => $iconData) {
                $fullName = "{$prefix}:{$name}";
                $label = is_array($iconData) ? ($iconData['label'] ?? Str::title(str_replace('-', ' ', $name))) : Str::title(str_replace('-', ' ', $name));
                $tags = is_array($iconData) ? ($iconData['tags'] ?? []) : [];
                $svg = is_array($iconData) ? ($iconData['svg'] ?? '') : (is_string($iconData) ? $iconData : '');

                if ($query) {
                    $q = strtolower($query);
                    $matchesName = str_contains(strtolower($name), $q);
                    $matchesLabel = str_contains(strtolower($label), $q);
                    $matchesTag = collect($tags)->contains(fn ($t) => str_contains(strtolower($t), $q));

                    if (! ($matchesName || $matchesLabel || $matchesTag)) {
                        continue;
                    }
                }

                $results[] = [
                    'key' => $fullName,
                    'name' => $name,
                    'label' => $label,
                    'library' => $prefix,
                    'library_name' => $lib['name'],
                    'svg' => $this->renderSvg($fullName),
                ];
            }
        }

        return $results;
    }

    /**
     * Render SVG element for an icon key (e.g., 'lucide:shield' or 'shield').
     */
    public function renderSvg(?string $iconKey, string $class = 'w-5 h-5'): string
    {
        if (empty($iconKey)) {
            return '';
        }

        $parts = explode(':', $iconKey, 2);
        if (count($parts) === 2) {
            $prefix = $parts[0];
            $name = $parts[1];
        } else {
            $prefix = 'lucide';
            $name = $parts[0];
        }

        $libraries = $this->getLibraries();
        $iconData = $libraries[$prefix]['icons'][$name] ?? null;

        if (! $iconData) {
            return '';
        }

        $svgContent = is_array($iconData) ? ($iconData['svg'] ?? '') : (is_string($iconData) ? $iconData : '');
        if (empty($svgContent)) {
            return '';
        }

        // If svgContent is inner SVG paths/shapes
        if (! str_contains($svgContent, '<svg')) {
            $svgContent = sprintf(
                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="%s">%s</svg>',
                e($class),
                $svgContent
            );
        } else {
            // Inject class into existing root <svg> tag
            if ($class) {
                if (str_contains($svgContent, 'class="')) {
                    $svgContent = preg_replace('/class="([^"]*)"/', 'class="$1 '.e($class).'"', $svgContent, 1);
                } else {
                    $svgContent = preg_replace('/<svg/', '<svg class="'.e($class).'"', $svgContent, 1);
                }
            }
        }

        return $svgContent;
    }

    /**
     * Clear icon cache.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Load built-in Lucide icons catalog.
     */
    protected function loadLucideIcons(): array
    {
        $manifestFile = resource_path('icons/lucide.json');
        if (File::exists($manifestFile)) {
            $data = json_decode(File::get($manifestFile), true);
            if (is_array($data)) {
                return $data;
            }
        }

        // Default popular Lucide SVG paths fallback catalog
        return $this->getFallbackLucideIcons();
    }

    /**
     * Essential fallback Lucide SVG inner paths.
     */
    protected function getFallbackLucideIcons(): array
    {
        return [
            'shield' => [
                'label' => 'Shield',
                'tags' => ['security', 'protection', 'safe', 'guard', 'defense'],
                'svg' => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>',
            ],
            'shield-check' => [
                'label' => 'Shield Check',
                'tags' => ['security', 'verified', 'approved'],
                'svg' => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>',
            ],
            'server' => [
                'label' => 'Server',
                'tags' => ['cloud', 'database', 'hosting', 'infrastructure'],
                'svg' => '<rect width="20" height="8" x="2" y="2" rx="2" ry="2"/><rect width="20" height="8" x="2" y="14" rx="2" ry="2"/><line x1="6" x2="6.01" y1="6" y2="6"/><line x1="6" x2="6.01" y1="18" y2="18"/>',
            ],
            'cpu' => [
                'label' => 'CPU',
                'tags' => ['processor', 'hardware', 'chip', 'tech'],
                'svg' => '<rect width="16" height="16" x="4" y="4" rx="2"/><rect width="6" height="6" x="9" y="9" rx="1"/><path d="M15 2v2"/><path d="M15 20v2"/><path d="M2 15h2"/><path d="M2 9h2"/><path d="M20 15h2"/><path d="M20 9h2"/><path d="M9 2v2"/><path d="M9 20v2"/>',
            ],
            'lock' => [
                'label' => 'Lock',
                'tags' => ['security', 'private', 'password'],
                'svg' => '<rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
            ],
            'database' => [
                'label' => 'Database',
                'tags' => ['storage', 'sql', 'data'],
                'svg' => '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/><path d="M3 12c0 1.66 4 3 9 3s9-1.34 9-3"/>',
            ],
            'globe' => [
                'label' => 'Globe',
                'tags' => ['world', 'network', 'cdn', 'internet'],
                'svg' => '<circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/>',
            ],
            'zap' => [
                'label' => 'Zap (Lightning)',
                'tags' => ['lightning', 'fast', 'energy', 'performance'],
                'svg' => '<path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/>',
            ],
            'cloud' => [
                'label' => 'Cloud',
                'tags' => ['hosting', 'sky', 'storage'],
                'svg' => '<path d="M17.5 19x-11A5.5 5.5 0 0 1 5 9.5a5.5 5.5 0 0 1 10.5-2.2A5 5 0 0 1 20 12.5a4.5 4.5 0 0 1-2.5 6.5Z"/>',
            ],
            'code' => [
                'label' => 'Code',
                'tags' => ['developer', 'programming', 'html'],
                'svg' => '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',
            ],
            'search' => [
                'label' => 'Search',
                'tags' => ['find', 'lookup', 'magnifier'],
                'svg' => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
            ],
            'settings' => [
                'label' => 'Settings',
                'tags' => ['cog', 'gear', 'config', 'options'],
                'svg' => '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/>',
            ],
            'user' => [
                'label' => 'User',
                'tags' => ['person', 'profile', 'account'],
                'svg' => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
            ],
            'chart-bar' => [
                'label' => 'Chart Bar',
                'tags' => ['analytics', 'metrics', 'stats'],
                'svg' => '<path d="M3 3v18h18"/><path d="M7 16h3"/><path d="M7 11h8"/><path d="M7 6h12"/>',
            ],
            'check' => [
                'label' => 'Check',
                'tags' => ['success', 'done', 'tick'],
                'svg' => '<polyline points="20 6 9 17 4 12"/>',
            ],
            'alert-circle' => [
                'label' => 'Alert Circle',
                'tags' => ['warning', 'error', 'important'],
                'svg' => '<circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/>',
            ],
            'layers' => [
                'label' => 'Layers',
                'tags' => ['stack', 'design', 'components'],
                'svg' => '<polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>',
            ],
            'terminal' => [
                'label' => 'Terminal',
                'tags' => ['console', 'cli', 'cmd'],
                'svg' => '<polyline points="4 17 10 11 4 5"/><line x1="12" x2="20" y1="19" y2="19"/>',
            ],
            'hard-drive' => [
                'label' => 'Hard Drive',
                'tags' => ['disk', 'storage', 'hardware'],
                'svg' => '<line x1="22" x2="2" y1="12" y2="12"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/><line x1="6" x2="6.01" y1="16" y2="16"/><line x1="10" x2="10.01" y1="16" y2="16"/>',
            ],
            'share-2' => [
                'label' => 'Share',
                'tags' => ['social', 'network', 'send'],
                'svg' => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" x2="15.42" y1="10.68" y2="6.32"/><line x1="8.59" x2="15.42" y1="13.32" y2="17.68"/>',
            ],
        ];
    }
}
