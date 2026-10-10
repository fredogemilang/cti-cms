<?php

namespace App\Services;

use App\Models\SearchIndex;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as ConcretePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SearchService
{
    /**
     * Search the denormalized index with natural language fulltext or LIKE fallback.
     */
    public function search(string $rawQuery, string $locale, int $perPage = 10): LengthAwarePaginator
    {
        $term = trim(preg_replace('/\s+/', ' ', $rawQuery));

        if ($term === '') {
            return $this->emptyPaginator($perPage);
        }

        $query = SearchIndex::query()->where('locale', $locale);
        $driver = DB::connection()->getDriverName();

        $tokens = preg_split('/\s+/', $term, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $longestToken = $tokens ? max(array_map('mb_strlen', $tokens)) : 0;

        // FULLTEXT ignores any token shorter than innodb_ft_min_token_size (default 3),
        // so a query made entirely of short tokens ("ai", "ai ml") would match nothing.
        // Gate on the longest token, not the whole string length.
        if ($driver === 'mysql' && $longestToken >= 3) {
            $cleanedTerm = $this->sanitizeFullTextQuery($term);

            $query->select('*')
                ->selectRaw('MATCH(title, excerpt, body) AGAINST(? IN NATURAL LANGUAGE MODE) AS relevance', [$cleanedTerm])
                ->whereRaw('MATCH(title, excerpt, body) AGAINST(? IN NATURAL LANGUAGE MODE)', [$cleanedTerm])
                ->orderByDesc('relevance')
                ->orderByDesc('indexed_at');
        } elseif ($driver === 'mysql') {
            // Short-token query. A raw %LIKE% would match mid-word — searching "ai"
            // returned "daily", "waiting" and "remains accurate" ahead of the actual
            // AI content. Match on word boundaries instead, and float title hits first.
            $pattern = $this->wordBoundaryPattern($tokens);

            if ($pattern === null) {
                return $this->emptyPaginator($perPage);
            }

            $query->where(fn ($sub) => $sub
                ->whereRaw('title REGEXP ?', [$pattern])
                ->orWhereRaw('excerpt REGEXP ?', [$pattern])
                ->orWhereRaw('body REGEXP ?', [$pattern]))
                ->orderByRaw('CASE WHEN title REGEXP ? THEN 0 ELSE 1 END', [$pattern])
                ->orderByDesc('indexed_at');
        } else {
            // SQLite (tests) and any other driver: no FULLTEXT/REGEXP guarantees.
            $query->where(function ($sub) use ($tokens) {
                foreach ($tokens as $token) {
                    $tokenLike = '%'.$token.'%';
                    $sub->orWhere('title', 'like', $tokenLike)
                        ->orWhere('excerpt', 'like', $tokenLike)
                        ->orWhere('body', 'like', $tokenLike);
                }
            })->orderByDesc('indexed_at');
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Build a MySQL (ICU) word-boundary alternation for short tokens, e.g. `\b(ai|ml)\b`.
     * Returns null when nothing usable survives sanitization.
     *
     * @param  array<int, string>  $tokens
     */
    protected function wordBoundaryPattern(array $tokens): ?string
    {
        $safe = [];

        foreach ($tokens as $token) {
            // Drop regex metacharacters entirely; short tokens are letters/digits in practice.
            $clean = preg_replace('/[^\p{L}\p{N}_]+/u', '', $token);
            if ($clean !== '' && $clean !== null) {
                $safe[] = $clean;
            }
        }

        if ($safe === []) {
            return null;
        }

        return '\\b('.implode('|', array_unique($safe)).')\\b';
    }

    /**
     * An empty result set shaped like a normal paginated response.
     */
    protected function emptyPaginator(int $perPage): LengthAwarePaginator
    {
        return new ConcretePaginator([], 0, $perPage, 1, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
    }

    /**
     * Sanitize query string for fulltext search.
     */
    protected function sanitizeFullTextQuery(string $query): string
    {
        // Strip out boolean operators that could trigger parser errors
        return (string) preg_replace('/[+\-><()~*\"@]+/', ' ', $query);
    }

    /**
     * Highlight query terms in excerpt or body text.
     */
    public function highlight(string $text, string $query, int $radius = 120): string
    {
        $cleanText = strip_tags($text);
        $cleanQuery = trim($query);

        // Every return path must be HTML-escaped: the views render this with {!! !!}
        // so that <mark> works, which makes any unescaped path an XSS sink. Do not
        // rely on strip_tags() above being sufficient on its own.
        if ($cleanQuery === '') {
            return e(Str::limit($cleanText, $radius * 2));
        }

        $pos = mb_stripos($cleanText, $cleanQuery);

        if ($pos === false) {
            return e(Str::limit($cleanText, $radius * 2));
        }

        $start = max(0, $pos - $radius);
        $snippet = mb_substr($cleanText, $start, ($radius * 2) + mb_strlen($cleanQuery));

        if ($start > 0) {
            $snippet = '...'.ltrim($snippet);
        }
        if ($start + ($radius * 2) + mb_strlen($cleanQuery) < mb_strlen($cleanText)) {
            $snippet = rtrim($snippet).'...';
        }

        // Highlight matching term safely
        $pattern = '/('.preg_quote($cleanQuery, '/').')/iu';

        return (string) preg_replace(
            $pattern,
            '<mark class="bg-red-100 text-[#E30613] font-semibold dark:bg-red-950/50 dark:text-red-400 px-1 rounded">$1</mark>',
            e($snippet)
        );
    }
}
