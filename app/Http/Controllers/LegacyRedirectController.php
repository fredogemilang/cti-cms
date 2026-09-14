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
        return redirect(trailing_slash_url(url("{$prefix}/customer-success"))."?page={$pageNum}", 301);
    }

    /**
     * Redirect legacy paginated blog /blog-news/page/{page} to query string ?page={page}.
     */
    public function blogNewsPage(Request $request, ?string $localeOrPage = null, $page = null): RedirectResponse
    {
        $locale = $page !== null ? $localeOrPage : null;
        $pageNum = (int) ($page ?? $localeOrPage);

        $prefix = $locale ? "/{$locale}" : '';
        return redirect(trailing_slash_url(url("{$prefix}/blog-news"))."?page={$pageNum}", 301);
    }

    /**
     * Redirect legacy paginated tags /(blog-news/)?tag/{slug}/page/{page} to ?page={page}.
     */
    public function tagPage(Request $request, ?string $localeOrSlug = null, $slugOrPage = null, $page = null): RedirectResponse
    {
        $hasLocale = $page !== null;
        $locale = $hasLocale ? $localeOrSlug : null;
        $targetSlug = $hasLocale ? $slugOrPage : $localeOrSlug;
        $pageNum = (int) ($hasLocale ? $page : $slugOrPage);

        $prefix = $locale ? "/{$locale}" : '';
        return redirect(trailing_slash_url(url("{$prefix}/blog-news/tag/{$targetSlug}"))."?page={$pageNum}", 301);
    }

    /**
     * Redirect legacy paginated categories /(blog-news/)?category/{slug}/page/{page} to ?page={page}.
     */
    public function categoryPage(Request $request, ?string $localeOrSlug = null, $slugOrPage = null, $page = null): RedirectResponse
    {
        $hasLocale = $page !== null;
        $locale = $hasLocale ? $localeOrSlug : null;
        $targetSlug = $hasLocale ? $slugOrPage : $localeOrSlug;
        $pageNum = (int) ($hasLocale ? $page : $slugOrPage);

        $prefix = $locale ? "/{$locale}" : '';
        return redirect(trailing_slash_url(url("{$prefix}/blog-news/category/{$targetSlug}"))."?page={$pageNum}", 301);
    }

    /**
     * Redirect legacy /solution/{slug} to new /industry/{slug} or /solution/{slug}.
     */
    public function legacySolution(Request $request, ?string $localeOrSlug = null, ?string $slug = null): RedirectResponse
    {
        $locale = ($slug !== null && in_array($localeOrSlug, available_locales(), true)) ? $localeOrSlug : null;
        $targetSlug = $slug ?? ($locale ? null : $localeOrSlug);
        $prefix = $locale ? "/{$locale}" : '';

        $industryMap = [
            'media' => ['en' => 'media', 'id' => 'media'],
            'insurance' => ['en' => 'insurance', 'id' => 'asuransi'],
            'asuransi' => ['en' => 'insurance', 'id' => 'asuransi'],
            'ecommerce' => ['en' => 'ecommerce', 'id' => 'ecommerce'],
            'healthcare' => ['en' => 'healthcare', 'id' => 'kesehatan'],
            'kesehatan' => ['en' => 'healthcare', 'id' => 'kesehatan'],
            'education' => ['en' => 'educations', 'id' => 'pendidikan'],
            'educations' => ['en' => 'educations', 'id' => 'pendidikan'],
            'pendidikan' => ['en' => 'educations', 'id' => 'pendidikan'],
            'manufacture' => ['en' => 'manufacture', 'id' => 'manufaktur'],
            'manufaktur' => ['en' => 'manufacture', 'id' => 'manufaktur'],
            'public-sector' => ['en' => 'public-sector', 'id' => 'sektor-publik'],
            'sektor-publik' => ['en' => 'public-sector', 'id' => 'sektor-publik'],
            'telecommunication' => ['en' => 'telecomunication-service-provider', 'id' => 'telekomunikasi-penyedia-layanan'],
            'telekomunikasi' => ['en' => 'telecomunication-service-provider', 'id' => 'telekomunikasi-penyedia-layanan'],
            'telecomunication-service-provider' => ['en' => 'telecomunication-service-provider', 'id' => 'telekomunikasi-penyedia-layanan'],
            'telekomunikasi-penyedia-layanan' => ['en' => 'telecomunication-service-provider', 'id' => 'telekomunikasi-penyedia-layanan'],
            'finance' => ['en' => 'financial-banking', 'id' => 'keuangan-perbankan'],
            'banking' => ['en' => 'financial-banking', 'id' => 'keuangan-perbankan'],
            'financial-banking' => ['en' => 'financial-banking', 'id' => 'keuangan-perbankan'],
            'keuangan-perbankan' => ['en' => 'financial-banking', 'id' => 'keuangan-perbankan'],
        ];

        if ($targetSlug && isset($industryMap[$targetSlug])) {
            $langKey = $locale === 'id' ? 'id' : 'en';
            $industrySlug = $industryMap[$targetSlug][$langKey];
            $dest = trailing_slash_url(url("{$prefix}/industry/{$industrySlug}"));
            if (rtrim($request->url(), '/') !== rtrim($dest, '/')) {
                return redirect($dest, 301);
            }
        }

        // If it matches a solution CPT entry (e.g. cloud, analytics)
        if ($targetSlug) {
            $solCpt = CptEntry::where('slug', $targetSlug)->where('post_type_id', 2)->first();
            if ($solCpt) {
                $canonicalUrl = $solCpt->getUrl($locale ?: 'en');
                if (rtrim($request->url(), '/') !== rtrim($canonicalUrl, '/')) {
                    return redirect($canonicalUrl, 301);
                }
            }
        }

        $fallbackUrl = trailing_slash_url(url($locale === 'id' ? '/id/solution/cloud' : '/solution/cloud'));
        if (rtrim($request->url(), '/') !== rtrim($fallbackUrl, '/')) {
            return redirect($fallbackUrl, 301);
        }

        abort(404);
    }

}
