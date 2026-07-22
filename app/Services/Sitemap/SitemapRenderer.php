<?php

namespace App\Services\Sitemap;

use Carbon\CarbonInterface;

class SitemapRenderer
{
    public function renderIndex(array $sitemaps): string
    {
        $xslUrl = url('/main-sitemap.xsl');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="'.htmlspecialchars($xslUrl, ENT_XML1, 'UTF-8').'"?>'."\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($sitemaps as $sm) {
            $xml .= "  <sitemap>\n";
            $xml .= '    <loc>'.htmlspecialchars($sm['loc'], ENT_XML1, 'UTF-8')."</loc>\n";
            if (! empty($sm['lastmod'])) {
                $xml .= '    <lastmod>'.htmlspecialchars($sm['lastmod'], ENT_XML1, 'UTF-8')."</lastmod>\n";
            }
            $xml .= "  </sitemap>\n";
        }

        $xml .= '</sitemapindex>';

        return $xml;
    }

    public function renderUrlSet(array $urls): string
    {
        $xslUrl = url('/main-sitemap.xsl');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="'.htmlspecialchars($xslUrl, ENT_XML1, 'UTF-8').'"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.htmlspecialchars($u['loc'], ENT_XML1, 'UTF-8')."</loc>\n";
            if (! empty($u['lastmod'])) {
                $mod = $u['lastmod'] instanceof CarbonInterface
                    ? $u['lastmod']->toAtomString()
                    : (string) $u['lastmod'];
                $xml .= '    <lastmod>'.htmlspecialchars($mod, ENT_XML1, 'UTF-8')."</lastmod>\n";
            }
            if (! empty($u['changefreq'])) {
                $xml .= '    <changefreq>'.htmlspecialchars($u['changefreq'], ENT_XML1, 'UTF-8')."</changefreq>\n";
            }
            if (isset($u['priority'])) {
                $xml .= '    <priority>'.number_format((float) $u['priority'], 1, '.', '')."</priority>\n";
            }
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
