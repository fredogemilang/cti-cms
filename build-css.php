<?php
/**
 * CSS Bundle Script
 * Merges bootstrap.min.css + style.css into a single theme.bundle.css
 * 
 * Usage: php build-css.php
 * Run this before deploying to production.
 */

$assetDir = __DIR__ . '/public/themes/iccom/assets/';

$files = [
    $assetDir . 'bootstrap.min.css',
    $assetDir . 'style.css',
];

$output = '';
foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "ERROR: $file not found\n";
        exit(1);
    }
    $output .= "/* === " . basename($file) . " === */\n";
    $output .= file_get_contents($file) . "\n\n";
    echo "Added: " . basename($file) . " (" . round(filesize($file) / 1024, 1) . " KB)\n";
}

$bundlePath = $assetDir . 'theme.bundle.css';
file_put_contents($bundlePath, $output);
$bundleSize = round(filesize($bundlePath) / 1024, 1);
echo "\nBundled: theme.bundle.css ({$bundleSize} KB)\n";
echo "Done!\n";
