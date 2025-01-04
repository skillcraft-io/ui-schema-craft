<?php

return [
    'schemas' => [],
    'enable_examples' => env('UI_SCHEMA_CRAFT_ENABLE_EXAMPLES', false),
    'dd_examples' => env('UI_SCHEMA_CRAFT_DD_EXAMPLES', false),
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
    ],

    /*
    |--------------------------------------------------------------------------
    | State Management
    |--------------------------------------------------------------------------
    |
    | Configure state persistence settings
    |
    */

    'state_ttl' => env('UI_SCHEMA_CRAFT_STATE_TTL', 3600),
    'state_prefix' => env('UI_SCHEMA_CRAFT_STATE_PREFIX', 'ui_schema_state:'),

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
