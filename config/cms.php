<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CMS Version
    |--------------------------------------------------------------------------
    |
    | The current version of the CMS. Used for plugin dependency validation
    | and displayed in the admin footer.
    |
    */
    'version' => '1.0.0',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Path
    |--------------------------------------------------------------------------
    |
    | The URI prefix for the admin panel. Change to a custom value
    | (e.g. 'cms', 'backend', 'manage') for security-through-obscurity.
    |
    */
    'path' => env('ADMIN_PATH', 'ctrlpanel'),

    /*
    |--------------------------------------------------------------------------
    | Installation Marker
    |--------------------------------------------------------------------------
    |
    | When true, the CMS has been installed and seeded. The cms:install
    | command writes a marker file to storage/cms_installed.
    | Plugins and middleware can check this to show install prompts.
    |
    */
    'installed' => file_exists(storage_path('cms_installed')),

    /*
    |--------------------------------------------------------------------------
    | Core Features
    |--------------------------------------------------------------------------
    |
    | Toggle built-in CMS modules. Disabling a module hides its admin UI
    | and routes but does NOT delete data.
    |
    */
    'features' => [
        'pages' => true,
        'forms' => true,
        'media' => true,
        'menus' => true,
        'api' => true,
        'webhooks' => true,
        'email_templates' => true,
        'activity_log' => true,
    ],
];
