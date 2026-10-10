<?php

use App\Models\CptEntry;
use App\Models\CustomPostType;

/*
 * CDT theme helpers. Loaded by CdtThemeServiceProvider::register() while the
 * CDT theme is active.
 */

if (! function_exists('resolve_solution_url')) {
    /**
     * Resolve solution link (converting legacy .html or static paths to CPT tech-product entry permalinks).
     */
    function resolve_solution_url(?string $link, string $vendorSlug = ''): string
    {
        if (empty($link) || $link === '#') {
            return '#';
        }

        // Clean html extension and leading slashes
        $clean = (string) preg_replace('/\.html$/i', '', ltrim($link, '/'));

        // Strip prefix like sub-akamai- or sub-aws- if present
        if (preg_match('/^sub-[^-\/]+-(.+)$/', $clean, $matches)) {
            $candidateSlug = $matches[1];
        } else {
            $candidateSlug = $clean;
        }

        // Try finding tech-product by slug
        $techProductType = CustomPostType::where('slug', 'tech-products')->first();
        if ($techProductType) {
            $entry = CptEntry::where('post_type_id', $techProductType->id)
                ->where(function ($q) use ($candidateSlug, $clean) {
                    $q->where('slug', $candidateSlug)
                        ->orWhere('slug', $clean)
                        ->orWhere('slug', 'like', '%'.$candidateSlug.'%');
                })
                ->first();

            if ($entry) {
                return $entry->getUrl();
            }
        }

        return url('/'.$clean);
    }
}
