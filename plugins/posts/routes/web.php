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

    // WordPress Migration
    Route::get('/wordpress-migration', function () {
        return view('posts::wordpress-migration');
    })->name('wordpress-migration')->middleware('permission:posts.create');

});

// Public Routes
Route::middleware(['web'])->group(function () {
    $archiveSlug = 'blog';
    if (Schema::hasTable('posts_settings')) {
        $archiveSlug = Setting::get('archive_slug', 'blog');
    }

    // Blog Index
    Route::get("/{$archiveSlug}", function () {
        $featuredPosts = Post::where('status', 'published')
            ->where('is_featured', true)
            ->latest()
            ->take(4)
            ->get();

        $activeTheme = app(ThemeLoader::class)->getActiveTheme();
        $themeSlug = $activeTheme ? $activeTheme->slug : 'default';

        $view = "{$themeSlug}::posts.index";
        if (! view()->exists($view)) {
            $view = view()->exists('iccom::posts.index') ? 'iccom::posts.index' : 'posts::index';
        }

        return view($view, compact('featuredPosts'));
    })->name('posts.index');

    // Category Index
    Route::get("/{$archiveSlug}/category/{category}", function ($category) {
        $featuredPosts = Post::where('status', 'published')
            ->where('is_featured', true)
            ->latest()
            ->take(4)
            ->get();

        $activeTheme = app(ThemeLoader::class)->getActiveTheme();
        $themeSlug = $activeTheme ? $activeTheme->slug : 'default';

        $view = "{$themeSlug}::posts.index";
        if (! view()->exists($view)) {
            $view = view()->exists('iccom::posts.index') ? 'iccom::posts.index' : 'posts::index';
        }

        return view($view, compact('featuredPosts', 'category'));
    })->name('posts.category');

    Route::get("/{$archiveSlug}/{slug}", function ($slug) {
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
    })->name('posts.show');
});
