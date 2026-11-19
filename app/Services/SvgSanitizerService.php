<?php

namespace App\Services;

use enshrined\svgSanitize\Sanitizer;

class SvgSanitizerService
{
    protected Sanitizer $sanitizer;

    public function __construct(?Sanitizer $sanitizer = null)
    {
        $this->sanitizer = $sanitizer ?? new Sanitizer;
        $this->sanitizer->removeRemoteReferences(true);
    }

    /**
     * Clean and sanitize SVG markup.
     * Returns sanitized SVG string, or null if invalid, unparseable, or empty.
     */
    public function clean(?string $svg): ?string
    {
        if ($svg === null || trim($svg) === '') {
            return null;
        }

        $clean = $this->sanitizer->sanitize($svg);

        if ($clean === false || trim($clean) === '') {
            return null;
        }

        return $clean;
    }

    /**
     * Get XML issues identified during last sanitization run.
     *
     * @return array<int, array{message: string, line: int}>
     */
    public function getXmlIssues(): array
    {
        return $this->sanitizer->getXmlIssues() ?? [];
    }

    /**
     * Check whether an SVG string contains threats or fails sanitization.
     */
    public function hasThreats(string $svg): bool
    {
        $clean = $this->clean($svg);

        if ($clean === null) {
            return true;
        }

        return ! empty($this->getXmlIssues());
    }

    /**
     * Get the underlying sanitizer instance.
     */
    public function getSanitizer(): Sanitizer
    {
        return $this->sanitizer;
    }
}
