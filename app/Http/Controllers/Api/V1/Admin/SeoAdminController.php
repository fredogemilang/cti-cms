<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\Page;
use App\Services\SeoRenderer;
use Illuminate\Http\Request;

class SeoAdminController extends Controller
{
    /**
     * Get SEO & GEO metadata for a Page
     */
    public function getPageSeo(int $id)
    {
        $page = Page::findOrFail($id);
        $seoMeta = $page->seoMeta;
        $resolvedSeo = app(SeoRenderer::class)->resolve($page);

        return response()->json([
            'success' => true,
            'data' => [
                'meta' => $seoMeta,
                'resolved' => $resolvedSeo,
            ],
        ]);
    }

    /**
     * Update SEO & GEO metadata for a Page
     */
    public function updatePageSeo(Request $request, int $id)
    {
        $page = Page::findOrFail($id);
        $validated = $this->validateSeoRequest($request);

        $seoMeta = $page->getOrCreateSeoMeta();
        $seoMeta->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Page SEO & GEO metadata updated successfully.',
            'data' => [
                'meta' => $seoMeta->fresh(),
                'resolved' => app(SeoRenderer::class)->resolve($page),
            ],
        ]);
    }

    /**
     * Get SEO & GEO metadata for a CPT Entry
     */
    public function getCptEntrySeo(string $type, int $id)
    {
        $postType = CustomPostType::where('slug', $type)->firstOrFail();
        $entry = CptEntry::where('post_type_id', $postType->id)->findOrFail($id);
        $seoMeta = $entry->seoMeta;
        $resolvedSeo = app(SeoRenderer::class)->resolve($entry);

        return response()->json([
            'success' => true,
            'data' => [
                'meta' => $seoMeta,
                'resolved' => $resolvedSeo,
            ],
        ]);
    }

    /**
     * Update SEO & GEO metadata for a CPT Entry
     */
    public function updateCptEntrySeo(Request $request, string $type, int $id)
    {
        $postType = CustomPostType::where('slug', $type)->firstOrFail();
        $entry = CptEntry::where('post_type_id', $postType->id)->findOrFail($id);
        $validated = $this->validateSeoRequest($request);

        $seoMeta = $entry->getOrCreateSeoMeta();
        $seoMeta->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'CPT Entry SEO & GEO metadata updated successfully.',
            'data' => [
                'meta' => $seoMeta->fresh(),
                'resolved' => app(SeoRenderer::class)->resolve($entry),
            ],
        ]);
    }

    protected function validateSeoRequest(Request $request): array
    {
        return $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'canonical_url' => 'nullable|url|max:500',
            'robots' => 'nullable|string|max:100',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:1000',
            'og_image_id' => 'nullable|integer|exists:media,id',
            'twitter_card' => 'nullable|string|max:50',
            'schema_type' => 'nullable|string|max:100',
            'schema_data' => 'nullable|array',
            'focus_keyword' => 'nullable|string|max:255',
            'is_cornerstone' => 'nullable|boolean',
            'locale' => 'nullable|string|max:10',
        ]);
    }
}
