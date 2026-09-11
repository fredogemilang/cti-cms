<?php

// Script to extract metadata (title, description) from production sitemaps
$sitemaps = [
    'https://www.centraldatatech.com/page-sitemap.xml',
    'https://www.centraldatatech.com/solution-sitemap.xml',
    'https://www.centraldatatech.com/industry-sitemap.xml',
    'https://www.centraldatatech.com/customer-success-sitemap.xml',
];

$allUrls = [];
foreach ($sitemaps as $sm) {
    echo "Fetching sitemap: {$sm}...\n";
    $xml = @file_get_contents($sm);
    if (! $xml) {
        echo "Failed to fetch {$sm}\n";
        continue;
    }
    preg_match_all('/<loc>([^<]+)<\/loc>/', $xml, $matches);
    if (! empty($matches[1])) {
        foreach ($matches[1] as $url) {
            $allUrls[] = trim($url);
        }
    }
}

$allUrls = array_unique($allUrls);
echo "Total URLs collected: " . count($allUrls) . "\n";

$results = [];
$withDesc = [];

// Concurrency using curl_multi for fast crawling
$chunks = array_chunk($allUrls, 20);
foreach ($chunks as $chunkIndex => $chunk) {
    echo "Processing chunk " . ($chunkIndex + 1) . "/" . count($chunks) . "...\n";
    $mh = curl_multi_init();
    $curlHandles = [];

    foreach ($chunk as $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        curl_multi_add_handle($mh, $ch);
        $curlHandles[$url] = $ch;
    }

    $running = null;
    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh);
    } while ($running > 0);

    foreach ($curlHandles as $url => $ch) {
        $html = curl_multi_getcontent($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);

        if ($httpCode === 200 && $html) {
            $title = '';
            $desc = '';

            if (preg_match('/<title\b[^>]*>(.*?)<\/title>/is', $html, $m)) {
                $title = html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }

            if (preg_match('/<meta\s+name=["\']description["\']\s+content=["\']([^"\']*)["\']/is', $html, $m)) {
                $desc = html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            } elseif (preg_match('/<meta\s+content=["\']([^"\']*)["\']\s+name=["\']description["\']/is', $html, $m)) {
                $desc = html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }

            $path = parse_url($url, PHP_URL_PATH);
            $results[$path] = [
                'url' => $url,
                'path' => $path,
                'title' => $title,
                'description' => $desc,
            ];

            if (! empty($desc)) {
                $withDesc[$path] = [
                    'url' => $url,
                    'path' => $path,
                    'title' => $title,
                    'description' => $desc,
                ];
            }
        }
    }
    curl_multi_close($mh);
}

echo "Total with description: " . count($withDesc) . "\n";
file_put_contents(__DIR__ . '/prod-with-description.json', json_encode($withDesc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
file_put_contents(__DIR__ . '/prod-all-meta.json', json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo "Saved to scripts/prod-with-description.json and scripts/prod-all-meta.json\n";
