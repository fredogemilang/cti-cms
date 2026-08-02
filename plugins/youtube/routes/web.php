<?php

use App\Models\Page;
use App\Services\ThemeLoader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Plugins\Youtube\Models\YoutubePlaylist;
use Plugins\Youtube\Models\YoutubeVideo;

$adminPath = config('admin.path', config('cms.path', 'admin'));

Route::middleware(['web', 'auth', 'permission:youtube.view'])
    ->prefix($adminPath.'/youtube')
    ->name('admin.youtube.')
    ->group(function () {
        Route::get('/', function () {
            return view('youtube::dashboard');
        })->name('index');

        Route::get('/videos', function () {
            return view('youtube::videos');
        })->name('videos');

        Route::get('/playlists', function () {
            return view('youtube::playlists');
        })->name('playlists');

        Route::get('/settings', function () {
            return view('youtube::settings');
        })->middleware('permission:youtube-settings.view')->name('settings');
    });

Route::middleware(['web'])
    ->group(function () {
        Route::get('/video', function (Request $request) {
            $featured = YoutubeVideo::featured()->visible()->first();
            if (! $featured) {
                $featured = YoutubeVideo::visible()->orderByDesc('published_at')->first();
            }

            $playlists = YoutubePlaylist::visible()->orderBy('sort_order')->get();

            $perPage = (int) (setting('youtube_per_page') ?? config('youtube.per_page') ?? 12);

            $activeCategory = $request->query('category') ?? $request->query('playlist') ?? 'All';
            $searchQuery = trim((string) ($request->query('q') ?? $request->query('search') ?? ''));

            $query = YoutubeVideo::with('playlists')->where('is_visible', true);

            if ($activeCategory && $activeCategory !== 'All') {
                $query->whereHas('playlists', function ($q) use ($activeCategory) {
                    $q->where('youtube_playlists.title', $activeCategory)
                        ->orWhere('youtube_playlists.youtube_id', $activeCategory)
                        ->orWhere('youtube_playlists.id', $activeCategory);
                });
            }

            if ($searchQuery !== '') {
                $query->where(function ($q) use ($searchQuery) {
                    $q->where('title', 'like', "%{$searchQuery}%")
                        ->orWhere('description', 'like', "%{$searchQuery}%")
                        ->orWhere('channel_title', 'like', "%{$searchQuery}%");
                });
            }

            $videos = $query->orderByDesc('published_at')
                ->paginate($perPage)
                ->withQueryString();

            $videoJsonData = collect($videos->items())->map(function ($v) {
                $firstPl = $v->playlists->first();
                $cat = $firstPl ? $firstPl->title : ($v->channel_title ?: 'CDT Video');
                $thumb = $v->thumbnail_high ?? $v->thumbnail_medium ?? "https://img.youtube.com/vi/{$v->youtube_id}/hqdefault.jpg";

                return [
                    'id' => $v->youtube_id,
                    'title' => $v->title,
                    'description' => $v->description ?? '',
                    'category' => $cat,
                    'date' => $v->published_at ? $v->published_at->format('M d, Y') : '',
                    'duration' => $v->formatted_duration,
                    'seconds' => $v->duration_seconds ?? 0,
                    'author' => $v->channel_title ?: 'Central Data Technology',
                    'company' => 'Central Data Technology',
                    'avatar' => 'CDT',
                    'thumbnail' => $thumb,
                ];
            })->values();

            $page = Page::where('slug', 'video')->first();
            if ($page) {
                View::share('page', $page);
            }

            $activeTheme = app(ThemeLoader::class)->getActiveTheme();
            $themeSlug = $activeTheme ? $activeTheme->slug : 'default';

            $view = "{$themeSlug}::youtube.index";
            if (! view()->exists($view)) {
                $view = 'youtube::front.index';
            }

            return view($view, compact('featured', 'playlists', 'videos', 'videoJsonData', 'page', 'activeCategory', 'searchQuery'));
        })->name('youtube.index');
    });
