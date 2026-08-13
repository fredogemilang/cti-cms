<?php

namespace App\Http\Controllers;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Page;
use App\Services\ThemeLoader;
use Illuminate\Http\Request;

/**
 * Serves deferred HTML fragments for below-fold homepage sections.
 * These are loaded via AJAX after initial page load to reduce
 * the initial HTML document size (~239KB → ~74KB).
 *
 * Allowed sections: testimonials, contact
 */
class DeferredSectionController extends Controller
{
    private const ALLOWED_SECTIONS = ['testimonials', 'contact'];

    public function show(string $section)
    {
        if (! in_array($section, self::ALLOWED_SECTIONS, true)) {
            abort(404);
        }

        $theme = app(ThemeLoader::class)->getActiveTheme();
        $themeNs = $theme?->slug ?? 'cdt';

        $page = Page::with(['blocks' => fn ($q) => $q->whereNull('parent_block_id')->orderBy('order')])
            ->where('slug', 'home')
            ->first();

        $data = ['page' => $page];

        if ($section === 'testimonials') {
            $data['testimonials'] = $this->loadTestimonials();
        }

        return view("{$themeNs}::partials.deferred.{$section}", $data)
            ->render();
    }

    private function loadTestimonials()
    {
        $cpt = CustomPostType::where('slug', 'client-says')->first();
        if (! $cpt) {
            return collect();
        }

        $currentLocale = app()->getLocale();

        return CptEntry::with('author')
            ->where('post_type_id', $cpt->id)
            ->where('status', 'published')
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->orderByDesc('id')
            ->get()
            ->filter(fn ($e) => $e->hasContentForLocale($currentLocale))
            ->values();
    }
}
