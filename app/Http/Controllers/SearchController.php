<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use App\Services\TemplateResolver;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __construct(
        protected SearchService $searchService,
        protected TemplateResolver $templateResolver
    ) {}

    public function index(Request $request, ?string $locale = null): View
    {
        $currentLocale = $locale ?: app()->getLocale();
        if ($locale) {
            app()->setLocale($locale);
        }

        $query = (string) $request->input('q', $request->input('s', ''));

        $results = $this->searchService->search($query, $currentLocale, 10);

        $view = $this->templateResolver->resolve('search', [
            'type' => 'search',
        ]);

        return view($view, [
            'query' => $query,
            'results' => $results,
            'locale' => $currentLocale,
            'searchService' => $this->searchService,
        ]);
    }
}
