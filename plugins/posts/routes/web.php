<?php

use App\Services\ThemeLoader;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Plugins\Posts\Models\Category;
use Plugins\Posts\Models\Post;
use Plugins\Posts\Models\Setting;
use Plugins\Posts\Models\Tag;

$adminPath = config('admin.path', config('cms.path', 'admin'));

Route::middleware(['web', 'auth', 'permission:posts.view'])->prefix("{$adminPath}/posts")->name('admin.posts.')->group(function () {

    // Posts
    Route::get('/', function () {
        return view('posts::index');
    })->name('index');

    Route::get('/create', function () {
        return view('posts::create');
    })->name('create')->middleware('permission:posts.create');

    Route::get('/{id}/edit', function ($id) {
        return view('posts::edit', ['id' => $id]);
    })->name('edit')->middleware('permission:posts.edit');

    // Categories
    Route::get('/categories', function () {
        return view('posts::categories.index');
    })->name('categories')->middleware('permission:categories.view');

    // Authors
    Route::get('/authors', function () {
        return view('posts::authors.index');
    })->name('authors')->middleware('permission:posts.view');

    // Tags
    Route::get('/tags', function () {
        return view('posts::tags.index');
    })->name('tags')->middleware('permission:tags.view');

    // Search & Replace
    Route::get('/search-replace', function () {
        return view('posts::search-replace');
    })->name('search-replace')->middleware('permission:posts.edit');

    // Settings
    Route::get('/settings', function () {
        return view('posts::settings');
    })->name('settings')->middleware('permission:posts.view'); // Reusing view permission

    // WordPress Migration / Import
    Route::get('/import', function () {
        return view('posts::wordpress-migration');
    })->name('import')->middleware('permission:posts.create');

    Route::get('/wordpress-migration', function () {
        return redirect()->route('admin.posts.import');
    })->name('wordpress-migration')->middleware('permission:posts.create');

});

// Public Routes
Route::middleware(['web'])->group(function () {
    $archiveSlug = 'blog-news';
    if (Schema::hasTable('posts_settings')) {
        $archiveSlug = Setting::get('archive_slug', 'blog-news');
    }
    // Permalink settings override (from core settings table)
    if (Schema::hasTable('settings')) {
        $archiveSlug = App\Models\Setting::get('permalink_post_base', $archiveSlug);
        $categoryBase = App\Models\Setting::get('permalink_category_base', 'category');
        $tagBase = App\Models\Setting::get('permalink_tag_base', 'tag');
    }
    $categoryBase ??= 'category';
    $tagBase ??= 'tag';

    try {
        $allLocales = array_filter(array_map('trim', explode(',', (string) setting('available_locales', 'id,en'))));
        $defaultLocale = setting('default_locale', config('app.locale', 'en'));
    } catch (Throwable $e) {
        $allLocales = ['id', 'en'];
        $defaultLocale = 'en';
    }
    $nonDefaultLocales = array_values(array_filter($allLocales, fn ($l) => $l !== $defaultLocale));
    $localePattern = ! empty($nonDefaultLocales) ? implode('|', array_map('preg_quote', $nonDefaultLocales)) : '';

    $renderIndex = function (?string $locale = null, ?string $categoryOverride = null, ?string $tagOverride = null) use ($categoryBase, $tagBase) {
        if ($locale && in_array($locale, available_locales(), true)) {
            app()->setLocale($locale);
        }

        $perPage = (int) Setting::get('posts_per_page', 9);
        $dateFormat = Setting::get('date_format', 'M d, Y');

        $selectedCategory = $categoryOverride ?: request('category');
        $selectedTag = $tagOverride ?: request('tag');
        $searchQuery = request('q') ?: request('search');

        // 301 Permanent Canonical Redirect ONLY for query string parameter requests (?category= or ?tag=)
        if (! request()->ajax() && ! request()->wantsJson() && request()->header('X-Requested-With') !== 'XMLHttpRequest') {
            $hasCategoryQuery = request()->query('category') !== null;
            $hasTagQuery = request()->query('tag') !== null;

            if ($hasCategoryQuery || $hasTagQuery) {
                $currentLocale = app()->getLocale();
                $defLocale = function_exists('default_locale') ? default_locale() : config('app.locale', 'en');
                $locArchiveSlug = Setting::getArchiveSlug($currentLocale);
                $basePath = ($currentLocale && $currentLocale !== $defLocale) ? "/{$currentLocale}/{$locArchiveSlug}" : "/{$locArchiveSlug}";

                if ($hasCategoryQuery) {
                    $catVal = request()->query('category');
                    $queryWithoutCat = request()->except(['category']);
                    $targetUrl = url("{$basePath}/{$categoryBase}/{$catVal}".(! empty($queryWithoutCat) ? '?'.http_build_query($queryWithoutCat) : ''));

                    return redirect($targetUrl, 301);
                }

                if ($hasTagQuery) {
                    $tagVal = request()->query('tag');
                    $queryWithoutTag = request()->except(['tag']);
                    $targetUrl = url("{$basePath}/{$tagBase}/{$tagVal}".(! empty($queryWithoutTag) ? '?'.http_build_query($queryWithoutTag) : ''));

                    return redirect($targetUrl, 301);
                }
            }
        }

        $featuredPosts = Post::where('status', 'published')
            ->where('is_featured', true)
            ->latest()
            ->take(4)
            ->get();

        $postsQuery = Post::published()->latest();

        if ($selectedCategory) {
            $postsQuery->whereHas('categories', function ($q) use ($selectedCategory) {
                $q->where('slug', $selectedCategory)
                    ->orWhere('id', $selectedCategory)
                    ->orWhereRaw('JSON_EXTRACT(translations, "$.id.slug") = ?', [$selectedCategory])
                    ->orWhereRaw('JSON_EXTRACT(translations, "$.en.slug") = ?', [$selectedCategory]);
            });
        }

        if ($selectedTag) {
            $postsQuery->whereHas('tags', function ($q) use ($selectedTag) {
                $q->where('slug', $selectedTag)
                    ->orWhere('id', $selectedTag)
                    ->orWhereRaw('JSON_EXTRACT(translations, "$.id.slug") = ?', [$selectedTag])
                    ->orWhereRaw('JSON_EXTRACT(translations, "$.en.slug") = ?', [$selectedTag]);
            });
        }

        if ($searchQuery) {
            $cleanSearch = str_replace("\xC2\xA0", ' ', (string) $searchQuery);
            $words = array_filter(explode(' ', trim($cleanSearch)));
            $currentLocale = app()->getLocale();

            if (! empty($words)) {
                $postsQuery->where(function ($q) use ($words, $currentLocale) {
                    foreach ($words as $word) {
                        $q->where(function ($sub) use ($word, $currentLocale) {
                            $term = "%{$word}%";
                            if ($currentLocale === 'en') {
                                $sub->where('title', 'like', $term)
                                    ->orWhere('excerpt', 'like', $term)
                                    ->orWhere('content', 'like', $term)
                                    ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(translations, "$.en.title"))) LIKE ?', [strtolower($term)])
                                    ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(translations, "$.en.excerpt"))) LIKE ?', [strtolower($term)])
                                    ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(translations, "$.en.content"))) LIKE ?', [strtolower($term)]);
                            } else {
                                $sub->whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(translations, ?))) LIKE ?', ["$.{$currentLocale}.title", strtolower($term)])
                                    ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(translations, ?))) LIKE ?', ["$.{$currentLocale}.excerpt", strtolower($term)])
                                    ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(translations, ?))) LIKE ?', ["$.{$currentLocale}.content", strtolower($term)]);
                            }
                        });
                    }
                });
            }
        }

        $posts = $postsQuery->paginate($perPage)->withQueryString();

        $activeTheme = app(ThemeLoader::class)->getActiveTheme();
        $themeSlug = $activeTheme ? $activeTheme->slug : 'default';

        if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            $partialView = "{$themeSlug}::posts.partials.grid-partial";
            if (! view()->exists($partialView)) {
                $partialView = 'posts::blog-index';
            }
            $html = view($partialView, compact('posts', 'dateFormat'))->render();

            return response()->json([
                'html' => $html,
                'total' => $posts->total(),
            ]);
        }

        $view = "{$themeSlug}::posts.index";
        if (! view()->exists($view)) {
            $view = view()->exists('iccom::posts.index') ? 'iccom::posts.index' : 'posts::blog-index';
        }

        $category = $selectedCategory ? Category::where('slug', $selectedCategory)->orWhere('id', $selectedCategory)->first() : null;
        $tag = $selectedTag ? Tag::where('slug', $selectedTag)->orWhere('id', $selectedTag)->first() : null;

        return view($view, compact('featuredPosts', 'posts', 'dateFormat', 'selectedCategory', 'selectedTag', 'searchQuery', 'category', 'tag'));
    };

    $renderCategory = function (?string $localeOrCategory = null, ?string $category = null) use (&$renderIndex) {
        $locale = null;
        if ($category !== null) {
            $locale = $localeOrCategory;
        } else {
            $category = $localeOrCategory;
        }

        return $renderIndex($locale, $category, null);
    };

    $renderTag = function (?string $localeOrTag = null, ?string $tag = null) use (&$renderIndex) {
        $locale = null;
        if ($tag !== null) {
            $locale = $localeOrTag;
        } else {
            $tag = $localeOrTag;
        }

        return $renderIndex($locale, null, $tag);
    };

    $renderSingle = function (?string $localeOrSlug = null, ?string $slug = null) {
        $requestedLocale = null;
        if ($slug !== null) {
            $requestedLocale = $localeOrSlug;
            if ($requestedLocale && in_array($requestedLocale, available_locales(), true)) {
                app()->setLocale($requestedLocale);
            }
        } else {
            $slug = $localeOrSlug;
        }

        $post = Post::findByLocalizedSlug($slug);
        abort_if(! $post, 404);

        request()->attributes->set('post', $post);
        View::share('post', $post);

        // Canonical URL Validation: 301 Redirect if URL path does not match post's canonical URL
        $currentLocale = app()->getLocale();
        $canonicalUrl = $post->getUrl($currentLocale);
        $currentFullUrl = request()->url();

        if ($currentFullUrl !== $canonicalUrl) {
            $queryString = request()->getQueryString();
            $targetUrl = $canonicalUrl.($queryString ? "?{$queryString}" : '');

            return redirect($targetUrl, 301);
        }

        // Track view if it's a real user (not bot/crawler) and hasn't been viewed in current session
        $userAgent = request()->header('User-Agent') ?: '';
        $isBot = preg_match('/bot|crawl|spider|slurp|mediapartners|google|bing|yandex|baidu|feedburner|facebookexternalhit|twitterbot|slackbot|whatsapp|discordbot/i', $userAgent);

        if (! $isBot) {
            $sessionKey = 'viewed_posts.'.$post->id;
            if (! session()->has($sessionKey)) {
                $post->increment('views_count');
                session()->put($sessionKey, true);
            }
        }

        $dateFormat = Setting::get('date_format', 'M d, Y');
        $enableComments = (bool) Setting::get('enable_comments', true);
        $closeCommentsDays = (int) Setting::get('close_comments_days', 0);

        // Theme-aware view resolution
        $activeTheme = app(ThemeLoader::class)->getActiveTheme();
        $themeSlug = $activeTheme ? $activeTheme->slug : 'default';

        $viewName = "{$themeSlug}::posts.single";
        if (! view()->exists($viewName)) {
            $viewName = view()->exists('iccom::posts.single') ? 'iccom::posts.single' : 'posts::show';
        }

        return view($viewName, [
            'post' => $post,
            'entry' => $post,  // Alias for theme compatibility
            'dateFormat' => $dateFormat,
            'enableComments' => $enableComments,
            'closeCommentsDays' => $closeCommentsDays,
        ]);
    };

    // Default locale routes
    Route::get("/{$archiveSlug}", fn () => $renderIndex())->name('posts.index');
    Route::get("/{$archiveSlug}/{$categoryBase}/{category}", fn ($category) => $renderCategory(null, $category))->name('posts.category');
    Route::get("/{$archiveSlug}/{$tagBase}/{tag}", fn ($tag) => $renderTag(null, $tag))->name('posts.tag');
    Route::get("/{$archiveSlug}/{slug}", fn ($slug) => $renderSingle(null, $slug))->name('posts.show');

    // Localized routes (supports custom archive slug per locale, falling back to primary archive slug)
    if (! empty($nonDefaultLocales)) {
        foreach ($nonDefaultLocales as $loc) {
            $locArchiveSlug = Setting::getArchiveSlug($loc);

            Route::get("/{$loc}/{$locArchiveSlug}", fn () => $renderIndex($loc))
                ->name("locale.{$loc}.posts.index");

            Route::get("/{$loc}/{$locArchiveSlug}/{$categoryBase}/{category}", fn ($category) => $renderCategory($loc, $category))
                ->name("locale.{$loc}.posts.category");

            Route::get("/{$loc}/{$locArchiveSlug}/{$tagBase}/{tag}", fn ($tag) => $renderTag($loc, $tag))
                ->name("locale.{$loc}.posts.tag");

            Route::get("/{$loc}/{$locArchiveSlug}/{slug}", fn ($slug) => $renderSingle($loc, $slug))
                ->name("locale.{$loc}.posts.show");

            // 301 Permanent Redirect if localized archive slug is different from primary archive slug
            if ($locArchiveSlug !== $archiveSlug) {
                Route::get("/{$loc}/{$archiveSlug}", function () use ($loc, $locArchiveSlug) {
                    $queryString = request()->getQueryString();
                    $targetUrl = url("/{$loc}/{$locArchiveSlug}".($queryString ? "?{$queryString}" : ''));

                    return redirect($targetUrl, 301);
                });

                Route::get("/{$loc}/{$archiveSlug}/{$categoryBase}/{category}", function ($category) use ($loc, $locArchiveSlug, $categoryBase) {
                    $queryString = request()->getQueryString();
                    $targetUrl = url("/{$loc}/{$locArchiveSlug}/{$categoryBase}/{$category}".($queryString ? "?{$queryString}" : ''));

                    return redirect($targetUrl, 301);
                });

                Route::get("/{$loc}/{$archiveSlug}/{$tagBase}/{tag}", function ($tag) use ($loc, $locArchiveSlug, $tagBase) {
                    $queryString = request()->getQueryString();
                    $targetUrl = url("/{$loc}/{$locArchiveSlug}/{$tagBase}/{$tag}".($queryString ? "?{$queryString}" : ''));

                    return redirect($targetUrl, 301);
                });

                Route::get("/{$loc}/{$archiveSlug}/{slug}", function ($slug) use ($loc, $locArchiveSlug) {
                    $queryString = request()->getQueryString();
                    $targetUrl = url("/{$loc}/{$locArchiveSlug}/{$slug}".($queryString ? "?{$queryString}" : ''));

                    return redirect($targetUrl, 301);
                });
            }
        }
    }

    if ($archiveSlug !== 'blog-news') {
        Route::get('/blog-news', function () {
            $queryString = request()->getQueryString();

            return redirect(url('/blog-news'.($queryString ? "?{$queryString}" : '')), 301);
        });
        Route::get('/blog-news/{slug}', function ($slug) {
            $queryString = request()->getQueryString();

            return redirect(url('/blog-news/'.$slug.($queryString ? "?{$queryString}" : '')), 301);
        });
    }
});
