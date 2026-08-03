<?php

use App\Services\ThemeLoader;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Plugins\Posts\Models\Post;
use Plugins\Posts\Models\Setting;

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
    $archiveSlug = 'blog';
    if (Schema::hasTable('posts_settings')) {
        $archiveSlug = Setting::get('archive_slug', 'blog');
    }
    // Permalink settings override (from core settings table)
    if (Schema::hasTable('settings')) {
        $archiveSlug = App\Models\Setting::get('permalink_post_base', $archiveSlug);
        $categoryBase = App\Models\Setting::get('permalink_category_base', 'category');
    }
    $categoryBase ??= 'category';

    try {
        $allLocales = array_filter(array_map('trim', explode(',', (string) setting('available_locales', 'id,en'))));
        $defaultLocale = setting('default_locale', config('app.locale', 'en'));
    } catch (Throwable $e) {
        $allLocales = ['id', 'en'];
        $defaultLocale = 'en';
    }
    $nonDefaultLocales = array_values(array_filter($allLocales, fn ($l) => $l !== $defaultLocale));
    $localePattern = ! empty($nonDefaultLocales) ? implode('|', array_map('preg_quote', $nonDefaultLocales)) : '';

    $renderIndex = function (?string $locale = null) {
        if ($locale && in_array($locale, available_locales(), true)) {
            app()->setLocale($locale);
        }

        $featuredPosts = Post::where('status', 'published')
            ->where('is_featured', true)
            ->latest()
            ->take(4)
            ->get();

        $activeTheme = app(ThemeLoader::class)->getActiveTheme();
        $themeSlug = $activeTheme ? $activeTheme->slug : 'default';

        $view = "{$themeSlug}::posts.index";
        if (! view()->exists($view)) {
            $view = view()->exists('iccom::posts.index') ? 'iccom::posts.index' : 'posts::blog-index';
        }

        return view($view, compact('featuredPosts'));
    };

    $renderCategory = function (?string $localeOrCategory = null, ?string $category = null) {
        if ($category !== null) {
            $locale = $localeOrCategory;
            if ($locale && in_array($locale, available_locales(), true)) {
                app()->setLocale($locale);
            }
        } else {
            $category = $localeOrCategory;
        }

        $featuredPosts = Post::where('status', 'published')
            ->where('is_featured', true)
            ->latest()
            ->take(4)
            ->get();

        $activeTheme = app(ThemeLoader::class)->getActiveTheme();
        $themeSlug = $activeTheme ? $activeTheme->slug : 'default';

        $view = "{$themeSlug}::posts.index";
        if (! view()->exists($view)) {
            $view = view()->exists('iccom::posts.index') ? 'iccom::posts.index' : 'posts::blog-index';
        }

        return view($view, compact('featuredPosts', 'category'));
    };

    $renderSingle = function (?string $localeOrSlug = null, ?string $slug = null) {
        if ($slug !== null) {
            $locale = $localeOrSlug;
            if ($locale && in_array($locale, available_locales(), true)) {
                app()->setLocale($locale);
            }
        } else {
            $slug = $localeOrSlug;
        }

        $post = Post::findByLocalizedSlug($slug);
        abort_if(! $post, 404);

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
    Route::get("/{$archiveSlug}/{slug}", fn ($slug) => $renderSingle(null, $slug))->name('posts.show');

    // Localized routes (e.g. /id/blog-news, /id/blog-news/category/..., /id/blog-news/{slug})
    if (! empty($localePattern)) {
        Route::get("/{locale}/{$archiveSlug}", fn ($locale) => $renderIndex($locale))
            ->where('locale', $localePattern)
            ->name('locale.posts.index');

        Route::get("/{locale}/{$archiveSlug}/{$categoryBase}/{category}", fn ($locale, $category) => $renderCategory($locale, $category))
            ->where('locale', $localePattern)
            ->name('locale.posts.category');

        Route::get("/{locale}/{$archiveSlug}/{slug}", fn ($locale, $slug) => $renderSingle($locale, $slug))
            ->where('locale', $localePattern)
            ->name('locale.posts.show');
    }
});
