<?php

namespace App\Http\Controllers;

use App\Models\CptEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LegacyRedirectController extends Controller
{
    /**
     * Redirect legacy /news-detail/{slug} to /blog-news/{slug}.
     */
    public function newsDetail(Request $request, ?string $localeOrSlug = null, ?string $slug = null): RedirectResponse
    {
        $locale = $slug !== null ? $localeOrSlug : null;
        $targetSlug = $slug ?? $localeOrSlug;
        $qs = $request->getQueryString();

        $path = ($locale ? "/{$locale}" : '') . "/blog-news/{$targetSlug}";
        return redirect(url($path . ($qs ? "?{$qs}" : '')), 301);
    }

    /**
     * Redirect legacy /blog/{slug} to /blog-news/{slug}.
     */
    public function blogSingle(Request $request, ?string $localeOrSlug = null, ?string $slug = null): RedirectResponse
    {
        $locale = $slug !== null ? $localeOrSlug : null;
        $targetSlug = $slug ?? $localeOrSlug;
        $qs = $request->getQueryString();

        $path = ($locale ? "/{$locale}" : '') . "/blog-news/{$targetSlug}";
        return redirect(url($path . ($qs ? "?{$qs}" : '')), 301);
    }

    /**
     * Redirect legacy /category/{slug} to /blog-news/category/{slug}.
     */
    public function category(Request $request, ?string $localeOrSlug = null, ?string $slug = null): RedirectResponse
    {
        $locale = $slug !== null ? $localeOrSlug : null;
        $targetSlug = $slug ?? $localeOrSlug;
        $qs = $request->getQueryString();

        $prefix = $locale ? "/{$locale}" : '';
        $path = $targetSlug ? "{$prefix}/blog-news/category/{$targetSlug}" : "{$prefix}/blog-news";
        return redirect(url($path . ($qs ? "?{$qs}" : '')), 301);
    }

    /**
     * Redirect legacy /tag/{slug} to /blog-news/tag/{slug}.
     */
    public function tag(Request $request, ?string $localeOrSlug = null, ?string $slug = null): RedirectResponse
    {
        $locale = $slug !== null ? $localeOrSlug : null;
        $targetSlug = $slug ?? $localeOrSlug;
        $qs = $request->getQueryString();

        $prefix = $locale ? "/{$locale}" : '';
        $path = $targetSlug ? "{$prefix}/blog-news/tag/{$targetSlug}" : "{$prefix}/blog-news";
        return redirect(url($path . ($qs ? "?{$qs}" : '')), 301);
    }

    /**
     * Redirect legacy client-says / our-clients to customer-success.
     * If the slug matches a published customer-success entry, redirect to its detail page.
     */
    public function clientSays(Request $request, ?string $localeOrSlug = null, ?string $slug = null): RedirectResponse
    {
        $locale = ($slug !== null && in_array($localeOrSlug, available_locales(), true)) ? $localeOrSlug : null;
        $targetSlug = $slug ?? ($locale ? null : $localeOrSlug);

        $prefix = $locale ? "/{$locale}" : '';

        if ($targetSlug) {
            $cptEntry = CptEntry::where('slug', $targetSlug)
                ->where('post_type_id', 3)
                ->where('status', 'published')
                ->first();

            if ($cptEntry) {
                return redirect(url("{$prefix}/customer-success/{$cptEntry->slug}"), 301);
            }
        }

        return redirect(url("{$prefix}/customer-success"), 301);
    }

    /**
     * Redirect legacy paginated customer-success /customer-success/page/{page} to query string.
     */
    public function customerSuccessPage(Request $request, ?string $localeOrPage = null, $page = null): RedirectResponse
    {
        $locale = $page !== null ? $localeOrPage : null;
        $pageNum = (int) ($page ?? $localeOrPage);

        $prefix = $locale ? "/{$locale}" : '';
        return redirect(url("{$prefix}/customer-success?page={$pageNum}"), 301);
    }
}
