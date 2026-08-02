<?php

namespace App\Livewire\Admin;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Media;
use App\Services\MediaUsageService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Plugins\Posts\Models\Post;
use Plugins\Posts\Models\PostAuthor;

class WordPressCptMigration extends Component
{
    // URL Input
    public $wpUrl = '';

    public $isValidUrl = false;

    // WordPress CPT Discovery
    public $availableCpts = []; // List of CPTs from WordPress

    public $selectedWpCpt = ''; // Selected WordPress CPT slug

    public $wpCptFields = []; // Sample fields from WordPress CPT

    // CMS CPT Selection
    public $cmsCpts = []; // Available CPTs in CMS

    public $selectedCmsCpt = ''; // Selected CMS CPT ID

    public $cmsCptFields = []; // Available fields in CMS CPT

    // Field Mappings
    public $fieldMappings = [];

    // Import Options
    public $downloadFeaturedImage = true;

    public $downloadContentImages = true;

    // Basic Info
    public $totalPosts = 0;

    public $totalPages = 0;

    public $perPage = 10;

    public $previewPosts = [];

    // Polylang / Bilingual State
    public $isPolylang = false;

    public $polylangLanguages = []; // e.g. ['en' => 19, 'id' => 17]

    public $defaultImportLocale = 'en'; // Which language goes to primary columns

    // JetEngine Detection
    public $hasJetEngine = false;

    public $jetEngineHelperInstalled = false;

    public $jetEngineFields = []; // Field definitions from JetEngine helper

    public $showJetEngineSnippet = false;

    // Repeater sub-field mapping
    public $wpRepeaterSubFields = []; // ['meta.key_features_list' => ['kf_image', 'kf_title', 'kf_description']]

    public $repeaterSubMappings = []; // ['meta.key_features_list' => ['cms_sub_name' => 'wp_sub_name', ...]]

    // Post Selection State (Step 3)
    public $selectedLanguages = []; // e.g. ['en', 'id'] — which languages to import

    public $selectedPostIds = []; // Individual post IDs to import

    public $selectAllPosts = true; // Select all by default

    public $fetchAllDone = false;

    public $isBatchImporting = false;

    public $currentBatchIndex = 0;

    public $totalBatchCount = 0;

    public $selectedPostIdChunks = [];

    // Import State
    public $step = 1; // 1: Input URL, 2: Select CPT, 3: Preview & Select, 4: Field Mapping, 5: Results

    public $isLoading = false;

    public $importProgress = 0;

    public $currentPageImporting = 0;

    public $importResults = [];

    public $errorMessage = '';

    public function mount()
    {
        // Load available CMS CPTs
        $this->cmsCpts = CustomPostType::active()->get()->map(function ($cpt) {
            return [
                'id' => (string) $cpt->id,
                'name' => $cpt->name,
                'slug' => $cpt->slug,
                'singular_label' => $cpt->singular_label,
            ];
        })->toArray();

        // If Posts plugin is installed & active, add Posts to dropdown
        if (class_exists(Post::class)) {
            array_unshift($this->cmsCpts, [
                'id' => 'plugin_post',
                'name' => 'Posts (Blog & Articles)',
                'slug' => 'post',
                'singular_label' => 'Post',
                'is_plugin' => true,
            ]);
        }
    }

    public function validateUrl()
    {
        $this->validate([
            'wpUrl' => 'required|url',
        ]);

        // Normalize URL
        $url = rtrim($this->wpUrl, '/');

        // Remove any path after domain
        if (Str::contains($url, '/wp-json')) {
            $url = Str::before($url, '/wp-json');
        }

        $this->wpUrl = $url;
        $this->isValidUrl = true;
    }

    public function fetchCptTypes()
    {
        $this->isLoading = true;
        $this->errorMessage = '';

        try {
            $this->validateUrl();

            // Fetch WordPress REST API index to detect namespaces (JetEngine, Polylang, etc.)
            $indexResponse = Http::timeout(30)->get($this->wpUrl.'/wp-json/');
            if ($indexResponse->successful()) {
                $index = $indexResponse->json();
                $namespaces = $index['namespaces'] ?? [];
                $this->hasJetEngine = in_array('jet-engine/v1', $namespaces) || in_array('jet-engine/v2', $namespaces);
            }

            // Fetch available post types from WordPress
            $response = Http::timeout(30)->get($this->wpUrl.'/wp-json/wp/v2/types');

            if ($response->failed()) {
                throw new \Exception('Failed to fetch post types from WordPress API.');
            }

            $types = $response->json();
            $this->availableCpts = [];

            // Filter out built-in types, keep only custom ones
            $builtInTypes = ['post', 'page', 'attachment', 'revision', 'nav_menu_item', 'wp_block', 'wp_template', 'wp_template_part', 'wp_navigation'];

            foreach ($types as $slug => $type) {
                // Include posts and pages too, plus any custom types
                if (! in_array($slug, ['attachment', 'revision', 'nav_menu_item', 'wp_block', 'wp_template', 'wp_template_part', 'wp_navigation'])) {
                    $this->availableCpts[] = [
                        'slug' => $slug,
                        'name' => $type['name'] ?? $slug,
                        'rest_base' => $type['rest_base'] ?? $slug,
                        'description' => $type['description'] ?? '',
                    ];
                }
            }

            // If JetEngine detected, check if helper endpoint is installed
            if ($this->hasJetEngine) {
                $this->checkJetEngineHelper();
            }

            if (empty($this->availableCpts)) {
                $this->errorMessage = 'No importable post types found.';
            } else {
                $this->step = 2;
            }

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }

        $this->isLoading = false;
    }

    public function selectWpCpt()
    {
        $this->isLoading = true;
        $this->errorMessage = '';

        try {
            if (empty($this->selectedWpCpt)) {
                throw new \Exception('Please select a WordPress post type.');
            }

            // Get the rest_base for the selected CPT
            $selectedCpt = collect($this->availableCpts)->firstWhere('slug', $this->selectedWpCpt);
            $restBase = $selectedCpt['rest_base'] ?? $this->selectedWpCpt;

            // Fetch sample posts to discover fields
            $response = Http::timeout(30)->get($this->wpUrl.'/wp-json/wp/v2/'.$restBase, [
                'per_page' => 10,
                '_embed' => true,
            ]);

            if ($response->failed()) {
                throw new \Exception('Failed to fetch sample data from WordPress.');
            }

            $posts = $response->json();
            $this->totalPosts = (int) $response->header('X-WP-Total', count($posts));
            // Recalculate totalPages using our perPage for efficient API calls
            $this->totalPages = (int) ceil($this->totalPosts / $this->perPage);

            // Detect Polylang: check if posts have 'lang' field across multiple samples
            $this->detectPolylang($posts);

            // Discover available fields from sample post
            $this->wpCptFields = [];
            if (! empty($posts[0])) {
                $samplePost = $posts[0];
                $this->discoverFields($samplePost);
            }

            // If JetEngine helper is installed, fetch JetEngine meta fields for this CPT
            if ($this->hasJetEngine && $this->jetEngineHelperInstalled) {
                $this->fetchJetEngineFields($this->selectedWpCpt, $posts);
            } elseif ($this->hasJetEngine && ! $this->jetEngineHelperInstalled) {
                // Show the snippet notice so user can install it
                $this->showJetEngineSnippet = true;
            }

            // Initialize default field mappings
            $this->initializeFieldMappings();

            // Fetch all posts for preview selection
            $this->fetchAllPostsForPreview($restBase);
            $this->step = 3;

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }

        $this->isLoading = false;
    }

    public function fetchAllPostsForPreview(string $restBase): void
    {
        $this->fetchAllDone = false;
        $this->previewPosts = [];

        // Fetch all posts across all pages
        for ($page = 1; $page <= $this->totalPages; $page++) {
            $response = Http::timeout(60)->get($this->wpUrl.'/wp-json/wp/v2/'.$restBase, [
                'per_page' => $this->perPage,
                'page' => $page,
                '_embed' => true,
            ]);

            if ($response->failed()) {
                continue;
            }

            foreach ($response->json() as $post) {
                $this->previewPosts[] = [
                    'id' => $post['id'],
                    'title' => html_entity_decode(strip_tags($post['title']['rendered'] ?? ''), ENT_QUOTES, 'UTF-8'),
                    'slug' => $post['slug'] ?? '',
                    'lang' => $post['lang'] ?? null,
                    'featured_media' => $post['featured_media'] ?? null,
                    'has_image' => ! empty($post['_embedded']['wp:featuredmedia'][0]['source_url']) || ! empty($post['featured_media']),
                ];
            }
        }

        // Auto-select all languages if Polylang detected
        if ($this->isPolylang) {
            $this->selectedLanguages = array_keys($this->polylangLanguages);
        }

        $this->selectAllPosts = true;
        $this->selectedPostIds = array_column($this->previewPosts, 'id');
        $this->fetchAllDone = true;
    }

    public function toggleLanguage($lang): void
    {
        if (in_array($lang, $this->selectedLanguages)) {
            $this->selectedLanguages = array_values(array_diff($this->selectedLanguages, [$lang]));
        } else {
            $this->selectedLanguages[] = $lang;
        }
        $this->updateSelectedPostIds();
    }

    public function toggleAllPosts(): void
    {
        $this->selectAllPosts = ! $this->selectAllPosts;
        if ($this->selectAllPosts) {
            $this->selectedPostIds = array_column($this->previewPosts, 'id');
        } else {
            $this->selectedPostIds = [];
        }
        $this->updateSelectedPostIds();
    }

    public function togglePost($postId): void
    {
        if (in_array($postId, $this->selectedPostIds)) {
            $this->selectedPostIds = array_values(array_diff($this->selectedPostIds, [$postId]));
        } else {
            $this->selectedPostIds[] = $postId;
        }
        $this->selectAllPosts = count($this->selectedPostIds) === count($this->previewPosts);
    }

    protected function updateSelectedPostIds(): void
    {
        if (! $this->isPolylang || empty($this->selectedLanguages)) {
            // No language filter — select all or none
            return;
        }

        $this->selectedPostIds = collect($this->previewPosts)
            ->filter(fn ($p) => in_array($p['lang'], $this->selectedLanguages))
            ->pluck('id')
            ->toArray();
    }

    protected function discoverFields($samplePost, $prefix = '', $depth = 0)
    {
        $ignoredFields = ['_links', '_embedded', 'guid', 'type', 'link', 'template', 'class_list', '_acf_changed'];
        $metaContexts = ['acf', 'meta', 'jet_meta'];
        $isInsideMeta = $depth > 0;
        $maxDepth = 4; // Prevent infinite recursion on deeply nested structures

        foreach ($samplePost as $key => $value) {
            if (in_array($key, $ignoredFields)) {
                continue;
            }

            $fieldPath = $prefix ? $prefix.'.'.$key : $key;

            if (is_array($value) && ! empty($value)) {
                // Check if it's an associative array with 'rendered' key (like title, content)
                if (isset($value['rendered'])) {
                    $this->wpCptFields[] = [
                        'path' => $fieldPath.'.rendered',
                        'label' => ucfirst(str_replace('_', ' ', $key)).' (rendered)',
                        'sample' => Str::limit(strip_tags($value['rendered']), 50),
                        'source' => $isInsideMeta ? 'jetengine' : 'wordpress',
                    ];
                } elseif (in_array($key, $metaContexts) || $isInsideMeta) {
                    // Inside meta context: check if it's an associative array (object-like)
                    $isAssoc = array_keys($value) !== range(0, count($value) - 1);

                    // Check if this is a JetEngine repeater pattern: keys like 'item-0', 'item-1'...
                    $isJetRepeater = $isAssoc && $this->isJetEngineRepeater($value);

                    if ($isJetRepeater) {
                        // Expose the whole repeater as a single mappable field
                        $firstItem = reset($value);
                        $subFieldNames = is_array($firstItem) ? array_keys($firstItem) : [];
                        $subFieldPreview = implode(', ', $subFieldNames);
                        $itemCount = count($value);

                        $this->wpCptFields[] = [
                            'path' => $fieldPath,
                            'label' => '⚡ '.ucfirst(str_replace('_', ' ', $key)).' (repeater: '.$itemCount.' items)',
                            'sample' => 'Sub-fields: '.$subFieldPreview,
                            'source' => 'jetengine',
                        ];

                        // Store sub-field names for sub-field mapping UI
                        $this->wpRepeaterSubFields[$fieldPath] = $subFieldNames;
                    } elseif ($isAssoc && $depth < $maxDepth) {
                        // Check if this looks like a simple value object (e.g. image: {id, url})
                        $hasOnlyScalarValues = collect($value)->every(fn ($v) => is_scalar($v) || is_null($v));

                        if ($hasOnlyScalarValues && ! in_array($key, $metaContexts)) {
                            // Simple object — expose as JSON and also expose individual sub-fields
                            $this->wpCptFields[] = [
                                'path' => $fieldPath,
                                'label' => ($isInsideMeta ? '⚡ ' : '').ucfirst(str_replace('_', ' ', $key)).' (object)',
                                'sample' => Str::limit(json_encode($value), 50),
                                'source' => 'jetengine',
                            ];
                            // Also expose individual scalar sub-fields (like url, id)
                            foreach ($value as $subKey => $subValue) {
                                if (is_scalar($subValue)) {
                                    $this->wpCptFields[] = [
                                        'path' => $fieldPath.'.'.$subKey,
                                        'label' => ($isInsideMeta ? '⚡ ' : '').ucfirst(str_replace('_', ' ', $key)).' → '.ucfirst($subKey),
                                        'sample' => Str::limit((string) $subValue, 50),
                                        'source' => 'jetengine',
                                    ];
                                }
                            }
                        } else {
                            // Container with nested objects — recurse deeper
                            $this->discoverFields($value, $fieldPath, $depth + 1);
                        }
                    } else {
                        // Sequential array or max depth — expose as JSON
                        $this->wpCptFields[] = [
                            'path' => $fieldPath,
                            'label' => ($isInsideMeta ? '⚡ ' : '').ucfirst(str_replace('_', ' ', $key)).' (array)',
                            'sample' => Str::limit(json_encode($value), 50),
                            'source' => $isInsideMeta ? 'jetengine' : 'wordpress',
                        ];
                    }
                }
            } elseif (is_scalar($value)) {
                $this->wpCptFields[] = [
                    'path' => $fieldPath,
                    'label' => ($isInsideMeta ? '⚡ ' : '').ucfirst(str_replace('_', ' ', $key)),
                    'sample' => Str::limit((string) $value, 50),
                    'source' => $isInsideMeta ? 'jetengine' : 'wordpress',
                ];
            }
        }
    }

    /**
     * Check if an array looks like a JetEngine repeater (keys: item-0, item-1, ...).
     */
    protected function isJetEngineRepeater(array $data): bool
    {
        $keys = array_keys($data);
        if (count($keys) < 1) {
            return false;
        }

        foreach ($keys as $k) {
            if (! preg_match('/^item-\d+$/', (string) $k)) {
                return false;
            }
        }

        // Additionally check that at least the first item is an array (nested structure)
        return is_array(reset($data));
    }

    protected function initializeFieldMappings()
    {
        // Default mappings for common fields
        $this->fieldMappings = [
            'title' => 'title.rendered',
            'slug' => 'slug',
            'content' => 'content.rendered',
            'excerpt' => 'excerpt.rendered',
            'featured_image' => 'featured_media',
            'published_at' => 'date',
        ];

        // Get CMS CPT meta fields
        $this->loadCmsCptFields();
    }

    public function loadCmsCptFields()
    {
        $this->cmsCptFields = [
            ['key' => 'title', 'label' => 'Title'],
            ['key' => 'slug', 'label' => 'Slug'],
            ['key' => 'content', 'label' => 'Content'],
            ['key' => 'excerpt', 'label' => 'Excerpt'],
            ['key' => 'featured_image', 'label' => 'Featured Image'],
            ['key' => 'published_at', 'label' => 'Published Date'],
        ];

        // Add meta fields from selected CMS CPT
        if ($this->selectedCmsCpt === 'plugin_post') {
            $this->cmsCptFields[] = [
                'key' => 'is_featured',
                'label' => 'Featured Post (Sticky)',
                'type' => 'boolean',
            ];
        } elseif ($this->selectedCmsCpt) {
            $cpt = CustomPostType::find($this->selectedCmsCpt);
            if ($cpt && $cpt->metaFields) {
                foreach ($cpt->metaFields as $metaField) {
                    $fieldInfo = [
                        'key' => 'meta.'.$metaField->name,
                        'label' => $metaField->label ?? $metaField->name,
                        'type' => $metaField->type,
                    ];

                    // For repeater fields, include sub-field definitions
                    if ($metaField->type === 'repeater' && ! empty($metaField->options['repeater_fields'])) {
                        $fieldInfo['sub_fields'] = array_map(function ($sf) {
                            return [
                                'name' => $sf['name'],
                                'label' => $sf['label'] ?? $sf['name'],
                                'type' => $sf['type'] ?? 'text',
                            ];
                        }, $metaField->options['repeater_fields']);
                    }

                    $this->cmsCptFields[] = $fieldInfo;
                }
            }
        }
    }

    public function updatedSelectedCmsCpt()
    {
        $this->loadCmsCptFields();
    }

    public function updateFieldMapping($cmsField, $wpField)
    {
        $this->fieldMappings[$cmsField] = $wpField;
    }

    /**
     * Generic updated hook — catches all property changes.
     * Used to auto-initialize repeater sub-field mappings when a field mapping changes.
     */
    public function updated($property, $value)
    {
        // Only handle fieldMappings changes
        if (! Str::startsWith($property, 'fieldMappings.')) {
            return;
        }

        // Extract the CMS field key (e.g. 'meta.key_features_list' from 'fieldMappings.meta.key_features_list')
        $cmsFieldKey = Str::after($property, 'fieldMappings.');

        if (empty($value)) {
            // Mapping removed — clean up sub-mappings
            unset($this->repeaterSubMappings[$cmsFieldKey]);

            return;
        }

        // Check if CMS field is a repeater
        $cmsField = collect($this->cmsCptFields)->firstWhere('key', $cmsFieldKey);
        if (! $cmsField || ($cmsField['type'] ?? '') !== 'repeater') {
            return;
        }

        // Check if WP field is a discovered repeater
        $wpSubFields = $this->wpRepeaterSubFields[$value] ?? null;
        if (! $wpSubFields) {
            return;
        }

        // Get CMS sub-fields
        $cmsSubFields = $cmsField['sub_fields'] ?? [];
        if (empty($cmsSubFields)) {
            return;
        }

        // Auto-initialize sub-field mappings with fuzzy name matching
        $mapping = [];

        foreach ($cmsSubFields as $cmsSub) {
            $cmsName = $cmsSub['name'];
            $bestMatch = $this->findBestSubFieldMatch($cmsName, $wpSubFields);
            $mapping[$cmsName] = $bestMatch ?? '';
        }

        $this->repeaterSubMappings[$cmsFieldKey] = $mapping;
    }

    /**
     * Find the best matching WP sub-field name for a CMS sub-field name.
     * Matches exactly first, then by normalized similarity.
     */
    protected function findBestSubFieldMatch(string $cmsName, array $wpFieldNames): ?string
    {
        // Exact match
        if (in_array($cmsName, $wpFieldNames)) {
            return $cmsName;
        }

        // Normalize: remove common prefixes/suffixes, lowercase, remove underscores
        $normalize = fn ($name) => strtolower(preg_replace('/^(kf_|key_|field_|meta_|cf_)/', '', $name));
        $cmsNorm = $normalize($cmsName);

        $bestScore = 0;
        $bestMatch = null;

        foreach ($wpFieldNames as $wpName) {
            $wpNorm = $normalize($wpName);

            // Exact normalized match
            if ($cmsNorm === $wpNorm) {
                return $wpName;
            }

            // Similarity score
            similar_text($cmsNorm, $wpNorm, $percent);
            if ($percent > $bestScore && $percent >= 50) {
                $bestScore = $percent;
                $bestMatch = $wpName;
            }
        }

        return $bestMatch;
    }

    protected function detectPolylang(array $samplePosts): void
    {
        $this->isPolylang = false;
        $this->polylangLanguages = [];

        // Check if posts have a 'lang' field (Polylang adds this to REST response)
        foreach ($samplePosts as $post) {
            if (! empty($post['lang'])) {
                $lang = $post['lang'];
                $this->polylangLanguages[$lang] = ($this->polylangLanguages[$lang] ?? 0) + 1;
            }
        }

        if (count($this->polylangLanguages) > 1) {
            $this->isPolylang = true;
            // Default import locale: use CMS default or first language found
            $defaultLocale = CptEntry::defaultLocale();
            $this->defaultImportLocale = isset($this->polylangLanguages[$defaultLocale])
                ? $defaultLocale
                : array_key_first($this->polylangLanguages);
        }
    }

    /**
     * Check if the JetEngine helper endpoint is installed on the WordPress site.
     */
    protected function checkJetEngineHelper(): void
    {
        try {
            $response = Http::timeout(10)->get($this->wpUrl.'/wp-json/cdt-migrate/v1/jet-fields');
            $this->jetEngineHelperInstalled = $response->successful();
        } catch (\Exception $e) {
            $this->jetEngineHelperInstalled = false;
        }
    }

    /**
     * Fetch JetEngine meta fields for a post type using the helper endpoint.
     */
    protected function fetchJetEngineFields(string $postType, array $samplePosts): void
    {
        try {
            // First, get field definitions from the helper
            $response = Http::timeout(15)->get($this->wpUrl.'/wp-json/cdt-migrate/v1/jet-fields', [
                'post_type' => $postType,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $fieldDefs = $data['fields'] ?? [];

                $this->jetEngineFields = $fieldDefs;

                // Now fetch a sample post with JetEngine meta included
                $metaResponse = Http::timeout(15)->get($this->wpUrl.'/wp-json/cdt-migrate/v1/jet-posts', [
                    'post_type' => $postType,
                    'per_page' => 1,
                ]);

                $sampleValues = [];
                if ($metaResponse->successful()) {
                    $jetPosts = $metaResponse->json();
                    if (! empty($jetPosts['posts'][0]['jet_meta'])) {
                        $sampleValues = $jetPosts['posts'][0]['jet_meta'];
                    }
                }

                // Add JetEngine fields to the discoverable fields list
                foreach ($fieldDefs as $field) {
                    $fieldName = $field['name'] ?? '';
                    if (empty($fieldName)) {
                        continue;
                    }

                    $sampleValue = $sampleValues[$fieldName] ?? '';
                    if (is_array($sampleValue)) {
                        $sampleValue = json_encode($sampleValue);
                    }

                    $this->wpCptFields[] = [
                        'path' => 'jet_meta.'.$fieldName,
                        'label' => '⚡ '.($field['title'] ?? ucfirst(str_replace('_', ' ', $fieldName))),
                        'sample' => Str::limit((string) $sampleValue, 50),
                        'source' => 'jetengine',
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch JetEngine fields', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Refresh JetEngine fields after user installs the helper snippet.
     */
    public function refreshJetEngineFields(): void
    {
        $this->checkJetEngineHelper();

        if ($this->jetEngineHelperInstalled) {
            $this->showJetEngineSnippet = false;

            // Remove existing JetEngine fields
            $this->wpCptFields = array_values(array_filter($this->wpCptFields, function ($field) {
                return ($field['source'] ?? 'wordpress') !== 'jetengine';
            }));

            // Fetch fresh JetEngine fields
            $selectedCpt = collect($this->availableCpts)->firstWhere('slug', $this->selectedWpCpt);
            $restBase = $selectedCpt['rest_base'] ?? $this->selectedWpCpt;

            $response = Http::timeout(30)->get($this->wpUrl.'/wp-json/wp/v2/'.$restBase, [
                'per_page' => 10,
                '_embed' => true,
            ]);

            $posts = $response->successful() ? $response->json() : [];

            $this->fetchJetEngineFields($this->selectedWpCpt, $posts);
        } else {
            $this->errorMessage = 'Helper endpoint not found. Please make sure the code snippet is installed and active on the WordPress site.';
        }
    }

    /**
     * Toggle JetEngine snippet visibility.
     */
    public function toggleJetEngineSnippet(): void
    {
        $this->showJetEngineSnippet = ! $this->showJetEngineSnippet;
    }

    /**
     * Get the PHP code snippet to install on WordPress for JetEngine REST API support.
     */
    public static function getJetEngineHelperSnippet(): string
    {
        return <<<'SNIPPET'
/**
 * CDT Migration Helper - Exposes JetEngine meta fields via REST API.
 * Add this as a Code Snippet in WordPress (or in functions.php).
 * Can be safely removed after migration is complete.
 */
add_action('rest_api_init', function () {
    // Endpoint: List JetEngine field definitions for a post type
    register_rest_route('cdt-migrate/v1', '/jet-fields', [
        'methods'  => 'GET',
        'callback' => function (WP_REST_Request $request) {
            $post_type = $request->get_param('post_type');

            if (! class_exists('Jet_Engine') || ! jet_engine()->meta_boxes) {
                return new WP_REST_Response(['error' => 'JetEngine not active'], 404);
            }

            $fields = [];

            // Get meta boxes registered for this post type
            $meta_boxes = jet_engine()->meta_boxes->get_registered_fields();

            foreach ($meta_boxes as $meta_box) {
                $box_post_types = $meta_box['args']['allowed_post_type'] ?? [];

                if (! empty($post_type) && ! empty($box_post_types) && ! in_array($post_type, $box_post_types)) {
                    continue;
                }

                foreach ($meta_box['meta_fields'] ?? [] as $field) {
                    $fields[] = [
                        'name'  => $field['name'] ?? '',
                        'title' => $field['title'] ?? $field['name'] ?? '',
                        'type'  => $field['type'] ?? 'text',
                    ];
                }
            }

            return new WP_REST_Response(['fields' => $fields, 'post_type' => $post_type], 200);
        },
        'permission_callback' => '__return_true',
    ]);

    // Endpoint: Get posts with their JetEngine meta values
    register_rest_route('cdt-migrate/v1', '/jet-posts', [
        'methods'  => 'GET',
        'callback' => function (WP_REST_Request $request) {
            $post_type = $request->get_param('post_type') ?: 'post';
            $per_page  = (int) ($request->get_param('per_page') ?: 10);
            $page      = (int) ($request->get_param('page') ?: 1);
            $post_ids  = $request->get_param('ids'); // Optional: comma-separated IDs

            if (! class_exists('Jet_Engine') || ! jet_engine()->meta_boxes) {
                return new WP_REST_Response(['error' => 'JetEngine not active'], 404);
            }

            // Get field names for this post type
            $field_names = [];
            $meta_boxes = jet_engine()->meta_boxes->get_registered_fields();
            foreach ($meta_boxes as $meta_box) {
                $box_post_types = $meta_box['args']['allowed_post_type'] ?? [];
                if (! empty($box_post_types) && ! in_array($post_type, $box_post_types)) {
                    continue;
                }
                foreach ($meta_box['meta_fields'] ?? [] as $field) {
                    if (! empty($field['name'])) {
                        $field_names[] = $field['name'];
                    }
                }
            }

            $query_args = [
                'post_type'      => $post_type,
                'posts_per_page' => min($per_page, 100),
                'paged'          => $page,
                'post_status'    => 'publish',
            ];

            if ($post_ids) {
                $query_args['post__in'] = array_map('intval', explode(',', $post_ids));
                $query_args['orderby']  = 'post__in';
            }

            $query = new WP_Query($query_args);
            $posts = [];

            foreach ($query->posts as $post) {
                $jet_meta = [];
                foreach ($field_names as $name) {
                    $jet_meta[$name] = get_post_meta($post->ID, $name, true);
                }
                $posts[] = [
                    'id'       => $post->ID,
                    'title'    => $post->post_title,
                    'slug'     => $post->post_name,
                    'jet_meta' => $jet_meta,
                ];
            }

            return new WP_REST_Response([
                'posts'       => $posts,
                'total'       => $query->found_posts,
                'total_pages' => $query->max_num_pages,
            ], 200);
        },
        'permission_callback' => '__return_true',
    ]);
});
SNIPPET;
    }

    public function importAllPosts()
    {
        $this->errorMessage = '';

        if (empty($this->selectedCmsCpt)) {
            $this->errorMessage = 'Please select a target CMS post type.';

            return;
        }

        if (empty($this->selectedPostIds)) {
            $this->errorMessage = 'Please select at least one post to import.';

            return;
        }

        $this->importResults = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
            'translated' => 0,
            'skipped_posts' => [],
            'errors' => [],
        ];

        // 5 posts per batch chunk to prevent HTTP execution timeout
        $this->selectedPostIdChunks = array_chunk($this->selectedPostIds, 5);
        $this->currentBatchIndex = 0;
        $this->totalBatchCount = count($this->selectedPostIdChunks);
        $this->importProgress = 0;
        $this->isBatchImporting = true;
        $this->step = 5;

        $this->processNextBatch();
    }

    public function processNextBatch()
    {
        if (! $this->isBatchImporting || $this->currentBatchIndex >= $this->totalBatchCount) {
            $this->isBatchImporting = false;
            $this->importProgress = 100;

            return;
        }

        $batchIds = $this->selectedPostIdChunks[$this->currentBatchIndex] ?? [];
        if (empty($batchIds)) {
            $this->currentBatchIndex++;

            return;
        }

        $selectedCpt = collect($this->availableCpts)->firstWhere('slug', $this->selectedWpCpt);
        $restBase = $selectedCpt['rest_base'] ?? $this->selectedWpCpt;

        try {
            $response = Http::timeout(60)->get($this->wpUrl.'/wp-json/wp/v2/'.$restBase, [
                'include' => implode(',', $batchIds),
                'per_page' => count($batchIds),
                '_embed' => true,
            ]);

            if ($response->successful()) {
                $posts = $response->json();
                if ($this->hasJetEngine && $this->jetEngineHelperInstalled) {
                    $posts = $this->enrichPostsWithJetMeta($posts);
                }

                foreach ($posts as $wpPost) {
                    $this->importOrSkip($wpPost);
                }
            }
        } catch (\Exception $e) {
            Log::error('Batch import error', ['batch' => $this->currentBatchIndex, 'error' => $e->getMessage()]);
        }

        $this->currentBatchIndex++;
        $this->importProgress = (int) round(($this->currentBatchIndex / $this->totalBatchCount) * 100);

        if ($this->currentBatchIndex >= $this->totalBatchCount) {
            $this->isBatchImporting = false;
            $this->importProgress = 100;
            app(MediaUsageService::class)->clearCache();
        }
    }

    protected function importMonolingualPosts(string $restBase): void
    {
        $selectedIds = array_flip($this->selectedPostIds);

        for ($page = 1; $page <= $this->totalPages; $page++) {
            $this->currentPageImporting = $page;

            $response = Http::timeout(60)->get($this->wpUrl.'/wp-json/wp/v2/'.$restBase, [
                'per_page' => $this->perPage,
                'page' => $page,
                '_embed' => true,
            ]);

            if ($response->failed()) {
                continue;
            }

            $posts = $response->json();

            // If JetEngine helper is available, enrich posts with JetEngine meta
            if ($this->hasJetEngine && $this->jetEngineHelperInstalled) {
                $posts = $this->enrichPostsWithJetMeta($posts);
            }

            foreach ($posts as $wpPost) {
                if (! isset($selectedIds[$wpPost['id']])) {
                    continue; // Not selected by user
                }
                $this->importOrSkip($wpPost);
            }

            $this->importProgress = round(($page / $this->totalPages) * 100);
        }
    }

    protected function importPolylangPosts(string $restBase): void
    {
        $selectedIds = array_flip($this->selectedPostIds);

        // Fetch ALL posts (filtered by selected IDs) to pair them by language
        $allPosts = [];
        for ($page = 1; $page <= $this->totalPages; $page++) {
            $response = Http::timeout(60)->get($this->wpUrl.'/wp-json/wp/v2/'.$restBase, [
                'per_page' => $this->perPage,
                'page' => $page,
                '_embed' => true,
            ]);
            if (! $response->failed()) {
                $pagePosts = $response->json();

                // Enrich with JetEngine meta if available
                if ($this->hasJetEngine && $this->jetEngineHelperInstalled) {
                    $pagePosts = $this->enrichPostsWithJetMeta($pagePosts);
                }

                foreach ($pagePosts as $post) {
                    if (isset($selectedIds[$post['id']])) {
                        $allPosts[] = $post;
                    }
                }
            }
            $this->importProgress = round(($page / $this->totalPages) * 50); // 0–50%: fetching
        }

        // Group posts: keyed by slug (or title) → collect language variants
        $groups = [];
        foreach ($allPosts as $post) {
            $key = $post['slug'] ?? Str::slug($post['title']['rendered'] ?? '');
            if (! isset($groups[$key])) {
                $groups[$key] = [];
            }
            $groups[$key][] = $post;
        }

        $this->currentPageImporting = 0;
        $total = count($groups);
        $done = 0;

        foreach ($groups as $slugKey => $variants) {
            $done++;
            $this->currentPageImporting = $done;

            // Find the default-locale post first
            $defaultPost = null;
            $otherPosts = [];
            foreach ($variants as $post) {
                if (($post['lang'] ?? '') === $this->defaultImportLocale) {
                    $defaultPost = $post;
                } else {
                    $otherPosts[] = $post;
                }
            }
            // If no default-locale post found, use first available
            if (! $defaultPost) {
                $defaultPost = array_shift($variants);
                $otherPosts = $variants;
            }

            // Import the default post as primary entry
            $result = $this->importOrSkip($defaultPost);
            if ($result === 'success' && ! empty($otherPosts)) {
                if ($this->selectedCmsCpt === 'plugin_post') {
                    $entry = Post::where('slug', $defaultPost['slug'] ?? Str::slug($defaultPost['title']['rendered'] ?? ''))->first();
                } else {
                    $entry = CptEntry::where('slug', $defaultPost['slug'] ?? Str::slug($defaultPost['title']['rendered'] ?? ''))
                        ->where('post_type_id', $this->selectedCmsCpt)
                        ->first();
                }

                if ($entry) {
                    // Add translations for other languages
                    foreach ($otherPosts as $otherPost) {
                        $otherLang = $otherPost['lang'] ?? 'other';
                        $transTitle = html_entity_decode(strip_tags($this->getWpFieldValue($otherPost, 'title.rendered') ?? ''), ENT_QUOTES, 'UTF-8');
                        $transSlug = $otherPost['slug'] ?? Str::slug($transTitle);
                        $transContent = $this->getWpFieldValue($otherPost, 'content.rendered') ?? '';

                        // Append locale to slug to avoid conflicts
                        $transSlug = $this->localizeSlug($transSlug, $otherLang);

                        $entry->setTranslation('title', $otherLang, $transTitle);
                        $entry->setTranslation('slug', $otherLang, $transSlug);
                        $entry->setTranslation('content', $otherLang, $transContent);
                        $entry->save();

                        $this->importResults['translated']++;
                    }
                }
            }

            $this->importProgress = 50 + round(($done / $total) * 50); // 50–100%: processing
        }
    }

    /**
     * Append locale to slug to ensure uniqueness across translations.
     */
    protected function localizeSlug(string $slug, string $locale): string
    {
        if (str_ends_with($slug, '-'.$locale)) {
            return $slug;
        }

        return $slug.'-'.$locale;
    }

    /**
     * Enrich standard WP REST API posts with JetEngine meta values from the helper endpoint.
     */
    protected function enrichPostsWithJetMeta(array $posts): array
    {
        if (empty($posts)) {
            return $posts;
        }

        $ids = array_column($posts, 'id');
        if (empty($ids)) {
            return $posts;
        }

        try {
            $response = Http::timeout(30)->get($this->wpUrl.'/wp-json/cdt-migrate/v1/jet-posts', [
                'post_type' => $this->selectedWpCpt,
                'ids' => implode(',', $ids),
                'per_page' => count($ids),
            ]);

            if ($response->successful()) {
                $jetData = $response->json();
                $jetPostsById = [];

                foreach ($jetData['posts'] ?? [] as $jetPost) {
                    $jetPostsById[$jetPost['id']] = $jetPost['jet_meta'] ?? [];
                }

                // Merge jet_meta into each post
                foreach ($posts as &$post) {
                    if (isset($jetPostsById[$post['id']])) {
                        $post['jet_meta'] = $jetPostsById[$post['id']];
                    }
                }
                unset($post);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to enrich posts with JetEngine meta', ['error' => $e->getMessage()]);
        }

        return $posts;
    }

    protected function importOrSkip(array $wpPost): string
    {
        try {
            $result = $this->importSinglePost($wpPost);

            if ($result === 'success') {
                $this->importResults['success']++;
            } elseif ($result === 'skipped') {
                $this->importResults['skipped']++;
                $this->importResults['skipped_posts'][] = [
                    'title' => $this->getWpFieldValue($wpPost, 'title.rendered') ?? 'Unknown',
                    'slug' => $wpPost['slug'] ?? '',
                    'reason' => 'Slug already exists',
                ];
            }

            return $result;
        } catch (\Exception $e) {
            $this->importResults['failed']++;
            $this->importResults['errors'][] = [
                'title' => $this->getWpFieldValue($wpPost, 'title.rendered') ?? 'Unknown',
                'error' => $e->getMessage(),
            ];

            return 'failed';
        }
    }

    protected function importSinglePost($wpPost)
    {
        // Get mapped values
        $title = html_entity_decode(strip_tags($this->getWpFieldValue($wpPost, $this->fieldMappings['title'] ?? 'title.rendered') ?? ''), ENT_QUOTES, 'UTF-8');
        $slug = $this->getWpFieldValue($wpPost, $this->fieldMappings['slug'] ?? 'slug') ?? Str::slug($title);

        // Check if entry with same slug already exists
        if ($this->selectedCmsCpt === 'plugin_post') {
            if (Post::where('slug', $slug)->exists()) {
                return 'skipped';
            }
        } elseif (CptEntry::where('slug', $slug)->where('post_type_id', $this->selectedCmsCpt)->exists()) {
            return 'skipped';
        }

        // Get content
        $content = $this->getWpFieldValue($wpPost, $this->fieldMappings['content'] ?? 'content.rendered') ?? '';

        // Process content images if enabled
        if ($this->downloadContentImages) {
            $content = $this->processContentImages($content);
        }

        // Get excerpt
        $excerpt = strip_tags($this->getWpFieldValue($wpPost, $this->fieldMappings['excerpt'] ?? 'excerpt.rendered') ?? '');
        $excerpt = html_entity_decode(trim($excerpt), ENT_QUOTES, 'UTF-8');

        // Handle published date
        $publishedAt = Carbon::parse($this->getWpFieldValue($wpPost, $this->fieldMappings['published_at'] ?? 'date') ?? now());

        // Handle featured image
        $featuredImage = null;
        if ($this->downloadFeaturedImage) {
            $featuredImage = $this->getFeaturedImage($wpPost);
        }

        // Handle Post Plugin import
        if ($this->selectedCmsCpt === 'plugin_post') {
            $authorId = null;
            if (class_exists(PostAuthor::class)) {
                $author = PostAuthor::first();
                if (! $author && auth()->check()) {
                    $user = auth()->user();
                    $author = PostAuthor::create([
                        'name' => $user->name,
                        'slug' => Str::slug($user->name),
                        'email' => $user->email,
                    ]);
                }
                $authorId = $author?->id;
            }

            $isFeatured = (bool) ($this->getWpFieldValue($wpPost, $this->fieldMappings['is_featured'] ?? 'sticky') ?? false);

            $post = Post::create([
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'excerpt' => $excerpt,
                'featured_image' => $featuredImage,
                'status' => 'published',
                'published_at' => $publishedAt,
                'author_id' => $authorId,
                'is_featured' => $isFeatured,
            ]);

            $post->forceFill(['created_at' => $publishedAt])->save();

            return 'success';
        }

        // Build meta data from mapped meta fields
        $meta = [
            'wp_original_id' => $wpPost['id'],
            'wp_original_url' => $wpPost['link'] ?? null,
        ];

        // Load CMS CPT meta field definitions for type-aware conversion
        $cmsMetaFieldDefs = [];
        if ($this->selectedCmsCpt) {
            $cpt = CustomPostType::find($this->selectedCmsCpt);
            if ($cpt) {
                foreach ($cpt->metaFields as $mf) {
                    $cmsMetaFieldDefs[$mf->name] = $mf;
                }
            }
        }

        foreach ($this->fieldMappings as $cmsField => $wpField) {
            if (Str::startsWith($cmsField, 'meta.')) {
                $metaKey = Str::after($cmsField, 'meta.');
                $rawValue = $this->getWpFieldValue($wpPost, $wpField);
                $cmsFieldDef = $cmsMetaFieldDefs[$metaKey] ?? null;

                // Auto-convert based on CMS field type
                $meta[$metaKey] = $this->convertMetaValue($rawValue, $cmsFieldDef, $cmsField);
            }
        }

        // Create the CPT entry
        $entry = CptEntry::create([
            'post_type_id' => $this->selectedCmsCpt,
            'title' => $title,
            'slug' => $this->ensureUniqueSlug($slug),
            'content' => $content,
            'excerpt' => $excerpt,
            'featured_image' => $featuredImage,
            'status' => 'published',
            'published_at' => $publishedAt,
            'author_id' => auth()->id(),
            'meta' => $meta,
        ]);

        // Force set created_at to preserve original date
        $entry->created_at = $publishedAt;
        $entry->save();

        return 'success';
    }

    /**
     * Convert a WP meta value to the appropriate CMS format based on the CMS field type.
     */
    protected function convertMetaValue($rawValue, ?MetaField $cmsFieldDef, string $cmsFieldKey = '')
    {
        if ($rawValue === null) {
            return null;
        }

        if (! $cmsFieldDef) {
            // No CMS field definition — return raw value, but still handle JetEngine repeater format
            if (is_array($rawValue) && $this->isJetEngineRepeater($rawValue)) {
                return array_values(array_map(function ($item) {
                    return is_array($item) ? $this->convertRepeaterItem($item, []) : $item;
                }, $rawValue));
            }

            return $rawValue;
        }

        switch ($cmsFieldDef->type) {
            case 'repeater':
                return $this->convertJetEngineRepeaterToCmsFormat($rawValue, $cmsFieldDef, $cmsFieldKey);

            case 'media':
                return $this->convertMediaValue($rawValue);

            default:
                // For scalar types, if value is array with 'rendered' key, extract it
                if (is_array($rawValue) && isset($rawValue['rendered'])) {
                    return strip_tags($rawValue['rendered']);
                }

                return $rawValue;
        }
    }

    /**
     * Convert a JetEngine repeater ({item-0: {...}, item-1: {...}}) to CMS array format ([{...}, {...}]).
     * Also handles sub-field media downloads, name mapping, and type conversion.
     */
    protected function convertJetEngineRepeaterToCmsFormat($rawValue, MetaField $cmsFieldDef, string $cmsFieldKey = ''): array
    {
        // Get CMS repeater sub-field definitions
        $cmsSubFields = $cmsFieldDef->options['repeater_fields'] ?? [];
        $cmsSubFieldTypes = [];
        foreach ($cmsSubFields as $sf) {
            $cmsSubFieldTypes[$sf['name']] = $sf['type'] ?? 'text';
        }

        // Get sub-field name mapping (CMS name => WP name)
        // We need to invert it to (WP name => CMS name) for conversion
        $subMapping = $this->repeaterSubMappings[$cmsFieldKey] ?? [];
        $wpToCmsNameMap = [];
        foreach ($subMapping as $cmsName => $wpName) {
            if (! empty($wpName)) {
                $wpToCmsNameMap[$wpName] = $cmsName;
            }
        }

        // Convert JetEngine format to array
        $items = [];
        if ($this->isJetEngineRepeater($rawValue)) {
            $items = array_values($rawValue);
        } elseif (is_array($rawValue) && array_is_list($rawValue)) {
            $items = $rawValue;
        } else {
            $items = [$rawValue];
        }

        // Convert each item
        $result = [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $result[] = $this->convertRepeaterItem($item, $cmsSubFieldTypes, $wpToCmsNameMap);
        }

        return $result;
    }

    /**
     * Convert a single repeater item, handling media sub-fields and name remapping.
     */
    protected function convertRepeaterItem(array $item, array $cmsSubFieldTypes, array $wpToCmsNameMap = []): array
    {
        $converted = [];

        foreach ($item as $wpSubName => $subFieldValue) {
            // Resolve the CMS field name (use mapping if available, else keep original)
            $cmsSubName = $wpToCmsNameMap[$wpSubName] ?? $wpSubName;

            // Get the CMS type for the resolved name
            $cmsType = $cmsSubFieldTypes[$cmsSubName] ?? null;

            if ($cmsType === 'media' || $this->looksLikeMediaObject($subFieldValue)) {
                $converted[$cmsSubName] = $this->convertMediaValue($subFieldValue);
            } elseif (is_array($subFieldValue) && isset($subFieldValue['rendered'])) {
                $converted[$cmsSubName] = strip_tags($subFieldValue['rendered']);
            } else {
                $converted[$cmsSubName] = $subFieldValue;
            }
        }

        return $converted;
    }

    /**
     * Convert a WP media value (URL string or {id, url} object) to a local path.
     * Downloads the image and returns the local storage path.
     */
    protected function convertMediaValue($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $imageUrl = null;

        if (is_string($value)) {
            // Direct URL string
            if (filter_var($value, FILTER_VALIDATE_URL) || Str::startsWith($value, '//')) {
                $imageUrl = $value;
            } else {
                return $value; // Already a local path or non-URL string
            }
        } elseif (is_array($value)) {
            // JetEngine media object: {id: 123, url: "https://..."}
            $imageUrl = $value['url'] ?? $value['source_url'] ?? null;
        }

        if (! $imageUrl) {
            return null;
        }

        try {
            return $this->downloadImage($imageUrl);
        } catch (\Exception $e) {
            Log::warning('Failed to download media during meta conversion', ['url' => $imageUrl, 'error' => $e->getMessage()]);

            // Fallback: return the original URL so data isn't lost
            return $imageUrl;
        }
    }

    /**
     * Check if a value looks like a WP/JetEngine media object ({id: int, url: string}).
     */
    protected function looksLikeMediaObject($value): bool
    {
        return is_array($value)
            && isset($value['url'])
            && is_string($value['url'])
            && (isset($value['id']) && is_numeric($value['id']));
    }

    protected function getWpFieldValue($wpPost, $fieldPath)
    {
        if (empty($fieldPath)) {
            return null;
        }

        $parts = explode('.', $fieldPath);
        $value = $wpPost;

        foreach ($parts as $part) {
            if (is_array($value) && isset($value[$part])) {
                $value = $value[$part];
            } else {
                return null;
            }
        }

        return $value;
    }

    protected function processContentImages($content)
    {
        // Remove srcset and sizes attributes
        $content = preg_replace('/\s+srcset=["\'][^"\']*["\']/', '', $content);
        $content = preg_replace('/\s+sizes=["\'][^"\']*["\']/', '', $content);

        // Find all img tags and their src attributes
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches);

        if (empty($matches[1])) {
            return $content;
        }

        $replacements = [];

        foreach ($matches[1] as $originalUrl) {
            if (Str::startsWith($originalUrl, '/storage/') || Str::startsWith($originalUrl, '/media/')) {
                continue;
            }

            if (Str::startsWith($originalUrl, '/') && ! Str::startsWith($originalUrl, '//')) {
                continue;
            }

            if (Str::startsWith($originalUrl, 'data:')) {
                continue;
            }

            try {
                $newPath = $this->downloadImage($originalUrl);

                if ($newPath && $newPath !== $originalUrl && ! Str::startsWith($newPath, 'http')) {
                    $newUrl = '/storage/'.$newPath;
                    $replacements[$originalUrl] = $newUrl;
                }
            } catch (\Exception $e) {
                Log::warning('Failed to download content image: '.$originalUrl);
            }
        }

        foreach ($replacements as $oldUrl => $newUrl) {
            $content = str_replace($oldUrl, $newUrl, $content);
        }

        return $content;
    }

    protected function getFeaturedImage($wpPost)
    {
        // Try to get from embedded data first
        if (isset($wpPost['_embedded']['wp:featuredmedia'][0]['source_url'])) {
            $imageUrl = $wpPost['_embedded']['wp:featuredmedia'][0]['source_url'];

            return $this->downloadImage($imageUrl);
        }

        // If no embedded media, try to fetch it
        if (! empty($wpPost['featured_media'])) {
            try {
                $response = Http::timeout(10)->get($this->wpUrl.'/wp-json/wp/v2/media/'.$wpPost['featured_media']);
                if ($response->successful()) {
                    $media = $response->json();
                    $imageUrl = $media['source_url'] ?? null;

                    if ($imageUrl) {
                        return $this->downloadImage($imageUrl);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch featured media: '.$e->getMessage());
            }
        }

        return null;
    }

    protected function downloadImage($imageUrl)
    {
        try {
            if (Str::startsWith($imageUrl, '//')) {
                $imageUrl = 'https:'.$imageUrl;
            }

            $response = Http::timeout(60)->withOptions([
                'verify' => false,
            ])->get($imageUrl);

            if (! $response->successful()) {
                return null;
            }

            $urlPath = parse_url($imageUrl, PHP_URL_PATH);
            $originalFilename = basename($urlPath);
            $extension = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));

            if (empty($extension) || ! in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                $contentType = $response->header('Content-Type');
                $extension = $this->getExtensionFromMimeType($contentType) ?? 'jpg';
            }

            $filename = 'wp-cpt-import-'.time().'-'.Str::random(8).'.'.$extension;
            $path = config('media.path', 'media').'/'.$filename;

            $disk = Storage::disk(config('media.disk', 'public'));

            $directory = dirname($path);
            if (! $disk->exists($directory)) {
                $disk->makeDirectory($directory);
            }

            $disk->put($path, $response->body());

            if (! $disk->exists($path)) {
                return null;
            }

            $fullPath = $disk->path($path);
            $imageInfo = @getimagesize($fullPath);
            $fileSize = $disk->size($path);

            $mimeType = $imageInfo['mime'] ?? $this->getMimeTypeFromExtension($extension);

            Media::create([
                'filename' => $filename,
                'original_filename' => $originalFilename ?: $filename,
                'mime_type' => $mimeType,
                'file_extension' => $extension,
                'size' => $fileSize,
                'path' => $path,
                'width' => $imageInfo[0] ?? null,
                'height' => $imageInfo[1] ?? null,
                'alt_text' => null,
                'title' => pathinfo($originalFilename ?: $filename, PATHINFO_FILENAME),
                'description' => 'Imported from WordPress CPT',
                'uploaded_by' => auth()->id(),
            ]);

            return $path;

        } catch (\Exception $e) {
            Log::error('WordPress CPT image download failed', ['url' => $imageUrl, 'error' => $e->getMessage()]);

            return null;
        }
    }

    protected function getExtensionFromMimeType($mimeType)
    {
        $map = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
        ];

        return $map[$mimeType] ?? null;
    }

    protected function getMimeTypeFromExtension($extension)
    {
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
        ];

        return $mimeTypes[$extension] ?? 'image/jpeg';
    }

    protected function ensureUniqueSlug($slug)
    {
        $originalSlug = $slug;
        $counter = 1;

        while (CptEntry::where('slug', $slug)->where('post_type_id', $this->selectedCmsCpt)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function resetMigration()
    {
        $this->step = 1;
        $this->wpUrl = '';
        $this->availableCpts = [];
        $this->selectedWpCpt = '';
        $this->wpCptFields = [];
        $this->selectedCmsCpt = '';
        $this->fieldMappings = [];
        $this->totalPosts = 0;
        $this->totalPages = 0;
        $this->previewPosts = [];
        $this->selectedLanguages = [];
        $this->selectedPostIds = [];
        $this->selectAllPosts = true;
        $this->fetchAllDone = false;
        $this->isPolylang = false;
        $this->polylangLanguages = [];
        $this->hasJetEngine = false;
        $this->jetEngineHelperInstalled = false;
        $this->jetEngineFields = [];
        $this->showJetEngineSnippet = false;
        $this->wpRepeaterSubFields = [];
        $this->repeaterSubMappings = [];
        $this->importProgress = 0;
        $this->currentPageImporting = 0;
        $this->importResults = [];
        $this->errorMessage = '';
    }

    public function goBack()
    {
        if ($this->step > 1) {
            if ($this->step === 3) {
                $this->step = 2;
                $this->previewPosts = [];
                $this->fetchAllDone = false;
            } else {
                $this->step--;
            }
        }
    }

    public function continueToFieldMapping()
    {
        $this->step = 4;
    }

    public function render()
    {
        return view('livewire.admin.wordpress-cpt-migration');
    }
}
