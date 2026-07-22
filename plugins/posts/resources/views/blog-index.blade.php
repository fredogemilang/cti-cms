<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ setting('site_name', config('app.name')) }} — Blog</title>
    <meta name="description" content="Latest posts from {{ setting('site_name', config('app.name')) }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased">
    <div class="max-w-5xl mx-auto px-6 py-12">
        <header class="mb-10">
            <h1 class="text-4xl font-extrabold mb-2">Blog</h1>
            @if(isset($category) && is_string($category))
                <p class="text-lg text-gray-500">Category: <span class="font-semibold text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('-', ' ', $category)) }}</span></p>
            @endif
        </header>

        @php
            $query = \Plugins\Posts\Models\Post::where('status', 'published')->latest('published_at');
            if (isset($category) && is_string($category)) {
                $categoryModel = \Plugins\Posts\Models\Category::where('slug', $category)->first();
                if ($categoryModel) {
                    $query->whereHas('categories', fn($q) => $q->where('categories.id', $categoryModel->id));
                }
            }
            $posts = $query->paginate(12);
        @endphp

        @if($posts->isEmpty())
            <div class="text-center py-16">
                <p class="text-gray-500 text-lg">No posts found.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($posts as $post)
                <article class="bg-gray-50 dark:bg-gray-800 rounded-2xl overflow-hidden hover:shadow-lg transition-shadow">
                    @if($post->featured_image)
                    <div class="aspect-video">
                        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                    </div>
                    @endif
                    <div class="p-5">
                        <h2 class="text-lg font-bold mb-2">
                            <a href="{{ url((\App\Models\Setting::get('permalink_post_base', 'blog')) . '/' . $post->slug) }}" class="hover:text-blue-600 transition-colors">
                                {{ $post->title }}
                            </a>
                        </h2>
                        @if($post->excerpt)
                        <p class="text-sm text-gray-500 line-clamp-3 mb-3">{{ $post->excerpt }}</p>
                        @endif
                        <div class="flex items-center gap-2 text-xs text-gray-400">
                            @if($post->published_at)
                            <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('M d, Y') }}</time>
                            @endif
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</body>
</html>
