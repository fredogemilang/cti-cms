<?php

namespace Plugins\KouventaPages\Services;

use App\Services\Sitemap\SitemapBuilder;
use Illuminate\Support\Facades\Schema;
use Plugins\KouventaPages\Models\KouventaPage;

class KouventaSitemapBuilder extends SitemapBuilder
{
    public function getPageUrls(): array
    {
        $urls = parent::getPageUrls();

        if (! setting('seo_content_type_pages_index_enabled', true)) {
            return $urls;
        }

        try {
            if (! Schema::hasTable('kouventa_pages')) {
                return $urls;
            }

            $locales = available_locales();

            foreach (KouventaPage::with('seoMeta')->published()->orderBy('updated_at', 'desc')->get() as $kPage) {
                if (! $this->isIndexable($kPage)) {
                    continue;
                }

                foreach ($locales as $loc) {
                    $urls[] = [
                        'loc' => $kPage->getUrl($loc),
                        'lastmod' => $kPage->updated_at ? $kPage->updated_at->toAtomString() : now()->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority' => 0.8,
                        'type' => 'Page',
                        'images' => [],
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Gracefully ignore DB errors during migration/setup
        }

        return $urls;
    }
}
