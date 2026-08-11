<?php

namespace App\Http\Controllers;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Services\ThemeLoader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SearchController extends Controller
{
    public function index(Request $request, ?string $locale = null)
    {
        if ($locale && in_array($locale, available_locales())) {
            app()->setLocale($locale);
        }

        $query = $request->query('q');

        $blogResults = collect();
        $productResults = collect();

        if (!empty($query)) {
            // Generate search variations to support both 'Wifi' and 'Wi-Fi' spellings
            $queries = [$query];
            $lowerQuery = strtolower($query);
            if (str_contains($lowerQuery, 'wifi')) {
                $queries[] = str_ireplace('wifi', 'wi-fi', $query);
            } elseif (str_contains($lowerQuery, 'wi-fi')) {
                $queries[] = str_ireplace('wi-fi', 'wifi', $query);
            }

            // Retrieve blog posts using the posts plugin model (fail-safe checks applied)
            if (is_plugin_active('posts') && class_exists(\Plugins\Posts\Models\Post::class)) {
                $blogResults = \Plugins\Posts\Models\Post::published()
                    ->where(function ($q) use ($queries) {
                        foreach ($queries as $qVar) {
                            $q->orWhere('title', 'like', "%{$qVar}%")
                              ->orWhere('content', 'like', "%{$qVar}%")
                              ->orWhere('excerpt', 'like', "%{$qVar}%");
                        }
                    })
                    ->orderBy('published_at', 'desc')
                    ->get();
            }

            // Retrieve products from CPT entries
            $productsCptId = CustomPostType::where('slug', 'products')->value('id');
            if ($productsCptId) {
                $productResults = CptEntry::where('post_type_id', $productsCptId)
                    ->where('status', 'published')
                    ->where(function ($q) use ($queries) {
                        foreach ($queries as $qVar) {
                            $q->orWhere('title', 'like', "%{$qVar}%")
                              ->orWhere('content', 'like', "%{$qVar}%")
                              ->orWhere('excerpt', 'like', "%{$qVar}%");
                        }
                    })
                    ->orderBy('title', 'asc')
                    ->get();
            }
        }

        $theme = app(ThemeLoader::class)->getActiveTheme();
        $themeNamespace = $theme?->slug;

        $candidates = array_filter([
            !empty($themeNamespace) ? "{$themeNamespace}::pages.search" : null,
            'pages.search',
        ]);

        $viewName = 'pages.search';
        foreach ($candidates as $candidate) {
            if (view()->exists($candidate)) {
                $viewName = $candidate;
                break;
            }
        }

        return view($viewName, [
            'query' => $query,
            'blogResults' => $blogResults,
            'productResults' => $productResults,
        ]);
    }
}
