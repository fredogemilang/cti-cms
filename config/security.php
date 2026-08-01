<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Content Security Policy (CSP)
    |--------------------------------------------------------------------------
    |
    | Enable CSP header injection via SecurityHeaders middleware.
    | Disabled by default to prevent breaking custom inline scripts/styles in themes.
    |
    */
    'csp_enabled' => (bool) env('SECURITY_CSP_ENABLED', false),
];
