<?php

return [
    'schemas' => [],
    'enable_examples' => env('UI_SCHEMA_CRAFT_ENABLE_EXAMPLES', true),
    'dd_examples' => env('UI_SCHEMA_CRAFT_DD_EXAMPLES', true),
    /*
    |--------------------------------------------------------------------------
    | Default Schema Settings
    |--------------------------------------------------------------------------
    |
    | Here you can specify default settings for your UI components.
    |
    */

    'defaults' => [
        'theme' => 'default',
        'size' => 'md',
        'validation_mode' => 'onBlur',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Configure caching behavior for component schemas.
    |
    */

    'cache' => [
        'enabled' => env('UI_SCHEMA_CRAFT_CACHE_ENABLED', true),
        'ttl' => env('UI_SCHEMA_CRAFT_CACHE_TTL', 3600),
    ],
];
