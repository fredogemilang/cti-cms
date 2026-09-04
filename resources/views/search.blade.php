<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results — {{ setting('site_name', config('app.name')) }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 2rem; }
        .container { max-width: 900px; margin: 0 auto; }
        .search-box { display: flex; gap: 0.5rem; margin-bottom: 2rem; }
        .search-box input { flex: 1; padding: 0.75rem 1rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 1rem; }
        .search-box button { padding: 0.75rem 1.5rem; background: #2563eb; color: white; border: none; border-radius: 0.5rem; font-weight: bold; cursor: pointer; }
        .result-card { background: white; padding: 1.5rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .result-card h2 { margin: 0 0 0.5rem; font-size: 1.25rem; }
        .result-card a { color: #2563eb; text-decoration: none; }
        .result-card a:hover { text-decoration: underline; }
        .result-card p { margin: 0; color: #64748b; font-size: 0.95rem; line-height: 1.5; }
        .result-url { font-size: 0.75rem; color: #94a3b8; margin-bottom: 0.25rem; display: block; }
        mark { background: #fef08a; padding: 0 0.2rem; border-radius: 0.2rem; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Search Results</h1>
        <form action="{{ url('/search') }}" method="GET" class="search-box">
            <input type="search" name="q" value="{{ $query }}" placeholder="Search website..." required>
            <button type="submit">Search</button>
        </form>

        @if($results->isNotEmpty())
            <p>Showing {{ $results->total() }} results for "{{ $query }}":</p>
            @foreach($results as $item)
                <div class="result-card">
                    <span class="result-url">{{ $item->url }}</span>
                    <h2>
                        <a href="{{ $item->url }}">
                            {!! $searchService->highlight($item->title, $query, 60) !!}
                        </a>
                    </h2>
                    <p>
                        {!! $searchService->highlight($item->excerpt ?: $item->body ?: '', $query, 120) !!}
                    </p>
                </div>
            @endforeach
            <div>
                {{ $results->links() }}
            </div>
        @elseif(!empty($query))
            <p>No results found for "{{ $query }}".</p>
        @endif
    </div>
</body>
</html>
