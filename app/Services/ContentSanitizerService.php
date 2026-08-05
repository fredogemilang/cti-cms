<?php

namespace App\Services;

class ContentSanitizerService
{
    /**
     * Sanitize and format legacy/imported HTML content into clean, TipTap-compatible HTML.
     */
    public function sanitize(?string $html): string
    {
        if (empty($html) || ! is_string($html)) {
            return $html ?? '';
        }

        $clean = $html;

        // 1. Demote <h1> in body content to <h2> (H1 is reserved for Page Title)
        $clean = preg_replace('/<h1(\s[^>]*)?>(.*?)<\/h1>/is', '<h2$1>$2</h2>', $clean) ?? $clean;

        // 2. Convert <span style="...font-weight: 700|bold..."> or <b> to <strong>
        $clean = preg_replace_callback('/<span\s+[^>]*style=["\'][^"\']*font-weight:\s*(700|bold)[^"\']*["\'][^>]*>(.*?)<\/span>/is', function ($matches) {
            return '<strong>'.$matches[2].'</strong>';
        }, $clean) ?? $clean;

        $clean = preg_replace('/<b(\s[^>]*)?>(.*?)<\/b>/is', '<strong>$2</strong>', $clean) ?? $clean;

        // 3. Convert <span style="...font-style: italic..."> or <i> to <em>
        $clean = preg_replace_callback('/<span\s+[^>]*style=["\'][^"\']*font-style:\s*italic[^"\']*["\'][^>]*>(.*?)<\/span>/is', function ($matches) {
            return '<em>'.$matches[2].'</em>';
        }, $clean) ?? $clean;

        $clean = preg_replace('/<i(\s[^>]*)?>(.*?)<\/i>/is', '<em>$2</em>', $clean) ?? $clean;

        // Convert <span style="...text-decoration: underline..."> to <u>
        $clean = preg_replace_callback('/<span\s+[^>]*style=["\'][^"\']*text-decoration:\s*underline[^"\']*["\'][^>]*>(.*?)<\/span>/is', function ($matches) {
            return '<u>'.$matches[1].'</u>';
        }, $clean) ?? $clean;

        // 5. Convert styled pseudo-headings (<p style="...font-size: 24px..."> or <p class="...h2...">) to <h2>
        $clean = preg_replace_callback('/<p\s+[^>]*(style=["\'][^"\']*font-size:\s*(2[0-9]|3[0-6])px[^"\']*["\']|class=["\'][^"\']*\b(h1|h2|title)\b[^"\']*["\'])[^>]*>(.*?)<\/p>/is', function ($matches) {
            return '<h2>'.strip_tags($matches[4], '<strong><em><u><s><code><del><strike><a><span><img>').'</h2>';
        }, $clean) ?? $clean;

        // 5. Clean MS Word / Mso classes and inline styles
        $clean = preg_replace('/class=["\'](Mso[a-zA-Z0-9]+|wp-block-[a-zA-Z0-9-]+)[^"\']*["\']/i', '', $clean) ?? $clean;
        $clean = preg_replace('/style=["\'](margin|padding|font-family|font-size|line-height|color|background-color):\s*[^"\';]+;?\s*["\']/i', '', $clean) ?? $clean;

        // 6. Unwrap plain <span> tags with no attributes
        $clean = preg_replace('/<span>\s*(.*?)\s*<\/span>/is', '$1', $clean) ?? $clean;
        $clean = preg_replace('/<span\s*>(.*?)<\/span>/is', '$1', $clean) ?? $clean;

        // 7. Remove empty inline attributes (e.g. <p  > or <h2 class="">)
        $clean = preg_replace('/\s+class=["\']\s*["\']/', '', $clean) ?? $clean;
        $clean = preg_replace('/\s+style=["\']\s*["\']/', '', $clean) ?? $clean;

        // 8. Collapse excessive empty paragraphs (<p>&nbsp;</p> or <p></p>)
        $clean = preg_replace('/(<p>\s*(&nbsp;|\s)*\s*<\/p>\s*){3,}/i', '<p>&nbsp;</p>', $clean) ?? $clean;

        return trim($clean);
    }
}
