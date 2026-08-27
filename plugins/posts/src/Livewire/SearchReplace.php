<?php

namespace Plugins\Posts\Livewire;

use App\Services\CacheManager;
use Livewire\Component;
use Plugins\Posts\Models\Category;
use Plugins\Posts\Models\Post;

class SearchReplace extends Component
{
    // Search & Replace Inputs
    public string $searchString = '';

    public string $replaceString = '';

    public array $targetFields = ['content', 'title', 'excerpt', 'meta'];

    public bool $caseSensitive = false;

    public string $selectedLocale = 'all';

    public string $statusFilter = 'all';

    public string $categoryFilter = '';

    public int $batchSize = 25;

    // UI & Execution State
    public bool $isPreviewing = false;

    public bool $isProcessing = false;

    public bool $isCompleted = false;

    public string $statusMessage = '';

    public string $errorMessage = '';

    // Preview / Scan Results
    public int $totalMatchedPosts = 0;

    public int $totalMatchedOccurrences = 0;

    public array $previewResults = [];

    // Batch Execution Tracking
    public array $postIdsToProcess = [];

    public int $totalPostsCount = 0;

    public int $processedCount = 0;

    public int $replacedOccurrencesCount = 0;

    public int $updatedPostsCount = 0;

    public int $progressPercent = 0;

    public array $executionLogs = [];

    public function mount(): void
    {
        $this->targetFields = ['content', 'title', 'excerpt', 'meta'];
    }

    /**
     * Run dry-run scan without making changes to the database.
     */
    public function scanPreview(): void
    {
        $this->errorMessage = '';
        $this->statusMessage = '';

        if (trim($this->searchString) === '') {
            $this->errorMessage = 'Please enter a search string.';

            return;
        }

        if (empty($this->targetFields)) {
            $this->errorMessage = 'Please select at least one target field.';

            return;
        }

        $this->isPreviewing = true;
        $this->isCompleted = false;
        $this->previewResults = [];
        $this->totalMatchedPosts = 0;
        $this->totalMatchedOccurrences = 0;

        $postsQuery = $this->buildBaseQuery();
        $posts = $postsQuery->get();

        $matchedIds = [];
        $previews = [];
        $totalOccurrences = 0;

        foreach ($posts as $post) {
            $postMatches = $this->analyzePostMatches($post);

            if ($postMatches['total_count'] > 0) {
                $matchedIds[] = $post->id;
                $totalOccurrences += $postMatches['total_count'];

                if (count($previews) < 20) {
                    $previews[] = [
                        'id' => $post->id,
                        'title' => $post->title,
                        'slug' => $post->slug,
                        'status' => $post->status,
                        'match_count' => $postMatches['total_count'],
                        'fields' => $postMatches['fields'],
                        'snippets' => $postMatches['snippets'],
                    ];
                }
            }
        }

        $this->postIdsToProcess = $matchedIds;
        $this->totalPostsCount = count($matchedIds);
        $this->totalMatchedPosts = count($matchedIds);
        $this->totalMatchedOccurrences = $totalOccurrences;
        $this->previewResults = $previews;

        if ($this->totalMatchedPosts === 0) {
            $this->statusMessage = 'No matching occurrences found with the given criteria.';
        } else {
            $this->statusMessage = "Found {$this->totalMatchedOccurrences} occurrences across {$this->totalMatchedPosts} posts.";
        }
    }

    /**
     * Start the batch replacement process.
     */
    public function startReplace(): void
    {
        $this->errorMessage = '';

        if (trim($this->searchString) === '') {
            $this->errorMessage = 'Please enter a search string.';

            return;
        }

        if (empty($this->targetFields)) {
            $this->errorMessage = 'Please select at least one target field.';

            return;
        }

        // Rescan if not already scanned or criteria changed
        if (empty($this->postIdsToProcess)) {
            $this->scanPreview();
        }

        if (empty($this->postIdsToProcess)) {
            $this->errorMessage = 'No matching posts found to replace.';

            return;
        }

        $this->isProcessing = true;
        $this->isCompleted = false;
        $this->processedCount = 0;
        $this->replacedOccurrencesCount = 0;
        $this->updatedPostsCount = 0;
        $this->progressPercent = 0;
        $this->executionLogs = [];
        $this->statusMessage = 'Starting search and replace...';

        // Dispatch browser event to kick off batching
        $this->dispatch('start-batch-loop');
    }

    /**
     * Process a single batch chunk (called by Livewire polling / event loop).
     */
    public function processBatch(): void
    {
        if (! $this->isProcessing || empty($this->postIdsToProcess)) {
            $this->finishProcess();

            return;
        }

        $offset = $this->processedCount;
        $chunkIds = array_slice($this->postIdsToProcess, $offset, $this->batchSize);

        if (empty($chunkIds)) {
            $this->finishProcess();

            return;
        }

        $posts = Post::whereIn('id', $chunkIds)->get();

        foreach ($posts as $post) {
            $replaceResult = $this->executePostReplace($post);

            if ($replaceResult['count'] > 0) {
                $this->replacedOccurrencesCount += $replaceResult['count'];
                $this->updatedPostsCount++;

                if (count($this->executionLogs) < 30) {
                    $this->executionLogs[] = [
                        'id' => $post->id,
                        'title' => $post->title,
                        'slug' => $post->slug,
                        'count' => $replaceResult['count'],
                        'fields' => $replaceResult['fields'],
                        'time' => now()->format('H:i:s'),
                    ];
                }
            }

            $this->processedCount++;
        }

        if ($this->totalPostsCount > 0) {
            $this->progressPercent = min(100, (int) round(($this->processedCount / $this->totalPostsCount) * 100));
        }

        $this->statusMessage = "Processing: {$this->processedCount} of {$this->totalPostsCount} posts ({$this->progressPercent}%)...";

        if ($this->processedCount >= $this->totalPostsCount) {
            $this->finishProcess();
        } else {
            $this->dispatch('continue-batch-loop');
        }
    }

    /**
     * Finalize process and purge caches.
     */
    public function finishProcess(): void
    {
        $this->isProcessing = false;
        $this->isCompleted = true;
        $this->progressPercent = 100;
        $this->postIdsToProcess = [];

        // Purge full frontend & web server cache
        try {
            CacheManager::purgeAll();
        } catch (\Throwable $e) {
            // Ignore cache purge exceptions
        }

        $this->statusMessage = "Successfully replaced {$this->replacedOccurrencesCount} occurrences across {$this->updatedPostsCount} posts!";
    }

    /**
     * Cancel active process.
     */
    public function cancelProcess(): void
    {
        $this->isProcessing = false;
        $this->statusMessage = "Process paused. Updated {$this->updatedPostsCount} posts so far.";
    }

    /**
     * Reset form and state.
     */
    public function resetForm(): void
    {
        $this->searchString = '';
        $this->replaceString = '';
        $this->targetFields = ['content', 'title', 'excerpt', 'meta'];
        $this->caseSensitive = false;
        $this->selectedLocale = 'all';
        $this->statusFilter = 'all';
        $this->categoryFilter = '';
        $this->isPreviewing = false;
        $this->isProcessing = false;
        $this->isCompleted = false;
        $this->previewResults = [];
        $this->postIdsToProcess = [];
        $this->executionLogs = [];
        $this->statusMessage = '';
        $this->errorMessage = '';
        $this->progressPercent = 0;
    }

    /**
     * Build base query for posts based on filters.
     */
    protected function buildBaseQuery()
    {
        $query = Post::query();

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if (! empty($this->categoryFilter)) {
            $query->whereHas('categories', function ($q) {
                $q->where('categories.id', $this->categoryFilter)
                    ->orWhere('categories.slug', $this->categoryFilter);
            });
        }

        return $query;
    }

    /**
     * Analyze occurrences of search string in a single post without altering it.
     */
    protected function analyzePostMatches(Post $post): array
    {
        $search = $this->searchString;
        $caseSensitive = $this->caseSensitive;
        $locales = $this->getLocalesToSearch();
        $targetFields = $this->targetFields;

        $totalCount = 0;
        $matchedFields = [];
        $snippets = [];

        // 1. Default locale fields
        if (in_array('en', $locales, true) || in_array('all', $locales, true)) {
            if (in_array('title', $targetFields, true) && ! empty($post->title)) {
                $count = $this->countOccurrences($post->title, $search, $caseSensitive);
                if ($count > 0) {
                    $totalCount += $count;
                    $matchedFields[] = "Title (EN: {$count})";
                    $snippets[] = $this->buildSnippet($post->title, $search, $caseSensitive);
                }
            }

            if (in_array('excerpt', $targetFields, true) && ! empty($post->excerpt)) {
                $count = $this->countOccurrences($post->excerpt, $search, $caseSensitive);
                if ($count > 0) {
                    $totalCount += $count;
                    $matchedFields[] = "Excerpt (EN: {$count})";
                    $snippets[] = $this->buildSnippet($post->excerpt, $search, $caseSensitive);
                }
            }

            if (in_array('content', $targetFields, true) && ! empty($post->content)) {
                $count = $this->countOccurrences($post->content, $search, $caseSensitive);
                if ($count > 0) {
                    $totalCount += $count;
                    $matchedFields[] = "Content (EN: {$count})";
                    $snippets[] = $this->buildSnippet($post->content, $search, $caseSensitive);
                }
            }

            if (in_array('meta', $targetFields, true) && ! empty($post->meta) && is_array($post->meta)) {
                $metaCount = $this->countOccurrencesInArray($post->meta, $search, $caseSensitive);
                if ($metaCount > 0) {
                    $totalCount += $metaCount;
                    $matchedFields[] = "Meta Fields ({$metaCount})";
                }
            }
        }

        // 2. Translations JSON column fields
        $translations = $post->translations ?? [];
        if (is_array($translations)) {
            foreach ($translations as $loc => $transData) {
                if (! is_array($transData)) {
                    continue;
                }

                if ($this->selectedLocale !== 'all' && $this->selectedLocale !== $loc) {
                    continue;
                }

                $locUpper = strtoupper($loc);

                if (in_array('title', $targetFields, true) && ! empty($transData['title'])) {
                    $count = $this->countOccurrences($transData['title'], $search, $caseSensitive);
                    if ($count > 0) {
                        $totalCount += $count;
                        $matchedFields[] = "Title ({$locUpper}: {$count})";
                        $snippets[] = $this->buildSnippet($transData['title'], $search, $caseSensitive);
                    }
                }

                if (in_array('excerpt', $targetFields, true) && ! empty($transData['excerpt'])) {
                    $count = $this->countOccurrences($transData['excerpt'], $search, $caseSensitive);
                    if ($count > 0) {
                        $totalCount += $count;
                        $matchedFields[] = "Excerpt ({$locUpper}: {$count})";
                        $snippets[] = $this->buildSnippet($transData['excerpt'], $search, $caseSensitive);
                    }
                }

                if (in_array('content', $targetFields, true) && ! empty($transData['content'])) {
                    $count = $this->countOccurrences($transData['content'], $search, $caseSensitive);
                    if ($count > 0) {
                        $totalCount += $count;
                        $matchedFields[] = "Content ({$locUpper}: {$count})";
                        $snippets[] = $this->buildSnippet($transData['content'], $search, $caseSensitive);
                    }
                }

                if (in_array('meta', $targetFields, true) && ! empty($transData['meta']) && is_array($transData['meta'])) {
                    $metaCount = $this->countOccurrencesInArray($transData['meta'], $search, $caseSensitive);
                    if ($metaCount > 0) {
                        $totalCount += $metaCount;
                        $matchedFields[] = "Meta ({$locUpper}: {$metaCount})";
                    }
                }
            }
        }

        return [
            'total_count' => $totalCount,
            'fields' => array_unique($matchedFields),
            'snippets' => array_slice(array_filter($snippets), 0, 3),
        ];
    }

    /**
     * Execute search and replace on a single post model.
     */
    protected function executePostReplace(Post $post): array
    {
        $search = $this->searchString;
        $replace = $this->replaceString;
        $caseSensitive = $this->caseSensitive;
        $locales = $this->getLocalesToSearch();
        $targetFields = $this->targetFields;

        $totalReplaced = 0;
        $affectedFields = [];
        $isDirty = false;

        // 1. Default locale fields
        if (in_array('en', $locales, true) || in_array('all', $locales, true)) {
            if (in_array('title', $targetFields, true) && ! empty($post->title)) {
                $count = $this->countOccurrences($post->title, $search, $caseSensitive);
                if ($count > 0) {
                    $post->title = $this->replaceStringValue($post->title, $search, $replace, $caseSensitive);
                    $totalReplaced += $count;
                    $affectedFields[] = 'Title (EN)';
                    $isDirty = true;
                }
            }

            if (in_array('excerpt', $targetFields, true) && ! empty($post->excerpt)) {
                $count = $this->countOccurrences($post->excerpt, $search, $caseSensitive);
                if ($count > 0) {
                    $post->excerpt = $this->replaceStringValue($post->excerpt, $search, $replace, $caseSensitive);
                    $totalReplaced += $count;
                    $affectedFields[] = 'Excerpt (EN)';
                    $isDirty = true;
                }
            }

            if (in_array('content', $targetFields, true) && ! empty($post->content)) {
                $count = $this->countOccurrences($post->content, $search, $caseSensitive);
                if ($count > 0) {
                    $post->content = $this->replaceStringValue($post->content, $search, $replace, $caseSensitive);
                    $totalReplaced += $count;
                    $affectedFields[] = 'Content (EN)';
                    $isDirty = true;
                }
            }

            if (in_array('meta', $targetFields, true) && ! empty($post->meta) && is_array($post->meta)) {
                $metaResult = $this->replaceInArray($post->meta, $search, $replace, $caseSensitive);
                if ($metaResult['count'] > 0) {
                    $post->meta = $metaResult['data'];
                    $totalReplaced += $metaResult['count'];
                    $affectedFields[] = 'Meta';
                    $isDirty = true;
                }
            }
        }

        // 2. Translations JSON column fields
        $translations = $post->translations ?? [];
        if (is_array($translations)) {
            $transDirty = false;
            foreach ($translations as $loc => $transData) {
                if (! is_array($transData)) {
                    continue;
                }

                if ($this->selectedLocale !== 'all' && $this->selectedLocale !== $loc) {
                    continue;
                }

                $locUpper = strtoupper($loc);

                if (in_array('title', $targetFields, true) && ! empty($transData['title'])) {
                    $count = $this->countOccurrences($transData['title'], $search, $caseSensitive);
                    if ($count > 0) {
                        $translations[$loc]['title'] = $this->replaceStringValue($transData['title'], $search, $replace, $caseSensitive);
                        $totalReplaced += $count;
                        $affectedFields[] = "Title ({$locUpper})";
                        $transDirty = true;
                    }
                }

                if (in_array('excerpt', $targetFields, true) && ! empty($transData['excerpt'])) {
                    $count = $this->countOccurrences($transData['excerpt'], $search, $caseSensitive);
                    if ($count > 0) {
                        $translations[$loc]['excerpt'] = $this->replaceStringValue($transData['excerpt'], $search, $replace, $caseSensitive);
                        $totalReplaced += $count;
                        $affectedFields[] = "Excerpt ({$locUpper})";
                        $transDirty = true;
                    }
                }

                if (in_array('content', $targetFields, true) && ! empty($transData['content'])) {
                    $count = $this->countOccurrences($transData['content'], $search, $caseSensitive);
                    if ($count > 0) {
                        $translations[$loc]['content'] = $this->replaceStringValue($transData['content'], $search, $replace, $caseSensitive);
                        $totalReplaced += $count;
                        $affectedFields[] = "Content ({$locUpper})";
                        $transDirty = true;
                    }
                }

                if (in_array('meta', $targetFields, true) && ! empty($transData['meta']) && is_array($transData['meta'])) {
                    $metaResult = $this->replaceInArray($transData['meta'], $search, $replace, $caseSensitive);
                    if ($metaResult['count'] > 0) {
                        $translations[$loc]['meta'] = $metaResult['data'];
                        $totalReplaced += $metaResult['count'];
                        $affectedFields[] = "Meta ({$locUpper})";
                        $transDirty = true;
                    }
                }
            }

            if ($transDirty) {
                $post->translations = $translations;
                $isDirty = true;
            }
        }

        if ($isDirty) {
            $post->save();
        }

        return [
            'count' => $totalReplaced,
            'fields' => array_unique($affectedFields),
        ];
    }

    /**
     * Count occurrences in a single string.
     */
    protected function countOccurrences(string $haystack, string $needle, bool $caseSensitive): int
    {
        if ($needle === '' || $haystack === '') {
            return 0;
        }

        if ($caseSensitive) {
            return substr_count($haystack, $needle);
        }

        return substr_count(mb_strtolower($haystack), mb_strtolower($needle));
    }

    /**
     * Count occurrences recursively in an array.
     */
    protected function countOccurrencesInArray(array $array, string $needle, bool $caseSensitive): int
    {
        $count = 0;
        foreach ($array as $value) {
            if (is_string($value)) {
                $count += $this->countOccurrences($value, $needle, $caseSensitive);
            } elseif (is_array($value)) {
                $count += $this->countOccurrencesInArray($value, $needle, $caseSensitive);
            }
        }

        return $count;
    }

    /**
     * Replace occurrences in a string.
     */
    protected function replaceStringValue(string $haystack, string $needle, string $replace, bool $caseSensitive): string
    {
        if ($needle === '') {
            return $haystack;
        }

        if ($caseSensitive) {
            return str_replace($needle, $replace, $haystack);
        }

        return str_ireplace($needle, $replace, $haystack);
    }

    /**
     * Replace occurrences recursively in an array.
     */
    protected function replaceInArray(array $array, string $needle, string $replace, bool $caseSensitive): array
    {
        $count = 0;
        $result = [];

        foreach ($array as $key => $value) {
            if (is_string($value)) {
                $c = $this->countOccurrences($value, $needle, $caseSensitive);
                if ($c > 0) {
                    $result[$key] = $this->replaceStringValue($value, $needle, $replace, $caseSensitive);
                    $count += $c;
                } else {
                    $result[$key] = $value;
                }
            } elseif (is_array($value)) {
                $nested = $this->replaceInArray($value, $needle, $replace, $caseSensitive);
                $result[$key] = $nested['data'];
                $count += $nested['count'];
            } else {
                $result[$key] = $value;
            }
        }

        return [
            'data' => $result,
            'count' => $count,
        ];
    }

    /**
     * Build contextual snippet with highlighted search match.
     */
    protected function buildSnippet(string $text, string $needle, bool $caseSensitive): ?string
    {
        $clean = strip_tags($text);
        $pos = $caseSensitive ? mb_strpos($clean, $needle) : mb_stripos($clean, $needle);

        if ($pos === false) {
            return null;
        }

        $start = max(0, $pos - 60);
        $length = mb_strlen($needle) + 120;
        $snippet = mb_substr($clean, $start, $length);

        if ($start > 0) {
            $snippet = '...'.$snippet;
        }
        if ($start + $length < mb_strlen($clean)) {
            $snippet = $snippet.'...';
        }

        // Highlight matched keyword
        $pattern = '/'.preg_quote($needle, '/').'/'.($caseSensitive ? 'u' : 'iu');

        return preg_replace($pattern, '<mark class="bg-amber-300 dark:bg-amber-500/40 text-gray-900 dark:text-white px-1 py-0.5 rounded font-semibold">$0</mark>', e($snippet));
    }

    /**
     * Get array of locales to scan.
     */
    protected function getLocalesToSearch(): array
    {
        if ($this->selectedLocale === 'all') {
            return array_merge(['en'], available_locales());
        }

        return [$this->selectedLocale];
    }

    public function render()
    {
        $categories = Category::orderBy('name')->get();
        $availableLocales = available_locales();

        return view('posts::livewire.search-replace', [
            'categories' => $categories,
            'availableLocales' => $availableLocales,
        ]);
    }
}
